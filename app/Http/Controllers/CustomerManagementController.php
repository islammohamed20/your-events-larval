<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Quote;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CustomersExport;
use App\Exports\CustomerDetailExport;

class CustomerManagementController extends Controller
{
    /**
     * Display customer management dashboard
     */
    public function index()
    {
        // Get only customers (non-admin users)
        $customers = User::where('is_admin', false)
                        ->withCount(['quotes', 'bookings'])
                        ->latest()
                        ->paginate(15);

        // Statistics
        $stats = [
            'total_customers' => User::where('is_admin', false)->count(),
            'active_quotes' => Quote::where('status', 'pending')->count(),
            'completed_bookings' => Booking::where('status', 'confirmed')->count(),
            'total_revenue' => Booking::where('status', 'confirmed')->sum('total_amount'),
        ];

        return view('admin.customers.index', compact('customers', 'stats'));
    }

    /**
     * Show customer details
     */
    public function show($id)
    {
        $customer = User::where('is_admin', false)
                       
                       ->with(['quotes.items.service', 'bookings.package', 'bookings.service'])
                       ->findOrFail($id);

        // Customer statistics
        $customerStats = [
            'total_quotes' => $customer->quotes->count(),
            'approved_quotes' => $customer->quotes->where('status', 'approved')->count(),
            'total_bookings' => $customer->bookings->count(),
            'completed_bookings' => $customer->bookings->where('status', 'confirmed')->count(),
            'total_spent' => $customer->bookings->where('status', 'confirmed')->sum('total_amount'),
        ];

        return view('admin.customers.show', compact('customer', 'customerStats'));
    }

    /**
     * Show customer quotes
     */
    public function quotes($id)
    {
        $customer = User::where('is_admin', false)
                       
                       ->findOrFail($id);

        $quotes = Quote::where('user_id', $id)
                      ->with(['items.service'])
                      ->latest()
                      ->paginate(10);

        return view('admin.customers.quotes', compact('customer', 'quotes'));
    }

    /**
     * Show customer payments/bookings
     */
    public function payments($id)
    {
        $customer = User::where('is_admin', false)
                       
                       ->findOrFail($id);

        $payments = Booking::where('user_id', $id)
                          ->where('status', 'confirmed')
                          ->with(['package', 'service'])
                          ->latest()
                          ->paginate(10);

        // Payment statistics
        $paymentStats = [
            'total_payments' => $payments->total(),
            'total_amount' => Booking::where('user_id', $id)
                                   ->where('status', 'confirmed')
                                   ->sum('total_amount'),
            'average_payment' => Booking::where('user_id', $id)
                                      ->where('status', 'confirmed')
                                      ->avg('total_amount'),
        ];

        return view('admin.customers.payments', compact('customer', 'payments', 'paymentStats'));
    }

    /**
     * Export customers to Excel
     */
    public function exportCustomers()
    {
        return Excel::download(new CustomersExport, 'customers-' . date('Y-m-d') . '.xlsx');
    }

    /**
     * Export specific customer data to Excel
     */
    public function exportCustomerDetail($id)
    {
        $customer = User::where('is_admin', false)
                       
                       ->with(['quotes.items.service', 'bookings.package', 'bookings.service'])
                       ->findOrFail($id);

        return Excel::download(new CustomerDetailExport($customer), 'customer-' . $customer->id . '-' . date('Y-m-d') . '.xlsx');
    }

    /**
     * Search customers
     */
    public function search(Request $request)
    {
        $query = $request->get('q');
        
        $customers = User::where('is_admin', false)
                        
                        ->where(function($q) use ($query) {
                            $q->where('name', 'LIKE', "%{$query}%")
                              ->orWhere('email', 'LIKE', "%{$query}%")
                              ->orWhere('phone', 'LIKE', "%{$query}%")
                              ->orWhere('company_name', 'LIKE', "%{$query}%");
                        })
                        ->withCount(['quotes', 'bookings'])
                        ->latest()
                        ->paginate(15);

        return view('admin.customers.index', compact('customers'))->with('search', $query);
    }

    /**
     * Get customer analytics data
     */
    public function analytics()
    {
        // Monthly customer registration
        $monthlyRegistrations = User::where('is_admin', false)
                                  
                                  ->selectRaw('MONTH(created_at) as month, COUNT(*) as count')
                                  ->whereYear('created_at', date('Y'))
                                  ->groupBy('month')
                                  ->orderBy('month')
                                  ->get();

        // Top customers by spending
        $topCustomers = User::where('is_admin', false)
                           
                           ->withSum(['bookings' => function($query) {
                               $query->where('status', 'confirmed');
                           }], 'total_amount')
                           ->orderBy('bookings_sum_total_amount', 'desc')
                           ->limit(10)
                           ->get();

        // Quote conversion rate
        $totalQuotes = Quote::count();
        $approvedQuotes = Quote::where('status', 'approved')->count();
        $conversionRate = $totalQuotes > 0 ? ($approvedQuotes / $totalQuotes) * 100 : 0;

        return view('admin.customers.analytics', compact(
            'monthlyRegistrations', 
            'topCustomers', 
            'conversionRate'
        ));
    }

    /**
     * Show customer edit form
     */
    public function edit($id)
    {
        $customer = User::where('is_admin', false)
                       
                       ->withCount(['quotes', 'bookings'])
                       ->findOrFail($id);

        return view('admin.customers.edit', compact('customer'));
    }

    /**
     * Update customer data
     */
    public function update(Request $request, $id)
    {
        $customer = User::where('is_admin', false)
                       
                       ->findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $customer->id,
            'phone' => 'nullable|string|max:20',
            'company_name' => 'required|string|max:255',
            'tax_number' => 'nullable|string|max:20',
            'status' => 'nullable|in:active,inactive,blocked',
            'card_type' => 'nullable|in:visa,mastercard,mada',
            'card_holder_name' => 'nullable|string|max:255',
            'card_last_four' => 'nullable|string|size:4|regex:/^[0-9]{4}$/',
            'card_expiry_month' => 'nullable|string|size:2|regex:/^(0[1-9]|1[0-2])$/',
            'card_expiry_year' => 'nullable|string|size:4|regex:/^[0-9]{4}$/',
            'notes' => 'nullable|string|max:1000',
        ]);

        // Update customer data
        $customer->update($validated);

        return redirect()->route('admin.customers.show', $customer->id)
                         ->with('success', 'تم تحديث بيانات العميل بنجاح');
    }

    /**
     * Update customer status or notes (legacy method)
     */
    public function updateCustomer(Request $request, $id)
    {
        $customer = User::where('is_admin', false)
                       
                       ->findOrFail($id);

        $request->validate([
            'notes' => 'nullable|string|max:1000',
            'status' => 'nullable|in:active,inactive,blocked',
        ]);

        // Update only status and notes
        $customer->update($request->only(['notes', 'status']));

        return redirect()->back()->with('success', 'تم تحديث بيانات العميل بنجاح');
    }

    /**
     * Delete a customer (with validations)
     */
    public function destroy($id)
    {
        // لا تسمح بحذف مسؤولي النظام
        $customer = User::where('is_admin', false)->findOrFail($id);

        // منع حذف عميل لديه حجوزات حالية أو مؤكدة
        $hasActiveBookings = $customer->bookings()->whereIn('status', ['pending', 'confirmed'])->exists();
        if ($hasActiveBookings) {
            return redirect()->back()->with('error', 'لا يمكن حذف عميل لديه حجوزات نشطة أو مؤكدة.');
        }

        // إذا كان لديه عروض أسعار غير مكتملة يمكن فقط الأرشفه (Soft Delete) إن كانت مدعومة
        // هنا سنجري حذف دائم مع التأكد من عدم وجود قيود

        try {
            // حذف عروض الأسعار المرتبطة غير المعتمدة لتفادي قيود العلاقات
            Quote::where('user_id', $customer->id)->where('status', 'pending')->delete();

            // تأكد من عدم وجود حجوزات نشطة، الحجوزات الملغاة لا تعيق الحذف
            // تنفيذ الحذف
            $customer->delete();

            return redirect()->route('admin.customers.index')->with('success', 'تم حذف العميل بنجاح.');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'فشل حذف العميل: ' . $e->getMessage());
        }
    }
}

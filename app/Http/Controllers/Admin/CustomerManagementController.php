<?php

namespace App\Http\Controllers\Admin;

use App\Exports\CustomersExport;
use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\LoginActivity;
use App\Models\OtpVerification;
use App\Models\User;
use App\Models\Visit;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;

class CustomerManagementController extends Controller
{
    public function index()
    {
        // عرض العملاء فقط (غير المديرين)
        $customers = User::where('is_admin', false)->latest()->paginate(15);

        return view('admin.customers.index', compact('customers'));
    }

    public function show(User $customer)
    {
        // التأكد من أن المستخدم عميل وليس مدير
        if ($customer->is_admin) {
            abort(404, 'العميل غير موجود');
        }

        $bookings = $customer->bookings()->latest()->paginate(10);
        $quotes = $customer->quotes()->latest()->paginate(10);

        return view('admin.customers.show', compact('customer', 'bookings', 'quotes'));
    }

    public function edit(User $customer)
    {
        // التأكد من أن المستخدم عميل وليس مدير
        if ($customer->is_admin) {
            abort(404, 'العميل غير موجود');
        }

        return view('admin.customers.edit', compact('customer'));
    }

    public function update(Request $request, User $customer)
    {
        // التأكد من أن المستخدم عميل وليس مدير
        if ($customer->is_admin) {
            abort(404, 'العميل غير موجود');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'company_name' => 'required|string|max:255',
            'tax_number' => 'nullable|string|max:20',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($customer->id)],
            'phone' => 'nullable|string|max:20',
            'status' => 'required|in:active,inactive,suspended',
            'card_type' => 'nullable|in:visa,mastercard,mada',
            'card_holder_name' => 'nullable|string|max:255',
            'card_last_four' => 'nullable|string|size:4|regex:/^[0-9]{4}$/',
            'card_expiry_month' => 'nullable|string|size:2|regex:/^(0[1-9]|1[0-2])$/',
            'card_expiry_year' => 'nullable|string|size:4|regex:/^[0-9]{4}$/',
        ]);

        // التأكد من عدم تغيير صلاحيات الإدارة
        $validated['is_admin'] = false;

        $customer->update($validated);

        return redirect()->route('admin.customers.index')
            ->with('success', 'تم تحديث بيانات العميل بنجاح');
    }

    public function destroy(User $customer)
    {
        // التأكد من أن المستخدم عميل وليس مدير
        if ($customer->is_admin) {
            abort(404, 'العميل غير موجود');
        }

        // التحقق من وجود حجوزات
        if ($customer->bookings()->count() > 0) {
            return redirect()->route('admin.customers.index')
                ->with('error', 'لا يمكن حذف العميل لأنه يحتوي على حجوزات');
        }

        // التحقق من وجود عروض أسعار
        if ($customer->quotes()->count() > 0) {
            return redirect()->route('admin.customers.index')
                ->with('error', 'لا يمكن حذف العميل لأنه يحتوي على عروض أسعار');
        }

        // تنظيف البيانات المرتبطة قبل الحذف النهائي
        $customer->wishlists()->delete();
        $customer->quotes()->delete();

        Visit::where('user_id', $customer->id)->delete();
        LoginActivity::where('user_id', $customer->id)->delete();
        OtpVerification::where('email', $customer->email)->delete();

        ActivityLog::where('subject_type', User::class)
            ->where('subject_id', $customer->id)
            ->delete();
        ActivityLog::where('actor_type', User::class)
            ->where('actor_id', $customer->id)
            ->delete();

        // حذف نهائي للمستخدم (Model لا يستخدم SoftDeletes)
        $customer->delete();

        return redirect()->route('admin.customers.index')
            ->with('success', 'تم حذف العميل بنجاح');
    }

    public function quotes(User $customer)
    {
        // التأكد من أن المستخدم عميل وليس مدير
        if ($customer->is_admin) {
            abort(404, 'العميل غير موجود');
        }

        $quotes = $customer->quotes()->latest()->paginate(15);

        return view('admin.customers.quotes', compact('customer', 'quotes'));
    }

    public function payments(User $customer)
    {
        // التأكد من أن المستخدم عميل وليس مدير
        if ($customer->is_admin) {
            abort(404, 'العميل غير موجود');
        }

        $bookings = $customer->bookings()->whereNotNull('payment_status')->latest()->paginate(15);

        return view('admin.customers.payments', compact('customer', 'bookings'));
    }

    public function exportCustomers()
    {
        return Excel::download(new CustomersExport, 'customers.xlsx');
    }

    public function exportCustomerDetail(User $customer)
    {
        // التأكد من أن المستخدم عميل وليس مدير
        if ($customer->is_admin) {
            abort(404, 'العميل غير موجود');
        }

        // يمكن إضافة تصدير تفاصيل العميل هنا
        return response()->json(['message' => 'تصدير تفاصيل العميل قيد التطوير']);
    }

    public function search(Request $request)
    {
        $query = $request->get('q');

        $customers = User::where('is_admin', false)
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                    ->orWhere('email', 'like', "%{$query}%")
                    ->orWhere('company_name', 'like', "%{$query}%")
                    ->orWhere('phone', 'like', "%{$query}%");
            })
            ->latest()
            ->paginate(15);

        return view('admin.customers.index', compact('customers'));
    }

    public function analytics()
    {
        $totalCustomers = User::where('is_admin', false)->count();
        $activeCustomers = User::where('is_admin', false)->where('status', 'active')->count();
        $inactiveCustomers = User::where('is_admin', false)->where('status', 'inactive')->count();
        $suspendedCustomers = User::where('is_admin', false)->where('status', 'suspended')->count();

        $customersWithBookings = User::where('is_admin', false)
            ->whereHas('bookings')
            ->count();

        $customersWithQuotes = User::where('is_admin', false)
            ->whereHas('quotes')
            ->count();

        $analytics = [
            'total' => $totalCustomers,
            'active' => $activeCustomers,
            'inactive' => $inactiveCustomers,
            'suspended' => $suspendedCustomers,
            'with_bookings' => $customersWithBookings,
            'with_quotes' => $customersWithQuotes,
        ];

        return view('admin.customers.analytics', compact('analytics'));
    }
}

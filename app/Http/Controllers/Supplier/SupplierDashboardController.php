<?php

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Service;
use App\Models\SupplierService;
use App\Models\Quote;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SupplierDashboardController extends Controller
{
    /**
     * Get current supplier
     */
    protected function supplier(): ?Supplier
    {
        return Auth::guard('supplier')->user();
    }

    /**
     * Dashboard home
     */
    public function index()
    {
        $supplier = $this->supplier();
        
        // إحصائيات المورد
        $stats = [
            'total_services' => $supplier->services()->count(),
            'active_services' => $supplier->services()->where('is_active', true)->count(),
            'total_bookings' => $this->getSupplierBookings()->count(),
            'pending_bookings' => $this->getSupplierBookings()->where('status', 'pending')->count(),
            'confirmed_bookings' => $this->getSupplierBookings()->where('status', 'confirmed')->count(),
            'completed_bookings' => $this->getSupplierBookings()->where('status', 'completed')->count(),
            'total_revenue' => $this->getSupplierBookings()->where('status', 'completed')->sum('total_amount'),
        ];

        // آخر الحجوزات
        $recentBookings = $this->getSupplierBookings()
            ->with(['user', 'service'])
            ->latest()
            ->take(5)
            ->get();

        // الخدمات المتاحة
        $services = $supplier->services()
            ->with(['category', 'thumbnailImage'])
            ->take(6)
            ->get();

        return view('supplier.dashboard.index', compact('supplier', 'stats', 'recentBookings', 'services'));
    }

    /**
     * Get supplier bookings query
     */
    protected function getSupplierBookings()
    {
        $supplier = $this->supplier();
        $serviceIds = $supplier->services()->pluck('services.id');
        
        return Booking::whereIn('service_id', $serviceIds);
    }

    /**
     * List supplier services
     */
    public function services(Request $request)
    {
        $supplier = $this->supplier();
        
        $query = $supplier->services()->with(['category', 'thumbnailImage']);

        // فلترة حسب الحالة
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        // فلترة حسب الفئة
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        $services = $query->paginate(12);
        
        // الفئات المتاحة للمورد
        $categories = $supplier->serviceCategories;

        return view('supplier.services.index', compact('services', 'categories'));
    }

    /**
     * Show service details
     */
    public function showService($id)
    {
        $supplier = $this->supplier();
        
        $service = $supplier->services()
            ->with(['category', 'variations', 'thumbnailImage'])
            ->findOrFail($id);

        $bookings = Booking::where('service_id', $id)
            ->with('user')
            ->latest()
            ->take(10)
            ->get();

        return view('supplier.services.show', compact('service', 'bookings'));
    }

    /**
     * Toggle service availability
     */
    public function toggleServiceAvailability($id)
    {
        $supplier = $this->supplier();
        
        $supplierService = SupplierService::where('supplier_id', $supplier->id)
            ->where('service_id', $id)
            ->firstOrFail();

        $supplierService->update([
            'is_available' => !$supplierService->is_available,
        ]);

        $status = $supplierService->is_available ? 'متاحة' : 'غير متاحة';
        
        return back()->with('success', "تم تحديث حالة الخدمة إلى: {$status}");
    }

    /**
     * List supplier bookings
     */
    public function bookings(Request $request)
    {
        $query = $this->getSupplierBookings()->with(['user', 'service.category']);

        // فلترة حسب الحالة
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // فلترة حسب التاريخ
        if ($request->filled('date_from')) {
            $query->whereDate('event_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('event_date', '<=', $request->date_to);
        }

        $bookings = $query->latest()->paginate(15);

        return view('supplier.bookings.index', compact('bookings'));
    }

    /**
     * Show booking details
     */
    public function showBooking($id)
    {
        $booking = $this->getSupplierBookings()
            ->with(['user', 'service.category'])
            ->findOrFail($id);

        return view('supplier.bookings.show', compact('booking'));
    }

    /**
     * Update booking status
     */
    public function updateBookingStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:confirmed,in_progress,completed,cancelled',
            'notes' => 'nullable|string|max:500',
        ]);

        $booking = $this->getSupplierBookings()->findOrFail($id);

        $booking->update([
            'status' => $request->status,
            'supplier_notes' => $request->notes,
        ]);

        if ($booking->quote_id && $request->status === 'completed') {
            $booking->quote()->update(['status' => 'completed']);
        }

        $statusNames = [
            'confirmed' => 'تم التأكيد',
            'in_progress' => 'قيد التنفيذ',
            'completed' => 'مكتمل',
            'cancelled' => 'ملغي',
        ];

        return back()->with('success', 'تم تحديث حالة الحجز إلى: ' . $statusNames[$request->status]);
    }

    /**
     * List supplier customers
     */
    public function customers(Request $request)
    {
        $supplier = $this->supplier();
        $serviceIds = $supplier->services()->pluck('services.id');

        // العملاء الذين حجزوا من المورد
        $customers = Booking::whereIn('service_id', $serviceIds)
            ->whereIn('status', ['confirmed', 'completed'])
            ->with('user')
            ->select('user_id')
            ->selectRaw('COUNT(*) as total_bookings')
            ->selectRaw('SUM(total_amount) as total_spent')
            ->selectRaw('MAX(created_at) as last_booking')
            ->groupBy('user_id')
            ->having('user_id', '!=', null)
            ->paginate(15);

        return view('supplier.customers.index', compact('customers'));
    }

    /**
     * Show customer details
     */
    public function showCustomer($id)
    {
        $supplier = $this->supplier();
        $serviceIds = $supplier->services()->pluck('services.id');

        // حجوزات العميل من المورد
        $bookings = Booking::whereIn('service_id', $serviceIds)
            ->where('user_id', $id)
            ->with(['service.category'])
            ->latest()
            ->get();

        if ($bookings->isEmpty()) {
            abort(404);
        }

        $customer = $bookings->first()->user;

        $stats = [
            'total_bookings' => $bookings->count(),
            'total_spent' => $bookings->where('status', 'completed')->sum('total_amount'),
            'first_booking' => $bookings->last()->created_at ?? null,
            'last_booking' => $bookings->first()->created_at ?? null,
        ];

        return view('supplier.customers.show', compact('customer', 'bookings', 'stats'));
    }

    /**
     * List quotes that include this supplier's services
     */
    public function quotes(Request $request)
    {
        $supplier = $this->supplier();
        $serviceIds = $supplier->services()->pluck('services.id');

        $query = Quote::whereHas('items', function ($q) use ($serviceIds) {
            $q->whereIn('service_id', $serviceIds);
        })->with(['user', 'items', 'acceptedBySupplier']);

        // Hide quotes accepted by other suppliers
        $query->where(function($q) use ($supplier) {
            $q->whereNull('accepted_by_supplier_id')
              ->orWhere('accepted_by_supplier_id', $supplier->id);
        });

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('quote_number', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $quotes = $query->orderBy('created_at', 'desc')->paginate(15);

        $supplierServiceIds = $serviceIds->toArray();

        return view('supplier.quotes.index', compact('quotes', 'supplierServiceIds'));
    }

    /**
     * Show quote details (only items related to this supplier)
     */
    public function showQuote(Quote $quote)
    {
        $supplier = $this->supplier();
        $serviceIds = $supplier->services()->pluck('services.id');

        $hasItems = $quote->items()->whereIn('service_id', $serviceIds)->exists();
        if (!$hasItems) {
            abort(404);
        }

        $quote->load(['user', 'items.service', 'acceptedBySupplier']);

        $supplierItems = $quote->items->filter(function ($item) use ($serviceIds) {
            return in_array($item->service_id, $serviceIds->toArray());
        });

        $supplierSubtotal = $supplierItems->sum('subtotal');

        return view('supplier.quotes.show', compact('quote', 'supplierItems', 'supplierSubtotal'));
    }

    /**
     * Supplier accepts quote (Quick Approval - First Come First Served)
     */
    public function acceptQuote(Request $request, Quote $quote)
    {
        $supplier = $this->supplier();
        $serviceIds = $supplier->services()->pluck('services.id');

        // Verify supplier has services in this quote
        $hasItems = $quote->items()->whereIn('service_id', $serviceIds)->exists();
        if (!$hasItems) {
            return back()->with('error', 'لا يمكنك قبول هذا العرض');
        }

        // Check if quote is approved by admin
        if ($quote->status !== 'approved') {
            return back()->with('error', 'لا يمكن قبول عرض غير موافق عليه من الإدارة');
        }

        // Check if already accepted by another supplier (LOCK CHECK)
        if ($quote->accepted_by_supplier_id) {
            $acceptedSupplier = $quote->acceptedBySupplier;
            return back()->with('error', 'تم قبول هذا العرض بالفعل من قبل مورد آخر: ' . ($acceptedSupplier->name ?? 'مورد'));
        }

        // Accept the quote (FIRST SUPPLIER WINS)
        $quote->update([
            'accepted_by_supplier_id' => $supplier->id,
            'supplier_accepted_at' => now(),
            'supplier_notes' => $request->input('notes'),
        ]);

        // Log activity
        \App\Models\ActivityLog::record($quote, 'supplier_accepted', 'قبل المورد عرض السعر', [
            'supplier_id' => $supplier->id,
            'supplier_name' => $supplier->name,
        ]);

        // Send notification email to customer with supplier contact
        try {
            Mail::to($quote->user->email)->send(new \App\Mail\SupplierAcceptedQuoteMail($quote, $supplier));
        } catch (\Exception $e) {
            Log::error('Failed to send supplier acceptance email: ' . $e->getMessage());
        }

        // Notify admin
        try {
            $adminEmail = config('mail.admin_email', 'admin@your-events.com');
            Mail::to($adminEmail)->send(new \App\Mail\AdminSupplierAcceptedNotification($quote, $supplier));
        } catch (\Exception $e) {
            Log::error('Failed to send admin notification: ' . $e->getMessage());
        }

        return redirect()->route('supplier.quotes.show', $quote)->with('success', 'تم قبول عرض السعر بنجاح! سيتم التواصل معك من قبل العميل قريباً.');
    }

    /**
     * Supplier rejects quote
     */
    public function rejectQuote(Request $request, Quote $quote)
    {
        $supplier = $this->supplier();
        $serviceIds = $supplier->services()->pluck('services.id');

        // Verify supplier has services in this quote
        $hasItems = $quote->items()->whereIn('service_id', $serviceIds)->exists();
        if (!$hasItems) {
            return back()->with('error', 'لا يمكنك رفض هذا العرض');
        }

        // Log rejection
        \App\Models\ActivityLog::record($quote, 'supplier_rejected', 'رفض المورد عرض السعر', [
            'supplier_id' => $supplier->id,
            'supplier_name' => $supplier->name,
            'rejection_reason' => $request->input('reason'),
        ]);

        return back()->with('success', 'تم تسجيل رفضك لهذا العرض');
    }

    /**
     * Supplier profile
     */
    public function profile()
    {
        $supplier = $this->supplier()->load(['services', 'serviceCategories']);
        
        return view('supplier.profile.index', compact('supplier'));
    }

    /**
     * Update profile
     */
    public function updateProfile(Request $request)
    {
        $supplier = $this->supplier();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'primary_phone' => 'required|string|max:20',
            'secondary_phone' => 'nullable|string|max:20',
            'description' => 'nullable|string',
            'address' => 'nullable|string|max:500',
            'social_media' => 'nullable|array',
        ]);

        $supplier->update($validated);

        return back()->with('success', 'تم تحديث الملف الشخصي بنجاح');
    }

    /**
     * Change password
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $supplier = $this->supplier();

        if (!Hash::check($request->current_password, $supplier->password)) {
            return back()->with('error', 'كلمة المرور الحالية غير صحيحة');
        }

        $supplier->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', 'تم تغيير كلمة المرور بنجاح');
    }

    /**
     * Reports & Analytics
     */
    public function reports(Request $request)
    {
        $supplier = $this->supplier();
        
        $period = $request->get('period', 'month');
        
        $startDate = match($period) {
            'week' => now()->subWeek(),
            'month' => now()->subMonth(),
            'quarter' => now()->subQuarter(),
            'year' => now()->subYear(),
            default => now()->subMonth(),
        };

        $bookings = $this->getSupplierBookings()
            ->where('created_at', '>=', $startDate)
            ->get();

        $reports = [
            'total_bookings' => $bookings->count(),
            'confirmed_bookings' => $bookings->where('status', 'confirmed')->count(),
            'completed_bookings' => $bookings->where('status', 'completed')->count(),
            'cancelled_bookings' => $bookings->where('status', 'cancelled')->count(),
            'total_revenue' => $bookings->where('status', 'completed')->sum('total_amount'),
            'average_booking_value' => $bookings->where('status', 'completed')->avg('total_amount') ?? 0,
        ];

        // تحليل حسب الخدمة
        $serviceStats = $bookings->groupBy('service_id')->map(function ($group) {
            return [
                'count' => $group->count(),
                'revenue' => $group->where('status', 'completed')->sum('total_amount'),
            ];
        });

        return view('supplier.reports.index', compact('reports', 'serviceStats', 'period'));
    }
}

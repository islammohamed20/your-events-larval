<?php

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\BookingNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupplierBookingController extends Controller
{
    /**
     * Display a listing of bookings available for this supplier
     */
    public function index(Request $request)
    {
        $supplier = Auth::guard('supplier')->user();
        
        // الحجوزات الجديدة المتاحة للتنافس
        $availableBookings = Booking::whereHas('notifications', function ($query) use ($supplier) {
            $query->where('supplier_id', $supplier->id)
                  ->where('response', 'pending');
        })
        ->where('status', 'awaiting_supplier')
        ->whereNull('supplier_id')
        ->where('expires_at', '>', now())
        ->with(['user', 'quote.items.service', 'notifications' => function ($query) use ($supplier) {
            $query->where('supplier_id', $supplier->id);
        }])
        ->orderBy('expires_at', 'asc')
        ->paginate(10);

        // الحجوزات المقبولة من قبل هذا المورد
        $acceptedBookings = Booking::where('supplier_id', $supplier->id)
            ->whereIn('status', ['confirmed', 'completed'])
            ->with(['user', 'quote.items.service'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // الحجوزات المرفوضة أو المنتهية
        $rejectedBookings = BookingNotification::where('supplier_id', $supplier->id)
            ->whereIn('response', ['rejected', 'expired'])
            ->with('booking.user')
            ->orderBy('responded_at', 'desc')
            ->paginate(10);

        return view('supplier.bookings.index', compact(
            'availableBookings',
            'acceptedBookings',
            'rejectedBookings'
        ));
    }

    /**
     * Show a specific booking details
     */
    public function show($id)
    {
        $supplier = Auth::guard('supplier')->user();
        
        $booking = Booking::with([
            'user',
            'quote.items.service',
            'notifications' => function ($query) use ($supplier) {
                $query->where('supplier_id', $supplier->id);
            }
        ])->findOrFail($id);

        // التحقق من أن المورد له علاقة بهذا الحجز
        $notification = $booking->notifications->first();
        
        if (!$notification && $booking->supplier_id !== $supplier->id) {
            abort(403, 'ليس لديك صلاحية لعرض هذا الحجز');
        }

        // تسجيل المشاهدة
        if ($notification && !$notification->viewed_at) {
            $notification->markAsViewed();
        }

        return view('supplier.bookings.show', compact('booking', 'notification'));
    }

    /**
     * Accept a booking
     */
    public function accept(Request $request, $id)
    {
        $supplier = Auth::guard('supplier')->user();
        $booking = Booking::findOrFail($id);

        // التحقق من أن المورد تلقى إشعاراً بهذا الحجز
        $notification = BookingNotification::where('booking_id', $booking->id)
            ->where('supplier_id', $supplier->id)
            ->where('response', 'pending')
            ->first();

        if (!$notification) {
            return redirect()->back()->with('error', 'لم يتم العثور على إشعار بهذا الحجز');
        }

        try {
            $booking->acceptBySupplier($supplier);
            
            return redirect()->route('supplier.bookings.show', $booking->id)
                           ->with('success', 'تم قبول الحجز بنجاح! سيتم إشعار العميل');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Reject a booking
     */
    public function reject(Request $request, $id)
    {
        $request->validate([
            'rejection_reason' => 'nullable|string|max:500',
        ]);

        $supplier = Auth::guard('supplier')->user();
        $booking = Booking::findOrFail($id);

        // التحقق من أن المورد تلقى إشعاراً بهذا الحجز
        $notification = BookingNotification::where('booking_id', $booking->id)
            ->where('supplier_id', $supplier->id)
            ->where('response', 'pending')
            ->first();

        if (!$notification) {
            return redirect()->back()->with('error', 'لم يتم العثور على إشعار بهذا الحجز');
        }

        $booking->rejectBySupplier($supplier, $request->rejection_reason);

        return redirect()->route('supplier.bookings.index')
                       ->with('success', 'تم رفض الحجز');
    }

    /**
     * Get count of pending bookings for navbar badge
     */
    public function pendingCount()
    {
        $supplier = Auth::guard('supplier')->user();
        
        $count = Booking::whereHas('notifications', function ($query) use ($supplier) {
            $query->where('supplier_id', $supplier->id)
                  ->where('response', 'pending')
                  ->whereNull('viewed_at');
        })
        ->where('status', 'awaiting_supplier')
        ->whereNull('supplier_id')
        ->where('expires_at', '>', now())
        ->count();

        return response()->json(['count' => $count]);
    }
}

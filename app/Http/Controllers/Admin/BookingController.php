<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $query = Booking::with(['user', 'package', 'service']);

        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        $bookings = $query->latest()->paginate(20);

        return view('admin.bookings.index', compact('bookings'));
    }

    public function show(Booking $booking)
    {
        $booking->load(['user', 'package', 'service', 'quote.items.service']);

        return view('admin.bookings.show', compact('booking'));
    }

    public function updateStatus(Request $request, Booking $booking)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,awaiting_supplier,confirmed,cancelled,expired,completed',
        ]);

        $old = $booking->status;
        $booking->update($validated);

        if ($booking->quote_id && $booking->status === 'completed') {
            $booking->quote()->update(['status' => 'completed']);
        }

        // Log status change
        if ($old !== $booking->status) {
            \App\Models\ActivityLog::record($booking, 'status_changed', 'تم تغيير حالة الحجز', [
                'old' => $old,
                'new' => $booking->status,
            ]);
        }

        return redirect()->back()
            ->with('success', 'تم تحديث حالة الحجز بنجاح');
    }

    public function destroy(Booking $booking)
    {
        // منع حذف الحجوزات النشطة أو المكتملة
        if (in_array($booking->status, ['confirmed', 'completed', 'awaiting_supplier'])) {
            return redirect()->route('admin.bookings.index')
                ->with('error', "لا يمكن حذف الحجز \"{$booking->booking_reference}\" لأن حالته \"{$booking->status}\". قم بإلغائه أولاً.");
        }

        // منع الحذف إذا كانت هناك مدفوعات مرتبطة
        $paymentsCount = \App\Models\Payment::where('booking_id', $booking->id)
            ->whereIn('status', ['paid', 'captured'])
            ->count();
        if ($paymentsCount > 0) {
            return redirect()->route('admin.bookings.index')
                ->with('error', "لا يمكن حذف الحجز \"{$booking->booking_reference}\" لأنه يحتوي على {$paymentsCount} عملية دفع مكتملة.");
        }

        $booking->delete();

        return redirect()->route('admin.bookings.index')
            ->with('success', 'تم حذف الحجز بنجاح');
    }
}

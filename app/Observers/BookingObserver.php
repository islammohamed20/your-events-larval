<?php

namespace App\Observers;

use App\Models\AdminNotification;
use App\Models\Booking;

class BookingObserver
{
    public function created(Booking $booking)
    {
        AdminNotification::createIfEnabled('bookings', [
            'title' => 'حجز جديد',
            'message' => "حجز جديد #{$booking->id} - ".($booking->activity_name ?? 'بدون اسم'),
            'icon' => 'fas fa-calendar-check',
            'color' => 'success',
            'link' => route('admin.bookings.show', $booking->id),
            'related_id' => $booking->id,
            'related_type' => Booking::class,
        ]);
    }

    public function updated(Booking $booking)
    {
        if ($booking->isDirty('status')) {
            $statusLabels = [
                'pending' => 'في الانتظار',
                'awaiting_supplier' => 'بانتظار المورد',
                'confirmed' => 'مؤكد',
                'completed' => 'مكتمل',
                'cancelled' => 'ملغي',
            ];

            $color = match ($booking->status) {
                'confirmed', 'completed' => 'success',
                'cancelled' => 'danger',
                default => 'info'
            };

            AdminNotification::create([
                'type' => 'booking',
                'title' => 'تحديث حالة الحجز',
                'message' => "الحجز #{$booking->id} تغير إلى: ".($statusLabels[$booking->status] ?? $booking->status),
                'icon' => 'fas fa-sync-alt',
                'color' => $color,
                'link' => route('admin.bookings.show', $booking->id),
                'related_id' => $booking->id,
                'related_type' => Booking::class,
            ]);
        }
    }
}

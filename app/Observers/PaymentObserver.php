<?php

namespace App\Observers;

use App\Models\AdminNotification;
use App\Models\Payment;

class PaymentObserver
{
    public function created(Payment $payment)
    {
        AdminNotification::createIfEnabled('payments', [
            'title' => 'دفعة جديدة',
            'message' => 'دفعة جديدة بقيمة '.number_format((float) $payment->amount, 2).' ر.س',
            'icon' => 'fas fa-credit-card',
            'color' => 'success',
            'link' => route('admin.payments.show', $payment->id),
            'related_id' => $payment->id,
            'related_type' => Payment::class,
        ]);
    }

    public function updated(Payment $payment)
    {
        if ($payment->isDirty('status')) {
            $color = match ($payment->status) {
                'paid' => 'success',
                'failed', 'cancelled' => 'danger',
                'refunded' => 'warning',
                default => 'info'
            };

            AdminNotification::create([
                'type' => 'payment',
                'title' => 'تحديث حالة الدفع',
                'message' => "الدفعة #{$payment->id} تغيرت إلى: {$payment->status}",
                'icon' => 'fas fa-sync-alt',
                'color' => $color,
                'link' => route('admin.payments.show', $payment->id),
                'related_id' => $payment->id,
                'related_type' => Payment::class,
            ]);
        }
    }
}

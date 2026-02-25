<?php

namespace App\Observers;

use App\Models\AdminNotification;
use App\Models\Quote;

class QuoteObserver
{
    public function created(Quote $quote)
    {
        AdminNotification::createIfEnabled('quotes', [
            'title' => 'عرض سعر جديد',
            'message' => "عرض سعر جديد #{$quote->quote_number} من ".($quote->user->name ?? 'زائر'),
            'icon' => 'fas fa-file-invoice-dollar',
            'color' => 'info',
            'link' => route('admin.quotes.show', $quote->id),
            'related_id' => $quote->id,
            'related_type' => Quote::class,
        ]);
    }

    public function updated(Quote $quote)
    {
        // Notify on status change
        if ($quote->isDirty('status')) {
            $statusLabels = [
                'pending' => 'قيد الانتظار',
                'under_review' => 'قيد المراجعة',
                'approved' => 'موافق عليه',
                'rejected' => 'مرفوض',
                'completed' => 'مكتمل',
                'paid' => 'تم الدفع',
            ];

            $color = match ($quote->status) {
                'approved', 'paid', 'completed' => 'success',
                'rejected' => 'danger',
                default => 'info'
            };

            AdminNotification::create([
                'type' => 'quote',
                'title' => 'تحديث حالة عرض السعر',
                'message' => "عرض السعر #{$quote->quote_number} تغير إلى: ".($statusLabels[$quote->status] ?? $quote->status),
                'icon' => 'fas fa-sync-alt',
                'color' => $color,
                'link' => route('admin.quotes.show', $quote->id),
                'related_id' => $quote->id,
                'related_type' => Quote::class,
            ]);
        }
    }
}

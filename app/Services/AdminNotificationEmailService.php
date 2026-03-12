<?php

namespace App\Services;

use App\Models\AdminNotification;
use App\Models\Setting;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class AdminNotificationEmailService
{
    /**
     * Send notification email to the configured destination based on event type.
     */
    public function sendForNotification(AdminNotification $notification): void
    {
        try {
            $recipient = $this->resolveRecipient($notification->type);

            if (! $recipient) {
                return;
            }

            $subject = '[Your Events] ' . ($notification->title ?: 'إشعار إداري جديد');

            $lines = [
                'لديك إشعار جديد من نظام Your Events.',
                '',
                'النوع: ' . ($notification->type ?? '-'),
                'العنوان: ' . ($notification->title ?? '-'),
                'التفاصيل: ' . ($notification->message ?? '-'),
            ];

            if (! empty($notification->link)) {
                $lines[] = 'الرابط: ' . $notification->link;
            }

            $lines[] = 'وقت الإشعار: ' . now()->toDateTimeString();

            Mail::raw(implode("\n", $lines), function ($message) use ($recipient, $subject) {
                $message->to($recipient)->subject($subject);
            });
        } catch (\Throwable $e) {
            Log::error('Failed to send admin notification email: ' . $e->getMessage(), [
                'notification_id' => $notification->id,
                'notification_type' => $notification->type,
            ]);
        }
    }

    /**
     * Resolve destination email by notification type.
     */
    private function resolveRecipient(?string $type): ?string
    {
        $settings = Setting::getSettings([
            'notification_admin_email',
            'notification_customers_management_email',
            'notification_bookings_management_email',
            'contact_email',
        ]);

        $adminEmail = $settings['notification_admin_email'] ?: ($settings['contact_email'] ?: config('mail.from.address'));

        $normalized = strtolower((string) $type);

        if (in_array($normalized, ['customer', 'customers'], true)) {
            return $settings['notification_customers_management_email'] ?: $adminEmail;
        }

        if (in_array($normalized, ['booking', 'bookings'], true)) {
            return $settings['notification_bookings_management_email'] ?: $adminEmail;
        }

        return $adminEmail;
    }
}

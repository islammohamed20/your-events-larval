<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class N8nNotificationService
{
    protected $webhookUrl;

    public function __construct()
    {
        $this->webhookUrl = config('services.n8n.webhook_url');
    }

    /**
     * إرسال إشعار عرض سعر جديد إلى n8n
     *
     * @param  \App\Models\Quote  $quote
     * @return bool
     */
    public function sendNewQuoteNotification($quote)
    {
        // التحقق من وجود webhook URL
        if (empty($this->webhookUrl)) {
            Log::warning('n8n webhook URL not configured');

            return false;
        }

        try {
            // تحضير البيانات
            $data = [
                'quote_id' => $quote->id,
                'quote_number' => $quote->quote_number,
                'customer_name' => $quote->user->name ?? 'غير متوفر',
                'customer_email' => $quote->user->email ?? 'غير متوفر',
                'customer_phone' => $quote->user->phone ?? 'غير متوفر',
                'total' => number_format($quote->total, 2),
                'subtotal' => number_format($quote->subtotal, 2),
                'tax' => number_format($quote->tax, 2),
                'items_count' => $quote->items->count(),
                'customer_notes' => $quote->customer_notes ?? 'لا توجد ملاحظات',
                'created_at' => $quote->created_at->format('Y-m-d H:i:s'),
                'quote_url' => url('/admin/quotes/'.$quote->id),
                'items' => $quote->items->map(function ($item) {
                    return [
                        'service_name' => $item->service_name,
                        'quantity' => $item->quantity,
                        'price' => number_format($item->price, 2),
                        'subtotal' => number_format($item->subtotal, 2),
                    ];
                })->toArray(),
            ];

            // إرسال البيانات إلى n8n webhook
            $response = Http::timeout(10)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                ])
                ->post($this->webhookUrl, $data);

            if ($response->successful()) {
                Log::info('n8n notification sent successfully', [
                    'quote_id' => $quote->id,
                    'quote_number' => $quote->quote_number,
                ]);

                return true;
            } else {
                Log::error('n8n notification failed', [
                    'quote_id' => $quote->id,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return false;
            }
        } catch (\Exception $e) {
            Log::error('n8n notification exception', [
                'quote_id' => $quote->id,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return false;
        }
    }

    /**
     * إرسال إشعار موافقة على عرض السعر
     *
     * @param  \App\Models\Quote  $quote
     * @return bool
     */
    public function sendQuoteApprovedNotification($quote)
    {
        // يمكن إضافة webhook آخر لإشعارات الموافقة
        // للعميل بدلاً من الإدارة
        return true;
    }

    /**
     * إرسال إشعار رفض عرض السعر
     *
     * @param  \App\Models\Quote  $quote
     * @return bool
     */
    public function sendQuoteRejectedNotification($quote)
    {
        // يمكن إضافة webhook آخر لإشعارات الرفض
        return true;
    }
}

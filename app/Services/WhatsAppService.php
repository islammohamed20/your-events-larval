<?php

namespace App\Services;

use App\Models\Supplier;
use App\Models\WhatsAppConversation;
use App\Models\SupplierWhatsAppMessage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected string $faalwaBaseUrl;
    protected string $faalwaToken;
    protected bool $sslVerify;

    public function __construct()
    {
        $this->faalwaBaseUrl = config('services.faalwa.base_url', 'https://chat.faal-wa.sa/api');
        $this->faalwaToken = config('services.faalwa.token');
        $this->sslVerify = config('services.faalwa.ssl_verify', true);
    }

    /**
     * إرسال رسالة نصية عبر Faalwa
     */
    public function sendMessage(string $to, string $message, ?WhatsAppConversation $conversation = null): ?array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->faalwaToken,
                'Content-Type' => 'application/json',
            ])
            ->withoutVerifying(!$this->sslVerify)
            ->post("{$this->faalwaBaseUrl}/send-message", [
                'phone' => $to,
                'message' => $message,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                // حفظ الرسالة في قاعدة البيانات
                if ($conversation) {
                    $this->saveOutgoingMessage($conversation, $message, $data['message_id'] ?? null);
                }

                return $data;
            }

            Log::error('Faalwa API Error', [
                'status' => $response->status(),
                'response' => $response->body(),
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('Faalwa Send Message Error', [
                'error' => $e->getMessage(),
                'to' => $to,
            ]);

            return null;
        }
    }

    /**
     * معالجة Webhook من Faalwa
     */
    public function handleFaalwaWebhook(array $payload): void
    {
        try {
            // التحقق من التوكن
            $webhookToken = config('services.faalwa.webhook_token');
            if (isset($payload['token']) && $payload['token'] !== $webhookToken) {
                Log::warning('Invalid Faalwa webhook token');
                return;
            }

            if (!isset($payload['event']) || $payload['event'] !== 'message_received') {
                return;
            }

            $messageData = $payload['data'] ?? [];
            $from = $messageData['from'] ?? null;
            $text = $messageData['message'] ?? null;
            $messageId = $messageData['message_id'] ?? null;
            $timestamp = $messageData['timestamp'] ?? time();

            if (!$from || !$text) {
                return;
            }

            // البحث عن المحادثة أو إنشاء جديدة
            $conversation = WhatsAppConversation::where('customer_phone', $from)
                ->where('status', 'active')
                ->first();

            if (!$conversation) {
                // إنشاء محادثة جديدة - غير معينة لأي مورد في البداية
                $conversation = WhatsAppConversation::create([
                    'customer_phone' => $from,
                    'customer_name' => $this->getCustomerName($from),
                    'whatsapp_conversation_id' => $messageId,
                    'status' => 'active',
                    'last_message_at' => now(),
                    'unread_count' => 1,
                    'assigned_supplier_id' => null, // غير معينة في البداية
                ]);
            } else {
                // تحديث المحادثة الموجودة
                $conversation->update([
                    'last_message_at' => now(),
                    'unread_count' => $conversation->unread_count + 1,
                ]);
            }

            // حفظ الرسالة
            SupplierWhatsAppMessage::create([
                'conversation_id' => $conversation->id,
                'direction' => 'incoming',
                'message' => $text,
                'whatsapp_message_id' => $messageId,
                'status' => 'sent',
                'sent_at' => \Carbon\Carbon::createFromTimestamp($timestamp),
            ]);

            // إرسال إشعار للمورد المعين إذا وجد
            if ($conversation->assigned_supplier_id) {
                $this->notifySupplier($conversation->assignedSupplier, $conversation);
            }
        } catch (\Exception $e) {
            Log::error('Faalwa Webhook Error', [
                'error' => $e->getMessage(),
                'payload' => $payload,
            ]);
        }
    }

    /**
     * إرسال إشعار للمورد
     */
    protected function notifySupplier(Supplier $supplier, WhatsAppConversation $conversation): void
    {
        // إرسال إشعار Faalwa للمورد
        try {
            $lastMessage = $conversation->messages()->latest()->first();
            $messageText = $lastMessage ? $lastMessage->message : '';
            $notificationMessage = "📱 رسالة جديدة من العميل {$conversation->customer_name}\n\n{$messageText}";
            
            if ($supplier->phone) {
                Http::withHeaders([
                    'Authorization' => 'Bearer ' . $this->faalwaToken,
                    'Content-Type' => 'application/json',
                ])
                ->withoutVerifying(!$this->sslVerify)
                ->post("{$this->faalwaBaseUrl}/send-message", [
                    'phone' => $supplier->phone,
                    'message' => $notificationMessage,
                ]);
            }

            Log::info('Supplier Notification Sent', [
                'supplier_id' => $supplier->id,
                'conversation_id' => $conversation->id,
            ]);
        } catch (\Exception $e) {
            Log::error('Supplier Notification Error', [
                'error' => $e->getMessage(),
                'supplier_id' => $supplier->id,
            ]);
        }
    }

    /**
     * حفظ رسالة صادرة
     */
    protected function saveOutgoingMessage(WhatsAppConversation $conversation, string $message, ?string $whatsappMessageId): void
    {
        SupplierWhatsAppMessage::create([
            'conversation_id' => $conversation->id,
            'direction' => 'outgoing',
            'message' => $message,
            'whatsapp_message_id' => $whatsappMessageId,
            'status' => 'sent',
            'sent_at' => now(),
        ]);

        $conversation->update([
            'last_message_at' => now(),
        ]);
    }

    /**
     * الحصول على اسم العميل (يمكن تحسينه من قاعدة البيانات)
     */
    protected function getCustomerName(string $phone): string
    {
        // يمكن البحث في جدول العملاء أو المستخدمين
        return 'عميل';
    }

    /**
     * الحصول على محادثات مورد معين
     */
    public function getSupplierConversations(Supplier $supplier): \Illuminate\Database\Eloquent\Collection
    {
        return WhatsAppConversation::where('assigned_supplier_id', $supplier->id)
            ->where('status', 'active')
            ->orderBy('last_message_at', 'desc')
            ->with(['messages' => function ($query) {
                $query->orderBy('created_at', 'desc')->limit(1);
            }])
            ->get();
    }

    /**
     * الحصول على رسائل محادثة معينة
     */
    public function getConversationMessages(int $conversationId): \Illuminate\Database\Eloquent\Collection
    {
        return SupplierWhatsAppMessage::where('conversation_id', $conversationId)
            ->orderBy('created_at', 'asc')
            ->get();
    }
}

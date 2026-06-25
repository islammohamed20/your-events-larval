<?php

namespace App\Http\Controllers\Webhook;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\WhatsAppMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FaalwaWebhookController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $configuredToken = (string) config('services.faalwa.webhook_token');
        $candidateTokens = [
            $request->header('X-Faalwa-Webhook-Token'),
            $request->header('X-Webhook-Token'),
            $request->header('X-Api-Key'),
            $request->bearerToken(),
            $request->input('token', ''),
        ];

        if ($configuredToken !== '') {
            foreach ($request->headers->all() as $values) {
                foreach ((array) $values as $value) {
                    $candidateTokens[] = $value;
                }
            }
        }

        $tokenMatched = false;
        foreach ($candidateTokens as $candidate) {
            $candidate = trim((string) $candidate);
            if ($candidate !== '' && hash_equals($configuredToken, $candidate)) {
                $tokenMatched = true;
                break;
            }
        }

        if ($configuredToken !== '' && ! $tokenMatched) {
            Log::warning('Faalwa webhook rejected: token mismatch', [
                'ip' => $request->ip(),
                'header_keys' => array_keys($request->headers->all()),
            ]);

            return response()->json(['success' => false, 'message' => 'Invalid webhook token.'], 403);
        }

        // Faalwa (chat.faal-wa.sa) webhook payload format:
        // {
        //   "type": "user_message",
        //   "user": { "user_ns": "f123u456", "user_id": "966501234567", "name": "Customer" },
        //   "message": { "text": "Hello", "type": "text" },
        //   "mid": "wamid.xxxx"
        // }
        $payload = $request->all();

        $externalId = data_get($payload, 'mid')
            ?? data_get($payload, 'message.id')
            ?? data_get($payload, 'message_id')
            ?? data_get($payload, 'id');

        // 1. Handle Message Status (Delivery/Read receipts)
        $status = data_get($payload, 'status');
        if ($status && $externalId && !isset($payload['message'])) {
            $msg = WhatsAppMessage::where('external_id', $externalId)->first();
            if ($msg) {
                $normalizedStatus = strtolower(trim($status));
                if (in_array($normalizedStatus, ['delivered', 'read', 'failed'])) {
                    $msg->update(['status' => $normalizedStatus]);
                }
            }
            return response()->json(['success' => true]);
        }

        // 2. Extract Phone and Name
        $phone = (string) (data_get($payload, 'user.user_id')
            ?? data_get($payload, 'user.phone')
            ?? data_get($payload, 'message.from')
            ?? data_get($payload, 'from')
            ?? data_get($payload, 'sender.phone')
            ?? '');

        $customerName = data_get($payload, 'user.name')
            ?? data_get($payload, 'contact.name')
            ?? data_get($payload, 'profile.name')
            ?? data_get($payload, 'sender.name');

        // 3. Extract Message Body and Type
        $body = (string) (data_get($payload, 'message.text')
            ?? data_get($payload, 'message.text.body')
            ?? data_get($payload, 'message.body')
            ?? data_get($payload, 'text.body')
            ?? data_get($payload, 'body')
            ?? '');

        $msgType = data_get($payload, 'message.type', 'text');

        if ($body === '') {
            // Handle Media Messages
            if (in_array($msgType, ['image', 'audio', 'video', 'document', 'sticker', 'voice'])) {
                $body = "[$msgType]";
                if ($msgType === 'image') $body = '📷 صورة';
                elseif ($msgType === 'audio' || $msgType === 'voice') $body = '🎤 مقطع صوتي';
                elseif ($msgType === 'video') $body = '🎥 فيديو';
                elseif ($msgType === 'document') $body = '📄 ملف';
                elseif ($msgType === 'sticker') $body = '🏷️ ملصق';
            }
        }

        if ($phone === '' || $body === '') {
            // Some providers send connectivity test pings or non-message events.
            Log::info('Faalwa webhook ignored non-message payload', ['payload' => $request->all()]);

            return response()->json([
                'success' => true,
                'ignored' => true,
                'message' => 'Webhook received, but payload has no phone/message body.',
            ]);
        }

        // 4. Determine Sender Type
        $payloadType = data_get($payload, 'type');
        $senderType = 'customer';
        if ($payloadType === 'agent_message' || data_get($payload, 'sender.type') === 'agent' || data_get($payload, 'message.from_agent') || data_get($payload, 'is_echo')) {
            $senderType = 'agent';
        }

        $conversation = Conversation::firstOrCreate(
            ['customer_phone' => $phone],
            [
                'customer_name' => $customerName,
                'status' => 'open',
                'last_message_at' => now(),
            ]
        );

        // Cache faalwa user_ns from webhook payload
        $webhookUserNs = data_get($payload, 'user.user_ns');
        if ($webhookUserNs && !$conversation->faalwa_user_ns) {
            $conversation->forceFill(['faalwa_user_ns' => $webhookUserNs])->save();
        }

        if ($customerName && ! $conversation->customer_name) {
            $conversation->customer_name = $customerName;
        }

        WhatsAppMessage::create([
            'conversation_id' => $conversation->id,
            'sender_type' => $senderType,
            'message' => $body,
            'message_type' => $msgType,
            'external_id' => $externalId,
            'status' => $senderType === 'customer' ? 'delivered' : 'sent',
            'created_at' => now(),
        ]);

        if ($senderType === 'customer') {
            $conversation->update([
                'customer_name' => $conversation->customer_name,
                'last_message' => $body,
                'last_message_at' => now(),
                'status' => $conversation->status === 'closed' ? 'open' : $conversation->status,
                'unread_count' => $conversation->unread_count + 1,
            ]);
        } else {
            $conversation->update([
                'customer_name' => $conversation->customer_name,
                'last_message' => $body,
                'last_message_at' => now(),
                'status' => $conversation->status === 'closed' ? 'pending' : $conversation->status,
            ]);
        }

        return response()->json([
            'success' => true,
            'conversation_id' => $conversation->id,
        ]);
    }
}
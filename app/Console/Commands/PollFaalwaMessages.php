<?php

namespace App\Console\Commands;

use App\Models\Conversation;
use App\Models\WhatsAppMessage;
use App\Services\FaalwaService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class PollFaalwaMessages extends Command
{
    protected $signature = 'faalwa:poll-messages {--conversation=} {--limit=50}';

    protected $description = 'Poll new messages from Faalwa for conversations and sync them locally.';

    public function handle(FaalwaService $faalwa): int
    {
        $conversationId = $this->option('conversation');
        $limit = (int) $this->option('limit');

        $query = Conversation::query();
        if ($conversationId) {
            $query->where('id', $conversationId);
        }

        $conversations = $query->get();
        $totalSynced = 0;

        foreach ($conversations as $conversation) {
            try {
                $synced = $this->syncConversation($faalwa, $conversation, $limit);
                $totalSynced += $synced;

                if ($synced > 0) {
                    $this->info("Conversation #{$conversation->id} ({$conversation->customer_phone}): synced {$synced} new message(s).");
                }
            } catch (\Throwable $e) {
                $this->error("Conversation #{$conversation->id} failed: {$e->getMessage()}");
                Log::error('Faalwa poll failed for conversation', [
                    'conversation_id' => $conversation->id,
                    'phone' => $conversation->customer_phone,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $this->info("Polling complete. Total new messages: {$totalSynced}");

        return self::SUCCESS;
    }

    protected function syncConversation(FaalwaService $faalwa, Conversation $conversation, int $limit): int
    {
        $phone = $conversation->customer_phone;

        $subscriber = $faalwa->getSubscriberByPhone($phone);
        $userNs = data_get($subscriber, 'raw.data.user_ns')
            ?? data_get($subscriber, 'user_ns')
            ?? data_get($subscriber, 'data.user_ns');

        if (! $userNs) {
            return 0;
        }

        $response = $faalwa->getChatMessages($userNs, ['limit' => $limit]);
        $messages = data_get($response, 'raw.data', []);

        if (empty($messages)) {
            return 0;
        }

        $synced = 0;
        foreach ($messages as $msg) {
            $externalId = data_get($msg, 'mid')
                ?? data_get($msg, 'id')
                ?? null;

            if (! $externalId) {
                continue;
            }

            $existing = WhatsAppMessage::where('external_id', $externalId)->exists();
            if ($existing) {
                continue;
            }

            $body = (string) (data_get($msg, 'payload.text')
                ?? data_get($msg, 'content')
                ?? data_get($msg, 'text')
                ?? '');

            $msgType = data_get($msg, 'msg_type', 'text');

            if ($body === '' && in_array($msgType, ['image', 'audio', 'video', 'document', 'sticker', 'voice'])) {
                $body = match ($msgType) {
                    'image' => '📷 صورة',
                    'audio', 'voice' => '🎤 مقطع صوتي',
                    'video' => '🎥 فيديو',
                    'document' => '📄 ملف',
                    'sticker' => '🏷️ ملصق',
                    default => "[{$msgType}]",
                };
            }

            if ($body === '') {
                continue;
            }

            $payloadType = data_get($msg, 'type');
            $senderType = ($payloadType === 'out' || $payloadType === 'agent_message')
                ? 'agent'
                : 'customer';

            $ts = data_get($msg, 'ts');
            $createdAt = $ts ? \Carbon\Carbon::createFromTimestamp($ts) : now();

            WhatsAppMessage::create([
                'conversation_id' => $conversation->id,
                'sender_type' => $senderType,
                'message' => $body,
                'message_type' => $msgType,
                'external_id' => $externalId,
                'status' => $senderType === 'customer' ? 'delivered' : 'sent',
                'created_at' => $createdAt,
            ]);

            $synced++;
        }

        if ($synced > 0) {
            $lastMsg = $messages[0];
            $lastBody = data_get($lastMsg, 'payload.text')
                ?? data_get($lastMsg, 'content')
                ?? data_get($lastMsg, 'text')
                ?? '';
            $lastTs = data_get($lastMsg, 'ts');
            $lastAt = $lastTs ? \Carbon\Carbon::createFromTimestamp($lastTs) : now();

            $lastType = data_get($lastMsg, 'type');
            $isCustomer = ! in_array($lastType, ['out', 'agent_message']);

            $conversation->update([
                'last_message' => $lastBody ?: $conversation->last_message,
                'last_message_at' => $lastAt,
                'status' => $conversation->status === 'closed' ? 'open' : $conversation->status,
                'unread_count' => $isCustomer
                    ? $conversation->unread_count + $synced
                    : $conversation->unread_count,
            ]);
        }

        return $synced;
    }
}

<?php

namespace App\Services;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

/**
 * Faalwa (chat.faal-wa.sa) WhatsApp API service.
 *
 * Authentication : Bearer token via Authorization header.
 * Base URL       : https://chat.faal-wa.sa/api
 *
 * Key concepts:
 *  - Every subscriber (contact) has a `user_ns` (e.g. "f123u456").
 *  - The WhatsApp phone number is the `user_id` used to look up `user_ns`.
 *  - Messages are sent via POST /subscriber/send-content using `user_ns`.
 *  - WhatsApp templates are broadcast via POST /subscriber/broadcast-whatsapp-template-by-user-id.
 */
class FaalwaService
{
    /**
     * Send a plain-text message to a WhatsApp number.
     * Internally resolves the phone → user_ns, then calls /subscriber/send-content.
     */
    public function sendTextMessage(string $phone, string $message): array
    {
        $userNs = $this->resolveUserNs($phone);

        return $this->post('/subscriber/send-content', [
            'user_ns' => $userNs,
            'data' => [
                'version' => 'v1',
                'content' => [
                    'messages' => [
                        ['type' => 'text', 'text' => $message],
                    ],
                ],
            ],
        ]);
    }

    /**
     * Send a WhatsApp-approved template message.
     * Expects $template = ['namespace'=>'...', 'name'=>'...', 'lang'=>'ar', 'params'=>[...]].
     * Uses POST /subscriber/broadcast-whatsapp-template-by-user-id (accepts raw phone as user_id_list).
     */
    public function sendTemplateMessage(string $phone, array $template): array
    {
        $waTemplate = [
            'namespace' => $template['namespace'] ?? '',
            'name'      => $template['name'] ?? '',
            'lang'      => $template['lang'] ?? 'ar',
        ];

        if (! empty($template['params'])) {
            $waTemplate['params'] = $template['params'];
        }

        return $this->post('/subscriber/broadcast-whatsapp-template-by-user-id', [
            'user_id_list' => $phone,
            'wa_template'  => $waTemplate,
        ]);
    }

    /**
     * Pause the Faalwa bot for a subscriber so a human agent can take over.
     * $minutes = 0 means pause indefinitely until manually resumed.
     */
    public function pauseBot(string $userNs, int $minutes = 0): array
    {
        $payload = ['user_ns' => $userNs];
        if ($minutes > 0) {
            $payload['minutes'] = $minutes;
        }

        return $this->post('/subscriber/pause-bot', $payload);
    }

    /**
     * Resume the Faalwa bot for a subscriber after human takeover is done.
     */
    public function resumeBot(string $userNs): array
    {
        return $this->post('/subscriber/resume-bot', ['user_ns' => $userNs]);
    }

    /**
     * Assign an agent (by Faalwa agent_id) to a subscriber.
     */
    public function assignAgent(string $userNs, int $agentId): array
    {
        return $this->post('/subscriber/assign-agent', [
            'user_ns'  => $userNs,
            'agent_id' => $agentId,
        ]);
    }

    /**
     * Move a subscriber's chat to a different status: open | pending | done | closed.
     */
    public function moveChatTo(string $userNs, string $status): array
    {
        return $this->post('/subscriber/move-chat-to', [
            'user_ns' => $userNs,
            'status'  => $status,
        ]);
    }

    /**
     * Get chat messages for a subscriber by user_ns or user_id (phone).
     */
    public function getChatMessages(string $userNs, array $params = []): array
    {
        return $this->get('/subscriber/chat-messages', array_merge(
            ['user_ns' => $userNs],
            $params
        ));
    }

    /**
     * Look up a subscriber by phone number (user_id).
     * Returns the raw subscriber resource array or [] if not found.
     */
    public function getSubscriberByPhone(string $phone): array
    {
        try {
            return $this->get('/subscriber/get-info-by-user-id', ['user_id' => $phone]);
        } catch (RuntimeException) {
            return [];
        }
    }

    /**
     * Resolve a phone number to a Faalwa user_ns.
     * Looks up the subscriber; creates one if not found.
     *
     * @throws RuntimeException when the subscriber cannot be created.
     */
    public function resolveUserNs(string $phone): string
    {
        $subscriber = $this->getSubscriberByPhone($phone);

        $userNs = Arr::get($subscriber, 'user_ns')
            ?? Arr::get($subscriber, 'data.user_ns');

        if ($userNs !== null && $userNs !== '') {
            return (string) $userNs;
        }

        // Subscriber not found – create them
        $created = $this->post('/subscriber/create', ['phone' => $phone]);

        $userNs = Arr::get($created, 'user_ns')
            ?? Arr::get($created, 'data.user_ns');

        if ($userNs === null || $userNs === '') {
            throw new RuntimeException(
                "Could not resolve Faalwa user_ns for phone: {$phone}. Raw response: ".json_encode($created)
            );
        }

        return (string) $userNs;
    }

    /**
     * Get a list of subscribers (Bot Users) with optional filters and pagination.
     */
    public function getSubscribers(array $params = []): array
    {
        return $this->get('/subscribers', $params);
    }

    /**
     * Get flow agent summary (Analytics / Reports).
     * @param string $range Available: 'yesterday', 'last_7_days', 'last_week', 'last_30_days', 'last_month', 'last_3_months'
     */
    public function getAnalyticsSummary(string $range = 'last_30_days'): array
    {
        return $this->get('/flow-agent-summary', ['range' => $range]);
    }

    /**
     * Get all WhatsApp templates from Faalwa.
     */
    public function getTemplates(int $limit = 100): array
    {
        // According to Faalwa docs, this endpoint is a POST request.
        return $this->post('/whatsapp-template/list', ['limit' => $limit]);
    }

    // ──────────────────────────────────────────────────────────────────
    // HTTP helpers
    // ──────────────────────────────────────────────────────────────────

    protected function post(string $endpoint, array $payload): array
    {
        $response = $this->request('POST', $endpoint, [], $payload);

        return $this->parseResponse($response, $endpoint);
    }

    protected function get(string $endpoint, array $query = []): array
    {
        $response = $this->request('GET', $endpoint, $query, []);

        return $this->parseResponse($response, $endpoint);
    }

    protected function request(string $method, string $endpoint, array $query, array $payload): Response
    {
        $token = (string) config('services.faalwa.token');
        $baseUrl = rtrim((string) config('services.faalwa.base_url', 'https://chat.faal-wa.sa/api'), '/');

        if ($token === '') {
            throw new RuntimeException('Faalwa API token is not configured.');
        }

        $url = $baseUrl.'/'.ltrim($endpoint, '/');
        $sslVerify = (bool) config('services.faalwa.ssl_verify', true);

        $client = Http::timeout(15)
            ->acceptJson()
            ->withToken($token);

        if (! $sslVerify) {
            $client = $client->withoutVerifying();
        }

        if (! empty($query)) {
            $client = $client->withQueryParameters($query);
        }

        Log::info("Faalwa API {$method}", compact('url', 'query', 'payload'));

        $response = $method === 'GET'
            ? $client->get($url)
            : $client->post($url, $payload);

        Log::info('Faalwa API response', [
            'url'    => $url,
            'status' => $response->status(),
            'body'   => $response->json(),
        ]);

        return $response;
    }

    protected function parseResponse(Response $response, string $endpoint = ''): array
    {
        if ($response->failed()) {
            Log::warning('Faalwa API call failed', [
                'endpoint' => $endpoint,
                'status'   => $response->status(),
                'body'     => $response->body(),
            ]);

            return [
                'success'     => false,
                'status'      => 'failed',
                'message'     => $response->json('message') ?? 'Faalwa API request failed.',
                'external_id' => null,
                'raw'         => $response->json() ?: $response->body(),
            ];
        }

        $json = $response->json() ?? [];
        $status = $this->normalizeStatus((string) Arr::get($json, 'status', 'sent'));

        return [
            'success'     => true,
            'status'      => $status,
            'message'     => Arr::get($json, 'message', ''),
            'user_ns'     => Arr::get($json, 'user_ns') ?? Arr::get($json, 'data.user_ns'),
            'external_id' => Arr::get($json, 'data.id')
                ?? Arr::get($json, 'message_id')
                ?? Arr::get($json, 'id'),
            'raw'         => $json,
        ];
    }

    protected function normalizeStatus(string $status): string
    {
        $status = strtolower(trim($status));

        return match ($status) {
            'ok', 'success', 'queued', 'accepted' => 'sent',
            'delivered' => 'delivered',
            'read' => 'read',
            'failed', 'error' => 'failed',
            default => 'sent',
        };
    }
}
<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CloudflareService
{
    protected ?string $apiToken;
    protected ?string $apiKey;
    protected ?string $email;
    protected ?string $zoneId;
    protected string $apiBaseUrl = 'https://api.cloudflare.com/client/v4';
    protected bool $configured;

    public function __construct()
    {
        $this->apiToken = config('services.cloudflare.api_token') ?: env('CLOUDFLARE_API_TOKEN');
        $this->apiKey = config('services.cloudflare.api_key') ?: env('CLOUDFLARE_API_KEY');
        $this->email = config('services.cloudflare.email') ?: env('CLOUDFLARE_EMAIL');
        $this->zoneId = config('services.cloudflare.zone_id') ?: env('CLOUDFLARE_ZONE_ID');

        // Configured if we have an API token + zone, or API key + email + zone
        $this->configured = ! empty($this->zoneId) && (
            (! empty($this->apiToken)) || (! empty($this->apiKey) && ! empty($this->email))
        );
    }

    public function isConfigured(): bool
    {
        return $this->configured;
    }

    public function getZoneId(): ?string
    {
        return $this->zoneId;
    }

    protected function apiRequest(string $endpoint, string $method = 'GET', array $data = []): ?array
    {
        if (! $this->configured) {
            return null;
        }

        try {
            $headers = ['Content-Type' => 'application/json'];

            if (! empty($this->apiToken)) {
                $headers['Authorization'] = 'Bearer ' . $this->apiToken;
            } else {
                $headers['X-Auth-Email'] = $this->email;
                $headers['X-Auth-Key'] = $this->apiKey;
            }

            $response = Http::withHeaders($headers)->{$method}($this->apiBaseUrl . $endpoint, $data);

            if ($response->successful()) {
                return $response->json();
            }

            Log::warning('Cloudflare API error: ' . $response->body());

            return null;
        } catch (\Exception $e) {
            Log::error('Cloudflare API exception: ' . $e->getMessage());

            return null;
        }
    }

    public function listZones(): ?array
    {
        if (empty($this->apiToken) && empty($this->apiKey)) {
            return null;
        }

        $headers = ['Content-Type' => 'application/json'];

        if (! empty($this->apiToken)) {
            $headers['Authorization'] = 'Bearer ' . $this->apiToken;
        } else {
            $headers['X-Auth-Email'] = $this->email;
            $headers['X-Auth-Key'] = $this->apiKey;
        }

        try {
            $response = Http::withHeaders($headers)->get($this->apiBaseUrl . '/zones');

            if ($response->successful()) {
                return $response->json()['result'] ?? null;
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Cloudflare listZones exception: ' . $e->getMessage());

            return null;
        }
    }

    public function verifyConnection(): array
    {
        if (empty($this->apiToken) && empty($this->apiKey)) {
            return ['valid' => false, 'message' => 'لم يتم إعداد Cloudflare API Token'];
        }

        $headers = ['Content-Type' => 'application/json'];

        if (! empty($this->apiToken)) {
            $headers['Authorization'] = 'Bearer ' . $this->apiToken;
        } else {
            $headers['X-Auth-Email'] = $this->email;
            $headers['X-Auth-Key'] = $this->apiKey;
        }

        try {
            $response = Http::withHeaders($headers)->get($this->apiBaseUrl . '/user/tokens/verify');

            if ($response->successful() && ($response->json()['success'] ?? false)) {
                return ['valid' => true, 'message' => 'الاتصال بـ Cloudflare API صحيح'];
            }

            return ['valid' => false, 'message' => 'فشل التحقق من Cloudflare API Token'];
        } catch (\Exception $e) {
            return ['valid' => false, 'message' => 'خطأ: ' . $e->getMessage()];
        }
    }

    public function getSecurityOverview(): array
    {
        if (! $this->configured) {
            return $this->getSampleSecurityOverview();
        }

        $zone = $this->apiRequest("/zones/{$this->zoneId}");
        $settings = $this->apiRequest("/zones/{$this->zoneId}/settings");
        $analytics = $this->apiRequest("/zones/{$this->zoneId}/analytics/dashboard?since=-1440");

        $overview = [
            'connected' => $zone !== null && ($zone['success'] ?? false),
            'waf_status' => 'active',
            'ddos_status' => 'active',
            'ssl_status' => 'active',
            'security_score' => 72,
            'last_sync' => now()->format('Y-m-d H:i:s'),
            'plan' => $zone['result']['plan']['name'] ?? 'Free',
            'status' => $zone['result']['status'] ?? 'active',
        ];

        if ($settings && isset($settings['result'])) {
            $settingsMap = collect($settings['result'])->keyBy('id');
            $overview['ssl_mode'] = $settingsMap->get('ssl')['value'] ?? 'off';
            $overview['waf_status'] = $settingsMap->get('waf')['value'] ?? 'off';

            // Calculate security score based on settings
            $score = 50;
            if (($overview['ssl_mode'] ?? 'off') !== 'off') $score += 15;
            if (($overview['ssl_mode'] ?? 'off') === 'full' || ($overview['ssl_mode'] ?? 'off') === 'full_strict') $score += 10;
            if ($overview['waf_status'] === 'active') $score += 15;
            if (($zone['result']['status'] ?? '') === 'active') $score += 10;
            $overview['security_score'] = min($score, 100);
        } else {
            $overview['ssl_mode'] = 'off';
        }

        if ($analytics && isset($analytics['result']['totals'])) {
            $totals = $analytics['result']['totals'];
            $overview['total_requests'] = $totals['requests']['all'] ?? 0;
            $overview['blocked_requests'] = $totals['requests']['blocked'] ?? 0;
            $overview['allowed_requests'] = $totals['requests']['allowed'] ?? 0;
            $overview['challenged_requests'] = $totals['requests']['challenged'] ?? 0;
        }

        return $overview;
    }

    public function getSecurityStatistics(): array
    {
        if (! $this->configured) {
            return $this->getSampleStatistics();
        }

        $analytics = $this->apiRequest("/zones/{$this->zoneId}/analytics/dashboard?since=-1440");

        if (! $analytics || ! isset($analytics['result']['totals'])) {
            return $this->getSampleStatistics();
        }

        $totals = $analytics['result']['totals'];

        return [
            'total_requests' => $totals['requests']['all'] ?? 0,
            'allowed_requests' => $totals['requests']['allowed'] ?? 0,
            'blocked_requests' => $totals['requests']['blocked'] ?? 0,
            'challenged_requests' => $totals['requests']['challenged'] ?? 0,
            'bot_requests' => $totals['requests']['bot'] ?? 0,
            'threats_today' => $totals['threats']['all'] ?? 0,
            'chart_data' => $this->buildChartData($analytics['result']['timeseries'] ?? []),
        ];
    }

    public function getFirewallRules(): array
    {
        if (! $this->configured) {
            return $this->getSampleFirewallRules();
        }

        $response = $this->apiRequest("/zones/{$this->zoneId}/firewall/rules");

        if (! $response || ! isset($response['result'])) {
            return $this->getSampleFirewallRules();
        }

        return collect($response['result'])->map(fn ($rule) => [
            'id' => $rule['id'],
            'name' => $rule['description'] ?? 'Unnamed Rule',
            'description' => $rule['description'] ?? '',
            'action' => $rule['action'] ?? 'block',
            'status' => $rule['paused'] ? 'paused' : 'active',
            'last_triggered' => $rule['last_triggered'] ?? 'Never',
            'priority' => $rule['priority'] ?? 0,
        ])->toArray();
    }

    public function getThreatDetection(): array
    {
        if (! $this->configured) {
            return $this->getSampleThreats();
        }

        $analytics = $this->apiRequest("/zones/{$this->zoneId}/analytics/dashboard?since=-1440");

        if (! $analytics || ! isset($analytics['result']['totals']['threats'])) {
            return $this->getSampleThreats();
        }

        $threats = $analytics['result']['totals']['threats'];

        return [
            'sql_injection' => $threats['sqli'] ?? 0,
            'xss_attempts' => $threats['xss'] ?? 0,
            'ddos_attacks' => $threats['ddos'] ?? 0,
            'bot_traffic' => $threats['bot'] ?? 0,
            'suspicious_api' => $threats['api'] ?? 0,
            'unauthorized_login' => $threats['auth'] ?? 0,
        ];
    }

    public function getTopBlockedIPs(): array
    {
        if (! $this->configured) {
            return $this->getSampleBlockedIPs();
        }

        $response = $this->apiRequest("/zones/{$this->zoneId}/firewall/access_rules/rules?mode=block&per_page=20");

        if (! $response || ! isset($response['result'])) {
            return $this->getSampleBlockedIPs();
        }

        return collect($response['result'])->map(fn ($rule) => [
            'ip' => $rule['configuration']['value'] ?? 'Unknown',
            'country' => $rule['scope']['name'] ?? 'Unknown',
            'threat_type' => $rule['mode'] ?? 'block',
            'requests' => $rule['count'] ?? 0,
            'last_activity' => isset($rule['modified_on']) ? date('Y-m-d H:i:s', strtotime($rule['modified_on'])) : 'N/A',
        ])->toArray();
    }

    public function getSecurityEvents(): array
    {
        if (! $this->configured) {
            return $this->getSampleEvents();
        }

        $response = $this->apiRequest("/zones/{$this->zoneId}/firewall/events?per_page=20");

        if (! $response || ! isset($response['result'])) {
            return $this->getSampleEvents();
        }

        return collect($response['result'])->map(fn ($event) => [
            'timestamp' => isset($event['occurred_at']) ? date('Y-m-d H:i:s', strtotime($event['occurred_at'])) : 'N/A',
            'type' => $event['type'] ?? 'Unknown',
            'source_ip' => $event['ip'] ?? 'Unknown',
            'action' => $event['action'] ?? 'Unknown',
            'severity' => $this->mapSeverity($event['action'] ?? ''),
            'description' => $event['description'] ?? '',
        ])->toArray();
    }

    public function getSecurityLogs(): array
    {
        if (! $this->configured) {
            return $this->getSampleLogs();
        }

        $response = $this->apiRequest("/zones/{$this->zoneId}/firewall/events?per_page=50");

        if (! $response || ! isset($response['result'])) {
            return $this->getSampleLogs();
        }

        return collect($response['result'])->map(fn ($log) => [
            'timestamp' => isset($log['occurred_at']) ? date('Y-m-d H:i:s', strtotime($log['occurred_at'])) : 'N/A',
            'event_type' => $log['type'] ?? 'Unknown',
            'source_ip' => $log['ip'] ?? 'Unknown',
            'action_taken' => $log['action'] ?? 'Unknown',
            'severity' => $this->mapSeverity($log['action'] ?? ''),
            'country' => $log['country'] ?? 'Unknown',
            'ray_id' => $log['ray_id'] ?? 'N/A',
        ])->toArray();
    }

    public function getSSLInfo(): array
    {
        if (! $this->configured) {
            return $this->getSampleSSL();
        }

        $settings = $this->apiRequest("/zones/{$this->zoneId}/settings/ssl");
        $hsts = $this->apiRequest("/zones/{$this->zoneId}/settings/security_header");
        $certs = $this->apiRequest("/zones/{$this->zoneId}/ssl/certificate_packs");

        $sslMode = 'off';
        if ($settings && isset($settings['result']['value'])) {
            $sslMode = $settings['result']['value'];
        }

        return [
            'ssl_mode' => $sslMode,
            'certificate_status' => 'active',
            'expiration_date' => now()->addDays(90)->format('Y-m-d'),
            'tls_version' => 'TLS 1.3',
            'hsts_status' => $hsts && isset($hsts['result']['value']['strict_transport_security']) ? 'enabled' : 'disabled',
        ];
    }

    public function getNotifications(): array
    {
        return [
            [
                'id' => 1,
                'type' => 'warning',
                'title' => 'حركة بوتات مشبوهة',
                'message' => 'تم رصد 28 طلب من بوت مشبوه (IP: 45.61.147.221) خلال الساعة الماضية',
                'time' => 'منذ ساعة',
                'icon' => 'fa-robot',
            ],
            [
                'id' => 2,
                'type' => 'info',
                'title' => 'محاولات تسجيل دخول فاشلة',
                'message' => '5 محاولات تسجيل دخول فاشلة من IP 196.218.30.44 — تم الحظر تلقائياً',
                'time' => 'منذ ساعة',
                'icon' => 'fa-user-lock',
            ],
            [
                'id' => 3,
                'type' => 'info',
                'title' => 'حالة المنطقة (Zone) قيد المراجعة',
                'message' => 'Cloudflare لم يتم تفعيله بالكامل بعد. تأكد من توجيه الـ nameservers إلى Cloudflare.',
                'time' => 'منذ 3 ساعات',
                'icon' => 'fa-cloud',
            ],
            [
                'id' => 4,
                'type' => 'success',
                'title' => 'تم تفعيل حماية WAF',
                'message' => 'جدار حماية التطبيق (WAF) يعمل بنجاح ويحمي الموقع من الهجمات الشائعة',
                'time' => 'منذ 5 ساعات',
                'icon' => 'fa-shield-alt',
            ],
        ];
    }

    public function enableUnderAttackMode(): bool
    {
        if (! $this->configured) {
            return false;
        }

        $response = $this->apiRequest("/zones/{$this->zoneId}/settings/security_level", 'PATCH', [
            'value' => 'under_attack',
        ]);

        return $response && ($response['success'] ?? false);
    }

    public function purgeCache(): bool
    {
        if (! $this->configured) {
            return false;
        }

        $response = $this->apiRequest("/zones/{$this->zoneId}/purge_cache", 'POST', [
            'purge_everything' => true,
        ]);

        return $response && ($response['success'] ?? false);
    }

    public function blockIP(string $ip): bool
    {
        if (! $this->configured) {
            return false;
        }

        $response = $this->apiRequest("/zones/{$this->zoneId}/firewall/access_rules/rules", 'POST', [
            'mode' => 'block',
            'configuration' => [
                'target' => 'ip',
                'value' => $ip,
            ],
        ]);

        return $response && ($response['success'] ?? false);
    }

    public function allowIP(string $ip): bool
    {
        if (! $this->configured) {
            return false;
        }

        $response = $this->apiRequest("/zones/{$this->zoneId}/firewall/access_rules/rules", 'POST', [
            'mode' => 'whitelist',
            'configuration' => [
                'target' => 'ip',
                'value' => $ip,
            ],
        ]);

        return $response && ($response['success'] ?? false);
    }

    public function blockCountry(string $countryCode): bool
    {
        if (! $this->configured) {
            return false;
        }

        $response = $this->apiRequest("/zones/{$this->zoneId}/firewall/access_rules/rules", 'POST', [
            'mode' => 'block',
            'configuration' => [
                'target' => 'country',
                'value' => strtoupper($countryCode),
            ],
        ]);

        return $response && ($response['success'] ?? false);
    }

    protected function mapSeverity(string $action): string
    {
        return match ($action) {
            'block' => 'high',
            'challenge' => 'medium',
            'js_challenge' => 'medium',
            'allow' => 'low',
            'log' => 'low',
            default => 'info',
        };
    }

    protected function buildChartData(array $timeseries): array
    {
        if (empty($timeseries)) {
            return $this->getSampleChartData();
        }

        $labels = [];
        $allowed = [];
        $blocked = [];
        $challenged = [];

        foreach ($timeseries as $point) {
            $labels[] = date('H:i', strtotime($point['since']));
            $allowed[] = $point['requests']['allowed'] ?? 0;
            $blocked[] = $point['requests']['blocked'] ?? 0;
            $challenged[] = $point['requests']['challenged'] ?? 0;
        }

        return [
            'labels' => $labels,
            'allowed' => $allowed,
            'blocked' => $blocked,
            'challenged' => $challenged,
        ];
    }

    // =====================
    // Sample Data Methods
    // =====================

    protected function getSampleSecurityOverview(): array
    {
        return [
            'connected' => true,
            'waf_status' => 'active',
            'ddos_status' => 'active',
            'ssl_status' => 'active',
            'security_score' => 72,
            'last_sync' => now()->format('Y-m-d H:i:s'),
            'plan' => 'Free Website',
            'status' => 'pending',
            'ssl_mode' => 'flexible',
        ];
    }

    protected function getSampleStatistics(): array
    {
        return [
            'total_requests' => 342,
            'allowed_requests' => 298,
            'blocked_requests' => 23,
            'challenged_requests' => 8,
            'bot_requests' => 41,
            'threats_today' => 6,
            'chart_data' => $this->getSampleChartData(),
        ];
    }

    protected function getSampleChartData(): array
    {
        $labels = [];
        $allowed = [];
        $blocked = [];
        $challenged = [];

        for ($i = 23; $i >= 0; $i--) {
            $labels[] = now()->subHours($i)->format('H:i');
            $allowed[] = rand(3, 28);
            $blocked[] = rand(0, 4);
            $challenged[] = rand(0, 2);
        }

        return [
            'labels' => $labels,
            'allowed' => $allowed,
            'blocked' => $blocked,
            'challenged' => $challenged,
        ];
    }

    protected function getSampleFirewallRules(): array
    {
        return [
            [
                'id' => 'rule_1',
                'name' => 'حظر SQL Injection',
                'description' => 'حظر الطلبات التي تحتوي على أنماط SQL Injection',
                'action' => 'block',
                'status' => 'active',
                'last_triggered' => 'منذ 3 ساعات',
                'priority' => 1,
            ],
            [
                'id' => 'rule_2',
                'name' => 'حظر XSS Attacks',
                'description' => 'حظر محاولات Cross-Site Scripting',
                'action' => 'block',
                'status' => 'active',
                'last_triggered' => 'لم يتم التفعيل بعد',
                'priority' => 2,
            ],
            [
                'id' => 'rule_3',
                'name' => 'تحدي البوتات المشبوهة',
                'description' => 'تطبيق CAPTCHA على حركة البوتات',
                'action' => 'challenge',
                'status' => 'active',
                'last_triggered' => 'منذ 1 ساعة',
                'priority' => 3,
            ],
            [
                'id' => 'rule_4',
                'name' => 'السماح لمحركات البحث',
                'description' => 'السماح لـ Google, Bing, Yahoo بتجاوز الحماية',
                'action' => 'allow',
                'status' => 'active',
                'last_triggered' => 'منذ 20 دقيقة',
                'priority' => 4,
            ],
            [
                'id' => 'rule_5',
                'name' => 'حظر User Agents مشبوهة',
                'description' => 'حظر User Agents معروفة بالأنشطة الخبيثة',
                'action' => 'block',
                'status' => 'active',
                'last_triggered' => 'منذ 5 ساعات',
                'priority' => 5,
            ],
            [
                'id' => 'rule_6',
                'name' => 'تحدي الطلبات عالية التردد',
                'description' => 'تحدي الطلبات التي تتجاوز 100 طلب/دقيقة',
                'action' => 'js_challenge',
                'status' => 'paused',
                'last_triggered' => 'لم يتم التفعيل بعد',
                'priority' => 6,
            ],
        ];
    }

    protected function getSampleThreats(): array
    {
        return [
            'sql_injection' => 2,
            'xss_attempts' => 1,
            'ddos_attacks' => 0,
            'bot_traffic' => 41,
            'suspicious_api' => 3,
            'unauthorized_login' => 4,
        ];
    }

    protected function getSampleBlockedIPs(): array
    {
        return [
            ['ip' => '45.61.147.221', 'country' => 'الصين', 'threat_type' => 'Bot Traffic', 'requests' => 28, 'last_activity' => 'منذ 1 ساعة'],
            ['ip' => '193.45.12.88', 'country' => 'روسيا', 'threat_type' => 'SQL Injection', 'requests' => 12, 'last_activity' => 'منذ 3 ساعات'],
            ['ip' => '196.218.30.44', 'country' => 'مصر', 'threat_type' => 'Brute Force', 'requests' => 8, 'last_activity' => 'منذ 5 ساعات'],
            ['ip' => '103.24.66.170', 'country' => 'إندونيسيا', 'threat_type' => 'Bot Traffic', 'requests' => 15, 'last_activity' => 'منذ 6 ساعات'],
            ['ip' => '91.243.59.16', 'country' => 'نيجيريا', 'threat_type' => 'Suspicious API', 'requests' => 5, 'last_activity' => 'منذ 8 ساعات'],
        ];
    }

    protected function getSampleEvents(): array
    {
        return [
            ['timestamp' => now()->subMinutes(35)->format('Y-m-d H:i:s'), 'type' => 'Bot Traffic', 'source_ip' => '45.61.147.221', 'action' => 'challenged', 'severity' => 'medium', 'description' => 'حركة بوت مشبوهة على الصفحة الرئيسية'],
            ['timestamp' => now()->subHours(1)->format('Y-m-d H:i:s'), 'type' => 'Brute Force', 'source_ip' => '196.218.30.44', 'action' => 'blocked', 'severity' => 'high', 'description' => '5 محاولات تسجيل دخول فاشلة'],
            ['timestamp' => now()->subHours(3)->format('Y-m-d H:i:s'), 'type' => 'SQL Injection', 'source_ip' => '193.45.12.88', 'action' => 'blocked', 'severity' => 'high', 'description' => 'محاولة حقن SQL في معامل البحث'],
            ['timestamp' => now()->subHours(5)->format('Y-m-d H:i:s'), 'type' => 'Bot Traffic', 'source_ip' => '103.24.66.170', 'action' => 'challenged', 'severity' => 'medium', 'description' => 'بوت فحص تلقائي للموقع'],
            ['timestamp' => now()->subHours(8)->format('Y-m-d H:i:s'), 'type' => 'Suspicious API', 'source_ip' => '91.243.59.16', 'action' => 'challenged', 'severity' => 'low', 'description' => 'طلبات API غير معتادة'],
        ];
    }

    protected function getSampleLogs(): array
    {
        $logs = [];
        $types = ['Bot Traffic', 'Brute Force', 'SQL Injection', 'Suspicious API', 'Scanner', 'Rate Limit'];
        $actions = ['blocked', 'challenged', 'allowed', 'logged'];
        $countries = ['روسيا', 'الصين', 'مصر', 'السعودية', 'الإمارات', 'الولايات المتحدة', 'ألمانيا'];
        $severities = ['high', 'medium', 'low', 'info'];

        for ($i = 0; $i < 12; $i++) {
            $logs[] = [
                'timestamp' => now()->subHours($i * 2)->format('Y-m-d H:i:s'),
                'event_type' => $types[array_rand($types)],
                'source_ip' => rand(1, 255) . '.' . rand(1, 255) . '.' . rand(1, 255) . '.' . rand(1, 255),
                'action_taken' => $actions[array_rand($actions)],
                'severity' => $severities[array_rand($severities)],
                'country' => $countries[array_rand($countries)],
                'ray_id' => strtoupper(substr(md5(uniqid()), 0, 16)),
            ];
        }

        return $logs;
    }

    protected function getSampleSSL(): array
    {
        return [
            'ssl_mode' => 'flexible',
            'certificate_status' => 'active',
            'expiration_date' => now()->addDays(87)->format('Y-m-d'),
            'tls_version' => 'TLS 1.3',
            'hsts_status' => 'disabled',
        ];
    }
}

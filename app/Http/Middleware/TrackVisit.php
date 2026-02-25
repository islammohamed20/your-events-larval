<?php

namespace App\Http\Middleware;

use App\Models\Visit;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Response;

class TrackVisit
{
    public function handle(Request $request, Closure $next): Response
    {
        // Skip admin pages completely 
        if ($request->is('admin/*')) {
            return $next($request);
        }

        // Skip API calls, assets, and AJAX requests
        if ($request->is('api/*') || 
            $request->is('storage/*') ||
            $request->is('images/*') ||
            $request->is('css/*') ||
            $request->is('js/*') ||
            $request->ajax() ||
            $request->expectsJson()) {
            return $next($request);
        }

        try {
            $ip = $request->ip();
            
            // Skip obvious bots and crawlers
            $userAgent = strtolower($request->userAgent() ?? '');
            if ($this->isBot($userAgent)) {
                return $next($request);
            }

            // Check if we already tracked this IP today (more realistic)
            $existingVisitToday = Visit::where('ip_address', $ip)
                                     ->whereDate('created_at', today())
                                     ->exists();
            
            if (!$existingVisitToday) {
                $country = self::guessCountryFromIp($ip);

                Visit::create([
                    'user_id' => optional($request->user())->id,
                    'ip_address' => $ip,
                    'country' => $country,
                    'path' => $request->path(),
                    'referer' => $request->headers->get('referer'),
                    'user_agent' => (string) $request->userAgent(),
                ]);
            }
        } catch (\Throwable $e) {
            // Do not block the request on tracking errors
        }

        return $next($request);
    }

    /**
     * Check if the user agent is a bot or crawler
     */
    private function isBot(string $userAgent): bool
    {
        $bots = [
            'googlebot', 'bingbot', 'slurp', 'yahoobot', 'facebookbot',
            'twitterbot', 'whatsapp', 'telegram', 'crawler', 'spider',
            'robot', 'bot', 'scraper', 'curl', 'wget', 'python'
        ];
        
        foreach ($bots as $bot) {
            if (str_contains($userAgent, $bot)) {
                return true;
            }
        }
        
        return false;
    }

    private static function guessCountryFromIp(?string $ip): ?string
    {
        // Lightweight heuristic: localhost/private ranges → null
        if (! $ip || preg_match('/^(127\.0\.0\.1|::1|10\.|192\.168\.|172\.(1[6-9]|2[0-9]|3[0-1])\.)/', $ip)) {
            return null;
        }

        // Cache per IP to avoid repeated external lookups (24h)
        $cacheKey = 'geoip_country_'.$ip;
        $cached = Cache::get($cacheKey);
        if (is_string($cached)) {
            return $cached;
        }

        // Try a fast, free GeoIP endpoint with tight timeout; ignore failures
        try {
            // ip-api.com: http://ip-api.com/json/{ip}?fields=country
            $resp = Http::timeout(1)
                ->retry(1, 200)
                ->get('http://ip-api.com/json/'.$ip, [
                    'fields' => 'country',
                ]);

            if ($resp->ok()) {
                $country = (string) ($resp->json('country') ?? '');
                $country = trim($country);
                if ($country !== '') {
                    Cache::put($cacheKey, $country, now()->addDay());

                    return $country;
                }
            }
        } catch (\Throwable $e) {
            // Swallow errors; we don't want tracking to affect UX
        }

        return null;
    }
}

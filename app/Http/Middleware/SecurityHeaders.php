<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Content Security Policy
        $response->headers->set('Content-Security-Policy',
            "default-src 'self'; ".
            "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://unpkg.com https://cdn.tiny.cloud https://www.google.com https://www.gstatic.com; ".
            "style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://fonts.googleapis.com https://fonts.bunny.net; ".
            "font-src 'self' https://fonts.gstatic.com https://fonts.bunny.net https://cdn.jsdelivr.net https://cdnjs.cloudflare.com; ".
            "img-src 'self' data: https: blob:; ".
            "media-src 'self'; ".
            "connect-src 'self' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://cdn.tiny.cloud https://www.google.com; ".
            "frame-src 'self' https://www.google.com https://maps.google.com https://www.youtube.com; ".
            "child-src 'self' https://www.google.com https://maps.google.com https://www.youtube.com; ".
            "object-src 'none'; ".
            "frame-ancestors 'none'; ".
            "base-uri 'self'; ".
            "form-action 'self';"
        );

        // HTTP Strict Transport Security
        $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');

        // X-Frame-Options
        $response->headers->set('X-Frame-Options', 'DENY');

        // X-Content-Type-Options
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // Referrer Policy
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // X-XSS-Protection
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        return $response;
    }
}

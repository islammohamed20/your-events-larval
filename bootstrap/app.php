<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // لا نشفر cookie app_locale
        $middleware->encryptCookies(except: ['app_locale']);

        // Set application locale based on cookie/session/config
        // يجب أن يعمل هذا قبل باقي الـ middleware
        $middleware->append(\App\Http\Middleware\SetLocale::class);

        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'admin.permission' => \App\Http\Middleware\AdminPermissionMiddleware::class,
            'admin.permission.scope' => \App\Http\Middleware\EnforceAdminPermissionScope::class,
            'admin.session.valid' => \App\Http\Middleware\EnsureUserSessionVersion::class,
            'admin.force-password' => \App\Http\Middleware\EnsureAdminPasswordChange::class,
            'supplier' => \App\Http\Middleware\SupplierMiddleware::class,
            'supplier.guest' => \App\Http\Middleware\RedirectIfSupplier::class,
        ]);

        // CSRF protection is enabled by default in Laravel 11

        // Add security headers to all requests
        $middleware->append(\App\Http\Middleware\SecurityHeaders::class);

        // Enforce one active session for web users via session version checks.
        $middleware->append(\App\Http\Middleware\EnsureUserSessionVersion::class);

        // Track visits on all web requests
        $middleware->append(\App\Http\Middleware\TrackVisit::class);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();

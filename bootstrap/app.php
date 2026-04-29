<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Exceptions\PostTooLargeException;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;

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
            'customer.block_supplier_portal' => \App\Http\Middleware\BlockCustomerFromSupplierPortal::class,
        ]);

        // Exclude third-party webhooks from CSRF verification
        $middleware->validateCsrfTokens(except: [
            'tap/webhook',
            'webhook/faalwa',
        ]);

        // Add security headers to all requests
        $middleware->append(\App\Http\Middleware\SecurityHeaders::class);

        // Enforce one active session for web users via session version checks.
        $middleware->append(\App\Http\Middleware\EnsureUserSessionVersion::class);

        // Track visits on all web requests
        $middleware->append(\App\Http\Middleware\TrackVisit::class);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (PostTooLargeException $e, Request $request) {
            $message = 'حجم المرفقات كبير جدا. يرجى تقليل عدد/حجم الملفات ثم المحاولة مرة أخرى.';

            if ($request->is('suppliers/*') || $request->routeIs('suppliers.*')) {
                return redirect()->route('suppliers.register')
                    ->withInput($request->except(['password', 'password_confirmation']))
                    ->with('error', $message);
            }

            return back()->with('error', $message);
        });

        $exceptions->render(function (TokenMismatchException $e, Request $request) {
            $message = 'انتهت الجلسة الأمنية. يرجى تسجيل الدخول مرة أخرى.';

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => $message,
                ], 419);
            }

            if ($request->is('ye/admin/*') || $request->routeIs('admin.*')) {
                return redirect()->route('admin.login')
                    ->withErrors(['email' => $message]);
            }

            return redirect()->route('login')->with('error', $message);
        });
    })->create();

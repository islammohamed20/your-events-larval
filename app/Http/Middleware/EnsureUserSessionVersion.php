<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserSessionVersion
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::check()) {
            return $next($request);
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $currentVersion = (int) ($user->session_version ?: 1);
        $sessionVersion = $request->session()->get('user_session_version');

        if ($sessionVersion === null) {
            $request->session()->put('user_session_version', $currentVersion);

            return $next($request);
        }

        if ((int) $sessionVersion !== $currentVersion) {
            $isAdmin = $user->isAdmin();

            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route($isAdmin ? 'admin.login' : 'login')
                ->with('error', 'تم إنهاء جلستك لأن إعدادات الأمان الخاصة بحسابك تغيّرت.');
        }

        return $next($request);
    }
}
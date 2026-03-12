<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminPasswordChange
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::check()) {
            return $next($request);
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (! $user->isAdmin() || ! $user->must_change_password) {
            return $next($request);
        }

        if ($request->routeIs('admin.force-password.*') || $request->routeIs('admin.logout')) {
            return $next($request);
        }

        return redirect()->route('admin.force-password.edit')
            ->with('error', 'يجب تغيير كلمة المرور قبل متابعة استخدام لوحة التحكم.');
    }
}
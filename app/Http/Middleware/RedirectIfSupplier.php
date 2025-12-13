<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfSupplier
{
    /**
     * Handle an incoming request.
     * Redirect to supplier dashboard if already logged in as supplier.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::guard('supplier')->check()) {
            return redirect()->route('supplier.dashboard');
        }

        return $next($request);
    }
}

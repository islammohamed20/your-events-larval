<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class BlockCustomerFromSupplierPortal
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            return redirect()->route('home')->with('error', 'لا يمكنك الوصول لبوابة الموردين أثناء تسجيل الدخول كعميل.');
        }

        return $next($request);
    }
}


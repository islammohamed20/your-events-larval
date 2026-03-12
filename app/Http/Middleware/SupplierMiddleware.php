<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SupplierMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::guard('supplier')->check()) {
            return redirect()->route('supplier.login')->with('error', 'يرجى تسجيل الدخول أولاً');
        }

        $supplier = Auth::guard('supplier')->user();

        // التحقق من أن المورد موافق عليه
        if ($supplier->status !== 'approved') {
            Auth::guard('supplier')->logout();

            $message = match ($supplier->status) {
                'pending' => 'حسابك قيد المراجعة. سيتم إعلامك عند الموافقة.',
                'rejected' => 'تم رفض طلبك. السبب: '.($supplier->rejection_reason ?? 'غير محدد'),
                'suspended' => 'تم إيقاف حسابك. يرجى التواصل مع الإدارة.',
                default => 'حسابك غير نشط.'
            };

            return redirect()->route('supplier.login')->with('error', $message);
        }

        // التحقق من تأكيد البريد الإلكتروني
        if (! $supplier->email_verified_at) {
            Auth::guard('supplier')->logout();

            return redirect()->route('supplier.login')->with('error', 'يرجى تأكيد بريدك الإلكتروني أولاً');
        }

        $currentVersion = (int) ($supplier->session_version ?: 1);
        $sessionVersion = $request->session()->get('supplier_session_version');

        if ($sessionVersion === null) {
            $request->session()->put('supplier_session_version', $currentVersion);
        } elseif ((int) $sessionVersion !== $currentVersion) {
            Auth::guard('supplier')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('supplier.login')
                ->with('error', 'تم إنهاء جلستك لأن حسابك سُجل دخوله من جهاز آخر.');
        }

        return $next($request);
    }
}

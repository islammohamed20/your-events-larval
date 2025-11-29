<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // قراءة اللغة من cookie أو استخدام الإعدادات الافتراضية
        $cookieLocale = $request->cookie('app_locale');
        $sessionLocale = session('app_locale');
        $configLocale = config('app.locale');
        
        // الأولوية: cookie > session > config
        $locale = $cookieLocale ?? $sessionLocale ?? $configLocale;
        
        // تحقق من أن اللغة مدعومة
        $supported = ['ar', 'en'];
        if (!in_array($locale, $supported)) {
            $locale = $configLocale;
        }
        
        // اضبط اللغة
        app()->setLocale($locale);
        
        return $next($request);
    }
}


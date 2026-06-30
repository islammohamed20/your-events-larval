<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\CloudflareService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CloudflareSecurityController extends Controller
{
    protected CloudflareService $cloudflare;

    public function __construct(CloudflareService $cloudflare)
    {
        $this->cloudflare = $cloudflare;

        $this->middleware(function ($request, $next) {
            $user = Auth::user();
            if (! $user || ! $user->isAdmin()) {
                abort(403, 'غير مصرح لك بالوصول إلى هذه الصفحة');
            }

            return $next($request);
        });
    }

    public function index()
    {
        $overview = $this->cloudflare->getSecurityOverview();
        $statistics = $this->cloudflare->getSecurityStatistics();
        $firewallRules = $this->cloudflare->getFirewallRules();
        $threats = $this->cloudflare->getThreatDetection();
        $blockedIPs = $this->cloudflare->getTopBlockedIPs();
        $events = $this->cloudflare->getSecurityEvents();
        $sslInfo = $this->cloudflare->getSSLInfo();
        $notifications = $this->cloudflare->getNotifications();
        $logs = $this->cloudflare->getSecurityLogs();
        $isConfigured = $this->cloudflare->isConfigured();

        return view('admin.cloudflare-security.index', compact(
            'overview',
            'statistics',
            'firewallRules',
            'threats',
            'blockedIPs',
            'events',
            'sslInfo',
            'notifications',
            'logs',
            'isConfigured'
        ));
    }

    public function enableUnderAttackMode(Request $request)
    {
        $result = $this->cloudflare->enableUnderAttackMode();

        if ($result) {
            return redirect()->route('admin.cloudflare-security.index')
                ->with('success', 'تم تفعيل وضع Under Attack بنجاح.');
        }

        return redirect()->route('admin.cloudflare-security.index')
            ->with('warning', 'وضع العرض التجريبي: قم بإعداد Cloudflare API للتفعيل الفعلي.');
    }

    public function purgeCache(Request $request)
    {
        $result = $this->cloudflare->purgeCache();

        if ($result) {
            return redirect()->route('admin.cloudflare-security.index')
                ->with('success', 'تم مسح Cloudflare Cache بنجاح.');
        }

        return redirect()->route('admin.cloudflare-security.index')
            ->with('warning', 'وضع العرض التجريبي: قم بإعداد Cloudflare API للمسح الفعلي.');
    }

    public function blockIP(Request $request)
    {
        $request->validate([
            'ip' => 'required|ip',
        ], [
            'ip.required' => 'عنوان IP مطلوب.',
            'ip.ip' => 'عنوان IP غير صالح.',
        ]);

        $result = $this->cloudflare->blockIP($request->ip);

        if ($result) {
            return redirect()->route('admin.cloudflare-security.index')
                ->with('success', "تم حظر IP: {$request->ip} بنجاح.");
        }

        return redirect()->route('admin.cloudflare-security.index')
            ->with('warning', "وضع العرض التجريبي: سيتم حظر IP {$request->ip} عند إعداد Cloudflare API.");
    }

    public function allowIP(Request $request)
    {
        $request->validate([
            'ip' => 'required|ip',
        ], [
            'ip.required' => 'عنوان IP مطلوب.',
            'ip.ip' => 'عنوان IP غير صالح.',
        ]);

        $result = $this->cloudflare->allowIP($request->ip);

        if ($result) {
            return redirect()->route('admin.cloudflare-security.index')
                ->with('success', "تم السماح لـ IP: {$request->ip} بنجاح.");
        }

        return redirect()->route('admin.cloudflare-security.index')
            ->with('warning', "وضع العرض التجريبي: سيتم السماح لـ IP {$request->ip} عند إعداد Cloudflare API.");
    }

    public function blockCountry(Request $request)
    {
        $request->validate([
            'country_code' => 'required|string|size:2',
        ], [
            'country_code.required' => 'رمز الدولة مطلوب.',
            'country_code.size' => 'رمز الدولة يجب أن يكون حرفين (مثل: RU, CN).',
        ]);

        $result = $this->cloudflare->blockCountry($request->country_code);

        if ($result) {
            return redirect()->route('admin.cloudflare-security.index')
                ->with('success', "تم حظر الدولة: {$request->country_code} بنجاح.");
        }

        return redirect()->route('admin.cloudflare-security.index')
            ->with('warning', "وضع العرض التجريبي: سيتم حظر الدولة {$request->country_code} عند إعداد Cloudflare API.");
    }
}

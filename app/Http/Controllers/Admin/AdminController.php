<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Package;
use App\Models\Service;
use App\Models\Booking;
use App\Models\Gallery;
use App\Models\Review;
use App\Models\Quote;
use App\Models\EmailTemplate;
use App\Models\OtpVerification;
use App\Models\Visit;
use App\Models\LoginActivity;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $user = Auth::user();
            /** @var User|null $user */
            if (!$user instanceof User || !$user->isAdmin()) {
                abort(403, 'غير مصرح لك بالوصول إلى هذه الصفحة');
            }
            return $next($request);
        });
    }

    public function dashboard()
    {
        // Build dashboard statistics
        $stats = [
            'total_users' => User::count(),
            'admin_users' => User::where('is_admin', true)->count(),
            'customers' => User::where('is_admin', false)->count(),
            'packages' => Package::count(),
            'services' => Service::count(),
            'bookings' => Booking::count(),
            'pending_bookings' => Booking::where('status', 'pending')->count(),
            'gallery_items' => Gallery::count(),
            'reviews' => Review::count(),
            'pending_reviews' => Review::where('is_approved', false)->count(),
            // Quotes statistics
            'quotes' => Quote::count(),
            'pending_quotes' => Quote::where('status', 'pending')->count(),
            'approved_quotes' => Quote::where('status', 'approved')->count(),
            'rejected_quotes' => Quote::where('status', 'rejected')->count(),
            'completed_quotes' => Quote::where('status', 'completed')->count(),
            // Email/OTP statistics (lightweight)
            'email_templates_total' => EmailTemplate::count(),
            'email_templates_active' => EmailTemplate::where('is_active', true)->count(),
            'otp_total' => OtpVerification::count(),
            'otp_today' => OtpVerification::whereDate('created_at', today())->count(),
            'otp_verified' => OtpVerification::where('status', 'verified')->count(),
            'otp_pending' => OtpVerification::where('status', 'pending')->count(),
            // Traffic statistics
            'visits_today' => Visit::whereDate('created_at', today())->count(),
            'visits_7d' => Visit::where('created_at', '>=', now()->subDays(7))->count(),
            'unique_visitors_7d' => Visit::where('created_at', '>=', now()->subDays(7))
                ->distinct('ip_address')->count('ip_address'),
            // Login statistics
            'logins_today' => LoginActivity::whereDate('created_at', today())->count(),
            'logins_7d' => LoginActivity::where('created_at', '>=', now()->subDays(7))->count(),
            'top_countries_7d' => Visit::selectRaw('COALESCE(country, "Unknown") as country, COUNT(*) as count')
                ->where('created_at', '>=', now()->subDays(7))
                ->groupBy('country')
                ->orderByDesc('count')
                ->limit(5)
                ->get(),
        ];

        // Compute OTP success rate
        $totalOtps = $stats['otp_total'];
        $stats['otp_success_rate'] = $totalOtps > 0
            ? round(($stats['otp_verified'] / $totalOtps) * 100, 2)
            : 0;

        // Recent bookings
        $recent_bookings = Booking::with(['package', 'service'])
            ->latest()
            ->take(5)
            ->get();

        // Recent quotes
        $recent_quotes = Quote::with(['user'])
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'recent_bookings', 'recent_quotes'));
    }
}

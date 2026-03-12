<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\EmailTemplate;
use App\Models\Gallery;
use App\Models\LoginActivity;
use App\Models\OtpVerification;
use App\Models\Package;
use App\Models\Quote;
use App\Models\Review;
use App\Models\Service;
use App\Models\User;
use App\Models\Visit;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $user = Auth::user();
            /** @var User|null $user */
            if (! $user instanceof User || ! $user->isAdmin()) {
                abort(403, 'غير مصرح لك بالوصول إلى هذه الصفحة');
            }

            return $next($request);
        });
    }

    public function dashboard()
    {
        /** @var User $user */
        $user = Auth::user();

        $canManageUsers = $user->hasAdminPermission('manage_users');
        $canManageEmails = $user->hasAdminPermission('manage_emails');
        $canManageServices = $user->hasAdminPermission('manage_services');
        $canManagePackages = $user->hasAdminPermission('manage_packages');
        $canManageBookings = $user->hasAdminPermission('manage_bookings')
            || $user->hasAdminPermission('bookings.view')
            || $user->hasAdminPermission('bookings.edit')
            || $user->hasAdminPermission('bookings.delete')
            || $user->hasAdminPermission('quotes.view')
            || $user->hasAdminPermission('quotes.edit')
            || $user->hasAdminPermission('quotes.delete');
        $canManageQuotes = $user->hasAdminPermission('manage_bookings')
            || $user->hasAdminPermission('quotes.view')
            || $user->hasAdminPermission('quotes.edit')
            || $user->hasAdminPermission('quotes.delete');
        $canManageCustomers = $user->hasAdminPermission('manage_customers')
            || $user->hasAdminPermission('customers.view')
            || $user->hasAdminPermission('customers.edit')
            || $user->hasAdminPermission('customers.delete')
            || $user->hasAdminPermission('customers.export')
            || $user->hasAdminPermission('customers.reset_password');

        // Widgets without detailed permissions yet remain tied to top-level admin management.
        $canViewTraffic = $canManageUsers;
        $canViewOtp = $canManageUsers;
        $canViewGallery = $canManageUsers;
        $canViewReviews = $canManageUsers;

        $dashboardPermissions = [
            'customers' => $canManageCustomers,
            'admins' => $canManageUsers,
            'services' => $canManageServices,
            'packages' => $canManagePackages,
            'bookings' => $canManageBookings,
            'quotes' => $canManageQuotes,
            'emails' => $canManageEmails,
            'traffic' => $canViewTraffic,
            'otp' => $canViewOtp,
            'gallery' => $canViewGallery,
            'reviews' => $canViewReviews,
            'quick_summary' => $canManageCustomers || $canManageServices || $canManagePackages || $canManageBookings || $canManageUsers,
        ];

        // Build dashboard statistics
        $stats = [
            'total_users' => 0,
            'admin_users' => 0,
            'customers' => 0,
            'packages' => 0,
            'services' => 0,
            'bookings' => 0,
            'pending_bookings' => 0,
            'gallery_items' => 0,
            'reviews' => 0,
            'pending_reviews' => 0,
            'quotes' => 0,
            'pending_quotes' => 0,
            'approved_quotes' => 0,
            'rejected_quotes' => 0,
            'completed_quotes' => 0,
            'email_templates_total' => 0,
            'email_templates_active' => 0,
            'otp_total' => 0,
            'otp_today' => 0,
            'otp_verified' => 0,
            'otp_pending' => 0,
            'visits_today' => 0,
            'visits_7d' => 0,
            'unique_visitors_7d' => 0,
            'logins_today' => 0,
            'logins_7d' => 0,
            'top_countries_7d' => collect(),
        ];

        if ($dashboardPermissions['customers'] || $dashboardPermissions['admins'] || $dashboardPermissions['quick_summary']) {
            $stats['total_users'] = User::count();
        }

        if ($dashboardPermissions['admins']) {
            $stats['admin_users'] = User::where('is_admin', true)->count();
        }

        if ($dashboardPermissions['customers']) {
            $stats['customers'] = User::where('is_admin', false)->count();
        }

        if ($dashboardPermissions['packages']) {
            $stats['packages'] = Package::count();
        }

        if ($dashboardPermissions['services']) {
            $stats['services'] = Service::count();
        }

        if ($dashboardPermissions['bookings']) {
            $stats['bookings'] = Booking::count();
            $stats['pending_bookings'] = Booking::where('status', 'pending')->count();
        }

        if ($dashboardPermissions['quotes']) {
            $stats['quotes'] = Quote::count();
            $stats['pending_quotes'] = Quote::where('status', 'pending')->count();
            $stats['approved_quotes'] = Quote::where('status', 'approved')->count();
            $stats['rejected_quotes'] = Quote::where('status', 'rejected')->count();
            $stats['completed_quotes'] = Quote::where('status', 'completed')->count();
        }

        if ($dashboardPermissions['gallery']) {
            $stats['gallery_items'] = Gallery::count();
        }

        if ($dashboardPermissions['reviews']) {
            $stats['reviews'] = Review::count();
            $stats['pending_reviews'] = Review::where('is_approved', false)->count();
        }

        if ($dashboardPermissions['emails']) {
            $stats['email_templates_total'] = EmailTemplate::count();
            $stats['email_templates_active'] = EmailTemplate::where('is_active', true)->count();
        }

        if ($dashboardPermissions['otp']) {
            $stats['otp_total'] = OtpVerification::count();
            $stats['otp_today'] = OtpVerification::whereDate('created_at', today())->count();
            $stats['otp_verified'] = OtpVerification::where('status', 'verified')->count();
            $stats['otp_pending'] = OtpVerification::where('status', 'pending')->count();
        }

        if ($dashboardPermissions['traffic']) {
            $stats['visits_today'] = Visit::whereDate('created_at', today())->count();
            $stats['visits_7d'] = Visit::where('created_at', '>=', now()->subDays(7))->count();
            $stats['unique_visitors_7d'] = Visit::where('created_at', '>=', now()->subDays(7))
                ->distinct('ip_address')->count('ip_address');
            $stats['logins_today'] = LoginActivity::whereDate('created_at', today())->count();
            $stats['logins_7d'] = LoginActivity::where('created_at', '>=', now()->subDays(7))->count();
            $stats['top_countries_7d'] = Visit::selectRaw('COALESCE(country, "Unknown") as country, COUNT(*) as count')
                ->where('created_at', '>=', now()->subDays(7))
                ->groupBy('country')
                ->orderByDesc('count')
                ->limit(5)
                ->get();
        }

        // Compute OTP success rate
        $totalOtps = $stats['otp_total'];
        $stats['otp_success_rate'] = $totalOtps > 0
            ? round(($stats['otp_verified'] / $totalOtps) * 100, 2)
            : 0;

        // Recent bookings
        $recent_bookings = $dashboardPermissions['bookings']
            ? Booking::with(['package', 'service'])->latest()->take(5)->get()
            : collect();

        // Recent quotes
        $recent_quotes = $dashboardPermissions['quotes']
            ? Quote::with(['user'])->latest()->take(5)->get()
            : collect();

        return view('admin.dashboard', compact('stats', 'recent_bookings', 'recent_quotes', 'dashboardPermissions'));
    }
}

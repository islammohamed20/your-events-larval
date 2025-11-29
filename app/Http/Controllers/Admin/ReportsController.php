<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Quote;
use App\Models\Booking;
use App\Models\LoginActivity;
use App\Models\OtpVerification;
use App\Models\Visit;

class ReportsController extends Controller
{
    /**
     * Display the main reports dashboard.
     */
    public function index(Request $request)
    {
        $startDate = $request->input('start_date')
            ? Carbon::parse($request->input('start_date'))->startOfDay()
            : Carbon::now()->subDays(30)->startOfDay();
        $endDate = $request->input('end_date')
            ? Carbon::parse($request->input('end_date'))->endOfDay()
            : Carbon::now()->endOfDay();

        // Revenue calculations
        $approvedQuotesTotal = Quote::where('status', 'approved')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('total');

        $confirmedBookingsTotal = Booking::where('status', 'confirmed')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('total_amount');

        $revenue = [
            'total' => (float) $approvedQuotesTotal + (float) $confirmedBookingsTotal,
            'bookings' => (float) $confirmedBookingsTotal,
            'quotes_count' => Quote::whereBetween('created_at', [$startDate, $endDate])->count(),
            'bookings_count' => Booking::whereBetween('created_at', [$startDate, $endDate])->count(),
        ];

        // Users statistics
        $users = [
            'total' => User::count(),
            'new' => User::whereBetween('created_at', [$startDate, $endDate])->count(),
            'active' => User::where('status', 'active')->count(),
        ];

        // Monthly revenue for last 12 months
        $months = [];
        $cursor = Carbon::now()->startOfMonth();
        for ($i = 11; $i >= 0; $i--) {
            $months[] = $cursor->copy()->subMonths($i)->format('Y-m');
        }

        $quotesMonthly = DB::table('quotes')
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, SUM(total) as total')
            ->where('status', 'approved')
            ->whereBetween('created_at', [$cursor->copy()->subMonths(11)->startOfMonth(), Carbon::now()->endOfMonth()])
            ->groupBy('month')
            ->pluck('total', 'month');

        $bookingsMonthly = DB::table('bookings')
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, SUM(total_amount) as total')
            ->where('status', 'confirmed')
            ->whereBetween('created_at', [$cursor->copy()->subMonths(11)->startOfMonth(), Carbon::now()->endOfMonth()])
            ->groupBy('month')
            ->pluck('total', 'month');

        $monthlyRevenue = [];
        foreach ($months as $m) {
            $monthlyRevenue[] = [
                'month' => $m,
                'total' => (float) ($quotesMonthly[$m] ?? 0) + (float) ($bookingsMonthly[$m] ?? 0),
            ];
        }

        // Top services/products placeholders (models may not exist in repo)
        $topServices = [];
        $topProducts = [];

        // Top visit countries within selected range (exclude unknown/empty)
        $topCountries = DB::table('visits')
            ->selectRaw('country as country, COUNT(*) as total')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereNotNull('country')
            ->where('country', '!=', '')
            ->whereNotIn('country', ['Unknown', 'غير معروف'])
            ->groupBy('country')
            ->orderByDesc('total')
            ->limit(8)
            ->get();

        return view('admin.reports.index', [
            'startDate' => $startDate->toDateString(),
            'endDate' => $endDate->toDateString(),
            'revenue' => $revenue,
            'users' => $users,
            'monthlyRevenue' => $monthlyRevenue,
            'topServices' => $topServices,
            'topProducts' => $topProducts,
            'topCountries' => $topCountries,
        ]);
    }

    /**
     * Display the security report.
     */
    public function security(Request $request)
    {
        $startDate = $request->input('start_date')
            ? Carbon::parse($request->input('start_date'))->startOfDay()
            : Carbon::now()->subDays(30)->startOfDay();
        $endDate = $request->input('end_date')
            ? Carbon::parse($request->input('end_date'))->endOfDay()
            : Carbon::now()->endOfDay();

        // Login summary
        $loginTotal = LoginActivity::whereBetween('created_at', [$startDate, $endDate])->count();
        $loginSuccessful = LoginActivity::whereBetween('created_at', [$startDate, $endDate])->where('successful', true)->count();
        $loginFailed = $loginTotal - $loginSuccessful;

        $loginSummary = [
            'total' => $loginTotal,
            'successful' => $loginSuccessful,
            'failed' => $loginFailed,
        ];

        // Login timeline (daily)
        $loginTimeline = DB::table('login_activities')
            ->selectRaw('DATE(created_at) as date, COUNT(*) as total, SUM(successful = 1) as successful, SUM(successful = 0) as failed')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // OTP summary
        $otpQuery = OtpVerification::whereBetween('created_at', [$startDate, $endDate]);
        $otpTotal = (clone $otpQuery)->count();
        $otpVerified = (clone $otpQuery)->where('status', 'verified')->count();
        $otpExpired = (clone $otpQuery)->where('status', 'expired')->count();
        $otpSuccessRate = $otpTotal > 0 ? round(($otpVerified / $otpTotal) * 100, 2) : 0;

        $otpByType = DB::table('otp_verifications')
            ->selectRaw('type, COUNT(*) as count')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('type')
            ->get();

        $otpByStatus = DB::table('otp_verifications')
            ->selectRaw('status, COUNT(*) as count')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('status')
            ->get();

        $otpSummary = [
            'total' => $otpTotal,
            'verified' => $otpVerified,
            'expired' => $otpExpired,
            'success_rate' => $otpSuccessRate,
            'by_type' => $otpByType,
            'by_status' => $otpByStatus,
        ];

        // Top failed IPs
        $topFailedIps = DB::table('login_activities')
            ->selectRaw('ip_address, SUM(successful = 0) as fails')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('ip_address')
            ->orderByDesc('fails')
            ->limit(10)
            ->get();

        return view('admin.reports.security', [
            'loginSummary' => $loginSummary,
            'otpSummary' => $otpSummary,
            'loginTimeline' => $loginTimeline,
            'topFailedIps' => $topFailedIps,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ]);
    }

    /**
     * Export report data (CSV for now).
     */
    public function export(Request $request)
    {
        $type = $request->input('type', 'excel');
        $startDate = $request->input('start_date')
            ? Carbon::parse($request->input('start_date'))->startOfDay()
            : Carbon::now()->subDays(30)->startOfDay();
        $endDate = $request->input('end_date')
            ? Carbon::parse($request->input('end_date'))->endOfDay()
            : Carbon::now()->endOfDay();

        $approvedQuotesTotal = Quote::where('status', 'approved')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('total');
        $confirmedBookingsTotal = Booking::where('status', 'confirmed')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('total_amount');

        $csv = [];
        $csv[] = ['التقرير', 'من', 'إلى'];
        $csv[] = ['ملخص الإيرادات', $startDate->toDateString(), $endDate->toDateString()];
        $csv[] = ['إجمالي الإيرادات', (float) $approvedQuotesTotal + (float) $confirmedBookingsTotal];
        $csv[] = ['إيرادات الحجوزات', (float) $confirmedBookingsTotal];
        $csv[] = ['عدد عروض الأسعار', Quote::whereBetween('created_at', [$startDate, $endDate])->count()];
        $csv[] = ['عدد الحجوزات', Booking::whereBetween('created_at', [$startDate, $endDate])->count()];

        // Build CSV response
        $filename = 'report_' . now()->format('Ymd_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $handle = fopen('php://temp', 'r+');
        // Add BOM for Excel compatibility with UTF-8 Arabic
        fwrite($handle, "\xEF\xBB\xBF");
        foreach ($csv as $row) {
            fputcsv($handle, $row);
        }
        rewind($handle);
        $content = stream_get_contents($handle);
        fclose($handle);

        return response($content, 200, $headers);
    }

    /**
     * Export security report data (CSV).
     */
    public function exportSecurity(Request $request)
    {
        $startDate = $request->input('start_date')
            ? Carbon::parse($request->input('start_date'))->startOfDay()
            : Carbon::now()->subDays(30)->startOfDay();
        $endDate = $request->input('end_date')
            ? Carbon::parse($request->input('end_date'))->endOfDay()
            : Carbon::now()->endOfDay();

        // Login summary
        $loginTotal = LoginActivity::whereBetween('created_at', [$startDate, $endDate])->count();
        $loginSuccessful = LoginActivity::whereBetween('created_at', [$startDate, $endDate])->where('successful', true)->count();
        $loginFailed = $loginTotal - $loginSuccessful;

        // OTP summary
        $otpQuery = OtpVerification::whereBetween('created_at', [$startDate, $endDate]);
        $otpTotal = (clone $otpQuery)->count();
        $otpVerified = (clone $otpQuery)->where('status', 'verified')->count();
        $otpExpired = (clone $otpQuery)->where('status', 'expired')->count();
        $otpSuccessRate = $otpTotal > 0 ? round(($otpVerified / $otpTotal) * 100, 2) : 0;

        $csv = [];
        $csv[] = ['تقرير الأمان', 'من', 'إلى'];
        $csv[] = ['ملخص تسجيلات الدخول', $startDate->toDateString(), $endDate->toDateString()];
        $csv[] = ['إجمالي المحاولات', $loginTotal];
        $csv[] = ['ناجح', $loginSuccessful];
        $csv[] = ['فاشل', $loginFailed];
        $csv[] = ['ملخص OTP', '', ''];
        $csv[] = ['إجمالي OTP', $otpTotal];
        $csv[] = ['تم التحقق', $otpVerified];
        $csv[] = ['منتهي', $otpExpired];
        $csv[] = ['نسبة النجاح (%)', $otpSuccessRate];

        // Build CSV response
        $filename = 'security_report_' . now()->format('Ymd_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $handle = fopen('php://temp', 'r+');
        fwrite($handle, "\xEF\xBB\xBF");
        foreach ($csv as $row) {
            fputcsv($handle, $row);
        }
        rewind($handle);
        $content = stream_get_contents($handle);
        fclose($handle);

        return response($content, 200, $headers);
    }
}

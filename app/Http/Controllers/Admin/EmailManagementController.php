<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmailTemplate;
use App\Models\OtpVerification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class EmailManagementController extends Controller
{
    /**
     * Display unified email management dashboard
     */
    public function index()
    {
        // Email Templates Stats
        $templatesStats = [
            'total' => EmailTemplate::count(),
            'active' => EmailTemplate::where('is_active', true)->count(),
            'inactive' => EmailTemplate::where('is_active', false)->count(),
            'types' => EmailTemplate::distinct('type')->count(),
        ];

        // Recent Templates
        $recentTemplates = EmailTemplate::orderBy('updated_at', 'desc')->limit(5)->get();

        // OTP Stats
        $otpStats = [
            'total' => OtpVerification::count(),
            'today' => OtpVerification::whereDate('created_at', today())->count(),
            'verified' => OtpVerification::where('status', 'verified')->count(),
            'pending' => OtpVerification::where('status', 'pending')->count(),
            'success_rate' => 0,
        ];

        if ($otpStats['total'] > 0) {
            $otpStats['success_rate'] = round(($otpStats['verified'] / $otpStats['total']) * 100, 2);
        }

        // Recent OTP Activity
        $recentOtps = OtpVerification::orderBy('created_at', 'desc')->limit(5)->get();

        // OTP by Type
        $otpByType = OtpVerification::select('type', DB::raw('count(*) as count'))
            ->groupBy('type')
            ->get()
            ->pluck('count', 'type');

        // Email Activity (last 7 days)
        $emailActivity = OtpVerification::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('count(*) as total'),
            DB::raw('sum(case when status = "verified" then 1 else 0 end) as verified')
        )
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        return view('admin.email-management.index', compact(
            'templatesStats',
            'recentTemplates',
            'otpStats',
            'recentOtps',
            'otpByType',
            'emailActivity'
        ));
    }

    /**
     * Send test email
     */
    public function sendTest(Request $request)
    {
        $request->validate([
            'to_email' => 'required|email',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'use_html' => 'nullable|boolean',
        ]);

        try {
            $useHtml = $request->has('use_html');

            Mail::send([], [], function ($message) use ($request, $useHtml) {
                $message->to($request->to_email)
                    ->subject($request->subject);

                if ($useHtml) {
                    $message->html($request->message);
                } else {
                    $message->text($request->message);
                }
            });

            return response()->json([
                'success' => true,
                'message' => 'تم إرسال البريد بنجاح إلى '.$request->to_email,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'فشل الإرسال: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get email statistics
     */
    public function statistics()
    {
        return response()->json([
            'templates' => [
                'total' => EmailTemplate::count(),
                'active' => EmailTemplate::where('is_active', true)->count(),
                'by_type' => EmailTemplate::select('type', DB::raw('count(*) as count'))
                    ->groupBy('type')
                    ->get(),
            ],
            'otp' => [
                'total' => OtpVerification::count(),
                'today' => OtpVerification::whereDate('created_at', today())->count(),
                'by_status' => OtpVerification::select('status', DB::raw('count(*) as count'))
                    ->groupBy('status')
                    ->get(),
                'by_type' => OtpVerification::select('type', DB::raw('count(*) as count'))
                    ->groupBy('type')
                    ->get(),
            ],
            'activity' => OtpVerification::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('count(*) as count')
            )
                ->where('created_at', '>=', Carbon::now()->subDays(30))
                ->groupBy('date')
                ->orderBy('date', 'asc')
                ->get(),
        ]);
    }
}

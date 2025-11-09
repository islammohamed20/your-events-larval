<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\OtpVerification;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class OtpManagementController extends Controller
{
    /**
     * Display OTP management dashboard
     */
    public function index(Request $request)
    {
        $query = OtpVerification::query();

        // Filters
        if ($request->filled('email')) {
            $query->where('email', 'like', '%' . $request->email . '%');
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Pagination
        $otps = $query->orderBy('created_at', 'desc')->paginate(20);

        // Statistics
        $stats = [
            'total' => OtpVerification::count(),
            'today' => OtpVerification::whereDate('created_at', today())->count(),
            'verified' => OtpVerification::where('status', 'verified')->count(),
            'pending' => OtpVerification::where('status', 'pending')->count(),
            'expired' => OtpVerification::where('status', 'expired')->count(),
            'failed' => OtpVerification::where('status', 'failed')->count(),
        ];

        // Success rate
        $stats['success_rate'] = $stats['total'] > 0 
            ? round(($stats['verified'] / $stats['total']) * 100, 2) 
            : 0;

        // By type
        $byType = OtpVerification::select('type', DB::raw('count(*) as count'))
            ->groupBy('type')
            ->get()
            ->pluck('count', 'type');

        // Recent activity (last 7 days)
        $recentActivity = OtpVerification::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('count(*) as total'),
                DB::raw('sum(case when status = "verified" then 1 else 0 end) as verified')
            )
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->get();

        return view('admin.otp.index', compact('otps', 'stats', 'byType', 'recentActivity'));
    }

    /**
     * Show OTP details
     */
    public function show($id)
    {
        $otp = OtpVerification::findOrFail($id);
        return view('admin.otp.show', compact('otp'));
    }

    /**
     * Clean expired OTPs
     */
    public function cleanExpired()
    {
        $count = OtpVerification::where('expires_at', '<', Carbon::now())
            ->where('status', 'pending')
            ->update(['status' => 'expired']);

        return redirect()->back()->with('success', "تم تحديث {$count} كود منتهي الصلاحية");
    }

    /**
     * Delete old OTPs (older than specified days)
     */
    public function deleteOld(Request $request)
    {
        $request->validate([
            'days' => 'required|integer|min:1|max:365'
        ]);

        $days = $request->days;
        $date = Carbon::now()->subDays($days);
        
        $count = OtpVerification::where('created_at', '<', $date)->delete();

        return redirect()->back()->with('success', "تم حذف {$count} كود أقدم من {$days} يوم");
    }

    /**
     * Delete specific OTP
     */
    public function destroy($id)
    {
        $otp = OtpVerification::findOrFail($id);
        $otp->delete();

        return redirect()->back()->with('success', 'تم حذف الكود بنجاح');
    }

    /**
     * Export OTPs to CSV
     */
    public function export(Request $request)
    {
        $query = OtpVerification::query();

        // Apply same filters as index
        if ($request->filled('email')) {
            $query->where('email', 'like', '%' . $request->email . '%');
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $otps = $query->orderBy('created_at', 'desc')->get();

        // Generate CSV
        $filename = 'otp_export_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($otps) {
            $file = fopen('php://output', 'w');
            
            // BOM for UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Headers
            fputcsv($file, ['ID', 'البريد الإلكتروني', 'النوع', 'الحالة', 'المحاولات', 'تاريخ الإنشاء', 'تاريخ الانتهاء', 'تاريخ التحقق', 'IP']);
            
            // Data
            foreach ($otps as $otp) {
                fputcsv($file, [
                    $otp->id,
                    $otp->email,
                    $this->getTypeLabel($otp->type),
                    $this->getStatusLabel($otp->status),
                    $otp->attempts,
                    $otp->created_at->format('Y-m-d H:i:s'),
                    $otp->expires_at->format('Y-m-d H:i:s'),
                    $otp->verified_at ? $otp->verified_at->format('Y-m-d H:i:s') : '-',
                    $otp->ip_address ?? '-',
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get statistics API
     */
    public function statistics()
    {
        $stats = [
            'total' => OtpVerification::count(),
            'today' => OtpVerification::whereDate('created_at', today())->count(),
            'verified' => OtpVerification::where('status', 'verified')->count(),
            'pending' => OtpVerification::where('status', 'pending')->count(),
            'expired' => OtpVerification::where('status', 'expired')->count(),
            'failed' => OtpVerification::where('status', 'failed')->count(),
            'by_type' => OtpVerification::select('type', DB::raw('count(*) as count'))
                ->groupBy('type')
                ->get(),
            'by_status' => OtpVerification::select('status', DB::raw('count(*) as count'))
                ->groupBy('status')
                ->get(),
            'last_7_days' => OtpVerification::select(
                    DB::raw('DATE(created_at) as date'),
                    DB::raw('count(*) as count')
                )
                ->where('created_at', '>=', Carbon::now()->subDays(7))
                ->groupBy('date')
                ->orderBy('date', 'asc')
                ->get(),
        ];

        return response()->json($stats);
    }

    /**
     * Helper: Get type label
     */
    private function getTypeLabel($type)
    {
        $labels = [
            'email_verification' => 'التحقق من البريد',
            'login' => 'تسجيل الدخول',
            'password_reset' => 'إعادة تعيين كلمة المرور',
            'booking_confirmation' => 'تأكيد الحجز',
            'payment_confirmation' => 'تأكيد الدفع',
        ];
        return $labels[$type] ?? $type;
    }

    /**
     * Helper: Get status label
     */
    private function getStatusLabel($status)
    {
        $labels = [
            'pending' => 'قيد الانتظار',
            'verified' => 'تم التحقق',
            'expired' => 'منتهي الصلاحية',
            'failed' => 'فشل',
        ];
        return $labels[$status] ?? $status;
    }
}

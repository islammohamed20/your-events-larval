<?php

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use App\Models\OtpVerification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class SupplierAuthController extends Controller
{
    /**
     * Show login form
     */
    public function showLoginForm()
    {
        if (Auth::guard('supplier')->check()) {
            return redirect()->route('supplier.dashboard');
        }
        
        return view('supplier.auth.login');
    }

    /**
     * Handle login request
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        $supplier = Supplier::where('email', $request->email)->first();

        if (!$supplier) {
            return back()->withInput()->with('error', 'البريد الإلكتروني غير مسجل');
        }

        // التحقق من كلمة المرور
        if (!Hash::check($request->password, $supplier->password)) {
            return back()->withInput()->with('error', 'كلمة المرور غير صحيحة');
        }

        // التحقق من حالة الحساب
        if ($supplier->status === 'pending') {
            return back()->withInput()->with('error', 'حسابك قيد المراجعة. سيتم إعلامك عند الموافقة.');
        }

        if ($supplier->status === 'rejected') {
            return back()->withInput()->with('error', 'تم رفض طلبك. السبب: ' . ($supplier->rejection_reason ?? 'غير محدد'));
        }

        if ($supplier->status === 'suspended') {
            return back()->withInput()->with('error', 'تم إيقاف حسابك. يرجى التواصل مع الإدارة.');
        }

        // التحقق من تأكيد البريد الإلكتروني
        if (!$supplier->email_verified_at) {
            return back()->withInput()->with('error', 'يرجى تأكيد بريدك الإلكتروني أولاً');
        }

        try {
            OtpVerification::generate($supplier->email, 'supplier_login');
            $request->session()->put('otp_email', $supplier->email);
            $request->session()->put('otp_type', 'supplier_login');
            $request->session()->put('supplier_login_pending', true);
            $request->session()->put('supplier_login_remember', $request->filled('remember'));
            $request->session()->put('supplier_login_supplier_id', $supplier->id);
            
            return redirect()->route('otp.verify.form')
                ->with('success', 'تم إرسال كود التحقق لتأكيد تسجيل الدخول');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'فشل في إرسال كود التحقق: ' . $e->getMessage());
        }
    }

    /**
     * Logout
     */
    public function logout(Request $request)
    {
        Auth::guard('supplier')->logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('supplier.login')->with('success', 'تم تسجيل الخروج بنجاح');
    }

    /**
     * Show forgot password form
     */
    public function showForgotPasswordForm()
    {
        return view('supplier.auth.forgot-password');
    }

    /**
     * Send password reset OTP
     */
    public function sendResetOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:suppliers,email',
        ]);

        try {
            OtpVerification::generate($request->email, 'password_reset');
            session(['supplier_reset_email' => $request->email]);
            
            return redirect()->route('supplier.password.verify-otp')->with('success', 'تم إرسال رمز التحقق إلى بريدك الإلكتروني');
        } catch (\Exception $e) {
            Log::error('Failed to send reset OTP: ' . $e->getMessage());
            return back()->with('error', 'حدث خطأ أثناء إرسال رمز التحقق');
        }
    }

    /**
     * Show OTP verification form for password reset
     */
    public function showVerifyOtpForm()
    {
        if (!session('supplier_reset_email')) {
            return redirect()->route('supplier.password.forgot');
        }
        
        return view('supplier.auth.verify-reset-otp');
    }

    /**
     * Verify OTP and show reset password form
     */
    public function verifyResetOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|string|size:6',
        ]);

        $email = session('supplier_reset_email');
        
        if (!$email) {
            return redirect()->route('supplier.password.forgot')->with('error', 'انتهت صلاحية الجلسة');
        }

        $result = OtpVerification::verify($email, $request->otp, 'password_reset');

        if (!$result['success']) {
            return back()->with('error', $result['message']);
        }

        session(['supplier_reset_verified' => true]);
        
        return redirect()->route('supplier.password.reset');
    }

    /**
     * Show reset password form
     */
    public function showResetPasswordForm()
    {
        if (!session('supplier_reset_email') || !session('supplier_reset_verified')) {
            return redirect()->route('supplier.password.forgot');
        }
        
        return view('supplier.auth.reset-password');
    }

    /**
     * Reset password
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $email = session('supplier_reset_email');
        
        if (!$email || !session('supplier_reset_verified')) {
            return redirect()->route('supplier.password.forgot')->with('error', 'انتهت صلاحية الجلسة');
        }

        $supplier = Supplier::where('email', $email)->first();
        
        if (!$supplier) {
            return redirect()->route('supplier.password.forgot')->with('error', 'المورد غير موجود');
        }

        $supplier->update([
            'password' => Hash::make($request->password),
        ]);

        // مسح بيانات الجلسة
        session()->forget(['supplier_reset_email', 'supplier_reset_verified']);

        return redirect()->route('supplier.login')->with('success', 'تم تغيير كلمة المرور بنجاح. يمكنك الآن تسجيل الدخول.');
    }
}

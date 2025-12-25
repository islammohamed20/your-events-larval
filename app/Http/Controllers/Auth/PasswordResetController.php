<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\OtpVerification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PasswordResetController extends Controller
{
    /**
     * Show forgot password form
     */
    public function showForgotForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Send OTP for password reset
     */
    public function sendResetOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ], [
            'email.required' => 'البريد الإلكتروني مطلوب',
            'email.email' => 'صيغة البريد الإلكتروني غير صحيحة',
            'email.exists' => 'البريد الإلكتروني غير مسجل',
        ]);

        try {
            // إرسال OTP
            OtpVerification::generate($request->email, 'password_reset');

            // حفظ البريد في الـ session
            $request->session()->put('reset_email', $request->email);

            return redirect()->route('password.otp.verify', ['email' => $request->email])
                ->with('success', 'تم إرسال كود التحقق إلى بريدك الإلكتروني');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'فشل في إرسال كود التحقق: '.$e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Show OTP verification form for password reset
     */
    public function showOtpVerifyForm(Request $request)
    {
        $email = $request->query('email') ?? $request->session()->get('reset_email');

        if (! $email) {
            return redirect()->route('password.request')
                ->with('error', 'الرجاء إدخال بريدك الإلكتروني أولاً');
        }

        return view('auth.verify-reset-otp', compact('email'));
    }

    /**
     * Verify OTP and show reset password form
     */
    public function verifyResetOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|string|size:6',
        ]);

        $result = OtpVerification::verify($request->email, $request->otp, 'password_reset');

        if ($result['success']) {
            // حفظ في الـ session
            $request->session()->put('reset_verified', true);
            $request->session()->put('reset_email', $request->email);

            return redirect()->route('password.reset.form')
                ->with('success', 'تم التحقق بنجاح. يمكنك الآن إعادة تعيين كلمة المرور');
        }

        return back()->withErrors(['otp' => $result['message']]);
    }

    /**
     * Show reset password form
     */
    public function showResetForm(Request $request)
    {
        if (! $request->session()->get('reset_verified')) {
            return redirect()->route('password.request')
                ->with('error', 'يرجى التحقق من بريدك الإلكتروني أولاً');
        }

        $email = $request->session()->get('reset_email');

        return view('auth.reset-password', compact('email'));
    }

    /**
     * Reset password
     */
    public function resetPassword(Request $request)
    {
        if (! $request->session()->get('reset_verified')) {
            return redirect()->route('password.request')
                ->with('error', 'يرجى التحقق من بريدك الإلكتروني أولاً');
        }

        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ], [
            'password.required' => 'كلمة المرور مطلوبة',
            'password.min' => 'كلمة المرور يجب أن تكون 8 أحرف على الأقل',
            'password.confirmed' => 'كلمة المرور غير متطابقة',
        ]);

        $email = $request->session()->get('reset_email');
        $user = User::where('email', $email)->first();

        if (! $user) {
            return redirect()->route('password.request')
                ->with('error', 'المستخدم غير موجود');
        }

        // تحديث كلمة المرور
        $user->password = Hash::make($request->password);
        $user->save();

        // مسح الـ session
        $request->session()->forget(['reset_verified', 'reset_email']);

        return redirect()->route('login')
            ->with('success', 'تم إعادة تعيين كلمة المرور بنجاح. يمكنك تسجيل الدخول الآن');
    }
}

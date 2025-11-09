<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\OtpVerification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $user = Auth::user();

            // تخطي OTP للمسؤولين (Admins)
            if ($user->is_admin || $user->role === 'admin') {
                $request->session()->regenerate();
                
                return redirect()->intended(route('admin.dashboard'))
                    ->with('success', 'مرحباً بك ' . $user->name);
            }

            // OTP للعملاء فقط
            try {
                \App\Models\OtpVerification::generate($user->email, 'login');

                $request->session()->put('otp_email', $user->email);
                $request->session()->put('otp_type', 'login');
                $request->session()->put('login_pending', true);
                $request->session()->put('login_remember', $request->boolean('remember'));
                $request->session()->put('login_user_id', $user->id);

                Auth::logout();

                return redirect()->route('otp.verify.form')
                    ->with('success', 'تم إرسال كود التحقق لتأكيد تسجيل الدخول');
            } catch (\Exception $e) {
                Auth::logout();
                $request->session()->forget(['login_pending', 'login_remember', 'login_user_id']);
                return back()->withErrors(['error' => 'فشل في إرسال كود التحقق: ' . $e->getMessage()])
                    ->withInput();
            }
        }

        return back()->withErrors([
            'email' => 'البيانات المدخلة غير صحيحة.',
        ])->onlyInput('email');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'company_name' => 'required|string|max:255',
            'tax_number' => 'nullable|string|max:20',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|string|max:20',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'name.required' => 'الاسم الكامل مطلوب',
            'company_name.required' => 'اسم الجهة مطلوب',
            'tax_number.max' => 'الرقم الضريبي يجب ألا يزيد عن 20 حرف',
            'email.required' => 'البريد الإلكتروني مطلوب',
            'email.email' => 'صيغة البريد الإلكتروني غير صحيحة',
            'email.unique' => 'البريد الإلكتروني مستخدم بالفعل',
            'phone.required' => 'رقم الهاتف مطلوب',
            'password.required' => 'كلمة المرور مطلوبة',
            'password.min' => 'كلمة المرور يجب أن تكون 8 أحرف على الأقل',
            'password.confirmed' => 'كلمة المرور غير متطابقة',
        ]);

        // حفظ بيانات التسجيل في الـ session
        $request->session()->put('registration_data', $validated);

        // إرسال OTP للبريد الإلكتروني
        try {
            OtpVerification::generate($validated['email'], 'email_verification');
            
            // حفظ البريد في الـ session
            $request->session()->put('otp_email', $validated['email']);
            $request->session()->put('otp_type', 'email_verification');
            
            return redirect()->route('otp.verify.form')
                ->with('success', 'تم إنشاء حسابك بنجاح! تم إرسال كود التحقق إلى بريدك الإلكتروني')
                ->with('show_success_message', true);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'فشل في إرسال كود التحقق: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}

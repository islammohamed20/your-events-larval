<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\OtpVerification;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        // ─── reCAPTCHA v3 ───────────────────────────────────────────────────
        if (config('services.recaptcha.enabled') && config('services.recaptcha.secret_key')) {
            $token = $request->input('recaptcha_token');
            if (! $token || ! $this->verifyRecaptcha($token)) {
                return back()->withErrors(['email' => 'فشل التحقق من reCAPTCHA. أعد المحاولة.'])->withInput();
            }
        }
        // ─────────────────────────────────────────────────────────────────────

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            /** @var \App\Models\User $user */
            $user = Auth::user();

            if ($user->isAdmin()) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()
                    ->withErrors(['email' => 'تسجيل الدخول من هذه الصفحة مخصص للعملاء فقط.'], 'customer_login')
                    ->onlyInput('email');
            }

            if (Supplier::where('email', $user->email)->exists()) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()
                    ->withErrors(['email' => 'هذا البريد مرتبط بحساب مورد. يرجى استخدام صفحة دخول المورد.'], 'customer_login')
                    ->onlyInput('email');
            }

            // OTP للعملاء
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

                return back()->withErrors(['error' => 'فشل في إرسال كود التحقق: '.$e->getMessage()])
                    ->withInput();
            }
        }

        return back()->withErrors([
            'email' => 'البيانات المدخلة غير صحيحة.',
        ])->onlyInput('email');
    }

    // ─── reCAPTCHA helper ────────────────────────────────────────────────────
    private function verifyRecaptcha(string $token): bool
    {
        try {
            $response = Http::timeout(5)->asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret'   => config('services.recaptcha.secret_key'),
                'response' => $token,
                'remoteip' => request()->ip(),
            ]);

            $data  = $response->json();
            $score = $data['score'] ?? 0;

            \Illuminate\Support\Facades\Log::info('reCAPTCHA result', [
                'success' => $data['success'] ?? false,
                'score'   => $score,
                'action'  => $data['action'] ?? '',
            ]);

            return ($data['success'] ?? false)
                && $score >= config('services.recaptcha.threshold', 0.3);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('reCAPTCHA check failed, allowing: ' . $e->getMessage());
            return true; // في حالة فشل الاتصال، نسمح بالمرور
        }
    }

    public function showRegister()
    {
        return redirect()->route('login')->with('error', 'التسجيل المباشر غير متاح حالياً. يرجى التواصل مع الإدارة.');
    }

    public function register(Request $request)
    {
        return redirect()->route('login')->with('error', 'التسجيل المباشر غير متاح حالياً.');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}

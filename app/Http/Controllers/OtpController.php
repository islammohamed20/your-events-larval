<?php

namespace App\Http\Controllers;

use App\Models\OtpVerification;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Validator;

class OtpController extends Controller
{
    /**
     * Show OTP verification form
     */
    public function showVerifyForm(Request $request)
    {
        $email = $request->session()->get('otp_email');
        $type = $request->session()->get('otp_type', 'email_verification');

        if (! $email) {
            if ($type === 'login') {
                return redirect()->route('login')->with('error', 'الرجاء تسجيل الدخول لإرسال كود التحقق');
            }
            if ($type === 'supplier_login') {
                return redirect()->route('supplier.login')->with('error', 'الرجاء تسجيل الدخول كمورد لإرسال كود التحقق');
            }

            return redirect()->route('register')->with('error', 'الرجاء إدخال بريدك الإلكتروني أولاً');
        }

        return view('auth.verify-otp', compact('email', 'type'));
    }

    /**
     * Send OTP to email
     */
    public function sendOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'type' => 'required|in:email_verification,login,password_reset,booking_confirmation,payment_confirmation,supplier_login',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'البيانات المدخلة غير صحيحة',
                'errors' => $validator->errors(),
            ], 422);
        }

        $email = $request->email;
        $type = $request->type;

        // Rate limiting: 3 attempts per 5 minutes
        $key = 'send-otp:'.$email;
        if (RateLimiter::tooManyAttempts($key, 3)) {
            $seconds = RateLimiter::availableIn($key);

            return response()->json([
                'success' => false,
                'message' => "تم تجاوز الحد الأقصى من المحاولات. حاول مرة أخرى بعد {$seconds} ثانية",
            ], 429);
        }

        RateLimiter::hit($key, 300); // 5 minutes

        try {
            // التحقق من وجود البريد في حالة التسجيل
            if ($type === 'email_verification') {
                $exists = User::where('email', $email)->exists();
                if ($exists) {
                    return response()->json([
                        'success' => false,
                        'message' => 'البريد الإلكتروني مسجل مسبقاً',
                    ], 422);
                }
            }

            // التحقق من وجود البريد في حالة إعادة التعيين أو تسجيل الدخول
            if (in_array($type, ['password_reset', 'login'])) {
                $exists = User::where('email', $email)->exists();
                if (! $exists) {
                    return response()->json([
                        'success' => false,
                        'message' => 'البريد الإلكتروني غير مسجل',
                    ], 422);
                }
            }

            // التحقق من وجود البريد لحالات تسجيل دخول الموردين
            if ($type === 'supplier_login') {
                $exists = Supplier::where('email', $email)->exists();
                if (! $exists) {
                    return response()->json([
                        'success' => false,
                        'message' => 'البريد الإلكتروني غير مسجل كمورد',
                    ], 422);
                }
            }

            // إنشاء وإرسال OTP
            $otp = OtpVerification::generate($email, $type);

            // حفظ البريد في الجلسة
            $request->session()->put('otp_email', $email);
            $request->session()->put('otp_type', $type);

            return response()->json([
                'success' => true,
                'message' => 'تم إرسال كود التحقق إلى بريدك الإلكتروني',
                'expires_in' => 10, // minutes
            ]);

        } catch (\Exception $e) {
            Log::error('OTP Send Error: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء إرسال كود التحقق. حاول مرة أخرى',
            ], 500);
        }
    }

    /**
     * Verify OTP
     */
    public function verifyOtp(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'otp' => 'required|string|size:6',
                'type' => 'required|in:email_verification,login,password_reset,booking_confirmation,payment_confirmation,supplier_login',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'البيانات المدخلة غير صحيحة',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $email = $request->email;
            $otp = $request->otp;
            $type = $request->type;

            // Rate limiting: 5 attempts per minute
            $key = 'verify-otp:'.$email;
            if (RateLimiter::tooManyAttempts($key, 5)) {
                return response()->json([
                    'success' => false,
                    'message' => 'تم تجاوز الحد الأقصى من المحاولات. حاول مرة أخرى لاحقاً',
                ], 429);
            }

            RateLimiter::hit($key, 60); // 1 minute

            // العثور على سجل OTP
            $otpRecord = OtpVerification::where('email', $email)
                ->where('type', $type)
                ->where('status', 'pending')
                ->latest()
                ->first();

            if (! $otpRecord) {
                return response()->json([
                    'success' => false,
                    'message' => 'لا توجد عملية تحقق نشطة. يرجى طلب كود جديد',
                ], 422);
            }

            // زيادة عدد المحاولات
            $otpRecord->incrementAttempts();

            // التحقق من الكود
            $result = OtpVerification::verify($email, $otp, $type);

            if ($result['success']) {
                // حفظ في الجلسة للاستخدام في الخطوة التالية
                $request->session()->put('otp_verified', true);
                $request->session()->put('otp_email', $email);
                $request->session()->put('otp_type', $type);

                // في حالة التحقق لتسجيل الدخول، نقوم بتسجيل الدخول فعلياً
                $redirectUrl = $this->getRedirectUrl($type);
                if ($type === 'login') {
                    try {
                        $userId = $request->session()->get('login_user_id');
                        $remember = (bool) $request->session()->get('login_remember', false);
                        if ($userId) {
                            $user = \App\Models\User::find($userId);
                        } else {
                            $user = \App\Models\User::where('email', $email)->first();
                        }

                        if ($user) {
                            Auth::login($user, $remember);
                            $request->session()->regenerate();
                            try {
                                DB::table(config('session.table', 'sessions'))
                                    ->where('user_id', $user->getAuthIdentifier())
                                    ->where('id', '!=', $request->session()->getId())
                                    ->delete();
                            } catch (\Throwable $e) {
                            }

                            // تحديث آخر تسجيل دخول
                            try {
                                $user->last_login_at = now();
                                $user->save();
                            } catch (\Throwable $e) {
                                // تجاهل أي خطأ يتعلق بعمود غير موجود
                            }

                            // تحديد إعادة التوجيه حسب الدور أو وجود حساب مورد
                            if ($user->isAdmin()) {
                                $redirectUrl = route('admin.dashboard');
                            } else {
                                // إن كان للمستخدم حساب مورد (موافق ومؤكد البريد)، نسجّل دخوله كـ مورّد ونحوّله للوحة الموردين
                                try {
                                    $supplier = \App\Models\Supplier::where('email', $user->email)->first();
                                    if ($supplier && $supplier->status === 'approved' && $supplier->email_verified_at) {
                                        // تسجيل خروج من حارس الويب ثم تسجيل دخول على حارس المورد
                                        Auth::logout();
                                        $remember = (bool) $request->session()->get('login_remember', false);
                                        Auth::guard('supplier')->login($supplier, $remember);
                                        $request->session()->regenerate();
                                        try {
                                            DB::table(config('session.table', 'sessions'))
                                                ->where('user_id', $supplier->getAuthIdentifier())
                                                ->where('id', '!=', $request->session()->getId())
                                                ->delete();
                                        } catch (\Throwable $e) {
                                        }
                                        $redirectUrl = route('supplier.dashboard');
                                    } else {
                                        $redirectUrl = url('/');
                                    }
                                } catch (\Throwable $e) {
                                    \Illuminate\Support\Facades\Log::error('Supplier auto-login after OTP failed: '.$e->getMessage());
                                    $redirectUrl = url('/');
                                }
                            }

                            // سجل نشاط تسجيل الدخول (OTP)
                            try {
                                \App\Models\LoginActivity::create([
                                    'user_id' => $user->id,
                                    'ip_address' => $request->ip(),
                                    'country' => null,
                                    'successful' => true,
                                    'method' => 'otp',
                                ]);
                            } catch (\Throwable $e) {
                                // تجاهل أي خطأ هنا
                            }
                        }

                        // تنظيف بيانات الجلسة المؤقتة الخاصة بتسجيل الدخول
                        $request->session()->forget(['login_pending', 'login_remember', 'login_user_id']);
                    } catch (\Exception $e) {
                        \Illuminate\Support\Facades\Log::error('Login after OTP failed: '.$e->getMessage());
                    }
                } elseif ($type === 'supplier_login') {
                    try {
                        $supplierId = $request->session()->get('supplier_login_supplier_id');
                        $remember = (bool) $request->session()->get('supplier_login_remember', false);
                        if ($supplierId) {
                            $supplier = \App\Models\Supplier::find($supplierId);
                        } else {
                            $supplier = \App\Models\Supplier::where('email', $email)->first();
                        }
                        if ($supplier && $supplier->status === 'approved' && $supplier->email_verified_at) {
                            Auth::guard('supplier')->login($supplier, $remember);
                            $request->session()->regenerate();
                            try {
                                DB::table(config('session.table', 'sessions'))
                                    ->where('user_id', $supplier->getAuthIdentifier())
                                    ->where('id', '!=', $request->session()->getId())
                                    ->delete();
                            } catch (\Throwable $e) {
                            }
                            try {
                                $supplier->forceFill(['last_login_at' => now()])->save();
                            } catch (\Throwable $e) {
                            }
                            $redirectUrl = route('supplier.dashboard');
                        } else {
                            $redirectUrl = route('supplier.login');
                        }
                        $request->session()->forget(['supplier_login_pending', 'supplier_login_remember', 'supplier_login_supplier_id']);
                    } catch (\Exception $e) {
                        \Illuminate\Support\Facades\Log::error('Supplier login after OTP failed: '.$e->getMessage());
                        $redirectUrl = route('supplier.login');
                    }
                }

                Log::info('OTP verified successfully', [
                    'email' => $email,
                    'type' => $type,
                    'session_data' => $request->session()->all(),
                ]);

                return response()->json([
                    'success' => true,
                    'message' => $result['message'],
                    'redirect' => $redirectUrl,
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => $result['message'],
            ], 422);

        } catch (\Exception $e) {
            Log::error('OTP Verify Exception: '.$e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ في النظام: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Resend OTP
     */
    public function resendOtp(Request $request)
    {
        return $this->sendOtp($request);
    }

    /**
     * Get redirect URL based on OTP type
     */
    private function getRedirectUrl($type)
    {
        try {
            $urls = [
                'email_verification' => route('register.complete'),
                'login' => route('login'),
                'password_reset' => route('password.reset.form'),
                'booking_confirmation' => route('booking.my-bookings'),
                'payment_confirmation' => route('home'),
                'supplier_login' => route('supplier.login'),
            ];

            return $urls[$type] ?? route('home');
        } catch (\Exception $e) {
            Log::error('getRedirectUrl error: '.$e->getMessage());

            return route('home');
        }
    }

    /**
     * Complete registration after OTP verification
     */
    public function completeRegistration(Request $request)
    {
        Log::info('Complete Registration called', [
            'otp_verified' => $request->session()->get('otp_verified'),
            'otp_email' => $request->session()->get('otp_email'),
            'registration_data' => $request->session()->get('registration_data'),
            'all_session' => $request->session()->all(),
        ]);

        // التحقق من أن OTP تم التحقق منه
        if (! $request->session()->get('otp_verified')) {
            Log::warning('OTP not verified in session');

            return redirect()->route('register')->with('error', 'يرجى التحقق من بريدك الإلكتروني أولاً');
        }

        // الحصول على بيانات التسجيل من الـ session
        $registrationData = $request->session()->get('registration_data');

        if (! $registrationData) {
            Log::warning('Registration data not found in session');

            return redirect()->route('register')->with('error', 'انتهت صلاحية الجلسة. يرجى التسجيل مرة أخرى');
        }

        try {
            $email = $request->session()->get('otp_email');

            Log::info('Creating user', ['email' => $email, 'data' => $registrationData]);

            // إنشاء المستخدم
            $user = User::create([
                'name' => $registrationData['name'],
                'company_name' => $registrationData['company_name'],
                'tax_number' => $registrationData['tax_number'] ?? null,
                'email' => $email,
                'phone' => $registrationData['phone'],
                'password' => Hash::make($registrationData['password']),
                'email_verified_at' => now(),
            ]);

            Log::info('User created successfully', ['user_id' => $user->id]);

            // تسجيل الدخول
            Auth::login($user);

            // مسح الجلسة
            $request->session()->forget(['otp_verified', 'otp_email', 'otp_type', 'registration_data']);

            return redirect('/')->with('success', 'تم إنشاء حسابك بنجاح! مرحباً بك في Your Events');

        } catch (\Exception $e) {
            Log::error('Registration Error: '.$e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء إنشاء الحساب: '.$e->getMessage());
        }
    }

    /**
     * Clean expired OTPs (for scheduled task)
     */
    public function cleanExpired()
    {
        OtpVerification::cleanExpired();

        return response()->json(['success' => true, 'message' => 'تم تنظيف الأكواد المنتهية']);
    }
}

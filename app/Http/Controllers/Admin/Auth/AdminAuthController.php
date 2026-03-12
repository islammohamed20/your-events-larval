<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class AdminAuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check() && (Auth::user()->is_admin || Auth::user()->role === 'admin')) {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $user = Auth::user();

            if ($user->is_admin || $user->role === 'admin') {
                $request->session()->regenerate();
                $newSessionVersion = ((int) ($user->session_version ?: 1)) + 1;
                $request->session()->put('user_session_version', $newSessionVersion);

                $user->forceFill([
                    'last_login_at' => now(),
                    'session_version' => $newSessionVersion,
                    'remember_token' => Str::random(60),
                ])->save();

                // Check if user has any registered passkeys — if not, prompt registration
                $hasPasskeys = \App\Models\Passkey::where('user_id', $user->id)
                    ->where('user_type', 'user')
                    ->exists();

                if (! $hasPasskeys) {
                    $request->session()->put('biometric_register_user_id', $user->id);
                    $request->session()->put('biometric_register_user_type', 'user');
                    $request->session()->put('admin_biometric_prompt', true);
                }

                if ($user->must_change_password) {
                    return redirect()->route('admin.force-password.edit');
                }

                return redirect()->intended(route('admin.dashboard'));
            }

            Auth::logout();
            return back()->withErrors([
                'email' => 'ليس لديك صلاحية الدخول كمسؤول.',
            ])->onlyInput('email');
        }

        return back()->withErrors([
            'email' => 'البيانات المدخلة غير صحيحة.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }

    public function showForcePasswordForm()
    {
        return view('admin.auth.force-password');
    }

    public function updateForcedPassword(Request $request)
    {
        $validated = $request->validate([
            'password' => ['required', 'confirmed', Password::defaults()],
        ], [
            'password.required' => 'كلمة المرور الجديدة مطلوبة',
            'password.confirmed' => 'تأكيد كلمة المرور غير متطابق',
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        $newSessionVersion = (int) ($user->session_version ?: 1);
        $updates = [
            'password' => Hash::make($validated['password']),
            'must_change_password' => false,
        ];

        if ($user->logout_other_devices) {
            $newSessionVersion++;
            $updates['session_version'] = $newSessionVersion;
            $updates['remember_token'] = Str::random(60);
        }

        $updates['logout_other_devices'] = false;

        $user->update($updates);
        $request->session()->put('user_session_version', $newSessionVersion);

        return redirect()->route('admin.dashboard')
            ->with('success', 'تم تغيير كلمة المرور بنجاح.');
    }
}

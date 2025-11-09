<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display the user's profile.
     */
    public function show()
    {
        $user = Auth::user();
        $bookings = $user->bookings()
            ->with(['package', 'services'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('profile.show', compact('user', 'bookings'));
    }

    /**
     * Show the form for editing the profile.
     */
    public function edit()
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }

    /**
     * Update the user's profile.
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'company_name' => ['required', 'string', 'max:255'],
            'tax_number' => ['nullable', 'string', 'max:20'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'phone' => ['nullable', 'string', 'max:20'],
            'card_type' => ['nullable', 'in:visa,mastercard,mada'],
            'card_holder_name' => ['nullable', 'string', 'max:255'],
            'card_last_four' => ['nullable', 'string', 'size:4', 'regex:/^[0-9]{4}$/'],
            'card_expiry_month' => ['nullable', 'string', 'size:2', 'regex:/^(0[1-9]|1[0-2])$/'],
            'card_expiry_year' => ['nullable', 'string', 'size:4', 'regex:/^[0-9]{4}$/'],
        ], [
            'name.required' => 'الاسم مطلوب',
            'company_name.required' => 'اسم الجهة مطلوب',
            'email.required' => 'البريد الإلكتروني مطلوب',
            'email.email' => 'البريد الإلكتروني غير صحيح',
            'email.unique' => 'البريد الإلكتروني مستخدم من قبل',
            'card_type.in' => 'نوع البطاقة غير صحيح',
            'card_last_four.size' => 'يجب إدخال 4 أرقام فقط',
            'card_last_four.regex' => 'يجب أن تكون أرقام فقط',
            'card_expiry_month.regex' => 'يجب إدخال شهر صحيح (01-12)',
            'card_expiry_year.regex' => 'يجب إدخال سنة صحيحة (4 أرقام)',
        ]);

        $user->update($validated);

        return redirect()->route('profile.show')
            ->with('success', 'تم تحديث البيانات بنجاح');
    }

    /**
     * Show the form for changing password.
     */
    public function editPassword()
    {
        return view('profile.password');
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ], [
            'current_password.required' => 'كلمة المرور الحالية مطلوبة',
            'current_password.current_password' => 'كلمة المرور الحالية غير صحيحة',
            'password.required' => 'كلمة المرور الجديدة مطلوبة',
            'password.confirmed' => 'كلمة المرور غير متطابقة',
        ]);

        Auth::user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('profile.show')
            ->with('success', 'تم تغيير كلمة المرور بنجاح');
    }
}


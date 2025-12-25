<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\LoginActivity;
use App\Models\OtpVerification;
use App\Models\User;
use App\Models\Visit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        $users = User::latest()->paginate(15);

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'company_name' => 'required|string|max:255',
            'tax_number' => 'nullable|string|max:20',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:8|confirmed',
            'is_admin' => 'boolean',
            'card_type' => 'nullable|in:visa,mastercard,mada',
            'card_holder_name' => 'nullable|string|max:255',
            'card_last_four' => 'nullable|string|size:4|regex:/^[0-9]{4}$/',
            'card_expiry_month' => 'nullable|string|size:2|regex:/^(0[1-9]|1[0-2])$/',
            'card_expiry_year' => 'nullable|string|size:4|regex:/^[0-9]{4}$/',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['is_admin'] = $request->has('is_admin');

        User::create($validated);

        return redirect()->route('admin.users.index')
            ->with('success', 'تم إنشاء المستخدم بنجاح');
    }

    public function show(User $user)
    {
        $bookings = $user->bookings()->latest()->paginate(10);

        return view('admin.users.show', compact('user', 'bookings'));
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'company_name' => 'required|string|max:255',
            'tax_number' => 'nullable|string|max:20',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:8|confirmed',
            'is_admin' => 'boolean',
            'card_type' => 'nullable|in:visa,mastercard,mada',
            'card_holder_name' => 'nullable|string|max:255',
            'card_last_four' => 'nullable|string|size:4|regex:/^[0-9]{4}$/',
            'card_expiry_month' => 'nullable|string|size:2|regex:/^(0[1-9]|1[0-2])$/',
            'card_expiry_year' => 'nullable|string|size:4|regex:/^[0-9]{4}$/',
        ]);

        if ($validated['password']) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $validated['is_admin'] = $request->has('is_admin');

        $user->update($validated);

        return redirect()->route('admin.users.index')
            ->with('success', 'تم تحديث المستخدم بنجاح');
    }

    public function destroy(User $user)
    {
        // Prevent deleting the current admin user
        if ($user->id === Auth::id()) {
            return redirect()->route('admin.users.index')
                ->with('error', 'لا يمكنك حذف حسابك الخاص');
        }

        // Check if user has bookings
        if ($user->bookings()->count() > 0) {
            return redirect()->route('admin.users.index')
                ->with('error', 'لا يمكن حذف المستخدم لأنه يحتوي على حجوزات');
        }

        // Cleanup related data before permanent deletion
        // Remove quotes (FK cascade will handle items)
        $user->quotes()->delete();

        // Remove wishlists
        $user->wishlists()->delete();

        // Remove visits and login activities
        Visit::where('user_id', $user->id)->delete();
        LoginActivity::where('user_id', $user->id)->delete();

        // Remove OTP verifications
        OtpVerification::where('email', $user->email)->delete();

        // Remove activity logs referencing this user
        ActivityLog::where('subject_type', User::class)
            ->where('subject_id', $user->id)
            ->delete();
        ActivityLog::where('actor_type', User::class)
            ->where('actor_id', $user->id)
            ->delete();

        // حذف نهائي للمستخدم (الموديل لا يستخدم SoftDeletes، لذا delete كافٍ)
        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'تم حذف المستخدم بنجاح');
    }

    public function toggleAdmin(User $user)
    {
        // Prevent removing admin privileges from the current user
        if ($user->id === Auth::id() && $user->is_admin) {
            return redirect()->route('admin.users.index')
                ->with('error', 'لا يمكنك إزالة صلاحيات الإدارة من حسابك الخاص');
        }

        $user->update(['is_admin' => ! $user->is_admin]);

        $message = $user->is_admin ? 'تم منح صلاحيات الإدارة للمستخدم' : 'تم إزالة صلاحيات الإدارة من المستخدم';

        return redirect()->route('admin.users.index')
            ->with('success', $message);
    }
}

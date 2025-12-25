<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
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
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'is_admin' => 'boolean',
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
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:8|confirmed',
            'is_admin' => 'boolean',
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

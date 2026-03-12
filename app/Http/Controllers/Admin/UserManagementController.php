<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Passkey;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserManagementController extends Controller
{
    public function index()
    {
        // عرض المستخدمين المصرحين فقط (المديرين)
        $users = User::where(function ($query) {
            $query->where('is_admin', true)
                ->orWhere('role', 'admin');
        })->latest()->paginate(15);

        return view('admin.user-management.index', compact('users'));
    }

    public function create()
    {
        return view('admin.user-management.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'status' => 'required|in:active,inactive,suspended',
            'must_change_password' => 'nullable|boolean',
            'logout_other_devices' => 'nullable|boolean',
            'permissions' => 'required|array|min:1',
            'permissions.*' => 'in:manage_users,manage_emails,manage_services,manage_categories,manage_packages,manage_customers,manage_bookings,customers.view,customers.edit,customers.delete,customers.export,customers.reset_password,bookings.view,bookings.edit,bookings.delete,quotes.view,quotes.edit,quotes.delete',
        ]);

        $mustChangePassword = $request->boolean('must_change_password');

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'address' => $request->address,
            'status' => $request->status,
            'is_admin' => true, // Always set as admin for user management
            'must_change_password' => $mustChangePassword,
            'logout_other_devices' => $mustChangePassword && $request->boolean('logout_other_devices'),
            'permissions' => $request->input('permissions', User::ADMIN_PERMISSIONS),
        ]);

        return redirect()->route('admin.user-management.index')
            ->with('success', 'تم إنشاء المستخدم المصرح بنجاح.');
    }

    public function show(User $user)
    {
        // التأكد من أن المستخدم مصرح
        if (! $user->isAdmin()) {
            abort(404, 'المستخدم غير موجود');
        }

        $bookings = $user->bookings()->latest()->paginate(10);

        return view('admin.user-management.show', compact('user', 'bookings'));
    }

    public function edit(User $user)
    {
        // التأكد من أن المستخدم مصرح
        if (! $user->isAdmin()) {
            abort(404, 'المستخدم غير موجود');
        }

        return view('admin.user-management.edit', compact('user'));
    }

    public function passkeys(User $user)
    {
        if (! $user->isAdmin()) {
            abort(404, 'المستخدم غير موجود');
        }

        $passkeys = Passkey::forUser($user->id, 'user')
            ->orderByDesc('id')
            ->get();

        return view('admin.user-management.passkeys', compact('user', 'passkeys'));
    }

    public function destroyPasskey(User $user, Passkey $passkey)
    {
        if (! $user->isAdmin()) {
            abort(404, 'المستخدم غير موجود');
        }

        if ($passkey->user_id !== $user->id || $passkey->user_type !== 'user') {
            abort(404);
        }

        $passkey->delete();

        return redirect()
            ->route('admin.user-management.passkeys', $user)
            ->with('success', 'تم حذف البصمة بنجاح');
    }

    public function destroyAllPasskeys(User $user)
    {
        if (! $user->isAdmin()) {
            abort(404, 'المستخدم غير موجود');
        }

        Passkey::forUser($user->id, 'user')->delete();

        return redirect()
            ->route('admin.user-management.passkeys', $user)
            ->with('success', 'تم حذف جميع البصمات بنجاح');
    }

    public function update(Request $request, User $user)
    {
        // Ensure we're only updating admin users
        if (! $user->isAdmin()) {
            return redirect()->route('admin.user-management.index')
                ->with('error', 'لا يمكن تعديل هذا المستخدم من هنا.');
        }

        $permissionsRule = $user->role === 'admin' ? 'nullable|array' : 'required|array|min:1';

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'status' => 'required|in:active,inactive,suspended',
            'must_change_password' => 'nullable|boolean',
            'logout_other_devices' => 'nullable|boolean',
            'permissions' => $permissionsRule,
            'permissions.*' => 'in:manage_users,manage_emails,manage_services,manage_categories,manage_packages,manage_customers,manage_bookings,customers.view,customers.edit,customers.delete,customers.export,customers.reset_password,bookings.view,bookings.edit,bookings.delete,quotes.view,quotes.edit,quotes.delete',
        ]);

        $mustChangePassword = $request->boolean('must_change_password');
        $logoutOtherDevices = $mustChangePassword && $request->boolean('logout_other_devices');

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'status' => $request->status,
            'must_change_password' => $mustChangePassword,
            'logout_other_devices' => $logoutOtherDevices,
            'permissions' => $user->role === 'admin'
                ? (is_array($user->permissions) && ! empty($user->permissions) ? $user->permissions : User::ADMIN_PERMISSIONS)
                : $request->input('permissions', []),
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        if ($logoutOtherDevices) {
            $data['session_version'] = ((int) $user->session_version) + 1;
            $data['remember_token'] = Str::random(60);
        }

        $user->update($data);

        if ($logoutOtherDevices && $user->id === Auth::id()) {
            $request->session()->put('user_session_version', (int) $user->fresh()->session_version);
        }

        return redirect()->route('admin.user-management.index')
            ->with('success', 'تم تحديث المستخدم بنجاح.');
    }

    public function destroy(User $user)
    {
        // التأكد من أن المستخدم مصرح
        if (! $user->isAdmin()) {
            abort(404, 'المستخدم غير موجود');
        }

        // منع حذف المستخدم الحالي
        if ($user->id === Auth::id()) {
            return redirect()->route('admin.user-management.index')
                ->with('error', 'لا يمكنك حذف حسابك الخاص');
        }

        // التحقق من وجود حجوزات
        if ($user->bookings()->count() > 0) {
            return redirect()->route('admin.user-management.index')
                ->with('error', 'لا يمكن حذف المستخدم لأنه يحتوي على حجوزات');
        }

        $user->delete();

        return redirect()->route('admin.user-management.index')
            ->with('success', 'تم حذف المستخدم بنجاح');
    }

    public function toggleAdmin(User $user)
    {
        // منع إزالة صلاحيات الإدارة من المستخدم الحالي
        if ($user->id === Auth::id() && $user->is_admin) {
            return redirect()->route('admin.user-management.index')
                ->with('error', 'لا يمكنك إزالة صلاحيات الإدارة من حسابك الخاص');
        }

        $user->update(['is_admin' => ! $user->is_admin]);

        $message = $user->is_admin ? 'تم منح صلاحيات الإدارة للمستخدم' : 'تم إزالة صلاحيات الإدارة من المستخدم';

        return redirect()->route('admin.user-management.index')
            ->with('success', $message);
    }

    public function permissions()
    {
        // صفحة إدارة الصلاحيات
        $users = User::where(function ($query) {
            $query->where('is_admin', true)
                ->orWhere('role', 'admin');
        })->get();

        return view('admin.user-management.permissions', compact('users'));
    }
}

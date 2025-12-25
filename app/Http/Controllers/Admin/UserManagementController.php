<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserManagementController extends Controller
{
    public function index()
    {
        // عرض المستخدمين المصرحين فقط (المديرين)
        $users = User::where('is_admin', true)->latest()->paginate(15);

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
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'address' => $request->address,
            'is_admin' => true, // Always set as admin for user management
        ]);

        return redirect()->route('admin.user-management.index')
            ->with('success', 'تم إنشاء المستخدم المصرح بنجاح.');
    }

    public function show(User $user)
    {
        // التأكد من أن المستخدم مصرح
        if (! $user->is_admin) {
            abort(404, 'المستخدم غير موجود');
        }

        $bookings = $user->bookings()->latest()->paginate(10);

        return view('admin.user-management.show', compact('user', 'bookings'));
    }

    public function edit(User $user)
    {
        // التأكد من أن المستخدم مصرح
        if (! $user->is_admin) {
            abort(404, 'المستخدم غير موجود');
        }

        return view('admin.user-management.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        // Ensure we're only updating admin users
        if (! $user->is_admin) {
            return redirect()->route('admin.user-management.index')
                ->with('error', 'لا يمكن تعديل هذا المستخدم من هنا.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('admin.user-management.index')
            ->with('success', 'تم تحديث المستخدم بنجاح.');
    }

    public function destroy(User $user)
    {
        // التأكد من أن المستخدم مصرح
        if (! $user->is_admin) {
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
        $users = User::where('is_admin', true)->get();

        return view('admin.user-management.permissions', compact('users'));
    }
}

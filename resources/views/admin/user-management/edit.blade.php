@extends('layouts.admin')

@section('title', 'تعديل المستخدم')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">تعديل المستخدم</h3>
                    <a href="{{ route('admin.user-management.show', $user) }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> العودة
                    </a>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('admin.user-management.update', $user) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-8">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">البيانات الشخصية</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="name" class="form-label">الاسم <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                                           id="name" name="name" value="{{ old('name', $user->name) }}" required>
                                                    @error('name')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="email" class="form-label">البريد الإلكتروني <span class="text-danger">*</span></label>
                                                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                                           id="email" name="email" value="{{ old('email', $user->email) }}" required>
                                                    @error('email')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="phone" class="form-label">رقم الهاتف</label>
                                                    <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                                           id="phone" name="phone" value="{{ old('phone', $user->phone) }}">
                                                    @error('phone')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="status" class="form-label">الحالة <span class="text-danger">*</span></label>
                                                    <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                                        <option value="active" {{ old('status', $user->status) === 'active' ? 'selected' : '' }}>نشط</option>
                                                        <option value="inactive" {{ old('status', $user->status) === 'inactive' ? 'selected' : '' }}>غير نشط</option>
                                                        <option value="suspended" {{ old('status', $user->status) === 'suspended' ? 'selected' : '' }}>معلق</option>
                                                    </select>
                                                    @error('status')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="address" class="form-label">العنوان</label>
                                            <textarea class="form-control @error('address') is-invalid @enderror"
                                                      id="address" name="address" rows="2">{{ old('address', $user->address) }}</textarea>
                                            @error('address')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>



                                <div class="card mt-3">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">تغيير كلمة المرور</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="alert alert-info">
                                            <i class="fas fa-info-circle"></i>
                                            اتركها فارغة إذا كنت لا تريد تغيير كلمة المرور
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="password" class="form-label">كلمة المرور الجديدة</label>
                                                    <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                                           id="password" name="password">
                                                    @error('password')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="password_confirmation" class="form-label">تأكيد كلمة المرور</label>
                                                    <input type="password" class="form-control" 
                                                           id="password_confirmation" name="password_confirmation">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="card border-warning-subtle bg-warning-subtle mt-3 mb-0">
                                            <div class="card-body">
                                                <h6 class="mb-3">إعدادات الأمان</h6>
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input" type="checkbox" id="must_change_password" name="must_change_password" value="1" {{ old('must_change_password', $user->must_change_password) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="must_change_password">إلزام المستخدم بتغيير كلمة المرور عند تسجيل الدخول لأول مرة</label>
                                                </div>
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input" type="checkbox" id="logout_other_devices" name="logout_other_devices" value="1" {{ old('logout_other_devices', $user->must_change_password ? $user->logout_other_devices : true) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="logout_other_devices">تسجيل الخروج من جميع الأجهزة بعد تغيير كلمة المرور <span class="text-muted">(موصى به)</span></label>
                                                </div>
                                                <div class="form-text">عند تفعيل هذا الخيار سيتم إنهاء الجلسات الأخرى للمستخدم أثناء تطبيق سياسة تغيير كلمة المرور.</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">معلومات المستخدم</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label class="form-label text-muted">تاريخ التسجيل</label>
                                            <p class="fw-bold">{{ $user->created_at->format('Y-m-d H:i') }}</p>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label class="form-label text-muted">آخر تحديث</label>
                                            <p class="fw-bold">{{ $user->updated_at->format('Y-m-d H:i') }}</p>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label class="form-label text-muted">آخر دخول</label>
                                            <p class="fw-bold">
                                                @if($user->last_login_at)
                                                    {{ $user->last_login_at->format('Y-m-d H:i') }}
                                                @else
                                                    لم يسجل دخول
                                                @endif
                                            </p>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label text-muted">سياسة الأمان</label>
                                            <div class="d-flex flex-column gap-2">
                                                @if($user->must_change_password)
                                                    <span class="badge bg-warning text-dark">مطلوب تغيير كلمة المرور عند أول دخول</span>
                                                @else
                                                    <span class="badge bg-secondary">لا يوجد تغيير إجباري لكلمة المرور</span>
                                                @endif

                                                @if($user->must_change_password && $user->logout_other_devices)
                                                    <span class="badge bg-info text-dark">سيتم تسجيل الخروج من الأجهزة الأخرى</span>
                                                @endif
                                            </div>
                                        </div>

                                        @if($user->role === 'admin')
                                            <div class="alert alert-info">
                                                <small>
                                                    <i class="fas fa-user-shield"></i>
                                                    <strong>حساب Admin:</strong> هذا النوع يمتلك صلاحيات كاملة ولا يستخدم الصلاحيات التفصيلية.
                                                </small>
                                            </div>
                                        @else
                                            <div class="alert alert-warning">
                                                <small>
                                                    <i class="fas fa-shield-alt"></i>
                                                    <strong>مستخدم إداري:</strong> صلاحياته تُدار من قسم الصلاحيات التفصيلية بالأسفل
                                                </small>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                @if($user->role !== 'admin')
                                    <div class="card mt-3">
                                        <div class="card-header">
                                            <h5 class="card-title mb-0">الصلاحيات التفصيلية</h5>
                                        </div>
                                        <div class="card-body">
                                        @php
                                            $storedPermissions = is_array($user->permissions) ? $user->permissions : [];
                                            $defaultPermissions = empty($storedPermissions) ? \App\Models\User::ADMIN_PERMISSIONS : $storedPermissions;
                                            $grantedPermissions = old('permissions', $defaultPermissions);
                                        @endphp
                                        <div class="mb-3">
                                            <label class="form-label d-block">قوالب صلاحيات جاهزة</label>
                                            <div class="btn-group" role="group" aria-label="Permission presets">
                                                <button type="button" class="btn btn-outline-primary btn-sm" onclick="applyPermissionPreset('full_admin')">مشرف كامل</button>
                                                <button type="button" class="btn btn-outline-success btn-sm" onclick="applyPermissionPreset('customers_manager')">مشرف عملاء</button>
                                                <button type="button" class="btn btn-outline-warning btn-sm" onclick="applyPermissionPreset('bookings_manager')">مشرف حجوزات</button>
                                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="applyPermissionPreset('read_only')">عرض فقط</button>
                                            </div>
                                        </div>

                                        <div class="mb-3 d-flex flex-wrap gap-2 align-items-center">
                                            <button type="button" class="btn btn-outline-dark btn-sm" onclick="setAllPermissions(true)">تحديد الكل</button>
                                            <button type="button" class="btn btn-outline-dark btn-sm" onclick="setAllPermissions(false)">إلغاء الكل</button>
                                            <span class="badge bg-info" id="permissionsCountBadge">0 صلاحية</span>
                                        </div>

                                        @error('permissions')
                                            <div class="alert alert-danger py-2">{{ $message }}</div>
                                        @enderror

                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" id="edit_perm_manage_users" name="permissions[]" value="manage_users" {{ in_array('manage_users', $grantedPermissions, true) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="edit_perm_manage_users">إدارة المستخدمين</label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" id="edit_perm_manage_emails" name="permissions[]" value="manage_emails" {{ in_array('manage_emails', $grantedPermissions, true) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="edit_perm_manage_emails">إدارة البريد الإلكتروني</label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" id="edit_perm_manage_whatsapp" name="permissions[]" value="manage_whatsapp" {{ in_array('manage_whatsapp', $grantedPermissions, true) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="edit_perm_manage_whatsapp">إدارة واتساب</label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" id="edit_perm_manage_services" name="permissions[]" value="manage_services" {{ in_array('manage_services', $grantedPermissions, true) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="edit_perm_manage_services">إدارة الخدمات</label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" id="edit_perm_manage_categories" name="permissions[]" value="manage_categories" {{ in_array('manage_categories', $grantedPermissions, true) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="edit_perm_manage_categories">إدارة الفئات</label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" id="edit_perm_manage_packages" name="permissions[]" value="manage_packages" {{ in_array('manage_packages', $grantedPermissions, true) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="edit_perm_manage_packages">إدارة الباقات</label>
                                        </div>
                                        <hr>
                                        <h6 class="mb-2">إدارة العملاء</h6>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" id="edit_perm_customers_view" name="permissions[]" value="customers.view" {{ in_array('customers.view', $grantedPermissions, true) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="edit_perm_customers_view">عرض العملاء</label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" id="edit_perm_customers_edit" name="permissions[]" value="customers.edit" {{ in_array('customers.edit', $grantedPermissions, true) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="edit_perm_customers_edit">تعديل العملاء</label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" id="edit_perm_customers_delete" name="permissions[]" value="customers.delete" {{ in_array('customers.delete', $grantedPermissions, true) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="edit_perm_customers_delete">حذف العملاء</label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" id="edit_perm_customers_export" name="permissions[]" value="customers.export" {{ in_array('customers.export', $grantedPermissions, true) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="edit_perm_customers_export">تصدير العملاء</label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" id="edit_perm_customers_reset" name="permissions[]" value="customers.reset_password" {{ in_array('customers.reset_password', $grantedPermissions, true) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="edit_perm_customers_reset">إعادة تعيين كلمة مرور العميل</label>
                                        </div>
                                        <hr>
                                        <h6 class="mb-2">إدارة الحجوزات وعروض الأسعار</h6>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" id="edit_perm_bookings_view" name="permissions[]" value="bookings.view" {{ in_array('bookings.view', $grantedPermissions, true) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="edit_perm_bookings_view">عرض الحجوزات</label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" id="edit_perm_bookings_edit" name="permissions[]" value="bookings.edit" {{ in_array('bookings.edit', $grantedPermissions, true) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="edit_perm_bookings_edit">تعديل حالة الحجز</label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" id="edit_perm_bookings_delete" name="permissions[]" value="bookings.delete" {{ in_array('bookings.delete', $grantedPermissions, true) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="edit_perm_bookings_delete">حذف الحجز</label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" id="edit_perm_quotes_view" name="permissions[]" value="quotes.view" {{ in_array('quotes.view', $grantedPermissions, true) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="edit_perm_quotes_view">عرض عروض الأسعار</label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" id="edit_perm_quotes_edit" name="permissions[]" value="quotes.edit" {{ in_array('quotes.edit', $grantedPermissions, true) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="edit_perm_quotes_edit">تعديل حالة عرض السعر</label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" id="edit_perm_quotes_delete" name="permissions[]" value="quotes.delete" {{ in_array('quotes.delete', $grantedPermissions, true) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="edit_perm_quotes_delete">حذف عرض السعر</label>
                                        </div>
                                        </div>
                                    </div>
                                @endif

                                <div class="card mt-3">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">البصمة</h5>
                                    </div>
                                    <div class="card-body">
                                        <a href="{{ route('admin.user-management.passkeys', $user) }}" class="btn btn-outline-primary w-100">
                                            <i class="fas fa-fingerprint"></i> إدارة البصمات
                                        </a>
                                    </div>
                                </div>

                                <div class="card mt-3">
                                    <div class="card-body text-center">
                                        <button type="submit" class="btn btn-primary btn-lg w-100">
                                            <i class="fas fa-save"></i> حفظ التغييرات
                                        </button>
                                        
                                        @if($user->id !== auth()->user()->id)
                                            <div class="mt-3">
                                                <button type="button" class="btn btn-outline-danger w-100" 
                                                        data-bs-toggle="modal" data-bs-target="#deleteModal">
                                                    <i class="fas fa-trash"></i> حذف المستخدم
                                                </button>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@if($user->id !== auth()->user()->id)
<!-- Modal للحذف -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">تأكيد الحذف</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>هل أنت متأكد من حذف المستخدم <strong>{{ $user->name }}</strong>؟</p>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle"></i>
                    هذا الإجراء لا يمكن التراجع عنه!
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <form action="{{ route('admin.user-management.destroy', $user) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">حذف المستخدم</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endif

<script>
function applyPermissionPreset(preset) {
    const all = document.querySelectorAll('input[name="permissions[]"]');
    all.forEach((el) => {
        el.checked = false;
    });

    const presets = {
        full_admin: [
            'manage_users', 'manage_whatsapp', 'manage_emails', 'manage_services', 'manage_categories', 'manage_packages',
            'customers.view', 'customers.edit', 'customers.delete', 'customers.export', 'customers.reset_password',
            'bookings.view', 'bookings.edit', 'bookings.delete',
            'quotes.view', 'quotes.edit', 'quotes.delete'
        ],
        customers_manager: [
            'customers.view', 'customers.edit', 'customers.delete', 'customers.export', 'customers.reset_password'
        ],
        bookings_manager: [
            'bookings.view', 'bookings.edit', 'bookings.delete',
            'quotes.view', 'quotes.edit', 'quotes.delete'
        ],
        read_only: [
            'customers.view', 'bookings.view', 'quotes.view', 'manage_whatsapp'
        ],
    };

    (presets[preset] || []).forEach((perm) => {
        const checkbox = document.querySelector('input[name="permissions[]"][value="' + perm + '"]');
        if (checkbox) {
            checkbox.checked = true;
        }
    });

    updatePermissionsCount();
}

function setAllPermissions(checked) {
    const all = document.querySelectorAll('input[name="permissions[]"]');
    all.forEach((el) => {
        el.checked = checked;
    });
    updatePermissionsCount();
}

function updatePermissionsCount() {
    const count = document.querySelectorAll('input[name="permissions[]"]:checked').length;
    const badge = document.getElementById('permissionsCountBadge');
    if (badge) {
        badge.textContent = count + ' صلاحية';
    }
}

document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('input[name="permissions[]"]').forEach((el) => {
        el.addEventListener('change', updatePermissionsCount);
    });
    updatePermissionsCount();

    const mustChangePassword = document.getElementById('must_change_password');
    const logoutOtherDevices = document.getElementById('logout_other_devices');

    function syncSecurityOptions() {
        if (!mustChangePassword || !logoutOtherDevices) {
            return;
        }

        logoutOtherDevices.disabled = !mustChangePassword.checked;
        if (!mustChangePassword.checked) {
            logoutOtherDevices.checked = false;
        }
    }

    if (mustChangePassword) {
        mustChangePassword.addEventListener('change', syncSecurityOptions);
        syncSecurityOptions();
    }

    const form = document.querySelector('form[action*="user-management"]');
    if (!form) {
        return;
    }

    const initialData = new FormData(form);
    let isSubmitting = false;

    form.addEventListener('submit', function () {
        isSubmitting = true;
    });

    window.addEventListener('beforeunload', function (event) {
        if (isSubmitting) {
            return;
        }

        const currentData = new FormData(form);
        let hasChanges = false;

        const initialEntries = Array.from(initialData.entries());
        const currentEntries = Array.from(currentData.entries());

        if (initialEntries.length !== currentEntries.length) {
            hasChanges = true;
        } else {
            for (let i = 0; i < initialEntries.length; i++) {
                const [initialKey, initialValue] = initialEntries[i];
                const [currentKey, currentValue] = currentEntries[i];

                if (initialKey !== currentKey || String(initialValue) !== String(currentValue)) {
                    hasChanges = true;
                    break;
                }
            }
        }

        if (hasChanges) {
            event.preventDefault();
            event.returnValue = '';
        }
    });
});
</script>
@endsection

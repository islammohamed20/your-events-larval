@extends('layouts.admin')

@section('title', 'إضافة مستخدم مصرح')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">إضافة مستخدم مصرح جديد</h3>
                    <a href="{{ route('admin.user-management.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> العودة
                    </a>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.user-management.store') }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            <!-- البيانات الشخصية -->
                            <div class="col-md-6">
                                <h5 class="mb-3">البيانات الشخصية</h5>
                                
                                <div class="mb-3">
                                    <label for="name" class="form-label">الاسم الكامل <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name') }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="email" class="form-label">البريد الإلكتروني <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                           id="email" name="email" value="{{ old('email') }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="phone" class="form-label">رقم الهاتف</label>
                                    <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                           id="phone" name="phone" value="{{ old('phone') }}">
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="status" class="form-label">الحالة <span class="text-danger">*</span></label>
                                    <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                        <option value="">اختر الحالة</option>
                                        <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>نشط</option>
                                        <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>غير نشط</option>
                                        <option value="suspended" {{ old('status') === 'suspended' ? 'selected' : '' }}>معلق</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="address" class="form-label">العنوان</label>
                                    <textarea class="form-control @error('address') is-invalid @enderror"
                                              id="address" name="address" rows="2">{{ old('address') }}</textarea>
                                    @error('address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- كلمة المرور -->
                            <div class="col-md-6">
                                <h5 class="mb-3">كلمة المرور</h5>
                                
                                <div class="mb-3">
                                    <label for="password" class="form-label">كلمة المرور <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                           id="password" name="password" required>
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="password_confirmation" class="form-label">تأكيد كلمة المرور <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control" 
                                           id="password_confirmation" name="password_confirmation" required>
                                </div>

                                <div class="card border-warning-subtle bg-warning-subtle mt-4">
                                    <div class="card-body">
                                        <h6 class="mb-3">إعدادات الأمان عند أول دخول</h6>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" id="must_change_password" name="must_change_password" value="1" {{ old('must_change_password') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="must_change_password">إلزام المستخدم بتغيير كلمة المرور عند تسجيل الدخول لأول مرة</label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" id="logout_other_devices" name="logout_other_devices" value="1" {{ old('logout_other_devices', '1') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="logout_other_devices">تسجيل الخروج من جميع الأجهزة بعد تغيير كلمة المرور <span class="text-muted">(موصى به)</span></label>
                                        </div>
                                        <div class="form-text">الخيار الثاني يعمل فقط عند تفعيل إجبار تغيير كلمة المرور.</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-12">
                                <h5 class="mb-3">الصلاحيات التفصيلية</h5>
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label class="form-label d-block">قوالب صلاحيات جاهزة</label>
                                            <div class="btn-group" role="group" aria-label="Permission presets">
                                                <button type="button" class="btn btn-outline-primary btn-sm" onclick="applyPermissionPreset('full_admin')">مشرف كامل</button>
                                                <button type="button" class="btn btn-outline-success btn-sm" onclick="applyPermissionPreset('customers_manager')">مشرف عملاء</button>
                                                <button type="button" class="btn btn-outline-warning btn-sm" onclick="applyPermissionPreset('bookings_manager')">مشرف حجوزات</button>
                                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="applyPermissionPreset('read_only')">عرض فقط</button>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input" type="checkbox" id="perm_manage_users" name="permissions[]" value="manage_users" {{ in_array('manage_users', old('permissions', \App\Models\User::ADMIN_PERMISSIONS), true) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="perm_manage_users">إدارة المستخدمين</label>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input" type="checkbox" id="perm_manage_emails" name="permissions[]" value="manage_emails" {{ in_array('manage_emails', old('permissions', \App\Models\User::ADMIN_PERMISSIONS), true) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="perm_manage_emails">إدارة البريد الإلكتروني</label>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input" type="checkbox" id="perm_manage_services" name="permissions[]" value="manage_services" {{ in_array('manage_services', old('permissions', \App\Models\User::ADMIN_PERMISSIONS), true) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="perm_manage_services">إدارة الخدمات</label>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input" type="checkbox" id="perm_manage_categories" name="permissions[]" value="manage_categories" {{ in_array('manage_categories', old('permissions', \App\Models\User::ADMIN_PERMISSIONS), true) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="perm_manage_categories">إدارة الفئات</label>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input" type="checkbox" id="perm_manage_packages" name="permissions[]" value="manage_packages" {{ in_array('manage_packages', old('permissions', \App\Models\User::ADMIN_PERMISSIONS), true) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="perm_manage_packages">إدارة الباقات</label>
                                                </div>
                                            </div>
                                        </div>

                                        <hr>
                                        <h6 class="mb-3">صلاحيات إدارة العملاء</h6>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input" type="checkbox" id="perm_customers_view" name="permissions[]" value="customers.view" {{ in_array('customers.view', old('permissions', \App\Models\User::ADMIN_PERMISSIONS), true) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="perm_customers_view">عرض العملاء</label>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input" type="checkbox" id="perm_customers_edit" name="permissions[]" value="customers.edit" {{ in_array('customers.edit', old('permissions', \App\Models\User::ADMIN_PERMISSIONS), true) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="perm_customers_edit">تعديل العملاء</label>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input" type="checkbox" id="perm_customers_delete" name="permissions[]" value="customers.delete" {{ in_array('customers.delete', old('permissions', \App\Models\User::ADMIN_PERMISSIONS), true) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="perm_customers_delete">حذف العملاء</label>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input" type="checkbox" id="perm_customers_export" name="permissions[]" value="customers.export" {{ in_array('customers.export', old('permissions', \App\Models\User::ADMIN_PERMISSIONS), true) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="perm_customers_export">تصدير العملاء</label>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input" type="checkbox" id="perm_customers_reset" name="permissions[]" value="customers.reset_password" {{ in_array('customers.reset_password', old('permissions', \App\Models\User::ADMIN_PERMISSIONS), true) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="perm_customers_reset">إعادة تعيين كلمة مرور العميل</label>
                                                </div>
                                            </div>
                                        </div>

                                        <hr>
                                        <h6 class="mb-3">صلاحيات إدارة الحجوزات وعروض الأسعار</h6>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input" type="checkbox" id="perm_bookings_view" name="permissions[]" value="bookings.view" {{ in_array('bookings.view', old('permissions', \App\Models\User::ADMIN_PERMISSIONS), true) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="perm_bookings_view">عرض الحجوزات</label>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input" type="checkbox" id="perm_bookings_edit" name="permissions[]" value="bookings.edit" {{ in_array('bookings.edit', old('permissions', \App\Models\User::ADMIN_PERMISSIONS), true) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="perm_bookings_edit">تعديل حالة الحجز</label>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input" type="checkbox" id="perm_bookings_delete" name="permissions[]" value="bookings.delete" {{ in_array('bookings.delete', old('permissions', \App\Models\User::ADMIN_PERMISSIONS), true) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="perm_bookings_delete">حذف الحجز</label>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input" type="checkbox" id="perm_quotes_view" name="permissions[]" value="quotes.view" {{ in_array('quotes.view', old('permissions', \App\Models\User::ADMIN_PERMISSIONS), true) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="perm_quotes_view">عرض عروض الأسعار</label>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input" type="checkbox" id="perm_quotes_edit" name="permissions[]" value="quotes.edit" {{ in_array('quotes.edit', old('permissions', \App\Models\User::ADMIN_PERMISSIONS), true) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="perm_quotes_edit">تعديل حالة عرض السعر</label>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input" type="checkbox" id="perm_quotes_delete" name="permissions[]" value="quotes.delete" {{ in_array('quotes.delete', old('permissions', \App\Models\User::ADMIN_PERMISSIONS), true) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="perm_quotes_delete">حذف عرض السعر</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                @error('permissions')
                                    <div class="alert alert-danger py-2">{{ $message }}</div>
                                @enderror

                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i>
                                    <strong>ملاحظة:</strong> سيتم منح هذا المستخدم صلاحيات الإدارة تلقائياً للوصول إلى لوحة التحكم.
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> حفظ المستخدم
                                </button>
                                <a href="{{ route('admin.user-management.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> إلغاء
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function applyPermissionPreset(preset) {
    const all = document.querySelectorAll('input[name="permissions[]"]');
    all.forEach((el) => {
        el.checked = false;
    });

    const presets = {
        full_admin: [
            'manage_users', 'manage_emails', 'manage_services', 'manage_categories', 'manage_packages',
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
            'customers.view', 'bookings.view', 'quotes.view'
        ],
    };

    (presets[preset] || []).forEach((perm) => {
        const checkbox = document.querySelector('input[name="permissions[]"][value="' + perm + '"]');
        if (checkbox) {
            checkbox.checked = true;
        }
    });
}

document.addEventListener('DOMContentLoaded', function () {
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
});
</script>
@endsection
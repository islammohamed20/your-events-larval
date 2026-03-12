@extends('layouts.admin')

@section('title', 'إدارة الصلاحيات')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">إدارة الصلاحيات</h3>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.user-management.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> إضافة مستخدم جديد
                        </a>
                        <a href="{{ route('admin.user-management.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> العودة
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-md-8">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover align-middle ye-user-management-table">
                                    <thead>
                                        <tr>
                                            <th>المستخدم</th>
                                            <th>البريد الإلكتروني</th>
                                            <th>الحالة</th>
                                            <th>سياسة الأمان</th>
                                            <th>آخر دخول</th>
                                            <th>الإجراءات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($users as $user)
                                            @php
                                                $grantedPermissions = is_array($user->permissions) ? $user->permissions : [];
                                            @endphp
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-sm me-2">
                                                            <div class="avatar-title bg-primary rounded-circle">
                                                                {{ strtoupper(substr($user->name, 0, 2)) }}
                                                            </div>
                                                        </div>
                                                        <div>
                                                            <h6 class="mb-0">{{ $user->name }}</h6>
                                                            <small class="text-muted">{{ $user->company_name }}</small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>{{ $user->email }}</td>
                                                <td>
                                                    @if($user->status === 'active')
                                                        <span class="badge bg-success">نشط</span>
                                                    @elseif($user->status === 'inactive')
                                                        <span class="badge bg-secondary">غير نشط</span>
                                                    @else
                                                        <span class="badge bg-danger">معلق</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="d-flex flex-column gap-1">
                                                        @if($user->must_change_password)
                                                            <span class="badge bg-warning text-dark">تغيير كلمة المرور إلزامي</span>
                                                        @endif
                                                        @if($user->must_change_password && $user->logout_other_devices)
                                                            <span class="badge bg-info text-dark">خروج كل الأجهزة بعد التغيير</span>
                                                        @endif
                                                        @if(! $user->must_change_password)
                                                            <span class="badge bg-secondary">سياسة عادية</span>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td>
                                                    @if($user->last_login_at)
                                                        {{ $user->last_login_at->diffForHumans() }}
                                                    @else
                                                        <span class="text-muted">لم يسجل دخول</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($user->id !== auth()->user()->id)
                                                        <div class="btn-group" role="group">
                                                            <a href="{{ route('admin.user-management.show', $user) }}" 
                                                               class="btn btn-sm btn-outline-info" 
                                                               title="عرض">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                            <a href="{{ route('admin.user-management.edit', $user) }}" 
                                                               class="btn btn-sm btn-outline-warning" 
                                                               title="تعديل">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                            @if($user->role !== 'admin')
                                                                <button type="button" class="btn btn-sm btn-outline-secondary" 
                                                                        data-bs-toggle="modal" 
                                                                        data-bs-target="#editPermissionsModal{{ $user->id }}"
                                                                        title="تعديل الصلاحيات">
                                                                    <i class="fas fa-shield-alt"></i>
                                                                </button>
                                                            @else
                                                                <span class="badge bg-info">Admin كامل الصلاحيات</span>
                                                            @endif
                                                            <form action="{{ route('admin.user-management.toggle-admin', $user) }}" 
                                                                  method="POST" 
                                                                  class="d-inline">
                                                                @csrf
                                                                <button type="submit" 
                                                                        class="btn btn-sm btn-outline-dark" 
                                                                        title="إزالة/منح صلاحيات الإدارة"
                                                                        onclick="return confirm('هل أنت متأكد من تبديل صلاحيات الإدارة؟')">
                                                                    <i class="fas fa-user-shield"></i>
                                                                </button>
                                                            </form>
                                                            <form action="{{ route('admin.user-management.destroy', $user) }}" 
                                                                  method="POST" 
                                                                  class="d-inline"
                                                                  onsubmit="return confirm('هل أنت متأكد من حذف هذا المستخدم؟')">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" 
                                                                        class="btn btn-sm btn-outline-danger" 
                                                                        title="حذف">
                                                                    <i class="fas fa-trash-alt"></i>
                                                                </button>
                                                            </form>
                                                        </div>
                                                    @else
                                                        <span class="badge bg-info">المستخدم الحالي</span>
                                                    @endif
                                                </td>
                                            </tr>

                                            @if($user->role !== 'admin')
                                            <!-- Modal لتعديل الصلاحيات -->
                                            <div class="modal fade" id="editPermissionsModal{{ $user->id }}" tabindex="-1">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">تعديل صلاحيات {{ $user->name }}</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <form action="{{ route('admin.user-management.update', $user) }}" method="POST">
                                                                @csrf
                                                                @method('PUT')
                                                                <input type="hidden" name="name" value="{{ $user->name }}">
                                                                <input type="hidden" name="email" value="{{ $user->email }}">
                                                                <input type="hidden" name="phone" value="{{ $user->phone }}">
                                                                <input type="hidden" name="address" value="{{ $user->address }}">
                                                                @if($user->must_change_password)
                                                                    <input type="hidden" name="must_change_password" value="1">
                                                                @endif
                                                                @if($user->logout_other_devices)
                                                                    <input type="hidden" name="logout_other_devices" value="1">
                                                                @endif

                                                                <div class="mb-3">
                                                                    <label class="form-label d-block">قوالب صلاحيات جاهزة</label>
                                                                    <div class="btn-group" role="group" aria-label="Permission presets">
                                                                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="applyPermissionPresetForModal('editPermissionsModal{{ $user->id }}', 'full_admin')">مشرف كامل</button>
                                                                        <button type="button" class="btn btn-outline-success btn-sm" onclick="applyPermissionPresetForModal('editPermissionsModal{{ $user->id }}', 'customers_manager')">مشرف عملاء</button>
                                                                        <button type="button" class="btn btn-outline-warning btn-sm" onclick="applyPermissionPresetForModal('editPermissionsModal{{ $user->id }}', 'bookings_manager')">مشرف حجوزات</button>
                                                                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="applyPermissionPresetForModal('editPermissionsModal{{ $user->id }}', 'read_only')">عرض فقط</button>
                                                                    </div>
                                                                </div>
                                                                
                                                                <div class="mb-3">
                                                                    <label class="form-label">الحالة</label>
                                                                    <select class="form-select" name="status">
                                                                        <option value="active" {{ $user->status === 'active' ? 'selected' : '' }}>نشط</option>
                                                                        <option value="inactive" {{ $user->status === 'inactive' ? 'selected' : '' }}>غير نشط</option>
                                                                        <option value="suspended" {{ $user->status === 'suspended' ? 'selected' : '' }}>معلق</option>
                                                                    </select>
                                                                </div>

                                                                <div class="mb-3">
                                                                    <div class="form-check">
                                                                        <input class="form-check-input" type="checkbox" 
                                                                               id="can_manage_users{{ $user->id }}" 
                                                                               name="permissions[]" 
                                                                              value="manage_users"
                                                                              {{ in_array('manage_users', old('permissions', $grantedPermissions), true) ? 'checked' : '' }}>
                                                                        <label class="form-check-label" for="can_manage_users{{ $user->id }}">
                                                                            إدارة المستخدمين
                                                                        </label>
                                                                    </div>
                                                                </div>

                                                                <div class="mb-3">
                                                                    <div class="form-check">
                                                                        <input class="form-check-input" type="checkbox" 
                                                                               id="can_manage_emails{{ $user->id }}" 
                                                                               name="permissions[]" 
                                                                              value="manage_emails"
                                                                              {{ in_array('manage_emails', old('permissions', $grantedPermissions), true) ? 'checked' : '' }}>
                                                                        <label class="form-check-label" for="can_manage_emails{{ $user->id }}">
                                                                            إدارة البريد الإلكتروني
                                                                        </label>
                                                                    </div>
                                                                </div>

                                                                <div class="mb-3">
                                                                    <div class="form-check">
                                                                        <input class="form-check-input" type="checkbox" 
                                                                               id="can_manage_services{{ $user->id }}" 
                                                                               name="permissions[]" 
                                                                              value="manage_services"
                                                                              {{ in_array('manage_services', old('permissions', $grantedPermissions), true) ? 'checked' : '' }}>
                                                                        <label class="form-check-label" for="can_manage_services{{ $user->id }}">
                                                                            إدارة الخدمات
                                                                        </label>
                                                                    </div>
                                                                </div>

                                                                <div class="mb-3">
                                                                    <div class="form-check">
                                                                        <input class="form-check-input" type="checkbox" 
                                                                               id="can_manage_categories{{ $user->id }}" 
                                                                               name="permissions[]" 
                                                                              value="manage_categories"
                                                                              {{ in_array('manage_categories', old('permissions', $grantedPermissions), true) ? 'checked' : '' }}>
                                                                        <label class="form-check-label" for="can_manage_categories{{ $user->id }}">
                                                                            إدارة الفئات
                                                                        </label>
                                                                    </div>
                                                                </div>

                                                                <div class="mb-3">
                                                                    <div class="form-check">
                                                                        <input class="form-check-input" type="checkbox" 
                                                                               id="can_manage_packages{{ $user->id }}" 
                                                                               name="permissions[]" 
                                                                              value="manage_packages"
                                                                              {{ in_array('manage_packages', old('permissions', $grantedPermissions), true) ? 'checked' : '' }}>
                                                                        <label class="form-check-label" for="can_manage_packages{{ $user->id }}">
                                                                            إدارة الباقات
                                                                        </label>
                                                                    </div>
                                                                </div>

                                                                <hr>
                                                                <h6 class="mb-2">صلاحيات إدارة العملاء</h6>
                                                                <div class="mb-3">
                                                                    <div class="form-check">
                                                                        <input class="form-check-input" type="checkbox" 
                                                                               id="can_customers_view{{ $user->id }}" 
                                                                               name="permissions[]" 
                                                                              value="customers.view"
                                                                              {{ in_array('customers.view', old('permissions', $grantedPermissions), true) ? 'checked' : '' }}>
                                                                        <label class="form-check-label" for="can_customers_view{{ $user->id }}">
                                                                            عرض العملاء
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <div class="form-check">
                                                                        <input class="form-check-input" type="checkbox" 
                                                                               id="can_customers_edit{{ $user->id }}" 
                                                                               name="permissions[]" 
                                                                              value="customers.edit"
                                                                              {{ in_array('customers.edit', old('permissions', $grantedPermissions), true) ? 'checked' : '' }}>
                                                                        <label class="form-check-label" for="can_customers_edit{{ $user->id }}">
                                                                            تعديل العملاء
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <div class="form-check">
                                                                        <input class="form-check-input" type="checkbox" 
                                                                               id="can_customers_delete{{ $user->id }}" 
                                                                               name="permissions[]" 
                                                                              value="customers.delete"
                                                                              {{ in_array('customers.delete', old('permissions', $grantedPermissions), true) ? 'checked' : '' }}>
                                                                        <label class="form-check-label" for="can_customers_delete{{ $user->id }}">
                                                                            حذف العملاء
                                                                        </label>
                                                                    </div>
                                                                </div>

                                                                <hr>
                                                                <h6 class="mb-2">صلاحيات إدارة الحجوزات</h6>
                                                                <div class="mb-3">
                                                                    <div class="form-check">
                                                                        <input class="form-check-input" type="checkbox" 
                                                                               id="can_bookings_view{{ $user->id }}" 
                                                                               name="permissions[]" 
                                                                              value="bookings.view"
                                                                              {{ in_array('bookings.view', old('permissions', $grantedPermissions), true) ? 'checked' : '' }}>
                                                                        <label class="form-check-label" for="can_bookings_view{{ $user->id }}">
                                                                            عرض الحجوزات
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <div class="form-check">
                                                                        <input class="form-check-input" type="checkbox" 
                                                                               id="can_bookings_edit{{ $user->id }}" 
                                                                               name="permissions[]" 
                                                                              value="bookings.edit"
                                                                              {{ in_array('bookings.edit', old('permissions', $grantedPermissions), true) ? 'checked' : '' }}>
                                                                        <label class="form-check-label" for="can_bookings_edit{{ $user->id }}">
                                                                            تعديل حالة الحجز
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <div class="form-check">
                                                                        <input class="form-check-input" type="checkbox" 
                                                                               id="can_bookings_delete{{ $user->id }}" 
                                                                               name="permissions[]" 
                                                                              value="bookings.delete"
                                                                              {{ in_array('bookings.delete', old('permissions', $grantedPermissions), true) ? 'checked' : '' }}>
                                                                        <label class="form-check-label" for="can_bookings_delete{{ $user->id }}">
                                                                            حذف الحجز
                                                                        </label>
                                                                    </div>
                                                                </div>

                                                                <h6 class="mb-2">صلاحيات عروض الأسعار</h6>
                                                                <div class="mb-3">
                                                                    <div class="form-check">
                                                                        <input class="form-check-input" type="checkbox" 
                                                                               id="can_quotes_view{{ $user->id }}" 
                                                                               name="permissions[]" 
                                                                              value="quotes.view"
                                                                              {{ in_array('quotes.view', old('permissions', $grantedPermissions), true) ? 'checked' : '' }}>
                                                                        <label class="form-check-label" for="can_quotes_view{{ $user->id }}">
                                                                            عرض عروض الأسعار
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <div class="form-check">
                                                                        <input class="form-check-input" type="checkbox" 
                                                                               id="can_quotes_edit{{ $user->id }}" 
                                                                               name="permissions[]" 
                                                                              value="quotes.edit"
                                                                              {{ in_array('quotes.edit', old('permissions', $grantedPermissions), true) ? 'checked' : '' }}>
                                                                        <label class="form-check-label" for="can_quotes_edit{{ $user->id }}">
                                                                            تعديل عروض الأسعار
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <div class="form-check">
                                                                        <input class="form-check-input" type="checkbox" 
                                                                               id="can_quotes_delete{{ $user->id }}" 
                                                                               name="permissions[]" 
                                                                              value="quotes.delete"
                                                                              {{ in_array('quotes.delete', old('permissions', $grantedPermissions), true) ? 'checked' : '' }}>
                                                                        <label class="form-check-label" for="can_quotes_delete{{ $user->id }}">
                                                                            حذف عروض الأسعار
                                                                        </label>
                                                                    </div>
                                                                </div>

                                                                

                                                                <input type="hidden" name="name" value="{{ $user->name }}">
                                                                <input type="hidden" name="email" value="{{ $user->email }}">
                                                                <input type="hidden" name="company_name" value="{{ $user->company_name }}">
                                                                <input type="hidden" name="tax_number" value="{{ $user->tax_number }}">
                                                                <input type="hidden" name="phone" value="{{ $user->phone }}">

                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                                                                    <button type="submit" class="btn btn-primary">حفظ التغييرات</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            @endif
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center py-4">
                                                    <i class="fas fa-shield-alt fa-3x text-muted mb-3"></i>
                                                    <p class="text-muted">لا توجد مستخدمين مصرحين</p>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">معلومات الصلاحيات</h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <h6><i class="fas fa-users text-primary"></i> إدارة المستخدمين</h6>
                                        <small class="text-muted">إضافة وتعديل وحذف المستخدمين المصرحين</small>
                                    </div>

                                        <div class="mb-3">
                                            <h6><i class="fas fa-envelope text-info"></i> إدارة البريد الإلكتروني</h6>
                                            <small class="text-muted">إدارة قوالب البريد وإرسال البريد التجريبي ومتابعة الإحصائيات</small>
                                        </div>

                                    <div class="mb-3">
                                        <h6><i class="fas fa-cogs text-primary"></i> إدارة الخدمات</h6>
                                        <small class="text-muted">إدارة الخدمات والتغييرات المرتبطة بها</small>
                                    </div>

                                    <div class="mb-3">
                                        <h6><i class="fas fa-folder text-primary"></i> إدارة الفئات</h6>
                                        <small class="text-muted">إضافة وتعديل وحذف الفئات</small>
                                    </div>

                                    <div class="mb-3">
                                        <h6><i class="fas fa-box text-primary"></i> إدارة الباقات</h6>
                                        <small class="text-muted">إدارة الباقات ومحتواها</small>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <h6><i class="fas fa-user-tie text-success"></i> إدارة العملاء</h6>
                                        <small class="text-muted">عرض وتعديل بيانات العملاء</small>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <h6><i class="fas fa-calendar text-warning"></i> إدارة الحجوزات</h6>
                                        <small class="text-muted">إدارة جميع الحجوزات وعروض الأسعار</small>
                                    </div>

                                    <div class="alert alert-warning mt-3">
                                        <small>
                                            <i class="fas fa-exclamation-triangle"></i>
                                            <strong>تنبيه:</strong> تأكد من منح الصلاحيات المناسبة لكل مستخدم حسب دوره في النظام.
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function applyPermissionPresetForModal(modalId, preset) {
    const modal = document.getElementById(modalId);
    if (!modal) {
        return;
    }

    const all = modal.querySelectorAll('input[name="permissions[]"]');
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
        const checkbox = modal.querySelector('input[name="permissions[]"][value="' + perm + '"]');
        if (checkbox) {
            checkbox.checked = true;
        }
    });
}
</script>
@endsection

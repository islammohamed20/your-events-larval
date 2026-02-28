@extends('layouts.admin')

@section('title', 'إدارة الصلاحيات')

@section('content')
<style>
    .ye-user-management-table.table-dark,
    .ye-user-management-table.table-dark th,
    .ye-user-management-table.table-dark td {
        color: #000 !important;
    }
</style>
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
                                <table class="table table-dark table-striped table-hover align-middle ye-user-management-table">
                                    <thead class="table-dark ye-user-management-table">
                                        <tr>
                                            <th>المستخدم</th>
                                            <th>البريد الإلكتروني</th>
                                            <th>الحالة</th>
                                            <th>آخر دخول</th>
                                            <th>الإجراءات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($users as $user)
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
                                                            <button type="button" class="btn btn-sm btn-outline-secondary" 
                                                                    data-bs-toggle="modal" 
                                                                    data-bs-target="#editPermissionsModal{{ $user->id }}"
                                                                    title="تعديل الصلاحيات">
                                                                <i class="fas fa-shield-alt"></i>
                                                            </button>
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
                                                                               value="manage_users" checked>
                                                                        <label class="form-check-label" for="can_manage_users{{ $user->id }}">
                                                                            إدارة المستخدمين
                                                                        </label>
                                                                    </div>
                                                                </div>

                                                                <div class="mb-3">
                                                                    <div class="form-check">
                                                                        <input class="form-check-input" type="checkbox" 
                                                                               id="can_manage_customers{{ $user->id }}" 
                                                                               name="permissions[]" 
                                                                               value="manage_customers" checked>
                                                                        <label class="form-check-label" for="can_manage_customers{{ $user->id }}">
                                                                            إدارة العملاء
                                                                        </label>
                                                                    </div>
                                                                </div>

                                                                <div class="mb-3">
                                                                    <div class="form-check">
                                                                        <input class="form-check-input" type="checkbox" 
                                                                               id="can_manage_bookings{{ $user->id }}" 
                                                                               name="permissions[]" 
                                                                               value="manage_bookings" checked>
                                                                        <label class="form-check-label" for="can_manage_bookings{{ $user->id }}">
                                                                            إدارة الحجوزات
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
@endsection

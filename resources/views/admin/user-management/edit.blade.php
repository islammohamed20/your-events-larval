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

                                        <div class="alert alert-warning">
                                            <small>
                                                <i class="fas fa-shield-alt"></i>
                                                <strong>مدير النظام:</strong> هذا المستخدم لديه صلاحيات إدارية كاملة
                                            </small>
                                        </div>
                                    </div>
                                </div>

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
@endsection

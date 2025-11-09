@extends('layouts.admin')

@section('title', 'تفاصيل المستخدم')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">تفاصيل المستخدم</h3>
                    <div>
                        <a href="{{ route('admin.user-management.edit', $user) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> تعديل
                        </a>
                        <a href="{{ route('admin.user-management.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> العودة
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label text-muted">الاسم</label>
                                        <p class="fw-bold">{{ $user->name }}</p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label text-muted">البريد الإلكتروني</label>
                                        <p class="fw-bold">{{ $user->email }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label text-muted">رقم الهاتف</label>
                                        <p class="fw-bold">{{ $user->phone ?? 'غير محدد' }}</p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label text-muted">الحالة</label>
                                        <p>
                                            @if($user->status === 'active')
                                                <span class="badge bg-success fs-6">نشط</span>
                                            @elseif($user->status === 'inactive')
                                                <span class="badge bg-secondary fs-6">غير نشط</span>
                                            @else
                                                <span class="badge bg-danger fs-6">معلق</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label text-muted">تاريخ التسجيل</label>
                                        <p class="fw-bold">{{ $user->created_at->format('Y-m-d H:i') }}</p>
                                    </div>
                                </div>
                                <div class="col-md-6">
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
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">الصلاحيات</h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-shield-alt text-success me-2"></i>
                                            <span>مدير النظام</span>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-2">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-users text-primary me-2"></i>
                                            <span>إدارة المستخدمين</span>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-2">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-user-tie text-success me-2"></i>
                                            <span>إدارة العملاء</span>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-2">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-calendar text-warning me-2"></i>
                                            <span>إدارة الحجوزات</span>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-2">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-cog text-info me-2"></i>
                                            <span>إدارة الإعدادات</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card bg-light mt-3">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">إحصائيات النشاط</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row text-center">
                                        <div class="col-6">
                                            <div class="border-end">
                                                <h4 class="text-primary">{{ $user->created_at->diffInDays() }}</h4>
                                                <small class="text-muted">يوم منذ التسجيل</small>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <h4 class="text-success">
                                                @if($user->last_login_at)
                                                    {{ $user->last_login_at->diffInDays() }}
                                                @else
                                                    --
                                                @endif
                                            </h4>
                                            <small class="text-muted">يوم منذ آخر دخول</small>
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
</div>
@endsection
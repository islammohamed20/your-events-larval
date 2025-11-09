@extends('layouts.admin')

@section('title', 'إدارة الباقات - Your Events')
@section('page-title', 'إدارة الباقات')
@section('page-description', 'عرض وإدارة جميع الباقات المتاحة')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">الباقات</h2>
    <a href="{{ route('admin.packages.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>إضافة باقة جديدة
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">
            <i class="fas fa-box me-2"></i>قائمة الباقات ({{ $packages->count() }})
        </h5>
    </div>
    <div class="card-body">
        @if($packages->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>الصورة</th>
                            <th>اسم الباقة</th>
                            <th>السعر</th>
                            <th>الحالة</th>
                            <th>تاريخ الإنشاء</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($packages as $package)
                            <tr>
                                <td>
                                    @if($package->image)
                                        <img src="{{ asset('storage/' . $package->image) }}" 
                                             alt="{{ $package->name }}" 
                                             class="rounded" 
                                             style="width: 60px; height: 60px; object-fit: cover;">
                                    @else
                                        <div class="bg-light rounded d-flex align-items-center justify-content-center" 
                                             style="width: 60px; height: 60px;">
                                            <i class="fas fa-image text-muted"></i>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <strong>{{ $package->name }}</strong>
                                    <br>
                                    <small class="text-muted">{{ Str::limit($package->description, 50) }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-success fs-6">{{ number_format($package->price) }} ريال</span>
                                </td>
                                <td>
                                    @if($package->is_active)
                                        <span class="badge bg-success">نشط</span>
                                    @else
                                        <span class="badge bg-secondary">غير نشط</span>
                                    @endif
                                </td>
                                <td>
                                    <small class="text-muted">{{ $package->created_at->format('d/m/Y') }}</small>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.packages.edit', $package) }}" 
                                           class="btn btn-sm btn-outline-primary" 
                                           title="تعديل">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form method="POST" 
                                              action="{{ route('admin.packages.destroy', $package) }}" 
                                              class="d-inline"
                                              onsubmit="return confirm('هل أنت متأكد من حذف هذه الباقة؟')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="btn btn-sm btn-outline-danger" 
                                                    title="حذف">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-box fa-3x text-muted mb-3"></i>
                <h5>لا توجد باقات</h5>
                <p class="text-muted">ابدأ بإضافة باقة جديدة</p>
                <a href="{{ route('admin.packages.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>إضافة باقة جديدة
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
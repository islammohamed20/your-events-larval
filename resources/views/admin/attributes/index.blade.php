@extends('layouts.admin')

@section('title', 'الخصائص')
@section('page-title', 'خصائص الخدمات')
@section('page-description', 'إدارة الخصائص المستخدمة في الخدمات المتغيرة (عدد الأشخاص، المدينة، إلخ)')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <p class="text-muted mb-0">
                    <i class="fas fa-info-circle me-1"></i>
                    الخصائص تُستخدم لإنشاء خدمات بأسعار متعددة (مثل: اختلاف السعر حسب عدد الأشخاص أو المدينة)
                </p>
            </div>
            <a href="{{ route('admin.attributes.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>إضافة خاصية جديدة
            </a>
        </div>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">
            <i class="fas fa-list me-2"></i>قائمة الخصائص
        </h5>
    </div>
    <div class="card-body">
        @if($attributes->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>الترتيب</th>
                            <th>اسم الخاصية</th>
                            <th>Slug</th>
                            <th>النوع</th>
                            <th>عدد القيم</th>
                            <th>الحالة</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($attributes as $attribute)
                            <tr>
                                <td>
                                    <span class="badge bg-secondary">{{ $attribute->order }}</span>
                                </td>
                                <td>
                                    <strong>{{ $attribute->name }}</strong>
                                </td>
                                <td>
                                    <code>{{ $attribute->slug }}</code>
                                </td>
                                <td>
                                    @switch($attribute->type)
                                        @case('select')
                                            <span class="badge bg-info">
                                                <i class="fas fa-list"></i> قائمة منسدلة
                                            </span>
                                            @break
                                        @case('radio')
                                            <span class="badge bg-warning">
                                                <i class="fas fa-dot-circle"></i> اختيار واحد
                                            </span>
                                            @break
                                        @case('checkbox')
                                            <span class="badge bg-success">
                                                <i class="fas fa-check-square"></i> اختيارات متعددة
                                            </span>
                                            @break
                                    @endswitch
                                </td>
                                <td>
                                    <span class="badge bg-primary">{{ $attribute->values_count }} قيمة</span>
                                </td>
                                <td>
                                    @if($attribute->is_active)
                                        <span class="badge bg-success">
                                            <i class="fas fa-check"></i> نشط
                                        </span>
                                    @else
                                        <span class="badge bg-danger">
                                            <i class="fas fa-times"></i> غير نشط
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.attributes.edit', $attribute) }}" 
                                           class="btn btn-sm btn-outline-primary"
                                           title="تعديل">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.attributes.destroy', $attribute) }}" 
                                              method="POST" 
                                              class="d-inline"
                                              onsubmit="return confirm('هل أنت متأكد من حذف هذه الخاصية؟')">
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

            <div class="d-flex justify-content-center mt-4">
                {{ $attributes->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-tags fa-3x text-muted mb-3"></i>
                <h5>لا توجد خصائص حتى الآن</h5>
                <p class="text-muted">ابدأ بإنشاء خاصية جديدة مثل "عدد الأشخاص" أو "المدينة"</p>
                <a href="{{ route('admin.attributes.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>إضافة خاصية جديدة
                </a>
            </div>
        @endif
    </div>
</div>

<div class="card mt-4">
    <div class="card-header bg-light">
        <h6 class="mb-0">
            <i class="fas fa-info-circle me-2"></i>معلومات مفيدة
        </h6>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4">
                <h6><i class="fas fa-list text-info me-2"></i>قائمة منسدلة (Select)</h6>
                <p class="text-muted small">مناسب لاختيار قيمة واحدة من عدة خيارات (مثل: المدينة)</p>
            </div>
            <div class="col-md-4">
                <h6><i class="fas fa-dot-circle text-warning me-2"></i>اختيار واحد (Radio)</h6>
                <p class="text-muted small">مشابه للقائمة المنسدلة لكن الخيارات تظهر مباشرة</p>
            </div>
            <div class="col-md-4">
                <h6><i class="fas fa-check-square text-success me-2"></i>اختيارات متعددة (Checkbox)</h6>
                <p class="text-muted small">يسمح باختيار أكثر من قيمة (مثل: خدمات إضافية)</p>
            </div>
        </div>
    </div>
</div>
@endsection

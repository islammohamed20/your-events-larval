@extends('supplier.layouts.app')

@section('title', 'خدماتي')
@section('page-title', 'خدماتي')

@section('content')
<!-- Filters -->
<div class="content-card mb-4">
    <div class="p-3">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label">الفئة</label>
                <select name="category" class="form-select">
                    <option value="">جميع الفئات</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">الحالة</label>
                <select name="status" class="form-select">
                    <option value="">الكل</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>نشط</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>غير نشط</option>
                </select>
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-supplier-primary">
                    <i class="fas fa-filter me-1"></i> تصفية
                </button>
                <a href="{{ route('supplier.services.index') }}" class="btn btn-light">
                    <i class="fas fa-undo me-1"></i> إعادة تعيين
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Services Grid -->
<div class="row g-4">
    @forelse($services as $service)
    <div class="col-lg-4 col-md-6">
        <div class="content-card h-100">
            <!-- Service Image -->
            <div class="position-relative">
                <img src="{{ $service->thumbnail_url }}" alt="{{ $service->name }}" 
                     class="w-100" style="height: 180px; object-fit: cover;">
                
                <!-- Status Badge -->
                <div class="position-absolute top-0 end-0 m-2">
                    @if($service->is_active)
                        <span class="badge bg-success"><i class="fas fa-check me-1"></i>نشط</span>
                    @else
                        <span class="badge bg-secondary"><i class="fas fa-pause me-1"></i>غير نشط</span>
                    @endif
                </div>
            </div>
            
            <!-- Service Info -->
            <div class="p-3">
                <div class="mb-2">
                    <span class="badge" style="background: rgba(102, 126, 234, 0.1); color: #667eea;">
                        {{ $service->category->name ?? 'غير مصنف' }}
                    </span>
                </div>
                <h5 class="fw-bold mb-2">{{ $service->name }}</h5>
                <p class="text-muted small mb-3">{{ Str::limit($service->description, 80) }}</p>
                
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div>
                        <span class="fw-bold" style="color: #1f144a; font-size: 1.2rem;">
                            {{ number_format($service->base_price, 0) }} ر.س
                        </span>
                    </div>
                </div>
                
                <!-- Actions -->
                <div class="d-flex gap-2">
                    <a href="{{ route('supplier.services.show', $service->id) }}" class="btn btn-sm btn-outline-primary flex-grow-1">
                        <i class="fas fa-eye me-1"></i> التفاصيل
                    </a>
                    <form action="{{ route('supplier.services.toggle', $service->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-sm {{ $service->pivot->is_available ?? true ? 'btn-outline-danger' : 'btn-outline-success' }}">
                            @if($service->pivot->is_available ?? true)
                                <i class="fas fa-pause"></i>
                            @else
                                <i class="fas fa-play"></i>
                            @endif
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="content-card">
            <div class="text-center py-5">
                <i class="fas fa-box-open fa-4x text-muted mb-3"></i>
                <h4 class="text-muted">لا توجد خدمات</h4>
                <p class="text-muted">لم يتم تسجيل أي خدمات لك بعد</p>
            </div>
        </div>
    </div>
    @endforelse
</div>

<!-- Pagination -->
@if($services->hasPages())
<div class="mt-4 d-flex justify-content-center">
    {{ $services->withQueryString()->links() }}
</div>
@endif
@endsection

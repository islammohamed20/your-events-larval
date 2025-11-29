@extends('layouts.admin')

@section('title', 'تفاصيل المورد')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">تفاصيل المورد</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.suppliers.index') }}">الموردين</a></li>
                    <li class="breadcrumb-item active">{{ $supplier->name }}</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="{{ route('admin.suppliers.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-right me-1"></i>عودة
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <!-- Main Info -->
        <div class="col-lg-8">
            <!-- Basic Information -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="fas fa-info-circle text-primary me-2"></i>المعلومات الأساسية</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="text-muted small mb-1">اسم المورد / المنشأة</label>
                            <p class="fw-semibold mb-0">{{ $supplier->name }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small mb-1">نوع المورد</label>
                            <p class="mb-0">
                                <span class="badge bg-{{ $supplier->supplier_type == 'company' ? 'info' : 'secondary' }}">
                                    {{ $supplier->supplier_type == 'company' ? 'منشأة' : 'فرد' }}
                                </span>
                            </p>
                        </div>
                        @if($supplier->commercial_register)
                            <div class="col-md-6">
                                <label class="text-muted small mb-1">رقم السجل التجاري</label>
                                <p class="fw-semibold mb-0">{{ $supplier->commercial_register }}</p>
                            </div>
                        @endif
                        @if($supplier->tax_number)
                            <div class="col-md-6">
                                <label class="text-muted small mb-1">الرقم الضريبي</label>
                                <p class="fw-semibold mb-0">{{ $supplier->tax_number }}</p>
                            </div>
                        @endif
                        <div class="col-md-6">
                            <label class="text-muted small mb-1">مقر المنشأة</label>
                            <p class="fw-semibold mb-0">{{ $supplier->headquarters_city }}</p>
                        </div>
                        <div class="col-12">
                            <label class="text-muted small mb-1">نبذة عن المنشأة</label>
                            <p class="mb-0">{{ $supplier->description }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Services Offered -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="fas fa-concierge-bell text-primary me-2"></i>الخدمات المقدمة</h5>
                </div>
                <div class="card-body">
                    @if($supplier->services_offered && count($supplier->services_offered) > 0)
                        <div class="row g-3">
                            @foreach($supplier->services_offered as $service)
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center p-3 border rounded">
                                        <i class="fas fa-{{ match($service) {
                                            'photography' => 'camera',
                                            'catering' => 'utensils',
                                            'entertainment' => 'music',
                                            'gifts' => 'gift',
                                            'logistics' => 'truck',
                                            'handicrafts' => 'palette',
                                            default => 'check'
                                        } }} text-warning fs-4 me-3"></i>
                                        <span>{{ match($service) {
                                            'photography' => 'التصوير الفوتوغرافي',
                                            'catering' => 'تقديم الطعام والضيافة',
                                            'entertainment' => 'الترفيه والتنشيط',
                                            'gifts' => 'الهدايا والتوزيعات',
                                            'logistics' => 'اللوجستيات والنقل',
                                            'handicrafts' => 'الأعمال والحرف اليدوية',
                                            default => $service
                                        } }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted mb-0">لم يتم تحديد خدمات</p>
                    @endif
                </div>
            </div>

            <!-- Documents -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="fas fa-file-alt text-primary me-2"></i>المستندات المرفقة</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        @if($supplier->commercial_register_file)
                            <div class="col-md-6">
                                <div class="border rounded p-3">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div>
                                            <i class="fas fa-file-pdf text-danger fs-4 me-2"></i>
                                            <span>السجل التجاري</span>
                                        </div>
                                        <a href="{{ route('admin.suppliers.download', [$supplier, 'commercial_register']) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-download"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if($supplier->tax_certificate_file)
                            <div class="col-md-6">
                                <div class="border rounded p-3">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div>
                                            <i class="fas fa-file-pdf text-danger fs-4 me-2"></i>
                                            <span>الشهادة الضريبية</span>
                                        </div>
                                        <a href="{{ route('admin.suppliers.download', [$supplier, 'tax_certificate']) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-download"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if($supplier->company_profile_file)
                            <div class="col-md-6">
                                <div class="border rounded p-3">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div>
                                            <i class="fas fa-file-word text-primary fs-4 me-2"></i>
                                            <span>الملف التعريفي</span>
                                        </div>
                                        <a href="{{ route('admin.suppliers.download', [$supplier, 'company_profile']) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-download"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if($supplier->portfolio_files && count($supplier->portfolio_files) > 0)
                            <div class="col-12">
                                <label class="text-muted small mb-2">معرض الأعمال ({{ count($supplier->portfolio_files) }} ملف)</label>
                                <div class="row g-2">
                                    @foreach($supplier->portfolio_files as $file)
                                        <div class="col-md-3">
                                            <div class="border rounded p-2 text-center">
                                                <i class="fas fa-image text-info fs-3"></i>
                                                <p class="small mb-0 mt-1 text-truncate">{{ basename($file) }}</p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Contact Information -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="fas fa-phone text-primary me-2"></i>معلومات التواصل</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="text-muted small mb-1">الجوال الأساسي</label>
                            <p class="mb-0"><i class="fas fa-phone me-2 text-success"></i>{{ $supplier->primary_phone }}</p>
                        </div>
                        @if($supplier->secondary_phone)
                            <div class="col-md-6">
                                <label class="text-muted small mb-1">جوال إضافي</label>
                                <p class="mb-0"><i class="fas fa-phone me-2 text-success"></i>{{ $supplier->secondary_phone }}</p>
                            </div>
                        @endif
                        <div class="col-md-6">
                            <label class="text-muted small mb-1">البريد الإلكتروني</label>
                            <p class="mb-0"><i class="fas fa-envelope me-2 text-primary"></i>{{ $supplier->email }}</p>
                        </div>
                        @if($supplier->address)
                            <div class="col-12">
                                <label class="text-muted small mb-1">العنوان</label>
                                <p class="mb-0"><i class="fas fa-map-marker-alt me-2 text-danger"></i>{{ $supplier->address }}</p>
                            </div>
                        @endif

                        @if($supplier->social_media)
                            <div class="col-12">
                                <label class="text-muted small mb-2">وسائل التواصل الاجتماعي</label>
                                <div class="d-flex gap-2">
                                    @if(isset($supplier->social_media['twitter']))
                                        <a href="{{ $supplier->social_media['twitter'] }}" target="_blank" class="btn btn-sm btn-outline-info">
                                            <i class="fab fa-twitter"></i> تويتر
                                        </a>
                                    @endif
                                    @if(isset($supplier->social_media['instagram']))
                                        <a href="{{ $supplier->social_media['instagram'] }}" target="_blank" class="btn btn-sm btn-outline-danger">
                                            <i class="fab fa-instagram"></i> إنستجرام
                                        </a>
                                    @endif
                                    @if(isset($supplier->social_media['snapchat']))
                                        <a href="{{ $supplier->social_media['snapchat'] }}" target="_blank" class="btn btn-sm btn-outline-warning">
                                            <i class="fab fa-snapchat"></i> سناب شات
                                        </a>
                                    @endif
                                    @if(isset($supplier->social_media['tiktok']))
                                        <a href="{{ $supplier->social_media['tiktok'] }}" target="_blank" class="btn btn-sm btn-outline-dark">
                                            <i class="fab fa-tiktok"></i> تيك توك
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Status Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">حالة الطلب</h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        {!! $supplier->status_badge !!}
                    </div>

                    <div class="d-grid gap-2">
                        @if($supplier->status == 'pending')
                            <form action="{{ route('admin.suppliers.approve', $supplier) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-success w-100" onclick="return confirm('هل أنت متأكد من قبول هذا المورد؟')">
                                    <i class="fas fa-check me-1"></i>قبول المورد
                                </button>
                            </form>

                            <form action="{{ route('admin.suppliers.reject', $supplier) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-danger w-100" onclick="return confirm('هل أنت متأكد من رفض هذا المورد؟')">
                                    <i class="fas fa-times me-1"></i>رفض المورد
                                </button>
                            </form>
                        @endif

                        @if($supplier->status == 'approved')
                            <form action="{{ route('admin.suppliers.suspend', $supplier) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-warning w-100" onclick="return confirm('هل أنت متأكد من تعليق هذا المورد؟')">
                                    <i class="fas fa-pause me-1"></i>تعليق المورد
                                </button>
                            </form>
                        @endif

                        @if($supplier->status == 'suspended')
                            <form action="{{ route('admin.suppliers.activate', $supplier) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-success w-100" onclick="return confirm('هل أنت متأكد من إعادة تفعيل هذا المورد؟')">
                                    <i class="fas fa-play me-1"></i>إعادة تفعيل
                                </button>
                            </form>
                        @endif

                        <form action="{{ route('admin.suppliers.destroy', $supplier) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger w-100" onclick="return confirm('هل أنت متأكد من حذف هذا المورد نهائياً؟ لا يمكن التراجع عن هذا الإجراء.')">
                                <i class="fas fa-trash me-1"></i>حذف نهائياً
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Timeline -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">السجل الزمني</h5>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="timeline-item">
                            <i class="fas fa-user-plus text-primary"></i>
                            <div>
                                <small class="text-muted">تاريخ التسجيل</small>
                                <p class="mb-0 fw-semibold">{{ $supplier->created_at->format('Y-m-d H:i') }}</p>
                            </div>
                        </div>

                        @if($supplier->email_verified_at)
                            <div class="timeline-item">
                                <i class="fas fa-envelope-open text-success"></i>
                                <div>
                                    <small class="text-muted">تم التحقق من البريد</small>
                                    <p class="mb-0 fw-semibold">{{ $supplier->email_verified_at->format('Y-m-d H:i') }}</p>
                                </div>
                            </div>
                        @endif

                        @if($supplier->approved_at)
                            <div class="timeline-item">
                                <i class="fas fa-check-circle text-success"></i>
                                <div>
                                    <small class="text-muted">تاريخ الموافقة</small>
                                    <p class="mb-0 fw-semibold">{{ $supplier->approved_at->format('Y-m-d H:i') }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@php $logs = $supplier->activityLogs()->latest()->limit(25)->get(); @endphp
@include('admin.partials.activity-logs', ['logs' => $logs, 'title' => 'سجل نشاط المورد'])

<style>
.timeline {
    position: relative;
    padding: 20px 0;
}

.timeline-item {
    position: relative;
    padding-left: 40px;
    padding-bottom: 20px;
}

.timeline-item:last-child {
    padding-bottom: 0;
}

.timeline-item i {
    position: absolute;
    left: 0;
    top: 0;
    width: 30px;
    height: 30px;
    background: white;
    border: 2px solid currentColor;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
}

.timeline-item:not(:last-child)::before {
    content: '';
    position: absolute;
    left: 14px;
    top: 30px;
    width: 2px;
    height: calc(100% - 10px);
    background: #e9ecef;
}
</style>
@endsection

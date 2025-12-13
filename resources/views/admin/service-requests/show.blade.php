@extends('layouts.app')

@section('title', 'تفاصيل الطلب')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- رأس الصفحة -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3">تفاصيل الطلب #{{ $serviceRequest->id }}</h1>
                <a href="{{ route('admin.service-requests.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>عودة
                </a>
            </div>

            <!-- الرسائل -->
            @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif

            <!-- البطاقة الرئيسية -->
            <div class="card border-0 shadow-lg mb-4">
                <div class="card-header bg-info text-white p-4">
                    <h4 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>معلومات الطلب
                    </h4>
                </div>
                <div class="card-body p-5">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <p class="text-muted mb-1">الخدمة</p>
                            <h5 class="fw-bold">{{ $serviceRequest->service->name }}</h5>
                        </div>
                        <div class="col-md-6">
                            <p class="text-muted mb-1">الفئة</p>
                            <h5 class="fw-bold">{{ $serviceRequest->category->name }}</h5>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <p class="text-muted mb-1">الكمية</p>
                            <h5 class="fw-bold">{{ $serviceRequest->quantity }}</h5>
                        </div>
                        <div class="col-md-6">
                            <p class="text-muted mb-1">السعر</p>
                            <h5 class="fw-bold">{{ number_format($serviceRequest->price, 2) }} ر.س</h5>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-md-6">
                            <p class="text-muted mb-1">تاريخ الإنشاء</p>
                            <h5 class="fw-bold">{{ $serviceRequest->created_at->format('d/m/Y H:i') }}</h5>
                        </div>
                        <div class="col-md-6">
                            <p class="text-muted mb-1">الحالة</p>
                            @php
                                $statusColors = [
                                    'pending' => 'warning',
                                    'accepted' => 'success',
                                    'rejected' => 'danger',
                                    'completed' => 'info'
                                ];
                                $statusNames = [
                                    'pending' => 'قيد الانتظار',
                                    'accepted' => 'مقبول',
                                    'rejected' => 'مرفوض',
                                    'completed' => 'مكتمل'
                                ];
                            @endphp
                            <span class="badge bg-{{ $statusColors[$serviceRequest->status] ?? 'secondary' }} p-2" style="font-size: 0.9rem;">
                                {{ $statusNames[$serviceRequest->status] ?? 'غير محدد' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- الملاحظات -->
            @if($serviceRequest->customer_notes || $serviceRequest->admin_notes)
            <div class="card border-0 shadow-lg mb-4">
                <div class="card-header bg-secondary text-white p-4">
                    <h4 class="mb-0">
                        <i class="fas fa-sticky-note me-2"></i>الملاحظات
                    </h4>
                </div>
                <div class="card-body p-5">
                    @if($serviceRequest->customer_notes)
                    <div class="mb-4">
                        <p class="text-muted fw-bold mb-2">ملاحظات العميل:</p>
                        <p class="bg-light p-3 rounded">{{ $serviceRequest->customer_notes }}</p>
                    </div>
                    @endif

                    @if($serviceRequest->admin_notes)
                    <div>
                        <p class="text-muted fw-bold mb-2">ملاحظات عامة:</p>
                        <p class="bg-light p-3 rounded">{{ $serviceRequest->admin_notes }}</p>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- الإجراءات -->
            <div class="card border-0 shadow-lg">
                <div class="card-header bg-primary text-white p-4">
                    <h4 class="mb-0">
                        <i class="fas fa-cogs me-2"></i>الإجراءات
                    </h4>
                </div>
                <div class="card-body p-5">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <a href="{{ route('admin.service-requests.edit', $serviceRequest->id) }}" 
                               class="btn btn-warning w-100">
                                <i class="fas fa-edit me-2"></i>تعديل
                            </a>
                        </div>
                        <div class="col-md-6">
                            <form method="POST" action="{{ route('admin.service-requests.destroy', $serviceRequest->id) }}" 
                                  onsubmit="return confirm('هل تريد حذف هذا الطلب؟');" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger w-100">
                                    <i class="fas fa-trash me-2"></i>حذف
                                </button>
                            </form>
                        </div>
                    </div>

                    @if($serviceRequest->status === 'pending')
                    <div class="row g-3 mt-3">
                        <div class="col-md-6">
                            <form method="POST" action="{{ route('admin.service-requests.accept', $serviceRequest->id) }}">
                                @csrf
                                <button type="submit" class="btn btn-success w-100">
                                    <i class="fas fa-check-circle me-2"></i>قبول الطلب
                                </button>
                            </form>
                        </div>
                        <div class="col-md-6">
                            <form method="POST" action="{{ route('admin.service-requests.reject', $serviceRequest->id) }}">
                                @csrf
                                <button type="submit" class="btn btn-danger w-100">
                                    <i class="fas fa-times-circle me-2"></i>رفض الطلب
                                </button>
                            </form>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

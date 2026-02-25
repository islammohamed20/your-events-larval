@extends('layouts.admin')

@section('title', 'تفاصيل الطلب')

@section('content')
<div class="container-fluid mt-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h2 mb-0">
                <i class="fas fa-receipt me-2 text-primary"></i>تفاصيل الطلب #{{ $order->id }}
            </h1>
            <p class="text-muted mb-0">{{ $order->created_at->format('d/m/Y H:i') }}</p>
        </div>
        <div>
            <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>العودة
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
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Order Information -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>معلومات الطلب</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-muted">الخدمة</label>
                            <p class="fs-5">{{ $order->service->name }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-muted">الفئة</label>
                            <p class="fs-5">{{ $order->category->name }}</p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-muted">الكمية</label>
                            <p class="fs-5">
                                <span class="badge bg-info">{{ $order->quantity }}</span>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-muted">السعر</label>
                            <p class="fs-5 text-success fw-bold">{{ number_format($order->price, 2) }} {{ __('common.currency') }}</p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-muted">تاريخ الإنشاء</label>
                            <p class="fs-5">{{ $order->created_at->format('d/m/Y H:i:s') }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-muted">آخر تحديث</label>
                            <p class="fs-5">{{ $order->updated_at->format('d/m/Y H:i:s') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Customer Notes -->
            @if($order->customer_notes)
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-comment me-2"></i>ملاحظات العميل</h5>
                </div>
                <div class="card-body">
                    <p class="mb-0">{{ $order->customer_notes }}</p>
                </div>
            </div>
            @endif

            <!-- General Notes -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0"><i class="fas fa-sticky-note me-2"></i>ملاحظات عامة</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.orders.update', $order) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <textarea class="form-control" name="general_notes" rows="4">{{ $order->general_notes }}</textarea>
                        
                        <div class="mt-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>حفظ الملاحظات
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Suppliers Status -->
            <div class="card shadow-sm">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="fas fa-users me-2"></i>حالة الموردين</h5>
                </div>
                <div class="card-body">
                    @if($order->supplierStatuses->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>المورد</th>
                                    <th>الحالة</th>
                                    <th>وقت القبول</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->supplierStatuses as $status)
                                <tr>
                                    <td>
                                        <strong>{{ $status->supplier->name }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $status->supplier->email }}</small>
                                    </td>
                                    <td>
                                        @php
                                            $badge = match($status->status) {
                                                'accepted' => 'success',
                                                'rejected' => 'danger',
                                                default => 'warning',
                                            };
                                            $text = match($status->status) {
                                                'accepted' => '✓ مقبول',
                                                'rejected' => '✗ مرفوض',
                                                default => '⏳ قيد الانتظار',
                                            };
                                        @endphp
                                        <span class="badge bg-{{ $badge }}">{{ $text }}</span>
                                    </td>
                                    <td>
                                        @if($status->accepted_at)
                                            {{ $status->accepted_at->format('d/m/Y H:i') }}
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <p class="text-muted mb-0">لم يتم إرسال الطلب لأي موردين</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Customer Info -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-user me-2"></i>العميل</h5>
                </div>
                <div class="card-body">
                    <p class="mb-2">
                        <strong>{{ $order->customer->name }}</strong>
                    </p>
                    <p class="text-muted mb-2">
                        <i class="fas fa-envelope me-2"></i>{{ $order->customer->email }}
                    </p>
                    <p class="text-muted mb-2">
                        <i class="fas fa-phone me-2"></i>{{ $order->customer->phone }}
                    </p>
                </div>
            </div>

            <!-- Supplier Info -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-building me-2"></i>المورد</h5>
                </div>
                <div class="card-body">
                    @if($order->supplier)
                    <p class="mb-2">
                        <strong>{{ $order->supplier->name }}</strong>
                    </p>
                    <p class="text-muted mb-2">
                        <i class="fas fa-envelope me-2"></i>{{ $order->supplier->email }}
                    </p>
                    <p class="text-muted mb-2">
                        <i class="fas fa-phone me-2"></i>{{ $order->supplier->phone }}
                    </p>
                    @if($order->supplier->company_name)
                    <p class="text-muted mb-0">
                        <i class="fas fa-briefcase me-2"></i>{{ $order->supplier->company_name }}
                    </p>
                    @endif
                    @else
                    <p class="text-muted mb-0">لم يتم إسناد الطلب لأي مورد حتى الآن</p>
                    @endif
                </div>
            </div>

            <!-- Status Card -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0"><i class="fas fa-chart-pie me-2"></i>حالة الطلب</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.orders.update', $order) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="status" class="form-label fw-bold">تحديث الحالة</label>
                            <select class="form-select" name="status">
                                <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>
                                    ⏳ معلق
                                </option>
                                <option value="assigned" {{ $order->status == 'assigned' ? 'selected' : '' }}>
                                    ✓ تم الإسناد
                                </option>
                                <option value="completed" {{ $order->status == 'completed' ? 'selected' : '' }}>
                                    ✅ مكتمل
                                </option>
                                <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>
                                    ❌ ملغي
                                </option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-check me-2"></i>تحديث الحالة
                        </button>
                    </form>

                    @php
                        $statusConfig = [
                            'pending' => ['badge' => 'warning', 'icon' => 'hourglass-half', 'text' => 'معلق'],
                            'assigned' => ['badge' => 'success', 'icon' => 'check-circle', 'text' => 'تم الإسناد'],
                            'completed' => ['badge' => 'info', 'icon' => 'clipboard-check', 'text' => 'مكتمل'],
                            'cancelled' => ['badge' => 'danger', 'icon' => 'times-circle', 'text' => 'ملغي'],
                        ];
                        $config = $statusConfig[$order->status] ?? $statusConfig['pending'];
                    @endphp

                    <div class="mt-3 text-center">
                        <span class="badge bg-{{ $config['badge'] }} fs-6 p-2">
                            <i class="fas fa-{{ $config['icon'] }} me-1"></i>{{ $config['text'] }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="card shadow-sm">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0"><i class="fas fa-trash me-2"></i>الإجراءات</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.orders.destroy', $order) }}" method="POST" 
                          onsubmit="return confirm('هل أنت متأكد من حذف هذا الطلب؟');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger w-100">
                            <i class="fas fa-trash me-2"></i>حذف الطلب
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .card {
        border: none;
        border-radius: 8px;
    }
    
    .card-header {
        border-radius: 8px 8px 0 0 !important;
    }
    
    .table-sm td {
        padding: 0.5rem !important;
    }
</style>
@endsection

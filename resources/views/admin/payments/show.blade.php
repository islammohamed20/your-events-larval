@extends('layouts.admin')

@section('title', 'تفاصيل الدفع #' . $payment->id)

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0"><i class="fas fa-file-invoice-dollar me-2"></i>تفاصيل الدفع #{{ $payment->id }}</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.payments.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>العودة للقائمة
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-3">
                <div class="card-header bg-black">
                    <h5 class="mb-0 text-white">المعلومات الأساسية</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="text-muted small mb-1">المستخدم</label>
                            <p class="mb-0 fw-semibold">
                                @if($payment->user)
                                    <a href="{{ route('admin.customers.show', $payment->user->id) }}">{{ $payment->user->name }}</a>
                                @else
                                    -
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small mb-1">الحجز</label>
                            <p class="mb-0 fw-semibold">
                                @if($payment->booking)
                                    <a href="{{ route('admin.bookings.show', $payment->booking) }}">{{ $payment->booking->booking_reference }}</a>
                                @else
                                    -
                                @endif
                            </p>
                        </div>
                        <div class="col-md-4">
                            <label class="text-muted small mb-1">المبلغ</label>
                            <p class="mb-0 fw-semibold">{{ number_format($payment->amount, 2) }} {{ $payment->currency }}</p>
                        </div>
                        <div class="col-md-4">
                            <label class="text-muted small mb-1">الطريقة</label>
                            <p class="mb-0 fw-semibold">{{ $payment->method ?? '-' }}</p>
                        </div>
                        <div class="col-md-4">
                            <label class="text-muted small mb-1">المزود</label>
                            <p class="mb-0 fw-semibold">{{ $payment->provider ?? '-' }}</p>
                        </div>
                        <div class="col-md-4">
                            <label class="text-muted small mb-1">الحالة</label>
                            <p class="mb-0">
                                <span class="badge 
                                    @if($payment->status==='paid') bg-success 
                                    @elseif($payment->status==='failed') bg-danger
                                    @elseif($payment->status==='processing') bg-info
                                    @elseif($payment->status==='refunded') bg-secondary
                                    @else bg-warning text-dark @endif">
                                    {{ $payment->status }}
                                </span>
                            </p>
                        </div>
                        <div class="col-md-4">
                            <label class="text-muted small mb-1">وقت الالتقاط</label>
                            <p class="mb-0 fw-semibold">{{ optional($payment->captured_at)->format('Y-m-d H:i') ?? '-' }}</p>
                        </div>
                        <div class="col-md-4">
                            <label class="text-muted small mb-1">مرجع المزود</label>
                            <p class="mb-0 fw-semibold">{{ $payment->provider_reference ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header bg-black">
                    <h5 class="mb-0 text-white">تفاصيل إضافية</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="text-muted small mb-1">ملاحظات</label>
                        <p class="mb-0">{{ $payment->notes ?? '-' }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted small mb-1">البيانات (JSON)</label>
                        <pre class="bg-light p-3 rounded">{{ json_encode($payment->metadata ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header bg-black">
                    <h5 class="mb-0 text-white">تحديث الحالة</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.payments.update-status', $payment) }}" class="row g-3">
                        @csrf
                        @method('PATCH')
                        <div class="col-12">
                            <label class="form-label">الحالة</label>
                            <select name="status" class="form-select">
                                @foreach(['pending','processing','paid','failed','refunded','cancelled'] as $st)
                                    <option value="{{ $st }}" {{ $payment->status===$st ? 'selected' : '' }}>{{ $st }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">ملاحظات</label>
                            <textarea name="notes" class="form-control" rows="3">{{ old('notes', $payment->notes) }}</textarea>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-save me-1"></i>تحديث
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

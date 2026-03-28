@extends('layouts.admin')

@section('title', 'إدارة المدفوعات')

@section('styles')
<style>
@media (min-width: 992px) { .mobile-only { display: none !important; } }
@media (max-width: 991.98px) {
    .desktop-only { display: none !important; }

    .payment-card {
        background: #fff;
        border-radius: 16px;
        border: 1px solid #eef1f6;
        padding: 1rem;
        margin-bottom: 1rem;
        box-shadow: 0 4px 12px rgba(0,0,0,0.04);
    }

    .payment-card-top {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: .6rem;
    }

    .payment-id { font-size: .8rem; color: #9ca3af; font-weight: 600; }
    .payment-amount { font-size: 1.15rem; font-weight: 800; color: #166534; }

    .payment-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        margin: .5rem 0;
    }

    .p-chip {
        font-size: .72rem;
        padding: 3px 9px;
        border-radius: 8px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }
    .chip-user    { background: #f0f9ff; color: #0369a1; }
    .chip-booking { background: #fdf4ff; color: #7e22ce; }
    .chip-method  { background: #fff7ed; color: #9a3412; }
    .chip-date    { background: #f9fafb; color: #374151; }

    .payment-status-row { margin-top: .6rem; }

    .btn-view-pay {
        display: block;
        width: 100%;
        padding: 8px;
        border-radius: 10px;
        font-size: .85rem;
        font-weight: 600;
        text-align: center;
        background: #eff6ff;
        color: #1d4ed8;
        text-decoration: none;
        border: none;
        margin-top: .75rem;
    }
}
</style>
@endsection

@section('content')
<div class="container-fluid" id="adminPaymentsAutoRefresh">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h4 mb-0 fw-bold" style="color:#1f144a;">
            <i class="fas fa-file-invoice-dollar me-2 opacity-75"></i>المدفوعات
        </h1>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-sm btn-outline-secondary rounded-pill">
            <i class="fas fa-arrow-right me-1"></i>العودة
        </a>
    </div>

    {{-- Filters --}}
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-body p-3">
            <form method="GET" class="row g-2">
                <div class="col-6 col-md-3">
                    <select name="status" class="form-select form-select-sm">
                        <option value="">كل الحالات</option>
                        @foreach(['pending','processing','paid','failed','refunded','cancelled'] as $st)
                            <option value="{{ $st }}" {{ request('status')===$st ? 'selected' : '' }}>{{ $st }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-3">
                    <select name="method" class="form-select form-select-sm">
                        <option value="">كل الطرق</option>
                        @foreach(['card'] as $m)
                            <option value="{{ $m }}" {{ request('method')===$m ? 'selected' : '' }}>{{ $m }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-3">
                    <select name="provider" class="form-select form-select-sm">
                        <option value="">كل المزودين</option>
                        @foreach(['manual','moyasar','hyperpay','paytabs','tap'] as $p)
                            <option value="{{ $p }}" {{ request('provider')===$p ? 'selected' : '' }}>{{ $p }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-3">
                    <button class="btn btn-primary btn-sm w-100 rounded-3"><i class="fas fa-filter me-1"></i>تصفية</button>
                </div>
            </form>
        </div>
    </div>

    {{-- 🖥️ Desktop Table --}}
    <div class="card border-0 shadow-sm desktop-only">
        <div class="table-responsive">
            <table class="table table-striped align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>المستخدم</th>
                        <th>الحجز</th>
                        <th>المبلغ</th>
                        <th>العملة</th>
                        <th>الطريقة</th>
                        <th>المزود</th>
                        <th>الحالة</th>
                        <th>السبب</th>
                        <th>تاريخ</th>
                        <th>إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $payment)
                        <tr>
                            <td>{{ $payment->id }}</td>
                            <td>
                                @if($payment->user)
                                    <a href="{{ route('admin.customers.show', $payment->user->id) }}">{{ $payment->user->name }}</a>
                                @else -
                                @endif
                            </td>
                            <td>
                                @if($payment->booking)
                                    <a href="{{ route('admin.bookings.show', $payment->booking) }}">{{ $payment->booking->booking_reference }}</a>
                                @else -
                                @endif
                            </td>
                            <td>{{ number_format($payment->amount, 2) }}</td>
                            <td>{{ $payment->currency }}</td>
                            <td>{{ $payment->method ?? '-' }}</td>
                            <td>{{ $payment->provider ?? '-' }}</td>
                            <td>
                                <span class="badge
                                    @if($payment->status==='paid') bg-success
                                    @elseif($payment->status==='failed') bg-danger
                                    @elseif($payment->status==='processing') bg-info
                                    @elseif($payment->status==='refunded') bg-secondary
                                    @else bg-warning text-dark @endif">
                                    {{ $payment->status }}
                                </span>
                            </td>
                            <td>
                                @php $reason = $payment->failure_reason ?: ($payment->notes ?: ''); @endphp
                                @if($reason !== '')
                                    <span class="text-muted small">{{ Str::limit($reason, 60) }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td><small>{{ optional($payment->created_at)->format('Y-m-d H:i') }}</small></td>
                            <td>
                                <a href="{{ route('admin.payments.show', $payment) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="11" class="text-center text-muted py-4">لا توجد مدفوعات</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-body py-3">{{ $payments->links() }}</div>
    </div>

    {{-- 📱 Mobile Cards --}}
    <div class="mobile-only">
        @forelse($payments as $payment)
            <div class="payment-card">
                <div class="payment-card-top">
                    <div>
                        <div class="payment-id">#{{ $payment->id }}</div>
                        <div class="payment-amount">{{ number_format($payment->amount, 2) }} {{ $payment->currency }}</div>
                    </div>
                    <span class="badge
                        @if($payment->status==='paid') bg-success
                        @elseif($payment->status==='failed') bg-danger
                        @elseif($payment->status==='processing') bg-info
                        @elseif($payment->status==='refunded') bg-secondary
                        @else bg-warning text-dark @endif">
                        {{ $payment->status }}
                    </span>
                </div>

                <div class="payment-meta">
                    @if($payment->user)
                        <span class="p-chip chip-user"><i class="fas fa-user"></i>{{ $payment->user->name }}</span>
                    @endif
                    @if($payment->booking)
                        <span class="p-chip chip-booking"><i class="fas fa-calendar-check"></i>{{ $payment->booking->booking_reference }}</span>
                    @endif
                    @if($payment->method)
                        <span class="p-chip chip-method"><i class="fas fa-credit-card"></i>{{ $payment->method }}</span>
                    @endif
                    @if($payment->provider)
                        <span class="p-chip chip-date"><i class="fas fa-building"></i>{{ $payment->provider }}</span>
                    @endif
                    <span class="p-chip chip-date"><i class="fas fa-clock"></i>{{ optional($payment->created_at)->format('Y-m-d') }}</span>
                </div>

                <a href="{{ route('admin.payments.show', $payment) }}" class="btn-view-pay">
                    <i class="fas fa-eye me-1"></i>عرض التفاصيل
                </a>
            </div>
        @empty
            <div class="text-center py-5">
                <i class="fas fa-file-invoice-dollar fa-3x text-muted mb-3 opacity-25"></i>
                <p class="text-muted fw-bold">لا توجد مدفوعات</p>
            </div>
        @endforelse
        <div class="mt-3">{{ $payments->links() }}</div>
    </div>

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var container = document.getElementById('adminPaymentsAutoRefresh');
    if (!container) return;
    function refreshPayments() {
        if (document.visibilityState !== 'visible') return;
        fetch(window.location.href, { headers: { 'X-Requested-With': 'XMLHttpRequest' }, cache: 'no-store' })
        .then(r => r.text())
        .then(html => {
            var doc = new DOMParser().parseFromString(html, 'text/html');
            var newC = doc.getElementById('adminPaymentsAutoRefresh');
            if (newC) container.innerHTML = newC.innerHTML;
        }).catch(() => {});
    }
    setInterval(refreshPayments, 5000);
});
</script>
@endpush

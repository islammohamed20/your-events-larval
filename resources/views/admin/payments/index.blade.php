@extends('layouts.admin')

@section('title', 'إدارة المدفوعات')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0"><i class="fas fa-file-invoice-dollar me-2"></i>المدفوعات</h1>
        <div>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>العودة للوحة التحكم
            </a>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" class="row g-2">
                <div class="col-md-3">
                    <select name="status" class="form-select">
                        <option value="">كل الحالات</option>
                        @foreach(['pending','processing','paid','failed','refunded','cancelled'] as $st)
                            <option value="{{ $st }}" {{ request('status')===$st ? 'selected' : '' }}>{{ $st }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="method" class="form-select">
                        <option value="">كل الطرق</option>
                        @foreach(['card','bank_transfer','cash'] as $m)
                            <option value="{{ $m }}" {{ request('method')===$m ? 'selected' : '' }}>{{ $m }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="provider" class="form-select">
                        <option value="">كل المزودين</option>
                        @foreach(['manual','moyasar','hyperpay','paytabs','tap'] as $p)
                            <option value="{{ $p }}" {{ request('provider')===$p ? 'selected' : '' }}>{{ $p }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <button class="btn btn-primary w-100"><i class="fas fa-filter me-1"></i>تصفية</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped align-middle">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>المستخدم</th>
                            <th>الحجز</th>
                            <th>المبلغ</th>
                            <th>العملة</th>
                            <th>الطريقة</th>
                            <th>المزود</th>
                            <th>الحالة</th>
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
                                        <a href="{{ route('admin.customers.show', $payment->user->id) }}">
                                            {{ $payment->user->name }}
                                        </a>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    @if($payment->booking)
                                        <a href="{{ route('admin.bookings.show', $payment->booking) }}">
                                            {{ $payment->booking->booking_reference }}
                                        </a>
                                    @else
                                        -
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
                                <td>{{ optional($payment->created_at)->format('Y-m-d H:i') }}</td>
                                <td>
                                    <a href="{{ route('admin.payments.show', $payment) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center text-muted">لا توجد مدفوعات</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $payments->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

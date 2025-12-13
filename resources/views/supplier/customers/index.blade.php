@extends('supplier.layouts.app')

@section('title', 'العملاء')
@section('page-title', 'العملاء')

@section('content')
<!-- Info Alert -->
<div class="alert alert-info border-0 mb-4" style="border-radius: 15px;">
    <div class="d-flex align-items-center">
        <i class="fas fa-info-circle me-2 fs-4"></i>
        <div>
            <strong>العملاء المعروضون:</strong> هؤلاء العملاء الذين حجزوا خدماتك وتم تأكيد أو إتمام حجوزاتهم.
        </div>
    </div>
</div>

<!-- Customers Grid -->
<div class="row g-4">
    @forelse($customers as $customer)
    @php
        $user = $customer->user;
    @endphp
    @if($user)
    <div class="col-lg-4 col-md-6">
        <div class="content-card h-100">
            <div class="p-4">
                <div class="text-center mb-3">
                    <div class="user-avatar mx-auto mb-3" style="width: 80px; height: 80px; font-size: 2rem; background: linear-gradient(135deg, #2dbcae, #1a8f84);">
                        {{ mb_substr($user->name ?? 'ع', 0, 1) }}
                    </div>
                    <h5 class="fw-bold mb-1">{{ $user->name ?? 'عميل' }}</h5>
                    <p class="text-muted small mb-0">{{ $user->email ?? '' }}</p>
                </div>
                
                <div class="border-top pt-3 mt-3">
                    <div class="row text-center g-3">
                        <div class="col-4">
                            <div class="fw-bold" style="color: #1f144a;">{{ $customer->total_bookings }}</div>
                            <small class="text-muted">حجوزات</small>
                        </div>
                        <div class="col-4">
                            <div class="fw-bold text-success">{{ number_format($customer->total_spent, 0) }}</div>
                            <small class="text-muted">ر.س</small>
                        </div>
                        <div class="col-4">
                            <div class="fw-bold text-info">{{ \Carbon\Carbon::parse($customer->last_booking)->diffForHumans() }}</div>
                            <small class="text-muted">آخر حجز</small>
                        </div>
                    </div>
                </div>
                
                <div class="mt-3">
                    <a href="{{ route('supplier.customers.show', $user->id) }}" class="btn btn-supplier-primary w-100">
                        <i class="fas fa-eye me-1"></i> عرض التفاصيل
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endif
    @empty
    <div class="col-12">
        <div class="content-card">
            <div class="text-center py-5">
                <i class="fas fa-users fa-4x text-muted mb-3"></i>
                <h4 class="text-muted">لا يوجد عملاء</h4>
                <p class="text-muted">لم يتم استقبال أي حجوزات مؤكدة بعد</p>
            </div>
        </div>
    </div>
    @endforelse
</div>

<!-- Pagination -->
@if($customers->hasPages())
<div class="mt-4 d-flex justify-content-center">
    {{ $customers->links() }}
</div>
@endif
@endsection

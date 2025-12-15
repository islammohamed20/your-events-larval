@extends('supplier.layouts.app')

@section('title', 'الحجوزات')
@section('page-title', 'الحجوزات')

@section('content')
<div class="container-fluid">
    <!-- Navigation Tabs -->
    <ul class="nav nav-tabs mb-4" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="available-tab" data-bs-toggle="tab" href="#available" role="tab">
                <i class="fas fa-clock me-2"></i>متاحة للقبول
                @if($availableBookings->count() > 0)
                    <span class="badge bg-warning ms-2">{{ $availableBookings->count() }}</span>
                @endif
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="accepted-tab" data-bs-toggle="tab" href="#accepted" role="tab">
                <i class="fas fa-check-circle me-2"></i>مقبولة
                @if($acceptedBookings->count() > 0)
                    <span class="badge bg-success ms-2">{{ $acceptedBookings->count() }}</span>
                @endif
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="rejected-tab" data-bs-toggle="tab" href="#rejected" role="tab">
                <i class="fas fa-times-circle me-2"></i>مرفوضة/منتهية
            </a>
        </li>
    </ul>

    <!-- Tab Content -->
    <div class="tab-content">
        <!-- Available Bookings -->
        <div class="tab-pane fade show active" id="available" role="tabpanel">
            @if($availableBookings->count() > 0)
                <div class="row">
                    @foreach($availableBookings as $booking)
                        <div class="col-lg-6 mb-4">
                            <div class="card border-warning shadow-sm">
                                <div class="card-header bg-warning text-dark d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0">
                                        <i class="fas fa-calendar-alt me-2"></i>
                                        {{ $booking->booking_reference }}
                                    </h6>
                                    <span class="badge bg-light text-dark">
                                        <i class="fas fa-clock me-1"></i>
                                        ينتهي في: {{ $booking->expires_at->diffForHumans() }}
                                    </span>
                                </div>
                                <div class="card-body">
                                    <!-- Customer Info (hidden per request) -->

                                    <!-- Services List -->
                                    @if($booking->quote && $booking->quote->items->count() > 0)
                                        <div class="mb-3">
                                            <h6 class="text-muted mb-2"><i class="fas fa-concierge-bell me-2"></i>الخدمات المطلوبة</h6>
                                            <ul class="list-unstyled">
                                                @foreach($booking->quote->items as $item)
                                                    <li class="mb-1">
                                                        <i class="fas fa-check-circle text-success me-1"></i>
                                                        {{ $item->service_name }}
                                                        @if($item->quantity > 1)
                                                            <span class="badge bg-secondary">× {{ $item->quantity }}</span>
                                                        @endif
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif

                                    <!-- Amount -->
                                    <div class="mb-3">
                                        <h6 class="text-muted mb-2"><i class="fas fa-money-bill-wave me-2"></i>المبلغ الإجمالي</h6>
                                        <h4 class="text-success mb-0">{{ number_format($booking->total_amount, 2) }} ر.س</h4>
                                        <small class="text-muted">تم الدفع مسبقاً ✓</small>
                                    </div>

                                    <!-- Competition Info -->
                                    <div class="alert alert-info mb-3">
                                        <i class="fas fa-info-circle me-2"></i>
                                        <strong>منافسة نشطة:</strong> 
                                        تم إرسال إشعارات لـ {{ $booking->notified_suppliers_count }} موردين.
                                        @if($booking->views_count > 0)
                                            شاهد الحجز {{ $booking->views_count }} موردين.
                                        @endif
                                    </div>

                                    <!-- Actions -->
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('supplier.bookings.show', $booking->id) }}" class="btn btn-primary flex-fill">
                                            <i class="fas fa-eye me-2"></i><span style="color: white;">عرض التفاصيل</span>
                                        </a>
                                        <form action="{{ route('supplier.bookings.accept', $booking->id) }}" method="POST" class="flex-fill">
                                            @csrf
                                            <button type="submit" class="btn btn-success w-100" onclick="return confirm('هل أنت متأكد من قبول هذا الحجز؟')">
                                                <i class="fas fa-check me-2"></i><span style="color: white;">قبول الحجز</span>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="d-flex justify-content-center">
                    {{ $availableBookings->links() }}
                </div>
            @else
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    لا توجد حجوزات متاحة للقبول حالياً
                </div>
            @endif
        </div>

        <!-- Accepted Bookings -->
        <div class="tab-pane fade" id="accepted" role="tabpanel">
            @if($acceptedBookings->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>رقم الحجز</th>
                                <th>العميل</th>
                                <th>الخدمات</th>
                                <th>المبلغ</th>
                                <th>الحالة</th>
                                <th>تاريخ القبول</th>
                                <th>إجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($acceptedBookings as $booking)
                                <tr>
                                    <td>{{ $booking->booking_reference }}</td>
                                    <td>—</td>
                                    <td>
                                        @if($booking->quote)
                                            {{ $booking->quote->items->count() }} خدمة
                                        @endif
                                    </td>
                                    <td>{{ number_format($booking->total_amount, 2) }} ر.س</td>
                                    <td>
                                        @if($booking->status === 'confirmed')
                                            <span class="badge bg-success">مؤكد</span>
                                        @elseif($booking->status === 'completed')
                                            <span class="badge bg-primary">مكتمل</span>
                                        @endif
                                    </td>
                                    <td>{{ $booking->accepted_at ? $booking->accepted_at->format('Y-m-d H:i') : '-' }}</td>
                                    <td>
                                        <a href="{{ route('supplier.bookings.show', $booking->id) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-center">
                    {{ $acceptedBookings->links() }}
                </div>
            @else
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    لا توجد حجوزات مقبولة
                </div>
            @endif
        </div>

        <!-- Rejected/Expired Bookings -->
        <div class="tab-pane fade" id="rejected" role="tabpanel">
            @if($rejectedBookings->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>رقم الحجز</th>
                                <th>العميل</th>
                                <th>الحالة</th>
                                <th>تاريخ الرد</th>
                                <th>السبب</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($rejectedBookings as $notification)
                                <tr>
                                    <td>{{ $notification->booking->booking_reference }}</td>
                                    <td>—</td>
                                    <td>
                                        @if($notification->response === 'rejected')
                                            <span class="badge bg-danger">مرفوض</span>
                                        @elseif($notification->response === 'expired')
                                            <span class="badge bg-secondary">منتهي (قبله مورد آخر)</span>
                                        @endif
                                    </td>
                                    <td>{{ $notification->responded_at ? $notification->responded_at->format('Y-m-d H:i') : '-' }}</td>
                                    <td>{{ $notification->rejection_reason ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-center">
                    {{ $rejectedBookings->links() }}
                </div>
            @else
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    لا توجد حجوزات مرفوضة أو منتهية
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
// Auto-refresh available bookings every 30 seconds
setInterval(function() {
    if ($('#available-tab').hasClass('active')) {
        location.reload();
    }
}, 30000);
</script>
@endpush
@endsection

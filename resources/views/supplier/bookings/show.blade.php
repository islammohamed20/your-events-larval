@extends('supplier.layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="mb-1">تفاصيل الحجز #{{ $booking->id }}</h2>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('supplier.dashboard') }}">لوحة التحكم</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('supplier.bookings.index') }}">الحجوزات</a></li>
                            <li class="breadcrumb-item active">حجز #{{ $booking->id }}</li>
                        </ol>
                    </nav>
                </div>
                <a href="{{ route('supplier.bookings.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-right me-2"></i> العودة
                </a>
            </div>

            <div class="row">
                <!-- Booking Details -->
                <div class="col-lg-8">
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-white py-3">
                            <h5 class="mb-0">معلومات الحجز</h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong>الحالة:</strong>
                                    <span class="ms-2">{!! $booking->status_badge !!}</span>
                                </div>
                                <div class="col-md-6">
                                    <strong>تاريخ الحجز:</strong>
                                    <span class="ms-2">{{ $booking->created_at->format('Y-m-d H:i') }}</span>
                                </div>
                            </div>

                            @if($booking->isActive() && !$booking->isExpired())
                            <div class="alert alert-warning mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <i class="fas fa-clock me-2"></i>
                                        <strong>حجز نشط - المنافسة جارية!</strong>
                                        <p class="mb-0 mt-1 small">
                                            تم إرسال إشعارات لـ {{ $booking->notified_suppliers_count }} مورد.
                                            @if($notification && !$notification->responded_at)
                                                كن الأول في القبول!
                                            @endif
                                        </p>
                                    </div>
                                    <div class="text-center">
                                        <div class="countdown-timer fw-bold fs-5" data-expires="{{ $booking->expires_at->toIso8601String() }}">
                                            <i class="fas fa-spinner fa-spin"></i>
                                        </div>
                                        <small class="text-muted">الوقت المتبقي</small>
                                    </div>
                                </div>
                            </div>
                            @endif

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong>رقم عرض السعر:</strong>
                                    <span class="ms-2">#{{ $booking->quote_id }}</span>
                                </div>
                                <div class="col-md-6">
                                    <strong>تاريخ الفعالية:</strong>
                                    <span class="ms-2">{{ $booking->event_date }}</span>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong>اسم العميل:</strong>
                                    <span class="ms-2">{{ $booking->client_name }}</span>
                                </div>
                                <div class="col-md-6">
                                    <strong>رقم الجوال:</strong>
                                    <span class="ms-2" dir="ltr">{{ $booking->client_phone }}</span>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong>نوع الفعالية:</strong>
                                    <span class="ms-2">{{ $booking->activity_name ?? '—' }}</span>
                                </div>
                                <div class="col-md-6">
                                    <strong>عدد الضيوف:</strong>
                                    <span class="ms-2">{{ $booking->guests_count }} ضيف</span>
                                </div>
                            </div>

                            <div class="mb-3">
                                <strong>الموقع:</strong>
                                <p class="mb-1 mt-1">{{ $booking->event_location ?: 'غير محدد' }}</p>
                                @if($booking->event_lat && $booking->event_lng)
                                    <a class="small text-decoration-none" target="_blank" rel="noopener noreferrer" href="https://www.google.com/maps?q={{ $booking->event_lat }},{{ $booking->event_lng }}">
                                        <i class="fas fa-map-marked-alt me-1"></i>فتح على الخريطة
                                    </a>
                                @endif
                            </div>

                            @if($booking->event_lat && $booking->event_lng)
                            <div class="mb-3">
                                <strong class="d-block mb-2">الخريطة:</strong>
                                <div class="ratio ratio-16x9 rounded border overflow-hidden">
                                    <iframe
                                        src="https://www.google.com/maps?q={{ $booking->event_lat }},{{ $booking->event_lng }}&z=15&output=embed"
                                        loading="lazy"
                                        referrerpolicy="no-referrer-when-downgrade"
                                        style="border:0;"
                                        allowfullscreen>
                                    </iframe>
                                </div>
                            </div>
                            @endif

                            @if($booking->special_requests)
                            <div class="mb-3">
                                <strong>ملاحظات العميل:</strong>
                                <p class="mb-0 mt-1 text-muted">{{ $booking->special_requests }}</p>
                            </div>
                            @endif

                            @if($booking->supplier_id && $booking->supplier_id == Auth::guard('supplier')->id())
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle me-2"></i>
                                <strong>تم قبول الحجز بنجاح!</strong>
                                <p class="mb-0 mt-1">
                                    تم القبول في: {{ $booking->accepted_at ? $booking->accepted_at->format('Y-m-d H:i') : '-' }}
                                </p>
                            </div>
                            @endif

                            @if($notification && $notification->rejection_reason)
                            <div class="alert alert-danger">
                                <i class="fas fa-times-circle me-2"></i>
                                <strong>تم رفض الحجز</strong>
                                <p class="mb-0 mt-1">السبب: {{ $notification->rejection_reason }}</p>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Services -->
                    @if($booking->quote && $booking->quote->items && $booking->quote->items->count() > 0)
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-white py-3">
                            <h5 class="mb-0">الخدمات المطلوبة</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>الخدمة</th>
                                            <th>الوصف</th>
                                            <th>الكمية</th>
                                            <th>السعر</th>
                                            <th>الإجمالي</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($booking->quote->items as $item)
                                        <tr>
                                            <td>{{ $item->service->name ?? $item->service_name }}</td>
                                            <td>{{ Str::limit($item->service->description ?? $item->service_description, 50) }}</td>
                                            <td>{{ $item->quantity }}</td>
                                            <td>{{ number_format($item->price, 2) }} {{ __('common.currency') }}</td>
                                            <td>{{ number_format($item->subtotal, 2) }} {{ __('common.currency') }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr class="table-light">
                                            <td colspan="4" class="text-end"><strong>الإجمالي:</strong></td>
                                            <td><strong>{{ number_format($booking->total_amount, 2) }} {{ __('common.currency') }}</strong></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Customer & Actions -->
                <div class="col-lg-4">
                    <!-- Customer Info hidden per request -->

                    <!-- Actions -->
                    @if($notification && !$notification->responded_at && $booking->isActive() && !$booking->isExpired())
                    <div class="card shadow-sm border-primary mb-4">
                        <div class="card-header bg-primary text-white py-3">
                            <h5 class="mb-0">إجراءات سريعة</h5>
                        </div>
                        <div class="card-body">
                            <p class="text-muted mb-3">
                                يرجى اتخاذ قرار بشأن هذا الحجز. أول مورد يقبل سيفوز بالحجز.
                            </p>
                            
                            <form action="{{ route('supplier.bookings.accept', $booking->id) }}" method="POST" class="mb-3">
                                @csrf
                                <button type="submit" class="btn btn-success w-100 py-2" 
                                        onclick="return confirm('هل أنت متأكد من قبول هذا الحجز؟')">
                                    <i class="fas fa-check-circle me-2"></i>
                                    قبول الحجز
                                </button>
                            </form>

                            <button type="button" class="btn btn-outline-danger w-100 py-2" 
                                    data-bs-toggle="modal" data-bs-target="#rejectModal">
                                <i class="fas fa-times-circle me-2"></i>
                                رفض الحجز
                            </button>
                        </div>
                    </div>
                    @endif

                    <!-- Competition Stats -->
                    @if($booking->isActive())
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-white py-3">
                            <h5 class="mb-0">إحصائيات المنافسة</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <strong>عدد الموردين المشاركين:</strong>
                                <span class="badge bg-info ms-2">{{ $booking->notified_suppliers_count }}</span>
                            </div>
                            <div class="mb-3">
                                <strong>عدد المشاهدات:</strong>
                                <span class="badge bg-secondary ms-2">{{ $booking->views_count }}</span>
                            </div>
                            <div class="mb-0">
                                <strong>ينتهي في:</strong>
                                <p class="mb-0 mt-1 text-danger">
                                    {{ $booking->expires_at->format('Y-m-d H:i') }}
                                </p>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('supplier.bookings.reject', $booking->id) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="rejectModalLabel">رفض الحجز</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="rejection_reason" class="form-label">سبب الرفض</label>
                        <textarea class="form-control" id="rejection_reason" name="rejection_reason" 
                                  rows="4" required 
                                  placeholder="يرجى ذكر سبب رفض الحجز..."></textarea>
                        <small class="text-muted">سيتم إرسال السبب إلى العميل</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-times-circle me-2"></i>
                        تأكيد الرفض
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Countdown Timer
function updateCountdowns() {
    document.querySelectorAll('.countdown-timer').forEach(function(element) {
        const expiresAt = new Date(element.dataset.expires);
        const now = new Date();
        const diff = expiresAt - now;
        
        if (diff <= 0) {
            element.innerHTML = '<span class="text-danger">منتهي</span>';
            element.closest('.alert-warning')?.classList.add('alert-danger');
            element.closest('.alert-warning')?.classList.remove('alert-warning');
            return;
        }
        
        const hours = Math.floor(diff / (1000 * 60 * 60));
        const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((diff % (1000 * 60)) / 1000);
        
        element.innerHTML = `${hours}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
        
        if (hours < 1) {
            element.classList.add('text-danger');
        }
    });
}

// Update every second
setInterval(updateCountdowns, 1000);
updateCountdowns();

// Mark as viewed when page loads
const shouldMarkViewed = {{ ($notification && !$notification->viewed_at) ? 'true' : 'false' }};
if (shouldMarkViewed) {
    fetch('{{ route('supplier.bookings.show', $booking->id) }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ mark_viewed: true })
    }).catch(() => {});
}
</script>
@endpush
@endsection

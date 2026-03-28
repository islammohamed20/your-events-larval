@extends('layouts.admin')

@section('title', 'إدارة الحجوزات')

@section('styles')
<style>
/* ============================================================
   BOOKINGS — MOBILE RESPONSIVE
   ============================================================ */
@media (min-width: 992px) { .mobile-only { display: none !important; } }
@media (max-width: 991.98px) {
    .desktop-only { display: none !important; }

    .booking-card {
        background: #fff;
        border-radius: 16px;
        border: 1px solid #eef1f6;
        padding: 1rem;
        margin-bottom: 1rem;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        position: relative;
    }

    .booking-card-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: .75rem;
    }

    .booking-ref {
        font-size: 1rem;
        font-weight: 800;
        color: #1f144a;
    }

    .booking-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        margin-bottom: .75rem;
    }

    .meta-chip {
        font-size: 0.72rem;
        padding: 3px 9px;
        border-radius: 8px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }
    .chip-date   { background: #f0f9ff; color: #0369a1; }
    .chip-amount { background: #f0fdf4; color: #166534; }
    .chip-type   { background: #fdf4ff; color: #7e22ce; }
    .chip-loc    { background: #fff7ed; color: #9a3412; }

    .booking-actions {
        display: flex;
        gap: 8px;
        margin-top: .75rem;
        border-top: 1px solid #f3f4f6;
        padding-top: .75rem;
    }

    .booking-actions a, .booking-actions button {
        flex: 1;
        padding: 7px;
        border-radius: 10px;
        font-size: .8rem;
        font-weight: 600;
        text-align: center;
        text-decoration: none;
        border: none;
    }
    .btn-view-booking  { background: #eff6ff; color: #1d4ed8; }
    .btn-ok-booking    { background: #f0fdf4; color: #166534; }
    .btn-del-booking   { background: #fff1f2; color: #be123c; }
    .btn-done-booking  { background: #f5f3ff; color: #5b21b6; }

    .filter-scroll {
        display: flex;
        gap: 8px;
        overflow-x: auto;
        padding-bottom: 6px;
        scrollbar-width: none;
    }
    .filter-scroll::-webkit-scrollbar { display: none; }
    .filter-pill {
        white-space: nowrap;
        padding: 5px 14px;
        border-radius: 20px;
        font-size: .8rem;
        font-weight: 600;
        border: 1px solid #e5e7eb;
        background: #fff;
        color: #374151;
        text-decoration: none;
        flex-shrink: 0;
    }
    .filter-pill.active { background: #1f144a; color: #fff; border-color: #1f144a; }
}
</style>
@endsection

@section('content')
<div class="container-fluid">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h4 mb-0 fw-bold" style="color:#1f144a;">
            <i class="fas fa-calendar-check me-2 opacity-75"></i>إدارة الحجوزات
        </h1>
        {{-- Desktop filter dropdown --}}
        <div class="dropdown desktop-only">
            <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                <i class="fas fa-filter me-1"></i>تصفية حسب الحالة
            </button>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="{{ route('admin.bookings.index') }}">جميع الحجوزات</a></li>
                <li><a class="dropdown-item" href="{{ route('admin.bookings.index', ['status' => 'pending']) }}">في الانتظار</a></li>
                <li><a class="dropdown-item" href="{{ route('admin.bookings.index', ['status' => 'awaiting_supplier']) }}">بانتظار المورد</a></li>
                <li><a class="dropdown-item" href="{{ route('admin.bookings.index', ['status' => 'confirmed']) }}">مؤكدة</a></li>
                <li><a class="dropdown-item" href="{{ route('admin.bookings.index', ['status' => 'completed']) }}">مكتملة</a></li>
                <li><a class="dropdown-item" href="{{ route('admin.bookings.index', ['status' => 'cancelled']) }}">ملغية</a></li>
                <li><a class="dropdown-item" href="{{ route('admin.bookings.index', ['status' => 'expired']) }}">منتهية الصلاحية</a></li>
            </ul>
        </div>
    </div>

    {{-- Mobile filter pills --}}
    <div class="filter-scroll mobile-only mb-3">
        <a href="{{ route('admin.bookings.index') }}" class="filter-pill {{ !request('status') ? 'active' : '' }}">الكل</a>
        <a href="{{ route('admin.bookings.index', ['status' => 'pending']) }}" class="filter-pill {{ request('status') === 'pending' ? 'active' : '' }}">انتظار</a>
        <a href="{{ route('admin.bookings.index', ['status' => 'confirmed']) }}" class="filter-pill {{ request('status') === 'confirmed' ? 'active' : '' }}">مؤكدة</a>
        <a href="{{ route('admin.bookings.index', ['status' => 'completed']) }}" class="filter-pill {{ request('status') === 'completed' ? 'active' : '' }}">مكتملة</a>
        <a href="{{ route('admin.bookings.index', ['status' => 'cancelled']) }}" class="filter-pill {{ request('status') === 'cancelled' ? 'active' : '' }}">ملغية</a>
        <a href="{{ route('admin.bookings.index', ['status' => 'expired']) }}" class="filter-pill {{ request('status') === 'expired' ? 'active' : '' }}">منتهية</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow-sm border-0 rounded-4 overflow-hidden" id="adminBookingsAutoRefresh">
        @if($bookings->count() > 0)

            {{-- 🖥️ Desktop Table --}}
            <div class="table-responsive desktop-only">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>رقم الحجز</th>
                            <th>العميل</th>
                            <th>نوع الخدمة</th>
                            <th>تاريخ المناسبة</th>
                            <th>الموقع</th>
                            <th>المبلغ</th>
                            <th>الحالة</th>
                            <th>تاريخ الحجز</th>
                            <th class="text-center">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($bookings as $booking)
                            <tr>
                                <td><strong class="text-primary">{{ $booking->booking_reference }}</strong></td>
                                <td><strong>{{ $booking->client_name ?: ($booking->user->name ?? '—') }}</strong></td>
                                <td>
                                    @if($booking->package)
                                        <span class="badge bg-info"><i class="fas fa-box me-1"></i>باقة</span>
                                    @elseif($booking->service)
                                        <span class="badge bg-success"><i class="fas fa-cogs me-1"></i>خدمة</span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    <i class="fas fa-calendar me-1"></i>{{ $booking->event_date ? $booking->event_date->format('Y-m-d') : 'غير محدد' }}
                                    @if($booking->event_time)
                                        <br><small class="text-muted"><i class="fas fa-clock me-1"></i>{{ $booking->event_time }}</small>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="small">{{ $booking->event_location ?: '—' }}</span>
                                        @if($booking->event_lat && $booking->event_lng)
                                            <a class="text-decoration-none" target="_blank" href="https://www.google.com/maps?q={{ $booking->event_lat }},{{ $booking->event_lng }}">
                                                <i class="fas fa-map-marker-alt text-danger"></i>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                                <td><strong class="text-success">{{ number_format($booking->total_amount) }} {{ __('common.currency') }}</strong></td>
                                <td>{!! $booking->status_badge !!}</td>
                                <td><small class="text-muted">{{ $booking->created_at->format('Y-m-d H:i') }}</small></td>
                                <td class="text-center">
                                    <div class="btn-group">
                                        <a href="{{ route('admin.bookings.show', $booking) }}" class="btn btn-sm btn-outline-primary" title="عرض التفاصيل">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if($booking->status === 'pending')
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li>
                                                        <form method="POST" action="{{ route('admin.bookings.update-status', $booking) }}">
                                                            @csrf @method('PATCH')
                                                            <input type="hidden" name="status" value="confirmed">
                                                            <button type="submit" class="dropdown-item text-success"><i class="fas fa-check me-2"></i>تأكيد الحجز</button>
                                                        </form>
                                                    </li>
                                                    <li>
                                                        <form method="POST" action="{{ route('admin.bookings.update-status', $booking) }}">
                                                            @csrf @method('PATCH')
                                                            <input type="hidden" name="status" value="cancelled">
                                                            <button type="submit" class="dropdown-item text-danger"><i class="fas fa-times me-2"></i>إلغاء الحجز</button>
                                                        </form>
                                                    </li>
                                                </ul>
                                            </div>
                                        @elseif($booking->status === 'confirmed')
                                            <form method="POST" action="{{ route('admin.bookings.update-status', $booking) }}" class="d-inline">
                                                @csrf @method('PATCH')
                                                <input type="hidden" name="status" value="completed">
                                                <button type="submit" class="btn btn-sm btn-outline-primary" title="تمييز كمكتمل">
                                                    <i class="fas fa-check-double"></i>
                                                </button>
                                            </form>
                                        @endif
                                        <form method="POST" action="{{ route('admin.bookings.destroy', $booking) }}" class="d-inline"
                                              onsubmit="return confirm('هل أنت متأكد من حذف هذا الحجز؟')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="حذف الحجز">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- 📱 Mobile Cards --}}
            <div class="p-3 mobile-only">
                @foreach($bookings as $booking)
                    <div class="booking-card">
                        <div class="booking-card-header">
                            <div>
                                <div class="booking-ref">#{{ $booking->booking_reference }}</div>
                                <div class="text-muted small mt-1">{{ $booking->client_name ?: ($booking->user->name ?? '—') }}</div>
                            </div>
                            <div>{!! $booking->status_badge !!}</div>
                        </div>

                        <div class="booking-meta">
                            @if($booking->package)
                                <span class="meta-chip chip-type"><i class="fas fa-box"></i>باقة</span>
                            @elseif($booking->service)
                                <span class="meta-chip chip-type"><i class="fas fa-cogs"></i>خدمة</span>
                            @endif

                            @if($booking->event_date)
                                <span class="meta-chip chip-date"><i class="fas fa-calendar"></i>{{ $booking->event_date->format('Y-m-d') }}</span>
                            @endif

                            <span class="meta-chip chip-amount"><i class="fas fa-money-bill"></i>{{ number_format($booking->total_amount) }} ر.س</span>

                            @if($booking->event_location)
                                <span class="meta-chip chip-loc"><i class="fas fa-map-marker-alt"></i>{{ Str::limit($booking->event_location, 20) }}</span>
                            @endif
                        </div>

                        <div class="text-muted" style="font-size:.75rem;">
                            <i class="fas fa-clock me-1 opacity-50"></i>{{ $booking->created_at->format('Y-m-d H:i') }}
                        </div>

                        <div class="booking-actions">
                            <a href="{{ route('admin.bookings.show', $booking) }}" class="btn-view-booking">
                                <i class="fas fa-eye me-1"></i>عرض
                            </a>

                            @if($booking->status === 'pending')
                                <form method="POST" action="{{ route('admin.bookings.update-status', $booking) }}" style="flex:1;display:flex;">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="status" value="confirmed">
                                    <button type="submit" class="btn-ok-booking w-100"><i class="fas fa-check me-1"></i>تأكيد</button>
                                </form>
                            @elseif($booking->status === 'confirmed')
                                <form method="POST" action="{{ route('admin.bookings.update-status', $booking) }}" style="flex:1;display:flex;">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="status" value="completed">
                                    <button type="submit" class="btn-done-booking w-100"><i class="fas fa-check-double me-1"></i>مكتمل</button>
                                </form>
                            @endif

                            <form method="POST" action="{{ route('admin.bookings.destroy', $booking) }}" style="flex:1;display:flex;"
                                  onsubmit="return confirm('حذف الحجز؟')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-del-booking w-100"><i class="fas fa-trash me-1"></i>حذف</button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>

            @if($bookings->hasPages())
                <div class="d-flex justify-content-center pb-4">
                    {{ $bookings->links() }}
                </div>
            @endif

        @else
            <div class="text-center py-5">
                <i class="fas fa-calendar-times fa-3x text-muted mb-3 opacity-25"></i>
                <h5 class="text-muted fw-bold">لا توجد حجوزات</h5>
                <p class="text-muted small">لم يتم العثور على أي حجوزات بعد.</p>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var container = document.getElementById('adminBookingsAutoRefresh');
    if (!container) return;

    function refreshAdminBookings() {
        if (document.visibilityState !== 'visible') return;
        fetch(window.location.href, { headers: { 'X-Requested-With': 'XMLHttpRequest' }, cache: 'no-store' })
            .then(r => r.text())
            .then(html => {
                var doc = new DOMParser().parseFromString(html, 'text/html');
                var newC = doc.getElementById('adminBookingsAutoRefresh');
                if (newC) container.innerHTML = newC.innerHTML;
            }).catch(() => {});
    }
    setInterval(refreshAdminBookings, 5000);
});
</script>
@endpush

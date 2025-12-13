@extends('supplier.layouts.app')

@section('title', 'الحجوزات')
@section('page-title', 'الحجوزات')

@section('content')
<!-- Filters -->
<div class="content-card mb-4">
    <div class="p-3">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label">الحالة</label>
                <select name="status" class="form-select">
                    <option value="">جميع الحالات</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>بانتظار التأكيد</option>
                    <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>مؤكد</option>
                    <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>قيد التنفيذ</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>مكتمل</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>ملغي</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">من تاريخ</label>
                <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">إلى تاريخ</label>
                <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-supplier-primary">
                    <i class="fas fa-filter me-1"></i> تصفية
                </button>
                <a href="{{ route('supplier.bookings.index') }}" class="btn btn-light">
                    <i class="fas fa-undo me-1"></i> إعادة
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Stats Summary -->
<div class="row g-3 mb-4">
    <div class="col-md-3 col-6">
        <div class="content-card p-3 text-center">
            <div class="fw-bold fs-4 text-warning">{{ $bookings->where('status', 'pending')->count() }}</div>
            <small class="text-muted">بانتظار التأكيد</small>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="content-card p-3 text-center">
            <div class="fw-bold fs-4 text-success">{{ $bookings->where('status', 'confirmed')->count() }}</div>
            <small class="text-muted">مؤكدة</small>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="content-card p-3 text-center">
            <div class="fw-bold fs-4 text-info">{{ $bookings->where('status', 'completed')->count() }}</div>
            <small class="text-muted">مكتملة</small>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="content-card p-3 text-center">
            <div class="fw-bold fs-4 text-danger">{{ $bookings->where('status', 'cancelled')->count() }}</div>
            <small class="text-muted">ملغية</small>
        </div>
    </div>
</div>

<!-- Bookings Table -->
<div class="content-card">
    <div class="table-responsive">
        <table class="table table-custom">
            <thead>
                <tr>
                    <th>رقم الحجز</th>
                    <th>العميل</th>
                    <th>الخدمة</th>
                    <th>تاريخ الفعالية</th>
                    <th>المبلغ</th>
                    <th>الحالة</th>
                    <th>الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bookings as $booking)
                <tr>
                    <td>
                        <span class="fw-bold">#{{ $booking->id }}</span>
                        <br>
                        <small class="text-muted">{{ $booking->created_at->format('Y/m/d') }}</small>
                    </td>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="user-avatar me-2" style="width: 40px; height: 40px; font-size: 0.9rem; background: #2dbcae;">
                                {{ mb_substr($booking->user->name ?? 'ز', 0, 1) }}
                            </div>
                            <div>
                                <div class="fw-semibold">{{ $booking->user->name ?? 'زائر' }}</div>
                                <small class="text-muted">{{ $booking->user->email ?? '' }}</small>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="fw-semibold">{{ $booking->service->name ?? '-' }}</div>
                        <small class="text-muted">{{ $booking->service->category->name ?? '' }}</small>
                    </td>
                    <td>
                        @if($booking->event_date)
                            <div class="fw-semibold">{{ $booking->event_date->format('Y/m/d') }}</div>
                            <small class="text-muted">{{ $booking->event_time ?? '' }}</small>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td>
                        <span class="fw-bold" style="color: #1f144a;">{{ number_format($booking->total_amount, 0) }} ر.س</span>
                    </td>
                    <td>
                        @php
                            $statusClass = match($booking->status) {
                                'pending' => 'status-pending',
                                'confirmed' => 'status-confirmed',
                                'in_progress' => 'status-confirmed',
                                'completed' => 'status-completed',
                                'cancelled' => 'status-cancelled',
                                default => 'status-pending'
                            };
                            $statusText = match($booking->status) {
                                'pending' => 'بانتظار التأكيد',
                                'confirmed' => 'مؤكد',
                                'in_progress' => 'قيد التنفيذ',
                                'completed' => 'مكتمل',
                                'cancelled' => 'ملغي',
                                default => 'غير محدد'
                            };
                        @endphp
                        <span class="status-badge {{ $statusClass }}">{{ $statusText }}</span>
                    </td>
                    <td>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <a class="dropdown-item" href="{{ route('supplier.bookings.show', $booking->id) }}">
                                        <i class="fas fa-eye me-2"></i>عرض التفاصيل
                                    </a>
                                </li>
                                @if($booking->status === 'pending')
                                <li>
                                    <form action="{{ route('supplier.bookings.update-status', $booking->id) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="confirmed">
                                        <button type="submit" class="dropdown-item text-success">
                                            <i class="fas fa-check me-2"></i>تأكيد الحجز
                                        </button>
                                    </form>
                                </li>
                                @endif
                                @if($booking->status === 'confirmed')
                                <li>
                                    <form action="{{ route('supplier.bookings.update-status', $booking->id) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="in_progress">
                                        <button type="submit" class="dropdown-item text-info">
                                            <i class="fas fa-play me-2"></i>بدء التنفيذ
                                        </button>
                                    </form>
                                </li>
                                @endif
                                @if(in_array($booking->status, ['confirmed', 'in_progress']))
                                <li>
                                    <form action="{{ route('supplier.bookings.update-status', $booking->id) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="completed">
                                        <button type="submit" class="dropdown-item text-primary">
                                            <i class="fas fa-check-double me-2"></i>إتمام الحجز
                                        </button>
                                    </form>
                                </li>
                                @endif
                                @if(!in_array($booking->status, ['completed', 'cancelled']))
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('supplier.bookings.update-status', $booking->id) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="cancelled">
                                        <button type="submit" class="dropdown-item text-danger" onclick="return confirm('هل أنت متأكد من إلغاء الحجز؟')">
                                            <i class="fas fa-times me-2"></i>إلغاء الحجز
                                        </button>
                                    </form>
                                </li>
                                @endif
                            </ul>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-5">
                        <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">لا توجد حجوزات</h5>
                        <p class="text-muted mb-0">لم يتم استلام أي حجوزات بعد</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Pagination -->
@if($bookings->hasPages())
<div class="mt-4 d-flex justify-content-center">
    {{ $bookings->withQueryString()->links() }}
</div>
@endif
@endsection

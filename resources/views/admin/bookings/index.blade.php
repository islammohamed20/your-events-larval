@extends('layouts.admin')

@section('title', 'إدارة الحجوزات')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">
                    <i class="fas fa-calendar-check me-2"></i>إدارة الحجوزات
                </h1>
                <div class="d-flex gap-2">
                    <div class="dropdown">
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
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="card shadow-sm">
                <div class="card-body">
                    @if($bookings->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>رقم الحجز</th>
                                        <th>العميل</th>
                                        <th>نوع الخدمة</th>
                                        <th>تاريخ المناسبة</th>
                                        <th>المبلغ</th>
                                        <th>الحالة</th>
                                        <th>تاريخ الحجز</th>
                                        <th>إجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($bookings as $booking)
                                        <tr>
                                            <td>
                                                <strong class="text-primary">{{ $booking->booking_reference }}</strong>
                                            </td>
                                            <td>
                                                <div>
                                                    <strong>{{ $booking->customer_name }}</strong>
                                                    <br>
                                                    <small class="text-muted">
                                                        <i class="fas fa-envelope me-1"></i>{{ $booking->customer_email }}
                                                        <br>
                                                        <i class="fas fa-phone me-1"></i>{{ $booking->customer_phone }}
                                                    </small>
                                                </div>
                                            </td>
                                            <td>
                                                @if($booking->package)
                                                    <span class="badge bg-info">
                                                        <i class="fas fa-box me-1"></i>باقة
                                                    </span>
                                                    <br>
                                                    <strong>{{ $booking->package->name }}</strong>
                                                @elseif($booking->service)
                                                    <span class="badge bg-success">
                                                        <i class="fas fa-cogs me-1"></i>خدمة
                                                    </span>
                                                    <br>
                                                    <strong>{{ $booking->service->name }}</strong>
                                                @endif
                                            </td>
                                            <td>
                                                <i class="fas fa-calendar me-1"></i>
                                                {{ $booking->event_date ? $booking->event_date->format('Y-m-d') : 'غير محدد' }}
                                                @if($booking->event_time)
                                                    <br>
                                                    <small class="text-muted">
                                                        <i class="fas fa-clock me-1"></i>{{ $booking->event_time }}
                                                    </small>
                                                @endif
                                            </td>
                                            <td>
                                                <strong class="text-success">{{ number_format($booking->total_amount) }} ريال</strong>
                                            </td>
                                            <td>
                                                @switch($booking->status)
                                                    @case('pending')
                                                        <span class="badge bg-warning">
                                                            <i class="fas fa-clock me-1"></i>في الانتظار
                                                        </span>
                                                        @break
                                                    @case('confirmed')
                                                        <span class="badge bg-success">
                                                            <i class="fas fa-check me-1"></i>مؤكد
                                                        </span>
                                                        @break
                                                    @case('completed')
                                                        <span class="badge bg-primary">
                                                            <i class="fas fa-check-double me-1"></i>مكتمل
                                                        </span>
                                                        @break
                                                    @case('cancelled')
                                                        <span class="badge bg-danger">
                                                            <i class="fas fa-times me-1"></i>ملغي
                                                        </span>
                                                        @break
                                                @endswitch
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    {{ $booking->created_at->format('Y-m-d H:i') }}
                                                </small>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('admin.bookings.show', $booking) }}" 
                                                       class="btn btn-sm btn-outline-primary" 
                                                       title="عرض التفاصيل">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    
                                                    @if($booking->status === 'pending')
                                                        <div class="dropdown">
                                                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" 
                                                                    type="button" data-bs-toggle="dropdown">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                            <ul class="dropdown-menu">
                                                                <li>
                                                                    <form method="POST" action="{{ route('admin.bookings.update-status', $booking) }}" class="d-inline">
                                                                        @csrf
                                                                        @method('PATCH')
                                                                        <input type="hidden" name="status" value="confirmed">
                                                                        <button type="submit" class="dropdown-item text-success">
                                                                            <i class="fas fa-check me-2"></i>تأكيد الحجز
                                                                        </button>
                                                                    </form>
                                                                </li>
                                                                <li>
                                                                    <form method="POST" action="{{ route('admin.bookings.update-status', $booking) }}" class="d-inline">
                                                                        @csrf
                                                                        @method('PATCH')
                                                                        <input type="hidden" name="status" value="cancelled">
                                                                        <button type="submit" class="dropdown-item text-danger">
                                                                            <i class="fas fa-times me-2"></i>إلغاء الحجز
                                                                        </button>
                                                                    </form>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    @elseif($booking->status === 'confirmed')
                                                        <form method="POST" action="{{ route('admin.bookings.update-status', $booking) }}" class="d-inline">
                                                            @csrf
                                                            @method('PATCH')
                                                            <input type="hidden" name="status" value="completed">
                                                            <button type="submit" class="btn btn-sm btn-outline-primary" title="تمييز كمكتمل">
                                                                <i class="fas fa-check-double"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                    
                                                    <!-- زر الحذف -->
                                                    <form method="POST" action="{{ route('admin.bookings.destroy', $booking) }}" class="d-inline" 
                                                          onsubmit="return confirm('هل أنت متأكد من حذف هذا الحجز؟ هذا الإجراء لا يمكن التراجع عنه.')">
                                                        @csrf
                                                        @method('DELETE')
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

                        <!-- Pagination -->
                        @if($bookings->hasPages())
                            <div class="d-flex justify-content-center mt-4">
                                {{ $bookings->links() }}
                            </div>
                        @endif
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">لا توجد حجوزات</h5>
                            <p class="text-muted">لم يتم العثور على أي حجوزات بعد.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
@if(request('status') == 'pending' || !request('status'))
<script>
    // Auto refresh every 30 seconds for pending bookings
    setInterval(function() {
        if (document.visibilityState === 'visible') {
            location.reload();
        }
    }, 30000);
</script>
@endif
@endpush
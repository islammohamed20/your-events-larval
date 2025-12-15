@extends('layouts.admin')

@section('title', 'تفاصيل الحجز')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">
                    <i class="fas fa-calendar-check me-2"></i>تفاصيل الحجز #{{ $booking->booking_reference }}
                </h1>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.bookings.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i>العودة للقائمة
                    </a>
                    @if($booking->status === 'pending')
                        <div class="dropdown">
                            <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-edit me-1"></i>تحديث الحالة
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
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-check-double me-1"></i>تمييز كمكتمل
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="row">
                <!-- معلومات الحجز الأساسية -->
                <div class="col-lg-8">
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-info-circle me-2"></i>معلومات الحجز
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">رقم الحجز:</label>
                                        <p class="text-primary fs-5">{{ $booking->booking_reference }}</p>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">حالة الحجز:</label>
                                        <div>
                                            @switch($booking->status)
                                                @case('pending')
                                                    <span class="badge bg-warning fs-6">
                                                        <i class="fas fa-clock me-1"></i>في الانتظار
                                                    </span>
                                                    @break
                                                @case('awaiting_supplier')
                                                    <span class="badge bg-info fs-6">
                                                        <i class="fas fa-hourglass-half me-1"></i>بانتظار المورد
                                                    </span>
                                                    @if($booking->expires_at)
                                                        <small class="text-muted d-block mt-1">
                                                            ينتهي في: {{ $booking->expires_at->diffForHumans() }}
                                                        </small>
                                                    @endif
                                                    @break
                                                @case('confirmed')
                                                    <span class="badge bg-success fs-6">
                                                        <i class="fas fa-check me-1"></i>مؤكد
                                                    </span>
                                                    @break
                                                @case('completed')
                                                    <span class="badge bg-primary fs-6">
                                                        <i class="fas fa-check-double me-1"></i>مكتمل
                                                    </span>
                                                    @break
                                                @case('cancelled')
                                                    <span class="badge bg-danger fs-6">
                                                        <i class="fas fa-times me-1"></i>ملغي
                                                    </span>
                                                    @break
                                                @case('expired')
                                                    <span class="badge bg-secondary fs-6">
                                                        <i class="fas fa-clock me-1"></i>منتهي الصلاحية
                                                    </span>
                                                    @break
                                            @endswitch
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">تاريخ الحجز:</label>
                                        <p>{{ $booking->created_at->format('Y-m-d H:i') }}</p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">تاريخ المناسبة:</label>
                                        <p>
                                            <i class="fas fa-calendar me-1"></i>
                                            {{ $booking->event_date ? $booking->event_date->format('Y-m-d') : 'غير محدد' }}
                                        </p>
                                    </div>
                                    @if($booking->event_time)
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">وقت المناسبة:</label>
                                            <p>
                                                <i class="fas fa-clock me-1"></i>
                                                {{ $booking->event_date ? $booking->event_date->format('Y-m-d') : 'غير محدد' }}
                                            </p>
                                        </div>
                                    @endif
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">المبلغ الإجمالي:</label>
                                        <p class="text-success fs-4 fw-bold">{{ number_format($booking->total_amount) }} ريال</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- تفاصيل الخدمة/الباقة -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-box me-2"></i>تفاصيل الخدمة
                            </h5>
                        </div>
                        <div class="card-body">
                            @if($booking->package)
                                <div class="d-flex align-items-center mb-3">
                                    <span class="badge bg-info me-2">
                                        <i class="fas fa-box me-1"></i>باقة
                                    </span>
                                    <h6 class="mb-0">{{ $booking->package->name }}</h6>
                                </div>
                                <p class="text-muted">{{ $booking->package->description }}</p>
                                <div class="row">
                                    <div class="col-md-6">
                                        <strong>السعر:</strong> {{ number_format($booking->package->price) }} ريال
                                    </div>
                                    @if($booking->package->duration)
                                        <div class="col-md-6">
                                            <strong>المدة:</strong> {{ $booking->package->duration }}
                                        </div>
                                    @endif
                                </div>
                            @endif
                            @if($booking->service)
                                <div class="d-flex align-items-center mb-3">
                                    <span class="badge bg-success me-2">
                                        <i class="fas fa-cogs me-1"></i>خدمة
                                    </span>
                                    <h6 class="mb-0">{{ $booking->service->name }}</h6>
                                </div>
                                <p class="text-muted">{{ $booking->service->description }}</p>
                                <div class="row">
                                    <div class="col-md-6">
                                        <strong>السعر:</strong> {{ number_format($booking->service->price) }} ريال
                                    </div>
                                    @if($booking->service->duration)
                                        <div class="col-md-6">
                                            <strong>المدة:</strong> {{ $booking->service->duration }}
                                        </div>
                                    @endif
                                </div>
                            @endif
                            @if($booking->quote && $booking->quote->items && $booking->quote->items->count() > 0)
                                <div class="d-flex align-items-center mb-3">
                                    <span class="badge bg-secondary me-2">
                                        <i class="fas fa-file-invoice-dollar me-1"></i>خدمات من عرض السعر
                                    </span>
                                    <h6 class="mb-0">الخدمات المطلوبة</h6>
                                </div>
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
                                                    <td>{{ Str::limit($item->service->description ?? $item->service_description, 80) }}</td>
                                                    <td>{{ $item->quantity }}</td>
                                                    <td>{{ number_format($item->price, 2) }} ريال</td>
                                                    <td>{{ number_format($item->subtotal, 2) }} ريال</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- ملاحظات إضافية -->
                    @if($booking->notes)
                        <div class="card shadow-sm">
                            <div class="card-header bg-secondary text-white">
                                <h5 class="mb-0">
                                    <i class="fas fa-sticky-note me-2"></i>ملاحظات إضافية
                                </h5>
                            </div>
                            <div class="card-body">
                                <p class="mb-0">{{ $booking->notes }}</p>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- معلومات العميل -->
                <div class="col-lg-4">
                    <div class="card shadow-sm">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-user me-2"></i>معلومات العميل
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="text-center mb-3">
                                <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                                    <i class="fas fa-user fa-2x text-muted"></i>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label fw-bold">الاسم:</label>
                                <p class="mb-0">{{ $booking->client_name }}</p>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label fw-bold">البريد الإلكتروني:</label>
                                <p class="mb-0">
                                    <a href="mailto:{{ $booking->client_email }}" class="text-decoration-none">
                                        <i class="fas fa-envelope me-1"></i>{{ $booking->client_email }}
                                    </a>
                                </p>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label fw-bold">رقم الهاتف:</label>
                                <p class="mb-0">
                                    <a href="tel:{{ $booking->client_phone }}" class="text-decoration-none">
                                        <i class="fas fa-phone me-1"></i>{{ $booking->client_phone }}
                                    </a>
                                </p>
                            </div>

                            @if($booking->event_location)
                                <div class="mb-3">
                                    <label class="form-label fw-bold">موقع المناسبة:</label>
                                    <p class="mb-0">
                                        <i class="fas fa-map-marker-alt me-1"></i>{{ $booking->event_location }}
                                    </p>
                                </div>
                            @endif

                            @if($booking->guests_count)
                                <div class="mb-3">
                                    <label class="form-label fw-bold">عدد الضيوف:</label>
                                    <p class="mb-0">
                                        <i class="fas fa-users me-1"></i>{{ number_format($booking->guests_count) }} ضيف
                                    </p>
                                </div>
                            @endif

                            @if($booking->special_requests)
                                <div class="mb-3">
                                    <label class="form-label fw-bold">طلبات خاصة:</label>
                                    <p class="mb-0 text-muted">{{ $booking->special_requests }}</p>
                                </div>
                            @endif

                            <div class="d-grid gap-2 mt-4">
                                <a href="mailto:{{ $booking->client_email }}" class="btn btn-outline-primary">
                                    <i class="fas fa-envelope me-1"></i>إرسال بريد إلكتروني
                                </a>
                                <a href="tel:{{ $booking->client_phone }}" class="btn btn-outline-success">
                                    <i class="fas fa-phone me-1"></i>اتصال
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Activity Logs --}}
    @php $logs = $booking->activityLogs()->latest()->limit(25)->get(); @endphp
    @include('admin.partials.activity-logs', ['logs' => $logs, 'title' => 'سجل نشاط الحجز'])
</div>
@endsection

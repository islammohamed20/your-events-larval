@extends('layouts.app')

@section('title', 'حجوزاتي - Your Events')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="text-white">حجوزاتي</h2>
                <a href="{{ route('booking.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>حجز جديد
                </a>
            </div>

            @if($bookings->count() > 0)
                <div class="row">
                    @foreach($bookings as $booking)
                        <div class="col-lg-6 col-md-12 mb-4">
                            <div class="card bg-dark border-primary">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0 text-white">
                                        <i class="fas fa-calendar me-2"></i>
                                        رقم الحجز: {{ $booking->booking_reference }}
                                    </h6>
                                    <span class="badge 
                                        @if($booking->status == 'pending') bg-warning
                                        @elseif($booking->status == 'confirmed') bg-success
                                        @elseif($booking->status == 'cancelled') bg-danger
                                        @else bg-secondary
                                        @endif">
                                        @if($booking->status == 'pending') في الانتظار
                                        @elseif($booking->status == 'confirmed') مؤكد
                                        @elseif($booking->status == 'cancelled') ملغي
                                        @else {{ $booking->status }}
                                        @endif
                                    </span>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p class="text-white mb-2">
                                                <i class="fas fa-user me-2 text-primary"></i>
                                                <strong>اسم العميل:</strong> {{ $booking->client_name }}
                                            </p>
                                            <p class="text-white mb-2">
                                                <i class="fas fa-envelope me-2 text-primary"></i>
                                                <strong>البريد الإلكتروني:</strong> {{ $booking->client_email }}
                                            </p>
                                            <p class="text-white mb-2">
                                                <i class="fas fa-phone me-2 text-primary"></i>
                                                <strong>الهاتف:</strong> {{ $booking->client_phone }}
                                            </p>
                                        </div>
                                        <div class="col-md-6">
                                            <p class="text-white mb-2">
                                                <i class="fas fa-calendar-alt me-2 text-primary"></i>
                                                <strong>تاريخ المناسبة:</strong> {{ $booking->event_date->format('Y-m-d') }}
                                            </p>
                                            <p class="text-white mb-2">
                                                <i class="fas fa-map-marker-alt me-2 text-primary"></i>
                                                <strong>المكان:</strong> {{ $booking->event_location }}
                                            </p>
                                            <p class="text-white mb-2">
                                                <i class="fas fa-users me-2 text-primary"></i>
                                                <strong>عدد الضيوف:</strong> {{ $booking->guests_count }}
                                            </p>
                                        </div>
                                    </div>
                                    
                                    @if($booking->package)
                                        <div class="mt-3 p-3 bg-secondary rounded">
                                            <p class="text-white mb-1">
                                                <i class="fas fa-box me-2 text-primary"></i>
                                                <strong>الباقة:</strong> {{ $booking->package->name }}
                                            </p>
                                        </div>
                                    @endif
                                    
                                    @if($booking->service)
                                        <div class="mt-3 p-3 bg-secondary rounded">
                                            <p class="text-white mb-1">
                                                <i class="fas fa-concierge-bell me-2 text-primary"></i>
                                                <strong>الخدمة:</strong> {{ $booking->service->name }}
                                            </p>
                                        </div>
                                    @endif
                                    
                                    @if($booking->special_requests)
                                        <div class="mt-3">
                                            <p class="text-white mb-1">
                                                <i class="fas fa-comment me-2 text-primary"></i>
                                                <strong>طلبات خاصة:</strong>
                                            </p>
                                            <p class="text-muted small">{{ $booking->special_requests }}</p>
                                        </div>
                                    @endif
                                    
                                    <div class="mt-3 pt-3 border-top border-secondary">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="text-white">
                                                <i class="fas fa-money-bill me-2 text-primary"></i>
                                                <strong>المبلغ الإجمالي:</strong>
                                            </span>
                                            <span class="h5 text-primary mb-0">{{ number_format($booking->total_amount, 2) }} ريال</span>
                                        </div>
                                        <small class="text-muted">
                                            <i class="fas fa-clock me-1"></i>
                                            تم الحجز في: {{ $booking->created_at->format('Y-m-d H:i') }}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-calendar-times fa-4x text-muted"></i>
                    </div>
                    <h4 class="text-white mb-3">لا توجد حجوزات</h4>
                    <p class="text-muted mb-4">لم تقم بأي حجوزات حتى الآن</p>
                    <a href="{{ route('booking.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>احجز الآن
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
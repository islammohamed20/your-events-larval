@extends('layouts.app')

@section('title', 'تمت عملية الحجز بنجاح') 'تم الحجز بنجاح - Your Events')

@section('content')
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="text-center mb-5" data-aos="fade-up">
                    <div class="mb-4">
                        <i class="fas fa-check-circle fa-5x text-success"></i>
                    </div>
                    <h1 class="display-5 fw-bold text-success mb-3">تم الحجز بنجاح!</h1>
                    <p class="lead text-muted">شكراً لك على الثقة بخدماتنا. سنتواصل معك قريباً لتأكيد التفاصيل.</p>
                </div>

                <div class="card shadow-lg" data-aos="fade-up" data-aos-delay="100">
                    <div class="card-body p-5">
                        <h3 class="mb-4 text-primary">
                            <i class="fas fa-receipt me-2"></i>تفاصيل الحجز
                        </h3>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <h6 class="text-muted">رقم الحجز</h6>
                                <strong>{{ $booking->booking_reference }}</strong>
                            </div>
                            <div class="col-md-6 mb-3">
                                <h6 class="text-muted">تاريخ الطلب</h6>
                                <strong>{{ $booking->created_at->format('d/m/Y H:i') }}</strong>
                            </div>
                            <div class="col-md-6 mb-3">
                                <h6 class="text-muted">اسم العميل</h6>
                                <strong>{{ $booking->client_name }}</strong>
                            </div>
                            <div class="col-md-6 mb-3">
                                <h6 class="text-muted">البريد الإلكتروني</h6>
                                <strong>{{ $booking->client_email }}</strong>
                            </div>
                            <div class="col-md-6 mb-3">
                                <h6 class="text-muted">رقم الهاتف</h6>
                                <strong>{{ $booking->client_phone }}</strong>
                            </div>
                            <div class="col-md-6 mb-3">
                                <h6 class="text-muted">تاريخ المناسبة</h6>
                                <strong>{{ \Carbon\Carbon::parse($booking->event_date)->format('d/m/Y') }}</strong>
                            </div>
                            <div class="col-md-6 mb-3">
                                <h6 class="text-muted">مكان المناسبة</h6>
                                <strong>{{ $booking->event_location }}</strong>
                            </div>
                            <div class="col-md-6 mb-3">
                                <h6 class="text-muted">عدد الضيوف</h6>
                                <strong>{{ number_format($booking->guests_count) }}</strong>
                            </div>
                            
                            @if($booking->package)
                                <div class="col-12 mb-3">
                                    <h6 class="text-muted">الباقة المحجوزة</h6>
                                    <strong>{{ $booking->package->name }}</strong>
                                </div>
                            @endif
                            
                            @if($booking->service)
                                <div class="col-12 mb-3">
                                    <h6 class="text-muted">الخدمة المحجوزة</h6>
                                    <strong>{{ $booking->service->name }}</strong>
                                </div>
                            @endif
                            
                            @if($booking->special_requests)
                                <div class="col-12 mb-3">
                                    <h6 class="text-muted">الطلبات الخاصة</h6>
                                    <p>{{ $booking->special_requests }}</p>
                                </div>
                            @endif
                            
                            @if($booking->total_amount > 0)
                                <div class="col-12 mb-3">
                                    <div class="alert alert-info">
                                        <h5 class="alert-heading">التكلفة المقدرة:</h5>
                                        <h4 class="mb-0 text-primary">{{ number_format($booking->total_amount) }} {{ __('common.currency') }}</h4>
                                        <small>* السعر النهائي سيتم تأكيده معك هاتفياً</small>
                                    </div>
                                </div>
                            @endif
                            
                            <div class="col-12">
                                <div class="alert alert-warning">
                                    <h6 class="alert-heading">
                                        <i class="fas fa-info-circle me-2"></i>حالة الحجز: قيد المراجعة
                                    </h6>
                                    <p class="mb-0">
                                        سيقوم فريقنا بمراجعة طلبك والتواصل معك خلال 24 ساعة لتأكيد الحجز ومناقشة التفاصيل.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Next Steps -->
                <div class="row mt-5" data-aos="fade-up" data-aos-delay="200">
                    <div class="col-md-4">
                        <div class="text-center mb-4">
                            <div class="mb-3">
                                <i class="fas fa-phone fa-2x text-primary"></i>
                            </div>
                            <h5>1. سنتواصل معك</h5>
                            <p class="text-muted small">سيتصل بك أحد أعضاء فريقنا خلال 24 ساعة</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-center mb-4">
                            <div class="mb-3">
                                <i class="fas fa-handshake fa-2x text-primary"></i>
                            </div>
                            <h5>2. تأكيد التفاصيل</h5>
                            <p class="text-muted small">سنناقش معك جميع تفاصيل المناسبة والمتطلبات</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-center mb-4">
                            <div class="mb-3">
                                <i class="fas fa-calendar-check fa-2x text-primary"></i>
                            </div>
                            <h5>3. بدء التنفيذ</h5>
                            <p class="text-muted small">نبدأ في التحضير لمناسبتك المميزة</p>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="text-center mt-4" data-aos="fade-up" data-aos-delay="300">
                    <div class="d-flex justify-content-center gap-3 flex-wrap">
                        <a href="{{ route('home') }}" class="btn btn-primary">
                            <i class="fas fa-home me-2"></i>العودة للرئيسية
                        </a>
                        @auth
                            <a href="{{ route('booking.my-bookings') }}" class="btn btn-outline-primary">
                                <i class="fas fa-list me-2"></i>حجوزاتي
                            </a>
                        @endauth
                        <a href="{{ route('contact') }}" class="btn btn-outline-primary">
                            <i class="fas fa-phone me-2"></i>تواصل معنا
                        </a>
                    </div>
                </div>

                <!-- Contact Information -->
                <div class="alert alert-light mt-4" data-aos="fade-up" data-aos-delay="400">
                    <h6 class="alert-heading">معلومات الاتصال:</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <p class="mb-1">
                                <i class="fas fa-phone me-2 text-primary"></i>
                                <strong>الهاتف:</strong>
                                <a href="tel:{{ preg_replace('/\s+/', '', \App\Models\Setting::get('contact_phone')) }}" class="text-decoration-none phone-ltr" dir="ltr">
                                    <span>{{ \App\Models\Setting::get('contact_phone') }}</span>
                                </a>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1">
                                <i class="fas fa-envelope me-2 text-primary"></i>
                                <strong>البريد:</strong>
                                <a href="mailto:{{ \App\Models\Setting::get('contact_email') }}" class="text-decoration-none">
                                    {{ \App\Models\Setting::get('contact_email') }}
                                </a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@extends('layouts.app')

@section('title', $service->name . ' - خدماتنا - Your Events')

@section('content')
<section class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <div class="mb-4" data-aos="fade-right">
                    @if($service->image)
                        <img src="{{ Storage::url($service->image) }}" class="img-fluid rounded" alt="{{ $service->name }}">
                    @else
                        <img src="https://images.unsplash.com/photo-1492684223066-81342ee5ff30?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" 
                             class="img-fluid rounded" alt="{{ $service->name }}">
                    @endif
                </div>
                
                <div data-aos="fade-up">
                    <h1 class="mb-4">{{ $service->name }}</h1>
                    
                    <!-- معلومات الخدمة -->
                    <div class="row mb-4">
                        @if($service->price)
                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-tag text-primary me-2"></i>
                                <div>
                                    <small class="text-muted d-block">السعر</small>
                                    <strong class="h5 mb-0">{{ number_format($service->price) }} ريال</strong>
                                </div>
                            </div>
                        </div>
                        @endif
                        
                        @if($service->duration)
                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-clock text-primary me-2"></i>
                                <div>
                                    <small class="text-muted d-block">المدة</small>
                                    <strong>{{ $service->duration }}</strong>
                                </div>
                            </div>
                        </div>
                        @endif
                        
                        @if($service->type)
                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-cogs text-primary me-2"></i>
                                <div>
                                    <small class="text-muted d-block">نوع الخدمة</small>
                                    <span class="badge bg-primary">{{ $service->type }}</span>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                    
                    <div class="mb-4">
                        <h5>وصف الخدمة</h5>
                        {!! nl2br(e($service->description)) !!}
                    </div>
                    
                    @if($service->features && count($service->features) > 0)
                    <div class="mb-4">
                        <h5>مميزات الخدمة</h5>
                        <ul class="list-unstyled">
                            @foreach($service->features as $feature)
                                <li class="mb-2">
                                    <i class="fas fa-check text-success me-2"></i>{{ $feature }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="card sticky-top" style="top: 100px;" data-aos="fade-left">
                    <div class="card-body">
                        <h5 class="card-title mb-4">احجز هذه الخدمة</h5>
                        
                        @if($service->price)
                        <div class="text-center mb-3">
                            <div class="h4 text-primary mb-0">{{ number_format($service->price) }} ريال</div>
                            @if($service->duration)
                                <small class="text-muted">لمدة {{ $service->duration }}</small>
                            @endif
                        </div>
                        @endif
                        
                        <p class="text-muted mb-4">
                            هل تريد الحصول على هذه الخدمة؟ احجز الآن واحصل على استشارة مجانية.
                        </p>
                        <a href="{{ route('booking.create', ['service_id' => $service->id]) }}" 
                           class="btn btn-primary btn-lg w-100 mb-3">
                            <i class="fas fa-calendar-check me-2"></i>احجز الآن
                        </a>
                        <a href="{{ route('contact') }}" class="btn btn-outline-primary w-100">
                            <i class="fas fa-phone me-2"></i>اتصل بنا
                        </a>
                    </div>
                </div>
                
                <div class="card mt-4" data-aos="fade-left" data-aos-delay="100">
                    <div class="card-body">
                        <h6 class="card-title">معلومات الاتصال</h6>
                        <ul class="list-unstyled mb-0">
                            <li class="mb-2">
                                <i class="fas fa-phone text-primary me-2"></i>
                                +966 50 123 4567
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-envelope text-primary me-2"></i>
                                info@yourevents.com
                            </li>
                            <li>
                                <i class="fas fa-clock text-primary me-2"></i>
                                السبت - الخميس: 9:00 ص - 6:00 م
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="text-center mt-5" data-aos="fade-up">
            <a href="{{ route('services.index') }}" class="btn btn-outline-primary">
                <i class="fas fa-arrow-right me-2"></i>العودة إلى الخدمات
            </a>
        </div>
    </div>
</section>
@endsection

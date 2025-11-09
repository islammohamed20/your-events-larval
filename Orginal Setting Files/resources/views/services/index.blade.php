@extends('layouts.app')

@section('title', 'خدماتنا - Your Events')

@section('content')
<!-- Page Header -->
<section class="hero-section" style="padding: 40px 0;">
    <div class="container">
        <div class="text-center">
            <h1 class="display-4 fw-bold mb-3" style="color: var(--primary-color);">خدماتنا</h1>
            <p class="lead" style="color: var(--text-color);">نقدم مجموعة شاملة من الخدمات لجعل مناسبتك مميزة ولا تُنسى</p>
        </div>
    </div>
</section>

<!-- Services Grid -->
<section class="py-4">
    <div class="container">
        <div class="row">
            @forelse($services as $service)
                <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
                    <div class="card h-100">
                        @if($service->image)
                            <img src="{{ Storage::url($service->image) }}" class="card-img-top" alt="{{ $service->name }}">
                        @else
                            <img src="https://images.unsplash.com/photo-1492684223066-81342ee5ff30?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80" 
                                 class="card-img-top" alt="{{ $service->name }}">
                        @endif
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h5 class="card-title mb-0">{{ $service->name }}</h5>
                                @if($service->type)
                                    <span class="badge bg-primary">{{ $service->type }}</span>
                                @endif
                            </div>
                            
                            @if($service->price)
                                <div class="mb-2">
                                    <span class="h6 text-primary">{{ number_format($service->price) }} ريال</span>
                                    @if($service->duration)
                                        <small class="text-muted"> - {{ $service->duration }}</small>
                                    @endif
                                </div>
                            @endif
                            
                            <p class="card-text flex-grow-1">{{ Str::limit($service->description, 100) }}</p>
                            <div class="mt-auto">
                                <a href="{{ route('booking.create', ['service_id' => $service->id]) }}" class="btn btn-primary me-2">
                                    احجز الآن
                                </a>
                                <a href="{{ route('services.show', $service->id) }}" class="btn btn-outline-primary">
                                    التفاصيل
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="text-center py-5">
                        <i class="fas fa-search fa-3x text-muted mb-3"></i>
                        <h4>لا توجد خدمات متاحة حالياً</h4>
                        <p class="text-muted">نعمل على إضافة المزيد من الخدمات قريباً</p>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</section>

<!-- Why Choose Us -->
<section class="py-4 bg-secondary-custom">
    <div class="container">
        <h2 class="section-title" data-aos="fade-up" style="margin-bottom: 2rem;">لماذا تختار خدماتنا؟</h2>
        <div class="row">
            <div class="col-lg-3 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="100">
                <div class="text-center">
                    <div class="mb-3">
                        <i class="fas fa-star fa-3x" style="color: var(--primary-color);"></i>
                    </div>
                    <h5>خدمة مميزة</h5>
                    <p class="text-muted">نقدم أعلى مستويات الخدمة والجودة</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="200">
                <div class="text-center">
                    <div class="mb-3">
                        <i class="fas fa-users fa-3x" style="color: var(--primary-color);"></i>
                    </div>
                    <h5>فريق خبير</h5>
                    <p class="text-muted">فريق متخصص من الخبراء في تنظيم المناسبات</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="300">
                <div class="text-center">
                    <div class="mb-3">
                        <i class="fas fa-clock fa-3x" style="color: var(--primary-color);"></i>
                    </div>
                    <h5>التزام بالمواعيد</h5>
                    <p class="text-muted">نلتزم بالمواعيد المحددة دون تأخير</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="400">
                <div class="text-center">
                    <div class="mb-3">
                        <i class="fas fa-heart fa-3x" style="color: var(--primary-color);"></i>
                    </div>
                    <h5>رضا العملاء</h5>
                    <p class="text-muted">نهدف لرضا عملائنا بنسبة 100%</p>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

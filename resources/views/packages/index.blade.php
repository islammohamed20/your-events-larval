@extends('layouts.app')

@section('title', 'الباقات - Your Events')

@section('content')
<!-- Page Header -->
<section class="hero-section" style="padding: 40px 0;">
    <div class="container">
        <div class="text-center">
            <h1 class="display-4 fw-bold mb-3" style="color: var(--primary-color);">باقاتنا المميزة</h1>
            <p class="lead" style="color: var(--text-color);">اختر الباقة التي تناسب مناسبتك واحتياجاتك</p>
        </div>
    </div>
</section>

<!-- Packages Grid -->
<section class="py-4">
    <div class="container">
        <div class="row">
            @forelse($packages as $package)
                <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
                    <div class="card h-100">
                        @if($package->image)
                            <img src="{{ Storage::url($package->image) }}" class="card-img-top" alt="{{ $package->name }}">
                        @else
                            <img src="https://images.unsplash.com/photo-1519167758481-83f550bb49b3?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80" 
                                 class="card-img-top" alt="{{ $package->name }}">
                        @endif
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title">{{ $package->name }}</h5>
                            <p class="card-text">{{ Str::limit($package->description, 150) }}</p>
                            
                            @if($package->features)
                                <h6 class="mt-3 mb-2">المميزات:</h6>
                                <ul class="list-unstyled mb-3">
                                    @foreach($package->features as $feature)
                                        <li class="mb-1">
                                            <i class="fas fa-check text-primary me-2"></i>{{ $feature }}
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                            
                            <div class="d-flex justify-content-between align-items-center mt-auto">
                                <span class="price-tag">{{ number_format($package->price) }} ر.س</span>
                                <div>
                                    <a href="{{ route('packages.show', $package->id) }}" class="btn btn-outline-primary btn-sm me-2">
                                        التفاصيل
                                    </a>
                                    <a href="{{ route('booking.create', ['package_id' => $package->id]) }}" class="btn btn-primary btn-sm">
                                        احجز الآن
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="text-center py-5">
                        <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                        <h4>لا توجد باقات متاحة حالياً</h4>
                        <p class="text-muted">نعمل على إضافة المزيد من الباقات قريباً</p>
                        <a href="{{ route('contact') }}" class="btn btn-primary mt-3">
                            تواصل معنا للحصول على عرض مخصص
                        </a>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</section>

<!-- Package Benefits -->
<section class="py-4 bg-secondary-custom">
    <div class="container">
        <h2 class="section-title" data-aos="fade-up" style="margin-bottom: 2rem;">مميزات باقاتنا</h2>
        <div class="row">
            <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="100">
                <div class="text-center">
                    <div class="mb-3">
                        <i class="fas fa-gem fa-3x" style="color: var(--primary-color);"></i>
                    </div>
                    <h5>جودة عالية</h5>
                    <p class="text-muted">نستخدم أفضل المواد والتجهيزات لضمان جودة عالية</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="200">
                <div class="text-center">
                    <div class="mb-3">
                        <i class="fas fa-palette fa-3x" style="color: var(--primary-color);"></i>
                    </div>
                    <h5>تصميم إبداعي</h5>
                    <p class="text-muted">فريق من المصممين المبدعين لإنشاء تصاميم فريدة</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="300">
                <div class="text-center">
                    <div class="mb-3">
                        <i class="fas fa-handshake fa-3x" style="color: var(--primary-color);"></i>
                    </div>
                    <h5>خدمة شاملة</h5>
                    <p class="text-muted">نوفر جميع الخدمات المطلوبة في مكان واحد</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="400">
                <div class="text-center">
                    <div class="mb-3">
                        <i class="fas fa-money-bill-wave fa-3x" style="color: var(--primary-color);"></i>
                    </div>
                    <h5>أسعار تنافسية</h5>
                    <p class="text-muted">أفضل الأسعار مع أعلى مستويات الجودة</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="500">
                <div class="text-center">
                    <div class="mb-3">
                        <i class="fas fa-headset fa-3x" style="color: var(--primary-color);"></i>
                    </div>
                    <h5>دعم مستمر</h5>
                    <p class="text-muted">فريق دعم متاح على مدار الساعة لخدمتكم</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="600">
                <div class="text-center">
                    <div class="mb-3">
                        <i class="fas fa-shield-alt fa-3x" style="color: var(--primary-color);"></i>
                    </div>
                    <h5>ضمان الجودة</h5>
                    <p class="text-muted">نضمن جودة العمل والالتزام بالمواصفات</p>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

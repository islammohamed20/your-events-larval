@extends('layouts.app')

@section('title', 'Your Events - حوّل مناسبتك العادية إلى لحظة استثنائية')

@section('content')
<!-- Hero Banner Section -->
<section class="hero-banner-section" style="background: linear-gradient(135deg, #1f144a 0%, #2d1a5e 50%, #3d2a7e 100%); position: relative; overflow: hidden; display: flex; align-items: center;">
    <!-- Background Image -->
    <div class="hero-bg-image" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background-image: url({{ asset('images/vr/VR_MAN.bmp') }}); background-size: 80%; background-position: center top; background-repeat: no-repeat; opacity: 1;"></div>
    
    <!-- Overlay -->
    <div class="hero-overlay" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: linear-gradient(135deg, rgba(31, 20, 74, 0.5) 0%, rgba(45, 26, 94, 0.4) 50%, rgba(61, 42, 126, 0.3) 100%);"></div>
    
    <!-- Content -->
    <div class="container position-relative" style="z-index: 10;">
        <div class="row align-items-center">
            <!-- Left side - VR Man Image (will be part of background) -->
            <div class="col-lg-6">
                <!-- This space is for the VR man image which is now part of the background -->
            </div>
            
            <!-- Right side - Text Content -->
            <div class="col-lg-6" data-aos="fade-right">
                <div class="hero-content text-end" style="padding: 60px 40px; direction: rtl;">
                    <!-- Main Title -->
                    <h1 class="hero-title arabic-text text-white mb-4" style="font-size: 3.5rem; font-weight: 800; line-height: 1.2; text-shadow: 2px 2px 8px rgba(0,0,0,0.7); margin-bottom: 30px;">
                        حوّل مناسبتك العادية إلى لحظة استثنائية
                    </h1>
                    
                    <!-- Subtitle -->
                    <p class="hero-subtitle arabic-text text-white mb-5" style="font-size: 1.4rem; line-height: 1.8; color: rgba(255,255,255,0.9) !important; text-shadow: 1px 1px 4px rgba(0,0,0,0.6); margin-bottom: 40px; font-weight: 400;">
                        بنقدّم لك كل اللي تحتاجه لحفلة كاملة ومتكاملة، من الأكل والمشروبات، للديكور والتصوير، والهدايا والتوصيل، كل ده بتنسيق احترافي يخلي يومك أحلى مما تتخيل.
                    </p>
                    
                    <!-- CTA Button -->
                    <div class="hero-cta">
                        <a href="{{ route('booking.create') }}" class="btn btn-cta arabic-text" style="background: #2dbcae; color: white; border: none; border-radius: 25px; padding: 18px 45px; font-size: 1.3rem; font-weight: 600; text-decoration: none; display: inline-block; transition: all 0.3s ease; box-shadow: 0 8px 25px rgba(45, 188, 174, 0.4); text-shadow: none;">
                            <i class="fas fa-calendar-check me-2"></i>احجز الآن
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Decorative Elements -->
    <div class="floating-elements">
        <div style="position: absolute; top: 15%; right: 10%; width: 60px; height: 60px; background: linear-gradient(45deg, #2dbcae, #3cc7b8); border-radius: 50%; animation: float 4s ease-in-out infinite; opacity: 0.7;"></div>
        <div style="position: absolute; bottom: 25%; left: 8%; width: 40px; height: 40px; background: linear-gradient(45deg, #ef4870, #f56b8a); border-radius: 50%; animation: float 6s ease-in-out infinite reverse; opacity: 0.6;"></div>
        <div style="position: absolute; top: 60%; right: 5%; width: 30px; height: 30px; background: linear-gradient(45deg, #f0c71d, #f5d347); border-radius: 50%; animation: float 5s ease-in-out infinite; opacity: 0.5;"></div>
    </div>
</section>

<!-- Services Section -->
<section class="py-4" id="services" style="background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 50%, #e9ecef 100%); position: relative;">
    <div class="container">
        <div class="text-center mb-4">
            <h2 class="section-title arabic-text" data-aos="fade-up" style="color: var(--primary-color); font-size: 2.5rem; margin-bottom: 20px; text-shadow: 2px 2px 4px rgba(0,0,0,0.1);">كل اللي تحتاجه لحفلتك، بنوفرك إياه من مكان واحد</h2>
            <p class="lead arabic-text" style="color: #444444; font-size: 1.2rem; font-weight: 500;">من تنظيم وتنسيق، لمعايدة وتجهيز وصولاً - دورنا إنك بطريقك</p>
        </div>
        
        <!-- Services Grid -->
        <div class="row justify-content-center mb-5">
            <div class="col-lg-3 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="100">
                <div class="service-card text-center" style="background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%); border-radius: 20px; padding: 30px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); border: 1px solid #e9ecef; transition: all 0.3s ease;">
                    <div class="service-icon" style="background: linear-gradient(135deg, var(--accent-color), var(--secondary-color)); width: 80px; height: 80px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                        <i class="fas fa-users" style="font-size: 2rem; color: white;"></i>
                    </div>
                    <h5 class="arabic-text" style="color: var(--primary-color); margin: 20px 0 15px; font-weight: 700;">حاضرين لوصل الفرح</h5>
                    <p class="arabic-text" style="color: #555555; font-size: 1rem; line-height: 1.6;">نساعدك في كل خطوة</p>
                    <a href="#" class="btn btn-secondary" style="margin-top: 15px; border-radius: 25px; padding: 10px 25px;">اطلب الآن</a>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="200">
                <div class="service-card text-center" style="background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%); border-radius: 20px; padding: 30px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); border: 1px solid #e9ecef; transition: all 0.3s ease;">
                    <div class="service-icon" style="background: linear-gradient(135deg, var(--gold-color), var(--accent-color)); width: 80px; height: 80px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                        <i class="fas fa-palette" style="font-size: 2rem; color: white;"></i>
                    </div>
                    <h5 class="arabic-text" style="color: var(--primary-color); margin: 20px 0 15px; font-weight: 700;">هديتنا لتصميم الفلل</h5>
                    <p class="arabic-text" style="color: #555555; font-size: 1rem; line-height: 1.6;">تصاميم عصرية ومبتكرة</p>
                    <a href="#" class="btn btn-secondary" style="margin-top: 15px; border-radius: 25px; padding: 10px 25px;">اطلب الآن</a>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="300">
                <div class="service-card text-center" style="background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%); border-radius: 20px; padding: 30px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); border: 1px solid #e9ecef; transition: all 0.3s ease;">
                    <div class="service-icon" style="background: linear-gradient(135deg, var(--secondary-color), var(--purple-light)); width: 80px; height: 80px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                        <i class="fas fa-camera" style="font-size: 2rem; color: white;"></i>
                    </div>
                    <h5 class="arabic-text" style="color: var(--primary-color); margin: 20px 0 15px; font-weight: 700;">ضمن أول كليك للفوتوغرافيا</h5>
                    <p class="arabic-text" style="color: #555555; font-size: 1rem; line-height: 1.6;">لحظات لا تُنسى بأعلى جودة</p>
                    <a href="#" class="btn btn-secondary" style="margin-top: 15px; border-radius: 25px; padding: 10px 25px;">اطلب الآن</a>
                </div>
            </div>
        </div>
        
        @forelse($services as $service)
            <div class="col-lg-4 col-md-6 mb-4" data-aos="zoom-in" data-aos-delay="{{ $loop->index * 100 }}" style="display: none;">
                <div class="card h-100">
                    @if($service->image)
                        <img src="{{ Storage::url($service->image) }}" class="card-img-top" alt="{{ $service->name }}">
                    @else
                        <img src="{{ asset('images/service-default.svg') }}" 
                             class="card-img-top" alt="{{ $service->name }}">
                    @endif
                    <div class="card-body">
                        <h5 class="card-title">{{ $service->name }}</h5>
                        <p class="card-text">{{ Str::limit($service->description, 100) }}</p>
                        <a href="{{ route('services.show', $service->id) }}" class="btn btn-primary">
                            اعرف المزيد <i class="fas fa-arrow-left ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        @empty
        @endforelse
        
        <div class="text-center mt-3">
            <a href="{{ route('services.index') }}" class="btn btn-outline-primary btn-lg" style="border: 2px solid var(--primary-color); color: var(--primary-color);">
                عرض جميع الخدمات
            </a>
        </div>
    </div>
</section>

<style>
.service-card {
    background: linear-gradient(145deg, #ffffff, #f8f9fa);
    border-radius: 20px;
    padding: 30px 20px;
    border: 1px solid rgba(45, 188, 174, 0.2);
    transition: all 0.3s ease;
    height: 100%;
    box-shadow: 0 5px 15px rgba(31, 20, 74, 0.1);
}

.service-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 20px 40px rgba(45, 188, 174, 0.2);
    border-color: var(--secondary-color);
}

.service-icon {
    width: 80px;
    height: 80px;
    background: linear-gradient(45deg, var(--secondary-color), #3cc7b8);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 20px;
    box-shadow: 0 10px 30px rgba(45, 188, 174, 0.3);
}
</style>

<!-- Packages Section -->
<section class="py-4 bg-secondary-custom" id="packages">
    <div class="container">
        <h2 class="section-title arabic-text" data-aos="fade-up" style="color: var(--primary-color); text-align: center; margin-bottom: 2rem;">باقاتنا المميزة</h2>
        <div class="row">
            @forelse($packages as $package)
                <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
                    <div class="card h-100">
                        @if($package->image)
                            <img src="{{ Storage::url($package->image) }}" class="card-img-top" alt="{{ $package->name }}">
                        @else
                            <img src="{{ asset('images/event-package.svg') }}" 
                                 class="card-img-top" alt="{{ $package->name }}">
                        @endif
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title">{{ $package->name }}</h5>
                            <p class="card-text flex-grow-1">{{ Str::limit($package->description, 120) }}</p>
                            @if($package->features)
                                <ul class="list-unstyled mb-3">
                                    @foreach(array_slice($package->features, 0, 3) as $feature)
                                        <li><i class="fas fa-check text-primary me-2"></i>{{ $feature }}</li>
                                    @endforeach
                                </ul>
                            @endif
                            <div class="d-flex justify-content-between align-items-center mt-auto">
                                <span class="price-tag">{{ number_format($package->price) }} ر.س</span>
                                <a href="{{ route('booking.create', ['package_id' => $package->id]) }}" class="btn btn-gold">
                                    احجز الآن
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center">
                    <p class="text-muted">لا توجد باقات متاحة حالياً</p>
                </div>
            @endforelse
        </div>
        <div class="text-center mt-3">
            <a href="{{ route('packages.index') }}" class="btn btn-outline-primary btn-lg" style="border: 2px solid var(--accent-color); color: var(--accent-color);">
                عرض جميع الباقات
            </a>
        </div>
    </div>
</section>

<!-- Gallery Section -->
@if($gallery->count() > 0)
<section class="py-4" id="gallery">
    <div class="container">
        <h2 class="section-title arabic-text" data-aos="fade-up" style="color: var(--primary-color); text-align: center; margin-bottom: 2rem;">معرض أعمالنا</h2>
        <div class="row">
            @foreach($gallery as $item)
                <div class="col-lg-3 col-md-6 mb-4" data-aos="zoom-in" data-aos-delay="{{ $loop->index * 50 }}">
                    <div class="gallery-item">
                        @if($item->type === 'image')
                            <img src="{{ Storage::url($item->path) }}" class="img-fluid w-100" 
                                 alt="{{ $item->title }}" style="height: 250px; object-fit: cover;">
                        @else
                            <video class="img-fluid w-100" style="height: 250px; object-fit: cover;" muted>
                                <source src="{{ Storage::url($item->path) }}" type="video/mp4">
                            </video>
                        @endif
                        <div class="gallery-overlay">
                            <i class="fas fa-{{ $item->type === 'image' ? 'image' : 'play' }}"></i>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="text-center mt-3">
            <a href="{{ route('gallery.index') }}" class="btn btn-outline-primary btn-lg" style="border: 2px solid var(--secondary-color); color: var(--secondary-color);">
                عرض المعرض الكامل
            </a>
        </div>
    </div>
</section>
@endif

<!-- Reviews Section -->
@if($reviews->count() > 0)
<section class="py-4 bg-secondary-custom" id="reviews">
    <div class="container">
        <h2 class="section-title arabic-text" data-aos="fade-up" style="color: var(--primary-color); text-align: center; margin-bottom: 2rem;">آراء عملائنا</h2>
        <div class="row">
            @foreach($reviews as $review)
                <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <div class="mb-3">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fas fa-star {{ $i <= $review->rating ? 'text-warning' : 'text-muted' }}"></i>
                                @endfor
                            </div>
                            <p class="card-text arabic-text">"{{ $review->comment }}"</p>
                            <h6 class="card-title arabic-text" style="color: var(--accent-color);">{{ $review->client_name }}</h6>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- VR Experience Section -->
<section class="py-4" style="background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 50%, #e9ecef 100%); position: relative; overflow: hidden;">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6" data-aos="fade-right">
                <div class="vr-person-container" style="position: relative;">
                    <img src="https://images.unsplash.com/photo-1622979135225-d2ba269cf1ac?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80" 
                         alt="VR Experience" class="img-fluid" 
                         style="border-radius: 20px; box-shadow: 0 20px 60px rgba(45, 188, 174, 0.3); border: 3px solid rgba(45, 188, 174, 0.2);">
                    <div class="floating-vr-elements">
                        <div style="position: absolute; top: 15%; right: 10%; width: 50px; height: 50px; background: linear-gradient(45deg, #2dbcae, #3cc7b8); border-radius: 15px; animation: float 5s ease-in-out infinite; display: flex; align-items: center; justify-content: center; color: white; font-size: 20px; box-shadow: 0 10px 20px rgba(45, 188, 174, 0.4);">🎮</div>
                        <div style="position: absolute; bottom: 25%; left: 15%; width: 40px; height: 40px; background: linear-gradient(45deg, #ef4870, #f56b8a); border-radius: 50%; animation: float 7s ease-in-out infinite reverse; box-shadow: 0 10px 20px rgba(239, 72, 112, 0.4);"></div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6" data-aos="fade-left">
                <h2 class="mb-4 arabic-text" style="color: var(--primary-color); font-size: 2.5rem; font-weight: 700; text-shadow: 2px 2px 4px rgba(0,0,0,0.1);">كل تفاصيل الفرح تجمعها لك في مكان واحد</h2>
                <p class="mb-4 arabic-text" style="color: #444444; font-size: 1.2rem; line-height: 1.8; font-weight: 500;">
                    نظام مناسبتك المتكامل، الفاتورة، مرافقة وحماية لحظات نجاح بطريقك
                </p>
                <div class="d-flex gap-3 mb-4">
                    <a href="{{ route('booking.create') }}" class="btn btn-gold" style="box-shadow: 0 8px 25px rgba(240, 199, 29, 0.4); border-radius: 25px; padding: 15px 30px;">
                        <i class="fas fa-calendar-check me-2"></i>احجز الآن
                    </a>
                    <a href="#" class="btn btn-outline-primary" style="border: 2px solid var(--primary-color); color: var(--primary-color); border-radius: 25px; padding: 15px 25px; background: rgba(31, 20, 74, 0.1); transition: all 0.3s ease;">
                        <i class="fas fa-info-circle me-2"></i>المزيد
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Enhanced CTA Section -->
<section class="py-4" style="background: linear-gradient(135deg, var(--primary-color) 0%, var(--purple-light) 50%, var(--accent-color) 100%); position: relative;">
    <div class="container">
        <div class="row align-items-center h-100">
            <!-- Right side - Text Content -->
            <div class="col-lg-6" data-aos="fade-right">
                <div class="vr-woman-container" style="position: relative; text-align: center;">
                    <img src="{{ asset('images/vr/VR_WONEM.png') }}" 
                         alt="امرأة ترتدي نظارة الواقع الافتراضي" 
                         class="img-fluid" 
                         style="max-height: 400px; width: auto; filter: drop-shadow(0 10px 30px rgba(0,0,0,0.3));">
                </div>
            </div>
            
            <!-- Left side - VR Woman Image -->
            <div class="col-lg-6" data-aos="fade-left">
                <div class="cta-content text-end" style="padding: 40px; direction: rtl;">
                    <!-- Main Title -->
                    <h2 class="mb-4 arabic-text" style="font-size: 3rem; font-weight: 800; line-height: 1.3; text-shadow: 2px 2px 8px rgba(0,0,0,0.8); color: var(--gold-color);">
                        عشان لحظات ما تتعوض
                    </h2>
                    
                    <!-- Subtitle -->
                    <p class="mb-5 arabic-text" style="font-size: 1.3rem; line-height: 1.7; text-shadow: 1px 1px 4px rgba(0,0,0,0.6); font-weight: 400; color: #f8f9fa;">
                        تواصل معنا الآن واحصل على استشارة مجانية لتنظيم مناسبتك القادمة مع أحدث تقنيات الواقع الافتراضي
                    </p>
                    
                    <!-- CTA Buttons -->
                    <div class="cta-buttons d-flex gap-3 justify-content-end">
                        <a href="{{ route('booking.create') }}" class="btn btn-gold" style="border-radius: 25px; padding: 15px 35px; font-size: 1.2rem; font-weight: 600; box-shadow: 0 8px 25px rgba(240, 199, 29, 0.4);">
                            <i class="fas fa-calendar-check me-2"></i>احجز الآن
                        </a>
                        <a href="{{ route('contact') }}" class="btn btn-outline-light" style="border: 2px solid var(--gold-color); color: var(--gold-color); border-radius: 25px; padding: 15px 30px; font-size: 1.1rem;">
                            <i class="fas fa-phone me-2"></i>تواصل معنا
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Navigation arrows -->
        <div class="navigation-arrows" style="position: absolute; bottom: 20px; right: 20px;">
            <button class="btn btn-outline-light me-2" style="border-radius: 50%; width: 50px; height: 50px; border: 2px solid rgba(255,255,255,0.3);">
                <i class="fas fa-chevron-left"></i>
            </button>
            <button class="btn btn-outline-light" style="border-radius: 50%; width: 50px; height: 50px; border: 2px solid rgba(255,255,255,0.3);">
                <i class="fas fa-chevron-right"></i>
            </button>
        </div>
    </div>
</section>
@endsection

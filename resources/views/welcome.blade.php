@extends('layouts.app')

@section('title', 'Your Events - حوّل مناسبتك العادية إلى لحظة استثنائية')

@push('styles')
<style>
    /* Hero Carousel Advanced Transitions */
    .hero-carousel {
        position: relative;
        overflow: hidden;
    }
    
    /* ========== BASE CAROUSEL ========== */
    .hero-carousel .carousel-inner {
        position: relative;
        width: 100%;
        overflow: hidden;
    }
    
    .hero-carousel .carousel-item {
        position: relative;
        display: none;
        float: left;
        width: 100%;
        margin-right: -100%;
        backface-visibility: hidden;
    }
    
    .hero-carousel .carousel-item.active {
        display: block;
    }
    
    /* ========== FADE EFFECT ========== */
    .carousel-fade .carousel-item {
        position: absolute !important;
        top: 0;
        left: 0;
        opacity: 0;
        transition: opacity 1s ease-in-out;
        z-index: 1;
    }
    
    .carousel-fade .carousel-item.active {
        position: relative !important;
        opacity: 1;
        z-index: 2;
    }
    
    .carousel-fade .carousel-item-next.carousel-item-start,
    .carousel-fade .carousel-item-prev.carousel-item-end {
        z-index: 2;
        opacity: 1;
    }
    
    .carousel-fade .active.carousel-item-start,
    .carousel-fade .active.carousel-item-end {
        z-index: 1;
        opacity: 0;
    }
    
    /* ========== SLIDE EFFECT (Bootstrap Default) ========== */
    .carousel-slide .carousel-item {
        transition: transform 0.8s ease-in-out;
    }
    
    /* ========== ZOOM EFFECT ========== */
    .carousel-zoom .carousel-item {
        position: absolute !important;
        top: 0;
        left: 0;
        opacity: 0;
        transform: scale(0.7);
        transition: all 1s cubic-bezier(0.4, 0, 0.2, 1);
        z-index: 1;
    }
    
    .carousel-zoom .carousel-item.active {
        position: relative !important;
        opacity: 1;
        transform: scale(1);
        z-index: 2;
    }
    
    .carousel-zoom .carousel-item-next.carousel-item-start,
    .carousel-zoom .carousel-item-prev.carousel-item-end {
        z-index: 2;
        opacity: 1;
        transform: scale(1);
    }
    
    .carousel-zoom .active.carousel-item-start,
    .carousel-zoom .active.carousel-item-end {
        z-index: 1;
        opacity: 0;
        transform: scale(1.3);
    }
    
    /* ========== FLIP EFFECT ========== */
    .carousel-flip {
        perspective: 2000px;
    }
    
    .carousel-flip .carousel-item {
        position: absolute !important;
        top: 0;
        left: 0;
        opacity: 0;
        transform: rotateY(90deg);
        transition: all 1s cubic-bezier(0.4, 0, 0.2, 1);
        transform-origin: center;
        transform-style: preserve-3d;
        z-index: 1;
    }
    
    .carousel-flip .carousel-item.active {
        position: relative !important;
        opacity: 1;
        transform: rotateY(0deg);
        z-index: 2;
    }
    
    .carousel-flip .carousel-item-next.carousel-item-start,
    .carousel-flip .carousel-item-prev.carousel-item-end {
        z-index: 2;
        opacity: 1;
        transform: rotateY(0deg);
    }
    
    .carousel-flip .active.carousel-item-start,
    .carousel-flip .active.carousel-item-end {
        z-index: 1;
        opacity: 0;
        transform: rotateY(-90deg);
    }
    
    /* ========== CONTENT ANIMATIONS ========== */
    .hero-slide-wrapper .container > .row > div {
        transform: translateY(30px);
        opacity: 0;
        transition: all 0.8s ease-out 0.3s;
    }
    
    .carousel-item.active .hero-slide-wrapper .container > .row > div {
        transform: translateY(0);
        opacity: 1;
    }
    
    /* Title Animation */
    .carousel-item:not(.active) .hero-slide-title {
        opacity: 0;
        transform: translateY(-30px);
    }
    
    .carousel-item.active .hero-slide-title {
        animation: slideInDown 0.8s ease-out 0.4s both;
    }
    
    .carousel-item:not(.active) .hero-slide-subtitle {
        opacity: 0;
        transform: translateY(30px);
    }
    
    .carousel-item.active .hero-slide-subtitle {
        animation: slideInUp 0.8s ease-out 0.6s both;
    }
    
    .carousel-item:not(.active) .hero-slide-description {
        opacity: 0;
    }
    
    .carousel-item.active .hero-slide-description {
        animation: fadeIn 0.8s ease-out 0.8s both;
    }
    
    .carousel-item:not(.active) .btn {
        opacity: 0;
        transform: scale(0.5);
    }
    
    .carousel-item.active .btn {
        animation: bounceIn 0.8s ease-out 1s both;
    }
    
    @keyframes slideInDown {
        from {
            transform: translate3d(0, -50px, 0);
            opacity: 0;
        }
        to {
            transform: translate3d(0, 0, 0);
            opacity: 1;
        }
    }
    
    @keyframes slideInUp {
        from {
            transform: translate3d(0, 50px, 0);
            opacity: 0;
        }
        to {
            transform: translate3d(0, 0, 0);
            opacity: 1;
        }
    }
    
    @keyframes fadeIn {
        from {
            opacity: 0;
        }
        to {
            opacity: 1;
        }
    }
    
    @keyframes bounceIn {
        0% {
            transform: scale(0.5);
            opacity: 0;
        }
        60% {
            transform: scale(1.1);
            opacity: 1;
        }
        100% {
            transform: scale(1);
        }
    }
    
    /* ========== ENHANCED CONTROLS ========== */
    .carousel-control-prev,
    .carousel-control-next {
        width: 60px;
        height: 60px;
        top: 50%;
        transform: translateY(-50%);
        background: rgba(66, 52, 123, 0.8);
        border-radius: 50%;
        opacity: 0;
        transition: all 0.3s ease;
        backdrop-filter: blur(10px);
    }
    
    .hero-carousel:hover .carousel-control-prev,
    .hero-carousel:hover .carousel-control-next {
        opacity: 1;
    }
    
    .carousel-control-prev:hover,
    .carousel-control-next:hover {
        background: rgba(66, 52, 123, 1);
        transform: translateY(-50%) scale(1.1);
        box-shadow: 0 5px 20px rgba(66, 52, 123, 0.5);
    }
    
    .carousel-control-prev {
        left: 30px;
    }
    
    .carousel-control-next {
        right: 30px;
    }
    
    .carousel-control-prev-icon,
    .carousel-control-next-icon {
        width: 30px;
        height: 30px;
    }
    
    /* ========== ENHANCED INDICATORS ========== */
    .carousel-indicators {
        bottom: 30px;
        margin-bottom: 0;
        z-index: 2;
    }
    
    .carousel-indicators [data-bs-target] {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        margin: 0 8px;
        background-color: rgba(255, 255, 255, 0.5);
        border: 2px solid transparent;
        transition: all 0.3s ease;
        cursor: pointer;
    }
    
    .carousel-indicators .active {
        width: 40px;
        border-radius: 6px;
        background-color: #fff;
        border-color: rgba(66, 52, 123, 0.5);
    }
    
    .carousel-indicators [data-bs-target]:hover {
        background-color: rgba(255, 255, 255, 0.8);
        transform: scale(1.2);
    }
    
    /* ========== IMAGE ZOOM EFFECT ========== */
    .hero-slide-wrapper {
        transition: transform 8s ease-out;
        will-change: transform;
    }
    
    .carousel-item.active .hero-slide-wrapper {
        transform: scale(1.05);
    }
    
    /* ========== MOBILE RESPONSIVE ========== */
    @media (max-width: 768px) {
        .carousel-control-prev,
        .carousel-control-next {
            width: 45px;
            height: 45px;
            opacity: 0.7;
        }
        
        .carousel-control-prev {
            left: 15px;
        }
        
        .carousel-control-next {
            right: 15px;
        }
        
        .carousel-indicators {
            bottom: 15px;
        }
        
        .carousel-indicators [data-bs-target] {
            width: 8px;
            height: 8px;
            margin: 0 5px;
        }
        
        .carousel-indicators .active {
            width: 24px;
        }
    }
    
    /* ========== LOADING STATE ========== */
    .hero-carousel.loading .carousel-item {
        opacity: 0;
    }
    
    /* ========== PROGRESS BAR (Optional) ========== */
    .carousel-progress {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: rgba(255, 255, 255, 0.3);
        z-index: 3;
    }
    
    .carousel-progress-bar {
        height: 100%;
        background: linear-gradient(90deg, #42347b, #EF4870);
        width: 0%;
        transition: width linear;
    }
</style>
@endpush

@section('content')
<!-- Hero Slider Section -->
@if($heroSlides->count() > 0)
@php
    $firstSlide = $heroSlides->first();
    $transitionEffect = $firstSlide->transition_effect ?? 'fade';
    $intervalTime = $firstSlide->duration ?? 6000;
@endphp
<div id="heroCarousel" 
     class="carousel slide hero-carousel" 
     data-bs-ride="carousel" 
     data-bs-interval="{{ $intervalTime }}" 
     data-bs-pause="hover"
     data-transition-effect="{{ $transitionEffect }}">
    <!-- Indicators -->
    @if($heroSlides->count() > 1)
    <div class="carousel-indicators">
        @foreach($heroSlides as $index => $slide)
        <button type="button" 
                data-bs-target="#heroCarousel" 
                data-bs-slide-to="{{ $index }}" 
                class="{{ $index === 0 ? 'active' : '' }}" 
                aria-current="{{ $index === 0 ? 'true' : 'false' }}" 
                aria-label="Slide {{ $index + 1 }}"></button>
        @endforeach
    </div>
    @endif

    <!-- Slides -->
    <div class="carousel-inner">
        @foreach($heroSlides as $index => $slide)
        <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
            <div class="hero-slide-wrapper" style="background-image: linear-gradient(rgba(66, 52, 123, 0.7), rgba(66, 52, 123, 0.7)), url('{{ Storage::url($slide->image) }}');">
                <div class="container">
                    <div class="row align-items-center min-vh-75">
                        <div class="col-lg-8 mx-auto text-center" data-aos="fade-up">
                            <h1 class="hero-slide-title arabic-text mb-3">{{ $slide->title }}</h1>
                            @if($slide->subtitle)
                            <h2 class="hero-slide-subtitle arabic-text mb-4">{{ $slide->subtitle }}</h2>
                            @endif
                            @if($slide->description)
                            <p class="hero-slide-description arabic-text mb-4">{{ $slide->description }}</p>
                            @endif
                            @if($slide->button_text && $slide->button_link)
                            <a href="{{ $slide->button_link }}" 
                               class="btn btn-{{ $slide->button_style === 'primary' ? 'cta' : ($slide->button_style === 'accent' ? 'accent' : 'outline-light') }} btn-lg arabic-text">
                                {{ $slide->button_text }}
                                <i class="fas fa-arrow-left ms-2"></i>
                            </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Controls -->
    @if($heroSlides->count() > 1)
    <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Previous</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Next</span>
    </button>
    
    <!-- Progress Bar (Optional) -->
    <div class="carousel-progress">
        <div class="carousel-progress-bar"></div>
    </div>
    @endif
</div>
@endif

<!-- About: وش سالفة Your Events -->
<section class="about-your-events-section py-5 py-md-6" style="background: linear-gradient(180deg, #ffffff 0%, #f9fbfd 50%, #ffffff 100%);">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10 col-xl-9">
                <div class="card border-0 shadow-sm" style="border-radius: 18px; overflow: hidden;" data-aos="fade-up">
                    <div class="card-body p-4 p-md-5">
                        <div class="d-flex align-items-center mb-3">
                            <span class="arabic-text" style="display: inline-block; background: rgba(45,188,174,0.12); border: 1px solid rgba(45,188,174,0.35); color: var(--secondary-color); padding: 8px 14px; border-radius: 999px; font-weight: 700;">
                                وش سالفة Your Events
                            </span>
                        </div>
                        <h2 class="arabic-text fw-bold mb-3" style="color:#1a202c; font-size: clamp(1.6rem, 2.5vw, 2.2rem);">
                            سالفتنا بدت من فكرة بسيطة…
                        </h2>
                        <p class="arabic-text lead text-muted mb-3" style="line-height: 1.9;">
                            ليه تجهيز الفعاليات يكون متعب، والخيارات متفرّقة، والتجربة معقدة؟
                        </p>
                        <p class="arabic-text mb-3" style="color:#2d3748; line-height: 1.9; font-size: 1.05rem;">
                            من هنا، طلعت Your Events — منصّة سعودية جمّعنا فيها كل خدمات تجهيز الفعاليات في مكان واحد.
                        </p>
                        <p class="arabic-text mb-3" style="color:#2d3748; line-height: 1.9; font-size: 1.05rem;">
                            تختار، تحدد اللي تحتاجه، وتشوف الأسعار فورًا — تقدر تطلب عرض سعر أو تدفع مباشرة، وكل التفاصيل توصلك على طول في إيميلك.
                        </p>
                        <p class="arabic-text mb-3" style="color:#2d3748; line-height: 1.9; font-size: 1.05rem;">
                            لا وسطاء، لا انتظار، ولا مكالمات ما تخلص. كل شي واضح، وسلس، وسريع.
                        </p>
                        <p class="arabic-text mb-0" style="color:#1a202c; line-height: 1.9; font-weight: 600;">
                            لأننا نؤمن إن فعّاليتك ما تبدأ وقت الحدث… فعّاليتك تبدأ من لحظة الطلب.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

 

 

<!-- Categories Section -->
@php
    $categoriesSection = $sections->firstWhere('section_key', 'categories');
@endphp
@if($categoriesSection && $categoriesSection->is_active)
@php
    $categoriesBgStyle = $categoriesSection->getBackgroundStyle() ?: 'background: linear-gradient(180deg, #f8f9fa 0%, #ffffff 50%, #f8f9fa 100%);';
@endphp
<section class="categories-showcase py-5 py-md-5" id="home-categories" data-bg-style="{{ $categoriesBgStyle }}">
    <div class="container position-relative px-3 px-md-3">
        <!-- Floating Shapes Background -->
        <div class="floating-shapes" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; overflow: hidden; pointer-events: none; z-index: 1;">
            <div style="position: absolute; top: 10%; left: 5%; width: 80px; height: 80px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 20px; opacity: 0.08; transform: rotate(15deg); animation: float 6s ease-in-out infinite;"></div>
            <div style="position: absolute; top: 60%; right: 8%; width: 100px; height: 100px; background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); border-radius: 50%; opacity: 0.08; animation: float 8s ease-in-out infinite reverse;"></div>
            <div style="position: absolute; bottom: 20%; left: 10%; width: 60px; height: 60px; background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); border-radius: 15px; opacity: 0.08; transform: rotate(-20deg); animation: float 7s ease-in-out infinite;"></div>
        </div>

        <!-- Section Header -->
        <div class="text-center mb-5 position-relative" style="z-index: 2;" data-aos="fade-up">
            <div class="d-inline-block mb-3" style="padding: 8px 24px; background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%); border-radius: 50px; border: 1px solid rgba(102, 126, 234, 0.2);">
                <span style="color: #667eea; font-weight: 600; font-size: 0.9rem; letter-spacing: 1px;">
                    <i class="fas fa-th-large me-2"></i>استكشف عالمنا
                </span>
            </div>
            <h2 class="arabic-text" style="color: #1a202c; font-weight: 800; font-size: 2.8rem; margin-bottom: 16px; line-height: 1.2;">
                {{ $categoriesSection->title ?: 'فئات الخدمات' }}
            </h2>
            <p class="arabic-text" style="color: #4a5568; font-size: 1.2rem; font-weight: 400; max-width: 600px; margin: 0 auto;">
                {{ $categoriesSection->subtitle ?: 'اختر ما يناسب فعاليتك من تشكيلة متنوعة' }}
            </p>
            @if($categoriesSection->content['description'] ?? null)
            <div class="arabic-text mt-3" style="color: #718096; font-size: 1rem; max-width: 700px; margin: 16px auto 0;">
                {!! nl2br(e($categoriesSection->content['description'])) !!}
            </div>
            @endif
        </div>

        <!-- Categories Grid - Desktop & Tablet Only -->
        <div class="row g-4 position-relative d-none d-md-flex" style="z-index: 2;">
            @foreach($categories as $category)
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
                    <a href="{{ route('services.index', ['category' => $category->id]) }}" 
                       class="category-modern-card text-decoration-none d-block h-100">
                        <div class="category-card-wrapper" style="
                            background: #ffffff;
                            border-radius: 24px;
                            overflow: hidden;
                            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
                            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
                            border: 1px solid #e2e8f0;
                            height: 100%;
                            position: relative;
                        ">
                            <!-- Gradient/Image Header -->
                            <div class="category-header"
                                @if($category->image)
                                    data-bg-image="{{ asset('storage/' . $category->image) }}"
                                    style="padding: 32px 24px 80px; position: relative; overflow: hidden;"
                                @else
                                    style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 32px 24px 80px; position: relative; overflow: hidden;"
                                @endif
                            >
                                @if(!$category->image)
                                <!-- Pattern Overlay (only when no image) -->
                                <div style="
                                    position: absolute;
                                    top: 0;
                                    right: 0;
                                    width: 100%;
                                    height: 100%;
                                    background-image: 
                                        repeating-linear-gradient(45deg, transparent, transparent 10px, rgba(255,255,255,0.03) 10px, rgba(255,255,255,0.03) 20px),
                                        repeating-linear-gradient(-45deg, transparent, transparent 10px, rgba(255,255,255,0.03) 10px, rgba(255,255,255,0.03) 20px);
                                    pointer-events: none;
                                "></div>
                                @endif

                                <!-- Icon Container -->
                                <div class="category-icon-wrapper" style="position: relative; z-index: 2;">
                                    <div style="
                                        width: 70px;
                                        height: 70px;
                                        background: rgba(255,255,255,0.2);
                                        backdrop-filter: blur(10px);
                                        border-radius: 18px;
                                        display: flex;
                                        align-items: center;
                                        justify-content: center;
                                        box-shadow: 0 8px 24px rgba(0,0,0,0.15);
                                        border: 2px solid rgba(255,255,255,0.3);
                                    ">
                                        @if($category->icon)
                                            <i class="{{ $category->icon }}" style="color: #ffffff; font-size: 2rem;"></i>
                                        @else
                                            <i class="fas fa-star" style="color: #ffffff; font-size: 2rem;"></i>
                                        @endif
                                    </div>
                                </div>

                                <!-- Title -->
                                <h3 class="arabic-text mt-3" style="
                                    color: #ffffff;
                                    font-weight: 700;
                                    font-size: 1.5rem;
                                    margin: 0;
                                    position: relative;
                                    z-index: 2;
                                    text-shadow: 0 2px 8px rgba(0,0,0,0.1);
                                ">
                                    {{ $category->name }}
                                </h3>
                            </div>

                            <!-- Floating Image -->
                            @if($category->image)
                            <div class="category-floating-image" style="
                                position: absolute;
                                bottom: 100px;
                                left: 24px;
                                width: 120px;
                                height: 120px;
                                z-index: 3;
                                filter: drop-shadow(0 10px 20px rgba(0,0,0,0.15));
                                transition: transform 0.4s ease;
                            ">
                                <img src="{{ Storage::url($category->image) }}" 
                                     alt="{{ $category->name }}" 
                                     loading="lazy" 
                                     style="width: 100%; height: 100%; object-fit: contain;">
                            </div>
                            @endif

                            <!-- Card Body -->
                            <div class="category-body" style="padding: 24px; background: #ffffff;">
                                <!-- Services Count Badge -->
                                @if($category->services_count ?? 0)
                                <div class="d-inline-flex align-items-center mb-3" style="
                                    padding: 6px 16px;
                                    background: linear-gradient(135deg, rgba(102, 126, 234, 0.08) 0%, rgba(118, 75, 162, 0.08) 100%);
                                    border-radius: 20px;
                                    border: 1px solid rgba(102, 126, 234, 0.15);
                                ">
                                    <i class="fas fa-briefcase" style="color: #667eea; font-size: 0.85rem; margin-left: 8px;"></i>
                                    <span style="color: #667eea; font-weight: 600; font-size: 0.9rem;">
                                        {{ $category->services_count }} خدمة
                                    </span>
                                </div>
                                @endif

                                <!-- Description -->
                                @if($category->description)
                                <p class="arabic-text" style="
                                    color: #718096;
                                    font-size: 0.95rem;
                                    line-height: 1.6;
                                    margin: 0 0 16px 0;
                                ">
                                    {{ Str::limit($category->description, 80) }}
                                </p>
                                @endif

                                <!-- CTA Link -->
                                <div class="d-flex align-items-center" style="color: #667eea; font-weight: 600; font-size: 0.95rem;">
                                    <span>استكشف الخدمات</span>
                                    <i class="fas fa-arrow-left me-2 category-arrow" style="transition: transform 0.3s ease;"></i>
                                </div>
                            </div>

                            <!-- Hover Shine Effect -->
                            <div class="category-shine" style="
                                position: absolute;
                                top: 0;
                                right: -100%;
                                width: 50%;
                                height: 100%;
                                background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
                                transform: skewX(-20deg);
                                transition: right 0.6s ease;
                                pointer-events: none;
                            "></div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>

        <!-- Categories Grid - Mobile Only (Simplified Design) -->
        <div class="row g-3 position-relative d-md-none" style="z-index: 2;">
            @foreach($categories as $category)
                <div class="col-6" data-aos="fade-up" data-aos-delay="{{ $loop->index * 50 }}">
                    <a href="{{ route('services.index', ['category' => $category->id]) }}" 
                       class="category-mobile-card text-decoration-none d-block">
                        <div class="category-mobile-wrapper"
                            @if($category->image)
                                data-bg-image="{{ asset('storage/' . $category->image) }}"
                                style="border-radius: 16px; padding: 20px 16px; text-align: center; position: relative; overflow: hidden; box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3); transition: all 0.3s ease; min-height: 140px; display: flex; flex-direction: column; justify-content: center; align-items: center;"
                            @else
                                style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 16px; padding: 20px 16px; text-align: center; position: relative; overflow: hidden; box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3); transition: all 0.3s ease; min-height: 140px; display: flex; flex-direction: column; justify-content: center; align-items: center;"
                            @endif
                        >
                            @if(!$category->image)
                            <!-- Pattern Overlay (only when no image) -->
                            <div style="
                                position: absolute;
                                top: 0;
                                left: 0;
                                right: 0;
                                bottom: 0;
                                background-image: 
                                    radial-gradient(circle at 20% 50%, rgba(255,255,255,0.1) 0%, transparent 50%),
                                    radial-gradient(circle at 80% 80%, rgba(255,255,255,0.1) 0%, transparent 50%);
                                pointer-events: none;
                            "></div>
                            @endif

                            <!-- Icon -->
                            @if($category->image)
                            <div class="mb-2" style="position: relative; z-index: 2;">
                                <img src="{{ asset('storage/' . $category->image) }}" 
                                     alt="{{ $category->name }}"
                                     style="
                                         width: 50px;
                                         height: 50px;
                                         object-fit: contain;
                                         filter: brightness(0) invert(1);
                                         opacity: 0.95;
                                     ">
                            </div>
                            @endif

                            <!-- Category Name -->
                            <h3 class="arabic-text mb-1" style="
                                color: #ffffff;
                                font-size: 1rem;
                                font-weight: 700;
                                margin: 0;
                                position: relative;
                                z-index: 2;
                                line-height: 1.3;
                            ">
                                {{ $category->name }}
                            </h3>

                            <!-- Services Count -->
                            @if($category->services_count ?? 0)
                            <div style="
                                color: rgba(255,255,255,0.85);
                                font-size: 0.75rem;
                                font-weight: 500;
                                position: relative;
                                z-index: 2;
                            ">
                                {{ $category->services_count }} خدمة
                            </div>
                            @endif

                            <!-- Corner Accent -->
                            <div style="
                                position: absolute;
                                top: -20px;
                                right: -20px;
                                width: 60px;
                                height: 60px;
                                background: rgba(255,255,255,0.1);
                                border-radius: 50%;
                            "></div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>

        @if($categories->isEmpty())
            <div class="alert alert-info arabic-text mt-4 text-center" style="border-radius: 16px; border: none; background: rgba(102, 126, 234, 0.08);">
                <i class="fas fa-info-circle me-2"></i>
                لا توجد فئات مفعلة حالياً
            </div>
        @endif
        
        @if($categoriesSection->content['button_text'] ?? null)
        <div class="text-center mt-5" data-aos="fade-up" data-aos-delay="400">
            <a href="{{ $categoriesSection->content['button_link'] ?? '#' }}" 
               class="btn-custom-gradient" 
               style="
                display: inline-flex;
                align-items: center;
                padding: 16px 40px;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
                font-weight: 600;
                font-size: 1.1rem;
                border-radius: 50px;
                text-decoration: none;
                box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
                transition: all 0.3s ease;
                border: none;
            ">
                {{ $categoriesSection->content['button_text'] }}
                <i class="fas fa-arrow-left me-2"></i>
            </a>
        </div>
        @endif
    </div>
</section>
@endif

<!-- How It Works Section (moved to be after Categories Section) -->
<section class="how-it-works-section py-5 py-md-6" style="background: linear-gradient(180deg, #ffffff 0%, #f9fbfd 50%, #ffffff 100%);">
    <div class="container">
        <!-- Header -->
        <div class="text-center mb-4" data-aos="fade-up">
            <span class="arabic-text d-inline-block mb-3" style="background: rgba(45,188,174,0.12); border: 1px solid rgba(45,188,174,0.35); color: var(--secondary-color); padding: 8px 16px; border-radius: 999px; font-weight: 700;">
                كيف تشتغل Your Events
            </span>
            <h2 class="arabic-text fw-bold mb-2" style="color:#1a202c; font-size: clamp(1.6rem, 2.6vw, 2.2rem);">ما تحتاج شرح طويل</h2>
            <p class="arabic-text mb-0" style="color:#4a5568; font-size: 1.15rem;">خمس خطوات بسيطة، وتكون فعّاليتك جاهزة</p>
        </div>

        <!-- Steps Grid -->
        <div class="row g-4">
            <!-- Step 1 -->
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="0">
                <div class="h-100 card border-0 shadow-sm" style="border-radius: 18px; overflow: hidden;">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-3">
                            <div style="width:42px; height:42px; border-radius: 12px; display:flex; align-items:center; justify-content:center; background: linear-gradient(135deg, var(--secondary-color), #3cc7b8); color:#fff; box-shadow: 0 8px 20px rgba(45,188,174,0.3);">1️⃣</div>
                            <h5 class="arabic-text mb-0 me-3" style="color:#1a202c; font-weight:800;">تتصفّح الخدمات</h5>
                        </div>
                        <p class="arabic-text mb-0" style="color:#2d3748; line-height:1.9;">تختار اللي يناسبك من الضيافة، الألعاب، التصوير، أو غيرها.</p>
                    </div>
                </div>
            </div>

            <!-- Step 2 -->
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
                <div class="h-100 card border-0 shadow-sm" style="border-radius: 18px; overflow: hidden;">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-3">
                            <div style="width:42px; height:42px; border-radius: 12px; display:flex; align-items:center; justify-content:center; background: linear-gradient(135deg, #667eea, #764ba2); color:#fff; box-shadow: 0 8px 20px rgba(118, 75, 162, 0.3);">2️⃣</div>
                            <h5 class="arabic-text mb-0 me-3" style="color:#1a202c; font-weight:800;">تضيف التفاصيل</h5>
                        </div>
                        <p class="arabic-text mb-0" style="color:#2d3748; line-height:1.9;">كم عدد الأشخاص؟ وين؟ ومتى؟</p>
                    </div>
                </div>
            </div>

            <!-- Step 3 -->
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="200">
                <div class="h-100 card border-0 shadow-sm" style="border-radius: 18px; overflow: hidden;">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-3">
                            <div style="width:42px; height:42px; border-radius: 12px; display:flex; align-items:center; justify-content:center; background: linear-gradient(135deg, #ef4870, #f56b8a); color:#fff; box-shadow: 0 8px 20px rgba(239, 72, 112, 0.3);">3️⃣</div>
                            <h5 class="arabic-text mb-0 me-3" style="color:#1a202c; font-weight:800;">تختار الطريقة اللي تريحك</h5>
                        </div>
                        <p class="arabic-text mb-0" style="color:#2d3748; line-height:1.9;">تطلب عرض سعر… أو تدفع مباشرة وتبدأ التنفيذ.</p>
                    </div>
                </div>
            </div>

            <!-- Step 4 -->
            <div class="col-lg-6 col-md-6" data-aos="fade-up" data-aos-delay="300">
                <div class="h-100 card border-0 shadow-sm" style="border-radius: 18px; overflow: hidden;">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-3">
                            <div style="width:42px; height:42px; border-radius: 12px; display:flex; align-items:center; justify-content:center; background: linear-gradient(135deg, #4facfe, #00f2fe); color:#fff; box-shadow: 0 8px 20px rgba(79, 172, 254, 0.3);">4️⃣</div>
                            <h5 class="arabic-text mb-0 me-3" style="color:#1a202c; font-weight:800;">توصلك التفاصيل على طول</h5>
                        </div>
                        <p class="arabic-text mb-0" style="color:#2d3748; line-height:1.9;">كل شي يوصلك في إيميلك واضح ومرتب.</p>
                    </div>
                </div>
            </div>

            <!-- Step 5 -->
            <div class="col-lg-6 col-md-6" data-aos="fade-up" data-aos-delay="400">
                <div class="h-100 card border-0 shadow-sm" style="border-radius: 18px; overflow: hidden;">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-3">
                            <div style="width:42px; height:42px; border-radius: 12px; display:flex; align-items:center; justify-content:center; background: linear-gradient(135deg, var(--accent-color), #ef4870); color:#fff; box-shadow: 0 8px 20px rgba(214, 60, 94, 0.3);">5️⃣</div>
                            <h5 class="arabic-text mb-0 me-3" style="color:#1a202c; font-weight:800;">ونبدأ نجهّز لك الفعالية</h5>
                        </div>
                        <p class="arabic-text mb-0" style="color:#2d3748; line-height:1.9;">بسرعة، دقّة، ونتيجة تخلّيك تقول: “ليه ما كانت الفعاليات كذا من زمان؟” 🎉</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Closing Statement -->
        <div class="mt-4" data-aos="fade-up" data-aos-delay="500">
            <div class="card border-0 shadow-sm" style="border-radius: 18px; overflow: hidden; background: linear-gradient(135deg, rgba(45,188,174,0.08), rgba(118,75,162,0.08));">
                <div class="card-body p-4 p-md-5 text-center">
                    <p class="arabic-text mb-2" style="color:#1a202c; font-weight:700; font-size: 1.15rem;">
                        Your Events ما تبيعك خدمة…
                    </p>
                    <p class="arabic-text mb-0" style="color:#2d3748; font-weight:600;">
                        Your Events تخلّيك تجهّز بثقة.
                    </p>
                </div>
            </div>
        </div>
    </div>
    
</section>

<!-- Featured Services by Category Carousel Section -->
@php
    $categoryServicesSection = $sections->firstWhere('section_key', 'category_services');
    $categoryServicesBgStyle = $categoryServicesSection ? ($categoryServicesSection->getBackgroundStyle() ?: 'background: #f8f9fa;') : 'background: #f8f9fa;';
@endphp
@if($categoryServicesSection && $categoryServicesSection->is_active)
<section class="category-services-carousel py-5" id="category-services" data-bg-style="{{ $categoryServicesBgStyle }}">
    <div class="container">
        <!-- Section Header -->
        <div class="text-center mb-5" data-aos="fade-up">
            <h2 class="arabic-text mb-3" style="color: #1a202c; font-weight: 800; font-size: 2.5rem;">
                {{ $categoryServicesSection->title ?: 'خدماتنا المميزة' }}
            </h2>
            @if($categoryServicesSection->subtitle)
            <p class="text-muted" style="font-size: 1.1rem;">
                {{ $categoryServicesSection->subtitle }}
            </p>
            @endif
        </div>

        @foreach($categories->where('is_active', true) as $category)
            @php
                $categoryServices = $category->services()
                    ->where('is_active', true)
                    ->orderBy('created_at', 'desc')
                    ->limit(8)
                    ->get();
            @endphp
            
            @if($categoryServices->isNotEmpty())
            <div class="klb-module module-carousel mb-5" data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
                <!-- Category Header -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="d-flex align-items-center">
                        @if($category->icon)
                        <div style="
                            width: 50px;
                            height: 50px;
                            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                            border-radius: 12px;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            margin-left: 15px;
                        ">
                            <i class="{{ $category->icon }}" style="color: white; font-size: 1.5rem;"></i>
                        </div>
                        @endif
                        <div>
                            <h3 class="arabic-text mb-0" style="color: #1a202c; font-weight: 700; font-size: 1.8rem;">
                                {{ $category->name }}
                            </h3>
                            @if($category->description)
                            <p class="text-muted mb-0 small">{{ Str::limit($category->description, 60) }}</p>
                            @endif
                        </div>
                    </div>
                    <a href="{{ route('services.index', ['category' => $category->id]) }}" 
                       class="btn btn-outline-primary rounded-pill px-4">
                        عرض الكل
                        <i class="fas fa-arrow-left me-2"></i>
                    </a>
                </div>

                <!-- Services Carousel -->
                <div class="klb-products-tab module-body klb-slider-wrapper slider-loaded">
                    <div class="services-carousel-wrapper position-relative">
                        <div class="slick-list draggable">
                            <div class="services-carousel-track" id="carousel-{{ $category->id }}">
                                @foreach($categoryServices as $service)
                                <div class="service-card-carousel">
                                    <a href="{{ route('services.show', $service) }}" class="text-decoration-none">
                                        <div class="card h-100 border-0 shadow-sm" style="border-radius: 16px; overflow: hidden; transition: all 0.3s ease;">
                                            <!-- Badge -->
                                            @if($service->type)
                                            <div class="position-absolute top-0 end-0 m-3" style="z-index: 10;">
                                                <span class="badge rounded-pill" data-bg-color="{{ $category->color ?? '#667eea' }}" style="padding: 8px 16px; font-size: 0.75rem;">
                                                    {{ $service->type }}
                                                </span>
                                            </div>
                                            @endif

                                            <!-- Service Image -->
                                            <div class="service-image" style="height: 200px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); position: relative; overflow: hidden;">
                                                <img src="{{ $service->thumbnail_url }}" 
                                                     alt="{{ $service->name }}"
                                                     style="width: 100%; height: 100%; object-fit: cover;">
                                            </div>

                                            <!-- Card Body -->
                                            <div class="card-body p-3">
                                                <h5 class="arabic-text mb-2" style="color: #1a202c; font-weight: 700; font-size: 1.1rem; line-height: 1.4;">
                                                    {{ Str::limit($service->name, 50) }}
                                                </h5>
                                                
                                                @if($service->subtitle)
                                                <p class="text-muted small mb-2" style="font-size: 0.85rem;">
                                                    {{ Str::limit($service->subtitle, 60) }}
                                                </p>
                                                @endif

                                                <div class="d-flex justify-content-between align-items-center mt-3">
                                                    @if($service->price)
                                                    <div>
                                                        <span class="text-primary fw-bold" style="font-size: 1.2rem;">
                                                            {{ number_format((float)$service->price, 2) }} ر.س
                                                        </span>
                                                    </div>
                                                    @endif
                                                    
                                                    <button class="btn btn-sm btn-primary rounded-pill px-3">
                                                        تحديد أحد الخيارات
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                                @endforeach
                            </div> <!-- /.services-carousel-track -->
                        </div> <!-- /.slick-list -->

                        <!-- Navigation Arrows -->
                        <button class="carousel-nav carousel-nav-prev" onclick="scrollCarousel('carousel-{{ $category->id }}', -1)">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                        <button class="carousel-nav carousel-nav-next" onclick="scrollCarousel('carousel-{{ $category->id }}', 1)">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                    </div> <!-- /.services-carousel-wrapper -->
                </div> <!-- /.klb-products-tab -->
            </div> <!-- /.klb-module -->
            @endif
        @endforeach
    </div>
</section>


<style>
/* Services Carousel Styles */
.klb-products-tab.module-body {
    position: relative;
}

.klb-slider-wrapper {
    overflow: hidden;
}

.slider-loaded {
    opacity: 1;
    transition: opacity 0.3s ease;
}

.slick-list.draggable {
    overflow: hidden;
    position: relative;
    cursor: grab;
    touch-action: pan-y;
}

.slick-list.draggable:active {
    cursor: grabbing;
}

.services-carousel-wrapper {
    position: relative;
    padding: 0 50px;
}

.services-carousel-track {
    display: flex;
    gap: 20px;
    overflow-x: auto;
    scroll-behavior: smooth;
    padding: 10px 5px;
    scrollbar-width: none; /* Firefox */
    -ms-overflow-style: none; /* IE and Edge */
}

.services-carousel-track::-webkit-scrollbar {
    display: none; /* Chrome, Safari, Opera */
}

.service-card-carousel {
    flex: 0 0 280px;
    min-width: 280px;
}

.service-card-carousel .card:hover {
    transform: translateY(-8px);
    box-shadow: 0 12px 30px rgba(0,0,0,0.15) !important;
}

.carousel-nav {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    width: 45px;
    height: 45px;
    border-radius: 50%;
    background: white;
    border: 2px solid #e2e8f0;
    color: #667eea;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    z-index: 10;
    transition: all 0.3s ease;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.carousel-nav:hover {
    background: #667eea;
    color: white;
    border-color: #667eea;
    transform: translateY(-50%) scale(1.1);
}

.carousel-nav-prev {
    right: 0;
}

.carousel-nav-next {
    left: 0;
}

@media (max-width: 768px) {
    .services-carousel-wrapper {
        padding: 0 10px;
    }
    
    .services-carousel-track {
        gap: 12px;
        padding: 10px 5px;
    }
    
    /* خدمتين بجانب بعض على الموبايل */
    .service-card-carousel {
        flex: 0 0 calc(50% - 6px);
        min-width: calc(50% - 6px);
    }
    
    .service-card-carousel .card {
        border-radius: 12px !important;
    }
    
    .service-image {
        height: 150px !important;
    }
    
    .card-body {
        padding: 12px !important;
    }
    
    .card-body h5 {
        font-size: 0.95rem !important;
    }
    
    .card-body p {
        font-size: 0.8rem !important;
    }
    
    .btn-sm {
        font-size: 0.75rem !important;
        padding: 6px 12px !important;
    }
    
    .carousel-nav {
        width: 32px;
        height: 32px;
        font-size: 0.75rem;
    }
    
    .carousel-nav-prev {
        right: -5px;
    }
    
    .carousel-nav-next {
        left: -5px;
    }
    
    .klb-module .d-flex.justify-content-between {
        flex-direction: column;
        align-items: flex-start !important;
        gap: 15px;
    }
}

/* إخفاء الأسهم على الموبايل الصغير */
@media (max-width: 576px) {
    .carousel-nav {
        display: none;
    }
    
    .services-carousel-wrapper {
        padding: 0;
    }
}
</style>

<script>
function scrollCarousel(carouselId, direction) {
    const carousel = document.getElementById(carouselId);
    const scrollAmount = 300;
    carousel.scrollBy({
        left: direction * scrollAmount,
        behavior: 'smooth'
    });
}
</script>
@endif

<style>
/* Floating Animation */
@keyframes float {
    0%, 100% { transform: translateY(0) rotate(0deg); }
    50% { transform: translateY(-20px) rotate(5deg); }
}

/* Category Card Hover Effects - Desktop */
.category-modern-card:hover .category-card-wrapper {
    transform: translateY(-8px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.12);
}

.category-modern-card:hover .category-floating-image {
    transform: scale(1.1) rotate(-5deg);
}

.category-modern-card:hover .category-arrow {
    transform: translateX(-5px);
}

/* Mobile Cards Hover/Active Effects */
.category-mobile-card:active .category-mobile-wrapper {
    transform: scale(0.95);
    box-shadow: 0 2px 10px rgba(102, 126, 234, 0.4);
}

@media (hover: hover) {
    .category-mobile-card:hover .category-mobile-wrapper {
        transform: translateY(-4px);
        box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
    }
}

.category-modern-card:hover .category-shine {
    right: 100%;
}

.category-modern-card:hover .category-icon-wrapper > div {
    transform: scale(1.1) rotate(5deg);
}

/* Button Hover */
.btn-custom-gradient:hover {
    transform: translateY(-3px);
    box-shadow: 0 15px 40px rgba(102, 126, 234, 0.5);
}

/* Responsive */
@media (max-width: 768px) {
    .categories-showcase .container {
        padding-left: 16px !important;
        padding-right: 16px !important;
    }
    
    .category-header {
        padding: 24px 20px 70px !important;
    }
    
    .category-floating-image {
        width: 100px !important;
        height: 100px !important;
        bottom: 90px !important;
        left: 20px !important;
    }
    
    .category-body {
        padding: 20px !important;
    }
    
    .category-icon-wrapper > div {
        width: 60px !important;
        height: 60px !important;
    }
    
    .category-icon-wrapper i {
        font-size: 1.6rem !important;
    }
    
    .category-header h3 {
        font-size: 1.3rem !important;
    }
    
    .floating-shapes > div {
        display: none;
    }
    
    /* Mobile: Better spacing */
    .categories-showcase .row {
        margin-left: -8px !important;
        margin-right: -8px !important;
    }
    
    .categories-showcase .row > div {
        padding-left: 8px !important;
        padding-right: 8px !important;
        margin-bottom: 16px;
    }
    
    /* Section header mobile */
    .categories-showcase h2 {
        font-size: 2rem !important;
        margin-bottom: 12px !important;
    }
    
    .categories-showcase .arabic-text p {
        font-size: 1rem !important;
    }
    
    /* Button mobile */
    .btn-custom-gradient {
        padding: 14px 32px !important;
        font-size: 1rem !important;
        width: 100%;
        max-width: 320px;
        justify-content: center !important;
    }
}

@media (max-width: 576px) {
    /* Extra small devices */
    .category-header {
        padding: 20px 16px 65px !important;
    }
    
    .category-floating-image {
        width: 85px !important;
        height: 85px !important;
        bottom: 85px !important;
        left: 16px !important;
    }
    
    .category-body {
        padding: 16px !important;
    }
    
    .category-header h3 {
        font-size: 1.2rem !important;
    }
    
    .category-icon-wrapper > div {
        width: 55px !important;
        height: 55px !important;
    }
    
    .category-icon-wrapper i {
        font-size: 1.4rem !important;
    }
    
    .categories-showcase h2 {
        font-size: 1.75rem !important;
    }
    
    /* Mobile badge tag */
    .categories-showcase .d-inline-block.mb-3 {
        padding: 6px 18px !important;
        font-size: 0.85rem !important;
    }
    
    /* Services count badge */
    .category-body .d-inline-flex {
        padding: 5px 14px !important;
        font-size: 0.85rem !important;
    }
    
    .category-body .d-inline-flex i {
        font-size: 0.75rem !important;
    }
    
    /* Description text */
    .category-body p {
        font-size: 0.9rem !important;
        margin-bottom: 12px !important;
    }
    
    /* CTA link */
    .category-body .d-flex {
        font-size: 0.9rem !important;
    }
}

/* Landscape mobile optimization */
@media (max-width: 768px) and (orientation: landscape) {
    .category-header {
        padding: 20px 16px 60px !important;
    }
    
    .category-floating-image {
        width: 80px !important;
        height: 80px !important;
        bottom: 75px !important;
    }
    
    .category-icon-wrapper > div {
        width: 50px !important;
        height: 50px !important;
    }
}

/* Tablet optimization */
@media (min-width: 769px) and (max-width: 991px) {
    .category-header {
        padding: 28px 22px 75px !important;
    }
    
    .category-floating-image {
        width: 110px !important;
        height: 110px !important;
        bottom: 95px !important;
    }
    
    .category-icon-wrapper > div {
        width: 65px !important;
        height: 65px !important;
    }
    
    .category-header h3 {
        font-size: 1.4rem !important;
    }
}
</style>

<!-- Services Section -->
@php
    $servicesSection = $sections->firstWhere('section_key', 'services');
    $servicesBgStyle = $servicesSection ? ($servicesSection->getBackgroundStyle() ?: 'background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 50%, #e9ecef 100%);') : 'background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 50%, #e9ecef 100%);';
@endphp
@if($servicesSection && $servicesSection->is_active)
<section class="py-4" id="services" data-bg-style="{{ $servicesBgStyle }}" style="position: relative;">
    <div class="container">
        <div class="text-center mb-4">
            <h2 class="section-title arabic-text" data-aos="fade-up" style="color: var(--primary-color); font-size: 2.5rem; margin-bottom: 20px; text-shadow: 2px 2px 4px rgba(0,0,0,0.1);">{{ $servicesSection->title ?: 'كل اللي تحتاجه لحفلتك، بنوفرك إياه من مكان واحد' }}</h2>
            <p class="lead arabic-text" style="color: #444444; font-size: 1.2rem; font-weight: 500;">{{ $servicesSection->subtitle ?: 'من تنظيم وتنسيق، لمعايدة وتجهيز وصولاً - دورنا إنك بطريقك' }}</p>
            @if($servicesSection->content['description'] ?? null)
            <div class="arabic-text mt-3" style="color: #555;">
                {!! nl2br(e($servicesSection->content['description'])) !!}
            </div>
            @endif
        </div>
        
        <!-- Services Grid -->
        <div class="row justify-content-center mb-5">
            @php
                // عرض أول 6 خدمات فقط (يمكنك تغيير العدد من لوحة التحكم لاحقاً)
                $displayServices = $services->take($servicesSection->settings['display_count'] ?? 6);
            @endphp
            
            @forelse($displayServices as $service)
            <div class="col-lg-4 col-md-6 mb-4" data-aos="zoom-in" data-aos-delay="{{ $loop->index * 100 }}">
                <div class="service-card text-center">
                    <div class="service-image mb-3" style="width: 100%; height: 200px; border-radius: 15px; overflow: hidden;">
                        <img src="{{ $service->thumbnail_url }}" alt="{{ $service->name }}" style="width: 100%; height: 100%; object-fit: cover;">
                    </div>
                    <h5 class="arabic-text" style="color: var(--primary-color); margin: 20px 0 15px; font-weight: 700;">{{ $service->name }}</h5>
                    <p class="arabic-text" style="color: #555555; font-size: 1rem; line-height: 1.6;">{{ Str::limit($service->description, 100) }}</p>
                    <div class="d-flex justify-content-center gap-2 mt-3">
                        <a href="{{ route('services.show', $service->id) }}" class="btn btn-primary" style="border-radius: 25px; padding: 10px 25px;">عرض التفاصيل</a>
                        @if($service->price)
                            <span class="badge bg-success" style="border-radius: 25px; padding: 10px 15px; font-size: 0.9rem;">{{ number_format($service->price) }} ر.س</span>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <div class="col-lg-3 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="100">
                <div class="service-card text-center">
                    <div class="service-icon" style="background: linear-gradient(135deg, var(--accent-color), var(--secondary-color)); width: 80px; height: 80px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                        <i class="fas fa-users" style="font-size: 2rem; color: white;"></i>
                    </div>
                    <h5 class="arabic-text" style="color: var(--primary-color); margin: 20px 0 15px; font-weight: 700;">حاضرين لوصل الفرح</h5>
                    <p class="arabic-text" style="color: #555555; font-size: 1rem; line-height: 1.6;">نساعدك في كل خطوة</p>
                    <a href="{{ route('booking.create') }}" class="btn btn-secondary" style="margin-top: 15px; border-radius: 25px; padding: 10px 25px;">اطلب الآن</a>
                </div>
            </div>
            @endforelse
        </div>
        
        <div class="text-center mt-3">
            <a href="{{ route('services.index') }}" class="btn btn-outline-primary btn-lg" style="border: 2px solid var(--primary-color); color: var(--primary-color);">
                استكشف جميع الخدمات
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
    margin-bottom: 20px;
}

/* Service card mobile responsiveness */
@media (max-width: 991px) {
    .service-card {
        padding: 25px 15px;
        margin-bottom: 25px;
    }
    
    .service-icon {
        width: 70px !important;
        height: 70px !important;
        margin: 0 auto 15px !important;
    }
    
    .service-icon i {
        font-size: 1.75rem !important;
    }
    
    .service-card h5 {
        font-size: 1.1rem !important;
        margin: 15px 0 10px !important;
    }
    
    .service-card p {
        font-size: 0.95rem !important;
        line-height: 1.5 !important;
    }
    
    .service-card .btn {
        padding: 8px 20px !important;
        font-size: 0.9rem !important;
    }
}

@media (max-width: 767px) {
    .service-card {
        padding: 20px 15px;
        margin-bottom: 20px;
        border-radius: 15px;
    }
    
    .service-icon {
        width: 60px !important;
        height: 60px !important;
        margin: 0 auto 12px !important;
    }
    
    .service-icon i {
        font-size: 1.5rem !important;
    }
    
    .service-card h5 {
        font-size: 1rem !important;
        margin: 12px 0 8px !important;
    }
    
    .service-card p {
        font-size: 0.9rem !important;
        line-height: 1.4 !important;
        margin-bottom: 12px !important;
    }
    
    .service-card .btn {
        padding: 7px 18px !important;
        font-size: 0.85rem !important;
        border-radius: 20px !important;
    }
}

@media (max-width: 575px) {
    .service-card {
        padding: 18px 12px;
        margin-bottom: 15px;
        border-radius: 12px;
    }
    
    .service-icon {
        width: 55px !important;
        height: 55px !important;
        margin: 0 auto 10px !important;
    }
    
    .service-icon i {
        font-size: 1.3rem !important;
    }
    
    .service-card h5 {
        font-size: 0.95rem !important;
        margin: 10px 0 6px !important;
    }
    
    .service-card p {
        font-size: 0.85rem !important;
        line-height: 1.3 !important;
        margin-bottom: 10px !important;
    }
    
    .service-card .btn {
        padding: 6px 16px !important;
        font-size: 0.8rem !important;
        width: 80%;
        max-width: 150px;
    }
}

/* Hero styles */
                        .hero-banner {
                            position: relative;
                            /* Removed legacy hero overrides */
                        }
                        .hero-floating-card.event-card {
                            top: 45%;
                            right: -40px;
                        }
                        .hero-floating-card.event-card .label {
                            font-size: 0.85rem;
                            color: #555;
                        }
                        .hero-scroll-cue {
                            position: absolute;
                            bottom: 24px;
                            left: 50%;
                            transform: translateX(-50%);
                            display: flex;
                            align-items: center;
                            gap: 10px;
                            color: rgba(255, 255, 255, 0.85);
                            cursor: pointer;
                            transition: opacity 0.2s ease;
                            background: none;
                            border: none;
                        }
                        .hero-scroll-cue:hover {
                            opacity: 1;
                        }
                        .hero-scroll-cue .mouse {
                            width: 28px;
                            height: 45px;
                            border: 2px solid rgba(255, 255, 255, 0.7);
                            border-radius: 20px;
                            position: relative;
                            display: inline-block;
                        }
                        .hero-scroll-cue .mouse::after {
                            content: '';
                            position: absolute;
                            top: 8px;
                            left: 50%;
                            transform: translateX(-50%);
                            width: 4px;
                            height: 10px;
                            background: rgba(255, 255, 255, 0.7);
                            border-radius: 2px;
                            animation: scrollDot 1.4s ease-in-out infinite;
                        }
                        @keyframes scrollDot {
                            0% { opacity: 0; transform: translate(-50%, 0); }
                            50% { opacity: 1; transform: translate(-50%, 10px); }
                            100% { opacity: 0; transform: translate(-50%, 0); }
                        }

                        @media (max-width: 1199.98px) {
                            .hero-banner {
                                min-height: 580px;
                            }
                            .hero-floating-card.event-card {
                                right: -10px;
                            }
                        }

                        @media (max-width: 991.98px) {
                            .hero-banner {
                                min-height: auto;
                                padding: 100px 0 80px;
                            }
                            .hero-copy {
                                text-align: center;
                                margin-bottom: 48px;
                            }
                            .hero-cta {
                                justify-content: center;
                            }
                            .hero-visual {
                                padding: 12px;
                            }
                            .hero-floating-card {
                                position: static;
                                margin: 12px auto 0;
                                max-width: 260px;
                                justify-content: flex-start;
                            }
                            .hero-visual-frame {
                                padding: 12px;
                            }
                            .hero-floating-card.rating-card,
                            .hero-floating-card.event-card,
                            .hero-floating-card.stats-card {
                                text-align: center;
                            }
                        }

                        @media (max-width: 767.98px) {
                            .hero-title {
                                font-size: clamp(2rem, 7vw, 2.6rem);
                            }
                            .hero-subtitle {
                                font-size: 1rem;
                                margin-bottom: 1.75rem;
                            }
                            .hero-cta-btn {
                                width: 100%;
                                max-width: 320px;
                                padding: 16px 34px;
                                font-size: 1.05rem;
                            }
                            .hero-cta-secondary {
                                width: 100%;
                                max-width: 320px;
                                text-align: center;
                                padding: 15px 34px;
                            }
                        }

                        @media (max-width: 575.98px) {
                            .hero-banner {
                                padding: 90px 0 70px;
                            }
                            .hero-copy {
                                margin-bottom: 40px;
                            }
                            .hero-visual {
                                margin-bottom: 24px;
                            }
                            .hero-floating-card {
                                width: 100%;
                                max-width: 260px;
                            }
                            .hero-scroll-cue {
                                bottom: 18px;
                            }
                        }
 

/* Hero overrides */
.hero-content-wrapper { 
    padding: 60px 40px; 
    text-align: right; 
    position: relative;
    z-index: 10;
}
.hero-title-display { 
    font-size: 3.2rem; 
    font-weight: 800; 
    line-height: 1.2; 
    text-shadow: 2px 2px 8px rgba(0,0,0,0.7); 
    margin-bottom: 1.5rem;
}
.hero-subtitle-display { 
    font-size: 1.35rem; 
    line-height: 1.8; 
    font-weight: 400; 
    text-shadow: 1px 1px 4px rgba(0,0,0,0.6); 
    margin-bottom: 2rem;
}
.hero-cta-btn { 
    background: #2dbcae; 
    color: #fff; 
    border: none; 
    border-radius: 25px; 
    padding: 18px 45px; 
    font-size: 1.25rem; 
    font-weight: 600; 
    box-shadow: 0 8px 25px rgba(45,188,174,0.4); 
    transition: .3s; 
    text-decoration: none; 
    display: inline-block; 
}
.hero-cta-btn:hover { 
    transform: translateY(-3px); 
    box-shadow: 0 12px 30px rgba(45,188,174,.55); 
    color: #fff; 
}

/* Legacy hero overrides removed */

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

/* Hero badge and secondary CTA */
.hero-badge {
    display: inline-block;
    background: rgba(45, 188, 174, 0.15);
    border: 1px solid rgba(45, 188, 174, 0.5);
    color: #eafffb;
    padding: 8px 12px;
    border-radius: 999px;
    font-size: 0.95rem;
    font-weight: 700;
    box-shadow: 0 6px 18px rgba(45, 188, 174, 0.25);
}
.hero-badge .badge-label i { margin-left: 8px; }
.hero-cta-secondary {
    border-radius: 25px;
    padding: 16px 30px;
    font-size: 1.1rem;
    border: 2px solid rgba(255,255,255,0.4) !important;
    color: #fff !important;
    backdrop-filter: blur(3px);
}
.hero-cta-secondary:hover { border-color: rgba(255,255,255,0.7) !important; }

/* Scroll cue */
.hero-scroll-cue {
    position: absolute;
    bottom: 20px;
    left: 50%;
    transform: translateX(-50%);
    display: flex;
    align-items: center;
    gap: 10px;
    color: rgba(255,255,255,0.85);
    cursor: pointer;
    transition: opacity .2s ease;
}
.hero-scroll-cue:hover { opacity: 1; }
.hero-scroll-cue .mouse {
    width: 28px;
    height: 45px;
    border: 2px solid rgba(255,255,255,0.7);
    border-radius: 20px;
    position: relative;
}
.hero-scroll-cue .mouse::after {
    content: '';
    position: absolute;
    top: 8px;
    left: 50%;
    transform: translateX(-50%);
    width: 4px;
    height: 10px;
    background: rgba(255,255,255,0.7);
    border-radius: 2px;
    animation: scrollDot 1.4s ease-in-out infinite;
}
@keyframes scrollDot {
    0% { opacity: 0; transform: translate(-50%, 0); }
    50% { opacity: 1; transform: translate(-50%, 10px); }
    100% { opacity: 0; transform: translate(-50%, 0); }
}

@media (max-width: 767px) {
    .hero-badge { font-size: 0.85rem; padding: 6px 10px; }
    .hero-cta-secondary { padding: 12px 24px; font-size: 1rem; }
}
</style>
@endif

<!-- Packages Section -->
@php
    $packagesSection = $sections->firstWhere('section_key', 'packages');
@endphp
@if($packagesSection && $packagesSection->is_active)
<section class="py-4" id="packages" data-bg-style="{{ $packagesSection->getBackgroundStyle() ?: 'background: #f8f9fa;' }}">
    <div class="container">
        <h2 class="section-title arabic-text" data-aos="fade-up" style="color: var(--primary-color); text-align: center; margin-bottom: 2rem;">{{ $packagesSection->title ?: 'باقاتنا المميزة' }}</h2>
        @if($packagesSection->subtitle)
        <p class="text-center arabic-text mb-4" style="color: #666; font-size: 1.15rem;">{{ $packagesSection->subtitle }}</p>
        @endif
        <div class="row">
            @forelse($packages as $package)
                <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
                    <div class="card h-100">
                        @if($package->image)
                            <img src="{{ Storage::url($package->image) }}" class="card-img-top" alt="{{ $package->name }}" loading="lazy" decoding="async">
                        @else
                            <img src="{{ asset('images/event-package.svg') }}" 
                                 class="card-img-top" alt="{{ $package->name }}" loading="lazy" decoding="async">
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
@endif

<!-- Gallery Section -->
@php
    $gallerySection = $sections->firstWhere('section_key', 'gallery');
@endphp
@if($gallerySection && $gallerySection->is_active && $gallery->count() > 0)
<section class="py-4" id="gallery" data-bg-style="{{ $gallerySection->getBackgroundStyle() ?: '' }}">
    <div class="container">
        <h2 class="section-title arabic-text" data-aos="fade-up" style="color: var(--primary-color); text-align: center; margin-bottom: 2rem;">{{ $gallerySection->title ?: 'معرض أعمالنا' }}</h2>
        @if($gallerySection->subtitle)
        <p class="text-center arabic-text mb-4" style="color: #666; font-size: 1.15rem;">{{ $gallerySection->subtitle }}</p>
        @endif
        <div class="row">
            @foreach($gallery as $item)
                <div class="col-lg-3 col-md-6 mb-4" data-aos="zoom-in" data-aos-delay="{{ $loop->index * 50 }}">
                    <div class="gallery-item">
                        @if($item->type === 'image')
                            <img src="{{ Storage::url($item->path) }}" class="img-fluid w-100" 
                                 alt="{{ $item->title }}" loading="lazy" decoding="async" style="height: 250px; object-fit: cover;">
                        @else
                            <video class="img-fluid w-100" style="height: 250px; object-fit: cover;" muted preload="metadata" playsinline>
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
@php
    $reviewsSection = $sections->firstWhere('section_key', 'reviews');
@endphp
@if($reviewsSection && $reviewsSection->is_active && $reviews->count() > 0)
<section class="py-4" id="reviews" data-bg-style="{{ $reviewsSection->getBackgroundStyle() ?: 'background: #f8f9fa;' }}">
    <div class="container">
        <h2 class="section-title arabic-text" data-aos="fade-up" style="color: var(--primary-color); text-align: center; margin-bottom: 2rem;">{{ $reviewsSection->title ?: 'آراء عملائنا' }}</h2>
        @if($reviewsSection->subtitle)
        <p class="text-center arabic-text mb-4" style="color: #666; font-size: 1.15rem;">{{ $reviewsSection->subtitle }}</p>
        @endif
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
                         alt="VR Experience" class="img-fluid" loading="lazy" decoding="async"
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
                    <a href="{{ route('services.index') }}" class="btn btn-outline-primary" style="border: 2px solid var(--primary-color); color: var(--primary-color); border-radius: 25px; padding: 15px 25px; background: rgba(31, 20, 74, 0.1); transition: all 0.3s ease;">
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
                    <img src="{{ asset('images/vr/tablet.png') }}" 
                         alt="امرأة ترتدي نظارة الواقع الافتراضي" 
                         class="img-fluid" loading="lazy" decoding="async"
                         style="max-height: 400px; width: auto; filter: drop-shadow(0 10px 30px rgba(0,0,0,0.3));">
                </div>
            </div>
            
            <!-- Left side - VR Woman Image -->
            <div class="col-lg-6" data-aos="fade-left">
                <div class="cta-content text-end" style="padding: 40px; direction: rtl;">
                    <!-- Main Title -->
                    <h2 class="mb-4 arabic-text" style="font-size: 3rem; font-weight: 800; line-height: 1.3; text-shadow: 2px 2px 8px rgba(0,0,0,0.8); color: var(--gold-color);">
                        جاهز تبدأ ؟
                    </h2>
                    
                    <!-- Subtitle -->
                    <p class="mb-5 arabic-text" style="font-size: 1.3rem; line-height: 1.7; text-shadow: 1px 1px 4px rgba(0,0,0,0.6); font-weight: 400; color: #f8f9fa;">
                        خلاص… لا تشيل هم ولا تضيع وقتك

كل شي جاهز، وكل خطوة أسهل من اللي قبلها.

تختار، تطلب، وتبدأ… والباقي على Your Events
<h3 class="arabic-text" style="font-size: 1.65rem; font-weight: 800; margin-bottom: 16px; color: var(--gold-color);">فعّاليتك تستاهل البداية الصح،</h3>
                    </p>
                    
                    <p class="arabic-text" style="font-size: 1.2rem; line-height: 1.8; margin-bottom: 24px;">و Your Events دايم تبدأها معك</p>
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

<!-- Quick Start CTA Section (Arabic)
<section class="cta-quick-start py-5" style="background: linear-gradient(135deg, rgba(31,20,74,0.95) 0%, rgba(45,188,174,0.95) 100%); color: #fff; position: relative; overflow: hidden;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="cta-box" style="background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.15); border-radius: 24px; padding: 32px 28px; box-shadow: 0 20px 40px rgba(0,0,0,0.25);">
                    <div class="d-flex flex-column align-items-end text-end" style="direction: rtl;">
                        <span class="badge" style="background: rgba(240,199,29,0.18); border: 1px solid rgba(240,199,29,0.45); color: var(--gold-color); border-radius: 999px; padding: 10px 16px; font-weight: 700; margin-bottom: 18px;">جاهز تبدأ ؟ :-</span>

                        <p class="arabic-text" style="font-size: 1.25rem; line-height: 1.8; margin: 0 0 8px 0;">خلاص… لا تشيل هم ولا تضيع وقتك</p>
                        <p class="arabic-text" style="font-size: 1.2rem; line-height: 1.8; margin: 0 0 8px 0;">كل شي جاهز، وكل خطوة أسهل من اللي قبلها.</p>
                        <p class="arabic-text" style="font-size: 1.2rem; line-height: 1.8; margin: 0 0 16px 0;">تختار، تطلب، وتبدأ… والباقي على Your Events.</p>

                        <h3 class="arabic-text" style="font-size: 1.65rem; font-weight: 800; margin-bottom: 16px; color: var(--gold-color);">فعّاليتك تستاهل البداية الصح،</h3>
                        <p class="arabic-text" style="font-size: 1.2rem; line-height: 1.8; margin-bottom: 24px;">وYour Events دايم تبدأها معك</p>

                        <div class="d-flex flex-wrap gap-2 justify-content-end">
                            <a href="{{ route('booking.create') }}" class="btn btn-gold" style="border-radius: 25px; padding: 14px 28px; font-size: 1.1rem; font-weight: 700; box-shadow: 0 8px 25px rgba(240,199,29,0.35);">
                                🔹 جهّز فعاليتك الآن
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section> -->

<style>
/* Mobile Responsive Fixes */
@media (max-width: 991px) {
    /* Hero Section Mobile */
    .hero-banner {
        min-height: auto !important;
        padding: 90px 0 70px !important;
    }
    
    .hero-copy {
        padding: 0 20px !important;
        text-align: center !important;
    }
    
    .hero-title {
        font-size: 2.3rem !important;
        line-height: 1.3 !important;
        margin-bottom: 20px !important;
    }
    
    .hero-subtitle {
        font-size: 1.1rem !important;
        margin-bottom: 24px !important;
        line-height: 1.6 !important;
    }
    
    .hero-cta {
        justify-content: center !important;
    }
    
    .hero-cta-btn {
        padding: 16px 36px !important;
        font-size: 1.1rem !important;
        min-width: 220px !important;
    }
    
    /* Services Section Mobile */
    .section-title {
        font-size: 1.8rem !important;
    }
    
    .lead {
        font-size: 1rem !important;
    }
    
    .service-card {
        margin-bottom: 20px;
    }
    
    /* VR Experience Section Mobile */
    .vr-person-container {
        margin-bottom: 40px;
        text-align: center;
    }
    
    .vr-person-container img {
        max-width: 100%;
        height: auto;
        max-height: 350px;
        border-radius: 15px !important;
    }
    
    /* CTA Section Mobile */
    .vr-woman-container {
        margin-bottom: 40px;
        text-align: center;
    }
    
    .vr-woman-container img {
        max-height: 320px !important;
        width: auto;
    }
    
    .cta-content {
        padding: 30px 20px !important;
        text-align: center !important;
    }
    
    .cta-content h2 {
        font-size: 2.2rem !important;
        line-height: 1.3 !important;
        margin-bottom: 20px !important;
    }
    
    .cta-content p {
        font-size: 1.15rem !important;
        line-height: 1.6 !important;
        margin-bottom: 25px !important;
    }
    
    .cta-buttons {
        justify-content: center !important;
        flex-wrap: wrap;
        gap: 15px !important;
    }
    
    .cta-buttons .btn {
        margin: 8px !important;
        min-width: 160px !important;
    }
    
    /* Navigation Arrows Mobile */
    /* .navigation-arrows {
        display: none !important;
    } */
    
    /* Floating Elements Mobile */
    .floating-elements > div {
        display: none;
    }
    
    .floating-vr-elements > div {
        display: none;
    }
}

@media (max-width: 767px) {
    /* Extra small devices */
    .hero-title {
        font-size: 1.8rem !important;
        line-height: 1.35 !important;
    }
    
    .hero-subtitle {
        font-size: 0.98rem !important;
        line-height: 1.6 !important;
    }
    
    .section-title {
        font-size: 1.5rem !important;
        margin-bottom: 15px !important;
    }
    
    .service-icon {
        width: 60px !important;
        height: 60px !important;
    }
    
    .service-icon i {
        font-size: 1.5rem !important;
    }
    
    .service-card h5 {
        font-size: 1.1rem !important;
    }
    
    .service-card p {
        font-size: 0.9rem !important;
    }
    
    .btn-secondary {
        padding: 8px 20px !important;
        font-size: 0.9rem !important;
    }
    
    .cta-content h2 {
        font-size: 1.6rem !important;
    }
    
    .cta-content p {
        font-size: 0.95rem !important;
    }
    
    .cta-buttons .btn {
        padding: 12px 25px !important;
        font-size: 1rem !important;
    }
}

/* Hero Section Background adjustments */
.hero-bg-gradient {
    opacity: 0.9;
}

@media (max-width: 575px) {
    /* إصلاح شامل للفراغات على الهاتف المحمول */
    body {
        overflow-x: hidden !important;
    }
    
    .container {
        padding-left: 15px !important;
        padding-right: 15px !important;
        margin-left: auto !important;
        margin-right: auto !important;
        max-width: 100% !important;
    }
    
    .row {
        margin-left: 0 !important;
        margin-right: 0 !important;
    }
    
    .col-12, .col-lg-6, .col-md-6, .col-lg-3, .col-lg-4 {
        padding-left: 10px !important;
        padding-right: 10px !important;
    }
    
    /* Mobile portrait mode */
    .hero-banner {
        padding: 90px 0 50px !important;
        margin-left: 0 !important;
        margin-right: 0 !important;
        width: 100% !important;
    }
    
    .hero-title {
        font-size: 1.6rem !important;
        line-height: 1.25 !important;
        margin-bottom: 15px !important;
    }
    
    .hero-subtitle {
        font-size: 1rem !important;
        line-height: 1.5 !important;
        margin-bottom: 20px !important;
    }
    
    .hero-cta-btn {
        padding: 14px 30px !important;
        font-size: 1.05rem !important;
        width: 90%;
        max-width: 280px;
        margin: 15px auto !important;
        border-radius: 25px !important;
    }
    
    /* إصلاح الأقسام */
    section {
        padding-left: 0 !important;
        padding-right: 0 !important;
        margin-left: 0 !important;
        margin-right: 0 !important;
        width: 100% !important;
        overflow-x: hidden !important;
    }
    
    .section-title {
        font-size: 1.3rem !important;
        line-height: 1.3 !important;
        padding: 0 15px !important;
        margin-bottom: 20px !important;
    }
    
    .lead {
        font-size: 0.9rem !important;
        line-height: 1.5 !important;
        margin-bottom: 15px !important;
    }
    
    .service-card {
        padding: 20px !important;
        margin-bottom: 15px !important;
    }
    
    .vr-woman-container img {
        max-height: 250px !important;
    }
    
    .cta-buttons {
        flex-direction: column;
        align-items: center;
    }
    
    .cta-buttons .btn {
        width: 100%;
        max-width: 250px;
        margin: 5px !important;
    }
    
    /* Additional mobile responsive styles */
    .navbar-brand img {
        height: 40px !important;
    }
    
    .btn-gold {
        padding: 10px 25px !important;
        font-size: 0.9rem !important;
        margin: 8px auto !important;
        border-radius: 20px !important;
        min-width: 120px !important;
        display: block !important;
        width: fit-content !important;
    }
    
    .card {
        margin: 10px 0 20px 0 !important;
        padding: 20px !important;
        border-radius: 15px !important;
        box-shadow: 0 8px 25px rgba(0,0,0,0.1) !important;
        width: 100% !important;
        max-width: 100% !important;
    }
    
    .card-body {
        padding: 15px !important;
    }
    
    .card-title {
        font-size: 1.1rem !important;
        margin-bottom: 10px !important;
    }
    
    .card-text {
        font-size: 0.9rem !important;
        line-height: 1.5 !important;
    }
    
    .price-tag {
        font-size: 1.3rem !important;
        padding: 10px 18px !important;
        border-radius: 15px !important;
        font-weight: 600 !important;
    }
    
    /* إصلاح الفراغات الجانبية */
    .service-card {
        margin-left: 0 !important;
        margin-right: 0 !important;
        width: 100% !important;
        max-width: 100% !important;
    }
    
    /* إصلاح الصور والعناصر المرئية */
    img, video {
        max-width: 100% !important;
        height: auto !important;
    }
    
    /* إصلاح الأزرار */
    .btn {
        margin-left: auto !important;
        margin-right: auto !important;
        display: block !important;
        width: fit-content !important;
    }
    
    .btn-secondary, .btn-primary {
        margin: 10px auto !important;
    }
    
    /* General spacing improvements for mobile */
    .container {
        padding: 0 10px !important;
    }
    
    .row {
        margin: 0 -5px !important;
    }
    
    .col-md-4, .col-lg-4, .col-sm-6 {
        padding: 0 5px !important;
        margin-bottom: 15px !important;
    }
    
    /* Section spacing */
    section {
        padding: 40px 0 !important;
    }
    
    .py-5 {
        padding: 30px 0 !important;
    }
    
    /* Text improvements */
    p {
        font-size: 0.9rem !important;
        line-height: 1.5 !important;
        margin-bottom: 15px !important;
    }
    
    h1 {
        font-size: 1.8rem !important;
        line-height: 1.2 !important;
        margin-bottom: 15px !important;
    }
    
    h2 {
        font-size: 1.5rem !important;
        line-height: 1.3 !important;
        margin-bottom: 12px !important;
    }
    
    h3 {
        font-size: 1.3rem !important;
        line-height: 1.3 !important;
        margin-bottom: 10px !important;
    }
    
    h4 {
        font-size: 1.1rem !important;
        line-height: 1.3 !important;
        margin-bottom: 8px !important;
    }
    
    h5 {
        font-size: 1rem !important;
        line-height: 1.3 !important;
        margin-bottom: 8px !important;
    }
    
    /* Button spacing */
    .btn {
        margin: 5px !important;
        min-width: 120px !important;
    }
    
    /* Footer adjustments */
    .footer {
        padding: 40px 0 25px !important;
        margin-left: 0 !important;
        margin-right: 0 !important;
        width: 100% !important;
    }
    
    .footer .container {
        padding-left: 15px !important;
        padding-right: 15px !important;
        margin-left: auto !important;
        margin-right: auto !important;
        max-width: 100% !important;
    }
    
    .footer .col-md-3 {
        margin-bottom: 25px !important;
        text-align: center !important;
    }
    
    .footer h5 {
        font-size: 1.1rem !important;
        margin-bottom: 12px !important;
        color: var(--primary-color) !important;
    }
    
    .footer p, .footer a {
        font-size: 0.9rem !important;
        line-height: 1.5 !important;
    }
    
    /* إصلاح نهائي للفراغات */
    * {
        box-sizing: border-box !important;
    }
    
    html, body {
        width: 100% !important;
        overflow-x: hidden !important;
        margin: 0 !important;
        padding: 0 !important;
    }
    
    .main-content {
        width: 100% !important;
        padding: 0 !important;
        margin: 0 !important;
    }
    
    /* إصلاح العناصر العائمة */
    .floating-elements, .floating-vr-elements {
        display: none !important;
    }
    
    /* إصلاح الخلفيات */
    .hero-banner, section {
        background-size: cover !important;
        background-position: center !important;
        background-repeat: no-repeat !important;
    }
    
    /* Gallery improvements */
    .gallery-item {
        margin-bottom: 20px !important;
        border-radius: 12px !important;
        overflow: hidden !important;
    }
    
    .gallery-item img,
    .gallery-item video {
        border-radius: 12px !important;
        transition: transform 0.3s ease !important;
    }
    
    /* Reviews section */
    .card h6 {
        font-size: 1rem !important;
        margin-top: 10px !important;
    }
    
    /* Floating elements hide on mobile */
    .floating-elements,
    .floating-vr-elements {
        display: none !important;
    }
    
    /* Improve button spacing */
    .btn-outline-primary,
    .btn-outline-light {
        padding: 12px 25px !important;
        font-size: 0.95rem !important;
        margin: 8px !important;
        border-radius: 20px !important;
    }
}
/* Products Grid System */
.products.grid-column {
    display: grid;
    gap: 1.5rem;
}

/* Default: 3 columns on desktop */
.products.column-3 {
    grid-template-columns: repeat(3, 1fr);
}

/* Mobile: 2 columns */
@media (max-width: 767px) {
    .products.mobile-grid-2 {
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
    }
    
    .products.mobile-grid-2 .category-card {
        min-height: 140px !important;
        padding: 25px 20px !important;
    }
    
    .products.mobile-grid-2 .category-title {
        font-size: 1.2rem !important;
        font-weight: 700 !important;
    }
    
    .products.mobile-grid-2 .category-icon-wrapper {
        padding: 12px !important;
    }
    
    .products.mobile-grid-2 .category-card i {
        font-size: 1.8rem !important;
    }
    
    .products.mobile-grid-2 .category-card img {
        width: 70px !important;
        height: 70px !important;
    }
}

/* Tablet: 2 columns */
@media (min-width: 768px) and (max-width: 991px) {
    .products.column-3 {
        grid-template-columns: repeat(2, 1fr);
    }
}

/* Categories cards styles */
.category-card {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
}

.category-card:hover {
    transform: translateY(-5px) scale(1.02);
    box-shadow: 0 20px 40px rgba(91,33,182,0.4) !important;
}

.category-card:hover .category-shine {
    transform: translateX(100%) translateY(100%) rotate(45deg);
}

.category-card:hover .fa-chevron-left {
    transform: translateX(-4px);
    color: rgba(255,255,255,1) !important;
}

.category-icon-wrapper {
    transition: all 0.3s ease;
}

.category-card:hover .category-icon-wrapper {
    transform: scale(1.05);
    background: rgba(255,255,255,0.2) !important;
}

/* Desktop - Large */
@media (min-width: 1200px) {
    .category-card {
        min-height: 200px !important;
        max-height: 200px !important;
    }
    .category-card .category-icon-wrapper {
        width: 90px !important;
        height: 90px !important;
        padding: 20px !important;
    }
    .category-card .category-icon-wrapper i {
        font-size: 2.8rem !important;
    }
    .category-card .category-icon-wrapper img {
        width: 55px !important;
        height: 55px !important;
    }
    .category-card .category-title {
        font-size: 1.5rem !important;
    }
}

/* Desktop - Standard */
@media (min-width: 992px) and (max-width: 1199px) {
    .category-card {
        min-height: 180px !important;
        max-height: 180px !important;
    }
    .category-card .category-icon-wrapper {
        width: 80px !important;
        height: 80px !important;
    }
    .category-card .category-title {
        font-size: 1.4rem !important;
    }
}

/* Tablet */
@media (max-width: 991px) {
    .category-card {
        min-height: 160px !important;
        max-height: 160px !important;
    }
    .category-card > div {
        padding: 18px 22px !important;
        gap: 14px !important;
    }
    .category-card .category-icon-wrapper {
        width: 75px !important;
        height: 75px !important;
        padding: 16px !important;
    }
    .category-card .category-icon-wrapper i {
        font-size: 2.3rem !important;
    }
    .category-card .category-icon-wrapper img {
        width: 45px !important;
        height: 45px !important;
    }
    .category-card .category-title {
        font-size: 1.3rem !important;
    }
}

/* Mobile - Large */
@media (max-width: 767px) {
    #home-categories .section-heading { 
        font-size: 2rem !important;
    }
    #home-categories .section-subheading { 
        font-size: 1rem !important;
    }
    .category-card { 
        border-radius: 18px !important;
        min-height: 150px !important;
        max-height: 150px !important;
    }
    .category-card > div {
        padding: 16px 20px !important;
        gap: 12px !important;
    }
    .category-card .category-icon-wrapper {
        width: 70px !important;
        height: 70px !important;
        padding: 15px !important;
        border-radius: 14px !important;
    }
    .category-card .category-icon-wrapper i {
        font-size: 2.1rem !important;
    }
    .category-card .category-icon-wrapper img {
        width: 42px !important;
        height: 42px !important;
    }
    .category-card .category-title {
        font-size: 1.25rem !important;
        font-weight: 800 !important;
    }
    .category-card .fa-chevron-left {
        font-size: 1.1rem !important;
    }
}

/* Mobile - Standard */
@media (max-width: 575px) {
    #home-categories .section-heading { 
        font-size: 1.75rem !important;
    }
    #home-categories .section-subheading { 
        font-size: 0.95rem !important;
    }
    .category-card { 
        border-radius: 16px !important;
        min-height: 140px !important;
        max-height: 140px !important;
    }
    .category-card > div {
        padding: 14px 18px !important;
        gap: 10px !important;
    }
    .category-card .category-icon-wrapper {
        width: 65px !important;
        height: 65px !important;
        padding: 13px !important;
        border-radius: 13px !important;
    }
    .category-card .category-icon-wrapper i {
        font-size: 1.9rem !important;
    }
    .category-card .category-icon-wrapper img {
        width: 38px !important;
        height: 38px !important;
    }
    .category-card .category-title {
        font-size: 1.15rem !important;
        font-weight: 800 !important;
    }
    .category-card p {
        font-size: 0.85rem !important;
    }
    .category-card .fa-chevron-left {
        font-size: 1rem !important;
    }
}

/* Mobile - Extra Small */
@media (max-width: 400px) {
    .category-card { 
        border-radius: 14px !important;
        min-height: 130px !important;
        max-height: 130px !important;
    }
    .category-card > div {
        padding: 12px 16px !important;
        gap: 8px !important;
    }
    .category-card .category-icon-wrapper {
        width: 60px !important;
        height: 60px !important;
        padding: 12px !important;
        border-radius: 12px !important;
    }
    .category-card .category-icon-wrapper i {
        font-size: 1.75rem !important;
    }
    .category-card .category-icon-wrapper img {
        width: 35px !important;
        height: 35px !important;
    }
    .category-card .category-title {
        font-size: 1.05rem !important;
    }
    .category-card p {
        font-size: 0.8rem !important;
    }
    .category-card .fa-chevron-left {
        font-size: 0.9rem !important;
    }
}

/* Hero Slider Styles */
.hero-slide-wrapper {
    position: relative;
    min-height: 75vh;
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    display: flex;
    align-items: center;
}

.min-vh-75 {
    min-height: 75vh;
}

.hero-slide-title {
    font-size: 3.5rem;
    font-weight: 800;
    color: white;
    text-shadow: 3px 3px 10px rgba(0, 0, 0, 0.5);
    line-height: 1.2;
}

.hero-slide-subtitle {
    font-size: 2rem;
    font-weight: 600;
    color: #f8f9fa;
    text-shadow: 2px 2px 8px rgba(0, 0, 0, 0.4);
}

.hero-slide-description {
    font-size: 1.25rem;
    color: #e9ecef;
    text-shadow: 1px 1px 5px rgba(0, 0, 0, 0.3);
    max-width: 700px;
    margin: 0 auto;
}

.carousel-indicators button {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background-color: rgba(255, 255, 255, 0.5);
    border: 2px solid white;
}

.carousel-indicators .active {
    background-color: white;
}

.carousel-control-prev-icon,
.carousel-control-next-icon {
    width: 3rem;
    height: 3rem;
    background-color: rgba(66, 52, 123, 0.7);
    border-radius: 50%;
    padding: 12px;
}

.btn-accent {
    background-color: var(--accent-color);
    color: white;
    border: none;
}

.btn-accent:hover {
    background-color: #d63c5e;
    color: white;
}

@media (max-width: 768px) {
    .hero-slide-wrapper {
        min-height: 60vh;
    }
    
    .min-vh-75 {
        min-height: 60vh;
    }
    
    .hero-slide-title {
        font-size: 2rem;
    }
    
    .hero-slide-subtitle {
        font-size: 1.25rem;
    }
    
    .hero-slide-description {
        font-size: 1rem;
    }
}
</style>

@php
    $intervalMs = isset($firstSlide) ? ($firstSlide->duration ?? 6000) : 6000;
    $hasMultipleSlides = isset($heroSlides) ? ($heroSlides->count() > 1) : false;
@endphp

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Apply dynamic backgrounds defined via data-bg-style attributes
    document.querySelectorAll('[data-bg-style]').forEach(function(el) {
        var styleText = el.getAttribute('data-bg-style');
        if (styleText) {
            el.style.cssText += (el.style.cssText ? '; ' : '') + styleText;
        }
    });

    const carousel = document.getElementById('heroCarousel');
    if (!carousel) return;

    const transitionEffect = carousel.getAttribute('data-transition-effect') || 'fade';
    carousel.classList.add('carousel-' + transitionEffect);

    const intervalMsAttr = carousel.getAttribute('data-bs-interval');
    const intervalMs = intervalMsAttr ? parseInt(intervalMsAttr, 10) : 6000;
    const hasMultipleSlides = carousel.querySelectorAll('.carousel-item').length > 1;

    const bsCarousel = new bootstrap.Carousel(carousel, {
        interval: intervalMs,
        pause: 'hover',
        wrap: true,
        touch: true
    });

    let progressBar = null;
    function animateProgressBar() {
        if (!progressBar) return;
        progressBar.style.transition = 'none';
        progressBar.style.width = '0%';
        setTimeout(() => {
            progressBar.style.transition = 'width ' + intervalMs + 'ms linear';
            progressBar.style.width = '100%';
        }, 50);
    }

    if (hasMultipleSlides) {
        progressBar = document.querySelector('.carousel-progress-bar');
        if (progressBar) {
            animateProgressBar();
            carousel.addEventListener('slide.bs.carousel', function() {
                animateProgressBar();
            });
        }
    }

    carousel.addEventListener('mouseenter', function() {
        bsCarousel.pause();
        if (hasMultipleSlides && progressBar) {
            const computedStyle = window.getComputedStyle(progressBar);
            progressBar.style.width = computedStyle.width;
            progressBar.style.transition = 'none';
        }
    });

    carousel.addEventListener('mouseleave', function() {
        bsCarousel.cycle();
        if (hasMultipleSlides && progressBar) {
            animateProgressBar();
        }
    });
});
</script>
@endpush
@endsection

@extends('layouts.app')

@section('title', 'Your Events - حوّل فعاليتك العادية إلى لحظة استثنائية')

@push('styles')
<style>
    /* Hero Carousel Advanced Transitions */
    .hero-carousel {
        position: relative;
        overflow: hidden;
        border-radius: 0;
    }
    
    .carousel-item {
        transition: opacity 0.8s ease-in-out;
    }
    
    .carousel-item.active .btn {
        animation: bounceIn 0.8s ease-out 1s both;
    }
    
    /* Button Styling */
    .hero-slide-wrapper .btn {
        padding: 16px 40px;
        font-size: clamp(1rem, 1.5vw, 1.15rem);
        font-weight: 700;
        border-radius: 50px;
        box-shadow: 0 10px 35px rgba(0, 0, 0, 0.25);
        transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }
    
    .hero-slide-wrapper .btn::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
        transition: left 0.5s ease;
    }
    
    .hero-slide-wrapper .btn:hover::before {
        left: 100%;
    }
    
    .hero-slide-wrapper .btn:hover {
        transform: translateY(-4px);
        box-shadow: 0 18px 45px rgba(0, 0, 0, 0.35);
    }
    
    .hero-slide-wrapper .btn-cta {
        background: linear-gradient(135deg, #f0c71d 0%, #ffed4e 100%);
        color: #1a1a2e;
        border: none;
    }
    
    .hero-slide-wrapper .btn-cta:hover {
        background: linear-gradient(135deg, #ffed4e 0%, #f0c71d 100%);
        color: #1a1a2e;
    }
    
    .hero-slide-wrapper .btn-outline-light {
        border: 2px solid rgba(255, 255, 255, 0.9);
        color: #ffffff;
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
    }
    
    .hero-slide-wrapper .btn-outline-light:hover {
        background: rgba(255, 255, 255, 0.25);
        border-color: #ffffff;
        color: #ffffff;
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
        width: 65px;
        height: 65px;
        top: 50%;
        transform: translateY(-50%);
        background: rgba(31, 20, 74, 0.75);
        border-radius: 50%;
        opacity: 0;
        transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
        backdrop-filter: blur(15px);
        border: 2px solid rgba(255, 255, 255, 0.15);
    }
    
    .hero-carousel:hover .carousel-control-prev,
    .hero-carousel:hover .carousel-control-next {
        opacity: 1;
    }
    
    .carousel-control-prev:hover,
    .carousel-control-next:hover {
        background: rgba(31, 20, 74, 0.95);
        transform: translateY(-50%) scale(1.15);
        box-shadow: 0 8px 25px rgba(31, 20, 74, 0.6);
        border-color: rgba(255, 255, 255, 0.3);
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
        bottom: 35px;
        margin-bottom: 0;
        z-index: 3;
        gap: 8px;
    }
    
    .carousel-indicators [data-bs-target] {
        width: 14px;
        height: 14px;
        border-radius: 50%;
        margin: 0;
        background-color: rgba(255, 255, 255, 0.4);
        border: 2px solid rgba(255, 255, 255, 0.6);
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        cursor: pointer;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
    }
    
    .carousel-indicators .active {
        width: 45px;
        border-radius: 8px;
        background: linear-gradient(135deg, #f0c71d 0%, #ffed4e 100%);
        border-color: rgba(255, 255, 255, 0.9);
        box-shadow: 0 4px 15px rgba(240, 199, 29, 0.6);
    }
    
    .carousel-indicators [data-bs-target]:hover {
        background-color: rgba(255, 255, 255, 0.7);
        transform: scale(1.15);
        border-color: rgba(255, 255, 255, 0.9);
    }
    
    /* ========== IMAGE ZOOM EFFECT ========== */
    .hero-slide-wrapper {
        transition: transform 10s ease-out;
        will-change: transform;
        min-height: 650px;
        background-size: cover;
        background-position: center;
        background-attachment: fixed;
        position: relative;
    }
    
    .hero-slide-wrapper::before {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(180deg, rgba(0, 0, 0, 0.2) 0%, rgba(0, 0, 0, 0.4) 100%);
        z-index: 1;
    }
    
    .hero-slide-wrapper > * {
        position: relative;
        z-index: 2;
    }
    
    .carousel-item.active .hero-slide-wrapper {
        transform: scale(1.08);
    }
    
    /* ========== HERO TEXT STYLING ========== */
    .hero-slide-title {
        color: #ffffff !important;
        font-weight: 800;
        text-shadow: 0 3px 15px rgba(0, 0, 0, 0.4);
    }
    
    .hero-slide-subtitle {
        color: #f0c71d !important;
        font-weight: 700;
        text-shadow: 0 2px 12px rgba(0, 0, 0, 0.3);
    }
    
    .hero-slide-description {
        color: #ffffff !important;
        font-weight: 400;
        text-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
        opacity: 0.95;
    }
    
    /* ========== MOBILE RESPONSIVE ========== */
    /* Hero Content Column */
    .hero-slide-wrapper .col-lg-8 {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: auto;
    }
    
    .min-vh-75 {
        min-height: 75vh !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
    }

    /* ====== Global Section Styling ====== */
    .section-tag-pill {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 8px 18px;
        border-radius: 999px;
        font-weight: 700;
        font-size: 0.95rem;
        letter-spacing: 0.5px;
        border: 1px solid transparent;
    }

    .section-tag-pill.teal {
        background: rgba(45, 188, 174, 0.12);
        border-color: rgba(45, 188, 174, 0.35);
        color: var(--secondary-color);
    }

    .section-tag-pill.purple {
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
        border-color: rgba(102, 126, 234, 0.2);
        color: #667eea;
    }

    .section-heading {
        color: #1a202c;
        font-weight: 800;
        line-height: 1.25;
        margin-bottom: 16px;
        font-size: clamp(1.8rem, 3vw, 2.6rem);
    }

    .section-subheading {
        color: #4a5568;
        font-size: clamp(1rem, 2vw, 1.2rem);
        font-weight: 400;
        max-width: 650px;
        margin: 0 auto;
    }

    .section-description {
        color: #718096;
        font-size: 1rem;
        line-height: 1.8;
        max-width: 720px;
        margin: 16px auto 0;
    }

    .section-body-text {
        color: #2d3748;
        line-height: 2;
        font-size: 1.08rem;
        margin-bottom: 1.25rem;
        font-weight: 400;
    }

    .section-body-text.lead {
        color: #1a202c;
        font-size: 1.15rem;
        font-weight: 600;
        line-height: 1.8;
    }

    .about-card {
        border-radius: 24px;
        overflow: hidden;
        box-shadow: 0 20px 60px rgba(15, 23, 42, 0.1);
        background: #ffffff;
        border: 1px solid rgba(226, 232, 240, 0.6);
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        z-index: 1;
    }

    .about-card:hover {
        box-shadow: 0 25px 70px rgba(15, 23, 42, 0.15);
        transform: translateY(-5px);
    }

    .about-your-events-section {
        position: relative;
        background: linear-gradient(180deg, #ffffff 0%, #f8fafb 50%, #ffffff 100%);
        overflow: hidden;
    }

    .about-your-events-section::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -10%;
        width: 40%;
        height: 150%;
        background: radial-gradient(circle, rgba(102, 126, 234, 0.05) 0%, transparent 70%);
        pointer-events: none;
    }

    .about-your-events-section::after {
        content: '';
        position: absolute;
        bottom: -50%;
        left: -10%;
        width: 40%;
        height: 150%;
        background: radial-gradient(circle, rgba(45, 188, 174, 0.04) 0%, transparent 70%);
        pointer-events: none;
    }

    .about-your-events-section .card-body {
        padding: clamp(2.5rem, 4vw, 4rem);
    }

    /* ====== Categories ====== */
    .categories-showcase .floating-shapes {
        position: absolute;
        inset: 0;
        overflow: hidden;
        pointer-events: none;
        z-index: 1;
    }

    .floating-shape {
        position: absolute;
        opacity: 0.1;
        border-radius: 30px;
        animation: float 8s ease-in-out infinite;
        filter: blur(2px);
    }

    .floating-shape.shape-purple {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    .floating-shape.shape-pink {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        border-radius: 50%;
    }

    .floating-shape.shape-blue {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    }

    .category-modern-card {
        display: block;
        height: 100%;
        text-decoration: none;
    }

    .category-card-wrapper {
        background: #ffffff;
        border-radius: 24px;
        overflow: hidden;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
        transition: all 0.45s cubic-bezier(0.4, 0, 0.2, 1);
        border: 1px solid rgba(226, 232, 240, 0.8);
        height: 100%;
        position: relative;
    }

    .category-card-wrapper:hover {
        transform: translateY(-10px) scale(1.02);
        box-shadow: 0 25px 50px rgba(102, 126, 234, 0.15);
        border-color: rgba(102, 126, 234, 0.3);
    }

    .category-header {
        padding: 36px 28px 85px;
        position: relative;
        overflow: hidden;
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        isolation: isolate;
        transition: all 0.4s ease;
    }

    .category-card-wrapper:hover .category-header {
        transform: scale(1.05);
    }

    .category-header::after {
        content: "";
        position: absolute;
        inset: 0;
        background: linear-gradient(180deg, rgba(17, 24, 39, 0.1) 0%, rgba(17, 24, 39, 0.7) 100%);
        opacity: 0.8;
        z-index: 1;
        transition: opacity 0.4s ease;
    }

    .category-card-wrapper:hover .category-header::after {
        opacity: 0.6;
    }

    .category-header.no-image::after {
        background: transparent;
        opacity: 1;
    }

    .category-header.default-gradient {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    .category-icon-wrapper {
        position: relative;
        z-index: 2;
    }

    .category-icon-bubble {
        width: 75px;
        height: 75px;
        background: rgba(255, 255, 255, 0.25);
        backdrop-filter: blur(15px);
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        border: 2px solid rgba(255, 255, 255, 0.4);
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .category-title {
        color: #ffffff;
        font-weight: 800;
        font-size: 1.55rem;
        margin: 28px 0 0;
        position: relative;
        z-index: 2;
        text-shadow: 0 3px 12px rgba(0, 0, 0, 0.3);
        letter-spacing: -0.5px;
        transition: all 0.3s ease;
    }

    .category-card-wrapper:hover .category-title {
        transform: translateY(-2px);
    }

    .category-body {
        padding: 28px;
        background: #ffffff;
        position: relative;
    }

    .category-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 6px 16px;
        border-radius: 20px;
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.08) 0%, rgba(118, 75, 162, 0.08) 100%);
        border: 1px solid rgba(102, 126, 234, 0.15);
        color: #667eea;
        font-weight: 600;
        font-size: 0.9rem;
        margin-bottom: 18px;
    }

    .category-description {
        color: #718096;
        font-size: 0.95rem;
        line-height: 1.6;
        margin-bottom: 16px;
    }

    .category-cta {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        color: #667eea;
        font-weight: 700;
        font-size: 1rem;
        transition: all 0.3s ease;
        padding: 8px 0;
    }

    .category-cta i {
        transition: transform 0.35s cubic-bezier(0.4, 0, 0.2, 1);
        font-size: 1.1rem;
    }

    .category-modern-card:hover .category-cta {
        color: #764ba2;
        gap: 14px;
    }

    .category-modern-card:hover .category-cta i {
        transform: translateX(-8px);
    }

    .category-shine {
        position: absolute;
        top: 0;
        right: -100%;
        width: 50%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.25), transparent);
        transform: skewX(-20deg);
        transition: right 0.6s ease;
        pointer-events: none;
    }

    .category-modern-card:hover .category-shine {
        right: 100%;
    }

    .category-modern-card:hover .category-icon-bubble {
        transform: scale(1.05) rotate(4deg);
    }

    .category-icon-bubble,
    .category-modern-card:hover .category-icon-bubble {
        transition: transform 0.3s ease;
    }

    /* Mobile Cards */
    .category-mobile-wrapper {
        border-radius: 18px;
        padding: 24px 18px;
        text-align: center;
        position: relative;
        overflow: hidden;
        box-shadow: 0 6px 20px rgba(102, 126, 234, 0.3);
        transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
        min-height: 150px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        border: 2px solid rgba(255, 255, 255, 0.3);
    }

    .category-mobile-wrapper.default-gradient {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    .category-mobile-wrapper .category-name {
        color: #ffffff;
        font-size: 1rem;
        font-weight: 800;
        margin-bottom: 4px;
        text-shadow: 0 1px 8px rgba(0, 0, 0, 0.25);
    }

    .category-mobile-wrapper .services-count {
        color: rgba(255, 255, 255, 0.92);
        font-size: 0.8rem;
        font-weight: 600;
    }

    .category-mobile-icon {
        position: relative;
        z-index: 2;
        margin-bottom: 8px;
    }

    .category-mobile-icon img {
        width: 54px;
        height: 54px;
        object-fit: contain;
        opacity: 0.95;
    }

    .category-mobile-icon.sm img {
        width: 36px;
        height: 36px;
    }

    .category-mobile-card:hover .category-mobile-wrapper {
        transform: translateY(-4px);
        box-shadow: 0 12px 25px rgba(102, 126, 234, 0.4);
    }

    .category-mobile-card:active .category-mobile-wrapper {
        transform: scale(0.95);
        box-shadow: 0 2px 10px rgba(102, 126, 234, 0.4);
    }

    /* Utility */
    .glass-overlay {
        position: absolute;
        inset: 0;
        pointer-events: none;
        background: linear-gradient(180deg, rgba(0, 0, 0, 0.18), rgba(0, 0, 0, 0.12));
    }

    .gradient-overlay-soft {
        position: absolute;
        inset: 0;
        background: linear-gradient(180deg, rgba(0, 0, 0, 0.12), rgba(0, 0, 0, 0.2));
        pointer-events: none;
    }

    .pattern-overlay {
        position: absolute;
        inset: 0;
        background-image:
            repeating-linear-gradient(45deg, transparent, transparent 10px, rgba(255, 255, 255, 0.06) 10px, rgba(255, 255, 255, 0.06) 20px),
            repeating-linear-gradient(-45deg, transparent, transparent 10px, rgba(255, 255, 255, 0.06) 10px, rgba(255, 255, 255, 0.06) 20px);
        pointer-events: none;
        opacity: 0.8;
    }

    .btn-custom-gradient {
        display: inline-flex;
        align-items: center;
        padding: 18px 45px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: #ffffff;
        font-weight: 700;
        font-size: 1.15rem;
        border-radius: 50px;
        text-decoration: none;
        box-shadow: 0 12px 35px rgba(102, 126, 234, 0.45);
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        border: none;
        gap: 12px;
        position: relative;
        overflow: hidden;
    }

    .btn-custom-gradient::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
        transition: left 0.6s ease;
    }

    .btn-custom-gradient:hover::before {
        left: 100%;
    }

    .btn-custom-gradient:hover {
        transform: translateY(-4px) scale(1.02);
        box-shadow: 0 18px 45px rgba(102, 126, 234, 0.55);
        color: #ffffff;
    }

    .radial-overlay {
        position: absolute;
        inset: 0;
        background-image:
            radial-gradient(circle at 20% 50%, rgba(255, 255, 255, 0.12) 0%, transparent 50%),
            radial-gradient(circle at 80% 80%, rgba(255, 255, 255, 0.12) 0%, transparent 50%);
        pointer-events: none;
    }
    @media (max-width: 768px) {
        .hero-slide-wrapper {
            min-height: 450px;
            background-attachment: scroll;
        }
        
        .hero-slide-wrapper::before {
            background: linear-gradient(180deg, rgba(0, 0, 0, 0.3) 0%, rgba(0, 0, 0, 0.5) 100%);
        }
        
        .hero-slide-title {
            font-size: clamp(2rem, 5.5vw, 2.8rem);
            text-shadow: 0 2px 20px rgba(0, 0, 0, 0.5);
        }
        
        .hero-slide-subtitle {
            font-size: clamp(1.1rem, 2.8vw, 1.5rem);
        }
        
        .hero-slide-description {
            font-size: clamp(0.95rem, 2vw, 1.1rem);
            margin-bottom: 20px !important;
        }
        
        .hero-slide-wrapper .btn {
            padding: 14px 32px;
            font-size: 1rem;
            margin: 10px 6px;
        }
        
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
        
        .min-vh-75 {
            min-height: 100vh !important;
        }
    }
    
    @media (max-width: 576px) {
        .hero-slide-wrapper {
            min-height: 320px;
        }
        
        .hero-slide-title {
            font-size: clamp(1.4rem, 4vw, 1.9rem);
        }
        
        .hero-slide-subtitle {
            font-size: clamp(0.9rem, 2vw, 1.2rem);
        }
        
        .hero-slide-description {
            font-size: clamp(0.8rem, 1.5vw, 0.95rem);
            margin-bottom: 12px !important;
        }
        
        .hero-slide-wrapper .btn {
            padding: 10px 22px;
            font-size: 0.85rem;
            margin: 6px 2px;
            display: block;
            width: 100%;
            max-width: 220px;
            margin-left: auto !important;
            margin-right: auto !important;
        }
        
        .carousel-control-prev,
        .carousel-control-next {
            width: 36px;
            height: 36px;
            opacity: 0.6;
        }
        
        .carousel-indicators {
            bottom: 10px;
        }
        
        .carousel-indicators [data-bs-target] {
            width: 6px;
            height: 6px;
            margin: 0 3px;
        }
        
        .carousel-indicators .active {
            width: 18px;
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
                                {{ str_replace('استكشف جميع الأقسام', 'استكشف جميع الخدمات', $slide->button_text) }}
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

@if($heroSlides->count() === 0)
<!-- Fallback Hero Banner when no slides are available -->
<section class="py-5" style="background: linear-gradient(135deg, #42347b 0%, #7269b0 100%); color: #fff;">
    <div class="container">
        <div class="row align-items-center min-vh-50">
            <div class="col-lg-8 mx-auto text-center">
                <h1 class="arabic-text mb-3">حوّل فعاليتك إلى لحظة استثنائية</h1>
                <p class="arabic-text mb-4">ابدأ التنظيم الآن واختر من خدماتنا وباقاتنا المتنوعة</p>
                <a href="{{ route('services.index') }}" class="btn btn-cta btn-lg arabic-text">
                    استكشف جميع الخدمات
                    <i class="fas fa-arrow-left ms-2"></i>
                </a>
            </div>
        </div>
    </div>
</section>
@endif

<!-- About: وش سالفة Your Events -->
<section class="about-your-events-section py-5 py-md-6 animated-white-bg">
    <!-- Animated floating icons -->
    <i class="fas fa-star float-icon"></i>
    <i class="fas fa-trophy float-icon"></i>
    <i class="fas fa-sparkles float-icon"></i>
    <i class="fas fa-gift float-icon"></i>
    <i class="fas fa-lightbulb float-icon"></i>
    
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10 col-xl-9">
                <div class="card border-0 about-card" data-aos="fade-up">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <span class="arabic-text section-tag-pill teal">
                                وش سالفة Your Events
                            </span>
                        </div>
                        <h2 class="arabic-text section-heading">
                            سالفتنا بدت من فكرة بسيطة…
                        </h2>
                        <p class="arabic-text section-body-text lead mb-3">
                            ليه تجهيز الفعاليات يكون متعب، والخيارات متفرّقة، والتجربة معقدة؟
                        </p>
                        <p class="arabic-text section-body-text">
                            من هنا، طلعت Your Events — منصّة سعودية جمّعنا فيها كل خدمات تجهيز الفعاليات في مكان واحد.
                        </p>
                        <p class="arabic-text section-body-text">
                            تختار، تحدد اللي تحتاجه، وتشوف الأسعار فورًا — تقدر تطلب عرض سعر أو تدفع مباشرة، وكل التفاصيل توصلك على طول في إيميلك.
                        </p>
                        <p class="arabic-text section-body-text">
                            لا وسطاء، لا انتظار، ولا مكالمات ما تخلص. كل شي واضح، وسلس، وسريع.
                        </p>
                        <p class="arabic-text section-body-text fw-semibold mb-0 text-dark">
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
        <div class="floating-shapes">
            <div class="floating-shape shape-purple" style="top: 10%; left: 5%; width: 80px; height: 80px; transform: rotate(15deg);"></div>
            <div class="floating-shape shape-pink" style="top: 60%; right: 8%; width: 100px; height: 100px; animation-direction: reverse;"></div>
            <div class="floating-shape shape-blue" style="bottom: 20%; left: 10%; width: 60px; height: 60px; transform: rotate(-20deg);"></div>
        </div>

        <!-- Section Header -->
        <div class="text-center mb-5 position-relative" style="z-index: 2;" data-aos="fade-up">
            <div class="section-tag-pill purple mb-3">
                <span class="d-inline-flex align-items-center gap-2" style="font-size: 0.9rem;">
                    <i class="fas fa-th-large"></i><span class="fw-semibold" style="letter-spacing: 1px;">استكشف عالمنا</span>
                </span>
            </div>
            <h2 class="arabic-text section-heading" style="font-size: 2.8rem;">
                {{ $categoriesSection->title ?: 'فئات الخدمات' }}
            </h2>
            <p class="arabic-text section-subheading">
                {{ $categoriesSection->subtitle ?: 'اختر ما يناسب فعاليتك من تشكيلة متنوعة' }}
            </p>
            @if($categoriesSection->content['description'] ?? null)
            <div class="arabic-text section-description">
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
                        <div class="category-card-wrapper">
                            <!-- Gradient/Image Header -->
                            <div class="category-header {{ $category->image ? '' : 'no-image default-gradient' }}"
                                @if($category->image)
                                    style="background-image: url('{{ asset('storage/' . $category->image) }}');"
                                @endif
                            >
                                @unless($category->image)
                                    <div class="pattern-overlay"></div>
                                @endunless

                                <!-- Icon Container -->
                                <div class="category-icon-wrapper">
                                    <div class="category-icon-bubble">
                                        @if($category->icon_png)
                                            <img src="{{ asset('storage/' . $category->icon_png) }}" 
                                                 alt="{{ $category->name }}" 
                                                 style="width: 48px; height: 48px; object-fit: contain;">
                                        @elseif($category->icon)
                                            <i class="{{ $category->icon }} text-white" style="font-size: 2rem;"></i>
                                        @else
                                            <i class="fas fa-star text-white" style="font-size: 2rem;"></i>
                                        @endif
                                    </div>
                                </div>

                                <!-- Title -->
                                <h3 class="arabic-text category-title">
                                    {{ $category->name }}
                                </h3>
                            </div>

                            <!-- Floating Image removed: الصورة الآن تظهر بالحجم الكامل داخل الـ category-header -->

                            <!-- Card Body -->
                            <div class="category-body">
                                <!-- Services Count Badge -->
                                @if($category->services_count ?? 0)
                                <div class="category-badge">
                                    <i class="fas fa-briefcase"></i>
                                    <span>
                                        {{ $category->services_count }} {{ __('common.service_count') }}
                                    </span>
                                </div>
                                @endif

                                <!-- Description -->
                                @if($category->description)
                                <p class="arabic-text category-description">
                                    {{ Str::limit($category->description, 80) }}
                                </p>
                                @endif

                                <!-- CTA Link -->
                                <div class="category-cta">
                                    <span>{{ __('common.explore_services') }}</span>
                                    <i class="fas fa-arrow-left category-arrow"></i>
                                </div>
                            </div>

                            <!-- Hover Shine Effect -->
                            <div class="category-shine"></div>
                        </div>
                    </a>
                </div>
            @endforeach

            <!-- ثابت: بطاقة فعالية -->
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="{{ ($categories->count() + 1) * 100 }}">
                <a href="{{ route('booking.create') }}" class="category-modern-card text-decoration-none d-block h-100">
                    <div class="category-card-wrapper">
                        <div class="category-header" style="background-image: linear-gradient(135deg, rgba(45,188,174,0.65) 0%, rgba(30,19,73,0.85) 100%), url('/storage/categories/XJKcHewH2iqtSVqeGhsnhGJZLTE5oDnNbnLCzavP.jpg');">
                            <div class="category-icon-wrapper">
                                <div class="category-icon-bubble">
                                    <img src="{{ asset('storage/category-icons/DKEmAe9M674z3c2ccsvnR2lQP1kM8I89oHNv9hoe.png') }}" alt="فعالية" style="width: 42px; height: 42px; object-fit: contain; filter: drop-shadow(0 2px 6px rgba(0,0,0,0.2));">
                                </div>
                            </div>
                            <h3 class="arabic-text category-title">فكرتك غير؟ نحولها لفعالية</h3>
                        </div>
                        <div class="category-body">
                            <div class="category-cta">
                                <span>صممها الان</span>
                                <i class="fas fa-arrow-left category-arrow"></i>
                            </div>
                        </div>
                        <div class="category-shine"></div>
                    </div>
                </a>
            </div>
        </div>

        <!-- Categories Grid - Mobile Only (Simplified Design) -->
        <div class="row g-3 position-relative d-md-none" style="z-index: 2;">
            @foreach($categories as $category)
                <div class="col-6" data-aos="fade-up" data-aos-delay="{{ $loop->index * 50 }}">
                    <a href="{{ route('services.index', ['category' => $category->id]) }}" 
                       class="category-mobile-card text-decoration-none d-block">
                        <div class="category-mobile-wrapper {{ $category->image ? 'has-image' : 'default-gradient' }}"
                            @if($category->image)
                                style="background-image: linear-gradient(180deg, rgba(0,0,0,0.25), rgba(0,0,0,0.15)), url('{{ asset('storage/' . $category->image) }}');"
                            @endif
                        >
                            @if($category->image)
                                <div class="gradient-overlay-soft"></div>
                            @else
                                <div class="radial-overlay"></div>
                            @endif

                            <!-- Icon -->
                            <div class="category-mobile-icon">
                                @if($category->icon)
                                    <i class="{{ $category->icon }}" style="font-size: 2.5rem; color: white;"></i>
                                @else
                                    <i class="fas fa-star" style="font-size: 2.5rem; color: white;"></i>
                                @endif
                            </div>

                            <!-- Category Name -->
                            <h3 class="arabic-text category-name">
                                {{ $category->name }}
                            </h3>
                        </div>
                    </a>
                </div>
            @endforeach

            <!-- ثابت الموبايل: بطاقة فعالية -->
            <div class="col-6" data-aos="fade-up" data-aos-delay="{{ ($categories->count() + 1) * 50 }}">
                <a href="{{ route('booking.create') }}" class="category-mobile-card text-decoration-none d-block">
                    <div class="category-mobile-wrapper" style="background-image: linear-gradient(135deg, rgba(45,188,174,0.6) 0%, rgba(30,19,73,0.85) 100%), url('/storage/categories/XJKcHewH2iqtSVqeGhsnhGJZLTE5oDnNbnLCzavP.jpg');">
                        <div class="gradient-overlay-soft"></div>
                        <div class="category-mobile-icon sm">
                            <i class="fas fa-lightbulb" style="font-size: 2rem; color: white;"></i>
                        </div>
                        <h3 class="arabic-text category-name">فكرتك غير؟ نحولها لفعالية</h3>
                    </div>
                </a>
            </div>
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
               class="btn-custom-gradient">
                {{ $categoriesSection->content['button_text'] }}
                <i class="fas fa-arrow-left me-2"></i>
            </a>
        </div>
        @endif
    </div>
</section>
@endif

<!-- How It Works Section (moved to be after Categories Section) -->
<section class="how-it-works-section py-5 py-md-6 animated-white-bg" style="background: linear-gradient(180deg, #ffffff 0%, #f9fbfd 50%, #ffffff 100%); position: relative;">
    <!-- Animated floating icons -->
    <i class="fas fa-check-circle float-icon"></i>
    <i class="fas fa-bolt float-icon"></i>
    <i class="fas fa-chart-line float-icon"></i>
    <i class="fas fa-handshake float-icon"></i>
    <i class="fas fa-trophy float-icon"></i>
    
    <div class="container">
        <!-- Header -->
        <div class="text-center mb-4" data-aos="fade-up">
            <span class="arabic-text d-inline-block mb-3" style="background: rgba(45,188,174,0.12); border: 1px solid rgba(45,188,174,0.35); color: var(--secondary-color); padding: 8px 16px; border-radius: 999px; font-weight: 700;">
                كيف تشتغل Your Events ؟
            </span>
            <h2 class="arabic-text fw-bold mb-2" style="color:#1a202c; font-size: clamp(1.6rem, 2.6vw, 2.2rem);">{{ __('common.no_long_explanation') }}</h2>
            <p class="arabic-text mb-0" style="color:#4a5568; font-size: 1.15rem;">{{ __('common.five_steps') }}</p>
        </div>

        <!-- Steps Grid -->
        <div class="row g-4">
            <!-- Step 1 -->
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="0">
                <div class="h-100 card border-0 shadow-sm" style="border-radius: 18px; overflow: hidden;">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-3">
                            <div style="width:42px; height:42px; border-radius: 12px; display:flex; align-items:center; justify-content:center; background: linear-gradient(135deg, var(--secondary-color), #3cc7b8); color:#fff; box-shadow: 0 8px 20px rgba(45,188,174,0.3);">1️⃣</div>
                            <h5 class="arabic-text mb-0 me-3" style="color:#1a202c; font-weight:800;">{{ __('common.browse_services') }}</h5>
                        </div>
                        <p class="arabic-text mb-0" style="color:#2d3748; line-height:1.9;">{{ __('common.choose_services') }}</p>
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
                        {{ __('common.tagline') }}
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
                                            <!-- Badge: ثابت أعلى يسار بلون واحد -->
                                            @if($service->type)
                                                <span class="badge bg-primary position-absolute top-0 start-0 m-2" style="background-color: rgb(13,110,253) !important; z-index: 10;">
                                                    {{ $service->type }}
                                                </span>
                                            @endif

                                            <!-- Service Image (square on mobile) -->
                                            <div class="service-image homepage-image-wrapper mb-3">
                                                <img src="{{ $service->thumbnail_url }}" 
                                                     alt="{{ $service->name }}"
                                                     class="homepage-image">
                                            </div>

                                            <!-- Card Body -->
                                            <div class="card-body p-3">
                                                <h5 class="arabic-text mb-2" style="color: #1a202c; font-weight: 700; font-size: 1.1rem; line-height: 1.4;">
                                                    {{ Str::limit($service->name, 50) }}
                                                </h5>
                                                
                                                <!-- تم حذف العنوان الفرعي الخاص بنوع الجهاز للعرض في الصفحة الرئيسية -->

                                                <div class="d-flex justify-content-between align-items-center mt-1">
                                                    @if($service->price)
                                                    <div>
                                                        <span class="text-primary fw-bold" style="font-size: 1.1rem;">
                                                            {{ number_format((float)$service->price, 2) }} ر.س
                                                        </span>
                                                    </div>
                                                    @endif

                                                    @if(method_exists($service, 'isVariable') && $service->isVariable())
                                                        <a href="{{ route('services.show', $service->id) }}" class="btn btn-sm btn-primary rounded-pill px-3">{{ __('common.select_option') }}</a>
                                                    @else
                                                        <form action="{{ route('cart.add', $service) }}" method="POST" class="m-0">
                                                            @csrf
                                                            <input type="hidden" name="quantity" value="1">
                                                            <button type="submit" class="btn btn-sm btn-primary rounded-pill px-3">أضف للسلة</button>
                                                        </form>
                                                    @endif
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
/* Unified Styles: carousel, badge, and service images */
/* Services Carousel */
.klb-products-tab.module-body { position: relative; }
.klb-slider-wrapper { overflow: hidden; }
.slider-loaded { opacity: 1; transition: opacity 0.3s ease; }
.slick-list.draggable { overflow: hidden; position: relative; cursor: grab; touch-action: pan-y; }
.slick-list.draggable:active { cursor: grabbing; }
.services-carousel-wrapper { position: relative; padding: 0 50px; }
.services-carousel-track { display: flex; gap: 20px; overflow-x: auto; scroll-behavior: smooth; padding: 10px 5px; scrollbar-width: none; }
.services-carousel-track::-webkit-scrollbar { display: none; }
.service-card-carousel { flex: 0 0 280px; min-width: 280px; }
.service-card-carousel .card:hover { transform: translateY(-8px); box-shadow: 0 12px 30px rgba(0,0,0,0.15) !important; }
.carousel-nav { position: absolute; top: 50%; transform: translateY(-50%); width: 45px; height: 45px; border-radius: 50%; background: #fff; border: 2px solid #e2e8f0; color: #667eea; display: flex; align-items: center; justify-content: center; cursor: pointer; z-index: 10; transition: all 0.3s ease; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
.carousel-nav:hover { background: #667eea; color: #fff; border-color: #667eea; transform: translateY(-50%) scale(1.1); }
.carousel-nav-prev { right: 0; }
.carousel-nav-next { left: 0; }

/* Badge style-1 */
.badge.style-1 {
  background-color: var(--badge-bg, #667eea);
  color: #fff;
  padding: 8px 16px;
  font-size: 0.75rem;
  border: none;
  box-shadow: 0 6px 18px rgba(0,0,0,0.15);
  letter-spacing: 0.3px;
  display: inline-block;
}

/* Homepage service image */
.homepage-image-wrapper {
  height: 220px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  position: relative;
  overflow: hidden;
  border-radius: 16px;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

.homepage-image { 
  width: 100%; 
  height: 100%; 
  object-fit: cover;
  transition: transform 0.4s ease;
}

.service-card:hover .homepage-image {
  transform: scale(1.08);
}

/* Reduce empty space after removing subtitle */
.service-card-carousel .card-body { padding: 12px 16px 6px; }
.service-card-carousel .card-body h5 { margin-bottom: 4px !important; }

/* Responsive */
@media (max-width: 768px) {
  .services-carousel-wrapper { padding: 0 10px; }
  .services-carousel-track { gap: 12px; padding: 10px 5px; }
  /* خدمتين بجانب بعض على الموبايل */
  .service-card-carousel { flex: 0 0 calc(50% - 6px); min-width: calc(50% - 6px); }
  .service-card-carousel .card { border-radius: 12px !important; }
  .service-image { height: auto !important; }
  .card-body { padding: 12px !important; }
  .card-body h5 { font-size: 0.95rem !important; }
  .card-body p { font-size: 0.8rem !important; }
  .btn-sm { font-size: 0.75rem !important; padding: 6px 12px !important; }
  .carousel-nav { width: 32px; height: 32px; font-size: 0.75rem; }
  .carousel-nav-prev { right: -5px; }
  .carousel-nav-next { left: -5px; }
  .klb-module .d-flex.justify-content-between { flex-direction: column; align-items: flex-start !important; gap: 15px; }
  /* Mobile square images */
  .homepage-image-wrapper { width: 100%; aspect-ratio: 1 / 1; height: auto !important; margin-bottom: 12px; }
  .homepage-image { width: 100% !important; height: 100% !important; object-fit: cover !important; }

  /* Center service card content on mobile */
  .service-card-carousel .card-body { padding: 8px 10px 4px !important; }
  .service-card-carousel .card-body { text-align: center; }
  .service-card-carousel .card-body h5 { text-align: center; margin-bottom: 2px !important; }
  .service-card-carousel .card-body .d-flex.justify-content-between { flex-direction: column; justify-content: center !important; align-items: center !important; gap: 6px; margin-top: 0 !important; }
  .service-card-carousel .card-body .text-primary { display: block; }
}

/* إخفاء الأسهم على الموبايل الصغير */
@media (max-width: 576px) {
  .carousel-nav { display: none; }
  .services-carousel-wrapper { padding: 0; }
  /* Extra compression on very small screens */
  .service-card-carousel .card-body { padding-bottom: 4px !important; }
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
@keyframes float {
    0%, 100% { transform: translateY(0) rotate(0deg); }
    50% { transform: translateY(-20px) rotate(5deg); }
}

.category-mobile-card:active .category-mobile-wrapper {
    transform: scale(0.95);
    box-shadow: 0 2px 10px rgba(102, 126, 234, 0.35);
}

@media (hover: hover) {
    .category-mobile-card:hover .category-mobile-wrapper {
        transform: translateY(-4px);
        box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
    }
}

@media (max-width: 992px) {
    .category-header {
        padding: 28px 22px 72px !important;
    }

    .category-icon-bubble {
        width: 64px !important;
        height: 64px !important;
    }

    .category-title {
        font-size: 1.35rem !important;
    }
}

@media (max-width: 768px) {
    .categories-showcase .container {
        padding-left: 18px !important;
        padding-right: 18px !important;
    }

    .floating-shapes { display: none; }

    .category-header {
        padding: 28px 22px 70px !important;
    }

    .category-body {
        padding: 24px !important;
    }

    .category-title {
        font-size: 1.35rem !important;
    }

    .category-description {
        font-size: 0.98rem !important;
    }

    .btn-custom-gradient {
        width: 100%;
        max-width: 340px;
        justify-content: center;
        padding: 16px 36px !important;
        font-size: 1.05rem !important;
    }

    .section-heading {
        font-size: clamp(2rem, 5.5vw, 2.6rem) !important;
    }
}

@media (max-width: 576px) {
    .category-header {
        padding: 20px 16px 56px !important;
    }

    .category-icon-bubble {
        width: 55px !important;
        height: 55px !important;
    }

    .category-title {
        font-size: 1.2rem !important;
    }

    .category-body {
        padding: 16px !important;
    }

    .section-heading {
        font-size: clamp(1.6rem, 5vw, 2rem);
    }

    .category-mobile-wrapper {
        min-height: 130px;
    }
}
</style>

<!-- Services Section -->
@php
    $servicesSection = $sections->firstWhere('section_key', 'services');
    $servicesBgStyle = $servicesSection ? ($servicesSection->getBackgroundStyle() ?: 'background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 50%, #e9ecef 100%);') : 'background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 50%, #e9ecef 100%);';
@endphp
@if($servicesSection && $servicesSection->is_active)
<section class="py-4 animated-white-bg" id="services" data-bg-style="{{ $servicesBgStyle }}" style="position: relative;">
    <!-- Animated floating icons -->
    <i class="fas fa-rocket float-icon"></i>
    <i class="fas fa-users-cog float-icon"></i>
    <i class="fas fa-gem float-icon"></i>
    <i class="fas fa-star float-icon"></i>
    <i class="fas fa-magic float-icon"></i>
    
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
                    <div class="service-image mb-3 homepage-image-wrapper" style="width: 100%; height: 200px; border-radius: 15px; overflow: hidden;">
                        <img src="{{ $service->thumbnail_url }}" alt="{{ $service->name }}" class="homepage-image" style="width: 100%; height: 100%; object-fit: cover;">
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
                {{ __('common.explore_all_categories') }}
            </a>
        </div>
    </div>
</section>

<style>
.service-card {
    background: #ffffff;
    border-radius: 24px;
    padding: 32px 24px;
    border: 1px solid rgba(226, 232, 240, 0.6);
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    height: 100%;
    box-shadow: 0 8px 25px rgba(15, 23, 42, 0.08);
    margin-bottom: 20px;
    position: relative;
    overflow: hidden;
}

.service-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
    opacity: 0;
    transition: opacity 0.4s ease;
}

.service-card:hover::before {
    opacity: 1;
}

.service-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 15px 40px rgba(102, 126, 234, 0.15);
    border-color: rgba(102, 126, 234, 0.3);
}

/* Service card mobile responsiveness */
@media (max-width: 991px) {
    .service-card {
        padding: 28px 18px;
        margin-bottom: 28px;
    }
    
    .homepage-image-wrapper {
        height: 200px;
        border-radius: 14px;
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
<section class="py-4 animated-white-bg" id="packages" data-bg-style="{{ $packagesSection->getBackgroundStyle() ?: 'background: #f8f9fa;' }}" style="position: relative;">
    <!-- Animated floating icons -->
    <i class="fas fa-gift float-icon"></i>
    <i class="fas fa-crown float-icon"></i>
    <i class="fas fa-bolt float-icon"></i>
    <i class="fas fa-fire float-icon"></i>
    <i class="fas fa-sparkles float-icon"></i>
    
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

<!-- VR Experience Section - Modern Carousel -->
<section class="vr-experience-section py-6" style="background: linear-gradient(135deg, #ffffff 0%, #f9fbfd 50%, #ffffff 100%); position: relative; overflow: hidden; padding-top: 80px !important; padding-bottom: 80px !important;">
    <div class="container">
        <div class="row align-items-center g-4">
            <!-- Left: Image Carousel -->
            <div class="col-lg-6 col-12" data-aos="fade-right">
                <div class="vr-carousel-wrapper" style="position: relative;">
                    <!-- Image Carousel -->
                    <div class="vr-carousel" id="vrCarousel" style="
                        position: relative;
                        width: 100%;
                        height: 400px;
                        border-radius: 24px;
                        overflow: hidden;
                        box-shadow: 0 20px 60px rgba(45, 188, 174, 0.25);
                        border: 3px solid rgba(45, 188, 174, 0.15);
                    ">
                        <!-- Slide 1: Unified Services -->
                        <div class="vr-slide vr-slide-active" style="
                            position: absolute;
                            width: 100%;
                            height: 100%;
                            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            opacity: 1;
                            transition: opacity 0.8s ease-in-out;
                            color: white;
                        ">
                            <div style="text-align: center; padding: 40px;">
                                <i class="fas fa-cubes" style="font-size: 80px; margin-bottom: 20px; opacity: 0.9;"></i>
                                <h3 class="arabic-text" style="font-size: 1.8rem; font-weight: 800; margin-bottom: 10px;">كل الخدمات في مكان واحد</h3>
                                <p class="arabic-text" style="font-size: 1rem; opacity: 0.95;">لا تحتاج تدور ولا تنتظر</p>
                            </div>
                        </div>

                        <!-- Slide 2: Simple Steps -->
                        <div class="vr-slide" style="
                            position: absolute;
                            width: 100%;
                            height: 100%;
                            background: linear-gradient(135deg, #2dbcae 0%, #3cc7b8 100%);
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            opacity: 0;
                            transition: opacity 0.8s ease-in-out;
                            color: white;
                        ">
                            <div style="text-align: center; padding: 40px;">
                                <i class="fas fa-list-check" style="font-size: 80px; margin-bottom: 20px; opacity: 0.9;"></i>
                                <h3 class="arabic-text" style="font-size: 1.8rem; font-weight: 800; margin-bottom: 10px;">خطوات بسيطة وواضحة</h3>
                                <p class="arabic-text" style="font-size: 1rem; opacity: 0.95;">اختر الخدمة وطلبها بسهولة</p>
                            </div>
                        </div>

                        <!-- Slide 3: Clear Pricing -->
                        <div class="vr-slide" style="
                            position: absolute;
                            width: 100%;
                            height: 100%;
                            background: linear-gradient(135deg, #f0c71d 0%, #f5a623 100%);
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            opacity: 0;
                            transition: opacity 0.8s ease-in-out;
                            color: #1a202c;
                        ">
                            <div style="text-align: center; padding: 40px;">
                                <i class="fas fa-tag" style="font-size: 80px; margin-bottom: 20px; opacity: 0.9;"></i>
                                <h3 class="arabic-text" style="font-size: 1.8rem; font-weight: 800; margin-bottom: 10px;">أسعار واضحة من البداية</h3>
                                <p class="arabic-text" style="font-size: 1rem; opacity: 0.95;">دفع إلكتروني وسريع وآمن</p>
                            </div>
                        </div>

                        <!-- Slide 4: Official Invoices -->
                        <div class="vr-slide" style="
                            position: absolute;
                            width: 100%;
                            height: 100%;
                            background: linear-gradient(135deg, #ef4870 0%, #f56b8a 100%);
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            opacity: 0;
                            transition: opacity 0.8s ease-in-out;
                            color: white;
                        ">
                            <div style="text-align: center; padding: 40px;">
                                <i class="fas fa-file-invoice" style="font-size: 80px; margin-bottom: 20px; opacity: 0.9;"></i>
                                <h3 class="arabic-text" style="font-size: 1.8rem; font-weight: 800; margin-bottom: 10px;">فواتير رسميّة</h3>
                                <p class="arabic-text" style="font-size: 1rem; opacity: 0.95;">تنفيذ مضمون في وقته</p>
                            </div>
                        </div>

                        <!-- Slide 5: Premium Vendors -->
                        <div class="vr-slide" style="
                            position: absolute;
                            width: 100%;
                            height: 100%;
                            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            opacity: 0;
                            transition: opacity 0.8s ease-in-out;
                            color: #1a202c;
                        ">
                            <div style="text-align: center; padding: 40px;">
                                <i class="fas fa-star" style="font-size: 80px; margin-bottom: 20px; opacity: 0.9;"></i>
                                <h3 class="arabic-text" style="font-size: 1.8rem; font-weight: 800; margin-bottom: 10px;">موردين مختارين</h3>
                                <p class="arabic-text" style="font-size: 1rem; opacity: 0.95;">أعلى مستوى من الجودة والاحترافية</p>
                            </div>
                        </div>
                    </div>

                    <!-- Carousel Indicators -->
                    <div class="vr-indicators" style="
                        display: flex;
                        justify-content: center;
                        gap: 10px;
                        margin-top: 20px;
                    ">
                        <button class="vr-indicator active" data-slide="0" style="
                            width: 12px;
                            height: 12px;
                            border-radius: 50%;
                            background: #667eea;
                            border: 2px solid transparent;
                            cursor: pointer;
                            transition: all 0.3s ease;
                        "></button>
                        <button class="vr-indicator" data-slide="1" style="
                            width: 12px;
                            height: 12px;
                            border-radius: 50%;
                            background: rgba(45, 188, 174, 0.4);
                            border: 2px solid transparent;
                            cursor: pointer;
                            transition: all 0.3s ease;
                        "></button>
                        <button class="vr-indicator" data-slide="2" style="
                            width: 12px;
                            height: 12px;
                            border-radius: 50%;
                            background: rgba(240, 199, 29, 0.4);
                            border: 2px solid transparent;
                            cursor: pointer;
                            transition: all 0.3s ease;
                        "></button>
                        <button class="vr-indicator" data-slide="3" style="
                            width: 12px;
                            height: 12px;
                            border-radius: 50%;
                            background: rgba(239, 72, 112, 0.4);
                            border: 2px solid transparent;
                            cursor: pointer;
                            transition: all 0.3s ease;
                        "></button>
                        <button class="vr-indicator" data-slide="4" style="
                            width: 12px;
                            height: 12px;
                            border-radius: 50%;
                            background: rgba(79, 172, 254, 0.4);
                            border: 2px solid transparent;
                            cursor: pointer;
                            transition: all 0.3s ease;
                        "></button>
                    </div>
                </div>
            </div>

            <!-- Right: Content -->
            <div class="col-lg-6 col-12" data-aos="fade-left">
                <div class="vr-content" style="text-align: right; direction: rtl;">
                    <h2 class="arabic-text mb-4" style="
                        color: var(--primary-color);
                        font-size: 2.5rem;
                        font-weight: 900;
                        line-height: 1.2;
                    ">
                        لأننا نحب نسهّلها عليك…
                    </h2>

                    <p class="arabic-text mb-3" style="
                        color: #2d3748;
                        font-size: 1.15rem;
                        line-height: 1.8;
                    ">
                        ونخلّي التجهيز تمشّي بالساهل
                    </p>

                    <!-- Key Features -->
                    <div class="vr-features" style="margin-bottom: 30px;">
                        <div class="vr-feature mb-3 d-flex gap-3" style="align-items: flex-start;">
                            <div style="
                                flex-shrink: 0;
                                width: 44px;
                                height: 44px;
                                background: linear-gradient(135deg, rgba(45, 188, 174, 0.15), rgba(45, 188, 174, 0.25));
                                border-radius: 12px;
                                display: flex;
                                align-items: center;
                                justify-content: center;
                                color: var(--secondary-color);
                                font-weight: 700;
                                font-size: 1.2rem;
                                box-shadow: 0 2px 8px rgba(45, 188, 174, 0.2);
                            ">✓</div>
                            <p class="arabic-text mb-0" style="color: #2d3748; font-size: 1.05rem; line-height: 1.7; font-weight: 500;">
                                كل خدمات تجهيز الفعاليات في مكان واحد، ما تحتاج تدور ولا تنتظر.
                            </p>
                        </div>

                        <div class="vr-feature mb-3 d-flex gap-3" style="align-items: flex-start;">
                            <div style="
                                flex-shrink: 0;
                                width: 44px;
                                height: 44px;
                                background: linear-gradient(135deg, rgba(102, 126, 234, 0.15), rgba(102, 126, 234, 0.25));
                                border-radius: 12px;
                                display: flex;
                                align-items: center;
                                justify-content: center;
                                color: #667eea;
                                font-weight: 700;
                                font-size: 1.2rem;
                                box-shadow: 0 2px 8px rgba(102, 126, 234, 0.2);
                            ">✓</div>
                            <p class="arabic-text mb-0" style="color: #2d3748; font-size: 1.05rem; line-height: 1.7; font-weight: 500;">
                                تختار الخدمة، وتطلبها بخطوات بسيطة وواضحة.
                            </p>
                        </div>

                        <div class="vr-feature mb-3 d-flex gap-3" style="align-items: flex-start;">
                            <div style="
                                flex-shrink: 0;
                                width: 44px;
                                height: 44px;
                                background: linear-gradient(135deg, rgba(240, 199, 29, 0.15), rgba(240, 199, 29, 0.25));
                                border-radius: 12px;
                                display: flex;
                                align-items: center;
                                justify-content: center;
                                color: #f0c71d;
                                font-weight: 700;
                                font-size: 1.2rem;
                                box-shadow: 0 2px 8px rgba(240, 199, 29, 0.2);
                            ">✓</div>
                            <p class="arabic-text mb-0" style="color: #2d3748; font-size: 1.05rem; line-height: 1.7; font-weight: 500;">
                                الأسعار واضحة من البداية، والدفع إلكتروني وسريع.
                            </p>
                        </div>

                        <div class="vr-feature mb-3 d-flex gap-3" style="align-items: flex-start;">
                            <div style="
                                flex-shrink: 0;
                                width: 44px;
                                height: 44px;
                                background: linear-gradient(135deg, rgba(239, 72, 112, 0.15), rgba(239, 72, 112, 0.25));
                                border-radius: 12px;
                                display: flex;
                                align-items: center;
                                justify-content: center;
                                color: #ef4870;
                                font-weight: 700;
                                font-size: 1.2rem;
                                box-shadow: 0 2px 8px rgba(239, 72, 112, 0.2);
                            ">✓</div>
                            <p class="arabic-text mb-0" style="color: #2d3748; font-size: 1.05rem; line-height: 1.7; font-weight: 500;">
                                فواتير رسميّة، وتنفيذ مضمون في وقته.
                            </p>
                        </div>

                        <div class="vr-feature d-flex gap-3" style="align-items: flex-start;">
                            <div style="
                                flex-shrink: 0;
                                width: 44px;
                                height: 44px;
                                background: linear-gradient(135deg, rgba(79, 172, 254, 0.15), rgba(79, 172, 254, 0.25));
                                border-radius: 12px;
                                display: flex;
                                align-items: center;
                                justify-content: center;
                                color: #4facfe;
                                font-weight: 700;
                                font-size: 1.2rem;
                                box-shadow: 0 2px 8px rgba(79, 172, 254, 0.2);
                            ">✓</div>
                            <p class="arabic-text mb-0" style="color: #2d3748; font-size: 1.05rem; line-height: 1.7; font-weight: 500;">
                                وموردين مختارين بعناية عشان تطلع الفعالية على أعلى مستوى.
                            </p>
                        </div>
                    </div>

                    <!-- CTA Buttons -->
                    <div class="d-flex gap-3 flex-wrap">
                        <a href="{{ route('booking.create') }}" class="btn btn-gold" style="
                            box-shadow: 0 8px 25px rgba(240, 199, 29, 0.4);
                            border-radius: 25px;
                            padding: 14px 32px;
                            font-weight: 700;
                        ">
                            <i class="fas fa-calendar-check me-2"></i>احجز الآن
                        </a>
                        <a href="{{ route('services.index') }}" class="btn btn-outline-primary" style="
                            border: 2px solid var(--primary-color);
                            color: var(--primary-color);
                            border-radius: 25px;
                            padding: 14px 28px;
                            background: rgba(31, 20, 74, 0.05);
                            transition: all 0.3s ease;
                            font-weight: 600;
                        ">
                            <i class="fas fa-arrow-left me-2"></i>استكشف الخدمات
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
// VR Carousel Auto-slide
document.addEventListener('DOMContentLoaded', function() {
    let currentSlide = 0;
    const slides = document.querySelectorAll('.vr-slide');
    const indicators = document.querySelectorAll('.vr-indicator');
    const totalSlides = slides.length;

    function showSlide(n) {
        slides.forEach(slide => {
            slide.style.opacity = '0';
        });
        indicators.forEach((indicator, index) => {
            indicator.style.background = index === n ? 
                ['#667eea', '#2dbcae', '#f0c71d', '#ef4870', '#4facfe'][n] : 
                ['rgba(102, 126, 234, 0.4)', 'rgba(45, 188, 174, 0.4)', 'rgba(240, 199, 29, 0.4)', 'rgba(239, 72, 112, 0.4)', 'rgba(79, 172, 254, 0.4)'][index];
            indicator.classList.remove('active');
        });

        slides[n].style.opacity = '1';
        indicators[n].classList.add('active');
        indicators[n].style.background = ['#667eea', '#2dbcae', '#f0c71d', '#ef4870', '#4facfe'][n];
    }

    // Auto-slide every 3.5 seconds
    setInterval(() => {
        currentSlide = (currentSlide + 1) % totalSlides;
        showSlide(currentSlide);
    }, 3500);

    // Manual indicator click
    indicators.forEach((indicator, index) => {
        indicator.addEventListener('click', () => {
            currentSlide = index;
            showSlide(currentSlide);
        });
    });
});
</script>

<!-- Enhanced CTA Section (Refactored) - Based on Promotional Banner Design -->
<section class="cta-section-refactored" style="
    background: linear-gradient(135deg, #8B2D5D 0%, #6B4A7F 50%, #4A2E5F 100%);
    position: relative;
    overflow: hidden;
    color: white;
    width: 100%;
    height: 470px;
    display: flex;
    align-items: center;
    justify-content: center;
">
    <!-- Animated Gradient Background Elements -->
    <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; z-index: 1; overflow: hidden;">
        <div style="position: absolute; top: -20%; right: -10%; width: 400px; height: 400px; background: radial-gradient(circle, rgba(240, 199, 29, 0.12) 0%, transparent 70%); border-radius: 50%;"></div>
        <div style="position: absolute; bottom: -20%; left: -10%; width: 350px; height: 350px; background: radial-gradient(circle, rgba(239, 72, 112, 0.1) 0%, transparent 70%); border-radius: 50%;"></div>
    </div>

    <div class="container-fluid position-relative" style="z-index: 2; max-width: 1427px; width: 100%; margin: 0 auto;">
        <div class="row align-items-center g-4 g-lg-5 h-100" style="height: 100%;">
            
            <!-- Left Content - Text Section -->
            <div class="col-lg-6 col-12 d-flex align-items-center" data-aos="fade-right">
                <div class="cta-content-refactored" style="direction: rtl; text-align: right; width: 100%; padding: 0 30px;">
                    
                    <!-- Main Headline -->
                    <h1 style="
                        font-size: clamp(2.5rem, 5.5vw, 3.8rem);
                        font-weight: 900;
                        line-height: 1.2;
                        color: #ffffff;
                        margin-bottom: 20px;
                        text-shadow: 0 3px 15px rgba(0, 0, 0, 0.4);
                        letter-spacing: -0.5px;
                    ">
                        جاهز تبدأ؟
                    </h1>

                    <!-- Main Tagline - Yellow/Gold -->
                    <p style="
                        font-size: clamp(1.05rem, 2vw, 1.3rem);
                        line-height: 1.7;
                        color: #f0c71d;
                        margin-bottom: 22px;
                        font-weight: 600;
                        font-style: italic;
                    ">
                        خلاص لا تشيل هم ولا تضيع وقتك، كل شي جاهز، وكل خطوة أسهل من اللي قبلها. تختار وتطلب، وتبدأ.. والباقي على Your Events.
                    </p>

                    <!-- Main Marketing Message -->
                    <p style="
                        font-size: clamp(1.1rem, 2.2vw, 1.35rem);
                        line-height: 1.8;
                        color: #ffffff;
                        margin-bottom: 28px;
                        font-weight: 700;
                        letter-spacing: 0.5px;
                    ">
                        فعالياتك تستاهل البداية الصح و Your Events دايم تبدأها معك.
                    </p>

                    <!-- CTA Buttons -->
                    <div class="d-flex gap-3 flex-wrap" style="align-items: center; margin-top: 8px;">
                        <!-- Primary Button - Book Now -->
                        <a href="{{ route('services.index') }}" class="btn-cta-primary" style="
                            display: inline-flex;
                            align-items: center;
                            justify-content: center;
                            gap: 10px;
                            background: linear-gradient(135deg, #f0c71d 0%, #f5a623 100%);
                            color: #1a1a2e !important;
                            padding: 16px 45px;
                            border-radius: 50px;
                            font-size: 1.1rem;
                            font-weight: 700;
                            border: none;
                            text-decoration: none;
                            box-shadow: 0 12px 35px rgba(240, 199, 29, 0.4);
                            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                            cursor: pointer;
                            white-space: nowrap;
                        ">
                            <i class="fas fa-calendar-check"></i>احجز الآن
                        </a>

                        <!-- Secondary Button - Contact -->
                        <a href="#contact" class="btn-cta-secondary" style="
                            display: inline-flex;
                            align-items: center;
                            justify-content: center;
                            gap: 10px;
                            border: 2px solid #ffffff;
                            background: transparent;
                            color: #ffffff !important;
                            padding: 14px 35px;
                            border-radius: 50px;
                            font-size: 1.05rem;
                            font-weight: 700;
                            text-decoration: none;
                            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                            cursor: pointer;
                            white-space: nowrap;
                        ">
                            <i class="fas fa-phone"></i>تواصل معنا
                        </a>
                    </div>

                </div>
            </div>

            <!-- Right Image - Hand with Phone -->
            <div class="col-lg-6 col-12 d-flex align-items-center justify-content-center" data-aos="fade-left" style="margin-top: 40px;">
                <div class="phone-image-wrapper" style="position: relative; display: inline-block; width: 100%; max-width: 480px; height: auto;">
                    <img src="{{ asset('images/vr/hand.png') }}" 
                         alt="تطبيق Your Events على الهاتف الذكي - يد تمسك هاتفًا ذكيًا" 
                         class="img-fluid phone-image" 
                         loading="lazy" 
                         decoding="async"
                         style="
                            width: 100%;
                            height: auto;
                            max-width: 100%;
                            display: block;
                            filter: drop-shadow(0 25px 60px rgba(0, 0, 0, 0.6));
                            animation: float-phone 3s ease-in-out infinite;
                            object-fit: contain;
                         ">
                </div>
            </div>

        </div>
    </div>

    <!-- Animation Keyframes -->
    <style>
        @keyframes float-phone {
            0%, 100% {
                transform: translateY(0px) scale(1);
            }
            50% {
                transform: translateY(-10px) scale(1.01);
            }
        }

        /* Button Hover Effects */
        .btn-cta-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 40px rgba(240, 199, 29, 0.45);
            background: linear-gradient(135deg, #f5a623 0%, #f0c71d 100%);
        }

        .btn-cta-primary:active {
            transform: translateY(-1px);
        }

        .btn-cta-secondary:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: #ffffff;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(255, 255, 255, 0.2);
        }

        .btn-cta-secondary:active {
            transform: translateY(0);
        }

        /* Responsive Design */
        @media (max-width: 992px) {
            .cta-section-refactored {
                height: auto;
                min-height: 420px;
                display: flex;
                align-items: center;
            }

            .cta-content-refactored {
                text-align: center;
                direction: ltr;
            }

            .cta-content-refactored h1 {
                margin-bottom: 12px;
            }

            .phone-image-wrapper {
                margin-top: 30px;
                max-width: 70%;
            }
        }

        @media (max-width: 768px) {
            .cta-section-refactored {
                height: auto;
                min-height: 380px;
                padding: 2rem 0 !important;
                display: flex;
                align-items: center;
            }

            .cta-content-refactored {
                margin-bottom: 20px;
                padding: 0 20px !important;
            }

            .cta-content-refactored h1 {
                font-size: 1.8rem !important;
                margin-bottom: 8px;
            }

            .cta-content-refactored p:first-of-type {
                font-size: 0.9rem !important;
                margin-bottom: 12px;
            }

            .cta-content-refactored p:nth-of-type(2) {
                font-size: 1rem !important;
                margin-bottom: 16px;
            }

            .d-flex.gap-3.flex-wrap {
                flex-direction: column !important;
                width: 100%;
                gap: 10px !important;
            }

            .btn-cta-primary,
            .btn-cta-secondary {
                width: 100%;
                padding: 12px 28px !important;
                font-size: 0.9rem !important;
            }

            .phone-image-wrapper {
                max-width: 80%;
                width: 100%;
            }
        }

        @media (max-width: 576px) {
            .cta-section-refactored {
                height: auto;
                min-height: 350px;
                padding: 1.5rem 0 !important;
            }

            .cta-content-refactored {
                padding: 0 16px !important;
            }

            .cta-content-refactored h1 {
                font-size: 1.6rem !important;
            }

            .cta-content-refactored p:first-of-type {
                font-size: 0.85rem !important;
                line-height: 1.5;
            }

            .cta-content-refactored p:nth-of-type(2) {
                font-size: 0.95rem !important;
            }

            .btn-cta-primary,
            .btn-cta-secondary {
                font-size: 0.85rem !important;
                padding: 10px 20px !important;
            }

            .btn-cta-primary i,
            .btn-cta-secondary i {
                font-size: 0.8rem;
            }

            .phone-image-wrapper {
                max-width: 90%;
            }
        }
    </style>
</section>

<!-- Gallery Section -->
@endsection







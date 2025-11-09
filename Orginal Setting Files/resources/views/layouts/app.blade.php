<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', 'Your Events - حوّل مناسبتك العادية إلى لحظة استثنائية')</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Amiri:wght@400;700&family=Tajawal:wght@200;300;400;500;700;800;900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- AOS Animation -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #1f144a;
            --accent-color: #ef4870;
            --secondary-color: #2dbcae;
            --gold-color: #f0c71d;
            --purple-light: #7269b0;
            --bg-light: #ffffff;
            --bg-secondary: #f8f9fa;
            --text-color: #222222;
            --text-muted: #666666;
            --border-color: #e9ecef;
            --gradient-primary: linear-gradient(135deg, #1f144a 0%, #2d1a5e 50%, #3d2a7e 100%);
            --gradient-accent: linear-gradient(135deg, #ef4870 0%, #f56b8a 50%, #ff7ba3 100%);
            --gradient-secondary: linear-gradient(135deg, #2dbcae 0%, #3cc7b8 50%, #4dd2c2 100%);
            --gradient-gold: linear-gradient(135deg, #f0c71d 0%, #f5d347 50%, #fae071 100%);
            --gradient-rainbow: linear-gradient(135deg, #ef4870 0%, #f0c71d 25%, #2dbcae 50%, #7269b0 75%, #1f144a 100%);
            --shadow-primary: 0 15px 50px rgba(31, 20, 74, 0.4);
            --shadow-accent: 0 15px 50px rgba(239, 72, 112, 0.4);
            --shadow-glow: 0 0 30px rgba(239, 72, 112, 0.3);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Tajawal', 'Inter', sans-serif;
            background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 50%, #e9ecef 100%);
            color: var(--text-color);
            line-height: 1.6;
            overflow-x: hidden;
            position: relative;
            padding-top: 80px; /* مساحة للـ fixed navbar */
        }
        
        /* حماية المحتوى من التداخل مع القوائم المنسدلة */
        main, .main-content, .container, .hero-section {
            position: relative;
            z-index: 1; /* أقل من navbar و dropdowns */
        }
        
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle at 20% 80%, rgba(239, 72, 112, 0.1) 0%, transparent 50%),
                        radial-gradient(circle at 80% 20%, rgba(45, 188, 174, 0.1) 0%, transparent 50%),
                        radial-gradient(circle at 40% 40%, rgba(240, 199, 29, 0.05) 0%, transparent 50%);
            pointer-events: none;
            z-index: -1;
        }
        
        .arabic-text {
            font-family: 'Tajawal', sans-serif;
        }
        
        .english-text {
            font-family: 'Inter', sans-serif;
        }
        
        /* Navbar Styles */
        .navbar {
            background: linear-gradient(135deg, var(--primary-color) 0%, #2d1a5e 50%, var(--purple-light) 100%) !important;
            border-bottom: 1px solid var(--border-color);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 8px 32px rgba(31, 20, 74, 0.4);
            padding: 15px 0;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1050;
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            overflow: hidden;
        }
        
        .navbar.scrolled {
            background: linear-gradient(135deg, rgba(31, 20, 74, 0.95) 0%, rgba(45, 26, 94, 0.95) 50%, rgba(114, 105, 176, 0.95) 100%) !important;
            box-shadow: 0 12px 40px rgba(31, 20, 74, 0.6);
            padding: 10px 0;
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
        }
        
        .navbar::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 200px;
            height: 100%;
            background: linear-gradient(90deg, transparent 0%, rgba(239, 72, 112, 0.1) 50%, rgba(45, 188, 174, 0.1) 100%);
            animation: shimmer 3s ease-in-out infinite;
        }
        
        @keyframes shimmer {
            0%, 100% { transform: translateX(100%); opacity: 0; }
            50% { transform: translateX(-100%); opacity: 1; }
        }
        
        .navbar-brand {
            position: relative;
            z-index: 2;
        }
        
        .navbar-brand img {
            height: 53px;
            transition: all 0.3s ease;
            filter: brightness(1.1) drop-shadow(0 4px 8px rgba(239, 72, 112, 0.3));
        }
        
        .navbar-brand:hover img {
            transform: scale(1.05) rotate(2deg);
            filter: brightness(1.2) drop-shadow(0 6px 12px rgba(239, 72, 112, 0.5));
        }
        
        .navbar-nav .nav-link {
            color: white !important;
            font-weight: 500;
            margin: 0 10px;
            padding: 8px 16px !important;
            border-radius: 25px;
            transition: all 0.3s ease;
            position: relative;
        }
        
        .navbar-nav .nav-link:hover {
            background: var(--gradient-accent);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(239, 72, 112, 0.4);
            color: white !important;
        }
        
        .navbar-nav .nav-link {
            position: relative;
            overflow: hidden;
        }
        
        .navbar-nav .nav-link::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            width: 0;
            height: 2px;
            background: var(--gradient-accent);
            transition: all 0.3s ease;
            transform: translateX(-50%);
        }
        
        .navbar-nav .nav-link:hover::after {
            width: 80%;
        }
        
        .navbar-nav .nav-link.active {
            background: var(--gradient-accent);
            box-shadow: var(--shadow-accent);
        }
        
        /* Button Styles */
        .btn-primary {
            background: var(--gradient-accent);
            border: none;
            border-radius: 25px;
            padding: 12px 30px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        /* Hero Banner Styles */
        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(180deg); }
        }
        
        .btn-cta:hover {
            background: #3cc7b8 !important;
            transform: translateY(-3px);
            box-shadow: 0 12px 35px rgba(45, 188, 174, 0.6) !important;
        }
        
        .hero-title {
            animation: fadeInUp 1s ease-out;
        }
        
        .hero-subtitle {
            animation: fadeInUp 1s ease-out 0.3s both;
        }
        
        .hero-cta {
            animation: fadeInUp 1s ease-out 0.6s both;
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @media (max-width: 768px) {
            .hero-title {
                font-size: 2.5rem !important;
            }
            
            .hero-subtitle {
                font-size: 1.1rem !important;
            }
            
            .hero-content {
                padding: 40px 20px !important;
                text-align: center !important;
            }
            
            .btn-cta {
                padding: 15px 35px !important;
                font-size: 1.1rem !important;
            }
        }
        
        /* Enhanced Right Section Styles */
        .navbar-right-section {
            position: relative;
            z-index: 1060; /* ضمان بقاء قسم المستخدم فوق المحتوى */
        }
        
        /* إصلاح مشكلة القائمة المنسدلة المخفية خلف المحتوى */
        .navbar-nav .dropdown-menu {
            z-index: 2100 !important;
            position: absolute !important;
        }
        
        .navbar-nav .dropdown {
            position: relative;
            z-index: 1070;
        }
        
        .user-profile-link {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px !important;
            border-radius: 25px;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .user-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: var(--gradient-accent);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
        }
        
        .user-name {
            font-weight: 500;
            color: white;
        }
        
        .enhanced-dropdown {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            margin-top: 10px;
            z-index: 2100 !important; /* أعلى من أي محتوى في الصفحة */
            position: relative;
        }
        
        .enhanced-dropdown .dropdown-item {
            padding: 12px 20px;
            border-radius: 10px;
            margin: 5px;
            transition: all 0.3s ease;
        }
        
        .enhanced-dropdown .dropdown-item:hover {
            background: var(--gradient-accent);
            color: white;
            transform: translateX(5px);
        }
        
        .login-link, .register-link {
            position: relative;
            overflow: hidden;
        }
        
        .login-link::before, .register-link::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s ease;
        }
        
        .login-link:hover::before, .register-link:hover::before {
            left: 100%;
        }
        
        .cta-button {
            position: relative;
            overflow: hidden;
        }
        
        .btn-glow {
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: left 0.6s ease;
        }
        
        .cta-button:hover .btn-glow {
            left: 100%;
        }
        
        .cta-button:hover {
            transform: translateY(-2px) scale(1.05);
            box-shadow: 0 8px 25px rgba(240, 199, 29, 0.6);
        }
        
        .btn-text {
            position: relative;
            z-index: 2;
        }
        
        .btn-secondary {
            background: var(--gradient-secondary);
            border: none;
            border-radius: 25px;
            padding: 12px 30px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-gold {
            background: var(--gradient-gold);
            border: none;
            border-radius: 25px;
            padding: 12px 30px;
            font-weight: 600;
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(240, 199, 29, 0.4);
            color: var(--primary-color);
            transition: all 0.3s ease;
            box-shadow: var(--shadow-primary);
        }
        
        .btn-primary:hover {
            transform: translateY(-3px) scale(1.05);
            box-shadow: 0 15px 50px rgba(239, 72, 112, 0.5);
        }

        .btn-secondary:hover {
            transform: translateY(-3px) scale(1.05);
            box-shadow: 0 15px 50px rgba(45, 188, 174, 0.5);
        }

        .btn-gold:hover {
            transform: translateY(-3px) scale(1.05);
            box-shadow: 0 15px 50px rgba(240, 199, 29, 0.5);
        }
        
        .btn-secondary:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 50px rgba(255, 107, 157, 0.4);
        }
        
        /* Card Styles */
        .card {
            background: linear-gradient(145deg, #ffffff 0%, #f8f9fa 100%);
            border: none;
            border-radius: 25px;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            position: relative;
        }
        
        .card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--gradient-accent);
            transform: scaleX(0);
            transition: transform 0.3s ease;
        }
        
        .card:hover::before {
            transform: scaleX(1);
        }
        
        .card:hover {
            transform: translateY(-15px) scale(1.02);
            box-shadow: 0 25px 80px rgba(239, 72, 112, 0.2);
        }
        
        /* Footer Styles */
        .footer {
            background: linear-gradient(135deg, #0F0F23 0%, #1A1A2E 100%);
            border-top: 1px solid var(--border-color);
            margin-top: 80px;
        }
        
        /* Animation Classes */
        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(5deg); }
        }
        
        @keyframes pulse {
            0%, 100% { transform: scale(1); opacity: 0.8; }
            50% { transform: scale(1.1); opacity: 1; }
        }
        
        @keyframes shimmer {
            0% { background-position: -200% 0; }
            100% { background-position: 200% 0; }
        }
        
        .float-animation {
            animation: float 6s ease-in-out infinite;
        }
        
        .pulse-animation {
            animation: pulse 3s ease-in-out infinite;
        }
        
        .shimmer-effect {
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
            background-size: 200% 100%;
            animation: shimmer 2s infinite;
        }
        
        /* VR Elements */
        .vr-element {
            position: absolute;
            width: 200px;
            height: 200px;
            background: linear-gradient(135deg, var(--accent-color) 0%, var(--secondary-color) 100%);
            border-radius: 50%;
            opacity: 0.15;
            animation: float 8s ease-in-out infinite;
            z-index: -1;
        }
        
        .vr-element::before {
            content: '';
            position: absolute;
            top: 20px;
            left: 20px;
            right: 20px;
            bottom: 20px;
            background: linear-gradient(135deg, var(--gold-color) 0%, var(--purple-light) 100%);
            border-radius: 50%;
            opacity: 0.6;
            animation: float 6s ease-in-out infinite reverse;
        }
        
        /* Hero Section */
        .hero-section {
            display: flex;
            align-items: center;
            position: relative;
            overflow: hidden;
            background: linear-gradient(135deg, var(--dark-bg) 0%, #1A1A2E 100%);
        }
        
        /* Section Titles */
        .section-title {
            font-size: 3rem;
            font-weight: 800;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--accent-color) 50%, var(--secondary-color) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-align: center;
            margin-bottom: 3rem;
            position: relative;
            display: inline-block;
        }
        
        .section-title::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background: var(--gradient-accent);
            border-radius: 2px;
        }
        
        /* Background Sections */
        .hero-bg-section {
            background-image: url('{{ asset("images/vr/VR_MAN.bmp") }}');
            background-size: 80%;
            background-position: center top;
            background-repeat: no-repeat;
            background-attachment: fixed;
            border-radius: 20px;
            position: relative;
            min-height: 500px;
            width: 100%;
        }
        
        .hero-overlay {
            background: linear-gradient(135deg, rgba(31, 20, 74, 0.8) 0%, rgba(239, 72, 112, 0.6) 50%, rgba(45, 188, 174, 0.4) 100%);
            border-radius: 25px;
            padding: 50px;
            text-align: right;
            direction: rtl;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .cta-bg-section {
            background-image: url('{{ asset("images/vr/VR_WONEM.png") }}');
            background-size: cover;
            background-position: fixed;
            background-repeat: no-repeat;
            background-attachment: fixed;
            border-radius: 20px;
            position: relative;
            min-height: 400px;
            width: 100%;
        }
        
        .cta-overlay {
            background: linear-gradient(135deg, rgba(45, 188, 174, 0.8) 0%, rgba(239, 72, 112, 0.6) 50%, rgba(31, 20, 74, 0.4) 100%);
            border-radius: 25px;
            padding: 50px;
            text-align: right;
            direction: rtl;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        /* Service Cards Hover Effects */
        .service-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.15) !important;
        }
        
        .service-card:hover .service-icon {
            transform: scale(1.1);
            box-shadow: 0 15px 30px rgba(0,0,0,0.2);
        }
        
        /* Button Hover Effects */
        .btn:hover {
            transform: translateY(-2px);
        }
        
        /* Gallery Item Hover */
        .gallery-item:hover {
            transform: scale(1.05);
            transition: all 0.3s ease;
        }
        
        /* VR Elements Animation */
        .vr-person-container:hover img {
            transform: scale(1.02);
            transition: all 0.3s ease;
        }
        
        /* Footer Styles */
        .footer {
            background: linear-gradient(135deg, var(--primary-color) 0%, #2d1a5e 50%, var(--purple-light) 100%);
            color: white;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .section-title {
                font-size: 2rem;
            }
            
            .navbar-nav .nav-link {
                margin: 5px 0;
            }
            
            .service-card {
                margin-bottom: 30px;
            }
        }
    </style>
    
    @yield('styles')
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">
                <img src="{{ asset('images/logo/logo.png') }}" alt="Your Events">
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">
                            <i class="fas fa-home me-1"></i>الرئيسية
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('services.*') ? 'active' : '' }}" href="{{ route('services.index') }}">
                            <i class="fas fa-cogs me-1"></i>خدماتنا
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('packages.*') ? 'active' : '' }}" href="{{ route('packages.index') }}">
                            <i class="fas fa-box me-1"></i>الباقات
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('gallery.*') ? 'active' : '' }}" href="{{ route('gallery.index') }}">
                            <i class="fas fa-images me-1"></i>المعرض
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('contact') ? 'active' : '' }}" href="{{ route('contact') }}">
                            <i class="fas fa-envelope me-1"></i>اتصل بنا
                        </a>
                    </li>
                </ul>
                
                <ul class="navbar-nav navbar-right-section">
                    @auth
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle user-profile-link" href="#" role="button" data-bs-toggle="dropdown">
                                <div class="user-avatar">
                                    <i class="fas fa-user"></i>
                                </div>
                                <span class="user-name">{{ Auth::user()->name }}</span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end enhanced-dropdown">
                                <li><a class="dropdown-item" href="{{ route('booking.my-bookings') }}">
                                    <i class="fas fa-calendar-check me-2"></i>حجوزاتي
                                </a></li>
                                @if(Auth::user()->is_admin)
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="{{ route('admin.dashboard') }}">
                                        <i class="fas fa-tachometer-alt me-2"></i>لوحة التحكم
                                    </a></li>
                                @endif
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                        @csrf
                                        <button type="submit" class="dropdown-item">
                                            <i class="fas fa-sign-out-alt me-2"></i>تسجيل الخروج
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link login-link" href="{{ route('login') }}">
                                <i class="fas fa-sign-in-alt me-1"></i>تسجيل الدخول
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link register-link" href="{{ route('register') }}">
                                <i class="fas fa-user-plus me-1"></i>إنشاء حساب
                            </a>
                        </li>
                    @endauth
                    <li class="nav-item">
                        <a class="btn btn-gold ms-2 cta-button" href="{{ route('booking.create') }}">
                            <span class="btn-text">
                                <i class="fas fa-calendar-plus me-1"></i>احجز الآن
                            </span>
                            <div class="btn-glow"></div>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    
    <!-- Main Content -->
    <main style="padding-top: 80px;">
        @yield('content')
    </main>
    
    <!-- Footer -->
    <footer class="footer py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-4">
                    <div class="d-flex align-items-center mb-3">
                        <img src="{{ asset('images/logo/logo.png') }}" alt="Your Events" height="55" class="me-3">
                        <h5 class="mb-0 text-white"></h5>
                    </div>
                    <p class="text-white mb-3">
                        نحن في Your Events نقدم خدمات تنظيم المناسبات والأحداث بأعلى مستويات الجودة والإبداع
                    </p>
                    <div class="d-flex gap-3">
                        <a href="#" class="text-primary"><i class="fab fa-facebook fa-lg"></i></a>
                        <a href="#" class="text-primary"><i class="fab fa-twitter fa-lg"></i></a>
                        <a href="#" class="text-primary"><i class="fab fa-instagram fa-lg"></i></a>
                        <a href="#" class="text-primary"><i class="fab fa-linkedin fa-lg"></i></a>
                    </div>
                </div>
                
                <div class="col-lg-2 col-md-6 mb-4">
                    <h6 class="mb-3 text-white">روابط سريعة</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="{{ route('home') }}" class="text-white text-decoration-none">الرئيسية</a></li>
                        <li class="mb-2"><a href="{{ route('services.index') }}" class="text-white text-decoration-none">خدماتنا</a></li>
                        <li class="mb-2"><a href="{{ route('packages.index') }}" class="text-white text-decoration-none">الباقات</a></li>
                        <li class="mb-2"><a href="{{ route('gallery.index') }}" class="text-white text-decoration-none">المعرض</a></li>
                        <li class="mb-2"><a href="{{ route('contact') }}" class="text-white text-decoration-none">اتصل بنا</a></li>
                    </ul>
                </div>
                
                <div class="col-lg-3 col-md-6 mb-4">
                    <h6 class="mb-3 text-white">معلومات التواصل</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <i class="fas fa-phone text-primary me-2"></i>
                            <span class="text-white">+966 50 123 4567</span>
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-envelope text-primary me-2"></i>
                            <span class="text-white">info@yourevents.com</span>
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-map-marker-alt text-primary me-2"></i>
                            <span class="text-white">الرياض، المملكة العربية السعودية</span>
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-clock text-primary me-2"></i>
                            <span class="text-white">السبت - الخميس: 9:00 ص - 6:00 م</span>
                        </li>
                    </ul>
                </div>
                
                <div class="col-lg-3 mb-4">
                    <h6 class="mb-3 text-white">اشترك في النشرة الإخبارية</h6>
                    <p class="text-white mb-3">احصل على آخر الأخبار والعروض الخاصة</p>
                    <form class="d-flex">
                        <input type="email" class="form-control me-2" placeholder="بريدك الإلكتروني">
                        <button class="btn btn-primary" type="submit">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </form>
                </div>
            </div>
            
            <hr class="my-4" style="border-color: var(--border-color);">
            
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="text-white mb-0">
                        &copy; {{ date('Y') }} Your Events. جميع الحقوق محفوظة.
                    </p>
                </div>
                <div class="col-md-6 text-md-end">
                    <a href="#" class="text-white text-decoration-none me-3">سياسة الخصوصية</a>
                    <a href="#" class="text-white text-decoration-none">شروط الاستخدام</a>
                </div>
            </div>
        </div>
    </footer>
    
    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
    
    <script>
        // Wait for DOM to be fully loaded
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize AOS with error handling
            if (typeof AOS !== 'undefined') {
                AOS.init({
                    duration: 1000,
                    once: true,
                    offset: 100,
                    easing: 'ease-in-out'
                });
            } else {
                console.warn('AOS library not loaded');
            }
            
            // Enhanced navbar scroll effect with smooth transitions
            let ticking = false;
            
            function updateNavbar() {
                const navbar = document.querySelector('.navbar');
                if (navbar) {
                    if (window.scrollY > 50) {
                        navbar.classList.add('scrolled');
                    } else {
                        navbar.classList.remove('scrolled');
                    }
                }
                ticking = false;
            }
            
            function requestTick() {
                if (!ticking) {
                    requestAnimationFrame(updateNavbar);
                    ticking = true;
                }
            }
            
            // Add scroll event listener with throttling
            window.addEventListener('scroll', requestTick, { passive: true });
            
            // Initial check on page load
            updateNavbar();
            
            // Add smooth hover effects for interactive elements
            const interactiveElements = document.querySelectorAll('.nav-link, .btn, .service-card');
            interactiveElements.forEach(element => {
                element.addEventListener('mouseenter', function() {
                    this.style.transition = 'all 0.3s cubic-bezier(0.4, 0, 0.2, 1)';
                });
            });
        });
    </script>
    
    @yield('scripts')
</body>
</html>
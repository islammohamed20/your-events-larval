<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    @php
        $siteName = \App\Models\Setting::get('site_name', 'Your Events');
        $siteDescription = \App\Models\Setting::get('site_description', __('common.site_slogan'));
        $metaKeywords = \App\Models\Setting::get('meta_keywords');
        $ogImage = \App\Models\Setting::get('og_image');
        $logo = \App\Models\Setting::get('logo');
        // Build resilient logo and OG image URLs with safe fallback
        $defaultLogoAsset = asset('images/logo/logo.png');
        $logoUrl = $logo
            ? (filter_var($logo, FILTER_VALIDATE_URL) ? $logo : url(\Illuminate\Support\Facades\Storage::url($logo)))
            : $defaultLogoAsset;
        $ogImageUrl = $ogImage
            ? (filter_var($ogImage, FILTER_VALIDATE_URL) ? $ogImage : url(\Illuminate\Support\Facades\Storage::url($ogImage)))
            : $logoUrl;
        $canonical = url()->current();
    @endphp

    <title>@yield('title', $siteName . ' - ' . __('common.site_slogan'))</title>
    <meta name="description" content="{{ $siteDescription }}">
    <meta name="robots" content="@yield('robotsMeta', 'index,follow')">
    @if($metaKeywords)
    <meta name="keywords" content="{{ $metaKeywords }}">
    @endif
    <link rel="canonical" href="{{ $canonical }}">

    @php
        $gsv = \App\Models\Setting::get('google_site_verification');
    @endphp
    @if($gsv)
    <meta name="google-site-verification" content="{{ $gsv }}">
    @endif

    <!-- Open Graph -->
    <meta property="og:title" content="@yield('title', $siteName)">
    <meta property="og:description" content="{{ $siteDescription }}">
    <meta property="og:url" content="{{ $canonical }}">
    <meta property="og:image" content="{{ $ogImageUrl }}">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="{{ $siteName }}">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('title', $siteName)">
    <meta name="twitter:description" content="{{ $siteDescription }}">
    <meta name="twitter:image" content="{{ $ogImageUrl }}">
    
    @php
        $siteUrl = url('/');
        $facebook = \App\Models\Setting::get('facebook_url');
        $twitter = \App\Models\Setting::get('twitter_url');
        $instagram = \App\Models\Setting::get('instagram_url');
        $linkedin = \App\Models\Setting::get('linkedin_url');
        $sameAs = array_values(array_filter([$facebook, $twitter, $instagram, $linkedin]));
        $orgSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => $siteName,
            'url' => $siteUrl,
            'logo' => $logoUrl,
            'sameAs' => $sameAs,
        ];
        $websiteSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            'url' => $siteUrl,
            'name' => $siteName,
            'potentialAction' => [
                '@type' => 'SearchAction',
                'target' => url('/search') . '?q={search_term_string}',
                'query-input' => 'required name=search_term_string',
            ],
        ];
    @endphp
    <script type="application/ld+json">@json(array_filter($orgSchema))</script>
    <script type="application/ld+json">@json(array_filter($websiteSchema))</script>

    @php
        $faviconSetting = \App\Models\Setting::get('favicon') ?: \App\Models\Setting::get('site_favicon');
        $faviconUrlSetting = \App\Models\Setting::get('favicon_url');
        $fallbackFaviconUrl = asset('images/logo/logo.png');
        $faviconUrl = $faviconSetting
            ? (filter_var($faviconSetting, FILTER_VALIDATE_URL) ? $faviconSetting : url(Storage::url($faviconSetting)))
            : ($faviconUrlSetting ? (filter_var($faviconUrlSetting, FILTER_VALIDATE_URL) ? $faviconUrlSetting : url($faviconUrlSetting)) : $fallbackFaviconUrl);
        $faviconPath = parse_url($faviconUrl, PHP_URL_PATH);
        $faviconExt = strtolower(pathinfo($faviconPath ?? $faviconUrl, PATHINFO_EXTENSION));
        $faviconType = $faviconExt === 'ico' ? 'image/x-icon' : ($faviconExt === 'svg' ? 'image/svg+xml' : 'image/png');
    @endphp
    <link rel="icon" type="{{ $faviconType }}" href="{{ $faviconUrl }}">
    <link rel="shortcut icon" type="{{ $faviconType }}" href="{{ $faviconUrl }}">
    
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
    
    <!-- Main Stylesheet - محسّن للأداء -->
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    
    <style>
        /* Payment Icons */
        .payment-icons { display:flex; align-items:center; justify-content:flex-start; gap:14px; }
        .payment-icons i { font-size: 1.6rem; color: #ffffff; opacity: 0.9; }
        .payment-logo { height: 26px; width:auto; filter: brightness(1.1) contrast(1.05); }
        .payment-badge { display:inline-block; background: rgba(255,255,255,0.15); color:#fff; padding:4px 8px; border-radius:6px; font-size:0.85rem; border:1px solid rgba(255,255,255,0.25); }

        /* Animated White Wave Background */
        /* ========== ENHANCED ANIMATED WHITE BACKGROUND WITH WAVE ICONS ========== */
        .animated-white-bg { 
            position: relative; 
            overflow: hidden; 
            background: #ffffff;
        }
        
        /* Base animated layer with wave effect */
        .animated-white-bg::before {
            content: '';
            position: absolute;
            left: -150%;
            top: 0;
            width: 300%;
            height: 100%;
            background:
                radial-gradient(circle at 20% 50%, rgba(30, 19, 73, 0.06), transparent 36%),
                radial-gradient(circle at 80% 50%, rgba(239, 72, 112, 0.04), transparent 36%);
            animation: waveShift 14s ease-in-out infinite;
            opacity: 1;
            pointer-events: none;
            z-index: 0;
        }
        
        /* Secondary wave layer for dual-color effect */
        .animated-white-bg::after {
            content: '';
            position: absolute;
            right: -150%;
            bottom: 0;
            width: 300%;
            height: 100%;
            background:
                radial-gradient(circle at 25% 50%, rgba(239, 72, 112, 0.05), transparent 40%),
                radial-gradient(circle at 75% 50%, rgba(30, 19, 73, 0.04), transparent 40%);
            animation: waveShiftReverse 16s ease-in-out infinite;
            opacity: 1;
            pointer-events: none;
            z-index: 0;
        }
        
        /* Advanced wave shift animation */
        @keyframes waveShift {
            0% { transform: translateX(0) scaleY(1); }
            25% { transform: translateX(10%) scaleY(1.02); }
            50% { transform: translateX(25%) scaleY(0.98); }
            75% { transform: translateX(15%) scaleY(1.01); }
            100% { transform: translateX(0) scaleY(1); }
        }
        
        /* Reverse wave animation for dual effect */
        @keyframes waveShiftReverse {
            0% { transform: translateX(0) scaleX(1); }
            25% { transform: translateX(-12%) scaleX(1.01); }
            50% { transform: translateX(-25%) scaleX(0.99); }
            75% { transform: translateX(-15%) scaleX(1.02); }
            100% { transform: translateX(0) scaleX(1); }
        }
        
        /* Floating animated icons in background */
        .animated-white-bg .float-icon {
            position: absolute;
            opacity: 0.08;
            pointer-events: none;
            z-index: 1;
        }
        
        .animated-white-bg .float-icon:nth-child(1) {
            top: 10%;
            left: 8%;
            font-size: 4rem;
            animation: float 8s ease-in-out infinite;
            color: #1e1349;
        }
        
        .animated-white-bg .float-icon:nth-child(2) {
            top: 60%;
            right: 12%;
            font-size: 3.5rem;
            animation: float 9s ease-in-out infinite 1s;
            color: #ef4870;
        }
        
        .animated-white-bg .float-icon:nth-child(3) {
            bottom: 15%;
            left: 15%;
            font-size: 4.5rem;
            animation: float 7s ease-in-out infinite 2s;
            color: #1e1349;
        }
        
        .animated-white-bg .float-icon:nth-child(4) {
            top: 35%;
            right: 8%;
            font-size: 3.8rem;
            animation: float 10s ease-in-out infinite 1.5s;
            color: #ef4870;
        }
        
        .animated-white-bg .float-icon:nth-child(5) {
            bottom: 20%;
            right: 18%;
            font-size: 3.2rem;
            animation: float 8.5s ease-in-out infinite 0.5s;
            color: #1e1349;
        }
        
        /* Floating animation with color gradient */
        @keyframes float {
            0%, 100% {
                transform: translateY(0) rotate(0deg);
                opacity: 0.06;
            }
            25% {
                transform: translateY(-25px) rotate(8deg);
                opacity: 0.08;
            }
            50% {
                transform: translateY(-40px) rotate(0deg);
                opacity: 0.1;
            }
            75% {
                transform: translateY(-20px) rotate(-8deg);
                opacity: 0.08;
            }
        }
        
        /* Wave pulse effect for icons */
        @keyframes wavePulse {
            0%, 100% {
                filter: drop-shadow(0 0 0px rgba(30, 19, 73, 0.1));
            }
            50% {
                filter: drop-shadow(0 0 15px rgba(239, 72, 112, 0.2));
            }
        }
        
        .animated-white-bg .float-icon:hover {
            animation-play-state: paused;
            opacity: 0.15;
        }
        
        /* Ensure content stays above animated background */
        .animated-white-bg > * {
            position: relative;
            z-index: 2;
        }
        
        /* عرض أرقام الهاتف من اليسار لليمين حتى في صفحات RTL */
        .phone-ltr { direction: ltr; unicode-bidi: bidi-override; text-align: left; }
        .phone-ltr span { white-space: nowrap; }
        /* Page-specific inline styles - فقط ما يحتاجه هذا الملف خصيصاً */
        
        /* Hero Background Image للصفحة الرئيسية */
        .hero-bg-image {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url('{{ asset("images/vr/VR_MAN.bmp") }}');
            background-size: 80%;
            background-position: center top;
            background-repeat: no-repeat;
            opacity: 1;
            background-attachment: fixed;
        }
        
        /* LTR (English) - Center alignment for content */
        [dir="ltr"] .hero-overlay,
        [dir="ltr"] .cta-overlay {
            text-align: center;
            direction: ltr;
        }
        
        /* LTR general text alignment */
        [dir="ltr"] .footer-links {
            text-align: center !important;
        }
        
        [dir="ltr"] .hero-section h1,
        [dir="ltr"] .hero-section h2,
        [dir="ltr"] .hero-section p,
        [dir="ltr"] .hero-contact h1,
        [dir="ltr"] .hero-contact p {
            text-align: center;
        }
        
        [dir="ltr"] section h2,
        [dir="ltr"] section .section-title {
            text-align: center;
        }
        
        /* LTR - Footer text alignment */
        [dir="ltr"] .footer p,
        [dir="ltr"] .footer .list-unstyled,
        [dir="ltr"] .footer h6 {
            text-align: left;
        }
        
        [dir="ltr"] .footer .col-md-6.text-md-start p {
            text-align: left;
        }
        
        /* RTL - Footer text alignment */
        [dir="rtl"] .footer p,
        [dir="rtl"] .footer .list-unstyled,
        [dir="rtl"] .footer h6 {
            text-align: right;
        }
        
        /* Service Card Title - Always Center */
        .service-card .card-title,
        .card .card-title {
            text-align: center !important;
        }
        
        .service-card .card-title a,
        .card .card-title a {
            display: block;
            text-align: center !important;
        }
        
        /* Service Card Description - Centered */
        .service-card .card-body p,
        .card .card-body > p {
            text-align: center;
        }
        
        /* Service Card Price - Centered */
        .service-card .card-body .mb-3,
        .service-card .card-body .h5 {
            text-align: center;
        }
        
        /* Category Card Title - Always Center */
        .category-title,
        .category-modern-card .category-title {
            text-align: center !important;
        }
        
        /* Section Headers - Always Center */
        .section-heading,
        .section-subheading,
        .section-description {
            text-align: center !important;
        }
        
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
        
        /* باقي الأنماط موجودة في style.css - Page Layout هنا فقط */
        main {
            padding-top: 80px;
            position: relative;
            z-index: 1;
        }
        
        .navbar-nav.me-auto {
            margin-right: 0 !important;
            margin-left: 20px;
        }
        
        .navbar-nav.navbar-right-section {
            display: flex;
            align-items: center;
            gap: 8px;
            /* margin-left: auto; */
            margin-right: 30px;
        }

        /* تحسين القائمة المنسدلة للهاتف المحمول */
        .navbar .dropdown-menu {
            z-index: 9999 !important;
            border: none;
            border-radius: 15px;
            box-shadow: 0 15px 50px rgba(31, 20, 74, 0.3);
            background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
            padding: 15px 0;
            margin-top: 10px;
            min-width: 200px;
        }
        
        .navbar .dropdown-menu.show {
            display: block !important;
            position: absolute !important;
            z-index: 9999 !important;
            visibility: visible !important;
            opacity: 1 !important;
            transform: translateY(0);
            transition: all 0.3s ease;
        }
        
        .navbar .dropdown-item {
            padding: 12px 20px;
            font-size: 1rem;
            color: var(--text-color);
            border-radius: 8px;
            margin: 2px 10px;
            transition: all 0.3s ease;
        }
        
        .navbar .dropdown-item:hover {
            background: linear-gradient(135deg, var(--secondary-color), #3cc7b8);
            color: white;
            transform: translateX(-5px);
        }
        
        /* منع قص القائمة من container */
        .navbar .container {
            overflow: visible !important;
        }
        
        /* للشاشات الكبيرة فقط - منع قص القائمة */
        @media (min-width: 992px) {
            .navbar .navbar-collapse {
                overflow: visible !important;
            }
        }
        
        .navbar .dropdown {
            position: relative;
        }
        
        /* تحسين للهاتف المحمول */
        @media (max-width: 991px) {
            .navbar {
                padding: 10px 0;
            }
            
            /* تحسين الـ Side Menu مع الـ Scrolling وإخفاء افتراضي */
            .navbar-collapse {
                max-height: calc(100vh - 80px) !important;
                overflow-y: auto !important;
                overflow-x: hidden !important;
                display: none !important;
                background: linear-gradient(135deg, rgba(31, 20, 74, 0.98) 0%, rgba(45, 26, 94, 0.98) 100%);
                backdrop-filter: blur(15px);
                -webkit-backdrop-filter: blur(15px);
                border-radius: 0 0 20px 20px;
                margin-top: 10px;
                padding: 20px 0;
                box-shadow: 0 10px 30px rgba(31, 20, 74, 0.3);
                position: relative;
                
                /* Smooth scrolling */
                scroll-behavior: smooth;
                -webkit-overflow-scrolling: touch;
                
                /* تأثير الحدود المتوهجة */
                border: 1px solid rgba(45, 188, 174, 0.3);
                
                /* تأثير الانيميشن عند الفتح */
                animation: slideDown 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            }
            /* إظهار القائمة عند إضافة .show */
            .navbar-collapse.show {
                display: block !important;
            }
            
            @keyframes slideDown {
                from {
                    opacity: 0;
                    transform: translateY(-20px);
                    max-height: 0;
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                    max-height: calc(100vh - 80px);
                }
            }
            
            /* تحسين شريط التمرير */
            .navbar-collapse::-webkit-scrollbar {
                width: 6px;
            }
            
            .navbar-collapse::-webkit-scrollbar-track {
                background: rgba(255, 255, 255, 0.1);
                border-radius: 3px;
            }
            
            .navbar-collapse::-webkit-scrollbar-thumb {
                background: linear-gradient(135deg, var(--secondary-color), var(--gold-color));
                border-radius: 3px;
                transition: all 0.3s ease;
            }
            
            .navbar-collapse::-webkit-scrollbar-thumb:hover {
                background: linear-gradient(135deg, var(--gold-color), var(--secondary-color));
            }
            
            /* إظهار القائمة عند الضغط على زر التبديل */
            .navbar-collapse.show {
                display: block !important;
                max-height: calc(100vh - 80px) !important;
                overflow-y: auto !important;
                overflow-x: hidden !important;
            }
            
            /* تحسين عناصر القائمة */
            .navbar-nav {
                padding: 0 15px;
            }
            
            .nav-item {
                margin: 5px 0;
            }
            
            .nav-link {
                padding: 15px 20px !important;
                border-radius: 12px;
                margin: 3px 0;
                transition: all 0.3s ease;
                background: rgba(255, 255, 255, 0.05);
                border: 1px solid rgba(255, 255, 255, 0.1);
            }
            
            .nav-link:hover,
            .nav-link.active {
                background: linear-gradient(135deg, var(--secondary-color), #3cc7b8);
                transform: translateX(5px);
                box-shadow: 0 5px 15px rgba(45, 188, 174, 0.3);
            }
            
            .navbar .dropdown-menu {
                position: relative !important;
                float: none;
                width: 100%;
                margin-top: 0;
                border-radius: 10px;
                box-shadow: inset 0 2px 8px rgba(0, 0, 0, 0.1);
                background: rgba(255, 255, 255, 0.95);
                backdrop-filter: blur(10px);
                display: none;
            }
            
            .navbar .dropdown-menu.show {
                display: block !important;
            }
            
            .navbar .dropdown-item {
                text-align: center;
                padding: 15px 20px;
                border-bottom: 1px solid rgba(31, 20, 74, 0.1);
            }
            
            .navbar .dropdown-item:last-child {
                border-bottom: none;
            }
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
        
        /* Hero Background Image */
        .hero-bg-image {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url('{{ asset("images/vr/VR_MAN.bmp") }}');
            background-size: 80%;
            background-position: center top;
            background-repeat: no-repeat;
            opacity: 1;
            background-attachment: fixed;
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
            font-size: 0.95rem;
            margin: 0;
            padding: 8px 14px !important;
            border-radius: 20px;
            transition: all 0.3s ease;
            position: relative;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            white-space: nowrap;
        }
        
        .navbar-nav .nav-link i {
            font-size: 0.9rem;
        }
        
        .navbar-nav .nav-link:hover {
            background: var(--gradient-accent);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(239, 72, 112, 0.4);
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
            z-index: 3;
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        /* Cart Styles */
        
        /* Search Form Styles */
        .search-nav-item {
            margin: 0;
        }
        
        .search-form {
            position: relative;
        }
        
        .search-input-wrapper {
            position: relative;
            width: 220px;
        }
        
        .search-input {
            width: 100%;
            padding: 7px 40px 7px 12px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 20px;
            background: rgba(255, 255, 255, 0.1);
            color: white;
            font-size: 0.85rem;
            transition: all 0.3s ease;
        }
        
        .search-input::placeholder {
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.85rem;
        }
        
        .search-input:focus {
            outline: none;
            border-color: var(--accent-color);
            background: rgba(255, 255, 255, 0.2);
            box-shadow: 0 0 10px rgba(239, 72, 112, 0.3);
            width: 250px;
        }
        
        .search-button {
            position: absolute;
            left: 3px;
            top: 50%;
            transform: translateY(-50%);
            background: var(--gradient-accent);
            border: none;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 0.85rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .search-button:hover {
            transform: translateY(-50%) scale(1.1);
            box-shadow: 0 0 15px rgba(239, 72, 112, 0.5);
        }
        
        .search-autocomplete {
            position: absolute;
            top: calc(100% + 10px);
            left: 0;
            right: 0;
            background: white;
            border-radius: 15px;
            box-shadow: 0 15px 50px rgba(31, 20, 74, 0.3);
            max-height: 400px;
            overflow-y: auto;
            display: none;
            z-index: 1000;
        }
        
        .search-autocomplete.show {
            display: block;
        }
        
        .autocomplete-item {
            padding: 12px 15px;
            border-bottom: 1px solid #e9ecef;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 12px;
            transition: all 0.3s ease;
            text-decoration: none;
            color: var(--text-color);
        }
        
        .autocomplete-item:last-child {
            border-bottom: none;
        }
        
        .autocomplete-item:hover {
            background: #f8f9fa;
            padding-right: 20px;
        }
        
        .autocomplete-image {
            width: 45px;
            height: 45px;
            object-fit: cover;
            border-radius: 8px;
            flex-shrink: 0;
        }
        
        .autocomplete-details {
            flex: 1;
        }
        
        .autocomplete-name {
            font-weight: 600;
            font-size: 14px;
            margin: 0;
            color: var(--primary-color);
        }
        
        .autocomplete-type {
            font-size: 12px;
            margin: 0;
            color: var(--text-muted);
        }
        
        .autocomplete-badge {
            font-size: 11px;
            padding: 3px 8px;
        }
        
        .autocomplete-empty {
            padding: 20px;
            text-align: center;
            color: var(--text-muted);
        }
        
        @media (max-width: 991px) {
            .search-nav-item {
                display: none;
            }
        }
        
        .cart-nav-item {
            margin: 0;
        }
        
        .cart-link {
            position: relative;
            padding: 8px 12px !important;
            border-radius: 20px;
        }
        
        .cart-icon-wrapper {
            position: relative;
            font-size: 1.1rem;
        }
        
        .cart-badge {
            position: absolute;
            top: -6px;
            right: -6px;
            background: var(--accent-color);
            color: white;
            border-radius: 50%;
            width: 18px;
            height: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.65rem;
            font-weight: 700;
            box-shadow: 0 2px 8px rgba(239, 72, 112, 0.5);
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.1);
            }
        }
        
        .cart-link:hover .cart-icon-wrapper {
            transform: scale(1.1);
            transition: transform 0.3s ease;
        }
        
        /* Cart Dropdown Styles */
        .cart-dropdown {
            width: 380px;
            max-width: 90vw;
            padding: 0;
            border: none;
            border-radius: 15px;
            box-shadow: 0 15px 50px rgba(31, 20, 74, 0.3);
            background: white;
            margin-top: 15px;
            max-height: 80vh;
            overflow: hidden;
            display: none;
            flex-direction: column;
        }

        .cart-dropdown.show {
            display: flex !important;
        }
        
        .cart-dropdown-header {
            padding: 15px 20px;
            background: var(--gradient-primary);
            color: white;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .cart-dropdown-header h6 {
            font-size: 16px;
            font-weight: 600;
            margin: 0;
        }
        
        .cart-dropdown-items {
            max-height: 400px;
            overflow-y: auto;
            padding: 10px;
        }
        
        .cart-dropdown-item {
            display: flex;
            gap: 12px;
            padding: 12px;
            border-bottom: 1px solid #f0f0f0;
            transition: background 0.3s ease;
        }
        
        .cart-dropdown-item:hover {
            background: #f8f9fa;
            border-radius: 8px;
        }
        
        .cart-dropdown-item:last-child {
            border-bottom: none;
        }
        
        .cart-dropdown-item .item-image {
            width: 60px;
            height: 60px;
            border-radius: 8px;
            overflow: hidden;
            flex-shrink: 0;
        }
        
        .cart-dropdown-item .item-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .cart-dropdown-item .item-details {
            flex: 1;
        }
        
        .cart-dropdown-item .item-name {
            font-size: 14px;
            font-weight: 600;
            margin: 0 0 5px 0;
            color: var(--text-color);
        }
        
        .cart-dropdown-item .item-quantity {
            font-size: 12px;
            color: var(--text-muted);
            margin: 0;
        }
        
        .cart-dropdown-item .item-variation {
            margin: 5px 0 0 0;
        }
        
        .cart-dropdown-item .item-variation .badge {
            font-size: 10px;
            padding: 3px 8px;
            margin-left: 3px;
        }
        
        .cart-dropdown-item .item-price {
            display: flex;
            align-items: center;
            color: var(--primary-color);
            font-size: 14px;
            white-space: nowrap;
        }
        
        .cart-dropdown-footer {
            padding: 15px 20px;
            background: #f8f9fa;
            border-top: 1px solid #e9ecef;
        }
        
        .cart-dropdown-footer .cart-total {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 16px;
        }
        
        .cart-dropdown-footer .cart-total strong {
            font-size: 18px;
        }
        
        .cart-dropdown-empty {
            padding: 60px 20px;
            text-align: center;
        }
        
        .cart-dropdown-empty i {
            font-size: 60px;
            color: #ddd;
            margin-bottom: 15px;
        }
        
        .cart-dropdown-empty p {
            color: var(--text-muted);
            margin-bottom: 15px;
        }
        
        /* Mobile: Show on click */
        @media (max-width: 991px) {
            .cart-dropdown {
                width: 320px;
            }
            
            .cart-dropdown-items {
                max-height: 300px;
            }
        }
        
        .user-profile-link {
            display: inline-flex !important;
            align-items: center;
            gap: 6px;
            padding: 6px 14px !important;
            border-radius: 20px;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .user-avatar {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: var(--gradient-accent);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
        }
        
        .user-name {
            font-weight: 500;
            color: white;
            font-size: 0.9rem;
            max-width: 150px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        
        .enhanced-dropdown {
            background: rgba(255, 255, 255, 0.98) !important;
            backdrop-filter: blur(20px);
            border: 1px solid rgba(0, 0, 0, 0.1) !important;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15) !important;
            margin-top: 10px !important;
            z-index: 9999 !important;
            min-width: 200px;
        }
        
        .nav-item.dropdown {
            position: relative;
        }
        
        .nav-item.dropdown .dropdown-menu {
            position: absolute !important;
            top: 100% !important;
            right: 0 !important;
            left: auto !important;
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
            padding: 7px 16px !important;
            font-size: 0.9rem;
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
            padding: 10px 25px;
            font-weight: 600;
            font-size: 0.95rem;
            position: relative;
            overflow: hidden;
            color: var(--primary-color);
            transition: all 0.3s ease;
            white-space: nowrap;
        }
        
        .navbar .btn-gold {
            padding: 8px 20px;
            font-size: 0.9rem;
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
            background-clip: text;
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
        
        /* Footer Logo - Desktop */
        .footer-logo {
            height: 55px;
            width: auto;
        }
        
        .footer a.text-white:hover,
        .footer .footer-link:hover {
            color: var(--gold-color) !important;
            transform: translateX(-3px);
            transition: all 0.3s ease;
            text-decoration: underline !important;
        }
        
        .footer .list-unstyled li {
            transition: all 0.3s ease;
        }
        
        .footer .list-unstyled li:hover {
            transform: translateX(-5px);
        }
        
        /* Social Media Colors */
        .hover-social-facebook {
            transition: all 0.3s ease;
        }
        
        .hover-social-facebook:hover {
            color: #1877F2 !important;
            transform: scale(1.2);
        }
        
        .hover-social-twitter {
            transition: all 0.3s ease;
        }
        
        .hover-social-twitter:hover {
            color: #ffffff !important;
            transform: scale(1.2);
        }
        
        .hover-social-instagram {
            transition: all 0.3s ease;
        }
        
        .hover-social-instagram:hover {
            background: linear-gradient(45deg, #f09433 0%,#e6683c 25%,#dc2743 50%,#cc2366 75%,#bc1888 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            transform: scale(1.2);
        }
        
        .hover-social-linkedin {
            transition: all 0.3s ease;
        }
        
        .hover-social-linkedin:hover {
            color: #0A66C2 !important;
            transform: scale(1.2);
        }
        
        .hover-social-youtube {
            transition: all 0.3s ease;
        }
        
        .hover-social-youtube:hover {
            color: #FF0000 !important;
            transform: scale(1.2);
        }
        
        .hover-social-tiktok {
            transition: all 0.3s ease;
        }
        
        .hover-social-tiktok:hover {
            color: #000000 !important;
            transform: scale(1.2);
        }
        
        .hover-social-snapchat {
            transition: all 0.3s ease;
        }
        
        .hover-social-snapchat:hover {
            color: #FFFC00 !important;
            transform: scale(1.2);
        }
        
        /* Mobile Bottom Navigation */
        .klb-mobile-bottom {
            display: none;
        }
        
        .hide-desktop {
            display: none !important;
        }
        
        @media (max-width: 991px) {
            .hide-desktop {
                display: block !important;
            }
            
            .klb-mobile-bottom {
                display: flex;
                position: fixed;
                bottom: 0;
                left: 0;
                right: 0;
                background: linear-gradient(135deg, rgba(31, 20, 74, 0.98) 0%, rgba(45, 26, 94, 0.98) 100%);
                backdrop-filter: blur(15px);
                -webkit-backdrop-filter: blur(15px);
                box-shadow: 0 -5px 20px rgba(31, 20, 74, 0.3);
                z-index: 1040;
                border-top: 2px solid rgba(45, 188, 174, 0.3);
                padding: 8px 0;
            }
            
            .mobile-nav-items {
                display: flex;
                justify-content: space-around;
                align-items: center;
                width: 100%;
                max-width: 600px;
                margin: 0 auto;
            }
            
            .mobile-nav-item {
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                padding: 8px 12px;
                text-decoration: none;
                color: white;
                transition: all 0.3s ease;
                border-radius: 12px;
                min-width: 70px;
                position: relative;
            }
            
            .mobile-nav-item:hover,
            .mobile-nav-item.active {
                background: linear-gradient(135deg, var(--secondary-color), #3cc7b8);
                color: white;
                transform: translateY(-3px);
                box-shadow: 0 5px 15px rgba(45, 188, 174, 0.4);
            }
            
            .mobile-nav-item i {
                font-size: 1.3rem;
                margin-bottom: 4px;
            }
            
            .mobile-nav-item .mobile-nav-icon {
                width: 1.5rem;
                height: 1.5rem;
                margin-bottom: 4px;
                object-fit: contain;
                filter: brightness(0) invert(1);
            }
            
            .mobile-nav-item span {
                font-size: 0.7rem;
                font-weight: 500;
            }
            
            .mobile-nav-item .badge {
                position: absolute;
                top: 2px;
                right: 8px;
                background: var(--accent-color);
                border-radius: 50%;
                width: 18px;
                height: 18px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 0.65rem;
                font-weight: 700;
            }
            
            /* Adjust main content for mobile bottom nav */
            main {
                padding-bottom: 70px !important;
            }
            
            /* Site Drawer Styles */
            .site-drawer {
                position: fixed;
                top: 0;
                right: -100%;
                width: 85%;
                max-width: 400px;
                height: 100%;
                height: 100dvh; /* Dynamic viewport height for mobile browsers */
                background: linear-gradient(135deg, rgba(31, 20, 74, 0.98) 0%, rgba(45, 26, 94, 0.98) 100%);
                backdrop-filter: blur(15px);
                -webkit-backdrop-filter: blur(15px);
                z-index: 9999;
                transition: right 0.4s cubic-bezier(0.4, 0, 0.2, 1);
                overflow-y: auto;
                -webkit-overflow-scrolling: touch;
                box-shadow: -5px 0 20px rgba(0, 0, 0, 0.3);
                padding-bottom: 40px; /* Space for bottom content */
            }
            
            .site-drawer.show {
                right: 0;
            }
            
            .site-drawer.color-layout-white {
                background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(248, 249, 250, 0.98) 100%);
            }
            
            .site-drawer.color-layout-white .drawer-header {
                background: linear-gradient(135deg, var(--primary-color) 0%, #2d1a5e 100%);
                border-bottom: 2px solid rgba(31, 20, 74, 0.1);
            }
            
            .site-drawer.color-layout-white .nav-link {
                color: var(--text-color) !important;
                background: rgba(31, 20, 74, 0.05) !important;
                border-color: rgba(31, 20, 74, 0.1) !important;
            }
            
            .site-drawer.color-layout-white .nav-link:hover,
            .site-drawer.color-layout-white .nav-link.active {
                background: linear-gradient(135deg, var(--secondary-color), #3cc7b8) !important;
                color: white !important;
            }
            
            .drawer-overlay {
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0, 0, 0, 0.7);
                z-index: 9998;
                opacity: 0;
                visibility: hidden;
                transition: all 0.3s ease;
            }
            
            .drawer-overlay.show {
                opacity: 1;
                visibility: visible;
            }
            
            /* Hide drawer on desktop */
            @media (min-width: 992px) {
                .site-drawer,
                .drawer-overlay {
                    display: none !important;
                    visibility: hidden !important;
                    opacity: 0 !important;
                    pointer-events: none !important;
                    z-index: -1 !important;
                }
            }
            
            .drawer-header {
                padding: 20px;
                border-bottom: 2px solid rgba(45, 188, 174, 0.3);
                display: flex;
                justify-content: space-between;
                align-items: center;
                background: linear-gradient(135deg, var(--primary-color) 0%, #2d1a5e 100%);
            }
            
            .drawer-header img {
                max-width: 60% !important;
                height: auto;
            }
            
            .drawer-close {
                background: none;
                border: none;
                color: white;
                font-size: 1.5rem;
                padding: 5px 10px;
                cursor: pointer;
                border-radius: 8px;
                transition: all 0.3s ease;
            }
            
            .drawer-close:hover {
                background: rgba(255, 255, 255, 0.1);
                transform: rotate(90deg);
            }
            
            .drawer-content {
                padding: 20px;
                padding-bottom: 40px; /* Extra space for auth buttons at bottom */
            }
            
            /* Mobile Search Wrapper */
            .mobile-search-wrapper {
                margin-bottom: 20px;
                padding-bottom: 15px;
                border-bottom: 2px solid rgba(31, 20, 74, 0.1);
            }
            
            .mobile-search-form .input-group {
                box-shadow: 0 5px 20px rgba(31, 20, 74, 0.15);
                border-radius: 12px;
                overflow: hidden;
            }
            
            .mobile-search-form .form-control {
                border: 2px solid rgba(31, 20, 74, 0.2);
                border-left: none;
                padding: 12px 15px;
                font-size: 14px;
            }
            
            .mobile-search-form .form-control:focus {
                box-shadow: none;
                border-color: var(--accent-color);
            }
            
            .mobile-search-form .btn {
                background: var(--gradient-accent);
                border: none;
                padding: 0 20px;
                font-size: 16px;
            }
            
            .mobile-search-form .btn:hover {
                transform: none;
                opacity: 0.9;
            }
            
            .drawer-content .navbar-nav {
                flex-direction: column;
                padding: 0;
            }
            
            .drawer-content .nav-item {
                margin: 0;
                width: 100%;
            }
            
            .drawer-content .nav-link {
                padding: 15px 20px;
                border-radius: 12px;
                margin: 5px 0;
                background: rgba(255, 255, 255, 0.05);
                border: 1px solid rgba(255, 255, 255, 0.1);
                color: white !important;
                display: flex;
                align-items: center;
                gap: 10px;
                transition: all 0.3s ease;
            }
            
            .drawer-content .nav-link:hover,
            .drawer-content .nav-link.active {
                background: linear-gradient(135deg, var(--secondary-color), #3cc7b8) !important;
                color: white !important;
                transform: translateX(-5px);
                box-shadow: 0 5px 15px rgba(45, 188, 174, 0.3);
                border-color: transparent;
            }
            
            .drawer-content .nav-link i {
                font-size: 1.1rem;
                width: 20px;
                text-align: center;
            }
            
            .drawer-content .btn {
                transition: all 0.3s ease;
            }
            
            .drawer-content .btn:hover {
                transform: translateY(-2px);
                box-shadow: 0 5px 15px rgba(45, 188, 174, 0.3);
            }
            
            /* Drawer Sections */
            .drawer-section {
                margin: 20px 0;
                padding: 15px 0;
                border-top: 1px solid rgba(31, 20, 74, 0.1);
                border-bottom: 1px solid rgba(31, 20, 74, 0.1);
            }
            
            .drawer-section-title {
                font-size: 1rem;
                font-weight: 600;
                color: var(--primary-color);
                margin-bottom: 15px;
                display: flex;
                align-items: center;
            }
            
            /* Categories Grid */
            .categories-grid {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                gap: 10px;
            }
            
            .category-card {
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                padding: 20px 12px;
                min-height: 150px;
                background: rgba(31, 20, 74, 0.05);
                border: 1px solid rgba(31, 20, 74, 0.1);
                border-radius: 14px;
                text-decoration: none;
                color: var(--text-color);
                transition: all 0.3s ease;
                min-height: 90px;
                text-align: center;
            }
            
            .category-card:hover {
                background: linear-gradient(135deg, var(--secondary-color), #3cc7b8);
                color: white;
                transform: translateY(-3px);
                box-shadow: 0 5px 15px rgba(45, 188, 174, 0.3);
                border-color: transparent;
            }
            
            .category-card i {
                font-size: 1.5rem;
                margin-bottom: 8px;
                transition: all 0.3s ease;
            }
            
            .category-card:hover i {
                color: white !important;
                transform: scale(1.1);
            }
            
            .category-card span {
                font-size: 0.85rem;
                font-weight: 500;
                line-height: 1.3;
            }
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
            
            /* Mobile Navbar */
        .navbar .container {
            padding-left: 15px !important;
            padding-right: 15px !important;
            margin-left: 0 !important;
            margin-right: 0 !important;
            max-width: 100% !important;
            width: 100% !important;
        }
        
        .navbar-brand {
            margin-right: auto !important;
        }
        
        html[dir="rtl"] .navbar-brand {
            margin-right: 0 !important;
            margin-left: auto !important;
        }
        
        .navbar-brand img {
            height: 40px;
        }
        
        .navbar-toggler {
            border: none;
            padding: 4px 8px;
            background: transparent !important;
            margin-left: auto !important;
        }
        
        html[dir="rtl"] .navbar-toggler {
            margin-left: 0 !important;
            margin-right: auto !important;
        }
        
        .navbar-toggler:focus {
            box-shadow: none;
        }
        
        .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%2845, 188, 174, 1%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
        }

        }
        
        /* Hide navbar-collapse on mobile - use drawer instead */
        @media (max-width: 991px) {
            .navbar-collapse {
                display: none !important;
            }
        }
        
        /* Desktop navbar-collapse styling */
        @media (min-width: 992px) {
            .navbar-collapse {
                background: transparent;
                backdrop-filter: none;
                padding: 0;
                border-radius: 0;
                margin-top: 0;
                margin-left: 0;
                margin-right: 0;
                box-shadow: none;
            }
            
            .navbar-nav {
                gap: 5px;
                text-align: right;
            }
            
            .nav-item.dropdown {
                position: relative;
            }
            
            .nav-item.dropdown .dropdown-menu {
                position: absolute !important;
                top: 100% !important;
                left: auto !important;
                right: 0 !important;
                display: none;
                min-width: 200px;
                background: rgba(255, 255, 255, 0.98) !important;
                border: 1px solid rgba(0, 0, 0, 0.1) !important;
                border-radius: 12px !important;
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15) !important;
                padding: 10px !important;
            }
            
            .nav-item.dropdown .dropdown-menu.show {
                display: block !important;
            }
            
            .nav-item.dropdown .dropdown-menu .dropdown-item {
                color: var(--primary-color) !important;
                padding: 10px 15px;
                border-radius: 8px;
                transition: all 0.3s ease;
            }
            
            .nav-item.dropdown .dropdown-menu .dropdown-item:hover {
                background: rgba(45, 188, 174, 0.1) !important;
                transform: translateX(-5px);
            }
        }
            
        /* Mobile styles for navbar-collapse */
        @media (max-width: 991px) {
            .navbar-collapse.show {
                display: block !important;
                background: rgba(255, 255, 255, 0.95);
                backdrop-filter: blur(10px);
                padding: 20px 15px;
                border-radius: 15px;
                margin-top: 15px;
                margin-left: -15px;
                margin-right: -15px;
                box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
            }
            
            .navbar-nav {
                gap: 10px;
                text-align: center;
            }
            
            .navbar-nav .nav-link {
                padding: 12px 15px !important;
                border-radius: 10px;
                color: var(--primary-color) !important;
            }
            
            .navbar-nav .nav-link:hover {
                background: var(--gradient-accent);
                color: white !important;
            }
            
            /* Mobile user profile */
            .user-profile-link {
                flex-direction: column;
                align-items: center !important;
                background: rgba(45, 188, 174, 0.1);
                color: var(--primary-color) !important;
            }
            
            .user-avatar {
                margin-bottom: 5px;
                width: 35px !important;
                height: 35px !important;
            }
            
            .user-name {
                font-size: 0.9rem;
                color: var(--primary-color) !important;
            }
            
            /* Mobile dropdown */
            .dropdown-menu {
                position: static !important;
                transform: none !important;
                width: 100%;
                margin-top: 10px !important;
                background: transparent !important;
                border: none !important;
                box-shadow: none !important;
                text-align: center;
            }
            
            .dropdown-item {
                color: var(--primary-color) !important;
                border-radius: 8px;
                margin: 5px 0;
                transition: all 0.3s ease;
            }
            
            .dropdown-item:hover {
                background: rgba(45, 188, 174, 0.1) !important;
                color: var(--primary-color) !important;
            }
            
            .dropdown-toggle::after {
                display: none;
            }
            
            .btn-cta {
                margin: 15px auto 0 !important;
                display: block !important;
                width: fit-content !important;
                padding: 10px 25px !important;
                font-size: 0.9rem !important;
            }
            
            /* Mobile footer */
            .footer .col-lg-4,
            .footer .col-lg-2,
            .footer .col-lg-3 {
                margin-bottom: 30px;
                text-align: center;
            }
            
            /* Footer logo - Mobile only */
            .footer-logo-wrapper {
                justify-content: center !important;
            }
            
            .footer-logo {
                height: 35px;
                width: auto;
                max-width: 40%;
                margin: 0 auto !important;
            }
            
            .footer .d-flex.gap-3 {
                justify-content: center;
            }
            
            .footer .text-md-end {
                text-align: center !important;
            }
            
            /* Mobile Browse Sections */
            .browse-sections-mobile {
                margin-top: 20px;
                padding-top: 20px;
                border-top: 1px solid rgba(45, 188, 174, 0.2);
            }
            
            .nav-section-header {
                font-weight: 600;
                color: var(--primary-color);
                font-size: 1rem;
                margin-bottom: 15px;
                text-align: center;
                padding: 10px 15px;
                background: rgba(45, 188, 174, 0.1);
                border-radius: 10px;
            }
            
            .nav-categories {
                display: flex;
                flex-direction: column;
                gap: 8px;
            }
            
            .nav-category-link {
                display: flex;
                align-items: center;
                padding: 12px 15px;
                color: var(--text-color) !important;
                text-decoration: none;
                border-radius: 8px;
                transition: all 0.3s ease;
                background: rgba(255, 255, 255, 0.5);
                border: 1px solid rgba(45, 188, 174, 0.1);
            }
            
            .nav-category-link:hover {
                background: rgba(45, 188, 174, 0.1);
                transform: translateX(-5px);
                border-color: rgba(45, 188, 174, 0.3);
                color: var(--primary-color) !important;
            }
            
            .nav-category-link i {
                font-size: 1.1rem;
                width: 20px;
            }
        }
        
        @media (max-width: 576px) {
            .section-title {
                font-size: 1.5rem;
            }
            
            .navbar-brand img {
                height: 35px;
            }
            
            .btn-gold,
            .btn-cta {
                width: 100%;
                max-width: 300px;
                margin: 0 auto;
                display: block;
            }
            
            .card {
                margin-bottom: 20px;
            }
            
            .price-tag {
                font-size: 1.2rem;
            }
        }
        
        @media (max-width: 768px) {
            .newsletter-form {
                flex-wrap: nowrap;
            }
            
            .newsletter-form .newsletter-input {
                flex: 1 1 auto;
                min-width: 0;
                height: 36px !important;
                font-size: 0.875rem;
                padding: 0.375rem 0.75rem;
            }
            
            .newsletter-form .newsletter-btn {
                flex: 0 0 auto;
                width: auto;
                height: 36px !important;
                padding: 0.25rem 0.6rem !important;
                min-width: 40px;
                font-size: 0.875rem;
            }
        }

    </style>
    <style>
        /* منع تمرير الصفحة عند فتح الدروار */
        body.no-scroll { overflow: hidden; }
        .btn-outline-primary {
            color: #7269b0 !important;
            border-color: #7269b0 !important;
        }
        .btn-outline-primary:hover,
        .btn-outline-primary:focus,
        .btn-outline-primary.active,
        .btn-outline-primary:active {
            background-color: #7269b0 !important;
            border-color: #7269b0 !important;
            color: #ffffff !important;
        }
        .badge.bg-primary {
            background-color: #7269b0 !important;
            color: #ffffff !important;
        }
    </style>
    
    @yield('styles')
    @stack('styles')
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container" style="max-width: 1320px;">
            <a class="navbar-brand ms-4" href="{{ route('home') }}">
                <img src="{{ asset('images/logo/logo.png') }}" alt="Your Events">
            </a>
            
            <button class="navbar-toggler" type="button" onclick="toggleDrawer()">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse d-none d-lg-flex" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">
                            {{ __('nav.home') }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('services.*') ? 'active' : '' }}" href="{{ route('services.index') }}">
                            {{ __('nav.services') }}
                        </a>
                    </li>
                    @if(\App\Models\Package::count() > 0)
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('packages.*') ? 'active' : '' }}" href="{{ route('packages.index') }}">
                            <i ></i>{{ __('common.packages') }}
                        </a>
                    </li>
                    @endif
                    @if(\App\Models\Gallery::count() > 0)
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('gallery.*') ? 'active' : '' }}" href="{{ route('gallery.index') }}">
                            <i class="fas fa-images me-1"></i>{{ __('common.gallery') }}
                        </a>
                    </li>
                    @endif
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('contact') ? 'active' : '' }}" href="{{ route('contact') }}">
                            {{ __('nav.contact') }}
                        </a>
                    </li>
                    
                    <!-- Browse Sections - Mobile Only -->
                    <li class="nav-item d-lg-none browse-sections-mobile">
                        <div class="nav-section-header">
                            <i class="fas fa-folder me-2"></i>{{ __('common.browse_categories') }}
                        </div>
                        <div class="nav-categories">
                            @php
                                $categories = \App\Models\Category::active()->ordered()->get();
                            @endphp
                            @foreach($categories as $category)
                            <a class="nav-category-link" href="{{ route('services.index', ['category' => $category->id]) }}">
                                <span class="category-mobile-wrapper">
                                    <span class="category-mobile-icon"><i class="{{ $category->icon }} me-2"></i></span>
                                    <span class="category-mobile-name">{{ $category->name }}</span>
                                </span>
                            </a>
                            @endforeach
                        </div>
                    </li>
                </ul>
                
                <ul class="navbar-nav navbar-right-section">
                    <!-- Search Form -->
                    <li class="nav-item search-nav-item">
                        <form action="{{ route('search') }}" method="GET" class="search-form" id="searchForm" data-autocomplete-url="{{ route('search.autocomplete') }}" data-no-results-text="{{ __('common.no_results') }}" data-service-text="{{ __('common.service') }}" data-package-text="{{ __('common.package') }}" data-default-image="{{ asset('images/service-default.svg') }}">
                            <div class="search-input-wrapper">
                                <input type="text" 
                                       name="q" 
                                       class="form-control search-input" 
                                       placeholder="{{ __('nav.search_placeholder') }}" 
                                       autocomplete="off"
                                       id="searchInput">
                                <button type="submit" class="search-button">
                                    <i class="fas fa-search"></i>
                                </button>
                                <div class="search-autocomplete" id="searchAutocomplete"></div>
                            </div>
                        </form>
                    </li>
                    
                    <!-- Shopping Cart with Dropdown -->
                    <li class="nav-item cart-nav-item dropdown">
                        <a class="nav-link cart-link {{ request()->routeIs('cart.*') ? 'active' : '' }}" 
                           href="{{ route('cart.index') }}"
                           id="cartDropdown" 
                           role="button" 
                           data-bs-toggle="dropdown" 
                           data-bs-auto-close="outside"
                           aria-expanded="false">
                            <div class="cart-icon-wrapper">
                                <i class="fas fa-shopping-cart"></i>
                                @php
                                    $cartCount = \App\Models\CartItem::getCartCount();
                                @endphp
                                @if($cartCount > 0)
                                <span class="cart-badge" id="cart-count">{{ $cartCount }}</span>
                                @endif
                            </div>
                        </a>
                        
                        <!-- Cart Dropdown -->
                        <div class="dropdown-menu dropdown-menu-end cart-dropdown" aria-labelledby="cartDropdown" id="cartDropdownMenu">
                            @php
                                $cartItems = \App\Models\CartItem::getCartItems();
                                $cartTotal = \App\Models\CartItem::getCartTotal();
                                $cartCount = \App\Models\CartItem::getCartCount();
                            @endphp
                            
                            @include('partials.cart-dropdown', ['cartItems' => $cartItems, 'cartTotal' => $cartTotal, 'cartCount' => $cartCount])
                        </div>
                    </li>
                    
                    @auth
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle user-profile-link" href="#" role="button" 
                               data-bs-toggle="dropdown" aria-expanded="false">
                                <div class="user-avatar">
                                    <i class="fas fa-user"></i>
                                </div>
                                <span class="user-name">{{ Auth::user()->name }}</span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end enhanced-dropdown animated-white-bg" style="text-align: center; min-width: 200px;">
                                <li><a class="dropdown-item" href="{{ route('profile.show') }}" style="text-align: center; display: flex; align-items: center; justify-content: center; gap: 10px;">
                                    <i class="fas fa-user-circle"></i><span>{{ __('common.profile') }}</span>
                                </a></li>
                                <li><a class="dropdown-item" href="{{ route('booking.my-bookings') }}" style="text-align: center; display: flex; align-items: center; justify-content: center; gap: 10px;">
                                    <i class="fas fa-calendar-check"></i><span>{{ __('common.my_bookings') }}</span>
                                </a></li>
                                <li><a class="dropdown-item" href="{{ route('quotes.index') }}" style="text-align: center; display: flex; align-items: center; justify-content: center; gap: 10px;">
                                    <i class="fas fa-file-invoice-dollar"></i><span>{{ __('common.quotes') }}</span>
                                </a></li>
                                <li><a class="dropdown-item" href="{{ route('wishlist.index') }}" style="text-align: center; display: flex; align-items: center; justify-content: center; gap: 10px;">
                                    <i class="fas fa-heart"></i><span>{{ __('common.wishlist') }}</span>
                                </a></li>
                                @if(Auth::user()->is_admin)
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="{{ route('admin.dashboard') }}" style="text-align: center; display: flex; align-items: center; justify-content: center; gap: 10px;">
                                        <i class="fas fa-tachometer-alt"></i><span>{{ __('common.dashboard') }}</span>
                                    </a></li>
                                @endif
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}" class="d-inline w-100">
                                        @csrf
                                        <button type="submit" class="dropdown-item" style="text-align: center; display: flex; align-items: center; justify-content: center; gap: 10px; width: 100%;">
                                            <i class="fas fa-sign-out-alt"></i><span>{{ __('common.logout') }}</span>
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link login-link" href="{{ route('login') }}">
                                <i class="fas fa-sign-in-alt me-1"></i>{{ __('common.login') }}
                            </a>
                        </li>
                        
                    @endauth
                    <!-- Language Switcher -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            @php $locale = app()->getLocale(); @endphp
                            <i class="fas fa-language me-1"></i>{{ $locale === 'ar' ? __('nav.arabic') : __('nav.english') }}
                        </a>
                        <ul class="dropdown-menu animated-white-bg" style="text-align: center; min-width: 120px;">
                            <li><a class="dropdown-item" href="{{ route('lang.switch', ['locale' => 'ar']) }}" style="text-align: center;">{{ __('nav.arabic') }}</a></li>
                            <li><a class="dropdown-item" href="{{ route('lang.switch', ['locale' => 'en']) }}" style="text-align: center;">{{ __('nav.english') }}</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-gold ms-2 cta-button" href="{{ route('services.index') }}">
                            <span class="btn-text">
                                <i ></i>{{ __('buttons.establish_event') }}
                            </span>
                            <div class="btn-glow"></div>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    
    <!-- Main Content -->
    <main style="padding-top: 80px; position: relative; z-index: 1;">
        @yield('content')
    </main>
    
    <!-- Footer -->
    <footer class="footer py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-4">
                    <div class="d-flex align-items-center mb-3 footer-logo-wrapper">
                        <img src="{{ asset('images/logo/logo.png') }}" alt="Your Events" class="footer-logo me-3">
                        <h5 class="mb-0 text-white"></h5>
                    </div>
                    <p class="text-white mb-3">
                        {{ __('common.footer_description') }}
                    </p>
                    <div class="d-flex gap-3">
                        @php
                            $facebookUrl = \App\Models\Setting::get('facebook_url');
                            $twitterUrl = \App\Models\Setting::get('twitter_url');
                            $instagramUrl = \App\Models\Setting::get('instagram_url');
                            $linkedinUrl = \App\Models\Setting::get('linkedin_url');
                            $youtubeUrl = \App\Models\Setting::get('youtube_url');
                            $tiktokUrl = \App\Models\Setting::get('tiktok_url');
                            $whatsappUrl = \App\Models\Setting::get('whatsapp_url');
                        @endphp
                        
                        @if($facebookUrl)
                            <a href="{{ $facebookUrl }}" target="_blank" rel="noopener noreferrer" class="text-white hover-social-facebook" title="Facebook">
                                <i class="fab fa-facebook fa-2x"></i>
                            </a>
                        @endif
                        
                        @if($twitterUrl)
                            <a href="{{ $twitterUrl }}" target="_blank" rel="noopener noreferrer" class="text-white hover-social-twitter" title="X (Twitter)">
                                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                                </svg>
                            </a>
                        @endif
                        
                        @if($instagramUrl)
                            <a href="{{ $instagramUrl }}" target="_blank" rel="noopener noreferrer" class="text-white hover-social-instagram" title="Instagram">
                                <i class="fab fa-instagram fa-2x"></i>
                            </a>
                        @endif
                        
                        @if($linkedinUrl)
                            <a href="{{ $linkedinUrl }}" target="_blank" rel="noopener noreferrer" class="text-white hover-social-linkedin" title="LinkedIn">
                                <i class="fab fa-linkedin fa-2x"></i>
                            </a>
                        @endif
                        
                        @if($youtubeUrl)
                            <a href="{{ $youtubeUrl }}" target="_blank" rel="noopener noreferrer" class="text-white hover-social-youtube" title="YouTube">
                                <i class="fab fa-youtube fa-2x"></i>
                            </a>
                        @endif
                        
                        @if($tiktokUrl)
                            <a href="{{ $tiktokUrl }}" target="_blank" rel="noopener noreferrer" class="text-white hover-social-tiktok" title="TikTok">
                                <i class="fab fa-tiktok fa-2x"></i>
                            </a>
                        @endif
                        
                        @if($whatsappUrl)
                            <a href="{{ $whatsappUrl }}" target="_blank" rel="noopener noreferrer" class="text-white hover-social-whatsapp" title="WhatsApp">
                                <i class="fab fa-whatsapp fa-2x"></i>
                            </a>
                        @endif
                    </div>
                </div>
                
                <!-- روابط سريعة + معلومات التواصل جنبًا إلى جنب بشكل احترافي -->
                <div class="col-lg-5 col-md-12 mb-4">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <h6 class="mb-3 text-white">{{ __('common.quick_links') }}</h6>
                            <ul class="list-unstyled">
                                <li class="mb-2"><a href="{{ route('home') }}" class="text-white text-decoration-none">{{ __('common.quick_links_home') }}</a></li>
                                <li class="mb-2"><a href="{{ route('services.index') }}" class="text-white text-decoration-none">{{ __('common.quick_links_services') }}</a></li>
                                <li class="mb-2"><a href="{{ route('packages.index') }}" class="text-white text-decoration-none">{{ __('common.quick_links_packages') }}</a></li>
                                @if(\App\Models\Gallery::count() > 0)
                                <li class="mb-2"><a href="{{ route('gallery.index') }}" class="text-white text-decoration-none">{{ __('common.quick_links_gallery') }}</a></li>
                                @endif
                                <li class="mb-2"><a href="{{ route('contact') }}" class="text-white text-decoration-none">{{ __('common.quick_links_contact') }}</a></li>
                                <li class="mb-2"><a href="{{ route('suppliers.register') }}" class="text-white text-decoration-none">{{ __('common.register_as_supplier') }}</a></li>
                                <li class="mb-2"><a href="{{ route('supplier.login') }}" class="text-white text-decoration-none">{{ __('common.supplier_login') }}</a></li>
                                <li class="mb-2">
                                    <a href="{{ route('terms') }}" class="text-white text-decoration-none">
                                        {{ __('common.quick_links_terms') }}
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6 class="mb-3 text-white">{{ __('common.contact_info') }}</h6>
                            <ul class="list-unstyled">
                                <li class="mb-2">
                                    <a href="tel:{{ preg_replace('/\s+/', '', \App\Models\Setting::get('contact_phone')) }}" class="text-white text-decoration-none phone-ltr" dir="ltr">
                                        <span>{{ \App\Models\Setting::get('contact_phone') }}</span>
                                    </a>
                                </li>
                                <li class="mb-2">
                                    <a href="mailto:{{ \App\Models\Setting::get('contact_email') }}" class="text-white text-decoration-none">
                                        {{ \App\Models\Setting::get('contact_email') }}
                                    </a>
                                </li>
                                <li class="mb-2">
                                    <span class="text-white">{{ \App\Models\Setting::get('contact_address', 'المملكة العربية السعودية، مدينة الرياض، حي العليا، شارع العليا') }}</span>
                                </li>
                                <!-- <li class="mb-2 d-flex align-items-center">
                                    <i class="fas fa-clock text-primary me-2"></i>
                                    <span class="text-white">{{ \App\Models\Setting::get('working_hours', 'السبت - الخميس: 9:00 ص - 6:00 م') }}</span>
                                </li> -->
                            </ul>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-3 mb-4">
                    <h6 class="mb-3 text-white">{{ __('common.newsletter') }}</h6>
                    <p class="text-white mb-3">{{ __('common.newsletter_desc') }}</p>
                    <form class="d-flex align-items-center gap-2 newsletter-form">
                        <input type="email" class="form-control newsletter-input" placeholder="{{ __('common.email_placeholder') }}" style="flex: 1; min-width: 0; height: 38px;">
                        <button class="btn btn-primary newsletter-btn" type="submit" style="height: 38px; padding: 0.375rem 0.75rem; flex-shrink: 0;">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </form>
                </div>
            </div>
            
            <hr class="my-4" style="border-color: var(--border-color);">
            
            <div class="row align-items-center">
                <div class="col-md-6 order-md-2 text-md-start">
                    <p class="text-white mb-0">
                        &copy; {{ date('Y') }} Your Events. {{ __('common.all_rights_reserved') }}.
                    </p>
                </div>
                <div class="col-md-6 order-md-1 text-md-end">
                    <!-- Payment Icons Left (on RTL this appears left) -->
                    <div class="payment-icons mb-3 text-end">
                        <i class="fab fa-cc-visa" aria-label="Visa"></i>
                        <i class="fab fa-cc-mastercard" aria-label="Mastercard"></i>
                        <i class="fab fa-apple-pay" aria-label="Apple Pay"></i>
                        @php
                            $madaLogo = public_path('images/payments/mada.png');
                            $stcPayLogo = public_path('images/payments/stc-pay.png');
                        @endphp
                        @if (file_exists($madaLogo))
                            <img src="{{ asset('images/payments/mada.png') }}" alt="Mada" class="payment-logo">
                        @else
                            <span class="payment-badge">Mada</span>
                        @endif
                        @if (file_exists($stcPayLogo))
                            <img src="{{ asset('images/payments/stc-pay.png') }}" alt="STC Pay" class="payment-logo">
                        @else
                            <span class="payment-badge">STC Pay</span>
                        @endif
                    </div>
                    <!-- Links on same level -->
                    <div class="footer-links text-end">
                        <a href="{{ route('terms') }}" class="text-white text-decoration-none me-3 footer-link">
                            {{ __('common.terms_and_conditions') }}
                        </a>
                        <span class="text-white mx-1">-</span>
                        <a href="{{ route('privacy') }}" class="text-white text-decoration-none footer-link" target="_blank">
                            {{ __('common.privacy_policy') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    
    <!-- Mobile Bottom Navigation -->
    <div class="klb-mobile-bottom hide-desktop">
        <div class="mobile-nav-items">
            <a href="{{ route('home') }}" class="mobile-nav-item {{ request()->routeIs('home') ? 'active' : '' }}">
                <i class="fas fa-home"></i>
                <span>{{ __('nav.home') }}</span>
            </a>
            <a href="{{ route('services.index') }}" class="mobile-nav-item {{ request()->routeIs('services.*') ? 'active' : '' }}">
                <img src="{{ asset('images/logo/White.png') }}" alt="Services" class="mobile-nav-icon">
                <span>{{ __('nav.services') }}</span>
            </a>
            <a href="{{ route('cart.index') }}" class="mobile-nav-item {{ request()->routeIs('cart.*') ? 'active' : '' }}">
                <i class="fas fa-shopping-cart"></i>
                <span>{{ __('common.cart') }}</span>
                @php
                    $cartCountMobile = \App\Models\CartItem::getCartCount();
                @endphp
                @if($cartCountMobile > 0)
                <span class="badge">{{ $cartCountMobile }}</span>
                @endif
            </a>
            <a href="#" class="mobile-nav-item" onclick="toggleDrawer(); return false;">
                <i class="fas fa-bars"></i>
                <span>{{ __('common.menu') }}</span>
            </a>
        </div>
    </div>
    
    <!-- Drawer Overlay -->
    <div class="drawer-overlay d-lg-none" id="drawerOverlay" onclick="toggleDrawer()"></div>
    
    <!-- Site Drawer (Mobile Menu) -->
    <div class="site-drawer color-layout-white animated-white-bg d-lg-none" id="siteDrawer">
        <div class="drawer-header">
            <img src="{{ asset('images/logo/logo.png') }}" alt="Your Events" height="20">
            <button class="drawer-close" onclick="toggleDrawer()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="drawer-content">
            <!-- Mobile Search Form -->
            <div class="mobile-search-wrapper">
                <form action="{{ route('search') }}" method="GET" class="mobile-search-form">
                    <div class="d-flex gap-2 align-items-center">
                        <input type="text" 
                               name="q" 
                               class="form-control" 
                               placeholder="{{ __('common.search_placeholder') }}"
                               style="flex: 1;"
                               required>
                        <button type="submit" class="btn btn-primary rounded-circle" style="width: 42px; height: 42px; padding: 0; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>
            </div>
            
            <!-- Main Navigation -->
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">
                        {{ __('nav.home') }}
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('services.*') ? 'active' : '' }}" href="{{ route('services.index') }}">
                        {{ __('nav.services') }}
                    </a>
                </li>
                @if(\App\Models\Package::count() > 0)
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('packages.*') ? 'active' : '' }}" href="{{ route('packages.index') }}">
                        {{ __('common.packages') }}
                    </a>
                </li>
                @endif
                @if(\App\Models\Gallery::count() > 0)
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('gallery.*') ? 'active' : '' }}" href="{{ route('gallery.index') }}">
                        <i class="fas fa-images"></i>{{ __('common.gallery') }}
                    </a>
                </li>
                @endif
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('contact') ? 'active' : '' }}" href="{{ route('contact') }}">
                        {{ __('nav.contact') }}
                    </a>
                </li>
            </ul>

            <!-- Language Switcher (Mobile) -->
            <div class="drawer-section mt-3">
                <h6 class="drawer-section-title"><i class="fas fa-language me-2"></i>{{ __('common.language') }}</h6>
                <div class="d-flex gap-2">
                    <a href="{{ route('lang.switch', ['locale' => 'ar']) }}" class="btn btn-outline-primary flex-fill">العربية</a>
                    <a href="{{ route('lang.switch', ['locale' => 'en']) }}" class="btn btn-outline-primary flex-fill">English</a>
                </div>
            </div>
            
            <!-- Categories Section -->
            @php
                $categories = \App\Models\Category::active()->ordered()->get();
            @endphp
            @if($categories->count() > 0)
            <div class="drawer-section">
                <h6 class="drawer-section-title">
                    <i class="fas fa-folder me-2"></i>{{ __('common.browse_categories') }}
                </h6>
                <div class="categories-grid">
                    @foreach($categories as $category)
                    <a href="{{ route('services.index', ['category' => $category->id]) }}" class="category-card">
                        <div class="category-mobile-wrapper">
                            <div class="category-mobile-icon"><i class="{{ $category->icon }}"></i></div>
                            <span class="category-mobile-name">{{ $category->name }}</span>
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>
            @endif
            
            <!-- User Section -->
            <ul class="navbar-nav mt-3">
                <li class="nav-item">
                    <hr style="border-color: rgba(31, 20, 74, 0.2); margin: 15px 0;">
                </li>
                
                @auth
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('profile.show') }}">
                            <i class="fas fa-user-circle"></i>{{ __('common.profile') }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('booking.my-bookings') }}">
                            <i class="fas fa-calendar-check"></i>{{ __('common.my_bookings') }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('quotes.index') }}">
                            <i class="fas fa-file-invoice-dollar"></i>{{ __('common.quotes') }}
                        </a>
                    </li>
                    @if(Auth::user()->is_admin)
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('admin.dashboard') }}">
                                <i class="fas fa-tachometer-alt"></i>{{ __('common.dashboard') }}
                            </a>
                        </li>
                    @endif
                    <li class="nav-item">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="nav-link btn w-100 text-start border-0">
                                <i class="fas fa-sign-out-alt"></i>{{ __('common.logout') }}
                            </button>
                        </form>
                    </li>
                    <li class="nav-item mt-3">
                        <a href="{{ route('booking.create') }}" class="btn btn-primary w-100" style="border-radius: 12px;">
                            <i class="fas fa-calendar-plus me-2"></i>{{ __('common.start_event') }}
                        </a>
                    </li>
                @else
                    <li class="nav-item mt-3">
                        <div class="d-flex gap-2">
                            <a href="{{ route('login') }}" class="btn btn-outline-primary flex-fill" style="border-radius: 12px;">
                                <i class="fas fa-sign-in-alt me-1"></i>{{ __('common.login') }}
                            </a>
                            <a href="{{ route('booking.create') }}" class="btn btn-primary flex-fill" style="border-radius: 12px;">
                                <i class="fas fa-calendar-plus me-1"></i>{{ __('common.start_event') }}
                            </a>
                        </div>
                    </li>
                @endauth
            </ul>
        </div>
    </div>
    
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
            
            // Cart Dropdown Hover for Desktop
            const cartNavItem = document.querySelector('.cart-nav-item');
            const cartDropdown = document.querySelector('.cart-dropdown');
            
            if (cartNavItem && cartDropdown) {
                // For desktop: show on hover
                if (window.innerWidth >= 992) {
                    let hideTimeout;
                    
                    cartNavItem.addEventListener('mouseenter', function() {
                        clearTimeout(hideTimeout);
                        cartDropdown.classList.add('show');
                    });
                    
                    cartNavItem.addEventListener('mouseleave', function() {
                        hideTimeout = setTimeout(() => {
                            cartDropdown.classList.remove('show');
                        }, 300);
                    });
                    
                    cartDropdown.addEventListener('mouseenter', function() {
                        clearTimeout(hideTimeout);
                    });
                    
                    cartDropdown.addEventListener('mouseleave', function() {
                        hideTimeout = setTimeout(() => {
                            cartDropdown.classList.remove('show');
                        }, 300);
                    });
                }
            }
            
            // Add smooth hover effects for interactive elements
            const interactiveElements = document.querySelectorAll('.nav-link, .btn, .service-card');
            interactiveElements.forEach(element => {
                element.addEventListener('mouseenter', function() {
                    this.style.transition = 'all 0.3s cubic-bezier(0.4, 0, 0.2, 1)';
                });
            });
        });
        
        // Toggle Drawer Function
        function toggleDrawer() {
            const drawer = document.getElementById('siteDrawer');
            const overlay = document.getElementById('drawerOverlay');
            
            if (drawer && overlay) {
                drawer.classList.toggle('show');
                overlay.classList.toggle('show');
                
                // منع التمرير عند فتح القائمة
                if (drawer.classList.contains('show')) {
                    document.body.style.overflow = 'hidden';
                } else {
                    document.body.style.overflow = '';
                }
            }
        }
        
        // Close drawer on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const drawer = document.getElementById('siteDrawer');
                if (drawer && drawer.classList.contains('show')) {
                    toggleDrawer();
                }
            }
        });
    </script>
    
    <!-- Cart Update Function -->
    <script>
        // دالة عامة لتحديث السلة بشكل لحظي
        function updateCartDropdown() {
            return fetch('{{ route("cart.dropdown") }}')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // تحديث العداد
                        updateCartCount(data.cart_count);
                        
                        // تحديث محتوى القائمة المنسدلة
                        const cartDropdownMenu = document.getElementById('cartDropdownMenu');
                        if (cartDropdownMenu) {
                            cartDropdownMenu.innerHTML = data.html;
                        }
                        
                        return data;
                    }
                })
                .catch(error => {
                    console.error('Error updating cart dropdown:', error);
                });
        }
        
        // دالة تحديث عداد السلة
        function updateCartCount(count) {
            let cartBadge = document.getElementById('cart-count');
            if (count > 0) {
                if (cartBadge) {
                    cartBadge.textContent = count;
                    cartBadge.style.display = 'inline-block';
                } else {
                    // إنشاء العداد إذا لم يكن موجوداً
                    const cartIcon = document.querySelector('.cart-icon-wrapper');
                    if (cartIcon) {
                        const badge = document.createElement('span');
                        badge.className = 'cart-badge';
                        badge.id = 'cart-count';
                        badge.textContent = count;
                        cartIcon.appendChild(badge);
                    }
                }
            } else {
                // إخفاء العداد إذا كانت السلة فارغة
                if (cartBadge) {
                    cartBadge.style.display = 'none';
                }
            }
        }
        
        // جعل الدوال متاحة بشكل عام
        window.updateCartDropdown = updateCartDropdown;
        window.updateCartCount = updateCartCount;
    </script>
    
    @stack('scripts')
    @yield('scripts')
    
    <!-- Mobile Side Menu Scrolling Enhancement -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const navbarCollapse = document.querySelector('.navbar-collapse');
            const navbarToggler = document.querySelector('.navbar-toggler');
            
            if (navbarCollapse && window.innerWidth <= 991) {
                // إضافة مؤشر التمرير
                function addScrollIndicators() {
                    // إزالة المؤشرات الموجودة
                    const existingIndicators = navbarCollapse.querySelectorAll('.scroll-indicator');
                    existingIndicators.forEach(indicator => indicator.remove());
                    
                    // إضافة مؤشر علوي
                    const topIndicator = document.createElement('div');
                    topIndicator.className = 'scroll-indicator scroll-indicator-top';
                    topIndicator.innerHTML = '<i class="fas fa-chevron-up"></i>';
                    topIndicator.style.cssText = `
                        position: sticky;
                        top: 0;
                        background: linear-gradient(180deg, rgba(31, 20, 74, 0.9) 0%, transparent 100%);
                        text-align: center;
                        padding: 10px;
                        color: var(--gold-color);
                        font-size: 0.8rem;
                        opacity: 0;
                        transition: opacity 0.3s ease;
                        z-index: 10;
                        pointer-events: none;
                    `;
                    
                    // إضافة مؤشر سفلي
                    const bottomIndicator = document.createElement('div');
                    bottomIndicator.className = 'scroll-indicator scroll-indicator-bottom';
                    bottomIndicator.innerHTML = '<i class="fas fa-chevron-down"></i>';
                    bottomIndicator.style.cssText = `
                        position: sticky;
                        bottom: 0;
                        background: linear-gradient(0deg, rgba(31, 20, 74, 0.9) 0%, transparent 100%);
                        text-align: center;
                        padding: 10px;
                        color: var(--gold-color);
                        font-size: 0.8rem;
                        opacity: 1;
                        transition: opacity 0.3s ease;
                        z-index: 10;
                        pointer-events: none;
                    `;
                    
                    navbarCollapse.insertBefore(topIndicator, navbarCollapse.firstChild);
                    navbarCollapse.appendChild(bottomIndicator);
                    
                    // تحديث المؤشرات عند التمرير
                    navbarCollapse.addEventListener('scroll', function() {
                        const scrollTop = this.scrollTop;
                        const scrollHeight = this.scrollHeight;
                        const clientHeight = this.clientHeight;
                        const scrollBottom = scrollHeight - clientHeight - scrollTop;
                        
                        // مؤشر علوي
                        if (scrollTop > 20) {
                            topIndicator.style.opacity = '1';
                        } else {
                            topIndicator.style.opacity = '0';
                        }
                        
                        // مؤشر سفلي
                        if (scrollBottom > 20) {
                            bottomIndicator.style.opacity = '1';
                        } else {
                            bottomIndicator.style.opacity = '0';
                        }
                    });
                }
                
                // إضافة المؤشرات عند فتح القائمة
                navbarToggler.addEventListener('click', function() {
                    setTimeout(() => {
                        if (navbarCollapse.classList.contains('show')) {
                            addScrollIndicators();
                        }
                    }, 350); // انتظار انتهاء الانيميشن
                });
                
                // تحسين التمرير بالعجلة
                navbarCollapse.addEventListener('wheel', function(e) {
                    e.stopPropagation();
                    
                    // تمرير سلس
                    const delta = e.deltaY;
                    const scrollAmount = delta * 0.8; // تقليل سرعة التمرير
                    
                    this.scrollBy({
                        top: scrollAmount,
                        behavior: 'smooth'
                    });
                }, { passive: true });
                
                // إضافة تأثير النقر على الروابط
                const navLinks = navbarCollapse.querySelectorAll('.nav-link');
                navLinks.forEach(link => {
                    link.addEventListener('click', function() {
                        // إضافة تأثير بصري
                        this.style.transform = 'scale(0.95)';
                        setTimeout(() => {
                            this.style.transform = '';
                        }, 150);
                    });
                });
            }
            
            // إعادة تهيئة عند تغيير حجم الشاشة
            window.addEventListener('resize', function() {
                if (window.innerWidth > 991) {
                    const indicators = document.querySelectorAll('.scroll-indicator');
                    indicators.forEach(indicator => indicator.remove());
                }
            });
        });
    </script>
    
    <!-- Search Autocomplete Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const searchAutocomplete = document.getElementById('searchAutocomplete');
            const searchForm = document.getElementById('searchForm');
            let searchTimeout;
            
            if (searchInput && searchAutocomplete) {
                searchInput.addEventListener('input', function() {
                    clearTimeout(searchTimeout);
                    const query = this.value.trim();
                    
                    if (query.length < 2) {
                        searchAutocomplete.classList.remove('show');
                        searchAutocomplete.innerHTML = '';
                        return;
                    }
                    
                    searchTimeout = setTimeout(function() {
                        const baseUrl = searchForm ? searchForm.dataset.autocompleteUrl : '';
                        if (!baseUrl) {
                            return;
                        }
                        fetch(`${baseUrl}?q=${encodeURIComponent(query)}`)
                            .then(response => response.json())
                            .then(data => {
                                if (data.length === 0) {
                                    const noResultsText = searchForm ? (searchForm.dataset.noResultsText || '') : '';
                                    searchAutocomplete.innerHTML = `<div class="autocomplete-empty"><i class="fas fa-search-minus mb-2"></i><p>${noResultsText}</p></div>`;
                                    searchAutocomplete.classList.add('show');
                                    return;
                                }
                                
                                let html = '';
                                data.forEach(item => {
                                    const badgeClass = item.type === 'service' ? 'bg-primary' : 'bg-success';
                                    const serviceText = searchForm ? (searchForm.dataset.serviceText || '') : '';
                                    const packageText = searchForm ? (searchForm.dataset.packageText || '') : '';
                                    const defaultImageUrl = searchForm ? (searchForm.dataset.defaultImage || '') : '';
                                    const badgeText = item.type === 'service' ? serviceText : packageText;
                                    const imageUrl = item.image || defaultImageUrl;
                                    
                                    html += `
                                        <a href="${item.url}" class="autocomplete-item">
                                            <img src="${imageUrl}" alt="${item.name}" class="autocomplete-image">
                                            <div class="autocomplete-details">
                                                <p class="autocomplete-name">${item.name}</p>
                                                <p class="autocomplete-type">
                                                    <span class="badge autocomplete-badge ${badgeClass}">${badgeText}</span>
                                                </p>
                                            </div>
                                            <i class="fas fa-arrow-left text-muted"></i>
                                        </a>
                                    `;
                                });
                                
                                searchAutocomplete.innerHTML = html;
                                searchAutocomplete.classList.add('show');
                            })
                            .catch(error => {
                                console.error('Search error:', error);
                                searchAutocomplete.classList.remove('show');
                            });
                    }, 300);
                });
                
                // Close autocomplete when clicking outside
                document.addEventListener('click', function(e) {
                    if (!searchInput.contains(e.target) && !searchAutocomplete.contains(e.target)) {
                        searchAutocomplete.classList.remove('show');
                    }
                });
                
                // Close autocomplete on ESC key
                document.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape') {
                        searchAutocomplete.classList.remove('show');
                    }
                });
            }
        });
    </script>
    
    <!-- Drawer Toggle Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const drawer = document.getElementById('siteDrawer');
            const overlay = document.getElementById('drawerOverlay');
            const navbar = document.querySelector('.navbar');

            // إضافة/إزالة خلفية الهيدر عند التمرير
            function updateNavbarOnScroll() {
                if (!navbar) return;
                if (window.scrollY > 10) {
                    navbar.classList.add('scrolled');
                } else {
                    navbar.classList.remove('scrolled');
                }
            }
            updateNavbarOnScroll();
            window.addEventListener('scroll', updateNavbarOnScroll);

            // توفير الدالة للاستخدام من الزر
            window.toggleDrawer = function(forceState = null) {
                if (!drawer || !overlay) return;
                const isOpen = drawer.classList.contains('show');
                const shouldOpen = forceState === null ? !isOpen : !!forceState;

                if (shouldOpen) {
                    drawer.classList.add('show');
                    overlay.classList.add('show');
                    document.body.classList.add('no-scroll');
                } else {
                    drawer.classList.remove('show');
                    overlay.classList.remove('show');
                    document.body.classList.remove('no-scroll');
                }
            };

            // إغلاق عند الضغط خارج القائمة أو زر ESC
            if (overlay) {
                overlay.addEventListener('click', function() { window.toggleDrawer(false); });
            }
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') { window.toggleDrawer(false); }
            });

            // إغلاق عند تغيير الحجم إلى الديسكتوب
            window.addEventListener('resize', function() {
                if (window.innerWidth > 991) {
                    window.toggleDrawer(false);
                }
            });

            // إغلاق عند الضغط على روابط النافبار داخل الدروار
            if (drawer) {
                const navLinks = drawer.querySelectorAll('.nav-link');
                navLinks.forEach(function(link) {
                    link.addEventListener('click', function() { window.toggleDrawer(false); });
                });
            }
        });
    </script>

    <!-- Bootstrap Bundle JS (includes Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Fix Dropdown Click -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize all dropdowns
            var dropdownElementList = [].slice.call(document.querySelectorAll('.dropdown-toggle'));
            dropdownElementList.forEach(function(dropdownToggleEl) {
                // Create Bootstrap dropdown instance
                new bootstrap.Dropdown(dropdownToggleEl);
            });
            
            // Manual click handler for dropdowns that might not work
            document.querySelectorAll('.nav-item.dropdown .dropdown-toggle').forEach(function(toggle) {
                toggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    var dropdownMenu = this.nextElementSibling;
                    if (!dropdownMenu || !dropdownMenu.classList.contains('dropdown-menu')) {
                        dropdownMenu = this.parentElement.querySelector('.dropdown-menu');
                    }
                    
                    if (dropdownMenu) {
                        // Close all other dropdowns first
                        document.querySelectorAll('.dropdown-menu.show').forEach(function(menu) {
                            if (menu !== dropdownMenu) {
                                menu.classList.remove('show');
                            }
                        });
                        
                        // Toggle current dropdown
                        dropdownMenu.classList.toggle('show');
                        this.setAttribute('aria-expanded', dropdownMenu.classList.contains('show'));
                    }
                });
            });
            
            // Close dropdown when clicking outside
            document.addEventListener('click', function(e) {
                if (!e.target.closest('.nav-item.dropdown')) {
                    document.querySelectorAll('.nav-item.dropdown .dropdown-menu.show').forEach(function(menu) {
                        menu.classList.remove('show');
                    });
                    document.querySelectorAll('.nav-item.dropdown .dropdown-toggle').forEach(function(toggle) {
                        toggle.setAttribute('aria-expanded', 'false');
                    });
                }
            });
        });
    </script>

    <!-- Universal Modal Close Fallback: ensures the × button closes media overlays -->
    <script>
        // Close modal when clicking on backdrop or outside the dialog
        document.addEventListener('click', function(e) {
            const openBackdrop = document.querySelector('.modal-backdrop');
            const openModal = document.querySelector('.modal.show');
            if (!openModal) return;

            const dialog = openModal.querySelector('.modal-dialog, .modal-content');
            const clickedInsideDialog = dialog && (dialog === e.target || dialog.contains(e.target));

            // If backdrop is clicked OR any area outside the dialog is clicked
            const backdropClicked = openBackdrop && (openBackdrop === e.target || openBackdrop.contains(e.target));
            if (!clickedInsideDialog || backdropClicked) {
                try {
                    if (window.bootstrap && bootstrap.Modal) {
                        const instance = bootstrap.Modal.getOrCreateInstance(openModal);
                        instance.hide();
                    } else {
                        openModal.classList.remove('show');
                        openModal.style.display = 'none';
                        openModal.setAttribute('aria-hidden', 'true');
                    }
                } finally {
                    document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
                    document.body.classList.remove('modal-open');
                    document.body.style.removeProperty('padding-right');
                }
            }
        }, true);

        document.addEventListener('click', function(e) {
            const closeTrigger = e.target.closest('.btn-close, [data-bs-dismiss="modal"], [data-dismiss="modal"]');
            if (!closeTrigger) return;

            const modalEl = closeTrigger.closest('.modal');
            if (!modalEl) {
                // If no modal container found, still remove any backdrops in case of stuck overlay
                document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
                document.body.classList.remove('modal-open');
                return;
            }

            try {
                if (window.bootstrap && bootstrap.Modal) {
                    const instance = bootstrap.Modal.getOrCreateInstance(modalEl);
                    instance.hide();
                } else {
                    // Fallback if Bootstrap JS isn't available for any reason
                    modalEl.classList.remove('show');
                    modalEl.style.display = 'none';
                    modalEl.setAttribute('aria-hidden', 'true');
                    document.body.classList.remove('modal-open');
                    document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
                }
            } catch (err) {
                // Hard fallback
                modalEl.classList.remove('show');
                modalEl.style.display = 'none';
                modalEl.setAttribute('aria-hidden', 'true');
                document.body.classList.remove('modal-open');
                document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
            }
        });

        // ESC key closes visible modals as a safety net
        document.addEventListener('keydown', function(e) {
            if (e.key !== 'Escape') return;
            const openModals = document.querySelectorAll('.modal.show');
            openModals.forEach(modalEl => {
                try {
                    if (window.bootstrap && bootstrap.Modal) {
                        const instance = bootstrap.Modal.getOrCreateInstance(modalEl);
                        instance.hide();
                    } else {
                        modalEl.classList.remove('show');
                        modalEl.style.display = 'none';
                    }
                } catch (_) {
                    modalEl.classList.remove('show');
                    modalEl.style.display = 'none';
                }
            });
            document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
            document.body.classList.remove('modal-open');
        });
    </script>
</body>
</html>

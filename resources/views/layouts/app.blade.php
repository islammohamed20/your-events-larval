<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', 'Your Events - حوّل مناسبتك العادية إلى لحظة استثنائية')</title>

    @php
        $siteName = \App\Models\Setting::get('site_name', 'Your Events');
        $siteDescription = \App\Models\Setting::get('site_description', 'حوّل مناسبتك العادية إلى لحظة استثنائية');
        $metaKeywords = \App\Models\Setting::get('meta_keywords');
        $ogImage = \App\Models\Setting::get('og_image');
        $logo = \App\Models\Setting::get('logo');
        $canonical = url()->current();
    @endphp
    <meta name="description" content="{{ $siteDescription }}">
    @if($metaKeywords)
    <meta name="keywords" content="{{ $metaKeywords }}">
    @endif
    <link rel="canonical" href="{{ $canonical }}">

    <!-- Open Graph -->
    <meta property="og:title" content="@yield('title', $siteName)">
    <meta property="og:description" content="{{ $siteDescription }}">
    <meta property="og:url" content="{{ $canonical }}">
    @if($ogImage)
    <meta property="og:image" content="{{ Storage::url($ogImage) }}">
    @elseif($logo)
    <meta property="og:image" content="{{ Storage::url($logo) }}">
    @endif
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="{{ $siteName }}">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('title', $siteName)">
    <meta name="twitter:description" content="{{ $siteDescription }}">
    @if($ogImage)
    <meta name="twitter:image" content="{{ Storage::url($ogImage) }}">
    @elseif($logo)
    <meta name="twitter:image" content="{{ Storage::url($logo) }}">
    @endif
    
    <!-- Favicon -->
    @php
        $favicon = \App\Models\Setting::get('favicon');
    @endphp
    @if($favicon)
        <link rel="icon" type="image/png" href="{{ Storage::url($favicon) }}">
        <link rel="shortcut icon" type="image/png" href="{{ Storage::url($favicon) }}">
    @endif
    
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
                position: static !important;
                float: none;
                width: 100%;
                margin-top: 0;
                border-radius: 0;
                box-shadow: none;
                background: rgba(255, 255, 255, 0.95);
                backdrop-filter: blur(10px);
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
            max-width: 100px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        
        .enhanced-dropdown {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            margin-top: 10px;
            position: absolute !important;
            z-index: 9999 !important;
            display: none;
        }
        
        .enhanced-dropdown.show {
            display: block !important;
            visibility: visible !important;
            opacity: 1 !important;
            transform: translateY(0) !important;
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
            padding: 10px 25px;
            font-weight: 600;
            font-size: 0.95rem;
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(240, 199, 29, 0.4);
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
            color: #000000 !important;
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
                height: 100vh;
                background: linear-gradient(135deg, rgba(31, 20, 74, 0.98) 0%, rgba(45, 26, 94, 0.98) 100%);
                backdrop-filter: blur(15px);
                -webkit-backdrop-filter: blur(15px);
                z-index: 9999;
                transition: right 0.4s cubic-bezier(0.4, 0, 0.2, 1);
                overflow-y: auto;
                box-shadow: -5px 0 20px rgba(0, 0, 0, 0.3);
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
        
        .navbar-brand img {
            height: 40px;
        }
        
        .navbar-toggler {
            border: none;
            padding: 4px 8px;
            background: transparent !important;
            margin-left: auto !important;
        }
        
        .navbar-toggler:focus {
            box-shadow: none;
        }
        
        .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%2845, 188, 174, 1%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
        }
        
        /* Hide navbar-collapse on mobile - use drawer instead */
        @media (max-width: 991px) {
            .navbar-collapse {
                display: none !important;
            }
        }
        
        .navbar-collapse {
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
                            <i class="fas fa-home me-1"></i>الرئيسية
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('services.*') ? 'active' : '' }}" href="{{ route('services.index') }}">
                            <i class="fas fa-cogs me-1"></i>خدماتنا
                        </a>
                    </li>
                    @if(\App\Models\Package::count() > 0)
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('packages.*') ? 'active' : '' }}" href="{{ route('packages.index') }}">
                            <i class="fas fa-box me-1"></i>الباقات
                        </a>
                    </li>
                    @endif
                    @if(\App\Models\Gallery::count() > 0)
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('gallery.*') ? 'active' : '' }}" href="{{ route('gallery.index') }}">
                            <i class="fas fa-images me-1"></i>المعرض
                        </a>
                    </li>
                    @endif
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('contact') ? 'active' : '' }}" href="{{ route('contact') }}">
                            <i class="fas fa-envelope me-1"></i>اتصل بنا
                        </a>
                    </li>
                    
                    <!-- Browse Sections - Mobile Only -->
                    <li class="nav-item d-lg-none browse-sections-mobile">
                        <div class="nav-section-header">
                            <i class="fas fa-folder me-2"></i>تصفح الأقسام
                        </div>
                        <div class="nav-categories">
                            @php
                                $categories = \App\Models\Category::active()->ordered()->get();
                            @endphp
                            @foreach($categories as $category)
                            <a class="nav-category-link" href="{{ route('services.index', ['category' => $category->id]) }}">
                                <i class="{{ $category->icon }} me-2"></i>
                                {{ $category->name }}
                            </a>
                            @endforeach
                        </div>
                    </li>
                </ul>
                
                <ul class="navbar-nav navbar-right-section">
                    <!-- Search Form -->
                    <li class="nav-item search-nav-item">
                        <form action="{{ route('search') }}" method="GET" class="search-form" id="searchForm">
                            <div class="search-input-wrapper">
                                <input type="text" 
                                       name="q" 
                                       class="form-control search-input" 
                                       placeholder="ابحث عن خدمة أو باقة..." 
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
                            <ul class="dropdown-menu dropdown-menu-end enhanced-dropdown">
                                <li><a class="dropdown-item" href="{{ route('profile.show') }}">
                                    <i class="fas fa-user-circle me-2"></i>الملف الشخصي
                                </a></li>
                                <li><a class="dropdown-item" href="{{ route('booking.my-bookings') }}">
                                    <i class="fas fa-calendar-check me-2"></i>حجوزاتي
                                </a></li>
                                <li><a class="dropdown-item" href="{{ route('quotes.index') }}">
                                    <i class="fas fa-file-invoice-dollar me-2"></i>عروض الأسعار
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
                        نحن في Your Events نقدم خدمات تنظيم المناسبات والأحداث بأعلى مستويات الجودة والإبداع
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
                                <i class="fab fa-x-twitter fa-2x"></i>
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
                
                <div class="col-lg-2 col-md-6 mb-4">
                    <h6 class="mb-3 text-white">روابط سريعة</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="{{ route('home') }}" class="text-white text-decoration-none">الرئيسية</a></li>
                        <li class="mb-2"><a href="{{ route('services.index') }}" class="text-white text-decoration-none">خدماتنا</a></li>
                        <li class="mb-2"><a href="{{ route('packages.index') }}" class="text-white text-decoration-none">الباقات</a></li>
                        <li class="mb-2"><a href="{{ route('gallery.index') }}" class="text-white text-decoration-none">المعرض</a></li>
                        <li class="mb-2"><a href="{{ route('contact') }}" class="text-white text-decoration-none">اتصل بنا</a></li>
                        <li class="mb-2">
                            <a href="{{ route('terms') }}" class="text-white text-decoration-none">
                                <i class="fas fa-file-contract me-1"></i>الشروط والأحكام
                            </a>
                        </li>
                    </ul>
                </div>
                
                <div class="col-lg-3 col-md-6 mb-4">
                    <h6 class="mb-3 text-white">معلومات التواصل</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <i class="fas fa-phone text-primary me-2"></i>
                            <a href="tel:{{ \App\Models\Setting::get('contact_phone') }}" class="text-white text-decoration-none">
                                {{ \App\Models\Setting::get('contact_phone') }}
                            </a>
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-envelope text-primary me-2"></i>
                            <a href="mailto:{{ \App\Models\Setting::get('contact_email') }}" class="text-white text-decoration-none">
                                {{ \App\Models\Setting::get('contact_email') }}
                            </a>
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-map-marker-alt text-primary me-2"></i>
                            <span class="text-white">{{ \App\Models\Setting::get('contact_address', 'الرياض، المملكة العربية السعودية') }}</span>
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-clock text-primary me-2"></i>
                            <span class="text-white">{{ \App\Models\Setting::get('working_hours', 'السبت - الخميس: 9:00 ص - 6:00 م') }}</span>
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
                        &copy; {{ date('Y') }} Masar Digital Group. جميع الحقوق محفوظة.
                    </p>
                </div>
                <div class="col-md-6 text-md-end">
                    <a href="{{ route('terms') }}" class="text-white text-decoration-none me-3 footer-link">
                        <i class="fas fa-file-contract me-1"></i>الشروط والأحكام
                    </a>
                    <a href="#" class="text-white text-decoration-none me-3 footer-link">سياسة الخصوصية</a>
                    <a href="#" class="text-white text-decoration-none footer-link">شروط الاستخدام</a>
                </div>
            </div>
        </div>
    </footer>
    
    <!-- Mobile Bottom Navigation -->
    <div class="klb-mobile-bottom hide-desktop">
        <div class="mobile-nav-items">
            <a href="{{ route('home') }}" class="mobile-nav-item {{ request()->routeIs('home') ? 'active' : '' }}">
                <i class="fas fa-home"></i>
                <span>الرئيسية</span>
            </a>
            <a href="{{ route('services.index') }}" class="mobile-nav-item {{ request()->routeIs('services.*') ? 'active' : '' }}">
                <i class="fas fa-cogs"></i>
                <span>الخدمات</span>
            </a>
            <a href="{{ route('cart.index') }}" class="mobile-nav-item {{ request()->routeIs('cart.*') ? 'active' : '' }}">
                <i class="fas fa-shopping-cart"></i>
                <span>السلة</span>
                @php
                    $cartCountMobile = \App\Models\CartItem::getCartCount();
                @endphp
                @if($cartCountMobile > 0)
                <span class="badge">{{ $cartCountMobile }}</span>
                @endif
            </a>
            <a href="#" class="mobile-nav-item" onclick="toggleDrawer(); return false;">
                <i class="fas fa-bars"></i>
                <span>القائمة</span>
            </a>
        </div>
    </div>
    
    <!-- Drawer Overlay -->
    <div class="drawer-overlay d-lg-none" id="drawerOverlay" onclick="toggleDrawer()"></div>
    
    <!-- Site Drawer (Mobile Menu) -->
    <div class="site-drawer color-layout-white d-lg-none" id="siteDrawer">
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
                    <div class="input-group">
                        <input type="text" 
                               name="q" 
                               class="form-control" 
                               placeholder="ابحث عن خدمة أو باقة..."
                               required>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>
            </div>
            
            <!-- Main Navigation -->
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">
                        <i class="fas fa-home"></i>الرئيسية
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('services.*') ? 'active' : '' }}" href="{{ route('services.index') }}">
                        <i class="fas fa-cogs"></i>خدماتنا
                    </a>
                </li>
                @if(\App\Models\Package::count() > 0)
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('packages.*') ? 'active' : '' }}" href="{{ route('packages.index') }}">
                        <i class="fas fa-box"></i>الباقات
                    </a>
                </li>
                @endif
                @if(\App\Models\Gallery::count() > 0)
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('gallery.*') ? 'active' : '' }}" href="{{ route('gallery.index') }}">
                        <i class="fas fa-images"></i>المعرض
                    </a>
                </li>
                @endif
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('contact') ? 'active' : '' }}" href="{{ route('contact') }}">
                        <i class="fas fa-envelope"></i>اتصل بنا
                    </a>
                </li>
            </ul>
            
            <!-- Categories Section -->
            @php
                $categories = \App\Models\Category::active()->ordered()->get();
            @endphp
            @if($categories->count() > 0)
            <div class="drawer-section">
                <h6 class="drawer-section-title">
                    <i class="fas fa-folder me-2"></i>تصفح الأقسام
                </h6>
                <div class="categories-grid">
                    @foreach($categories as $category)
                    <a href="{{ route('services.index', ['category' => $category->id]) }}" class="category-card">
                        <i class="{{ $category->icon }}"></i>
                        <span>{{ $category->name }}</span>
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
                            <i class="fas fa-user-circle"></i>الملف الشخصي
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('booking.my-bookings') }}">
                            <i class="fas fa-calendar-check"></i>حجوزاتي
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('quotes.index') }}">
                            <i class="fas fa-file-invoice-dollar"></i>عروض الأسعار
                        </a>
                    </li>
                    @if(Auth::user()->is_admin)
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('admin.dashboard') }}">
                                <i class="fas fa-tachometer-alt"></i>لوحة التحكم
                            </a>
                        </li>
                    @endif
                    <li class="nav-item">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="nav-link btn w-100 text-start border-0">
                                <i class="fas fa-sign-out-alt"></i>تسجيل الخروج
                            </button>
                        </form>
                    </li>
                @else
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">
                            <i class="fas fa-sign-in-alt"></i>تسجيل الدخول
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('register') }}">
                            <i class="fas fa-user-plus"></i>إنشاء حساب
                        </a>
                    </li>
                @endauth
                
                <li class="nav-item mt-3">
                    <a href="{{ route('booking.create') }}" class="btn btn-primary w-100" style="border-radius: 12px;">
                        <i class="fas fa-calendar-plus me-2"></i>احجز الآن
                    </a>
                </li>
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
                        fetch(`{{ route('search.autocomplete') }}?q=${encodeURIComponent(query)}`)
                            .then(response => response.json())
                            .then(data => {
                                if (data.length === 0) {
                                    searchAutocomplete.innerHTML = '<div class="autocomplete-empty"><i class="fas fa-search-minus mb-2"></i><p>لا توجد نتائج</p></div>';
                                    searchAutocomplete.classList.add('show');
                                    return;
                                }
                                
                                let html = '';
                                data.forEach(item => {
                                    const badgeClass = item.type === 'service' ? 'bg-primary' : 'bg-success';
                                    const badgeText = item.type === 'service' ? 'خدمة' : 'باقة';
                                    const imageUrl = item.image || '{{ asset("images/service-default.svg") }}';
                                    
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
</body>
</html>
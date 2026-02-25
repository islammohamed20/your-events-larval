<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', __('common.admin_dashboard_title') . ' - Your Events')</title>
    
    <!-- Favicon -->
    @php
        $faviconSetting = \App\Models\Setting::get('favicon') ?: \App\Models\Setting::get('site_favicon');
        $faviconUrlSetting = \App\Models\Setting::get('favicon_url');
        $fallbackFaviconUrl = asset('images/logo/logo.png');
        $faviconUrl = $faviconSetting
            ? (filter_var($faviconSetting, FILTER_VALIDATE_URL) ? $faviconSetting : url(Storage::url($faviconSetting)))
            : ($faviconUrlSetting ? (filter_var($faviconUrlSetting, FILTER_VALIDATE_URL) ? $faviconUrlSetting : url($faviconUrlSetting)) : $fallbackFaviconUrl);
        $faviconPath = parse_url($faviconUrl, PHP_URL_PATH);
        $faviconExt = strtolower(pathinfo($faviconPath ?? $faviconUrl, PATHINFO_EXTENSION));
        $faviconType = $faviconExt === 'ico' ? 'image/x-icon' : 'image/png';
    @endphp
    <link rel="icon" type="{{ $faviconType }}" href="{{ $faviconUrl }}">
    <link rel="shortcut icon" type="{{ $faviconType }}" href="{{ $faviconUrl }}">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@200;300;400;500;700;800;900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <!-- Custom Admin CSS -->
    <style>
        :root {
            --primary-color: #1f144a;
            --accent-color: #ef4870;
            --secondary-color: #2dbcae;
            --gold-color: #f0c71d;
            --purple-light: #7269b0;
            --text-color: #222222;
            --bg-light: #FFFFFF;
            --hover-color: #f56b8a;
            --sidebar-bg: #1f144a;
            --sidebar-hover: #2d1a5e;
        }

        * {
            font-family: 'Tajawal', sans-serif;
        }

        body {
            direction: rtl;
            text-align: right;
            background-color: #f8f9fa;
        }

        .sidebar {
            background-color: var(--sidebar-bg);
            height: 100vh;
            position: fixed;
            top: 0;
            right: 0;
            width: 250px;
            transition: all 0.3s;
            z-index: 1000;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .sidebar-menu {
            flex: 1;
            overflow-y: auto;
            overflow-x: hidden;
            padding-bottom: 20px;
        }

        /* Custom Scrollbar للقائمة الجانبية */
        .sidebar-menu::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar-menu::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.05);
        }

        .sidebar-menu::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 3px;
        }

        .sidebar-menu::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        .sidebar-footer {
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding: 15px 0;
            background-color: var(--sidebar-bg);
        }

        .logout-button {
            text-align: center !important;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 12px 20px;
            background-color: rgba(239, 72, 112, 0.1);
            border-radius: 8px;
            margin: 0 15px;
            transition: all 0.3s;
        }

        .logout-button:hover {
            background-color: var(--accent-color);
            color: white !important;
            transform: translateY(-2px);
        }

        .sidebar .nav-link {
            color: #ecf0f1;
            padding: 15px 20px;
            border-radius: 0;
            transition: all 0.3s;
        }

        .sidebar .nav-link:hover {
            background-color: var(--sidebar-hover);
            color: var(--gold-color);
        }

        .sidebar .nav-link.active {
            background-color: var(--accent-color);
            color: white;
        }

        .main-content {
            margin-right: 250px;
            padding: 20px;
            transition: all 0.3s;
        }

        .admin-header {
            background: white;
            padding: 15px 30px;
            margin: -20px -20px 20px -20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            border-bottom: 3px solid var(--primary-color);
        }

        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .card-header {
            background: var(--primary-color);
            color: white;
            border-radius: 10px 10px 0 0 !important;
            font-weight: 600;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), var(--purple-light));
            border: none;
            border-radius: 25px;
            padding: 10px 25px;
            font-weight: 500;
            box-shadow: 0 4px 15px rgba(31, 20, 74, 0.3);
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, var(--accent-color), var(--hover-color));
            box-shadow: 0 6px 20px rgba(239, 72, 112, 0.4);
            transform: translateY(-2px);
        }

        .table th {
            background-color: #f8f9fa;
            font-weight: 600;
        }

        /* Pagination tweaks: ضبط حجم الأيقونات ليطابق حجم الأرقام */
        .pagination .page-link i {
            font-size: 14px !important; /* حجم ثابت لتفادي التضخيم */
            line-height: 1 !important; /* يقلل الارتفاع العمودي للأيقونة */
            vertical-align: middle !important; /* تحسين المحاذاة مع النص */
        }
        /* تأكيد ضبط الحجم حتى لو خرجت الأيقونة خارج عنصر الصفحة */
        i.fa-chevron-left,
        i.fa-chevron-right,
        i[class*="fa-chevron-"] {
            font-size: 14px !important;
            line-height: 1 !important;
            vertical-align: middle !important;
            display: inline-block !important;
        }

        /* دعم أيقونات SVG من Font Awesome (إذا وُجدت) */
        .pagination .page-link svg,
        .pagination .page-link .svg-inline--fa {
            width: 14px !important;
            height: 14px !important;
            vertical-align: middle !important;
        }

        /* قيود عامة لأي أيقونة شيفرون أينما ظهرت في لوحة الإدارة */
        .svg-inline--fa[data-icon="chevron-left"],
        .svg-inline--fa[data-icon="chevron-right"],
        svg[aria-hidden="true"][data-icon="chevron-left"],
        svg[aria-hidden="true"][data-icon="chevron-right"],
        i.fa-chevron-left,
        i.fa-chevron-right,
        [class*="fa-chevron-"] {
            width: 14px !important;
            height: 14px !important;
            font-size: 14px !important;
            line-height: 14px !important;
        }

        /* توحيد حجم عناصر الترقيم حتى للقوالب الافتراضية بدون .page-link */
        .pagination li > a,
        .pagination li > span {
            font-size: 0.95rem;
            padding: 0.375rem 0.75rem;
            line-height: 1.5;
        }
        /* خيار إضافي لتوازن ارتفاع الروابط إن لزم */
        .pagination .page-link {
            padding: 0.375rem 0.75rem;
            font-size: 0.95rem;
        }

        .stats-card {
            background: linear-gradient(135deg, var(--primary-color), var(--purple-light));
            color: white;
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(31, 20, 74, 0.2);
            padding: 20px;
            margin-bottom: 20px;
            transition: transform 0.3s;
        }

        .stats-card:hover {
            transform: translateY(-2px);
        }

        .sidebar-brand {
            padding: 20px;
            border-bottom: 1px solid #34495e;
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
        
        .sidebar-brand img {
            max-width: 100%;
            height: auto;
            max-height: 60px;
            object-fit: contain;
            margin-bottom: 10px;
        }

        .sidebar-brand h4 {
            color: var(--primary-color);
            margin: 0;
            font-weight: 700;
        }
        
        .sidebar-brand small {
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.85rem;
        }

        .sidebar-language {
            width: 100%;
            margin-top: 10px;
        }

        .sidebar-language .btn {
            width: 100%;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.15);
            color: #ffffff;
            border-radius: 10px;
            padding: 8px 10px;
        }

        .sidebar-language .btn:hover,
        .sidebar-language .btn:focus {
            background: rgba(255, 255, 255, 0.12);
            border-color: rgba(255, 255, 255, 0.25);
            color: #ffffff;
        }

        .sidebar-language .dropdown-menu {
            width: 100%;
            min-width: 100%;
            background: #ffffff;
            border-radius: 10px;
            border: none;
            padding: 6px 0;
            overflow: hidden;
        }

        .sidebar-language .dropdown-item {
            padding: 10px 12px;
            text-align: center;
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(100%);
                width: 100%;
            }
            
            .sidebar.show {
                transform: translateX(0);
            }
            
            .main-content {
                margin-right: 0;
            }
            
            .sidebar-menu {
                max-height: calc(100vh - 200px);
            }
            
            .logout-button {
                margin: 0 10px;
                padding: 10px 15px;
                font-size: 0.95rem;
            }
            
            .sidebar-brand {
                padding: 15px;
            }
            
            .sidebar-brand img {
                max-height: 50px;
            }
        }
    </style>
    
    @yield('styles')
</head>
<body data-confirm-delete="{{ __('common.confirm_delete') }}">
    <!-- Sidebar -->
    <nav class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            @php
                $logo = \App\Models\Setting::get('logo');
                $defaultLogo = asset('images/logo/logo.png');
                $logoUrl = $logo
                    ? (filter_var($logo, FILTER_VALIDATE_URL) ? $logo : url(\Illuminate\Support\Facades\Storage::url($logo)))
                    : $defaultLogo;
            @endphp
            <img src="{{ $logoUrl }}" alt="Your Events" onerror="this.onerror=null;this.src='{{ $defaultLogo }}';">
            <small>{{ __('common.admin_dashboard_title') }}</small>
            <div class="sidebar-language dropdown">
                <button class="btn btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    @php $locale = app()->getLocale(); @endphp
                    <i class="fas fa-language me-1"></i>{{ $locale === 'ar' ? __('nav.arabic') : __('nav.english') }}
                </button>
                <ul class="dropdown-menu">
                    <li>
                        <a class="dropdown-item" href="{{ route('lang.switch', ['locale' => 'ar']) }}">
                            {{ __('nav.arabic') }}
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="{{ route('lang.switch', ['locale' => 'en']) }}">
                            {{ __('nav.english') }}
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        
        <div class="sidebar-menu">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" 
                       href="{{ route('admin.dashboard') }}">
                        <i class="fas fa-tachometer-alt me-2"></i>{{ __('common.admin_dashboard_main') }}
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}" 
                       href="{{ route('admin.categories.index') }}">
                        <i class="fas fa-folder me-2"></i>{{ __('common.categories') }}
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.packages.*') ? 'active' : '' }}" 
                       href="{{ route('admin.packages.index') }}">
                        <i class="fas fa-box me-2"></i>{{ __('common.packages') }}
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.services.*') ? 'active' : '' }}" 
                       href="{{ route('admin.services.index') }}">
                        <i class="fas fa-cogs me-2"></i>{{ __('common.services') }}
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.attributes.*') ? 'active' : '' }}" 
                       href="{{ route('admin.attributes.index') }}">
                        <i class="fas fa-tags me-2"></i>{{ __('common.service_attributes') }}
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.bookings.*') ? 'active' : '' }}" 
                       href="{{ route('admin.bookings.index') }}">
                        <i class="fas fa-calendar-check me-2"></i>{{ __('common.bookings') }}
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.quotes.*') ? 'active' : '' }}" 
                       href="{{ route('admin.quotes.index') }}">
                        <i class="fas fa-file-invoice-dollar me-2"></i>{{ __('common.quotes') }}
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.payments.*') ? 'active' : '' }}" 
                       href="{{ route('admin.payments.index') }}">
                        <i class="fas fa-credit-card me-2"></i>{{ __('common.payments') }}
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.gallery.*') ? 'active' : '' }}" 
                       href="{{ route('admin.gallery.index') }}">
                        <i class="fas fa-images me-2"></i>{{ __('common.gallery') }}
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.user-management.*') ? 'active' : '' }}" 
                       href="{{ route('admin.user-management.index') }}">
                        <i class="fas fa-users-cog me-2"></i>{{ __('common.user_management') }}
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.customers.*') ? 'active' : '' }}" 
                       href="{{ route('admin.customers.index') }}">
                        <i class="fas fa-user-tie me-2"></i>{{ __('common.customers_management') }}
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.suppliers.*') ? 'active' : '' }}" 
                       href="{{ route('admin.suppliers.index') }}">
                        <i class="fas fa-handshake me-2"></i>{{ __('common.suppliers_management') }}
                        @if(\App\Models\Supplier::where('status', 'pending')->count() > 0)
                            <span class="badge bg-warning text-dark ms-1">{{ \App\Models\Supplier::where('status', 'pending')->count() }}</span>
                        @endif
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.hero-slides.*') ? 'active' : '' }}" 
                       href="{{ route('admin.hero-slides.index') }}">
                        <i class="fas fa-images me-2"></i>{{ __('common.hero_slides') }}
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.homepage.*') ? 'active' : '' }}" 
                       href="{{ route('admin.homepage.index') }}">
                        <i class="fas fa-home me-2"></i>{{ __('common.homepage_management') }}
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}" 
                       href="{{ route('admin.reports.index') }}">
                        <i class="fas fa-chart-line me-2"></i>{{ __('common.reports') }}
                    </a>
                </li>
                <li class="nav-item ms-3">
                    <a class="nav-link {{ request()->routeIs('admin.reports.security') ? 'active' : '' }}" 
                       href="{{ route('admin.reports.security') }}">
                        <i class="fas fa-shield-alt me-2 text-muted"></i>
                        <small>{{ __('common.security_report') }}</small>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.login-activities.*') ? 'active' : '' }}" 
                       href="{{ route('admin.login-activities.index') }}">
                        <i class="fas fa-user-shield me-2"></i>{{ __('common.login_logs') }}
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}" 
                       href="{{ route('admin.settings.index') }}">
                        <i class="fas fa-cog me-2"></i>{{ __('common.settings') }}
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.contact-messages.*') ? 'active' : '' }}" 
                       href="{{ route('admin.contact-messages.index') }}">
                        <i class="fas fa-envelope me-2"></i>{{ __('common.contact_messages') }}
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.email-management.*') ? 'active' : '' }}" 
                       href="{{ route('admin.email-management.index') }}">
                        <i class="fas fa-envelope-open-text me-2"></i>{{ __('common.email_management') }}
                    </a>
                </li>
                <li class="nav-item ms-3">
                    <a class="nav-link {{ request()->routeIs('admin.email-templates.*') ? 'active' : '' }}" 
                       href="{{ route('admin.email-templates.index') }}">
                        <i class="fas fa-file-alt me-2 text-muted"></i>
                        <small>{{ __('common.templates') }}</small>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.otp.*') ? 'active' : '' }}" 
                       href="{{ route('admin.otp.index') }}">
                        <i class="fas fa-shield-alt me-2 text-muted"></i>
                        <small>{{ __('common.otp_codes') }}</small>
                    </a>
                </li>
                <li class="nav-item">
                    <hr style="border-color: #34495e; margin: 15px 20px;">
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('home') }}" target="_blank">
                        <i class="fas fa-external-link-alt me-2"></i>{{ __('common.view_site') }}
                    </a>
                </li>
            </ul>
        </div>
        
        <div class="sidebar-footer">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="logout-button nav-link btn border-0 w-auto">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>{{ __('common.logout') }}</span>
                </button>
            </form>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <div class="admin-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="mb-0">@yield('page-title', __('common.admin_dashboard_title'))</h3>
                    <small class="text-muted">@yield('page-description', __('common.admin_manage_site'))</small>
                </div>
                <div class="d-flex align-items-center">
                    <button class="btn btn-outline-primary d-md-none me-2" onclick="toggleSidebar()">
                        <i class="fas fa-bars"></i>
                    </button>

                    {{-- Notification Bell --}}
                    <div class="dropdown me-3" id="notificationDropdown">
                        <button class="btn btn-link position-relative p-0 border-0" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="font-size:22px;color:#555;" id="notifBellBtn">
                            <i class="fas fa-bell"></i>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" id="notification-badge" style="display:none;font-size:10px;min-width:18px;">0</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0" style="width:380px;max-height:450px;overflow:hidden;border-radius:12px;padding:0;" id="notifDropdownMenu">
                            {{-- Header --}}
                            <li class="px-3 py-2 d-flex justify-content-between align-items-center" style="background:linear-gradient(135deg,#1f144a,#3b2d7a);border-radius:12px 12px 0 0;">
                                <span class="fw-bold text-white" style="font-size:15px;"><i class="fas fa-bell me-1"></i> الإشعارات</span>
                                <button class="btn btn-sm text-white-50 p-0 border-0" onclick="markAllNotificationsRead()" title="قراءة الكل" style="font-size:12px;">
                                    <i class="fas fa-check-double me-1"></i>قراءة الكل
                                </button>
                            </li>
                            {{-- Notification Items Container --}}
                            <li>
                                <div id="notifItemsContainer" style="max-height:340px;overflow-y:auto;">
                                    <div class="text-center py-4 text-muted" id="notifEmpty">
                                        <i class="fas fa-bell-slash fa-2x mb-2 d-block" style="opacity:0.3;"></i>
                                        <span style="font-size:13px;">لا توجد إشعارات جديدة</span>
                                    </div>
                                </div>
                            </li>
                            {{-- Footer --}}
                            <li class="border-top">
                                <a href="{{ route('admin.notifications.index') }}" class="dropdown-item text-center py-2" style="font-size:13px;color:#1f144a;font-weight:600;">
                                    <i class="fas fa-list me-1"></i> عرض كل الإشعارات
                                </a>
                            </li>
                        </ul>
                    </div>

                    <span class="text-muted">{{ __('common.hello_name', ['name' => auth()->user()->name]) }}</span>
                </div>
            </div>
        </div>

        <!-- Alerts -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Page Content -->
        @yield('content')
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('show');
        }

        // ─── Notification Bell Dropdown ───────────────────────────────────
        function getNotifIcon(type) {
            const map = {
                quote: 'fas fa-file-invoice',
                order: 'fas fa-shopping-cart',
                booking: 'fas fa-calendar-check',
                payment: 'fas fa-credit-card',
                contact: 'fas fa-envelope',
                supplier: 'fas fa-truck',
                customer: 'fas fa-user-plus'
            };
            return map[type] || 'fas fa-bell';
        }
        function getNotifColor(color) {
            const map = { primary:'#1f144a', success:'#22c55e', warning:'#f59e0b', danger:'#ef4444', info:'#3b82f6' };
            return map[color] || color || '#1f144a';
        }

        function loadDropdownNotifications() {
            fetch('{{ route("admin.notifications.recent") }}', { headers: { 'Accept': 'application/json' } })
            .then(r => r.json())
            .then(data => {
                const container = document.getElementById('notifItemsContainer');
                const empty = document.getElementById('notifEmpty');
                if (!data.notifications || data.notifications.length === 0) {
                    container.innerHTML = '';
                    container.appendChild(empty);
                    empty.style.display = 'block';
                    return;
                }
                let html = '';
                data.notifications.forEach(n => {
                    const iconClass = n.icon || getNotifIcon(n.type);
                    const clr = getNotifColor(n.color);
                    html += `<a href="${n.link || '#'}" class="dropdown-item px-3 py-2 border-bottom notif-item" data-id="${n.id}" style="white-space:normal;">
                        <div class="d-flex align-items-start">
                            <div class="flex-shrink-0 me-2 mt-1" style="width:36px;height:36px;border-radius:50%;background:${clr}15;display:flex;align-items:center;justify-content:center;">
                                <i class="${iconClass}" style="color:${clr};font-size:14px;"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-bold" style="font-size:13px;color:#1f144a;">${n.title}</div>
                                <div class="text-muted" style="font-size:12px;line-height:1.4;">${n.message}</div>
                                <div class="text-muted mt-1" style="font-size:11px;"><i class="fas fa-clock me-1"></i>${n.created_at}</div>
                            </div>
                        </div>
                    </a>`;
                });
                container.innerHTML = html;

                // Mark as read on click
                container.querySelectorAll('.notif-item').forEach(item => {
                    item.addEventListener('click', function() {
                        const nid = this.dataset.id;
                        fetch('{{ url("admin/notifications") }}/' + nid + '/read', {
                            method: 'POST',
                            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
                        });
                    });
                });
            })
            .catch(() => {});
        }

        function markAllNotificationsRead() {
            fetch('{{ route("admin.notifications.read-all") }}', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
            }).then(() => {
                document.getElementById('notifItemsContainer').innerHTML =
                    '<div class="text-center py-4 text-muted"><i class="fas fa-bell-slash fa-2x mb-2 d-block" style="opacity:0.3;"></i><span style="font-size:13px;">لا توجد إشعارات جديدة</span></div>';
                const badge = document.getElementById('notification-badge');
                if (badge) badge.style.display = 'none';
            });
        }

        // Load notifications when dropdown opens
        document.getElementById('notifBellBtn').addEventListener('click', loadDropdownNotifications);

        // Initial badge count on page load
        fetch('{{ route("admin.notifications.count") }}', { headers: { 'Accept': 'application/json' } })
        .then(r => r.json())
        .then(data => {
            const badge = document.getElementById('notification-badge');
            if (badge) {
                badge.textContent = data.count;
                badge.style.display = data.count > 0 ? 'inline-block' : 'none';
            }
        }).catch(() => {});

        // Auto dismiss alerts
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);

        // Confirm delete actions
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('btn-delete') || 
                e.target.closest('.btn-delete') || 
                e.target.closest('form[data-confirm]')) {
                const confirmText = document.body.dataset.confirmDelete || '';
                if (!confirm(confirmText)) {
                    e.preventDefault();
                    return false;
                }
            }
        });

        // ========================================
        // Real-time Admin Notifications System
        // ========================================
        @php
            $notificationSettings = app(\App\Models\Setting::class)->getSettings([
                'notifications_enabled',
                'notification_sound_enabled', 
                'notification_refresh_interval',
                'notification_auto_dismiss'
            ]);
        @endphp
        
        @if(($notificationSettings['notifications_enabled'] ?? true))
        (function() {
            // Load last check time from localStorage
            let lastCheck = localStorage.getItem('admin_notifications_last_check');
            let notificationSound = null;
            let shownNotifications = JSON.parse(localStorage.getItem('admin_shown_notifications') || '[]');
            
            const refreshInterval = {{ $notificationSettings['notification_refresh_interval'] ?? 3 }} * 1000; // Convert to milliseconds
            const autoDismissTime = {{ $notificationSettings['notification_auto_dismiss'] ?? 10 }} * 1000; // Convert to milliseconds
            const soundEnabled = {{ ($notificationSettings['notification_sound_enabled'] ?? true) ? 'true' : 'false' }};
            
            // Clean old shown notifications (older than 1 hour)
            const oneHourAgo = new Date(Date.now() - 60 * 60 * 1000).toISOString();
            shownNotifications = shownNotifications.filter(item => item.time > oneHourAgo);
            localStorage.setItem('admin_shown_notifications', JSON.stringify(shownNotifications));
            
            // Create notification container
            const notifContainer = document.createElement('div');
            notifContainer.id = 'admin-notifications-container';
            notifContainer.style.cssText = 'position: fixed; top: 20px; left: 20px; z-index: 99999; max-width: 400px; direction: rtl;';
            document.body.appendChild(notifContainer);

            // Create notification sound (optional)
            if (soundEnabled) {
                try {
                    notificationSound = new Audio('data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2teleQ0NJYfO8tiJLyMrfcXv3pio');
                } catch(e) {}
            }

            function showNotification(notification) {
                // Check if this notification was already shown
                if (shownNotifications.some(item => item.id === notification.id)) {
                    return; // Don't show again
                }
                
                // Mark as shown
                shownNotifications.push({
                    id: notification.id,
                    time: new Date().toISOString()
                });
                localStorage.setItem('admin_shown_notifications', JSON.stringify(shownNotifications));
                
                const colorMap = {
                    'primary': '#667eea',
                    'success': '#28a745',
                    'warning': '#ffc107',
                    'danger': '#dc3545',
                    'info': '#17a2b8'
                };

                const notifEl = document.createElement('div');
                notifEl.className = 'admin-popup-notification animate__animated animate__fadeInLeft';
                notifEl.style.cssText = `
                    background: white;
                    border-radius: 12px;
                    padding: 15px;
                    margin-bottom: 10px;
                    box-shadow: 0 10px 40px rgba(0,0,0,0.2);
                    border-right: 4px solid ${colorMap[notification.color] || colorMap.primary};
                    cursor: pointer;
                    transition: all 0.3s ease;
                    opacity: 0;
                    transform: translateX(-20px);
                `;

                notifEl.innerHTML = `
                    <div style="display: flex; align-items: flex-start; gap: 12px;">
                        <div style="width: 40px; height: 40px; border-radius: 50%; background: ${colorMap[notification.color] || colorMap.primary}; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <i class="${notification.icon}" style="color: white; font-size: 16px;"></i>
                        </div>
                        <div style="flex: 1; min-width: 0;">
                            <div style="font-weight: 700; color: #1f144a; font-size: 14px; margin-bottom: 4px;">${notification.title}</div>
                            <div style="color: #666; font-size: 13px; line-height: 1.4; word-wrap: break-word;">${notification.message}</div>
                            <div style="color: #999; font-size: 11px; margin-top: 6px;">${notification.created_at}</div>
                        </div>
                        <button onclick="event.stopPropagation(); this.parentElement.parentElement.remove(); markNotificationRead(${notification.id}); removeFromShown(${notification.id});" style="background: none; border: none; color: #999; cursor: pointer; padding: 0; font-size: 18px; line-height: 1;">&times;</button>
                    </div>
                `;

                notifEl.onclick = function() {
                    if (notification.link) {
                        markNotificationRead(notification.id);
                        removeFromShown(notification.id);
                        window.location.href = notification.link;
                    }
                };

                notifContainer.appendChild(notifEl);

                // Animate in
                setTimeout(() => {
                    notifEl.style.opacity = '1';
                    notifEl.style.transform = 'translateX(0)';
                }, 10);

                // Play sound
                if (notificationSound && soundEnabled) {
                    try { notificationSound.play(); } catch(e) {}
                }

                // Auto remove after configured time
                setTimeout(() => {
                    notifEl.style.opacity = '0';
                    notifEl.style.transform = 'translateX(-20px)';
                    setTimeout(() => {
                        notifEl.remove();
                        // Keep in shown list to prevent re-showing
                    }, 300);
                }, autoDismissTime);
            }

            window.markNotificationRead = function(id) {
                fetch(`{{ url('admin/notifications') }}/${id}/read`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                }).catch(() => {});
            };

            window.removeFromShown = function(id) {
                shownNotifications = shownNotifications.filter(item => item.id !== id);
                localStorage.setItem('admin_shown_notifications', JSON.stringify(shownNotifications));
            };

            function checkNotifications() {
                if (document.visibilityState !== 'visible') return;

                let url = '{{ route("admin.notifications.recent") }}';
                if (lastCheck) {
                    url += '?last_check=' + encodeURIComponent(lastCheck);
                }

                fetch(url, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    // Update lastCheck time and save to localStorage
                    lastCheck = data.server_time;
                    localStorage.setItem('admin_notifications_last_check', lastCheck);
                    
                    if (data.notifications && data.notifications.length > 0) {
                        data.notifications.forEach(notification => {
                            showNotification(notification);
                        });
                        
                        // Update badge count if exists
                        updateNotificationBadge();
                    }
                })
                .catch(() => {});
            }

            function updateNotificationBadge() {
                fetch('{{ route("admin.notifications.count") }}', {
                    headers: { 'Accept': 'application/json' }
                })
                .then(response => response.json())
                .then(data => {
                    const badge = document.getElementById('notification-badge');
                    if (badge) {
                        badge.textContent = data.count;
                        badge.style.display = data.count > 0 ? 'inline-block' : 'none';
                    }
                })
                .catch(() => {});
            }

            // Check immediately on page load
            setTimeout(checkNotifications, 1000);

            // Then check at configured interval
            setInterval(checkNotifications, refreshInterval);
        })();
        @endif
    </script>

    @stack('scripts')
    @yield('scripts')
</body>
</html>

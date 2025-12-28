<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'لوحة تحكم المورد') - Your Events</title>
    
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
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
    
    <!-- Bootstrap RTL -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    
    <style>
        :root {
            --primary-color: #1f144a;
            --secondary-color: #2dbcae;
            --accent-color: #ef4870;
            --gold-color: #f0c71d;
            --sidebar-width: 280px;
            --header-height: 70px;
        }
        
        * {
            font-family: 'Tajawal', sans-serif;
        }
        
        body {
            background: #f4f6f9;
            min-height: 100vh;
        }
        
        /* Sidebar */
        .supplier-sidebar {
            position: fixed;
            right: 0;
            top: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: linear-gradient(180deg, var(--primary-color) 0%, #2a1d5c 100%);
            z-index: 1000;
            overflow-y: auto;
            transition: all 0.3s ease;
        }
        
        .sidebar-header {
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        .sidebar-logo {
            max-width: 150px;
            margin-bottom: 10px;
        }
        
        .supplier-info {
            padding: 15px 20px;
            background: rgba(255,255,255,0.05);
            border-radius: 10px;
            margin: 15px;
        }
        
        .supplier-info .name {
            color: #fff;
            font-weight: 700;
            font-size: 1rem;
            margin-bottom: 5px;
        }
        
        .supplier-info .type {
            color: var(--gold-color);
            font-size: 0.85rem;
        }
        
        .sidebar-nav {
            padding: 15px 0;
        }
        
        .nav-section {
            padding: 10px 20px;
            color: rgba(255,255,255,0.5);
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .nav-link {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            transition: all 0.3s ease;
            border-right: 3px solid transparent;
        }
        
        .nav-link:hover {
            background: rgba(255,255,255,0.1);
            color: #fff;
        }
        
        .nav-link.active {
            background: rgba(255,255,255,0.1);
            color: var(--gold-color);
            border-right-color: var(--gold-color);
        }
        
        .nav-link i {
            width: 24px;
            margin-left: 12px;
            font-size: 1.1rem;
        }
        
        .nav-link .badge {
            margin-right: auto;
            margin-left: 0;
        }
        
        /* Main Content */
        .main-content {
            margin-right: var(--sidebar-width);
            min-height: 100vh;
            transition: all 0.3s ease;
        }
        
        /* Header */
        .supplier-header {
            background: #fff;
            height: var(--header-height);
            padding: 0 30px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            position: sticky;
            top: 0;
            z-index: 100;
        }
        
        .header-title {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--primary-color);
            margin: 0;
        }
        
        .header-actions {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .header-btn {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: #f4f6f9;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary-color);
            transition: all 0.3s ease;
            position: relative;
        }
        
        .header-btn:hover {
            background: var(--primary-color);
            color: #fff;
        }
        
        .header-btn .badge {
            position: absolute;
            top: -5px;
            left: -5px;
            font-size: 0.65rem;
        }
        
        .user-dropdown .dropdown-toggle {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 5px 15px;
            background: #f4f6f9;
            border-radius: 25px;
            border: none;
            color: var(--primary-color);
        }
        
        .user-dropdown .dropdown-toggle::after {
            display: none;
        }
        
        .user-avatar {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            background: var(--secondary-color);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-weight: 700;
        }
        
        /* Page Content */
        .page-content {
            padding: 30px;
        }
        
        /* Stats Cards */
        .stat-card {
            background: #fff;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        
        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: #fff;
        }
        
        .stat-icon.primary { background: linear-gradient(135deg, var(--primary-color), #3d2a7a); }
        .stat-icon.secondary { background: linear-gradient(135deg, var(--secondary-color), #1a8f84); }
        .stat-icon.accent { background: linear-gradient(135deg, var(--accent-color), #c93d5c); }
        .stat-icon.gold { background: linear-gradient(135deg, var(--gold-color), #d4a917); }
        
        .stat-value {
            font-size: 2rem;
            font-weight: 800;
            color: var(--primary-color);
            margin: 10px 0 5px;
        }
        
        .stat-label {
            color: #6c757d;
            font-size: 0.9rem;
        }
        
        /* Cards */
        .content-card {
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            overflow: hidden;
        }
        
        .card-header-custom {
            padding: 20px 25px;
            border-bottom: 1px solid #eee;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .card-header-custom h5 {
            margin: 0;
            font-weight: 700;
            color: var(--primary-color);
        }
        
        /* Tables */
        .table-custom {
            margin: 0;
        }
        
        .table-custom th {
            background: #f8f9fa;
            color: var(--primary-color);
            font-weight: 600;
            border: none;
            padding: 15px;
        }
        
        .table-custom td {
            padding: 15px;
            vertical-align: middle;
            border-color: #eee;
        }
        
        /* Status Badges */
        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        
        .status-pending { background: #fff3cd; color: #856404; }
        .status-confirmed { background: #d4edda; color: #155724; }
        .status-completed { background: #cce5ff; color: #004085; }
        .status-cancelled { background: #f8d7da; color: #721c24; }
        .status-active { background: #d4edda; color: #155724; }
        .status-inactive { background: #f8d7da; color: #721c24; }
        
        /* Buttons */
        .btn-supplier-primary {
            background: linear-gradient(135deg, var(--primary-color), #3d2a7a);
            border: none;
            color: #fff;
            padding: 10px 25px;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-supplier-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(31, 20, 74, 0.3);
            color: #fff;
        }
        
        .btn-supplier-gold {
            background: linear-gradient(135deg, var(--gold-color), #d4a917);
            border: none;
            color: var(--primary-color);
            padding: 10px 25px;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-supplier-gold:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(240, 199, 29, 0.3);
        }
        
        /* Responsive */
        @media (max-width: 991px) {
            .supplier-sidebar {
                transform: translateX(100%);
            }
            
            .supplier-sidebar.show {
                transform: translateX(0);
            }
            
            .main-content {
                margin-right: 0;
            }
            
            .sidebar-toggle {
                display: block !important;
            }
        }
        
        .sidebar-toggle {
            display: none;
        }
        
        /* Overlay */
        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.5);
            z-index: 999;
        }
        
        .sidebar-overlay.show {
            display: block;
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <!-- Sidebar Overlay (Mobile) -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>
    
    <!-- Sidebar -->
    <aside class="supplier-sidebar" id="supplierSidebar">
        <div class="sidebar-header">
            <a href="{{ route('supplier.dashboard') }}">
                <img 
                    src="{{ asset('images/logo/logo-white.png') }}" 
                    alt="Your Events" 
                    class="sidebar-logo" 
                    data-fallback="{{ asset('images/logo/logo.png') }}"
                    onerror="this.src=this.dataset.fallback">
            </a>
        </div>
        
        <div class="supplier-info">
            <div class="name">{{ Auth::guard('supplier')->user()->name ?? 'المورد' }}</div>
            <div class="type">
                @if(Auth::guard('supplier')->user()->supplier_type === 'company')
                    <i class="fas fa-building me-1"></i> منشأة
                @else
                    <i class="fas fa-user me-1"></i> فرد
                @endif
            </div>
        </div>
        
        <nav class="sidebar-nav">
            <div class="nav-section">القائمة الرئيسية</div>
            
            <a href="{{ route('supplier.dashboard') }}" class="nav-link {{ request()->routeIs('supplier.dashboard') ? 'active' : '' }}">
                <i class="fas fa-home"></i>
                <span>لوحة التحكم</span>
            </a>
            
            <a href="{{ route('supplier.services.index') }}" class="nav-link {{ request()->routeIs('supplier.services*') ? 'active' : '' }}">
                <i class="fas fa-concierge-bell"></i>
                <span>خدماتي</span>
            </a>
            
            <a href="{{ route('supplier.bookings.index') }}" class="nav-link {{ request()->routeIs('supplier.bookings*') ? 'active' : '' }}">
                <i class="fas fa-calendar-check"></i>
                <span>الحجوزات</span>
                @php
                    $pendingCount = Auth::guard('supplier')->user()->services()
                        ->join('bookings', 'services.id', '=', 'bookings.service_id')
                        ->where('bookings.status', 'pending')
                        ->count();
                @endphp
                @if($pendingCount > 0)
                    <span class="badge bg-danger">{{ $pendingCount }}</span>
                @endif
            </a>
            
            

            <a href="{{ route('supplier.quotes.index') }}" class="nav-link {{ request()->routeIs('supplier.quotes*') ? 'active' : '' }}">
                <i class="fas fa-file-invoice-dollar"></i>
                <span>عروض الأسعار</span>
                @php
                    try {
                        $serviceIds = Auth::guard('supplier')->user()->services()->pluck('services.id');
                        $pendingQuotesCount = \App\Models\Quote::where('status', 'pending')
                            ->whereHas('items', function($q) use ($serviceIds) { $q->whereIn('service_id', $serviceIds); })
                            ->count();
                    } catch (\Throwable $e) {
                        $pendingQuotesCount = 0;
                    }
                @endphp
                @if(($pendingQuotesCount ?? 0) > 0)
                    <span class="badge bg-warning text-dark">{{ $pendingQuotesCount }}</span>
                @endif
            </a>
            
            <div class="nav-section">الإحصائيات</div>
            
            <a href="{{ route('supplier.reports.index') }}" class="nav-link {{ request()->routeIs('supplier.reports*') ? 'active' : '' }}">
                <i class="fas fa-chart-bar"></i>
                <span>التقارير</span>
            </a>
            
            <div class="nav-section">الإعدادات</div>
            
            <a href="{{ route('supplier.profile.index') }}" class="nav-link {{ request()->routeIs('supplier.profile*') ? 'active' : '' }}">
                <i class="fas fa-user-cog"></i>
                <span>الملف الشخصي</span>
            </a>
            
            <a href="{{ route('home') }}" class="nav-link" target="_blank">
                <i class="fas fa-globe"></i>
                <span>زيارة الموقع</span>
            </a>
            
            <form action="{{ route('supplier.logout') }}" method="POST" class="mt-3">
                @csrf
                <button type="submit" class="nav-link w-100 text-start border-0 bg-transparent" style="color: #ff6b6b;">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>تسجيل الخروج</span>
                </button>
            </form>
        </nav>
    </aside>
    
    <!-- Main Content -->
    <main class="main-content">
        <!-- Header -->
        <header class="supplier-header">
            <div class="d-flex align-items-center gap-3">
                <button class="sidebar-toggle header-btn d-lg-none" id="sidebarToggle">
                    <i class="fas fa-bars"></i>
                </button>
                <h1 class="header-title">@yield('page-title', 'لوحة التحكم')</h1>
            </div>
            
            <div class="header-actions">
                @php
                    try {
                        $supplierHeader = Auth::guard('supplier')->user();
                        $serviceIdsHeader = $supplierHeader ? $supplierHeader->services()->pluck('services.id') : collect([]);
                        $pendingBookingsCountHeader = \App\Models\Booking::whereIn('service_id', $serviceIdsHeader)
                            ->where('status', 'pending')
                            ->count();
                        $pendingQuotesCountHeader = \App\Models\Quote::where('status', 'pending')
                            ->whereHas('items', function($q) use ($serviceIdsHeader) { $q->whereIn('service_id', $serviceIdsHeader); })
                            ->count();
                        $notificationCountHeader = $pendingBookingsCountHeader + $pendingQuotesCountHeader;
                    } catch (\Throwable $e) {
                        $pendingBookingsCountHeader = 0;
                        $pendingQuotesCountHeader = 0;
                        $notificationCountHeader = 0;
                    }
                @endphp

                <div class="dropdown notifications-dropdown">
                    <button class="header-btn" title="الإشعارات" data-bs-toggle="dropdown">
                        <i class="fas fa-bell"></i>
                        @if(($notificationCountHeader ?? 0) > 0)
                            <span class="badge bg-danger">{{ $notificationCountHeader }}</span>
                        @endif
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li class="dropdown-header">إشعاراتك</li>
                        <li>
                            <a class="dropdown-item" href="{{ route('supplier.bookings.index', ['status' => 'pending']) }}">
                                <i class="fas fa-calendar-check me-2"></i>
                                حجوزات بانتظارك
                                <span class="badge bg-danger ms-2">{{ $pendingBookingsCountHeader }}</span>
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('supplier.quotes.index', ['status' => 'pending']) }}">
                                <i class="fas fa-file-invoice-dollar me-2"></i>
                                عروض أسعار قيد الانتظار
                                <span class="badge bg-warning text-dark ms-2">{{ $pendingQuotesCountHeader }}</span>
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item" href="{{ route('supplier.quotes.index') }}">
                                <i class="fas fa-list me-2"></i>
                                عرض كل العروض
                            </a>
                        </li>
                    </ul>
                </div>
                
                <div class="dropdown user-dropdown">
                    <button class="dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <div class="user-avatar">
                            {{ mb_substr(Auth::guard('supplier')->user()->name ?? 'م', 0, 1) }}
                        </div>
                        <span class="d-none d-md-inline">{{ Auth::guard('supplier')->user()->name ?? 'المورد' }}</span>
                        <i class="fas fa-chevron-down ms-1" style="font-size: 0.7rem;"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="{{ route('supplier.profile.index') }}"><i class="fas fa-user me-2"></i>الملف الشخصي</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form action="{{ route('supplier.logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="fas fa-sign-out-alt me-2"></i>تسجيل الخروج
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </header>
        
        <!-- Page Content -->
        <div class="page-content">
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
            
            @yield('content')
        </div>
    </main>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Sidebar Toggle
        const sidebar = document.getElementById('supplierSidebar');
        const overlay = document.getElementById('sidebarOverlay');
        const toggle = document.getElementById('sidebarToggle');
        
        if (toggle) {
            toggle.addEventListener('click', () => {
                sidebar.classList.toggle('show');
                overlay.classList.toggle('show');
            });
        }
        
        if (overlay) {
            overlay.addEventListener('click', () => {
                sidebar.classList.remove('show');
                overlay.classList.remove('show');
            });
        }
    </script>
    
    @stack('scripts')
</body>
</html>

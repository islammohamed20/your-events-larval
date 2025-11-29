<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'لوحة التحكم - Your Events')</title>
    
    <!-- Favicon -->
    @php
        $faviconSetting = \App\Models\Setting::get('site_favicon');
        $faviconUrlSetting = \App\Models\Setting::get('favicon_url');
        $faviconUrl = $faviconSetting
            ? Storage::url($faviconSetting)
            : ($faviconUrlSetting ?: asset('images/logo/logo.png'));
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
<body>
    <!-- Sidebar -->
    <nav class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <img src="{{ asset('images/logo/logo.png') }}" alt="Your Events">
            <small>لوحة التحكم</small>
        </div>
        
        <div class="sidebar-menu">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" 
                       href="{{ route('admin.dashboard') }}">
                        <i class="fas fa-tachometer-alt me-2"></i>لوحة التحكم الرئيسية
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}" 
                       href="{{ route('admin.categories.index') }}">
                        <i class="fas fa-folder me-2"></i>الفئات
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.packages.*') ? 'active' : '' }}" 
                       href="{{ route('admin.packages.index') }}">
                        <i class="fas fa-box me-2"></i>الباقات
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.services.*') ? 'active' : '' }}" 
                       href="{{ route('admin.services.index') }}">
                        <i class="fas fa-cogs me-2"></i>الخدمات
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.attributes.*') ? 'active' : '' }}" 
                       href="{{ route('admin.attributes.index') }}">
                        <i class="fas fa-tags me-2"></i>خصائص الخدمات
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.bookings.*') ? 'active' : '' }}" 
                       href="{{ route('admin.bookings.index') }}">
                        <i class="fas fa-calendar-check me-2"></i>الحجوزات
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.quotes.*') ? 'active' : '' }}" 
                       href="{{ route('admin.quotes.index') }}">
                        <i class="fas fa-file-invoice-dollar me-2"></i>عروض الأسعار
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.gallery.*') ? 'active' : '' }}" 
                       href="{{ route('admin.gallery.index') }}">
                        <i class="fas fa-images me-2"></i>المعرض
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.user-management.*') ? 'active' : '' }}" 
                       href="{{ route('admin.user-management.index') }}">
                        <i class="fas fa-users-cog me-2"></i>إدارة المستخدمين
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.customers.*') ? 'active' : '' }}" 
                       href="{{ route('admin.customers.index') }}">
                        <i class="fas fa-user-tie me-2"></i>إدارة العملاء
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.suppliers.*') ? 'active' : '' }}" 
                       href="{{ route('admin.suppliers.index') }}">
                        <i class="fas fa-handshake me-2"></i>إدارة الموردين
                        @if(\App\Models\Supplier::where('status', 'pending')->count() > 0)
                            <span class="badge bg-warning text-dark ms-1">{{ \App\Models\Supplier::where('status', 'pending')->count() }}</span>
                        @endif
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.hero-slides.*') ? 'active' : '' }}" 
                       href="{{ route('admin.hero-slides.index') }}">
                        <i class="fas fa-images me-2"></i>سلايدات البانر الرئيسي
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.homepage.*') ? 'active' : '' }}" 
                       href="{{ route('admin.homepage.index') }}">
                        <i class="fas fa-home me-2"></i>إدارة الصفحة الرئيسية
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}" 
                       href="{{ route('admin.reports.index') }}">
                        <i class="fas fa-chart-line me-2"></i>التقارير
                    </a>
                </li>
                <li class="nav-item ms-3">
                    <a class="nav-link {{ request()->routeIs('admin.reports.security') ? 'active' : '' }}" 
                       href="{{ route('admin.reports.security') }}">
                        <i class="fas fa-shield-alt me-2 text-muted"></i>
                        <small>تقرير الأمان</small>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.login-activities.*') ? 'active' : '' }}" 
                       href="{{ route('admin.login-activities.index') }}">
                        <i class="fas fa-user-shield me-2"></i>سجلات الدخول
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}" 
                       href="{{ route('admin.settings.index') }}">
                        <i class="fas fa-cog me-2"></i>الإعدادات
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.email-management.*') ? 'active' : '' }}" 
                       href="{{ route('admin.email-management.index') }}">
                        <i class="fas fa-envelope-open-text me-2"></i>إدارة البريد الإلكتروني
                    </a>
                </li>
                <li class="nav-item ms-3">
                    <a class="nav-link {{ request()->routeIs('admin.email-templates.*') ? 'active' : '' }}" 
                       href="{{ route('admin.email-templates.index') }}">
                        <i class="fas fa-file-alt me-2 text-muted"></i>
                        <small>القوالب</small>
                    </a>
                </li>
                <li class="nav-item ms-3">
                    <a class="nav-link {{ request()->routeIs('admin.otp.*') ? 'active' : '' }}" 
                       href="{{ route('admin.otp.index') }}">
                        <i class="fas fa-shield-alt me-2 text-muted"></i>
                        <small>أكواد OTP</small>
                    </a>
                </li>
                <li class="nav-item">
                    <hr style="border-color: #34495e; margin: 15px 20px;">
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('home') }}" target="_blank">
                        <i class="fas fa-external-link-alt me-2"></i>عرض الموقع
                    </a>
                </li>
            </ul>
        </div>
        
        <div class="sidebar-footer">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="logout-button nav-link btn border-0 w-auto">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>تسجيل الخروج</span>
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
                    <h3 class="mb-0">@yield('page-title', 'لوحة التحكم')</h3>
                    <small class="text-muted">@yield('page-description', 'إدارة موقع Your Events')</small>
                </div>
                <div class="d-flex align-items-center">
                    <button class="btn btn-outline-primary d-md-none me-2" onclick="toggleSidebar()">
                        <i class="fas fa-bars"></i>
                    </button>
                    <span class="text-muted">مرحباً، {{ auth()->user()->name }}</span>
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
                if (!confirm('هل أنت متأكد من الحذف؟ لا يمكن التراجع عن هذا الإجراء.')) {
                    e.preventDefault();
                    return false;
                }
            }
        });
    </script>

    @yield('scripts')
</body>
</html>

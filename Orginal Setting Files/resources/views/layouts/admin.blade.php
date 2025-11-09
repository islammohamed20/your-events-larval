<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'لوحة التحكم - Your Events')</title>
    
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
            min-height: 100vh;
            position: fixed;
            top: 0;
            right: 0;
            width: 250px;
            transition: all 0.3s;
            z-index: 1040; /* أعلى من أي محتوى أساسي */
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
            position: relative; /* إنشاء سياق مكدس منفصل بدون التأثير على الـ sidebar */
            z-index: 1;
        }

        .admin-header {
            background: white;
            padding: 15px 30px;
            margin: -20px -20px 20px -20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            border-bottom: 3px solid var(--primary-color);
            position: relative; /* لضمان ظهور القوائم المنسدلة فوق المحتوى */
            z-index: 1060;
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
        }

        .sidebar-brand h4 {
            color: var(--primary-color);
            margin: 0;
            font-weight: 700;
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
        }

        /* معالجة مشكلة اختفاء القوائم المنسدلة (Dropdown) خلف المحتوى */
        .dropdown-menu,
        .modal,
        .popover,
        .tooltip {
            z-index: 2000; /* أعلى من الـ sidebar و الترويسة */
        }

        /* في حال وجود عناصر overlay أخرى لاحقاً */
        .overlay-active .sidebar {
            z-index: 1500;
        }
    </style>
    
    @yield('styles')
</head>
<body>
    <!-- Sidebar -->
    <nav class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <img src="{{ asset('images/logo/logo.png') }}" alt="Your Events" height="35" class="mb-2">
            <small class="text-muted d-block">لوحة التحكم</small>
        </div>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" 
                   href="{{ route('admin.dashboard') }}">
                    <i class="fas fa-tachometer-alt me-2"></i>الرئيسية
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
                <a class="nav-link {{ request()->routeIs('admin.bookings.*') ? 'active' : '' }}" 
                   href="{{ route('admin.bookings.index') }}">
                    <i class="fas fa-calendar-check me-2"></i>الحجوزات
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.gallery.*') ? 'active' : '' }}" 
                   href="{{ route('admin.gallery.index') }}">
                    <i class="fas fa-images me-2"></i>المعرض
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" 
                   href="{{ route('admin.users.index') }}">
                    <i class="fas fa-users me-2"></i>إدارة المستخدمين
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}" 
                   href="{{ route('admin.settings.index') }}">
                    <i class="fas fa-cog me-2"></i>الإعدادات
                </a>
            </li>
            <li class="nav-item">
                <hr style="border-color: #34495e;">
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('home') }}" target="_blank">
                    <i class="fas fa-external-link-alt me-2"></i>عرض الموقع
                </a>
            </li>
            <li class="nav-item">
                <form method="POST" action="{{ route('logout') }}" class="d-inline">
                    @csrf
                    <button type="submit" class="nav-link btn text-start w-100 border-0">
                        <i class="fas fa-sign-out-alt me-2"></i>تسجيل الخروج
                    </button>
                </form>
            </li>
        </ul>
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

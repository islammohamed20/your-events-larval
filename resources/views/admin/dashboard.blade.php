@extends('layouts.admin')

@section('title', 'لوحة التحكم - Your Events')
@section('page-title', 'لوحة التحكم الرئيسية')
@section('page-description', 'نظرة عامة على إحصائيات الموقع والأنشطة الحديثة')

@section('content')
<div class="container-fluid">
    <!-- Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <h3 class="mb-0">{{ $stats['customers'] ?? 0 }}</h3>
                        <p class="mb-0 text-muted">العملاء</p>
                    </div>
                    <i class="fas fa-user-tie fa-2x text-primary opacity-75"></i>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <h3 class="mb-0">{{ $stats['admin_users'] ?? 0 }}</h3>
                        <p class="mb-0 text-muted">المديرين</p>
                    </div>
                    <i class="fas fa-user-shield fa-2x text-secondary opacity-75"></i>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <h3 class="mb-0">{{ $stats['services'] ?? 0 }}</h3>
                        <p class="mb-0 text-muted">الخدمات</p>
                    </div>
                    <i class="fas fa-cogs fa-2x text-info opacity-75"></i>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <h3 class="mb-0">{{ $stats['packages'] ?? 0 }}</h3>
                        <p class="mb-0 text-muted">الباقات</p>
                    </div>
                    <i class="fas fa-box fa-2x text-warning opacity-75"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Second Row: Bookings -->
    <div class="row g-3 mb-4">
        <div class="col-xl-6 col-md-12">
            <div class="card shadow-sm h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <h3 class="mb-0">{{ $stats['bookings'] ?? 0 }}</h3>
                        <p class="mb-0 text-muted">إجمالي الحجوزات</p>
                        <small class="text-muted">معلّق: {{ $stats['pending_bookings'] ?? 0 }}</small>
                    </div>
                    <i class="fas fa-calendar-check fa-2x text-success opacity-75"></i>
                </div>
            </div>
        </div>
        <div class="col-xl-6 col-md-12">
            <div class="card shadow-sm h-100">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i>ملخص سريع</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-4">
                            <h4 class="text-primary">{{ $stats['total_users'] ?? 0 }}</h4>
                            <small class="text-muted">إجمالي المستخدمين</small>
                        </div>
                        <div class="col-4">
                            <h4 class="text-info">{{ $stats['services'] + $stats['packages'] ?? 0 }}</h4>
                            <small class="text-muted">إجمالي المنتجات</small>
                        </div>
                        <div class="col-4">
                            <h4 class="text-success">{{ $stats['bookings'] ?? 0 }}</h4>
                            <small class="text-muted">الحجوزات</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quotes Statistics -->
    <div class="row g-3 mb-4">
        <div class="col-xl-4 col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="card-title mb-1">زيارات اليوم</h6>
                        <h3 class="mb-0">{{ $stats['visits_today'] ?? 0 }}</h3>
                        <small class="text-muted">آخر 7 أيام: {{ $stats['visits_7d'] ?? 0 }} | زوار فريدون: {{ $stats['unique_visitors_7d'] ?? 0 }}</small>
                    </div>
                    <i class="fas fa-chart-line fa-2x text-primary opacity-75"></i>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="card-title mb-1">تسجيلات الدخول اليوم</h6>
                        <h3 class="mb-0">{{ $stats['logins_today'] ?? 0 }}</h3>
                        <small class="text-muted">آخر 7 أيام: {{ $stats['logins_7d'] ?? 0 }}</small>
                    </div>
                    <i class="fas fa-sign-in-alt fa-2x text-success opacity-75"></i>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-12">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h6 class="card-title">البلدان الأكثر زيارة (7 أيام)</h6>
                    <ul class="list-unstyled mb-0">
                        @forelse (($stats['top_countries_7d'] ?? []) as $row)
                            <li class="d-flex justify-content-between">
                                <span>{{ $row->country }}</span>
                                <span class="text-muted">{{ $row->count }}</span>
                            </li>
                        @empty
                            <li class="text-muted">لا توجد بيانات كافية</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <h3 class="mb-0">{{ $stats['quotes'] ?? 0 }}</h3>
                        <p class="mb-0 text-muted">عروض الأسعار</p>
                    </div>
                    <i class="fas fa-file-invoice-dollar fa-2x text-primary opacity-75"></i>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <h3 class="mb-0">{{ $stats['pending_quotes'] ?? 0 }}</h3>
                        <p class="mb-0 text-muted">قيد الانتظار</p>
                    </div>
                    <i class="fas fa-clock fa-2x text-warning opacity-75"></i>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <h3 class="mb-0">{{ $stats['approved_quotes'] ?? 0 }}</h3>
                        <p class="mb-0 text-muted">موافق عليها</p>
                    </div>
                    <i class="fas fa-check-circle fa-2x text-success opacity-75"></i>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <h3 class="mb-0">{{ $stats['rejected_quotes'] ?? 0 }}</h3>
                        <p class="mb-0 text-muted">مرفوضة</p>
                    </div>
                    <i class="fas fa-times-circle fa-2x text-danger opacity-75"></i>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <h3 class="mb-0">{{ $stats['completed_quotes'] ?? 0 }}</h3>
                        <p class="mb-0 text-muted">مكتملة</p>
                    </div>
                    <i class="fas fa-check-double fa-2x text-info opacity-75"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-xl-6 col-md-12">
            <div class="card shadow-sm h-100">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="mb-0"><i class="fas fa-images me-2"></i>المعرض</h5>
                    <a href="{{ route('admin.gallery.index') }}" class="btn btn-outline-info btn-sm">إدارة المعرض</a>
                </div>
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <h2 class="text-info mb-0">{{ $stats['gallery_items'] ?? 0 }}</h2>
                        <p class="text-muted mb-0">صور وفيديوهات</p>
                    </div>
                    <i class="fas fa-photo-video fa-2x text-info opacity-75"></i>
                </div>
            </div>
        </div>
        <div class="col-xl-6 col-md-12">
            <div class="card shadow-sm h-100">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i>إحصائيات إضافية</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <h4 class="text-primary">{{ $stats['reviews'] ?? 0 }}</h4>
                            <p class="text-muted mb-2">التقييمات</p>
                        </div>
                        <div class="col-6">
                            <h4 class="text-warning">{{ $stats['pending_reviews'] ?? 0 }}</h4>
                            <p class="text-muted mb-2">تقييمات معلّقة</p>
                        </div>
                    </div>
                    <hr>
                    <div class="text-center">
                        <small class="text-muted">آخر تحديث: {{ now()->format('d/m/Y H:i') }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Email & OTP Statistics -->
    <div class="row g-3 mb-4">
        <div class="col-xl-4 col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="mb-0"><i class="fas fa-envelope-open-text me-2"></i>البريد الإلكتروني</h5>
                    <a href="{{ route('admin.email-management.index') }}" class="btn btn-outline-primary btn-sm">إدارة البريد</a>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <h4 class="mb-0">{{ $stats['email_templates_total'] ?? 0 }}</h4>
                            <small class="text-muted">قوالب البريد</small>
                        </div>
                        <div>
                            <span class="badge bg-success">نشط: {{ $stats['email_templates_active'] ?? 0 }}</span>
                        </div>
                    </div>
                    <div class="text-muted small">قم بإدارة القوالب وأنواع الرسائل المرسلة من النظام.</div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-shield-alt me-2"></i>OTP</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-4">
                            <h4 class="text-primary mb-0">{{ $stats['otp_total'] ?? 0 }}</h4>
                            <small class="text-muted">الإجمالي</small>
                        </div>
                        <div class="col-4">
                            <h4 class="text-success mb-0">{{ $stats['otp_verified'] ?? 0 }}</h4>
                            <small class="text-muted">متحقق</small>
                        </div>
                        <div class="col-4">
                            <h4 class="text-warning mb-0">{{ $stats['otp_pending'] ?? 0 }}</h4>
                            <small class="text-muted">معلّق</small>
                        </div>
                    </div>
                    <hr>
                    <div class="text-center">
                        <small class="text-muted">نسبة النجاح: {{ $stats['otp_success_rate'] ?? 0 }}%</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Quotes -->
    <div class="row g-3 mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="mb-0"><i class="fas fa-file-invoice-dollar me-2"></i>أحدث عروض الأسعار</h5>
                    <a href="{{ route('admin.quotes.index') }}" class="btn btn-outline-secondary btn-sm">عرض الكل</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>رقم العرض</th>
                                    <th>العميل</th>
                                    <th>الحالة</th>
                                    <th>الإجمالي</th>
                                    <th>تاريخ الإنشاء</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recent_quotes as $quote)
                                    <tr>
                                        <td>{{ $quote->id }}</td>
                                        <td>{{ $quote->quote_number }}</td>
                                        <td>{{ optional($quote->user)->name ?? '-' }}</td>
                                        <td>
                                            <span class="badge bg-{{ $quote->status === 'pending' ? 'warning' : ($quote->status === 'approved' ? 'success' : ($quote->status === 'rejected' ? 'danger' : 'info')) }}">
                                                {{ $quote->status_text }}
                                            </span>
                                        </td>
                                        <td>{{ number_format($quote->total, 2) }}</td>
                                        <td>{{ $quote->created_at?->format('d/m/Y H:i') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">لا توجد عروض أسعار حديثة حالياً</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Employee Activity Monitor -->
    <div class="row g-3 mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="mb-0"><i class="fas fa-user-clock me-2"></i>مراقبة حركات الموظفين</h5>
                    <div class="d-flex gap-2">
                        <select class="form-select form-select-sm" id="employeeFilter" style="width: auto;">
                            <option value="">جميع الموظفين</option>
                            @foreach($employees ?? [] as $employee)
                                <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                            @endforeach
                        </select>
                        <button class="btn btn-outline-primary btn-sm" onclick="refreshEmployeeActivity()">
                            <i class="fas fa-sync-alt"></i> تحديث
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>الموظف</th>
                                    <th>النشاط</th>
                                    <th>التفاصيل</th>
                                    <th>الوقت</th>
                                    <th>الحالة</th>
                                </tr>
                            </thead>
                            <tbody id="employeeActivityTable">
                                @forelse($recent_employee_activities ?? [] as $activity)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="{{ $activity->employee->avatar_url ?? asset('images/default-avatar.png') }}" 
                                                     alt="{{ $activity->employee->name }}" 
                                                     class="rounded-circle me-2" 
                                                     style="width: 35px; height: 35px; object-fit: cover;">
                                                <div>
                                                    <div class="fw-semibold">{{ $activity->employee->name }}</div>
                                                    <small class="text-muted">{{ $activity->employee->role ?? 'موظف' }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $activity->type_color }}">
                                                <i class="{{ $activity->type_icon }}"></i>
                                                {{ $activity->type_text }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="activity-details">
                                                <div class="fw-medium">{{ $activity->description }}</div>
                                                @if($activity->related_model)
                                                <small class="text-muted">
                                                    <i class="fas fa-link"></i> {{ $activity->related_model }}
                                                </small>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <div class="text-muted">
                                                <div>{{ $activity->created_at->format('d/m/Y') }}</div>
                                                <small>{{ $activity->created_at->format('H:i') }}</small>
                                            </div>
                                        </td>
                                        <td>
                                            @if($activity->status === 'success')
                                                <span class="badge bg-success">
                                                    <i class="fas fa-check"></i> ناجح
                                                </span>
                                            @elseif($activity->status === 'failed')
                                                <span class="badge bg-danger">
                                                    <i class="fas fa-times"></i> فشل
                                                </span>
                                            @else
                                                <span class="badge bg-warning">
                                                    <i class="fas fa-clock"></i> قيد التنفيذ
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">
                                            <i class="fas fa-user-clock fa-2x mb-2"></i>
                                            <p class="mb-0">لا توجد أنشطة حديثة للموظفين</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-light">
                    <div class="d-flex justify-content-between align-items-center">
                        <small class="text-muted">
                            <i class="fas fa-info-circle"></i> 
                            يتم عرض آخر 10 أنشطة - 
                            الموظفين النشطين الآن: <span class="badge bg-success" id="activeEmployeesCount">{{ $active_employees_count ?? 0 }}</span>
                        </small>
                        <small class="text-muted">
                            آخر تحديث: <span id="lastUpdateTime">{{ now()->format('H:i:s') }}</span>
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Bookings -->
    <div class="row g-3">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="mb-0"><i class="fas fa-history me-2"></i>آخر الحجوزات</h5>
                    <a href="{{ route('admin.bookings.index') }}" class="btn btn-outline-secondary btn-sm">عرض الكل</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>الباقة</th>
                                    <th>الخدمة</th>
                                    <th>الحالة</th>
                                    <th>تاريخ الإنشاء</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recent_bookings as $booking)
                                    <tr>
                                        <td>{{ $booking->id }}</td>
                                        <td>{{ optional($booking->package)->name ?? '-' }}</td>
                                        <td>{{ optional($booking->service)->name ?? '-' }}</td>
                                        <td>
                                            <span class="badge bg-{{ $booking->status === 'pending' ? 'warning' : ($booking->status === 'approved' ? 'success' : ($booking->status === 'rejected' ? 'danger' : 'info')) }}">
                                                {{ $booking->status }}
                                            </span>
                                        </td>
                                        <td>{{ $booking->created_at?->format('d/m/Y H:i') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">لا توجد حجوزات حديثة حالياً</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    // Employee Activity Monitor Functions
    function refreshEmployeeActivity() {
        const employeeId = document.getElementById('employeeFilter').value;
        const tableBody = document.getElementById('employeeActivityTable');
        
        // Show loading state
        tableBody.innerHTML = `
            <tr>
                <td colspan="5" class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">جاري التحديث...</span>
                    </div>
                    <p class="mt-2 text-muted">جاري تحديث البيانات...</p>
                </td>
            </tr>
        `;
        
        // Simulate AJAX call (replace with actual API call)
        setTimeout(() => {
            updateLastUpdateTime();
            // Here you would make an actual AJAX call to fetch updated data
            // For now, we'll just update the timestamp
            if (employeeId) {
                console.log('Filtering by employee ID:', employeeId);
            }
        }, 1000);
    }
    
    function updateLastUpdateTime() {
        document.getElementById('lastUpdateTime').textContent = new Date().toLocaleTimeString('ar-SA');
    }
    
    // Auto refresh every 30 seconds
    setInterval(() => {
        updateLastUpdateTime();
        // Uncomment the next line to enable auto-refresh
        // refreshEmployeeActivity();
    }, 30000);
    
    // Employee filter change handler
    document.getElementById('employeeFilter')?.addEventListener('change', function() {
        refreshEmployeeActivity();
    });
    
    // Activity type color mapping
    const activityTypeColors = {
        'login': 'primary',
        'logout': 'secondary',
        'create': 'success',
        'update': 'warning',
        'delete': 'danger',
        'view': 'info',
        'export': 'dark'
    };
    
    // Activity type icon mapping
    const activityTypeIcons = {
        'login': 'fas fa-sign-in-alt',
        'logout': 'fas fa-sign-out-alt',
        'create': 'fas fa-plus',
        'update': 'fas fa-edit',
        'delete': 'fas fa-trash',
        'view': 'fas fa-eye',
        'export': 'fas fa-download'
    };
</script>
@endsection

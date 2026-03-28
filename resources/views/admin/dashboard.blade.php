@extends('layouts.admin')

@section('title', __('common.admin_dashboard_title') . ' - Your Events')
@section('page-title', __('common.admin_dashboard_main'))
@section('page-description', __('common.admin_dashboard_overview'))

@section('styles')
<style>
/* ============================================================
   DASHBOARD — MOBILE IMPROVEMENTS
   ============================================================ */

/* ===== بطاقات الإحصائيات الملونة ===== */
.dash-stat-card {
    border: none !important;
    border-radius: 16px !important;
    overflow: hidden;
    position: relative;
    transition: transform 0.2s, box-shadow 0.2s;
}

.dash-stat-card:active {
    transform: scale(0.98);
}

.dash-stat-card .card-body {
    padding: 1.1rem 1.25rem;
    position: relative;
    z-index: 1;
}

/* درج خفي خلف كل بطاقة */
.dash-stat-card::before {
    content: '';
    position: absolute;
    inset: 0;
    opacity: 0.08;
    z-index: 0;
}

/* ألوان البطاقات */
.dash-stat-card.dc-primary   { background: linear-gradient(135deg, #1f144a 0%, #3b2d83 100%); }
.dash-stat-card.dc-secondary  { background: linear-gradient(135deg, #374151 0%, #6b7280 100%); }
.dash-stat-card.dc-info       { background: linear-gradient(135deg, #0369a1 0%, #0ea5e9 100%); }
.dash-stat-card.dc-warning    { background: linear-gradient(135deg, #b45309 0%, #f59e0b 100%); }
.dash-stat-card.dc-success    { background: linear-gradient(135deg, #065f46 0%, #10b981 100%); }
.dash-stat-card.dc-danger     { background: linear-gradient(135deg, #991b1b 0%, #ef4444 100%); }
.dash-stat-card.dc-teal       { background: linear-gradient(135deg, #0d9488 0%, #14b8a6 100%); }
.dash-stat-card.dc-purple     { background: linear-gradient(135deg, #6d28d9 0%, #a78bfa 100%); }

.dash-stat-card h3,
.dash-stat-card h6,
.dash-stat-card p,
.dash-stat-card small,
.dash-stat-card i {
    color: #fff !important;
    opacity: 1;
}

.dash-stat-card h3 { font-size: 2rem; font-weight: 800; }
.dash-stat-card p  { font-size: 0.82rem; opacity: 0.85; margin-bottom: 0; }
.dash-stat-card small { font-size: 0.75rem; opacity: 0.75; }
.dash-stat-card i.fa-2x { opacity: 0.35 !important; font-size: 2.5rem; }

/* ===== شبكة 2×2 على الموبايل ===== */
@media (max-width: 767.98px) {
    /* أول صف (4 بطاقات): عمودان */
    .dash-grid-2col {
        display: grid !important;
        grid-template-columns: 1fr 1fr;
        gap: 0.75rem;
    }

    .dash-grid-2col > [class*="col-"] {
        width: 100% !important;
        padding: 0 !important;
    }

    /* بطاقات stats أصغر على الموبايل */
    .dash-stat-card h3 { font-size: 1.6rem; }
    .dash-stat-card .card-body { padding: 0.9rem 1rem; }
    .dash-stat-card i.fa-2x { font-size: 1.8rem; }

    /* حجم الخط للجداول */
    .table { font-size: 0.8rem; }

    /* card-header على الموبايل */
    .card-header h5 { font-size: 0.9rem; }
    .card-header .btn-sm { font-size: 0.75rem; padding: 0.25rem 0.6rem; }
}

/* ===== بطاقات الإحصائيات المثبتة (Traffic, Email, OTP) ===== */
.dash-info-card {
    border-radius: 14px !important;
    border: none !important;
    box-shadow: 0 2px 12px rgba(0,0,0,0.08) !important;
}

.dash-info-card .card-header {
    border-radius: 14px 14px 0 0 !important;
    background: #f8f7ff !important;
    border-bottom: 1px solid #ece9fd !important;
}

/* ===== تحسين الجداول على الموبايل ===== */
@media (max-width: 767.98px) {
    /* إخفاء بعض الأعمدة الثانوية */
    .dash-table-hide-mobile { display: none !important; }

    /* جدول الـ quotes: إخفاء رقم الـ quote (طويل) والتاريخ */
    .recent-quotes-table th:nth-child(2),
    .recent-quotes-table td:nth-child(2) { display: none; }

    /* جدول الـ bookings: إخفاء عمود الخدمة */
    .recent-bookings-table th:nth-child(3),
    .recent-bookings-table td:nth-child(3) { display: none; }
}

/* ===== Quick Action Buttons على الموبايل ===== */
@media (max-width: 767.98px) {
    .dash-quick-actions {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0.5rem;
        margin-bottom: 1rem;
    }

    .dash-quick-actions .btn {
        border-radius: 12px;
        padding: 0.6rem 0.5rem;
        font-size: 0.78rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
    }
}

@media (min-width: 768px) {
    .dash-quick-actions { display: none; }
}

/* ===== Badge الحالة داخل الجدول ===== */
.badge { font-size: 0.72rem; }

/* ===== شريط فاصل عصري ===== */
.dash-section-title {
    font-size: 0.8rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 1px;
    color: #1f144a; /* اللون المطلوب */
    margin-bottom: 0.6rem;
    margin-top: 1rem;
    display: flex;
    align-items: center;
    gap: 8px;
}
.dash-section-title i { opacity: 0.8; }

</style>
@endsection

@section('content')
<div class="container-fluid" id="adminDashboardAutoRefresh">
    @php
        $canCustomers = $dashboardPermissions['customers'] ?? true;
        $canAdmins = $dashboardPermissions['admins'] ?? true;
        $canServices = $dashboardPermissions['services'] ?? true;
        $canPackages = $dashboardPermissions['packages'] ?? true;
        $canBookings = $dashboardPermissions['bookings'] ?? true;
        $canQuotes = $dashboardPermissions['quotes'] ?? true;
        $canEmails = $dashboardPermissions['emails'] ?? true;
        $canTraffic = $dashboardPermissions['traffic'] ?? true;
        $canOtp = $dashboardPermissions['otp'] ?? true;
        $canGallery = $dashboardPermissions['gallery'] ?? true;
        $canReviews = $dashboardPermissions['reviews'] ?? true;
        $canQuickSummary = $dashboardPermissions['quick_summary'] ?? true;
    @endphp

    <!-- Statistics Cards -->
    <div class="dash-section-title px-2"><i class="fas fa-chart-pie me-1"></i>{{ __('common.admin_dashboard_overview') }}</div>
    
    <!-- Quick Actions (Mobile Only) -->
    <div class="dash-quick-actions">
        <a href="{{ route('admin.bookings.index') }}" class="btn btn-outline-success">
            <i class="fas fa-calendar-check"></i> {{ __('common.bookings') }}
        </a>
        <a href="{{ route('admin.quotes.index') }}" class="btn btn-outline-primary">
            <i class="fas fa-file-invoice-dollar"></i> {{ __('common.quotes') }}
        </a>
        <a href="{{ route('admin.services.index') }}" class="btn btn-outline-info">
            <i class="fas fa-cogs"></i> {{ __('common.services') }}
        </a>
        <a href="{{ route('admin.customers.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-users"></i> {{ __('common.customers') }}
        </a>
    </div>

    <div class="row g-3 mb-4 dash-grid-2col">
        @if($canCustomers)
        <div class="col-xl-3 col-md-6">
            <div class="card dash-stat-card dc-primary h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <h3 class="mb-0">{{ $stats['customers'] ?? 0 }}</h3>
                        <p class="mb-0">{{ __('common.customers') }}</p>
                    </div>
                    <i class="fas fa-user-tie fa-2x"></i>
                </div>
            </div>
        </div>
        @endif
        @if($canAdmins)
        <div class="col-xl-3 col-md-6">
            <div class="card dash-stat-card dc-secondary h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <h3 class="mb-0">{{ $stats['admin_users'] ?? 0 }}</h3>
                        <p class="mb-0">{{ __('common.admins') }}</p>
                    </div>
                    <i class="fas fa-user-shield fa-2x"></i>
                </div>
            </div>
        </div>
        @endif
        @if($canServices)
        <div class="col-xl-3 col-md-6">
            <div class="card dash-stat-card dc-info h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <h3 class="mb-0">{{ $stats['services'] ?? 0 }}</h3>
                        <p class="mb-0">{{ __('common.services') }}</p>
                    </div>
                    <i class="fas fa-cogs fa-2x"></i>
                </div>
            </div>
        </div>
        @endif
        @if($canPackages)
        <div class="col-xl-3 col-md-6">
            <div class="card dash-stat-card dc-warning h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <h3 class="mb-0">{{ $stats['packages'] ?? 0 }}</h3>
                        <p class="mb-0">{{ __('common.packages') }}</p>
                    </div>
                    <i class="fas fa-box fa-2x"></i>
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- Second Row: Bookings -->
    <div class="row g-3 mb-4">
        @if($canBookings)
        <div class="col-xl-6 col-md-12">
            <div class="card dash-stat-card dc-success h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <h3 class="mb-0">{{ $stats['bookings'] ?? 0 }}</h3>
                        <p class="mb-0">{{ __('common.total_bookings') }}</p>
                        <small>{{ __('common.pending') }}: {{ $stats['pending_bookings'] ?? 0 }}</small>
                    </div>
                    <i class="fas fa-calendar-check fa-2x"></i>
                </div>
            </div>
        </div>
        @endif
        @if($canQuickSummary)
        <div class="col-xl-6 col-md-12">
            <div class="card dash-info-card h-100">
                <div class="card-header">
                    <h5 class="mb-0" style="color: #1f144a;"><i class="fas fa-chart-line me-2"></i>{{ __('common.quick_summary') }}</h5>
                </div>
                <div class="card-body">
                    @php
                        $summaryItems = [];
                        if ($canCustomers || $canAdmins) {
                            $summaryItems[] = [
                                'value' => $stats['total_users'] ?? 0,
                                'label' => __('common.total_users'),
                                'icon' => 'fas fa-users-cog',
                                'bg' => 'rgba(31,20,74,0.05)',
                                'color' => '#1f144a',
                            ];
                        }
                        if ($canServices || $canPackages) {
                            $summaryItems[] = [
                                'value' => ($stats['services'] ?? 0) + ($stats['packages'] ?? 0),
                                'label' => __('common.total_products'),
                                'icon' => 'fas fa-cubes',
                                'bg' => 'rgba(13,148,136,0.05)',
                                'color' => '#0d9488',
                            ];
                        }
                        if ($canBookings) {
                            $summaryItems[] = [
                                'value' => $stats['bookings'] ?? 0,
                                'label' => __('common.bookings'),
                                'icon' => 'fas fa-calendar-alt',
                                'bg' => 'rgba(16,185,129,0.05)',
                                'color' => '#10b981',
                            ];
                        }
                        $summaryColClass = count($summaryItems) <= 1 ? 'col-12' : (count($summaryItems) === 2 ? 'col-6' : 'col-4');
                    @endphp
                    <div class="row g-2">
                        @foreach($summaryItems as $item)
                            <div class="{{ $summaryColClass }}">
                                <div class="p-2 rounded-3 text-center" style="background: {{ $item['bg'] }};">
                                    <i class="{{ $item['icon'] }} mb-1" style="color: {{ $item['color'] }}; font-size: 1.1rem; opacity: 0.7;"></i>
                                    <h4 class="mb-0 fw-bold" style="color: {{ $item['color'] }};">{{ $item['value'] }}</h4>
                                    <div class="text-muted" style="font-size: 0.7rem;">{{ $item['label'] }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- Quotes Statistics -->
    <div class="dash-section-title px-2"><i class="fas fa-file-invoice-dollar me-1"></i>إحصائيات عروض الأسعار</div>
    <div class="row g-3 mb-4">
        @if($canTraffic)
        <div class="col-xl-4 col-md-6">
            <div class="card dash-stat-card dc-primary h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="card-title mb-1">{{ __('common.unique_visitors_today') }}</h6>
                        <h3 class="mb-0">{{ $stats['visits_today'] ?? 0 }}</h3>
                        <small>{{ __('common.last_7_days') }}: {{ $stats['visits_7d'] ?? 0 }}</small>
                    </div>
                    <i class="fas fa-users fa-2x"></i>
                </div>
            </div>
        </div>
        @endif
        @if($canTraffic)
        <div class="col-xl-4 col-md-6">
            <div class="card dash-stat-card dc-success h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="card-title mb-1">{{ __('common.logins_today') }}</h6>
                        <h3 class="mb-0">{{ $stats['logins_today'] ?? 0 }}</h3>
                        <small>{{ __('common.last_7_days') }}: {{ $stats['logins_7d'] ?? 0 }}</small>
                    </div>
                    <i class="fas fa-sign-in-alt fa-2x"></i>
                </div>
            </div>
        </div>
        @endif
        @if($canTraffic)
        <div class="col-xl-4 col-md-12">
            <div class="card dash-info-card h-100">
                <div class="card-body">
                    <h6 class="card-title">{{ __('common.top_countries_7_days') }}</h6>
                    <ul class="list-unstyled mb-0">
                        @forelse (($stats['top_countries_7d'] ?? []) as $row)
                            <li class="d-flex justify-content-between">
                                <span>{{ $row->country }}</span>
                                <span class="text-muted">{{ $row->count }}</span>
                            </li>
                        @empty
                            <li class="text-muted">{{ __('common.no_sufficient_data') }}</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
        @endif
        @if($canQuotes)
        <div class="col-xl-3 col-md-6">
            <div class="card dash-stat-card dc-purple h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <h3 class="mb-0">{{ $stats['quotes'] ?? 0 }}</h3>
                        <p class="mb-0">{{ __('common.quotes') }}</p>
                    </div>
                    <i class="fas fa-file-invoice-dollar fa-2x"></i>
                </div>
            </div>
        </div>
        @endif
        @if($canQuotes)
        <div class="col-xl-3 col-md-6">
            <div class="card dash-stat-card dc-warning h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <h3 class="mb-0">{{ $stats['pending_quotes'] ?? 0 }}</h3>
                        <p class="mb-0">{{ __('common.pending') }}</p>
                    </div>
                    <i class="fas fa-clock fa-2x"></i>
                </div>
            </div>
        </div>
        @endif
        @if($canQuotes)
        <div class="col-xl-3 col-md-6">
            <div class="card dash-stat-card dc-success h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <h3 class="mb-0">{{ $stats['approved_quotes'] ?? 0 }}</h3>
                        <p class="mb-0">{{ __('common.approved') }}</p>
                    </div>
                    <i class="fas fa-check-circle fa-2x"></i>
                </div>
            </div>
        </div>
        @endif
        @if($canQuotes)
        <div class="col-xl-3 col-md-6">
            <div class="card dash-stat-card dc-danger h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <h3 class="mb-0">{{ $stats['rejected_quotes'] ?? 0 }}</h3>
                        <p class="mb-0">{{ __('common.rejected') }}</p>
                    </div>
                    <i class="fas fa-times-circle fa-2x"></i>
                </div>
            </div>
        </div>
        @endif
        @if($canQuotes)
        <div class="col-xl-3 col-md-6">
            <div class="card dash-stat-card dc-teal h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <h3 class="mb-0">{{ $stats['completed_quotes'] ?? 0 }}</h3>
                        <p class="mb-0">{{ __('common.completed') }}</p>
                    </div>
                    <i class="fas fa-check-double fa-2x"></i>
                </div>
            </div>
        </div>
        @endif
    </div>

    <div class="dash-section-title px-2 mt-2"><i class="fas fa-th-large me-1"></i>{{ __('common.additional_statistics') }}</div>
    <div class="row g-3 mb-4">
        @if($canGallery)
        <div class="col-xl-6 col-md-12">
            <div class="card dash-info-card h-100">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="mb-0" style="color: #1f144a;"><i class="fas fa-images me-2"></i>{{ __('common.gallery') }}</h5>
                    <a href="{{ route('admin.gallery.index') }}" class="btn btn-outline-info btn-sm">{{ __('common.manage_gallery') }}</a>
                </div>
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <h2 class="text-info mb-0 fw-bold">{{ $stats['gallery_items'] ?? 0 }}</h2>
                        <p class="text-muted mb-0 small">{{ __('common.photos_and_videos') }}</p>
                    </div>
                    <i class="fas fa-photo-video fa-2x text-info opacity-25"></i>
                </div>
            </div>
        </div>
        @endif
        @if($canReviews)
        <div class="col-xl-6 col-md-12">
            <div class="card dash-info-card h-100">
                <div class="card-header">
                    <h5 class="mb-0" style="color: #1f144a;"><i class="fas fa-chart-bar me-2"></i>{{ __('common.additional_statistics') }}</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center g-0">
                        <div class="col-6 border-end">
                            <h4 class="text-primary fw-bold mb-0">{{ $stats['reviews'] ?? 0 }}</h4>
                            <p class="text-muted mb-0 small">{{ __('common.reviews') }}</p>
                        </div>
                        <div class="col-6">
                            <h4 class="text-warning fw-bold mb-0">{{ $stats['pending_reviews'] ?? 0 }}</h4>
                            <p class="text-muted mb-0 small">{{ __('common.pending_reviews') }}</p>
                        </div>
                    </div>
                    <hr class="my-3 opacity-50">
                    <div class="text-center">
                        <small class="text-muted" style="font-size: 0.65rem;">
                            <i class="fas fa-sync-alt me-1"></i>{{ __('common.last_updated') }}: {{ now()->format('H:i') }}
                        </small>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- Email & OTP Statistics -->
    <div class="dash-section-title px-2 mt-2"><i class="fas fa-shield-alt me-1"></i>التحقق والتواصل</div>
    <div class="row g-3 mb-4">
        @if($canEmails)
        <div class="col-xl-4 col-md-6">
            <div class="card dash-info-card h-100">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="mb-0" style="color: #1f144a;"><i class="fas fa-envelope-open-text me-2"></i>{{ __('common.email') }}</h5>
                    <a href="{{ route('admin.email-management.index') }}" class="btn btn-outline-primary btn-sm">{{ __('common.manage_email') }}</a>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h4 class="mb-0 fw-bold">{{ $stats['email_templates_total'] ?? 0 }}</h4>
                            <small class="text-muted">{{ __('common.email_templates') }}</small>
                        </div>
                        <div>
                            <span class="badge bg-success-subtle text-success px-2 py-1" style="font-size: 0.65rem;">
                                {{ __('common.active') }}: {{ $stats['email_templates_active'] ?? 0 }}
                            </span>
                        </div>
                    </div>
                    <div class="alert alert-info py-2 mb-0 border-0" style="font-size: 0.7rem; background: rgba(13,202,240,0.05);">
                        <i class="fas fa-info-circle me-1"></i>{{ __('common.email_templates_manage_hint') }}
                    </div>
                </div>
            </div>
        </div>
        @endif
        @if($canOtp)
        <div class="col-xl-4 col-md-6">
            <div class="card dash-info-card h-100">
                <div class="card-header">
                    <h5 class="mb-0" style="color: #1f144a;"><i class="fas fa-shield-alt me-2"></i>OTP</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center g-0 mb-3">
                        <div class="col-4 border-end">
                            <h4 class="text-primary mb-0 fw-bold">{{ $stats['otp_total'] ?? 0 }}</h4>
                            <div class="text-muted" style="font-size: 0.65rem;">{{ __('common.total') }}</div>
                        </div>
                        <div class="col-4 border-end">
                            <h4 class="text-success mb-0 fw-bold">{{ $stats['otp_verified'] ?? 0 }}</h4>
                            <div class="text-muted" style="font-size: 0.65rem;">{{ __('common.verified') }}</div>
                        </div>
                        <div class="col-4">
                            <h4 class="text-warning mb-0 fw-bold">{{ $stats['otp_pending'] ?? 0 }}</h4>
                            <div class="text-muted" style="font-size: 0.65rem;">{{ __('common.pending') }}</div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center bg-light p-2 rounded-2">
                        <small class="text-muted" style="font-size: 0.7rem;">{{ __('common.success_rate') }}</small>
                        <span class="fw-bold text-success" style="font-size: 0.8rem;">{{ $stats['otp_success_rate'] ?? 0 }}%</span>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- Recent Quotes -->
    <div class="dash-section-title px-2 mt-2"><i class="fas fa-history me-1"></i>النشاط الأخير</div>
    @if($canQuotes)
    <div class="row g-3 mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="mb-0"><i class="fas fa-file-invoice-dollar me-2"></i>{{ __('common.recent_quotes') }}</h5>
                    <a href="{{ route('admin.quotes.index') }}" class="btn btn-outline-secondary btn-sm">{{ __('common.view_all') }}</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 recent-quotes-table">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('common.quote_number') }}</th>
                                    <th>{{ __('common.customer') }}</th>
                                    <th>{{ __('common.status') }}</th>
                                    <th>{{ __('common.total') }}</th>
                                    <th class="dash-table-hide-mobile">{{ __('common.created_at') }}</th>
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
                                        <td class="dash-table-hide-mobile">{{ $quote->created_at?->format('d/m/Y H:i') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">{{ __('common.no_recent_quotes') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Recent Bookings -->
    @if($canBookings)
    <div class="row g-3">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="mb-0"><i class="fas fa-history me-2"></i>{{ __('common.recent_bookings') }}</h5>
                    <a href="{{ route('admin.bookings.index') }}" class="btn btn-outline-secondary btn-sm">{{ __('common.view_all') }}</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 recent-bookings-table">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('common.package') }}</th>
                                    <th class="dash-table-hide-mobile">{{ __('common.service') }}</th>
                                    <th>{{ __('common.status') }}</th>
                                    <th class="dash-table-hide-mobile">{{ __('common.created_at') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recent_bookings as $booking)
                                    <tr>
                                        <td>{{ $booking->id }}</td>
                                        <td>{{ optional($booking->package)->name ?? '-' }}</td>
                                        <td class="dash-table-hide-mobile">{{ optional($booking->service)->name ?? '-' }}</td>
                                        <td>
                                            <span class="badge bg-{{ $booking->status === 'pending' ? 'warning' : ($booking->status === 'approved' ? 'success' : ($booking->status === 'rejected' ? 'danger' : 'info')) }}">
                                                {{ $booking->status }}
                                            </span>
                                        </td>
                                        <td class="dash-table-hide-mobile">{{ $booking->created_at?->format('d/m/Y H:i') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">{{ __('common.no_recent_bookings') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var container = document.getElementById('adminDashboardAutoRefresh');
    if (!container) return;

    function refreshDashboard() {
        if (document.visibilityState !== 'visible') return;

        fetch(window.location.href, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            cache: 'no-store'
        })
        .then(response => response.text())
        .then(html => {
            var parser = new DOMParser();
            var doc = parser.parseFromString(html, 'text/html');
            var newContainer = doc.getElementById('adminDashboardAutoRefresh');
            if (newContainer) {
                container.innerHTML = newContainer.innerHTML;
            }
        })
        .catch(() => {});
    }

    setInterval(refreshDashboard, 10000); // كل 10 ثواني
});
</script>

{{-- Biometric Registration Prompt --}}
@if(session('admin_biometric_prompt'))
<script>
document.addEventListener('DOMContentLoaded', async function() {
    if (!window.PublicKeyCredential) return;
    try {
        const available = await PublicKeyCredential.isUserVerifyingPlatformAuthenticatorAvailable();
        if (!available) return;
    } catch(e) { return; }

    // Already registered on this device?
    if (localStorage.getItem('ye_admin_biometric_registered') === '1') return;

    // Show registration modal
    const modal = new bootstrap.Modal(document.getElementById('adminBiometricModal'));
    modal.show();
});

function base64ToArrayBuffer(base64) {
    const b64 = base64.replace(/-/g, '+').replace(/_/g, '/');
    const raw = atob(b64);
    const buf = new ArrayBuffer(raw.length);
    const view = new Uint8Array(buf);
    for (let i = 0; i < raw.length; i++) view[i] = raw.charCodeAt(i);
    return buf;
}
function arrayBufferToBase64(buffer) {
    const bytes = new Uint8Array(buffer);
    let binary = '';
    for (let i = 0; i < bytes.length; i++) binary += String.fromCharCode(bytes[i]);
    return btoa(binary);
}

async function registerAdminBiometric() {
    const btn = document.getElementById('adminBioRegBtn');
    const msg = document.getElementById('adminBioRegMsg');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>جارٍ التسجيل...';
    msg.style.display = 'none';

    try {
        // 1. Get registration options
        const optRes = await fetch('{{ route("biometric.register.options") }}', {
            method: 'POST',
            headers: {'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json'},
        });
        if (!optRes.ok) throw new Error((await optRes.json()).error || 'فشل في الحصول على خيارات التسجيل');
        const options = await optRes.json();

        // 2. Create credential
        const credential = await navigator.credentials.create({
            publicKey: {
                challenge: base64ToArrayBuffer(options.challenge),
                rp: options.rp,
                user: {
                    id: base64ToArrayBuffer(options.user.id),
                    name: options.user.name,
                    displayName: options.user.displayName,
                },
                pubKeyCredParams: options.pubKeyCredParams,
                authenticatorSelection: options.authenticatorSelection,
                timeout: options.timeout,
                attestation: options.attestation,
            }
        });

        // 3. Send to server
        const regRes = await fetch('{{ route("biometric.register") }}', {
            method: 'POST',
            headers: {'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json'},
            body: JSON.stringify({
                id: credential.id,
                rawId: arrayBufferToBase64(credential.rawId),
                response: {
                    clientDataJSON: arrayBufferToBase64(credential.response.clientDataJSON),
                    attestationObject: arrayBufferToBase64(credential.response.attestationObject),
                },
                type: credential.type,
                device_name: navigator.userAgent.match(/\(([^)]+)\)/)?.[1]?.split(';')[0] || navigator.platform,
            })
        });

        const result = await regRes.json();
        if (result.success) {
            localStorage.setItem('ye_admin_biometric_registered', '1');
            msg.className = 'alert alert-success small';
            msg.textContent = '✅ تم تسجيل البصمة بنجاح! يمكنك استخدامها للدخول مباشرة في المرات القادمة.';
            msg.style.display = 'block';
            btn.innerHTML = '<i class="fas fa-check me-2"></i>تم التسجيل';
            setTimeout(() => bootstrap.Modal.getInstance(document.getElementById('adminBiometricModal'))?.hide(), 2000);
        } else {
            throw new Error(result.error || 'فشل التسجيل');
        }
    } catch(err) {
        msg.className = 'alert alert-danger small';
        msg.textContent = err.name === 'NotAllowedError' ? 'تم إلغاء التسجيل' : ('خطأ: ' + err.message);
        msg.style.display = 'block';
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-fingerprint me-2"></i>نعم، فعّل البصمة';
    }
}
</script>

<!-- Biometric Registration Modal -->
<div class="modal fade" id="adminBiometricModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:15px;border:none;">
            <div class="modal-header border-0 pb-0" style="background:linear-gradient(135deg,#1f144a,#3b2d7a);border-radius:15px 15px 0 0;">
                <h5 class="modal-title text-white"><i class="fas fa-fingerprint me-2"></i>تفعيل الدخول بالبصمة</h5>
            </div>
            <div class="modal-body text-center py-4">
                <div style="font-size:60px;color:#1f144a;margin-bottom:15px;">
                    <i class="fas fa-fingerprint"></i>
                </div>
                <h5 class="fw-bold mb-2">هل تريد تفعيل الدخول السريع بالبصمة؟</h5>
                <p class="text-muted small">بعد التفعيل، يمكنك تسجيل الدخول لاحقاً بلمسة واحدة باستخدام بصمة الإصبع أو Face ID</p>
                <div id="adminBioRegMsg" class="mb-2" style="display:none;"></div>
                <button type="button" class="btn btn-primary w-100 mb-2" id="adminBioRegBtn" onclick="registerAdminBiometric()">
                    <i class="fas fa-fingerprint me-2"></i>نعم، فعّل البصمة
                </button>
                <button type="button" class="btn btn-light w-100" data-bs-dismiss="modal">
                    تخطي
                </button>
            </div>
        </div>
    </div>
</div>
@endif
@endpush

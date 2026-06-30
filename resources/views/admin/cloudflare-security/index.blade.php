@extends('layouts.admin')

@section('title', 'Cloudflare Security Center')
@section('page-title', 'Cloudflare Security Center')
@section('page-description', 'مراقبة وإدارة حماية السيرفر من Cloudflare')

@section('styles')
<style>
    :root {
        --cf-bg-dark: #0f1126;
        --cf-bg-card: #1a1d3a;
        --cf-bg-card-hover: #22254a;
        --cf-border: #2d3160;
        --cf-text: #e4e6f0;
        --cf-text-muted: #8b8fb5;
        --cf-orange: #f6821f;
        --cf-orange-dark: #e5701a;
        --cf-green: #28cd41;
        --cf-red: #fc3b3b;
        --cf-yellow: #ffb800;
        --cf-blue: #2b7fff;
        --cf-purple: #9b4dff;
    }

    .cf-security-wrapper {
        background: var(--cf-bg-dark);
        border-radius: 16px;
        padding: 24px;
        margin-bottom: 20px;
    }

    .cf-card {
        background: var(--cf-bg-card);
        border: 1px solid var(--cf-border);
        border-radius: 12px;
        padding: 20px;
        transition: all 0.3s ease;
        height: 100%;
    }

    .cf-card:hover {
        background: var(--cf-bg-card-hover);
        border-color: var(--cf-orange);
        transform: translateY(-2px);
    }

    .cf-stat-card {
        background: var(--cf-bg-card);
        border: 1px solid var(--cf-border);
        border-radius: 12px;
        padding: 20px;
        position: relative;
        overflow: hidden;
        transition: all 0.3s ease;
    }

    .cf-stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 4px;
        height: 100%;
        border-radius: 0 12px 12px 0;
    }

    .cf-stat-card.stat-blue::before { background: var(--cf-blue); }
    .cf-stat-card.stat-green::before { background: var(--cf-green); }
    .cf-stat-card.stat-red::before { background: var(--cf-red); }
    .cf-stat-card.stat-yellow::before { background: var(--cf-yellow); }
    .cf-stat-card.stat-purple::before { background: var(--cf-purple); }
    .cf-stat-card.stat-orange::before { background: var(--cf-orange); }

    .cf-stat-card:hover {
        background: var(--cf-bg-card-hover);
        transform: translateY(-2px);
    }

    .cf-stat-value {
        font-size: 1.75rem;
        font-weight: 800;
        color: var(--cf-text);
        margin: 0;
        line-height: 1.2;
    }

    .cf-stat-label {
        font-size: 0.8rem;
        color: var(--cf-text-muted);
        margin-bottom: 4px;
        font-weight: 500;
    }

    .cf-stat-icon {
        position: absolute;
        top: 15px;
        left: 15px;
        font-size: 1.5rem;
        opacity: 0.3;
    }

    .cf-section-title {
        color: var(--cf-text);
        font-size: 1.1rem;
        font-weight: 700;
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .cf-section-title i {
        color: var(--cf-orange);
    }

    .cf-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 3px 10px;
        border-radius: 20px;
        font-size: 0.72rem;
        font-weight: 600;
    }

    .cf-badge-active { background: rgba(40, 205, 65, 0.15); color: var(--cf-green); border: 1px solid rgba(40, 205, 65, 0.3); }
    .cf-badge-blocked { background: rgba(252, 59, 59, 0.15); color: var(--cf-red); border: 1px solid rgba(252, 59, 59, 0.3); }
    .cf-badge-challenge { background: rgba(255, 184, 0, 0.15); color: var(--cf-yellow); border: 1px solid rgba(255, 184, 0, 0.3); }
    .cf-badge-allow { background: rgba(43, 127, 255, 0.15); color: var(--cf-blue); border: 1px solid rgba(43, 127, 255, 0.3); }
    .cf-badge-paused { background: rgba(139, 143, 181, 0.15); color: var(--cf-text-muted); border: 1px solid rgba(139, 143, 181, 0.3); }

    .cf-severity {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 3px 10px;
        border-radius: 20px;
        font-size: 0.72rem;
        font-weight: 600;
    }

    .cf-severity-critical { background: rgba(252, 59, 59, 0.2); color: var(--cf-red); }
    .cf-severity-high { background: rgba(255, 100, 50, 0.2); color: #ff6432; }
    .cf-severity-medium { background: rgba(255, 184, 0, 0.2); color: var(--cf-yellow); }
    .cf-severity-low { background: rgba(43, 127, 255, 0.2); color: var(--cf-blue); }
    .cf-severity-info { background: rgba(139, 143, 181, 0.2); color: var(--cf-text-muted); }

    .cf-table {
        width: 100%;
        color: var(--cf-text);
    }

    .cf-table thead th {
        background: rgba(15, 17, 38, 0.5);
        color: var(--cf-text-muted);
        font-size: 0.78rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 1px solid var(--cf-border);
        padding: 12px 16px;
    }

    .cf-table tbody td {
        padding: 12px 16px;
        border-bottom: 1px solid rgba(45, 49, 96, 0.5);
        font-size: 0.87rem;
        color: var(--cf-text);
    }

    .cf-table tbody tr {
        transition: background 0.2s;
    }

    .cf-table tbody tr:hover {
        background: rgba(45, 49, 96, 0.3);
    }

    .cf-score-ring {
        position: relative;
        width: 120px;
        height: 120px;
        margin: 0 auto;
    }

    .cf-score-ring svg {
        transform: rotate(-90deg);
    }

    .cf-score-ring .score-text {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        text-align: center;
    }

    .cf-score-ring .score-number {
        font-size: 2rem;
        font-weight: 800;
        color: var(--cf-text);
        line-height: 1;
    }

    .cf-score-ring .score-label {
        font-size: 0.7rem;
        color: var(--cf-text-muted);
    }

    .cf-timeline {
        position: relative;
        padding-right: 24px;
    }

    .cf-timeline::before {
        content: '';
        position: absolute;
        right: 8px;
        top: 0;
        bottom: 0;
        width: 2px;
        background: var(--cf-border);
    }

    .cf-timeline-item {
        position: relative;
        margin-bottom: 16px;
        padding-right: 24px;
    }

    .cf-timeline-item::before {
        content: '';
        position: absolute;
        right: 1px;
        top: 6px;
        width: 16px;
        height: 16px;
        border-radius: 50%;
        border: 3px solid var(--cf-bg-dark);
        z-index: 1;
    }

    .cf-timeline-item.severity-critical::before { background: var(--cf-red); }
    .cf-timeline-item.severity-high::before { background: #ff6432; }
    .cf-timeline-item.severity-medium::before { background: var(--cf-yellow); }
    .cf-timeline-item.severity-low::before { background: var(--cf-blue); }
    .cf-timeline-item.severity-info::before { background: var(--cf-text-muted); }

    .cf-timeline-content {
        background: var(--cf-bg-card);
        border: 1px solid var(--cf-border);
        border-radius: 8px;
        padding: 12px 16px;
    }

    .cf-timeline-time {
        font-size: 0.72rem;
        color: var(--cf-text-muted);
        margin-bottom: 4px;
    }

    .cf-timeline-title {
        font-size: 0.87rem;
        color: var(--cf-text);
        font-weight: 600;
        margin-bottom: 2px;
    }

    .cf-timeline-desc {
        font-size: 0.78rem;
        color: var(--cf-text-muted);
    }

    .cf-action-btn {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 14px 16px;
        background: var(--cf-bg-card);
        border: 1px solid var(--cf-border);
        border-radius: 10px;
        color: var(--cf-text);
        text-decoration: none;
        transition: all 0.3s ease;
        width: 100%;
        text-align: right;
        font-size: 0.87rem;
        font-weight: 500;
    }

    .cf-action-btn:hover {
        color: var(--cf-text);
        border-color: var(--cf-orange);
        background: var(--cf-bg-card-hover);
    }

    .cf-action-btn i {
        font-size: 1.1rem;
        width: 24px;
        text-align: center;
    }

    .cf-action-btn.danger:hover { border-color: var(--cf-red); }
    .cf-action-btn.danger i { color: var(--cf-red); }
    .cf-action-btn.warning:hover { border-color: var(--cf-yellow); }
    .cf-action-btn.warning i { color: var(--cf-yellow); }
    .cf-action-btn.success:hover { border-color: var(--cf-green); }
    .cf-action-btn.success i { color: var(--cf-green); }
    .cf-action-btn.info:hover { border-color: var(--cf-blue); }
    .cf-action-btn.info i { color: var(--cf-blue); }

    .cf-notification {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        padding: 14px 16px;
        background: var(--cf-bg-card);
        border: 1px solid var(--cf-border);
        border-radius: 10px;
        margin-bottom: 10px;
        transition: all 0.2s;
    }

    .cf-notification:hover {
        background: var(--cf-bg-card-hover);
    }

    .cf-notification-icon {
        flex-shrink: 0;
        width: 36px;
        height: 36px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
    }

    .cf-notification.critical .cf-notification-icon { background: rgba(252, 59, 59, 0.15); color: var(--cf-red); }
    .cf-notification.warning .cf-notification-icon { background: rgba(255, 184, 0, 0.15); color: var(--cf-yellow); }
    .cf-notification.info .cf-notification-icon { background: rgba(43, 127, 255, 0.15); color: var(--cf-blue); }
    .cf-notification.success .cf-notification-icon { background: rgba(40, 205, 65, 0.15); color: var(--cf-green); }

    .cf-notification-title {
        font-size: 0.87rem;
        font-weight: 600;
        color: var(--cf-text);
        margin-bottom: 2px;
    }

    .cf-notification-message {
        font-size: 0.78rem;
        color: var(--cf-text-muted);
        line-height: 1.4;
    }

    .cf-notification-time {
        font-size: 0.72rem;
        color: var(--cf-text-muted);
        margin-top: 4px;
    }

    .cf-search-input {
        background: var(--cf-bg-dark);
        border: 1px solid var(--cf-border);
        color: var(--cf-text);
        border-radius: 8px;
        padding: 8px 14px;
        font-size: 0.85rem;
        width: 100%;
    }

    .cf-search-input::placeholder { color: var(--cf-text-muted); }
    .cf-search-input:focus {
        outline: none;
        border-color: var(--cf-orange);
        background: var(--cf-bg-card);
    }

    .cf-chart-container {
        background: var(--cf-bg-card);
        border: 1px solid var(--cf-border);
        border-radius: 12px;
        padding: 20px;
    }

    .cf-status-indicator {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 0.82rem;
        font-weight: 600;
    }

    .cf-status-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        animation: cf-pulse 2s infinite;
    }

    @keyframes cf-pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
    }

    .cf-status-dot.active { background: var(--cf-green); }
    .cf-status-dot.inactive { background: var(--cf-red); }

    .cf-demo-banner {
        background: rgba(255, 184, 0, 0.1);
        border: 1px solid rgba(255, 184, 0, 0.3);
        border-radius: 10px;
        padding: 12px 16px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
        color: var(--cf-yellow);
        font-size: 0.85rem;
    }

    .cf-section-divider {
        height: 1px;
        background: var(--cf-border);
        margin: 24px 0;
    }

    .cf-threat-card {
        background: var(--cf-bg-card);
        border: 1px solid var(--cf-border);
        border-radius: 10px;
        padding: 16px;
        display: flex;
        align-items: center;
        gap: 14px;
        transition: all 0.3s ease;
    }

    .cf-threat-card:hover {
        background: var(--cf-bg-card-hover);
        transform: translateY(-1px);
    }

    .cf-threat-icon {
        flex-shrink: 0;
        width: 44px;
        height: 44px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
    }

    .cf-threat-info { flex: 1; }
    .cf-threat-label { font-size: 0.8rem; color: var(--cf-text-muted); margin-bottom: 2px; }
    .cf-threat-count { font-size: 1.3rem; font-weight: 700; color: var(--cf-text); }

    .cf-ssl-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 0;
        border-bottom: 1px solid rgba(45, 49, 96, 0.5);
    }

    .cf-ssl-item:last-child { border-bottom: none; }
    .cf-ssl-label { font-size: 0.85rem; color: var(--cf-text-muted); }
    .cf-ssl-value { font-size: 0.85rem; color: var(--cf-text); font-weight: 600; }

    @media (max-width: 768px) {
        .cf-security-wrapper { padding: 14px; }
        .cf-stat-value { font-size: 1.3rem; }
    }
</style>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const chartData = @json($statistics['chart_data']);
    const ctx = document.getElementById('cfTrafficChart');
    if (ctx) {
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: chartData.labels,
                datasets: [
                    {
                        label: 'مسموح',
                        data: chartData.allowed,
                        borderColor: '#28cd41',
                        backgroundColor: 'rgba(40, 205, 65, 0.08)',
                        fill: true,
                        tension: 0.4,
                        pointRadius: 0,
                        borderWidth: 2,
                    },
                    {
                        label: 'محظور',
                        data: chartData.blocked,
                        borderColor: '#fc3b3b',
                        backgroundColor: 'rgba(252, 59, 59, 0.08)',
                        fill: true,
                        tension: 0.4,
                        pointRadius: 0,
                        borderWidth: 2,
                    },
                    {
                        label: 'تحدّي',
                        data: chartData.challenged,
                        borderColor: '#ffb800',
                        backgroundColor: 'rgba(255, 184, 0, 0.08)',
                        fill: true,
                        tension: 0.4,
                        pointRadius: 0,
                        borderWidth: 2,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        labels: { color: '#8b8fb5', font: { size: 11 }, usePointStyle: true, pointStyle: 'circle' }
                    },
                    tooltip: {
                        backgroundColor: '#1a1d3a',
                        titleColor: '#e4e6f0',
                        bodyColor: '#e4e6f0',
                        borderColor: '#2d3160',
                        borderWidth: 1,
                        padding: 12,
                    }
                },
                scales: {
                    x: {
                        ticks: { color: '#8b8fb5', font: { size: 10 }, maxTicksLimit: 12 },
                        grid: { color: 'rgba(45, 49, 96, 0.3)' }
                    },
                    y: {
                        ticks: { color: '#8b8fb5', font: { size: 10 } },
                        grid: { color: 'rgba(45, 49, 96, 0.3)' }
                    }
                }
            }
        });
    }

    // Doughnut chart for threats
    const threatCtx = document.getElementById('cfThreatChart');
    if (threatCtx) {
        new Chart(threatCtx, {
            type: 'doughnut',
            data: {
                labels: ['SQL Injection', 'XSS', 'DDoS', 'Bot Traffic', 'Suspicious API', 'Unauthorized Login'],
                datasets: [{
                    data: [
                        {{ $threats['sql_injection'] }},
                        {{ $threats['xss_attempts'] }},
                        {{ $threats['ddos_attacks'] }},
                        {{ min($threats['bot_traffic'], 50000) }},
                        {{ $threats['suspicious_api'] }},
                        {{ $threats['unauthorized_login'] }},
                    ],
                    backgroundColor: [
                        '#fc3b3b',
                        '#ff6432',
                        '#9b4dff',
                        '#2b7fff',
                        '#ffb800',
                        '#28cd41',
                    ],
                    borderWidth: 0,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { color: '#8b8fb5', font: { size: 10 }, usePointStyle: true, pointStyle: 'circle', padding: 12 }
                    },
                    tooltip: {
                        backgroundColor: '#1a1d3a',
                        titleColor: '#e4e6f0',
                        bodyColor: '#e4e6f0',
                        borderColor: '#2d3160',
                        borderWidth: 1,
                    }
                },
                cutout: '65%',
            }
        });
    }

    // Firewall rules search/filter
    const fwSearch = document.getElementById('fwSearch');
    const fwFilter = document.getElementById('fwFilter');
    const fwRows = document.querySelectorAll('.fw-rule-row');

    function filterFwRules() {
        const search = fwSearch ? fwSearch.value.toLowerCase() : '';
        const filter = fwFilter ? fwFilter.value : 'all';

        fwRows.forEach(row => {
            const text = row.textContent.toLowerCase();
            const status = row.getAttribute('data-status');
            const action = row.getAttribute('data-action');

            const matchesSearch = text.includes(search);
            const matchesFilter = filter === 'all' || status === filter || action === filter;

            row.style.display = (matchesSearch && matchesFilter) ? '' : 'none';
        });
    }

    if (fwSearch) fwSearch.addEventListener('input', filterFwRules);
    if (fwFilter) fwFilter.addEventListener('change', filterFwRules);

    // Security logs search
    const logSearch = document.getElementById('logSearch');
    const logFilter = document.getElementById('logFilter');
    const logRows = document.querySelectorAll('.log-row');

    function filterLogs() {
        const search = logSearch ? logSearch.value.toLowerCase() : '';
        const filter = logFilter ? logFilter.value : 'all';

        logRows.forEach(row => {
            const text = row.textContent.toLowerCase();
            const severity = row.getAttribute('data-severity');

            const matchesSearch = text.includes(search);
            const matchesFilter = filter === 'all' || severity === filter;

            row.style.display = (matchesSearch && matchesFilter) ? '' : 'none';
        });
    }

    if (logSearch) logSearch.addEventListener('input', filterLogs);
    if (logFilter) logFilter.addEventListener('change', filterLogs);
});
</script>
@endpush

@section('content')

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('warning'))
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-triangle me-2"></i>{{ session('warning') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="cf-security-wrapper">

    @if(!$isConfigured)
        <div class="cf-demo-banner">
            <i class="fas fa-info-circle"></i>
            <span>
                <strong>وضع العرض التجريبي:</strong>
                يتم عرض بيانات تجريبية. لإظهار البيانات الحقيقية، أضف الإعدادات التالية إلى ملف <code>.env</code>:
                <code>CLOUDFLARE_API_KEY</code>, <code>CLOUDFLARE_EMAIL</code>, <code>CLOUDFLARE_ZONE_ID</code>
            </span>
        </div>
    @endif

    {{-- ============================================================ --}}
    {{-- 1. Security Overview --}}
    {{-- ============================================================ --}}
    <div class="row g-3 mb-3">
        <div class="col-lg-3 col-md-6">
            <div class="cf-card">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <div class="cf-stat-label">حالة الاتصال</div>
                        <div class="cf-status-indicator" style="color: var(--cf-green);">
                            <span class="cf-status-dot {{ $overview['connected'] ? 'active' : 'inactive' }}"></span>
                            {{ $overview['connected'] ? 'متصل' : 'غير متصل' }}
                        </div>
                    </div>
                    <i class="fas fa-cloud fa-2x" style="color: var(--cf-orange); opacity: 0.5;"></i>
                </div>
                <div class="text-muted" style="font-size: 0.78rem; color: var(--cf-text-muted);">
                    الخطة: {{ $overview['plan'] ?? 'Free' }} | الحالة: {{ $overview['status'] ?? 'N/A' }}
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="cf-card">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <div class="cf-stat-label">جدار الحماية (WAF)</div>
                        <div class="cf-status-indicator" style="color: var(--cf-green);">
                            <span class="cf-status-dot active"></span>
                            نشط
                        </div>
                    </div>
                    <i class="fas fa-shield-alt fa-2x" style="color: var(--cf-blue); opacity: 0.5;"></i>
                </div>
                <div style="font-size: 0.78rem; color: var(--cf-text-muted);">
                    حماية طبقة التطبيق (Layer 7)
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="cf-card">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <div class="cf-stat-label">حماية DDoS</div>
                        <div class="cf-status-indicator" style="color: var(--cf-green);">
                            <span class="cf-status-dot active"></span>
                            نشط
                        </div>
                    </div>
                    <i class="fas fa-network-wired fa-2x" style="color: var(--cf-purple); opacity: 0.5;"></i>
                </div>
                <div style="font-size: 0.78rem; color: var(--cf-text-muted);">
                    حماية طبقات 3 و 4 و 7
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="cf-card">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <div class="cf-stat-label">SSL / TLS</div>
                        <div class="cf-status-indicator" style="color: var(--cf-green);">
                            <span class="cf-status-dot active"></span>
                            نشط
                        </div>
                    </div>
                    <i class="fas fa-lock fa-2x" style="color: var(--cf-green); opacity: 0.5;"></i>
                </div>
                <div style="font-size: 0.78rem; color: var(--cf-text-muted);">
                    الوضع: {{ $overview['ssl_mode'] ?? 'full' }}
                </div>
            </div>
        </div>
    </div>

    {{-- Security Score + Last Sync --}}
    <div class="row g-3 mb-3">
        <div class="col-lg-8 col-md-7">
            <div class="cf-chart-container" style="height: 280px;">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="cf-section-title mb-0"><i class="fas fa-chart-area"></i> حركة المرور (آخر 24 ساعة)</h6>
                    <div class="d-flex gap-3" style="font-size: 0.78rem;">
                        <span style="color: var(--cf-green);"><i class="fas fa-circle me-1" style="font-size: 8px;"></i>مسموح</span>
                        <span style="color: var(--cf-red);"><i class="fas fa-circle me-1" style="font-size: 8px;"></i>محظور</span>
                        <span style="color: var(--cf-yellow);"><i class="fas fa-circle me-1" style="font-size: 8px;"></i>تحدّي</span>
                    </div>
                </div>
                <canvas id="cfTrafficChart"></canvas>
            </div>
        </div>
        <div class="col-lg-4 col-md-5">
            <div class="cf-card text-center h-100 d-flex flex-column justify-content-center">
                <h6 class="cf-section-title justify-content-center"><i class="fas fa-star"></i> درجة الأمان</h6>
                <div class="cf-score-ring">
                    <svg width="120" height="120" viewBox="0 0 120 120">
                        <circle cx="60" cy="60" r="52" fill="none" stroke="var(--cf-border)" stroke-width="8"/>
                        <circle cx="60" cy="60" r="52" fill="none" stroke="var(--cf-orange)" stroke-width="8"
                                stroke-dasharray="{{ 2 * pi() * 52 }}"
                                stroke-dashoffset="{{ 2 * pi() * 52 * (1 - ($overview['security_score'] ?? 85) / 100) }}"
                                stroke-linecap="round"
                                style="transition: stroke-dashoffset 1s ease;"/>
                    </svg>
                    <div class="score-text">
                        <div class="score-number">{{ $overview['security_score'] ?? 85 }}</div>
                        <div class="score-label">من 100</div>
                    </div>
                </div>
                <div class="mt-3" style="font-size: 0.82rem; color: var(--cf-text-muted);">
                    <i class="fas fa-clock me-1"></i>آخر مزامنة: {{ $overview['last_sync'] ?? 'N/A' }}
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- 2. Live Security Statistics --}}
    {{-- ============================================================ --}}
    <div class="cf-section-divider"></div>
    <h5 class="cf-section-title"><i class="fas fa-chart-bar"></i> الإحصائيات المباشرة</h5>
    <div class="row g-3 mb-3">
        <div class="col-lg-2 col-md-4 col-sm-6">
            <div class="cf-stat-card stat-blue">
                <i class="fas fa-globe cf-stat-icon" style="color: var(--cf-blue);"></i>
                <div class="cf-stat-label">إجمالي الطلبات</div>
                <div class="cf-stat-value">{{ number_format($statistics['total_requests']) }}</div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6">
            <div class="cf-stat-card stat-green">
                <i class="fas fa-check-circle cf-stat-icon" style="color: var(--cf-green);"></i>
                <div class="cf-stat-label">طلبات مسموحة</div>
                <div class="cf-stat-value">{{ number_format($statistics['allowed_requests']) }}</div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6">
            <div class="cf-stat-card stat-red">
                <i class="fas fa-ban cf-stat-icon" style="color: var(--cf-red);"></i>
                <div class="cf-stat-label">طلبات محظورة</div>
                <div class="cf-stat-value">{{ number_format($statistics['blocked_requests']) }}</div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6">
            <div class="cf-stat-card stat-yellow">
                <i class="fas fa-shield-alt cf-stat-icon" style="color: var(--cf-yellow);"></i>
                <div class="cf-stat-label">طلبات التحدّي</div>
                <div class="cf-stat-value">{{ number_format($statistics['challenged_requests']) }}</div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6">
            <div class="cf-stat-card stat-purple">
                <i class="fas fa-robot cf-stat-icon" style="color: var(--cf-purple);"></i>
                <div class="cf-stat-label">طلبات البوتات</div>
                <div class="cf-stat-value">{{ number_format($statistics['bot_requests']) }}</div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6">
            <div class="cf-stat-card stat-orange">
                <i class="fas fa-exclamation-triangle cf-stat-icon" style="color: var(--cf-orange);"></i>
                <div class="cf-stat-label">تهديدات اليوم</div>
                <div class="cf-stat-value">{{ number_format($statistics['threats_today']) }}</div>
            </div>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- 3. Cloudflare Firewall Rules --}}
    {{-- ============================================================ --}}
    <div class="cf-section-divider"></div>
    <h5 class="cf-section-title"><i class="fas fa-list-shield"></i> قواعد جدار الحماية</h5>
    <div class="cf-card mb-3">
        <div class="row g-2 mb-3">
            <div class="col-md-8">
                <input type="text" id="fwSearch" class="cf-search-input" placeholder="🔍 ابحث في القواعد...">
            </div>
            <div class="col-md-4">
                <select id="fwFilter" class="cf-search-input">
                    <option value="all">كل القواعد</option>
                    <option value="active">نشطة</option>
                    <option value="paused">متوقفة</option>
                    <option value="block">حظر</option>
                    <option value="allow">سماح</option>
                    <option value="challenge">تحدّي</option>
                </select>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table cf-table">
                <thead>
                    <tr>
                        <th>اسم القاعدة</th>
                        <th>الوصف</th>
                        <th>الإجراء</th>
                        <th>الحالة</th>
                        <th>آخر تفعيل</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($firewallRules as $rule)
                        <tr class="fw-rule-row" data-status="{{ $rule['status'] }}" data-action="{{ $rule['action'] }}">
                            <td>
                                <i class="fas fa-shield-alt me-1" style="color: var(--cf-orange); opacity: 0.6;"></i>
                                {{ $rule['name'] }}
                            </td>
                            <td style="color: var(--cf-text-muted);">{{ $rule['description'] }}</td>
                            <td>
                                @php
                                    $actionLabels = ['block' => 'حظر', 'allow' => 'سماح', 'challenge' => 'تحدّي', 'js_challenge' => 'JS تحدّي', 'log' => 'تسجيل'];
                                    $actionClasses = ['block' => 'cf-badge-blocked', 'allow' => 'cf-badge-allow', 'challenge' => 'cf-badge-challenge', 'js_challenge' => 'cf-badge-challenge', 'log' => 'cf-badge-paused'];
                                @endphp
                                <span class="cf-badge {{ $actionClasses[$rule['action']] ?? 'cf-badge-paused' }}">
                                    {{ $actionLabels[$rule['action']] ?? $rule['action'] }}
                                </span>
                            </td>
                            <td>
                                @if($rule['status'] === 'active')
                                    <span class="cf-badge cf-badge-active"><i class="fas fa-circle" style="font-size: 6px;"></i> نشط</span>
                                @else
                                    <span class="cf-badge cf-badge-paused"><i class="fas fa-pause" style="font-size: 8px;"></i> متوقف</span>
                                @endif
                            </td>
                            <td style="color: var(--cf-text-muted);">{{ $rule['last_triggered'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- 4. Threat Detection Center --}}
    {{-- ============================================================ --}}
    <div class="cf-section-divider"></div>
    <h5 class="cf-section-title"><i class="fas fa-radar"></i> مركز كشف التهديدات</h5>
    <div class="row g-3 mb-3">
        <div class="col-lg-8">
            <div class="row g-2">
                <div class="col-md-6">
                    <div class="cf-threat-card">
                        <div class="cf-threat-icon" style="background: rgba(252, 59, 59, 0.15); color: var(--cf-red);">
                            <i class="fas fa-database"></i>
                        </div>
                        <div class="cf-threat-info">
                            <div class="cf-threat-label">SQL Injection</div>
                            <div class="cf-threat-count">{{ $threats['sql_injection'] }}</div>
                        </div>
                        <span class="cf-severity cf-severity-high">عالي</span>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="cf-threat-card">
                        <div class="cf-threat-icon" style="background: rgba(255, 100, 50, 0.15); color: #ff6432;">
                            <i class="fas fa-code"></i>
                        </div>
                        <div class="cf-threat-info">
                            <div class="cf-threat-label">XSS Attempts</div>
                            <div class="cf-threat-count">{{ $threats['xss_attempts'] }}</div>
                        </div>
                        <span class="cf-severity cf-severity-high">عالي</span>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="cf-threat-card">
                        <div class="cf-threat-icon" style="background: rgba(155, 77, 255, 0.15); color: var(--cf-purple);">
                            <i class="fas fa-network-wired"></i>
                        </div>
                        <div class="cf-threat-info">
                            <div class="cf-threat-label">DDoS Attacks</div>
                            <div class="cf-threat-count">{{ $threats['ddos_attacks'] }}</div>
                        </div>
                        <span class="cf-severity cf-severity-critical">حرج</span>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="cf-threat-card">
                        <div class="cf-threat-icon" style="background: rgba(43, 127, 255, 0.15); color: var(--cf-blue);">
                            <i class="fas fa-robot"></i>
                        </div>
                        <div class="cf-threat-info">
                            <div class="cf-threat-label">Bot Traffic</div>
                            <div class="cf-threat-count">{{ number_format($threats['bot_traffic']) }}</div>
                        </div>
                        <span class="cf-severity cf-severity-medium">متوسط</span>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="cf-threat-card">
                        <div class="cf-threat-icon" style="background: rgba(255, 184, 0, 0.15); color: var(--cf-yellow);">
                            <i class="fas fa-plug"></i>
                        </div>
                        <div class="cf-threat-info">
                            <div class="cf-threat-label">Suspicious API</div>
                            <div class="cf-threat-count">{{ $threats['suspicious_api'] }}</div>
                        </div>
                        <span class="cf-severity cf-severity-medium">متوسط</span>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="cf-threat-card">
                        <div class="cf-threat-icon" style="background: rgba(40, 205, 65, 0.15); color: var(--cf-green);">
                            <i class="fas fa-user-lock"></i>
                        </div>
                        <div class="cf-threat-info">
                            <div class="cf-threat-label">Unauthorized Login</div>
                            <div class="cf-threat-count">{{ $threats['unauthorized_login'] }}</div>
                        </div>
                        <span class="cf-severity cf-severity-high">عالي</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="cf-chart-container" style="height: 100%; min-height: 300px;">
                <h6 class="cf-section-title"><i class="fas fa-chart-pie"></i> توزيع التهديدات</h6>
                <div style="position: relative; height: 250px;">
                    <canvas id="cfThreatChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- 5. Top Blocked IP Addresses --}}
    {{-- ============================================================ --}}
    <div class="cf-section-divider"></div>
    <h5 class="cf-section-title"><i class="fas fa-ban"></i> أكثر عناوين IP المحظورة</h5>
    <div class="cf-card mb-3">
        <div class="table-responsive">
            <table class="table cf-table">
                <thead>
                    <tr>
                        <th>عنوان IP</th>
                        <th>الدولة</th>
                        <th>نوع التهديد</th>
                        <th>عدد الطلبات</th>
                        <th>آخر نشاط</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($blockedIPs as $ip)
                        <tr>
                            <td>
                                <i class="fas fa-globe me-1" style="color: var(--cf-text-muted);"></i>
                                <code style="color: var(--cf-orange);">{{ $ip['ip'] }}</code>
                            </td>
                            <td>{{ $ip['country'] }}</td>
                            <td>
                                <span class="cf-badge cf-badge-blocked">{{ $ip['threat_type'] }}</span>
                            </td>
                            <td style="color: var(--cf-red); font-weight: 600;">{{ number_format($ip['requests']) }}</td>
                            <td style="color: var(--cf-text-muted);">{{ $ip['last_activity'] }}</td>
                            <td>
                                <form method="POST" action="{{ route('admin.cloudflare-security.allow-ip') }}" style="display:inline;">
                                    @csrf
                                    <input type="hidden" name="ip" value="{{ $ip['ip'] }}">
                                    <button type="submit" class="btn btn-sm btn-outline-success" title="السماح"
                                            onclick="return confirm('السماح لـ {{ $ip['ip'] }}؟')">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- 6. Security Events Timeline --}}
    {{-- ============================================================ --}}
    <div class="cf-section-divider"></div>
    <h5 class="cf-section-title"><i class="fas fa-stream"></i> سجل الأحداث الأمنية</h5>
    <div class="row g-3 mb-3">
        <div class="col-lg-7">
            <div class="cf-card" style="max-height: 400px; overflow-y: auto;">
                <div class="cf-timeline">
                    @foreach($events as $event)
                        <div class="cf-timeline-item severity-{{ $event['severity'] }}">
                            <div class="cf-timeline-content">
                                <div class="cf-timeline-time">
                                    <i class="fas fa-clock me-1"></i>{{ $event['timestamp'] }}
                                    <span class="cf-severity cf-severity-{{ $event['severity'] }} ms-2">{{ ucfirst($event['severity']) }}</span>
                                </div>
                                <div class="cf-timeline-title">
                                    <i class="fas fa-exclamation-circle me-1" style="color: var(--cf-orange);"></i>
                                    {{ $event['type'] }}
                                    <span style="color: var(--cf-text-muted); font-weight: 400;">— {{ $event['description'] }}</span>
                                </div>
                                <div class="cf-timeline-desc">
                                    <i class="fas fa-map-marker-alt me-1"></i>IP: <code style="color: var(--cf-orange);">{{ $event['source_ip'] }}</code>
                                    <span class="ms-2"><i class="fas fa-shield-alt me-1"></i>الإجراء: {{ $event['action'] }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- 7. Quick Security Actions --}}
        <div class="col-lg-5">
            <h5 class="cf-section-title"><i class="fas fa-bolt"></i> إجراءات أمنية سريعة</h5>
            <div class="d-grid gap-2">
                <form method="POST" action="{{ route('admin.cloudflare-security.enable-under-attack') }}">
                    @csrf
                    <button type="submit" class="cf-action-btn danger"
                            onclick="return confirm('هل أنت متأكد من تفعيل وضع Under Attack؟ سيتم تطبيق تحدّي على جميع الزوار.')">
                        <i class="fas fa-exclamation-triangle"></i>
                        <span>تفعيل وضع Under Attack</span>
                    </button>
                </form>
                <form method="POST" action="{{ route('admin.cloudflare-security.purge-cache') }}">
                    @csrf
                    <button type="submit" class="cf-action-btn warning"
                            onclick="return confirm('هل أنت متأكد من مسح Cloudflare Cache؟')">
                        <i class="fas fa-trash-alt"></i>
                        <span>مسح Cloudflare Cache</span>
                    </button>
                </form>
                <button type="button" class="cf-action-btn danger" data-bs-toggle="modal" data-bs-target="#blockIPModal">
                    <i class="fas fa-ban"></i>
                    <span>حظر عنوان IP</span>
                </button>
                <button type="button" class="cf-action-btn success" data-bs-toggle="modal" data-bs-target="#allowIPModal">
                    <i class="fas fa-check-circle"></i>
                    <span>السماح لعنوان IP</span>
                </button>
                <button type="button" class="cf-action-btn warning" data-bs-toggle="modal" data-bs-target="#blockCountryModal">
                    <i class="fas fa-flag"></i>
                    <span>حظر دولة</span>
                </button>
                <a href="#security-logs-section" class="cf-action-btn info">
                    <i class="fas fa-file-alt"></i>
                    <span>عرض سجلات الأمان</span>
                </a>
            </div>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- 8. SSL & Encryption Section --}}
    {{-- ============================================================ --}}
    <div class="cf-section-divider"></div>
    <h5 class="cf-section-title"><i class="fas fa-lock"></i> SSL والتشفير</h5>
    <div class="row g-3 mb-3">
        <div class="col-lg-6">
            <div class="cf-card">
                <div class="cf-ssl-item">
                    <span class="cf-ssl-label"><i class="fas fa-shield-alt me-2" style="color: var(--cf-blue);"></i>وضع SSL</span>
                    <span class="cf-ssl-value">
                        <span class="cf-badge cf-badge-active">{{ $sslInfo['ssl_mode'] }}</span>
                    </span>
                </div>
                <div class="cf-ssl-item">
                    <span class="cf-ssl-label"><i class="fas fa-certificate me-2" style="color: var(--cf-green);"></i>حالة الشهادة</span>
                    <span class="cf-ssl-value">
                        <span class="cf-badge cf-badge-active">{{ $sslInfo['certificate_status'] === 'active' ? 'نشطة' : 'منتهية' }}</span>
                    </span>
                </div>
                <div class="cf-ssl-item">
                    <span class="cf-ssl-label"><i class="fas fa-calendar-alt me-2" style="color: var(--cf-yellow);"></i>تاريخ الانتهاء</span>
                    <span class="cf-ssl-value">{{ $sslInfo['expiration_date'] }}</span>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="cf-card">
                <div class="cf-ssl-item">
                    <span class="cf-ssl-label"><i class="fas fa-key me-2" style="color: var(--cf-purple);"></i>إصدار TLS</span>
                    <span class="cf-ssl-value">
                        <span class="cf-badge cf-badge-active">{{ $sslInfo['tls_version'] }}</span>
                    </span>
                </div>
                <div class="cf-ssl-item">
                    <span class="cf-ssl-label"><i class="fas fa-shield-alt me-2" style="color: var(--cf-orange);"></i>HSTS</span>
                    <span class="cf-ssl-value">
                        @if($sslInfo['hsts_status'] === 'enabled')
                            <span class="cf-badge cf-badge-active">مفعّل</span>
                        @else
                            <span class="cf-badge cf-badge-paused">معطّل</span>
                        @endif
                    </span>
                </div>
                <div class="cf-ssl-item">
                    <span class="cf-ssl-label"><i class="fas fa-info-circle me-2" style="color: var(--cf-blue);"></i>إعادة توجيه HTTPS</span>
                    <span class="cf-ssl-value">
                        <span class="cf-badge cf-badge-active">مفعّل</span>
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- 9. Security Logs --}}
    {{-- ============================================================ --}}
    <div class="cf-section-divider"></div>
    <div id="security-logs-section">
        <h5 class="cf-section-title"><i class="fas fa-file-alt"></i> سجلات الأمان</h5>
        <div class="cf-card mb-3">
            <div class="row g-2 mb-3">
                <div class="col-md-8">
                    <input type="text" id="logSearch" class="cf-search-input" placeholder="🔍 ابحث في السجلات...">
                </div>
                <div class="col-md-4">
                    <select id="logFilter" class="cf-search-input">
                        <option value="all">كل المستويات</option>
                        <option value="critical">حرج</option>
                        <option value="high">عالي</option>
                        <option value="medium">متوسط</option>
                        <option value="low">منخفض</option>
                        <option value="info">معلومات</option>
                    </select>
                </div>
            </div>
            <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                <table class="table cf-table">
                    <thead style="position: sticky; top: 0; z-index: 1;">
                        <tr>
                            <th>الوقت</th>
                            <th>نوع الحدث</th>
                            <th>IP المصدر</th>
                            <th>الدولة</th>
                            <th>الإجراء</th>
                            <th>الخطورة</th>
                            <th>Ray ID</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($logs as $log)
                            <tr class="log-row" data-severity="{{ $log['severity'] }}">
                                <td style="color: var(--cf-text-muted); white-space: nowrap;">{{ $log['timestamp'] }}</td>
                                <td>{{ $log['event_type'] }}</td>
                                <td><code style="color: var(--cf-orange);">{{ $log['source_ip'] }}</code></td>
                                <td>{{ $log['country'] }}</td>
                                <td>
                                    @php
                                        $logActionLabels = ['blocked' => 'محظور', 'challenged' => 'تحدّي', 'allowed' => 'مسموح', 'logged' => 'مسجل'];
                                    @endphp
                                    <span class="cf-badge {{ $log['action_taken'] === 'blocked' ? 'cf-badge-blocked' : ($log['action_taken'] === 'challenged' ? 'cf-badge-challenge' : ($log['action_taken'] === 'allowed' ? 'cf-badge-allow' : 'cf-badge-paused')) }}">
                                        {{ $logActionLabels[$log['action_taken']] ?? $log['action_taken'] }}
                                    </span>
                                </td>
                                <td>
                                    @php
                                        $severityLabels = ['critical' => 'حرج', 'high' => 'عالي', 'medium' => 'متوسط', 'low' => 'منخفض', 'info' => 'معلومات'];
                                    @endphp
                                    <span class="cf-severity cf-severity-{{ $log['severity'] }}">{{ $severityLabels[$log['severity']] ?? $log['severity'] }}</span>
                                </td>
                                <td><code style="color: var(--cf-text-muted); font-size: 0.75rem;">{{ $log['ray_id'] }}</code></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- 10. Notification Center --}}
    {{-- ============================================================ --}}
    <div class="cf-section-divider"></div>
    <h5 class="cf-section-title"><i class="fas fa-bell"></i> مركز الإشعارات</h5>
    <div class="row g-3">
        @foreach($notifications as $notification)
            <div class="col-md-6">
                <div class="cf-notification {{ $notification['type'] }}">
                    <div class="cf-notification-icon">
                        <i class="fas {{ $notification['icon'] }}"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="cf-notification-title">{{ $notification['title'] }}</div>
                        <div class="cf-notification-message">{{ $notification['message'] }}</div>
                        <div class="cf-notification-time"><i class="fas fa-clock me-1"></i>{{ $notification['time'] }}</div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

</div>

{{-- ============================================================ --}}
{{-- Modals --}}
{{-- ============================================================ --}}

{{-- Block IP Modal --}}
<div class="modal fade" id="blockIPModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background: var(--cf-bg-dark); color: var(--cf-text);">
                <h5 class="modal-title"><i class="fas fa-ban me-2" style="color: var(--cf-red);"></i>حظر عنوان IP</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('admin.cloudflare-security.block-ip') }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">عنوان IP المراد حظره</label>
                        <input type="text" name="ip" class="form-control" placeholder="مثال: 192.168.1.1" required pattern="^(\d{1,3}\.){3}\d{1,3}$">
                        <small class="text-muted">أدخل عنوان IPv4 صالح</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-danger"><i class="fas fa-ban me-1"></i>حظر</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Allow IP Modal --}}
<div class="modal fade" id="allowIPModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background: var(--cf-bg-dark); color: var(--cf-text);">
                <h5 class="modal-title"><i class="fas fa-check-circle me-2" style="color: var(--cf-green);"></i>السماح لعنوان IP</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('admin.cloudflare-security.allow-ip') }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">عنوان IP المراد السماح له</label>
                        <input type="text" name="ip" class="form-control" placeholder="مثال: 192.168.1.1" required pattern="^(\d{1,3}\.){3}\d{1,3}$">
                        <small class="text-muted">أدخل عنوان IPv4 صالح</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-success"><i class="fas fa-check me-1"></i>سماح</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Block Country Modal --}}
<div class="modal fade" id="blockCountryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background: var(--cf-bg-dark); color: var(--cf-text);">
                <h5 class="modal-title"><i class="fas fa-flag me-2" style="color: var(--cf-yellow);"></i>حظر دولة</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('admin.cloudflare-security.block-country') }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">رمز الدولة (حرفان)</label>
                        <input type="text" name="country_code" class="form-control text-uppercase" placeholder="مثال: RU, CN, IR" required maxlength="2" style="text-transform: uppercase;">
                        <small class="text-muted">أدخل رمز الدولة ISO 3166-1 alpha-2</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-warning"><i class="fas fa-flag me-1"></i>حظر الدولة</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

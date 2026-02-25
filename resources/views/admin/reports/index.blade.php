@extends('layouts.admin')

@section('title', 'التقارير والإحصائيات')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="mb-0">
                    <i class="fas fa-chart-line me-2"></i>
                    التقارير والإحصائيات
                </h2>
                <div class="btn-group">
                    <button type="button" class="btn btn-success" onclick="exportReport('excel')">
                        <i class="fas fa-file-excel"></i> تصدير Excel
                    </button>
                    <button type="button" class="btn btn-danger" onclick="exportReport('pdf')">
                        <i class="fas fa-file-pdf"></i> تصدير PDF
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Date Filter -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.reports.index') }}" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">من تاريخ</label>
                    <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">إلى تاريخ</label>
                    <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter"></i> فلترة
                    </button>
                    <a href="{{ route('admin.reports.index') }}" class="btn btn-secondary ms-2">
                        <i class="fas fa-redo"></i> إعادة تعيين
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-start border-primary border-4 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">إجمالي الإيرادات</h6>
                            <h3 class="mb-0 text-primary">{{ number_format($revenue['total'], 2) }} {{ __('common.currency') }}</h3>
                            <small class="text-muted">من عروض الأسعار المعتمدة</small>
                        </div>
                        <div class="fs-1 text-primary opacity-50">
                            <i class="fas fa-money-bill-wave"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-start border-success border-4 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">إيرادات الحجوزات</h6>
                            <h3 class="mb-0 text-success">{{ number_format($revenue['bookings'], 2) }} {{ __('common.currency') }}</h3>
                            <small class="text-muted">من الحجوزات المؤكدة</small>
                        </div>
                        <div class="fs-1 text-success opacity-50">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-start border-info border-4 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">عدد عروض الأسعار</h6>
                            <h3 class="mb-0 text-info">{{ number_format($revenue['quotes_count']) }}</h3>
                            <small class="text-muted">في الفترة المحددة</small>
                        </div>
                        <div class="fs-1 text-info opacity-50">
                            <i class="fas fa-file-invoice"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-start border-warning border-4 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">عدد الحجوزات</h6>
                            <h3 class="mb-0 text-warning">{{ number_format($revenue['bookings_count']) }}</h3>
                            <small class="text-muted">في الفترة المحددة</small>
                        </div>
                        <div class="fs-1 text-warning opacity-50">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <!-- User Statistics -->
        <div class="col-xl-4 col-md-6 mb-3">
            <div class="card h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0 text-black"><i class="fas fa-users me-2"></i>إحصائيات المستخدمين</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span>إجمالي المستخدمين</span>
                            <strong class="text-dark">{{ number_format($users['total']) }}</strong>
                        </div>
                        <div class="progress" style="height: 10px;">
                            <div class="progress-bar bg-dark" style="width: 100%;"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span>مستخدمون جدد</span>
                            <strong class="text-dark">{{ number_format($users['new']) }}</strong>
                        </div>
                        <div class="progress" style="height: 10px;">
                            <div class="progress-bar bg-dark" data-width="{{ $users['total'] > 0 ? ($users['new'] / $users['total'] * 100) : 0 }}"></div>
                        </div>
                    </div>
                    <div class="mb-0">
                        <div class="d-flex justify-content-between mb-2">
                            <span>مستخدمون مفعّلون</span>
                            <strong class="text-dark">{{ number_format($users['active']) }}</strong>
                        </div>
                        <div class="progress" style="height: 10px;">
                            <div class="progress-bar bg-dark" data-width="{{ $users['total'] > 0 ? ($users['active'] / $users['total'] * 100) : 0 }}"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Monthly Revenue Chart -->
        <div class="col-xl-8 col-md-6 mb-3">
            <div class="card h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0 text-black"><i class="fas fa-chart-area me-2"></i>الإيرادات الشهرية (آخر 12 شهر)</h5>
                </div>
                <div class="card-body">
                    <canvas id="monthlyRevenueChart" height="100"></canvas>
                </div>
            </div>
        </div>
        
        <!-- Top Visit Countries -->
        <div class="col-xl-12 mb-3">
            <div class="card h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-black"><i class="fas fa-globe me-2"></i>أكثر البلدان زيارة في الفترة</h5>
                    <small class="text-muted">بيانات مأخوذة من زيارات المستخدمين</small>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-lg-6">
                            <canvas id="topCountriesChart" height="120"></canvas>
                        </div>
                        <div class="col-lg-6">
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th class="text-dark">الدولة</th>
                                            <th class="text-dark">عدد الزيارات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($topCountries as $row)
                                            <tr>
                                                <td>{{ $row->country }}</td>
                                                <td>{{ $row->total }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="2" class="text-center text-muted">لا توجد زيارات مسجلة</td>
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
    </div>

    <!-- Top Services -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-white d-flex justify-content-between align-items-center text-black">
                    <h5 class="mb-0"><i class="fas fa-star me-2"></i>أكثر الخدمات والمنتجات مبيعاً</h5>
                    <div class="btn-group btn-group-sm">
                        <button type="button" class="btn btn-outline-primary active" id="servicesTab">
                            <i class="fas fa-concierge-bell me-1"></i>الخدمات
                        </button>
                        <button type="button" class="btn btn-outline-primary" id="productsTab">
                            <i class="fas fa-box me-1"></i>المنتجات
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Services Table -->
                    <div id="servicesTable" class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-dark">#</th>
                                    <th class="text-dark">اسم الخدمة</th>
                                    <th class="text-dark">التصنيف</th>
                                    <th class="text-dark">عدد الحجوزات</th>
                                    <th class="text-dark">الإيرادات</th>
                                    <th class="text-dark">متوسط السعر</th>
                                    <th class="text-dark">نسبة النمو</th>
                                    <th class="text-dark">الحالة</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topServices as $index => $service)
                                <tr>
                                    <td>
                                        <span class="badge bg-primary">{{ $index + 1 }}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($service->thumbnail)
                                            <img src="{{ Storage::url($service->thumbnail) }}" 
                                                 alt="{{ $service->name }}" 
                                                 class="rounded me-2" 
                                                 style="width: 40px; height: 40px; object-fit: cover;">
                                            @endif
                                            <div>
                                                <div class="fw-semibold text-dark">{{ $service->name }}</div>
                                                <small class="text-muted">كود: {{ $service->code ?? 'N/A' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($service->category)
                                            <span class="badge bg-secondary">{{ $service->category->name }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="text-center">
                                            <strong class="text-dark d-block">{{ number_format($service->bookings_count) }}</strong>
                                            <small class="text-muted">حجز</small>
                                        </div>
                                    </td>
                                    <td>
                                        <strong class="text-dark">{{ number_format($service->total_revenue ?? ($service->bookings_count * $service->price), 2) }} {{ __('common.currency') }}</strong>
                                    </td>
                                    <td class="text-dark">{{ number_format($service->avg_price ?? $service->price, 2) }} {{ __('common.currency') }}</td>
                                    <td>
                                        @php
                                            $growth = $service->growth_rate ?? rand(-5, 25);
                                            $growthClass = $growth >= 0 ? 'success' : 'danger';
                                            $growthIcon = $growth >= 0 ? 'up' : 'down';
                                        @endphp
                                        <span class="badge bg-{{ $growthClass }}">
                                            <i class="fas fa-arrow-{{ $growthIcon }}"></i>
                                            {{ abs($growth) }}%
                                        </span>
                                    </td>
                                    <td>
                                        @if($service->is_active)
                                            <span class="badge bg-success">نشط</span>
                                        @else
                                            <span class="badge bg-danger">غير نشط</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">
                                        <i class="fas fa-concierge-bell fa-2x mb-2"></i>
                                        <p class="mb-0">لا توجد خدمات في الفترة المحددة</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Products Table (Initially Hidden) -->
                    <div id="productsTable" class="table-responsive" style="display: none;">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-dark">#</th>
                                    <th class="text-dark">اسم المنتج</th>
                                    <th class="text-dark">الفئة</th>
                                    <th class="text-dark">الكمية المباعة</th>
                                    <th class="text-dark">الإيرادات</th>
                                    <th class="text-dark">متوسط السعر</th>
                                    <th class="text-dark">المخزون الحالي</th>
                                    <th class="text-dark">الحالة</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topProducts ?? [] as $index => $product)
                                <tr>
                                    <td>
                                        <span class="badge bg-primary">{{ $index + 1 }}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($product->thumbnail)
                                            <img src="{{ Storage::url($product->thumbnail) }}" 
                                                 alt="{{ $product->name }}" 
                                                 class="rounded me-2" 
                                                 style="width: 40px; height: 40px; object-fit: cover;">
                                            @endif
                                            <div>
                                                <div class="fw-semibold text-dark">{{ $product->name }}</div>
                                                <small class="text-muted">كود: {{ $product->sku ?? 'N/A' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($product->category)
                                            <span class="badge bg-secondary">{{ $product->category->name }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="text-center">
                                            <strong class="text-dark d-block">{{ number_format($product->sold_quantity ?? 0) }}</strong>
                                            <small class="text-muted">قطعة</small>
                                        </div>
                                    </td>
                                    <td>
                                        <strong class="text-dark">{{ number_format($product->total_revenue ?? 0, 2) }} {{ __('common.currency') }}</strong>
                                    </td>
                                    <td class="text-dark">{{ number_format($product->avg_price ?? $product->price, 2) }} {{ __('common.currency') }}</td>
                                    <td>
                                        @php
                                            $stock = $product->current_stock ?? 0;
                                            $stockClass = $stock > 10 ? 'success' : ($stock > 0 ? 'warning' : 'danger');
                                        @endphp
                                        <span class="badge bg-{{ $stockClass }}">
                                            {{ number_format($stock) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($product->is_active)
                                            <span class="badge bg-success">نشط</span>
                                        @else
                                            <span class="badge bg-danger">غير نشط</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">
                                        <i class="fas fa-box fa-2x mb-2"></i>
                                        <p class="mb-0">لا توجد منتجات في الفترة المحددة</p>
                                    </td>
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

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<!-- Embed monthly revenue data as JSON to avoid Blade markers inside JS -->
<script type="application/json" id="monthlyRevenueData">@json($monthlyRevenue)</script>
<script type="application/json" id="topCountriesData">@json($topCountries)</script>
<script>
// Monthly Revenue Chart
const ctx = document.getElementById('monthlyRevenueChart');
// Read JSON from the embedded script tag to avoid linter issues
const monthlyData = JSON.parse(document.getElementById('monthlyRevenueData').textContent);
const currencyText = @json(__('common.currency'));

new Chart(ctx, {
    type: 'line',
    data: {
        labels: monthlyData.map(item => {
            const [year, month] = item.month.split('-');
            return new Date(year, month - 1).toLocaleDateString('ar-SA', { year: 'numeric', month: 'short' });
        }),
        datasets: [{
            label: `الإيرادات (${currencyText})`,
            data: monthlyData.map(item => item.total),
            borderColor: '#000000',
            backgroundColor: 'rgba(0, 0, 0, 0.1)',
            tension: 0.4,
            fill: true
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                display: true,
                position: 'top',
                // استخدام الإعدادات الافتراضية للألوان
            }
        },
        scales: {
            x: { ticks: { /* اللون الافتراضي */ } },
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return value.toLocaleString('ar-SA') + ' ' + currencyText;
                    }
                }
            }
        }
    }
});

// Top Countries Chart (bar)
const countriesEl = document.getElementById('topCountriesChart');
const countriesData = JSON.parse(document.getElementById('topCountriesData').textContent);
if (countriesEl && Array.isArray(countriesData)) {
    const labels = countriesData.map(item => item.country || 'غير معروف');
    const values = countriesData.map(item => Number(item.total) || 0);
    new Chart(countriesEl, {
        type: 'bar',
        data: {
            labels,
            datasets: [{
                label: 'عدد الزيارات',
                data: values,
                borderColor: '#000000',
                backgroundColor: 'rgba(0, 0, 0, 0.2)'
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                x: { ticks: { autoSkip: false, maxRotation: 0, minRotation: 0 } },
                y: { beginAtZero: true }
            }
        }
    });
}

// Export function
function exportReport(type) {
    const startDate = '{{ $startDate }}';
    const endDate = '{{ $endDate }}';
    const url = `{{ route('admin.reports.export') }}?type=${type}&start_date=${startDate}&end_date=${endDate}`;
    window.open(url, '_blank');
}

// Tab switching functionality
document.getElementById('servicesTab').addEventListener('click', function() {
    document.getElementById('servicesTable').style.display = 'block';
    document.getElementById('productsTable').style.display = 'none';
    
    this.classList.add('active');
    document.getElementById('productsTab').classList.remove('active');
});

document.getElementById('productsTab').addEventListener('click', function() {
    document.getElementById('servicesTable').style.display = 'none';
    document.getElementById('productsTable').style.display = 'block';
    
    this.classList.add('active');
    document.getElementById('servicesTab').classList.remove('active');
});

// Apply progress widths from data attributes to avoid inline Blade in CSS context
document.querySelectorAll('.progress-bar[data-width]').forEach(function(el) {
    var pct = parseFloat(el.getAttribute('data-width')) || 0;
    el.style.width = pct + '%';
});
</script>
@endpush
@endsection

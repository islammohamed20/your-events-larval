@extends('supplier.layouts.app')

@section('title', 'التقارير')
@section('page-title', 'التقارير والإحصائيات')

@section('content')
<!-- Period Filter -->
<div class="content-card mb-4">
    <div class="p-3">
        <form method="GET" class="d-flex align-items-center gap-3">
            <label class="form-label mb-0 fw-semibold">الفترة:</label>
            <select name="period" class="form-select" style="width: auto;" onchange="this.form.submit()">
                <option value="week" {{ $period == 'week' ? 'selected' : '' }}>آخر أسبوع</option>
                <option value="month" {{ $period == 'month' ? 'selected' : '' }}>آخر شهر</option>
                <option value="quarter" {{ $period == 'quarter' ? 'selected' : '' }}>آخر 3 أشهر</option>
                <option value="year" {{ $period == 'year' ? 'selected' : '' }}>آخر سنة</option>
            </select>
        </form>
    </div>
</div>

<!-- Stats Overview -->
<div class="row g-4 mb-4">
    <div class="col-lg-3 col-md-6">
        <div class="stat-card">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="stat-value">{{ $reports['total_bookings'] }}</div>
                    <div class="stat-label">إجمالي الحجوزات</div>
                </div>
                <div class="stat-icon primary">
                    <i class="fas fa-calendar-alt"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6">
        <div class="stat-card">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="stat-value">{{ $reports['completed_bookings'] }}</div>
                    <div class="stat-label">حجوزات مكتملة</div>
                </div>
                <div class="stat-icon secondary">
                    <i class="fas fa-check-double"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6">
        <div class="stat-card">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="stat-value">{{ number_format($reports['total_revenue'], 0) }}</div>
                    <div class="stat-label">الإيرادات (ر.س)</div>
                </div>
                <div class="stat-icon gold">
                    <i class="fas fa-coins"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6">
        <div class="stat-card">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="stat-value">{{ number_format($reports['average_booking_value'], 0) }}</div>
                    <div class="stat-label">متوسط قيمة الحجز</div>
                </div>
                <div class="stat-icon accent">
                    <i class="fas fa-chart-line"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Booking Status Breakdown -->
    <div class="col-lg-6">
        <div class="content-card h-100">
            <div class="card-header-custom">
                <h5><i class="fas fa-chart-pie me-2"></i>توزيع الحجوزات حسب الحالة</h5>
            </div>
            <div class="p-4">
                <div class="row text-center">
                    <div class="col-6 col-md-3 mb-3">
                        <div class="p-3 rounded-3" style="background: #fff3cd;">
                            <div class="fw-bold fs-3" style="color: #856404;">{{ $reports['total_bookings'] - $reports['confirmed_bookings'] - $reports['completed_bookings'] - $reports['cancelled_bookings'] }}</div>
                            <small class="text-muted">معلقة</small>
                        </div>
                    </div>
                    <div class="col-6 col-md-3 mb-3">
                        <div class="p-3 rounded-3" style="background: #d4edda;">
                            <div class="fw-bold fs-3" style="color: #155724;">{{ $reports['confirmed_bookings'] }}</div>
                            <small class="text-muted">مؤكدة</small>
                        </div>
                    </div>
                    <div class="col-6 col-md-3 mb-3">
                        <div class="p-3 rounded-3" style="background: #cce5ff;">
                            <div class="fw-bold fs-3" style="color: #004085;">{{ $reports['completed_bookings'] }}</div>
                            <small class="text-muted">مكتملة</small>
                        </div>
                    </div>
                    <div class="col-6 col-md-3 mb-3">
                        <div class="p-3 rounded-3" style="background: #f8d7da;">
                            <div class="fw-bold fs-3" style="color: #721c24;">{{ $reports['cancelled_bookings'] }}</div>
                            <small class="text-muted">ملغية</small>
                        </div>
                    </div>
                </div>
                
                <!-- Success Rate -->
                @php
                    $successRate = $reports['total_bookings'] > 0 
                        ? round(($reports['completed_bookings'] / $reports['total_bookings']) * 100) 
                        : 0;
                @endphp
                <div class="mt-4">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="fw-semibold">نسبة الإنجاز</span>
                        <span class="fw-bold text-success">{{ $successRate }}%</span>
                    </div>
                    <div class="progress" style="height: 10px; border-radius: 5px;">
                        <div class="progress-bar bg-success" role="progressbar" style="width: {{ $successRate }}%;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Performance by Service -->
    <div class="col-lg-6">
        <div class="content-card h-100">
            <div class="card-header-custom">
                <h5><i class="fas fa-concierge-bell me-2"></i>أداء الخدمات</h5>
            </div>
            <div class="p-4">
                @if($serviceStats->count() > 0)
                    @foreach($serviceStats->take(5) as $serviceId => $stat)
                        @php
                            $service = \App\Models\Service::find($serviceId);
                        @endphp
                        @if($service)
                        <div class="d-flex align-items-center justify-content-between mb-3 pb-3 border-bottom">
                            <div>
                                <div class="fw-semibold">{{ $service->name }}</div>
                                <small class="text-muted">{{ $stat['count'] }} حجز</small>
                            </div>
                            <div class="text-end">
                                <div class="fw-bold" style="color: #1f144a;">{{ number_format($stat['revenue'], 0) }} ر.س</div>
                                <small class="text-success">إيرادات</small>
                            </div>
                        </div>
                        @endif
                    @endforeach
                @else
                    <div class="text-center py-4 text-muted">
                        <i class="fas fa-chart-bar fa-3x mb-3"></i>
                        <p>لا توجد بيانات للفترة المحددة</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Summary Cards -->
<div class="row g-4 mt-4">
    <div class="col-12">
        <div class="content-card">
            <div class="card-header-custom">
                <h5><i class="fas fa-info-circle me-2"></i>ملخص الأداء</h5>
            </div>
            <div class="p-4">
                <div class="row">
                    <div class="col-md-4">
                        <div class="text-center p-4 rounded-3" style="background: linear-gradient(135deg, rgba(31, 20, 74, 0.05), rgba(61, 42, 122, 0.05));">
                            <i class="fas fa-trophy text-warning mb-2" style="font-size: 2rem;"></i>
                            <h4 class="fw-bold mb-1">{{ $reports['completed_bookings'] }}</h4>
                            <p class="text-muted mb-0">حجز ناجح</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-center p-4 rounded-3" style="background: linear-gradient(135deg, rgba(45, 188, 174, 0.05), rgba(26, 143, 132, 0.05));">
                            <i class="fas fa-wallet text-success mb-2" style="font-size: 2rem;"></i>
                            <h4 class="fw-bold mb-1">{{ number_format($reports['total_revenue'], 0) }} ر.س</h4>
                            <p class="text-muted mb-0">إجمالي الإيرادات</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-center p-4 rounded-3" style="background: linear-gradient(135deg, rgba(239, 72, 112, 0.05), rgba(201, 61, 92, 0.05));">
                            <i class="fas fa-star text-danger mb-2" style="font-size: 2rem;"></i>
                            <h4 class="fw-bold mb-1">{{ $successRate }}%</h4>
                            <p class="text-muted mb-0">نسبة النجاح</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

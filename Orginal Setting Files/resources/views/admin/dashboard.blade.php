@extends('layouts.admin')

@section('title', 'لوحة التحكم - Your Events')
@section('page-title', 'لوحة التحكم الرئيسية')
@section('page-description', 'نظرة عامة على إحصائيات الموقع والأنشطة الحديثة')

@section('content')
<!-- Statistics Cards -->
<div class="row">
    <div class="col-xl-3 col-md-6">
        <div class="stats-card">
            <div class="d-flex align-items-center">
                <div class="flex-grow-1">
                    <h3 class="mb-0">{{ $stats['users'] }}</h3>
                    <p class="mb-0">إجمالي المستخدمين</p>
                </div>
                <div>
                    <i class="fas fa-users fa-2x opacity-75"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6">
        <div class="stats-card">
            <div class="d-flex align-items-center">
                <div class="flex-grow-1">
                    <h3 class="mb-0">{{ $stats['bookings'] }}</h3>
                    <p class="mb-0">إجمالي الحجوزات</p>
                </div>
                <div>
                    <i class="fas fa-calendar-check fa-2x opacity-75"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6">
        <div class="stats-card">
            <div class="d-flex align-items-center">
                <div class="flex-grow-1">
                    <h3 class="mb-0">{{ $stats['packages'] }}</h3>
                    <p class="mb-0">الباقات المتاحة</p>
                </div>
                <div>
                    <i class="fas fa-box fa-2x opacity-75"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6">
        <div class="stats-card">
            <div class="d-flex align-items-center">
                <div class="flex-grow-1">
                    <h3 class="mb-0">{{ $stats['services'] }}</h3>
                    <p class="mb-0">الخدمات المتاحة</p>
                </div>
                <div>
                    <i class="fas fa-cogs fa-2x opacity-75"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Stats Row -->
<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-clock me-2"></i>الحجوزات المعلقة
                </h5>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h2 class="text-warning mb-0">{{ $stats['pending_bookings'] }}</h2>
                        <p class="text-muted mb-0">حجوزات تحتاج للمراجعة</p>
                    </div>
                    <div>
                        <a href="{{ route('admin.bookings.index', ['status' => 'pending']) }}" 
                           class="btn btn-outline-warning">
                            عرض الكل
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-images me-2"></i>المعرض
                </h5>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h2 class="text-info mb-0">{{ $stats['gallery_items'] }}</h2>
                        <p class="text-muted mb-0">صور وفيديوهات</p>
                    </div>
                    <div>
                        <a href="{{ route('admin.gallery.index') }}" 
                           class="btn btn-outline-info">
                            إدارة المعرض
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Bookings -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-list me-2"></i>أحدث الحجوزات
                </h5>
                <a href="{{ route('admin.bookings.index') }}" class="btn btn-sm btn-outline-primary">
                    عرض الكل
                </a>
            </div>
            <div class="card-body">
                @if($recent_bookings->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>رقم الحجز</th>
                                    <th>العميل</th>
                                    <th>الخدمة/الباقة</th>
                                    <th>تاريخ المناسبة</th>
                                    <th>الحالة</th>
                                    <th>إجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recent_bookings as $booking)
                                    <tr>
                                        <td>
                                            <strong>{{ $booking->booking_reference }}</strong>
                                        </td>
                                        <td>
                                            {{ $booking->client_name }}<br>
                                            <small class="text-muted">{{ $booking->client_phone }}</small>
                                        </td>
                                        <td>
                                            @if($booking->package)
                                                <span class="badge bg-primary">باقة</span>
                                                {{ $booking->package->name }}
                                            @elseif($booking->service)
                                                <span class="badge bg-info">خدمة</span>
                                                {{ $booking->service->name }}
                                            @else
                                                <span class="text-muted">غير محدد</span>
                                            @endif
                                        </td>
                                        <td>{{ \Carbon\Carbon::parse($booking->event_date)->format('d/m/Y') }}</td>
                                        <td>
                                            @switch($booking->status)
                                                @case('pending')
                                                    <span class="badge bg-warning">معلق</span>
                                                    @break
                                                @case('confirmed')
                                                    <span class="badge bg-success">مؤكد</span>
                                                    @break
                                                @case('cancelled')
                                                    <span class="badge bg-danger">ملغي</span>
                                                    @break
                                                @case('completed')
                                                    <span class="badge bg-info">مكتمل</span>
                                                    @break
                                            @endswitch
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.bookings.show', $booking->id) }}" 
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                        <h5>لا توجد حجوزات حديثة</h5>
                        <p class="text-muted">ستظهر الحجوزات الجديدة هنا</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-plus me-2"></i>إضافة جديد
                </h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.packages.create') }}" class="btn btn-primary">
                        <i class="fas fa-box me-2"></i>إضافة باقة جديدة
                    </a>
                    <a href="{{ route('admin.services.create') }}" class="btn btn-info">
                        <i class="fas fa-cogs me-2"></i>إضافة خدمة جديدة
                    </a>
                    <a href="{{ route('admin.gallery.create') }}" class="btn btn-success">
                        <i class="fas fa-image me-2"></i>إضافة للمعرض
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-chart-bar me-2"></i>إحصائيات إضافية
                </h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6">
                        <h4 class="text-primary">{{ $stats['reviews'] }}</h4>
                        <p class="text-muted mb-2">التقييمات</p>
                    </div>
                    <div class="col-6">
                        <h4 class="text-warning">{{ $stats['pending_reviews'] }}</h4>
                        <p class="text-muted mb-2">تقييمات معلقة</p>
                    </div>
                </div>
                <hr>
                <div class="text-center">
                    <small class="text-muted">
                        آخر تحديث: {{ now()->format('d/m/Y H:i') }}
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

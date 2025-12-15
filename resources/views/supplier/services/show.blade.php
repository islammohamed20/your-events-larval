@extends('supplier.layouts.app')

@section('title', 'تفاصيل الخدمة')
@section('page-title', 'تفاصيل الخدمة')

@section('content')
<div class="row g-4">
    <div class="col-lg-8">
        <div class="content-card">
            <div class="card-header-custom">
                <h5>معلومات الخدمة</h5>
                <a href="{{ route('supplier.services.index') }}" class="btn btn-light">
                    <i class="fas fa-arrow-right me-1"></i> العودة
                </a>
            </div>
            <div class="p-3">
                <div class="row">
                    <div class="col-md-5 mb-3">
                        @php
                            $imageUrl = $service->thumbnail_url ?? ($service->image ? Storage::url($service->image) : null);
                        @endphp
                        @if($imageUrl)
                            <img src="{{ $imageUrl }}" alt="{{ $service->name }}" class="w-100 rounded" style="height: 220px; object-fit: cover;">
                        @else
                            <div class="w-100 d-flex align-items-center justify-content-center" 
                                 style="height: 220px; background: linear-gradient(135deg, #667eea, #764ba2);">
                                <i class="fas fa-concierge-bell text-white" style="font-size: 3rem;"></i>
                            </div>
                        @endif
                    </div>
                    <div class="col-md-7">
                        <h4 class="fw-bold mb-2">{{ $service->name }}</h4>
                        @if($service->subtitle)
                            <p class="text-muted mb-2">{{ $service->subtitle }}</p>
                        @endif
                        <div class="mb-2">
                            <span class="badge" style="background: rgba(102, 126, 234, 0.1); color: #667eea;">
                                {{ $service->category->name ?? 'غير مصنف' }}
                            </span>
                            @if($service->is_active)
                                <span class="badge status-active ms-2">نشط</span>
                            @else
                                <span class="badge status-inactive ms-2">غير نشط</span>
                            @endif
                        </div>
                        <div class="mt-3">
                            @if($service->isVariable())
                                <div>
                                    <strong>نطاق السعر:</strong>
                                    <span class="ms-2">{{ $service->price_range }}</span>
                                </div>
                            @else
                                <div>
                                    <strong>السعر:</strong>
                                    <span class="ms-2">{{ number_format($service->price ?? 0, 2) }} ر.س</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                @if($service->description)
                <hr>
                <div>
                    <strong>الوصف:</strong>
                    <p class="text-muted mb-0 mt-2">{{ $service->description }}</p>
                </div>
                @endif
            </div>
        </div>

        @if($service->isVariable() && $service->variations->count() > 0)
        <div class="content-card mt-4">
            <div class="card-header-custom">
                <h5>التنويعات</h5>
            </div>
            <div class="p-3">
                <div class="table-responsive">
                    <table class="table table-custom">
                        <thead>
                            <tr>
                                <th>SKU</th>
                                <th>السعر</th>
                                <th>السعر المخفّض</th>
                                <th>المخزون</th>
                                <th>الحالة</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($service->variations as $variation)
                            <tr>
                                <td>{{ $variation->sku ?: '-' }}</td>
                                <td>{{ number_format($variation->price ?? 0, 2) }} ر.س</td>
                                <td>{{ $variation->sale_price ? number_format($variation->sale_price, 2) . ' ر.س' : '-' }}</td>
                                <td>{{ $variation->stock ?? '-' }}</td>
                                <td>
                                    @if($variation->is_active)
                                        <span class="badge status-active">نشط</span>
                                    @else
                                        <span class="badge status-inactive">غير نشط</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif
    </div>

    <div class="col-lg-4">
        <div class="content-card">
            <div class="card-header-custom">
                <h5>آخر 10 حجوزات لهذه الخدمة</h5>
            </div>
            <div class="p-3">
                @if($bookings->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-custom">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>العميل</th>
                                    <th>الحالة</th>
                                    <th>التاريخ</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($bookings as $booking)
                                <tr>
                                    <td>{{ $booking->id }}</td>
                                    <td>{{ $booking->user->name ?? '-' }}</td>
                                    <td>{!! $booking->status_badge !!}</td>
                                    <td>{{ $booking->created_at->format('Y-m-d') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-inbox fa-2x text-muted mb-2"></i>
                        <p class="text-muted mb-0">لا توجد حجوزات حديثة لهذه الخدمة</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

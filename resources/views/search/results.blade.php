@extends('layouts.app')

@section('title', 'نتائج البحث') 'نتائج البحث عن: ' . $query)

@section('content')
<div class="container my-5">
    <!-- Search Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h1 class="mb-0">نتائج البحث</h1>
                <a href="{{ route('home') }}" class="btn btn-outline-primary">
                    <i class="fas fa-home"></i> الرئيسية
                </a>
            </div>
            <div class="search-info bg-light p-3 rounded">
                <p class="mb-0">
                    <i class="fas fa-search text-primary"></i>
                    البحث عن: <strong>"{{ $query }}"</strong>
                    <span class="badge bg-primary ms-2">{{ $total }} نتيجة</span>
                </p>
            </div>
        </div>
    </div>

    @if($results->isEmpty())
        <!-- No Results -->
        <div class="row">
            <div class="col-12">
                <div class="alert alert-info text-center p-5">
                    <i class="fas fa-search fa-3x mb-3 text-muted"></i>
                    <h3>لا توجد نتائج</h3>
                    <p class="mb-4">لم نعثر على أي نتائج تطابق "{{ $query }}"</p>
                    <a href="{{ route('home') }}" class="btn btn-primary">
                        <i class="fas fa-home"></i> العودة للرئيسية
                    </a>
                </div>
            </div>
        </div>
    @else
        <!-- Filter Tabs -->
        <div class="row mb-4">
            <div class="col-12">
                <ul class="nav nav-pills" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link {{ $type === 'all' ? 'active' : '' }}" href="{{ route('search', ['q' => $query, 'type' => 'all']) }}">
                            الكل ({{ $results->count() }})
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $type === 'services' ? 'active' : '' }}" href="{{ route('search', ['q' => $query, 'type' => 'services']) }}">
                            الخدمات ({{ $results->where('result_type', 'service')->count() }})
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $type === 'packages' ? 'active' : '' }}" href="{{ route('search', ['q' => $query, 'type' => 'packages']) }}">
                            الباقات ({{ $results->where('result_type', 'package')->count() }})
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Results Grid -->
        <div class="row g-4">
            @foreach($results as $result)
                @if($result->result_type === 'service')
                    <!-- Service Card -->
                    <div class="col-md-6 col-lg-4">
                        <div class="card h-100 shadow-sm hover-shadow">
                            @if($result->image)
                                <img src="{{ asset('storage/' . $result->image) }}" 
                                     class="card-img-top" 
                                     alt="{{ $result->name }}"
                                     style="height: 200px; object-fit: cover;">
                            @else
                                <div class="bg-gradient-primary d-flex align-items-center justify-content-center" style="height: 200px;">
                                    <i class="fas fa-concierge-bell fa-4x text-white opacity-50"></i>
                                </div>
                            @endif
                            
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <span class="badge bg-primary">خدمة</span>
                                    @if($result->category)
                                        <span class="badge bg-secondary">{{ $result->category->name }}</span>
                                    @endif
                                </div>
                                
                                <h5 class="card-title">{{ $result->name }}</h5>
                                <p class="card-text text-muted small">
                                    {{ Str::limit(strip_tags($result->description), 100) }}
                                </p>
                                
                                <div class="d-flex justify-content-between align-items-center mt-3">
                                    <span class="h5 mb-0 text-primary">
                                        {{ number_format($result->price) }} ر.س
                                    </span>
                                    <a href="{{ route('services.show', $result->id) }}" class="btn btn-outline-primary btn-sm">
                                        عرض التفاصيل <i class="fas fa-arrow-left"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                @elseif($result->result_type === 'package')
                    <!-- Package Card -->
                    <div class="col-md-6 col-lg-4">
                        <div class="card h-100 shadow-sm hover-shadow">
                            @if($result->image)
                                <img src="{{ asset('storage/' . $result->image) }}" 
                                     class="card-img-top" 
                                     alt="{{ $result->name }}"
                                     style="height: 200px; object-fit: cover;">
                            @else
                                <div class="bg-gradient-success d-flex align-items-center justify-content-center" style="height: 200px;">
                                    <i class="fas fa-box fa-4x text-white opacity-50"></i>
                                </div>
                            @endif
                            
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <span class="badge bg-success">باقة</span>
                                    @if($result->discount > 0)
                                        <span class="badge bg-danger">خصم {{ $result->discount }}%</span>
                                    @endif
                                </div>
                                
                                <h5 class="card-title">{{ $result->name }}</h5>
                                <p class="card-text text-muted small">
                                    {{ Str::limit(strip_tags($result->description), 100) }}
                                </p>
                                
                                <div class="d-flex justify-content-between align-items-center mt-3">
                                    <div>
                                        @if($result->discount > 0)
                                            <span class="text-muted text-decoration-line-through small">
                                                {{ number_format($result->price) }} ر.س
                                            </span>
                                            <span class="h5 mb-0 text-success d-block">
                                                {{ number_format($result->price * (1 - $result->discount / 100)) }} ر.س
                                            </span>
                                        @else
                                            <span class="h5 mb-0 text-success">
                                                {{ number_format($result->price) }} ر.س
                                            </span>
                                        @endif
                                    </div>
                                    <a href="{{ route('packages.show', $result->id) }}" class="btn btn-outline-success btn-sm">
                                        عرض التفاصيل <i class="fas fa-arrow-left"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    @endif
</div>

<style>
.hover-shadow {
    transition: all 0.3s ease;
}

.hover-shadow:hover {
    transform: translateY(-5px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
}

.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.bg-gradient-success {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
}

.nav-pills .nav-link {
    color: #495057;
    border-radius: 50rem;
    padding: 0.5rem 1.5rem;
    margin-right: 0.5rem;
}

.nav-pills .nav-link:hover {
    background-color: #e9ecef;
}

.nav-pills .nav-link.active {
    background-color: #0d6efd;
    color: white;
}
</style>
@endsection

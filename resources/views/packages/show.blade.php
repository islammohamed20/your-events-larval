@extends('layouts.app')

@section('title', $package->name . ' - الباقات - Your Events')

@section('content')
<section class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <div class="mb-4" data-aos="fade-right">
                    @if($package->image)
                        <img src="{{ Storage::url($package->image) }}" class="img-fluid rounded" alt="{{ $package->name }}">
                    @else
                        <img src="https://images.unsplash.com/photo-1519167758481-83f550bb49b3?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" 
                             class="img-fluid rounded" alt="{{ $package->name }}">
                    @endif
                </div>
                
                <div data-aos="fade-up">
                    <h1 class="mb-4">{{ $package->name }}</h1>
                    <div class="mb-4">
                        {!! nl2br(e($package->description)) !!}
                    </div>
                    
                    @if($package->features)
                        <div class="card mb-4">
                            <div class="card-body">
                                <h5 class="card-title mb-3">
                                    <i class="fas fa-list-check me-2 text-primary"></i>
                                    مميزات الباقة
                                </h5>
                                <div class="row">
                                    @foreach($package->features as $feature)
                                        <div class="col-md-6 mb-2">
                                            <i class="fas fa-check text-primary me-2"></i>{{ $feature }}
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="card sticky-top" style="top: 100px;" data-aos="fade-left">
                    <div class="card-body text-center">
                        <h5 class="card-title mb-3">{{ $package->name }}</h5>
                        <div class="price-tag mb-4" style="font-size: 2rem; display: inline-block;">
                            {{ number_format($package->price) }} ر.س
                        </div>
                        <p class="text-muted mb-4">
                            احجز هذه الباقة الآن واحصل على خدمة مميزة لمناسبتك.
                        </p>
                        <a href="{{ route('booking.create', ['package_id' => $package->id]) }}" 
                           class="btn btn-primary btn-lg w-100 mb-3">
                            <i class="fas fa-calendar-check me-2"></i>احجز الباقة
                        </a>
                        <a href="{{ route('contact') }}" class="btn btn-outline-primary w-100">
                            <i class="fas fa-question-circle me-2"></i>استفسار عن الباقة
                        </a>
                    </div>
                </div>
                
                <div class="card mt-4" data-aos="fade-left" data-aos-delay="100">
                    <div class="card-body">
                        <h6 class="card-title mb-3">
                            <i class="fas fa-info-circle me-2 text-primary"></i>
                            معلومات مهمة
                        </h6>
                        <ul class="list-unstyled mb-0 small">
                            <li class="mb-2">
                                <i class="fas fa-clock text-muted me-2"></i>
                                مدة التنفيذ: حسب حجم المناسبة
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-calendar text-muted me-2"></i>
                                يُنصح بالحجز قبل أسبوعين
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-phone text-muted me-2"></i>
                                استشارة مجانية قبل الحجز
                            </li>
                            <li>
                                <i class="fas fa-percent text-muted me-2"></i>
                                خصومات للمناسبات الكبيرة
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="text-center mt-5" data-aos="fade-up">
            <a href="{{ route('packages.index') }}" class="btn btn-outline-primary">
                <i class="fas fa-arrow-right me-2"></i>العودة إلى الباقات
            </a>
        </div>
    </div>
</section>

<style>
.card.sticky-top {
    top: 100px !important;
}

.card-body {
    position: relative;
    overflow: visible;
    padding: 1.5rem;
}

.card {
    border: none;
    box-shadow: 0 0.125rem 0.5rem rgba(0, 0, 0, 0.075);
    border-radius: 0.75rem;
    overflow: visible;
}

.card-header {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-bottom: 1px solid #dee2e6;
}

.price-tag {
    color: var(--primary-color, #1f144a);
    font-weight: 700;
    background: linear-gradient(135deg, rgba(31, 20, 74, 0.05) 0%, rgba(45, 188, 174, 0.05) 100%);
    padding: 1rem 2rem;
    border-radius: 0.5rem;
    border: 2px solid rgba(31, 20, 74, 0.1);
}

.btn-lg {
    padding: 0.75rem 1.5rem;
    font-size: 1.1rem;
}
</style>
@endsection

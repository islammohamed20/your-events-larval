@extends('layouts.app')

@section('title', $package->name)

@section('content')
<section class="py-5" style="margin-top: 80px;">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <div class="mb-4" data-aos="fade-right">
                    @if($package->images->count() > 0)
                        <!-- معرض صور متعدد -->
                        <div id="packageImageCarousel" class="carousel slide" data-bs-ride="carousel">
                            <div class="carousel-indicators">
                                @foreach($package->images as $index => $img)
                                    <button type="button" 
                                            data-bs-target="#packageImageCarousel" 
                                            data-bs-slide-to="{{ $index }}" 
                                            class="{{ $index === 0 ? 'active' : '' }}"
                                            aria-current="{{ $index === 0 ? 'true' : 'false' }}"
                                            aria-label="صورة {{ $index + 1 }}"></button>
                                @endforeach
                            </div>
                            <div class="carousel-inner rounded">
                                @foreach($package->images as $index => $img)
                                    <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                                        <img src="{{ $img->image_url }}" 
                                             class="d-block w-100" 
                                             alt="{{ $img->alt_text ?? $package->name }}"
                                             style="height: 400px; object-fit: cover;">
                                    </div>
                                @endforeach
                            </div>
                            @if($package->images->count() > 1)
                                <button class="carousel-control-prev" type="button" data-bs-target="#packageImageCarousel" data-bs-slide="prev">
                                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                    <span class="visually-hidden">السابق</span>
                                </button>
                                <button class="carousel-control-next" type="button" data-bs-target="#packageImageCarousel" data-bs-slide="next">
                                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                    <span class="visually-hidden">التالي</span>
                                </button>
                            @endif
                        </div>
                        
                        <!-- صور مصغرة -->
                        @if($package->images->count() > 1)
                            <div class="row mt-3 g-2">
                                @foreach($package->images as $index => $img)
                                    <div class="col-3">
                                        <img src="{{ $img->image_url }}" 
                                             class="img-thumbnail thumbnail-img {{ $index === 0 ? 'active' : '' }}" 
                                             alt="{{ $img->alt_text ?? $package->name }}"
                                             style="height: 80px; object-fit: cover; cursor: pointer; width: 100%;"
                                             data-bs-target="#packageImageCarousel" 
                                             data-bs-slide-to="{{ $index }}">
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    @elseif($package->image)
                        <img src="{{ Storage::url($package->image) }}" class="img-fluid rounded" alt="{{ $package->name }}">
                    @else
                        <img src="https://images.unsplash.com/photo-1519167758481-83f550bb49b3?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" 
                             class="img-fluid rounded" alt="{{ $package->name }}">
                    @endif
                </div>
                
                <div data-aos="fade-up">
                    <h1 class="mb-4">{{ $package->name }}</h1>
                    
                    @if($package->persons_min || $package->persons_max)
                        <div class="alert alert-info mb-4">
                            <i class="fas fa-users me-2"></i>
                            <strong>عدد الأشخاص:</strong>
                            @if($package->persons_min && $package->persons_max)
                                من {{ $package->persons_min }} إلى {{ $package->persons_max }} شخص
                            @elseif($package->persons_min)
                                من {{ $package->persons_min }} شخص
                            @elseif($package->persons_max)
                                حتى {{ $package->persons_max }} شخص
                            @endif
                        </div>
                    @endif
                    
                    <div class="mb-4">
                        {!! nl2br(e($package->description)) !!}
                    </div>
                    
                    @php
                        $validFeatures = collect($package->features ?? [])->filter(function($feature) {
                            return !empty(trim($feature));
                        });
                    @endphp
                    
                    @if($validFeatures->count() > 0)
                        <div class="card mb-4">
                            <div class="card-body">
                                <h5 class="card-title mb-3">
                                    <i class="fas fa-list-check me-2 text-primary"></i>
                                    مميزات الباقة
                                </h5>
                                <div class="row">
                                    @foreach($validFeatures as $feature)
                                        <div class="col-md-6 mb-2">
                                            <i class="fas fa-check text-primary me-2"></i>{{ $feature }}
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif
                    
                    @php
                        $visibleAttributes = collect($package->attributes ?? [])->filter(function($attr) {
                            return isset($attr['visible']) ? $attr['visible'] : true;
                        });
                    @endphp
                    
                    @if($visibleAttributes->count() > 0)
                        <div class="card mb-4">
                            <div class="card-body">
                                <h5 class="card-title mb-3">
                                    <i class="fas fa-list-alt me-2 text-primary"></i>
                                    خواص الباقة
                                </h5>
                                <div class="row">
                                    @foreach($visibleAttributes as $attr)
                                        <div class="col-md-6 mb-3">
                                            <div class="card h-100 border-0 bg-light">
                                                <div class="card-body">
                                                    <h6 class="card-title text-primary mb-2">
                                                        <i class="fas fa-cog me-1"></i>
                                                        {{ $attr['name'] }}
                                                    </h6>
                                                    @if(!empty($attr['description']))
                                                        <p class="card-text small text-muted mb-1">{{ $attr['description'] }}</p>
                                                    @endif
                                                    @if(!empty($attr['details']))
                                                        <p class="card-text small">{{ $attr['details'] }}</p>
                                                    @endif
                                                </div>
                                            </div>
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
    top: 120px !important;
    z-index: 100;
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

/* Fix for content below sticky card */
.col-lg-8 {
    padding-top: 20px;
}

/* Carousel Styles */
.carousel-inner {
    border-radius: 0.75rem;
    overflow: hidden;
}

.carousel-control-prev-icon,
.carousel-control-next-icon {
    background-color: rgba(0, 0, 0, 0.5);
    border-radius: 50%;
    padding: 20px;
}

.thumbnail-img {
    opacity: 0.6;
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

.thumbnail-img:hover,
.thumbnail-img.active {
    opacity: 1;
    border-color: var(--primary-color, #1f144a);
}

.carousel-indicators button {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    background-color: rgba(255, 255, 255, 0.8);
}

.carousel-indicators button.active {
    background-color: var(--primary-color, #1f144a);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Update thumbnail active state when carousel slides
    const carousel = document.getElementById('packageImageCarousel');
    if (carousel) {
        carousel.addEventListener('slide.bs.carousel', function (e) {
            document.querySelectorAll('.thumbnail-img').forEach((img, index) => {
                img.classList.toggle('active', index === e.to);
            });
        });
        
        // Click on thumbnail to change slide
        document.querySelectorAll('.thumbnail-img').forEach(img => {
            img.addEventListener('click', function() {
                document.querySelectorAll('.thumbnail-img').forEach(i => i.classList.remove('active'));
                this.classList.add('active');
            });
        });
    }
});
</script>
@endsection

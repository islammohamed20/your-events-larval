@extends('layouts.app')

@section('title', __('common.searching_for') . ': ' . $query)

@section('content')
<!-- Page Header -->
<section class="hero-section" style="padding: 47px 0; background: var(--gradient-primary);">
    <div class="container">
        <div class="text-center">
            <h1 class="display-5 fw-bold mb-3 text-white">{{ __('common.results') }}</h1>
            <p class="lead text-white-50">
                {{ __('common.searching_for') }}: "<strong>{{ $query }}</strong>" 
                <span class="badge bg-light text-dark ms-2">{{ $total }} {{ __('common.result') }}</span>
            </p>
        </div>
    </div>
</section>

<!-- Results Section -->
<section class="py-5">
    <div class="container">
        @if($results->isEmpty())
            <!-- No Results -->
            <div class="row justify-content-center">
                <div class="col-md-6 text-center py-5">
                    <i class="fas fa-search fa-4x text-muted mb-4"></i>
                    <h3>{{ __('common.no_results') }}</h3>
                    <p class="text-muted mb-4">{{ __('common.no_results_for', ['query' => $query]) }}</p>
                    <div class="d-flex justify-content-center gap-3">
                        <a href="{{ route('home') }}" class="btn btn-outline-primary">
                            <i class="fas fa-home me-2"></i>{{ __('nav.home') }}
                        </a>
                        <a href="{{ route('services.index') }}" class="btn btn-primary">
                            <i class="fas fa-th-large me-2"></i>{{ __('nav.services') }}
                        </a>
                    </div>
                </div>
            </div>
        @else
            <!-- Filter Tabs -->
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
                <ul class="nav nav-pills" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link {{ $type === 'all' ? 'active' : '' }}" 
                           href="{{ route('search', ['q' => $query, 'type' => 'all']) }}">
                            {{ __('common.all') }} ({{ $results->count() }})
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $type === 'services' ? 'active' : '' }}" 
                           href="{{ route('search', ['q' => $query, 'type' => 'services']) }}">
                            {{ __('nav.services') }} ({{ $results->where('result_type', 'service')->count() }})
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $type === 'packages' ? 'active' : '' }}" 
                           href="{{ route('search', ['q' => $query, 'type' => 'packages']) }}">
                            {{ __('common.packages') }} ({{ $results->where('result_type', 'package')->count() }})
                        </a>
                    </li>
                </ul>
                <a href="{{ route('services.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-th-large me-2"></i>{{ __('common.all_services') }}
                </a>
            </div>

            <!-- Results Grid (Same as Services Page) -->
            <div class="products grid-column mobile-grid-2 column-3">
                @foreach($results as $result)
                    @if($result->result_type === 'service')
                        <!-- Service Card - Same as Services Index -->
                        <div class="service-item mb-4" data-aos="fade-up" data-aos-delay="{{ $loop->index * 50 }}">
                            <div class="card h-100 service-card">
                                <!-- Service Image -->
                                <div class="position-relative service-image-wrapper">
                                    <a href="{{ route('services.show', $result->id) }}" class="d-block" style="text-decoration: none;">
                                        <img src="{{ $result->thumbnail_url }}" 
                                             class="card-img-top service-image" 
                                             alt="{{ $result->name }}"
                                             style="object-fit: cover; cursor: pointer;">
                                    </a>
                                    
                                    <!-- Wishlist Button -->
                                    @auth
                                        <button type="button" 
                                                class="btn btn-link wishlist-toggle-btn position-absolute top-0 end-0 m-2" 
                                                data-service-id="{{ $result->id }}"
                                                style="z-index: 10; background: rgba(255,255,255,0.95); border-radius: 50%; width: 40px; height: 40px; padding: 0; box-shadow: 0 2px 10px rgba(0,0,0,0.2);">
                                            <i class="fas fa-heart {{ auth()->user()->hasInWishlist($result->id) ? 'text-danger' : 'text-muted' }}" 
                                               style="font-size: 1.2rem;"></i>
                                        </button>
                                    @else
                                        <a href="{{ route('login') }}?redirect={{ urlencode(request()->fullUrl()) }}"
                                           class="btn btn-link wishlist-login-btn position-absolute top-0 end-0 m-2"
                                           style="z-index: 10; background: rgba(255,255,255,0.95); border-radius: 50%; width: 40px; height: 40px; padding: 0; box-shadow: 0 2px 10px rgba(0,0,0,0.2); display: inline-flex; align-items: center; justify-content: center; text-decoration: none;">
                                            <i class="fas fa-heart text-muted" style="font-size: 1.2rem;"></i>
                                        </a>
                                    @endauth

                                    <!-- Badge -->
                                    <span class="badge bg-primary position-absolute top-0 start-0 m-2">
                                        {{ __('common.service') }}
                                    </span>
                                </div>
                                
                                <div class="card-body d-flex flex-column">
                                    <!-- Service Name -->
                                    <h5 class="card-title mb-2">
                                        <a href="{{ route('services.show', $result->id) }}" class="text-decoration-none text-dark service-title-link">
                                            {{ $result->name }}
                                        </a>
                                    </h5>
                                    
                                    <!-- Category -->
                                    @if($result->category)
                                        <small class="text-muted mb-2">
                                            <i class="fas fa-tag me-1"></i>{{ $result->category->name }}
                                        </small>
                                    @endif
                                    
                                    <!-- Service Price -->
                                    @if($result->price)
                                        <div class="mb-3">
                                            <span class="h5 text-primary mb-0">{{ number_format($result->price) }} {{ __('common.currency') }}</span>
                                            @if($result->duration)
                                                <small class="text-muted d-block">{{ $result->duration }}</small>
                                            @endif
                                        </div>
                                    @endif
                                    
                                    <!-- Service Description -->
                                    <p class="text-muted small">
                                        {{ Str::limit(strip_tags($result->description), 80) }}
                                    </p>
                                    
                                    <!-- Actions -->
                                    <div class="mt-auto">
                                        @if($result->isVariable())
                                            <a href="{{ route('services.show', $result->id) }}" 
                                               class="btn btn-primary w-100">
                                                <i class="fas fa-sliders-h me-2"></i>
                                                {{ __('common.choose_options') }}
                                            </a>
                                        @else
                                            <button type="button" 
                                                    class="btn btn-primary w-100 add-to-cart-btn" 
                                                    data-service-id="{{ $result->id }}"
                                                    data-service-name="{{ $result->name }}"
                                                    data-service-price="{{ $result->price }}">
                                                <i class="fas fa-cart-plus me-2"></i>
                                                {{ __('common.add_to_cart') }}
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @elseif($result->result_type === 'package')
                        <!-- Package Card -->
                        <div class="service-item mb-4" data-aos="fade-up" data-aos-delay="{{ $loop->index * 50 }}">
                            <div class="card h-100 service-card">
                                <!-- Package Image -->
                                <div class="position-relative service-image-wrapper">
                                    <a href="{{ route('packages.show', $result->id) }}" class="d-block" style="text-decoration: none;">
                                        @if($result->image)
                                            <img src="{{ asset('storage/' . $result->image) }}" 
                                                 class="card-img-top service-image" 
                                                 alt="{{ $result->name }}"
                                                 style="object-fit: cover; cursor: pointer;">
                                        @else
                                            <div class="bg-gradient-success d-flex align-items-center justify-content-center service-image" style="cursor: pointer;">
                                                <i class="fas fa-box fa-4x text-white opacity-50"></i>
                                            </div>
                                        @endif
                                    </a>

                                    <!-- Badge -->
                                    <span class="badge bg-success position-absolute top-0 start-0 m-2">
                                        {{ __('common.package') }}
                                    </span>
                                    
                                    @if($result->discount > 0)
                                        <span class="badge bg-danger position-absolute top-0 end-0 m-2">
                                            {{ __('common.discount') }} {{ $result->discount }}%
                                        </span>
                                    @endif
                                </div>
                                
                                <div class="card-body d-flex flex-column">
                                    <!-- Package Name -->
                                    <h5 class="card-title mb-2">
                                        <a href="{{ route('packages.show', $result->id) }}" class="text-decoration-none text-dark service-title-link">
                                            {{ $result->name }}
                                        </a>
                                    </h5>
                                    
                                    <!-- Package Price -->
                                    <div class="mb-3">
                                        @if($result->discount > 0)
                                            <span class="text-muted text-decoration-line-through small">
                                                {{ number_format($result->price) }} {{ __('common.currency') }}
                                            </span>
                                            <span class="h5 text-success mb-0 d-block">
                                                {{ number_format($result->price * (1 - $result->discount / 100)) }} {{ __('common.currency') }}
                                            </span>
                                        @else
                                            <span class="h5 text-success mb-0">
                                                {{ number_format($result->price) }} {{ __('common.currency') }}
                                            </span>
                                        @endif
                                    </div>
                                    
                                    <!-- Package Description -->
                                    <p class="text-muted small">
                                        {{ Str::limit(strip_tags($result->description), 80) }}
                                    </p>
                                    
                                    <!-- Actions -->
                                    <div class="mt-auto">
                                        <a href="{{ route('packages.show', $result->id) }}" class="btn btn-success w-100">
                                            <i class="fas fa-eye me-2"></i>
                                            {{ __('common.view_details') }}
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
</section>

@push('styles')
<style>
/* Grid Layout - Same as Services Page */
.products.grid-column {
    display: grid;
    gap: 20px;
}

.products.grid-column.column-3 {
    grid-template-columns: repeat(3, 1fr);
}

/* Mobile Grid (2 columns) */
@media (max-width: 767px) {
    .products.grid-column.mobile-grid-2 {
        grid-template-columns: repeat(2, 1fr);
        gap: 15px;
    }
}

/* Tablet Grid (2 columns) */
@media (min-width: 768px) and (max-width: 991px) {
    .products.grid-column.column-3 {
        grid-template-columns: repeat(2, 1fr);
    }
}

/* Service Card Styles */
.service-card {
    border: none;
    border-radius: 12px;
    overflow: hidden;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.service-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.15);
}

.service-card .service-image {
    width: 100%;
    height: 250px;
    object-fit: cover;
    display: block;
    transition: transform 0.3s ease;
}

.service-card:hover .service-image {
    transform: scale(1.05);
}

@media (max-width: 768px) {
    .service-card .service-image {
        height: 180px;
    }
}

.service-title-link:hover {
    color: var(--primary-color) !important;
}

/* Filter Tabs */
.nav-pills .nav-link {
    color: var(--text-color);
    border-radius: 50rem;
    padding: 0.5rem 1.25rem;
    margin-inline-end: 0.5rem;
    transition: all 0.3s ease;
}

.nav-pills .nav-link:hover {
    background-color: rgba(31, 20, 74, 0.1);
}

.nav-pills .nav-link.active {
    background: var(--gradient-accent);
    color: white;
}

/* Gradient Backgrounds */
.bg-gradient-success {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
}
</style>
@endpush

@push('scripts')
<div id="search-translations"
     style="display: none;"
     data-adding-to-cart="{{ __('common.adding_to_cart') }}"
     data-added-to-cart="{{ __('common.added_to_cart') }}"
     data-error-occurred="{{ __('common.error_occurred') }}"
     data-try-again="{{ __('common.try_again') }}">
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const translationsEl = document.getElementById('search-translations');
    const addingToCartText = translationsEl ? (translationsEl.dataset.addingToCart || '') : '';
    const addedToCartText = translationsEl ? (translationsEl.dataset.addedToCart || '') : '';
    const errorOccurredText = translationsEl ? (translationsEl.dataset.errorOccurred || '') : '';
    const tryAgainText = translationsEl ? (translationsEl.dataset.tryAgain || '') : '';
    // Add to cart functionality
    const addToCartButtons = document.querySelectorAll('.add-to-cart-btn');
    
    addToCartButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const serviceId = this.dataset.serviceId;
            const serviceName = this.dataset.serviceName;
            const originalHtml = this.innerHTML;
            
            this.classList.add('loading');
            this.innerHTML = `<i class="fas fa-spinner fa-spin me-2"></i>${addingToCartText}`;
            
            fetch(`/cart/add/${serviceId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ quantity: 1 })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (typeof window.updateCartDropdown === 'function') {
                        window.updateCartDropdown();
                    }
                    
                    this.classList.remove('loading');
                    this.classList.add('btn-success');
                    this.innerHTML = `<i class="fas fa-check me-2"></i>${addedToCartText}`;
                    
                    setTimeout(() => {
                        this.classList.remove('btn-success');
                        this.classList.add('btn-primary');
                        this.innerHTML = originalHtml;
                    }, 2000);
                } else {
                    throw new Error(data.message || errorOccurredText);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                this.classList.remove('loading');
                this.innerHTML = originalHtml;
                alert(error.message || `${errorOccurredText}. ${tryAgainText}`);
            });
        });
    });
    
    // Wishlist toggle functionality
    document.querySelectorAll('.wishlist-toggle-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const serviceId = this.dataset.serviceId;
            const icon = this.querySelector('.fa-heart');
            
            fetch('/wishlist/toggle', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ service_id: serviceId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (data.action === 'added') {
                        icon.classList.remove('text-muted');
                        icon.classList.add('text-danger');
                    } else {
                        icon.classList.remove('text-danger');
                        icon.classList.add('text-muted');
                    }
                }
            })
            .catch(error => console.error('Error:', error));
        });
    });
});
</script>
@endpush
@endsection

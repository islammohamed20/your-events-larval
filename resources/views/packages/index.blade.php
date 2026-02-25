@extends('layouts.app')

@section('title', __('common.packages') . ' - Your Events')

@section('content')
<!-- Page Header -->
<section class="hero-section packages-hero" data-bg-url="{{ Storage::url('extra/packetge-banner.jpg') }}" style="padding: 0; background-position: center; background-size: cover; background-repeat: no-repeat; min-height: 205px; display: flex; align-items: center;">
</section>

<!-- Packages Grid -->
<section class="py-4">
    <div class="container">
        <div class="row">
            @forelse($packages as $package)
                <div class="col-6 col-md-6 col-lg-4 mb-4" data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
                    <div class="card h-100 package-card">
                        <div class="package-image-wrapper">
                            <img src="{{ $package->thumbnail_url }}" 
                                 class="card-img-top package-image" 
                                 alt="{{ $package->name }}">
                        </div>
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title">{{ $package->name }}</h5>
                        
                            
                            @php
                                $validFeatures = collect($package->features ?? [])->filter(function($feature) {
                                    return !empty(trim($feature));
                                });
                            @endphp
                            @if($validFeatures->count() > 0)
                                <h6 class="mt-3 mb-2">{{ __('common.features') }}:</h6>
                                <ul class="list-unstyled mb-3">
                                    @foreach($validFeatures as $feature)
                                        <li class="mb-1">
                                            <i class="fas fa-check text-primary me-2"></i>{{ $feature }}
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                            
                            <div class="d-flex justify-content-between align-items-center mt-auto">
                                <span class="price-tag">{{ number_format($package->price) }} {{ __('common.currency') }}</span>
                                <div>
                                    <a href="{{ route('packages.show', $package->id) }}" class="btn btn-outline-primary btn-sm me-2">
                                        {{ __('common.details') }}
                                    </a>
                                    <a href="{{ route('booking.create', ['package_id' => $package->id]) }}" class="btn btn-primary btn-sm">
                                        {{ __('buttons.book_now') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="text-center py-5">
                        <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                        <h4>{{ __('common.no_packages_available') }}</h4>
                        <p class="text-muted">{{ __('common.more_packages_soon') }}</p>
                        <a href="{{ route('contact') }}" class="btn btn-primary mt-3">
                            {{ __('common.contact_for_custom_offer') }}
                        </a>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</section>

<style>
.package-card .package-image {
    width: 100%;
    height: 200px;
    object-fit: cover;
    display: block;
}

@media (max-width: 768px) {
    .package-card .package-image-wrapper {
        width: 100%;
        aspect-ratio: 1 / 1;
    }
    .package-card .package-image {
        width: 100% !important;
        height: 100% !important;
        object-fit: cover !important;
    }
    .hero-section {
        min-height: 85px !important;
    }
}
</style>

<!-- Package Benefits -->
<section class="py-4 bg-secondary-custom">
    <div class="container">
        <h2 class="section-title" data-aos="fade-up" style="margin-bottom: 2rem; background: none; -webkit-text-fill-color: #000000; color: #000000;">{{ __('common.our_package_benefits') }}</h2>
        <div class="row">
            <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="100">
                <div class="text-center">
                    <div class="mb-3">
                        <i class="fas fa-gem fa-3x" style="color: var(--primary-color);"></i>
                    </div>
                    <h5>{{ __('common.benefit_high_quality_title') }}</h5>
                    <p class="text-muted">{{ __('common.benefit_high_quality_desc') }}</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="200">
                <div class="text-center">
                    <div class="mb-3">
                        <i class="fas fa-palette fa-3x" style="color: var(--primary-color);"></i>
                    </div>
                    <h5>{{ __('common.benefit_creative_design_title') }}</h5>
                    <p class="text-muted">{{ __('common.benefit_creative_design_desc') }}</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="300">
                <div class="text-center">
                    <div class="mb-3">
                        <i class="fas fa-handshake fa-3x" style="color: var(--primary-color);"></i>
                    </div>
                    <h5>{{ __('common.benefit_all_in_one_title') }}</h5>
                    <p class="text-muted">{{ __('common.benefit_all_in_one_desc') }}</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="400">
                <div class="text-center">
                    <div class="mb-3">
                        <i class="fas fa-money-bill-wave fa-3x" style="color: var(--primary-color);"></i>
                    </div>
                    <h5>{{ __('common.benefit_competitive_prices_title') }}</h5>
                    <p class="text-muted">{{ __('common.benefit_competitive_prices_desc') }}</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="500">
                <div class="text-center">
                    <div class="mb-3">
                        <i class="fas fa-headset fa-3x" style="color: var(--primary-color);"></i>
                    </div>
                    <h5>{{ __('common.benefit_support_title') }}</h5>
                    <p class="text-muted">{{ __('common.benefit_support_desc') }}</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="600">
                <div class="text-center">
                    <div class="mb-3">
                        <i class="fas fa-shield-alt fa-3x" style="color: var(--primary-color);"></i>
                    </div>
                    <h5>{{ __('common.benefit_quality_assurance_title') }}</h5>
                    <p class="text-muted">{{ __('common.benefit_quality_assurance_desc') }}</p>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const hero = document.querySelector('.packages-hero');
    if (!hero || !hero.dataset.bgUrl) {
        return;
    }
    hero.style.backgroundImage = `url('${hero.dataset.bgUrl}')`;
});
</script>
@endpush

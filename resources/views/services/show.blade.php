@extends('layouts.app')

@section('title', $service->name)

@if($service->meta_description)
@section('meta')
    <meta name="description" content="{{ $service->meta_description }}">
@endsection
@endif

@section('content')
<section class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <!-- معرض الصور -->
                @if($service->images->count() > 0)
                <div class="mb-4" data-aos="fade-right">
                    <!-- الصورة الرئيسية -->
                    <div id="mainImageContainer" class="mb-3">
                        @php
                            $mainImage = $service->thumbnailImage ?? $service->images->first();
                        @endphp
                        <img id="mainImage" 
                             src="{{ $mainImage->image_url }}" 
                             class="img-fluid rounded shadow service-main-image service-image" 
                             alt="{{ $service->name }}"
                             style="width: 100%; height: 500px; object-fit: cover; cursor: pointer;">
                    </div>
                    
                    <!-- صور مصغرة -->
                    @if($service->images->count() > 1)
                    <div class="row g-2" id="thumbnailGallery">
                        @foreach($service->images as $img)
                        <div class="col-3 col-md-2">
                            <img src="{{ $img->image_url }}" 
                                 class="img-thumbnail thumbnail-img" 
                                 alt="{{ $service->name }}"
                                 style="width: 100%; height: 80px; object-fit: cover; cursor: pointer;"
                                 onclick="changeMainImage('{{ $img->image_url }}')">
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>
                @elseif($service->image)
                <div class="mb-4" data-aos="fade-right">
                    <img src="{{ Storage::url($service->image) }}" class="img-fluid rounded shadow service-main-image service-image" alt="{{ $service->name }}" style="width: 100%; height: 500px; object-fit: cover;">
                </div>
                @else
                <div class="mb-4" data-aos="fade-right">
                    <img src="https://images.unsplash.com/photo-1492684223066-81342ee5ff30?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" 
                         class="img-fluid rounded shadow service-main-image service-image" alt="{{ $service->name }}" style="width: 100%; height: 500px; object-fit: cover;">
                </div>
                @endif
                
                <div data-aos="fade-up">
                    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
                        <h1 class="mb-0">{{ $service->name }}</h1>
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            @if($service->type)
                            <span class="badge bg-primary fs-6 px-3 py-2">
                                <i ></i>{{ $service->type }}
                            </span>
                            @endif
                            @if($service->suppliers && $service->suppliers->count() === 0)
                            <span class="badge bg-danger fs-6 px-3 py-2">
                                غير متوفرة حالياً
                            </span>
                            @endif
                        </div>
                    </div>
                    
                    <div class="row mb-4">
                        @if($service->duration)
                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-clock text-primary me-2"></i>
                                <div>
                                    <small class="text-muted d-block">{{ __('common.duration') }}</small>
                                    <strong>{{ $service->duration }}</strong>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                    
                    <ul class="nav nav-tabs mb-4" id="serviceTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="details-tab" data-bs-toggle="tab" data-bs-target="#service-details" type="button" role="tab">
                                التفاصيل
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="suppliers-tab" data-bs-toggle="tab" data-bs-target="#service-suppliers" type="button" role="tab">
                                الموردون
                            </button>
                        </li>
                    </ul>
                    
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="service-details" role="tabpanel" aria-labelledby="details-tab">
                            @if($service->description || $service->marketing_description)
                            <div class="mb-4">
                                <h5 class="mb-3">
                                    <i class="fas fa-align-left text-primary me-2"></i>&nbsp;{{ __('common.description') }}
                                </h5>
                                <div class="text-muted" style="line-height: 1.8;">
                                    {!! nl2br(e($service->description ?: $service->marketing_description)) !!}
                                </div>
                            </div>
                            @endif
                            
                            @if($service->what_we_offer)
                            <div class="mb-4">
                                <h5 class="mb-3">
                                    <i class="fas fa-gift text-primary me-2"></i>
                                    @php
                                        $isGiftsCategory = optional($service->category)->name === 'الهدايا';
                                    @endphp
                                    {{ $isGiftsCategory ? __('common.top_features_question') : __('common.what_we_offer_question') }}
                                </h5>
                                <div class="card border-0 shadow-sm">
                                    <div class="card-body">
                                        @php 
                                            $offerLines = array_values(array_filter(preg_split("/\r\n|\r|\n/", (string)$service->what_we_offer), function($l){ return trim($l) !== ''; }));
                                        @endphp
                                        @if(count($offerLines) > 0)
                                            <ul class="list-unstyled m-0" style="line-height: 1.9;">
                                                @foreach($offerLines as $line)
                                                    <li class="mb-2 d-flex align-items-start">
                                                        <span class="text-success me-2" aria-hidden="true">✔</span>
                                                        <span>{{ $line }}</span>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @else
                                            <div class="text-muted" style="line-height: 1.8;">{!! nl2br(e($service->what_we_offer)) !!}</div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endif
                            
                            @if($service->why_choose_us)
                            <div class="mb-4">
                                <h5 class="mb-3">
                                    <i class="fas fa-star text-primary me-2"></i>{{ __('common.why_choose_your_events') }}
                                </h5>
                                <div class="card border-0 shadow-sm bg-light">
                                    <div class="card-body">
                                        @php 
                                            $whyLines = array_values(array_filter(preg_split("/\r\n|\r|\n/", (string)$service->why_choose_us), function($l){ return trim($l) !== ''; }));
                                        @endphp
                                        @if(count($whyLines) > 0)
                                            <ul class="list-unstyled m-0" style="line-height: 1.9;">
                                                @foreach($whyLines as $line)
                                                    <li class="mb-2 d-flex align-items-start">
                                                        <span class="text-primary me-2" aria-hidden="true">★</span>
                                                        <span>{{ $line }}</span>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @else
                                            <div class="text-muted" style="line-height: 1.8;">{!! nl2br(e($service->why_choose_us)) !!}</div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endif
                            
                            @if($service->features && count($service->features) > 0)
                            <div class="mb-4">
                                <h5>{{ __('common.service_features') }}</h5>
                                <ul class="list-unstyled">
                                    @foreach($service->features as $feature)
                                        <li class="mb-2">
                                            <i class="fas fa-check text-success me-2"></i>{{ $feature }}
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                            @endif
                        </div>
                        
                        <div class="tab-pane fade" id="service-suppliers" role="tabpanel" aria-labelledby="suppliers-tab">
                            @php $suppliers = $service->suppliers; @endphp
                            @if($suppliers && $suppliers->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle">
                                        <thead>
                                            <tr>
                                                <th>المورد</th>
                                                <th>الحالة</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($suppliers as $supplier)
                                            <tr>
                                                <td>{{ $supplier->name }}</td>
                                                <td>
                                                    @if($supplier->pivot && $supplier->pivot->is_available)
                                                        <span class="badge bg-success">متاحة</span>
                                                    @else
                                                        <span class="badge bg-secondary">غير متاحة</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p class="text-muted mb-0">لا يوجد موردون مرتبطون بهذه الخدمة حالياً.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="card sticky-top" style="top: 100px;" data-aos="fade-left">
                    <div class="card-body">
                        
                        
                        @if($service->price || $service->isVariable() || $service->attributes->count() > 0)
                        <div class="text-center mb-3">
                            <div id="service-price-display" class="h4 text-primary mb-0">
                                @if($service->isVariable())
                                    {{ $service->price_range }}
                                @elseif($service->price)
                                    {{ number_format((float) $service->price) }} {{ __('common.currency') }}
                                @else
                                    —
                                @endif
                            </div>
                            @if($service->duration)
                                <small class="text-muted">{{ __('common.for_duration', ['duration' => $service->duration]) }}</small>
                            @endif
                        </div>
                        @endif
                        
                        <p class="text-muted mb-2 text-center">
                            {{ __('common.ready_to_start') }}<br>
                            {{ __('common.ready_to_start_hint') }}
                        </p>
                        
                        @php $unavailable = $service->suppliers && $service->suppliers->count() === 0; @endphp
                        
                        @if($unavailable)
                        <div class="alert alert-warning text-center mb-3">
                            هذه الخدمة غير متوفرة حالياً ولا يمكن إضافتها إلى السلة أو الحجز المباشر.
                        </div>
                        @else
                        <!-- Add to Cart Form -->
                        <div class="card border-primary mb-3" id="add-to-cart-card">
                            <div class="card-body">
                                <h6 class="card-title"><i class="fas fa-shopping-cart me-2"></i>{{ __('common.add_to_cart') }}</h6>
                                <form id="add-to-cart-form" 
                                          data-has-variations="{{ $service->isVariable() ? '1' : ($service->attributes->count() > 0 ? '1' : '0') }}"
                                          data-attr-count="{{ $service->attributes->count() }}"
                                          data-variation-url="{{ route('services.get-variation', $service) }}"
                                          data-add-url="{{ route('cart.add', $service) }}"
                                          data-price-fallback="{{ $service->isVariable() ? $service->price_range : ( ($service->price ? number_format((float) $service->price) . ' ' . __('common.currency') : '—') ) }}">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">{{ __('common.quantity') }}</label>
                                            <input type="number" name="quantity" class="form-control" 
                                                   value="1" min="1" max="100" required>
                                        </div>
                                        <div class="col-12 mb-3">
                                            <label class="form-label">{{ __('common.special_notes_optional') }}</label>
                                            <textarea name="customer_notes" class="form-control" rows="3" 
                                                      placeholder="{{ __('common.special_notes_placeholder') }}"></textarea>
                                            <small class="text-muted">{{ __('common.special_notes_example') }}</small>
                                        </div>
                                    </div>

                                    @if($service->isVariable() || $service->attributes->count() > 0)
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">
                                            <i class="fas fa-sliders-h me-2"></i>{{ __('common.customize_service_options') }}
                                        </label>
                                        <div class="border rounded p-3">
                                            @foreach($service->attributes as $attribute)
                                                @php $values = $attribute->values()->active()->get(); @endphp
                                                @if($values->count() > 0)
                                                <div class="mb-3">
                                                    <div class="fw-bold mb-2">{{ $attribute->name }}</div>
                                                    {{-- احترم نوع الحقل: select -> قائمة منسدلة، غير ذلك -> راديو --}}
                                                    @if($attribute->type === 'select')
                                                        <select class="form-select variation-select" name="variation[{{ $attribute->id }}]" id="attr-select-{{ $attribute->id }}">
                                                            <option value="">{{ __('common.select_attribute', ['attribute' => $attribute->name]) }}</option>
                                                            @foreach($values as $val)
                                                                <option value="{{ $val->id }}">{{ $val->value }}</option>
                                                            @endforeach
                                                        </select>
                                                    @else
                                                        <div class="d-flex flex-wrap gap-2">
                                                            @foreach($values as $val)
                                                                <div class="form-check">
                                                                    <input class="form-check-input variation-input" type="radio" 
                                                                           name="variation[{{ $attribute->id }}]" 
                                                                           value="{{ $val->id }}" id="var-{{ $attribute->id }}-{{ $val->id }}">
                                                                    <label class="form-check-label" for="var-{{ $attribute->id }}-{{ $val->id }}">{{ $val->value }}</label>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    @endif
                                                </div>
                                                @endif
                                            @endforeach
                                        </div>
                                        <div class="small text-muted">{{ __('common.price_auto_update_hint') }}</div>
                                    </div>
                                    @endif
                                    
                                    <!-- الحقول المخصصة -->
                                    @if(is_array($service->custom_fields) && count($service->custom_fields) > 0)
                                    <div class="mb-3">
                                        <label class="form-label">{{ __('common.your_selections_optional') }}</label>
                                        <div class="border rounded p-3">
                                            @foreach($service->custom_fields as $field)
                                                @php
                                                    $slug = \Illuminate\Support\Str::slug($field['label'] ?? '');
                                                    $type = $field['type'] ?? 'single';
                                                    $options = is_array($field['options'] ?? null) ? $field['options'] : [];
                                                @endphp
                                                @if($slug && count($options) > 0)
                                                <div class="mb-3">
                                                    <div class="fw-bold mb-2">{{ $field['label'] }}</div>
                                                    @if($type === 'multiple')
                                                        <div class="d-flex flex-wrap gap-2">
                                                            @foreach($options as $opt)
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="checkbox" 
                                                                           name="selections[{{ $slug }}][]" 
                                                                           value="{{ $opt }}" id="{{ $slug }}-{{ \Illuminate\Support\Str::slug($opt) }}">
                                                                    <label class="form-check-label" for="{{ $slug }}-{{ \Illuminate\Support\Str::slug($opt) }}">{{ $opt }}</label>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    @else
                                                        <div class="d-flex flex-wrap gap-2">
                                                            @foreach($options as $opt)
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="radio" 
                                                                           name="selections[{{ $slug }}]" 
                                                                           value="{{ $opt }}" id="{{ $slug }}-{{ \Illuminate\Support\Str::slug($opt) }}">
                                                                    <label class="form-check-label" for="{{ $slug }}-{{ \Illuminate\Support\Str::slug($opt) }}">{{ $opt }}</label>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    @endif
                                                </div>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                    @endif
                                    
                                    <div class="row g-2">
                                        <div class="col-12 col-md-6">
                                            <button type="submit" class="btn btn-primary w-100 mb-2 d-flex align-items-center justify-content-center gap-2 text-center">
                                                <i class="fas fa-cart-plus"></i>
                                                <span>{{ __('common.add_to_cart') }}</span>
                                            </button>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <a href="{{ route('booking.create', ['service_id' => $service->id]) }}" 
                                               class="btn btn-success btn-lg w-100 mb-2">
                                                <i class="fas fa-calendar-check me-2"></i>{{ __('common.direct_booking') }}
                                            </a>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        @endif
                        
                        <div class="row g-2">
                            <div class="col-12 col-md-6">
                                @auth
                                <button type="button" 
                                        class="btn btn-outline-danger w-100 mb-3 wishlist-toggle-btn" 
                                        data-service-id="{{ $service->id }}">
                                    <i class="fas fa-heart me-2 {{ auth()->user()->hasInWishlist($service->id) ? '' : 'text-muted' }}"></i>
                                    <span class="wishlist-text">
                                        {{ auth()->user()->hasInWishlist($service->id) ? __('common.remove_from_wishlist') : __('common.add_to_wishlist') }}
                                    </span>
                                </button>
                                @endauth
                            </div>
                        </div>
                        
                        <div class="row g-2">
                            <div class="col-12">
                                <a href="{{ route('contact') }}" class="btn btn-outline-primary w-100">
                                    <i class="fas fa-phone me-2"></i>&nbsp;{{ __('buttons.contact_us') }}
                                </a>
                            </div>
                        </div>
                        @guest
                        <div class="row g-2">
                            <div class="col-12">
                                <a href="{{ route('login') }}" class="btn btn-outline-danger w-100 mb-3">
                                    <i class="far fa-heart me-2"></i>&nbsp;{{ __('common.login_to_add_wishlist') }}
                                </a>
                            </div>
                        </div>
                        @endguest
                    </div>
                </div>
                
                <div class="card mt-4" data-aos="fade-left" data-aos-delay="100">
                    <div class="card-body">
                        <h6 class="card-title">{{ __('contact.contact_info') }}</h6>
                        <ul class="list-unstyled mb-0">
                            <li class="mb-2">
                                <i class="fas fa-phone text-primary me-2"></i>
                                <a href="tel:{{ preg_replace('/\s+/', '', setting('contact_phone', '+966 50 123 4567')) }}" class="text-decoration-none text-dark phone-ltr" dir="ltr">
                                    <span>{{ setting('contact_phone', '+966 50 123 4567') }}</span>
                                </a>
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-envelope text-primary me-2"></i>
                                <a href="mailto:{{ setting('contact_email', 'hello@yourevents.sa') }}" class="text-decoration-none text-dark">
                                    {{ setting('contact_email', 'hello@yourevents.sa') }}
                                </a>
                            </li>
                            <li>
                                <i class="fas fa-clock text-primary me-2"></i>
                                {{ setting('working_hours', __('common.working_hours_default')) }}
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="text-center mt-5" data-aos="fade-up">
            <a href="{{ route('services.index') }}" class="btn btn-outline-primary">
                <i class="fas fa-arrow-right me-2"></i>&nbsp;{{ __('common.back_to_services') }}
            </a>
        </div>
    </div>
</section>

@if(isset($similar) && $similar->count() > 0)
<section class="py-5 bg-light">
    <div class="container">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <h3 class="mb-0">
                <i class="fas fa-sparkles me-2"></i>{{ __('common.similar_services') }}
            </h3>
            <a href="{{ route('services.index', ['category' => $service->category_id]) }}" class="btn btn-outline-primary">
                {{ __('common.more_from_same_category') }}
            </a>
        </div>

        <div id="similar-slider" class="position-relative">
            <div class="d-flex overflow-hidden products grid-column mobile-grid-2 column-5" style="scroll-behavior: smooth;" data-slider-track>
                @foreach($similar as $s)
                <div class="flex-shrink-0 p-2" style="width: 25%;">
                    <div class="card h-100 shadow-sm">
                        @php $thumb = $s->thumbnail_url; @endphp
                        @if($thumb)
                            <a href="{{ route('services.show', $s->id) }}" class="d-block">
                                <img src="{{ $thumb }}" alt="{{ $s->name }}" class="card-img-top service-image" style="height: 160px; object-fit: cover;">
                            </a>
                        @endif
                        <div class="card-body d-flex flex-column">
                            <h6 class="card-title mb-1">
                                <a href="{{ route('services.show', $s->id) }}" class="text-decoration-none">{{ $s->name }}</a>
                            </h6>
                            @if($s->category)
                                <small class="text-muted mb-2 d-block text-center">{{ $s->category->name }}</small>
                            @endif
                            {{-- إزالة العنوان الفرعي داخل الخدمات المشابهة لتقليل الازدحام --}}
                            <div class="mt-auto">
                                @php $unavailable = $s->suppliers->count() === 0; @endphp
                                @if($unavailable)
                                    <button class="btn btn-sm btn-secondary rounded-pill px-3 w-100" disabled>{{ __('common.unavailable') }}</button>
                                @else
                                    @if($s->isVariable())
                                        <a href="{{ route('services.show', $s->id) }}" class="btn btn-sm btn-primary rounded-pill px-3 w-100">{{ __('common.select_options') }}</a>
                                    @else
                                        <form action="{{ route('cart.add', $s) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="quantity" value="1">
                                            <button type="submit" class="btn btn-sm btn-primary rounded-pill px-3 w-100">{{ __('common.add_to_cart') }}</button>
                                        </form>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <style>
            #similar-slider [data-slider-track] { gap: 0; }
            @media (max-width: 992px) { #similar-slider .flex-shrink-0 { width: 50% !important; } }
            /* المطلوب للموبايل: عمودين */
            @media (max-width: 576px) { #similar-slider .flex-shrink-0 { width: 50% !important; } }
        </style>

        @push('scripts')
        <script>
        document.addEventListener('DOMContentLoaded', function(){
            const track = document.querySelector('#similar-slider [data-slider-track]');
            if (!track) return;

            function getItemWidth(){
                return track.querySelector('.flex-shrink-0')?.getBoundingClientRect().width || 0;
            }

            let offset = 0;
            const totalItems = track.children.length;

            function slideNext(){
                const itemWidth = getItemWidth();
                if (!itemWidth) return;
                const containerWidth = track.parentElement.getBoundingClientRect().width;
                const visible = Math.max(1, Math.round(containerWidth / itemWidth));
                const maxOffset = itemWidth * Math.max(0, (totalItems - visible));
                offset = offset + itemWidth;
                if (offset > maxOffset) { offset = 0; }
                track.scrollTo({ left: offset, behavior: 'smooth' });
            }

            let autoTimer = setInterval(slideNext, 3000);
            track.addEventListener('mouseenter', () => { if (autoTimer) { clearInterval(autoTimer); autoTimer = null; } });
            track.addEventListener('mouseleave', () => { if (!autoTimer) { autoTimer = setInterval(slideNext, 3000); } });

            // تحديث الحسابات عند تغيير الحجم
            window.addEventListener('resize', () => { offset = 0; });
        });
        </script>
        @endpush
    </div>
</section>
@endif

<style>
.btn-success {
    background: linear-gradient(135deg, #2dbcae 0%, #4dd2c2 100%);
    border: none;
}

.btn-success:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(45, 188, 174, 0.4);
}

#add-to-cart-card {
    animation: none !important;
}

#add-to-cart-card .card-body {
    padding: 1.25rem;
}

.card.sticky-top {
    top: 100px !important;
}

.card-body {
    position: relative;
    overflow: visible;
    animation: none !important;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.alert-cart-success {
    position: fixed;
    top: 100px;
    left: 50%;
    transform: translateX(-50%);
    z-index: 9999;
    min-width: 300px;
    animation: slideDown 0.5s ease-out;
}

@keyframes slideDown {
    from { opacity: 0; transform: translate(-50%, -20px); }
    to { opacity: 1; transform: translate(-50%, 0); }
}

.wishlist-toggle-btn:hover {
    background-color: #dc3545;
    color: white;
}

.wishlist-toggle-btn:hover .fa-heart {
    color: white !important;
}

/* Gallery Styles */
#mainImage {
    transition: opacity 0.3s ease;
}

.thumbnail-img {
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

.thumbnail-img:hover {
    transform: scale(1.05);
    border-color: #007bff;
}
.card, .card-body {
    transition: none !important;
}
.card:hover, .card-body:hover {
    transform: none !important;
    box-shadow: none !important;
}
.card:hover::before {
    transform: none !important;
}

#service-price-display {
    color: #7269b0 !important;
}
.fa-align-left.text-primary,
.fa-gift.text-primary,
.fa-clock.text-primary,
.fa-envelope.text-primary {
    color: #7269b0 !important;
}
.card-title.mb-1,
.card-title.mb-1 a {
    color: #7269b0 !important;
}
.card-title.mb-1 a:hover {
    color: #7269b0 !important;
}
@media (min-width: 992px) {
    #add-to-cart-form .btn {
        font-size: 0.95rem !important;
        white-space: nowrap;
    }
}
@media (min-width: 768px) and (max-width: 991.98px) {
    #add-to-cart-form .btn {
        font-size: 0.9rem !important;
        white-space: nowrap;
        padding: .5rem .75rem !important;
    }
}
</style>

<style>
/* تحسين عرض الصور على الموبايل: مربعة وتملأ الحاوية */
@media (max-width: 768px) {
    /* اجعل الحاوية الرئيسية مربعة */
    #mainImageContainer { width: 100%; aspect-ratio: 1 / 1; }
    /* اجعل الصورة تملأ المربع بالكامل */
    .service-main-image,
    .service-image {
        width: 100% !important;
        height: 100% !important;
        object-fit: cover !important;
        display: block;
    }
}
@media (max-width: 576px) {
    .service-image {
        aspect-ratio: 1 / 1;
    }
}
</style>

@push('scripts')
<div id="service-script-data" style="display: none;" data-currency="{{ __('common.currency') }}"></div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('add-to-cart-form');
    const priceBox = document.getElementById('service-price-display');
    const serviceScriptData = document.getElementById('service-script-data');
    const currencyText = serviceScriptData ? (serviceScriptData.dataset.currency || '') : '';

    // Wishlist Toggle
    const wishlistBtns = document.querySelectorAll('.wishlist-toggle-btn');
    wishlistBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const serviceId = this.dataset.serviceId;
            const icon = this.querySelector('.fa-heart');
            const text = this.querySelector('.wishlist-text');
            
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
                    // Update icon
                    if (data.action === 'added') {
                        icon.classList.remove('text-muted');
                        icon.classList.add('text-danger');
                        text.textContent = 'إزالة من المفضلة';
                    } else {
                        icon.classList.remove('text-danger');
                        icon.classList.add('text-muted');
                        text.textContent = 'أضف للمفضلة';
                    }
                    
                    // Show alert
                    showAlert(data.message, 'success');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('حدث خطأ، حاول مرة أخرى', 'danger');
            });
        });
    });

    function showAlert(message, type = 'success') {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-cart-success alert-dismissible fade show`;
        alertDiv.innerHTML = `
            <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        document.body.appendChild(alertDiv);
        setTimeout(() => { alertDiv.remove(); }, 3000);
    }

    function currentSelectedValueIds(){
        // اجمع قيم الراديو
        const radioInputs = document.querySelectorAll('input.variation-input');
        const map = new Map();
        radioInputs.forEach(inp => {
            const m = inp.name.match(/variation\[(\d+)\]/);
            const attrId = m ? m[1] : null;
            if (!attrId) return;
            if (inp.checked) { map.set(attrId, parseInt(inp.value)); }
        });
        // اجمع قيم القوائم المنسدلة
        const selects = document.querySelectorAll('select.variation-select');
        selects.forEach(sel => {
            const m = sel.name.match(/variation\[(\d+)\]/);
            const attrId = m ? m[1] : null;
            const val = sel.value;
            if (!attrId || !val) return;
            map.set(attrId, parseInt(val));
        });
        const ids = Array.from(map.values());
        ids.sort((a,b)=>a-b);
        return ids;
    }

    function updatePriceViaVariation(){
        if (!priceBox || !form) return;
        const ids = currentSelectedValueIds();
        const attrCount = parseInt(form.dataset.attrCount || '0', 10);
        const variationUrl = form.dataset.variationUrl;
        const fallbackPriceText = (form.dataset.priceFallback || '0');

        if (ids.length === attrCount && variationUrl){
            fetch(variationUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ value_ids: ids })
            })
            .then(r => r.json())
            .then(data => {
                if (data && data.success){
                    priceBox.textContent = `${Number(data.price).toLocaleString()} ${currencyText}`;
                    let hid = document.getElementById('selected_variation_id');
                    if (!hid){
                        hid = document.createElement('input');
                        hid.type = 'hidden';
                        hid.name = 'selected_variation_id';
                        hid.id = 'selected_variation_id';
                        form.appendChild(hid);
                    }
                    hid.value = data.variation_id || '';
                } else {
                    priceBox.textContent = fallbackPriceText;
                    const hid = document.getElementById('selected_variation_id');
                    if (hid) hid.value = '';
                }
            })
            .catch(()=>{
                priceBox.textContent = fallbackPriceText;
                const hid = document.getElementById('selected_variation_id');
                if (hid) hid.value = '';
            });
        } else {
            priceBox.textContent = fallbackPriceText;
            const hid = document.getElementById('selected_variation_id');
            if (hid) hid.value = '';
        }
    }

    // مستمعات الراديو
    document.querySelectorAll('input.variation-input').forEach(inp => {
        inp.addEventListener('change', updatePriceViaVariation);
    });
    // مستمعات القوائم المنسدلة
    document.querySelectorAll('select.variation-select').forEach(sel => {
        sel.addEventListener('change', updatePriceViaVariation);
    });

    const hasVariations = form?.dataset?.hasVariations === '1';
    if (hasVariations) { updatePriceViaVariation(); }

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(form);
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>جاري الإضافة...';
        fetch(form.dataset.addUrl, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        })
        .then(async response => {
            const data = await response.json().catch(() => null);
            if (!response.ok) {
                const message = (data && (data.message || data.error)) ? (data.message || data.error) : 'حدث خطأ. حاول مرة أخرى.';
                throw new Error(message);
            }
            return data;
        })
        .then(data => {
            if (data && data.success) {
                // تحديث السلة بالكامل (العداد والقائمة المنسدلة)
                if (typeof window.updateCartDropdown === 'function') {
                    window.updateCartDropdown();
                } else {
                    // Fallback للتحديث التقليدي
                    const cartBadge = document.getElementById('cart-count');
                    if (cartBadge) {
                        cartBadge.textContent = data.cart_count;
                    } else if (data.cart_count > 0) {
                        const cartIcon = document.querySelector('.cart-icon-wrapper');
                        if (cartIcon) {
                            const badge = document.createElement('span');
                            badge.className = 'cart-badge';
                            badge.id = 'cart-count';
                            badge.textContent = data.cart_count;
                            cartIcon.appendChild(badge);
                        }
                    }
                }
                showAlert(data.message, 'success');
                form.reset();
            } else {
                showAlert((data && data.message) ? data.message : 'حدث خطأ. حاول مرة أخرى.', 'danger');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert(error?.message || 'حدث خطأ. حاول مرة أخرى.', 'danger');
        })
        .finally(() => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        });
    });

    // ===== معرض الصور =====
    window.changeMainImage = function(imageUrl) {
        const mainImage = document.getElementById('mainImage');
        if (mainImage) {
            mainImage.src = imageUrl;
            // تأثير بسيط
            mainImage.style.opacity = '0.5';
            setTimeout(() => {
                mainImage.style.opacity = '1';
            }, 200);
        }
    };
});
</script>
@endpush
@endsection

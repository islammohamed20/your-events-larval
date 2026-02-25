@extends('layouts.app')

@section('title', ($selectedCategory ? $selectedCategory->name : 'خدماتنا') . ' - Your Events')

@section('content')
<!-- Page Header -->
<section class="hero-section" style="padding: 40px 0;">
    <div class="container">
        <div class="text-center">
            <h1 class="display-4 fw-bold mb-3" style="color: var(--primary-color);">
                @if($selectedCategory)
                    {{ $selectedCategory->name }}
                @else
                    خدماتنا
                @endif
            </h1>
            <p class="lead" style="color: var(--text-color);">
                @if($selectedCategory && $selectedCategory->description)
                    {{ $selectedCategory->description }}
                @else
                    نقدم مجموعة شاملة من الخدمات لجعل مناسبتك مميزة ولا تُنسى
                @endif
            </p>
        </div>
    </div>
</section>

<!-- Services Grid -->
<section class="py-4">
    <div class="container">
        <div class="row">
            @forelse($services as $service)
                <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
                    <div class="card h-100">
                        @if($service->image)
                            <img src="{{ Storage::url($service->image) }}" class="card-img-top" alt="{{ $service->name }}">
                        @else
                            <img src="https://images.unsplash.com/photo-1492684223066-81342ee5ff30?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80" 
                                 class="card-img-top" alt="{{ $service->name }}">
                        @endif
                        
                        <!-- Wishlist Button -->
                        @auth
                            <button type="button" 
                                    class="btn btn-link wishlist-toggle-btn position-absolute top-0 end-0 m-2" 
                                    data-service-id="{{ $service->id }}"
                                    style="z-index: 10; background: rgba(255,255,255,0.9); border-radius: 50%; width: 40px; height: 40px; padding: 0; box-shadow: 0 2px 8px rgba(0,0,0,0.15);">
                                <i class="fas fa-heart {{ auth()->user()->hasInWishlist($service->id) ? 'text-danger' : 'text-muted' }}" 
                                   style="font-size: 1.2rem;"></i>
                            </button>
                        @endauth
                        
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h5 class="card-title mb-0">{{ $service->name }}</h5>
                                @if($service->type)
                                    <span class="badge bg-primary">{{ $service->type }}</span>
                                @endif
                            </div>
                            
                            @if($service->price)
                                <div class="mb-2">
                                    <span class="h6 text-primary">{{ number_format($service->price) }} {{ __('common.currency') }}</span>
                                    @if($service->duration)
                                        <small class="text-muted"> - {{ $service->duration }}</small>
                                    @endif
                                </div>
                            @endif
                            
                            <p class="card-text flex-grow-1">{{ Str::limit($service->description, 100) }}</p>
                            <div class="mt-auto">
                                @if($service->isVariable())
                                    <a href="{{ route('services.show', $service->id) }}" class="btn btn-primary w-100">
                                        <i class="fas fa-sliders-h me-2"></i> Select Option
                                    </a>
                                @else
                                    <button type="button" class="btn btn-primary w-100 add-to-cart-btn" 
                                            data-service-id="{{ $service->id }}"
                                            data-service-name="{{ $service->name }}"
                                            data-service-price="{{ $service->price }}">
                                        <i class="fas fa-cart-plus me-2"></i>{{ __('common.add_to_cart') }}
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="text-center py-5">
                        <i class="fas fa-search fa-3x text-muted mb-3"></i>
                        <h4>لا توجد خدمات متاحة حالياً</h4>
                        <p class="text-muted">نعمل على إضافة المزيد من الخدمات قريباً</p>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</section>

<!-- Why Choose Us -->
<section class="py-4 bg-secondary-custom">
    <div class="container">
        <h2 class="section-title" data-aos="fade-up" style="margin-bottom: 2rem;">لماذا تختار خدماتنا؟</h2>
        <div class="row">
            <div class="col-lg-3 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="100">
                <div class="text-center">
                    <div class="mb-3">
                        <i class="fas fa-star fa-3x" style="color: var(--primary-color);"></i>
                    </div>
                    <h5>خدمة مميزة</h5>
                    <p class="text-muted">نقدم أعلى مستويات الخدمة والجودة</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="200">
                <div class="text-center">
                    <div class="mb-3">
                        <i class="fas fa-users fa-3x" style="color: var(--primary-color);"></i>
                    </div>
                    <h5>فريق خبير</h5>
                    <p class="text-muted">فريق متخصص من الخبراء في تنظيم المناسبات</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="300">
                <div class="text-center">
                    <div class="mb-3">
                        <i class="fas fa-clock fa-3x" style="color: var(--primary-color);"></i>
                    </div>
                    <h5>التزام بالمواعيد</h5>
                    <p class="text-muted">نلتزم بالمواعيد المحددة دون تأخير</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="400">
                <div class="text-center">
                    <div class="mb-3">
                        <i class="fas fa-heart fa-3x" style="color: var(--primary-color);"></i>
                    </div>
                    <h5>رضا العملاء</h5>
                    <p class="text-muted">نهدف لرضا عملائنا بنسبة 100%</p>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
.add-to-cart-btn {
    position: relative;
    overflow: hidden;
    transition: all 0.3s ease;
}

.add-to-cart-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(239, 72, 112, 0.3);
}

.add-to-cart-btn:active {
    transform: translateY(0);
}

.add-to-cart-btn.loading {
    pointer-events: none;
    opacity: 0.7;
}

.add-to-cart-btn.success {
    background: linear-gradient(135deg, #2dbcae 0%, #4dd2c2 100%) !important;
}

/* Alert Animation */
@keyframes slideInDown {
    from {
        opacity: 0;
        transform: translate(-50%, -30px);
    }
    to {
        opacity: 1;
        transform: translate(-50%, 0);
    }
}

.cart-alert {
    position: fixed;
    top: 100px;
    left: 50%;
    transform: translateX(-50%);
    z-index: 9999;
    min-width: 300px;
    animation: slideInDown 0.3s ease-out;
}
</style>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add to cart buttons
    const addToCartButtons = document.querySelectorAll('.add-to-cart-btn');
    
    addToCartButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const serviceId = this.dataset.serviceId;
            const serviceName = this.dataset.serviceName;
            const originalHtml = this.innerHTML;
            
            // Disable button and show loading
            this.classList.add('loading');
            this.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>جاري الإضافة...';
            
            // Send AJAX request
            fetch(`/cart/add/${serviceId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    quantity: 1
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update cart count
                    updateCartCount(data.cart_count);
                    
                    // Show success state
                    this.classList.remove('loading');
                    this.classList.add('success');
                    this.innerHTML = '<i class="fas fa-check me-2"></i>تمت الإضافة!';
                    
                    // Show alert
                    showAlert('success', `تمت إضافة "${serviceName}" إلى السلة بنجاح`);
                    
                    // Reset button after 2 seconds
                    setTimeout(() => {
                        this.classList.remove('success');
                        this.innerHTML = originalHtml;
                    }, 2000);
                } else {
                    throw new Error(data.message || 'حدث خطأ');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                this.classList.remove('loading');
                this.innerHTML = originalHtml;
                showAlert('danger', 'حدث خطأ أثناء الإضافة للسلة. حاول مرة أخرى.');
            });
        });
    });
    
    function updateCartCount(count) {
        let cartBadge = document.getElementById('cart-count');
        if (cartBadge) {
            cartBadge.textContent = count;
        } else if (count > 0) {
            // Create badge if it doesn't exist
            const cartIcon = document.querySelector('.cart-icon-wrapper');
            if (cartIcon) {
                const badge = document.createElement('span');
                badge.className = 'cart-badge';
                badge.id = 'cart-count';
                badge.textContent = count;
                cartIcon.appendChild(badge);
            }
        }
    }
    
    function showAlert(type, message) {
        // Remove any existing alerts
        const existingAlert = document.querySelector('.cart-alert');
        if (existingAlert) {
            existingAlert.remove();
        }
        
        // Create new alert
        const alert = document.createElement('div');
        alert.className = `alert alert-${type} alert-dismissible fade show cart-alert`;
        alert.innerHTML = `
            <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        document.body.appendChild(alert);
        
        // Auto remove after 3 seconds
        setTimeout(() => {
            alert.remove();
        }, 3000);
    }
    
    // Wishlist functionality
    document.querySelectorAll('.wishlist-toggle-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const serviceId = this.dataset.serviceId;
            const icon = this.querySelector('i');
            
            fetch('{{ route("wishlist.toggle") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
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
                    showAlert('success', data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('danger', 'حدث خطأ أثناء إضافة الخدمة إلى قائمة الأمنيات');
            });
        });
    });
});
</script>
@endpush
@endsection

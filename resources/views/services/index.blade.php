@extends('layouts.app')

@section('title', ($selectedCategory ? $selectedCategory->name : 'خدماتنا') . ' - Your Events')

@section('content')
<!-- Page Header -->
<section class="hero-section" style="padding: 40px 0; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
    <div class="container">
        <div class="text-center">
            <h1 class="display-4 fw-bold mb-3 text-white">
                @if($selectedCategory)
                    {{ $selectedCategory->name }}
                @else
                    خدماتنا
                @endif
            </h1>
            <p class="lead text-white-50">
                @if($selectedCategory && $selectedCategory->description)
                    {{ $selectedCategory->description }}
                @else
                    نقدم مجموعة شاملة من الخدمات لجعل مناسبتك مميزة ولا تُنسى
                @endif
            </p>
        </div>
    </div>
</section>

<!-- Services Section with Sidebar Filter -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <!-- Sidebar Filter (Desktop) -->
            <div class="col-12 col-lg-3 sidebar-column filtered-sidebar sticky d-none d-lg-block" 
                 style="position: relative; overflow: visible; box-sizing: border-box; min-height: 1px;">
                <div class="filter-sidebar" style="position: sticky; top: 100px;">
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white border-0 py-3">
                            <h5 class="mb-0">
                                <i class="fas fa-filter me-2"></i>
                                تصفية النتائج
                            </h5>
                        </div>
                        <div class="card-body">
                            <!-- Categories Filter -->
                            <div class="filter-group mb-4">
                                <h6 class="fw-bold mb-3">الفئات</h6>
                                <div class="list-group list-group-flush">
                                    <a href="{{ route('services.index') }}" 
                                       class="list-group-item list-group-item-action border-0 px-0 {{ !$selectedCategory ? 'active' : '' }}">
                                        <i class="fas fa-th-large me-2"></i>
                                        كل الخدمات
                                        <span class="badge bg-secondary float-end">{{ $services->total() }}</span>
                                    </a>
                                    @foreach($categories as $category)
                                        <a href="{{ route('services.index', ['category' => $category->id]) }}" 
                                           class="list-group-item list-group-item-action border-0 px-0 {{ $selectedCategory && $selectedCategory->id == $category->id ? 'active' : '' }}">
                                            <i class="fas fa-tag me-2"></i>
                                            {{ $category->name }}
                                            <span class="badge bg-secondary float-end">{{ $category->services_count }}</span>
                                        </a>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Price Range Filter -->
                            <div class="filter-group mb-4">
                                <h6 class="fw-bold mb-3">السعر</h6>
                                <div class="form-check mb-2">
                                    <input class="form-check-input price-filter" type="checkbox" value="0-500" id="price1">
                                    <label class="form-check-label" for="price1">
                                        أقل من 500 ريال
                                    </label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input price-filter" type="checkbox" value="500-1000" id="price2">
                                    <label class="form-check-label" for="price2">
                                        500 - 1000 ريال
                                    </label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input price-filter" type="checkbox" value="1000-2000" id="price3">
                                    <label class="form-check-label" for="price3">
                                        1000 - 2000 ريال
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input price-filter" type="checkbox" value="2000+" id="price4">
                                    <label class="form-check-label" for="price4">
                                        أكثر من 2000 ريال
                                    </label>
                                </div>
                            </div>

                            <!-- Sort Filter -->
                            <div class="filter-group mb-4">
                                <h6 class="fw-bold mb-3">ترتيب حسب</h6>
                                <select class="form-select" id="sortFilter">
                                    <option value="latest">الأحدث</option>
                                    <option value="price_low">السعر: من الأقل للأعلى</option>
                                    <option value="price_high">السعر: من الأعلى للأقل</option>
                                    <option value="name">الاسم</option>
                                </select>
                            </div>

                            <!-- Clear Filters Button -->
                            <button type="button" class="btn btn-outline-secondary w-100" id="clearFilters">
                                <i class="fas fa-redo me-2"></i>
                                إعادة تعيين الفلاتر
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mobile Filter Button -->
            <div class="col-12 d-lg-none mb-3">
                <button class="btn btn-outline-primary w-100" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileFilters">
                    <i class="fas fa-filter me-2"></i>
                    تصفية النتائج
                </button>
            </div>

            <!-- Services Grid -->
            <div class="col-12 col-lg-9">
                <!-- Results Header -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h5 class="mb-0">عرض {{ $services->count() }} من {{ $services->total() }} خدمة</h5>
                    </div>
                    <div class="d-none d-md-block">
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-outline-secondary active" id="gridView">
                                <i class="fas fa-th"></i>
                            </button>
                            <button type="button" class="btn btn-outline-secondary" id="listView">
                                <i class="fas fa-list"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Products Grid -->
                <div class="products grid-column mobile-grid-2 column-3" id="servicesContainer">
                    @forelse($services as $service)
                        <div class="service-item mb-4" 
                             data-price="{{ $service->price }}" 
                             data-name="{{ $service->name }}"
                             data-aos="fade-up" 
                             data-aos-delay="{{ $loop->index * 50 }}">
                            <div class="card h-100 service-card">
                                <!-- Service Image -->
                                <div class="position-relative service-image-wrapper">
                                    <img src="{{ $service->thumbnail_url }}" 
                                         class="card-img-top service-image" 
                                         alt="{{ $service->name }}"
                                         style="object-fit: cover;">
                                    
                                    <!-- Wishlist Button -->
                                    @auth
                                        <button type="button" 
                                                class="btn btn-link wishlist-toggle-btn position-absolute top-0 end-0 m-2" 
                                                data-service-id="{{ $service->id }}"
                                                style="z-index: 10; background: rgba(255,255,255,0.95); border-radius: 50%; width: 40px; height: 40px; padding: 0; box-shadow: 0 2px 10px rgba(0,0,0,0.2);">
                                            <i class="fas fa-heart {{ auth()->user()->hasInWishlist($service->id) ? 'text-danger' : 'text-muted' }}" 
                                               style="font-size: 1.2rem;"></i>
                                        </button>
                                    @endauth

                                    <!-- Service Type Badge -->
                                    @if($service->type)
                                        <span class="badge bg-primary position-absolute top-0 start-0 m-2">
                                            {{ $service->type }}
                                        </span>
                                    @endif
                                </div>
                                
                                <div class="card-body d-flex flex-column">
                                    <!-- Service Name -->
                                    <h5 class="card-title mb-2">
                                        <a href="{{ route('services.show', $service->id) }}" class="text-decoration-none text-dark service-title-link">
                                            {{ $service->name }}
                                        </a>
                                    </h5>
                                    
                                    <!-- Service Price -->
                                    @if($service->price)
                                        <div class="mb-3">
                                            <span class="h5 text-primary mb-0">{{ number_format($service->price) }} ريال</span>
                                            @if($service->duration)
                                                <small class="text-muted d-block">{{ $service->duration }}</small>
                                            @endif
                                        </div>
                                    @endif
                                    
                                    <!-- Service Description -->
                                    <p>
                                        {{ Str::limit($service->description, 100) }}
                                    </p>
                                    
                                    <!-- Actions -->
                                    <div class="mt-auto">
                                        @if($service->isVariable())
                                            <a href="{{ route('services.show', $service->id) }}" 
                                               class="btn btn-primary w-100">
                                                <i class="fas fa-sliders-h me-2"></i>
                                                اختر الخيارات
                                            </a>
                                        @else
                                            <button type="button" 
                                                    class="btn btn-primary w-100 add-to-cart-btn" 
                                                    data-service-id="{{ $service->id }}"
                                                    data-service-name="{{ $service->name }}"
                                                    data-service-price="{{ $service->price }}">
                                                <i class="fas fa-cart-plus me-2"></i>
                                                اضف إلي السلة
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
                                <h4>لا توجد خدمات متاحة</h4>
                                <p class="text-muted">جرب تغيير الفلاتر</p>
                            </div>
                        </div>
                    @endforelse
                </div>

                <!-- Pagination -->
                @if($services->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                        {{ $services->appends(request()->query())->links('vendor.pagination.bootstrap-5') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>

<style>
/* الحفاظ على ارتفاع ثابت على الشاشات الكبيرة لتناسق الكروت */
.service-card .service-image { width: 100%; height: 250px; object-fit: cover; display: block; }

/* حاوية مربعة على الموبايل والصورة تملأها بالكامل */
@media (max-width: 768px) {
    .service-card .service-image-wrapper { width: 100%; aspect-ratio: 1 / 1; }
    .service-card .service-image { width: 100%; height: 100%; object-fit: cover; }
}
</style>

<!-- Mobile Filters Offcanvas -->
<div class="offcanvas offcanvas-start" tabindex="-1" id="mobileFilters">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title">
            <i class="fas fa-filter me-2"></i>
            تصفية النتائج
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body">
        <!-- Same filters as sidebar -->
        <div class="filter-group mb-4">
            <h6 class="fw-bold mb-3">الفئات</h6>
            <div class="list-group list-group-flush">
                <a href="{{ route('services.index') }}" 
                   class="list-group-item list-group-item-action {{ !$selectedCategory ? 'active' : '' }}">
                    كل الخدمات
                    <span class="badge bg-secondary float-end">{{ $services->total() }}</span>
                </a>
                @foreach($categories as $category)
                    <a href="{{ route('services.index', ['category' => $category->id]) }}" 
                       class="list-group-item list-group-item-action {{ $selectedCategory && $selectedCategory->id == $category->id ? 'active' : '' }}">
                        {{ $category->name }}
                        <span class="badge bg-secondary float-end">{{ $category->services_count }}</span>
                    </a>
                @endforeach
            </div>
        </div>

        <div class="filter-group mb-4">
            <h6 class="fw-bold mb-3">السعر</h6>
            <div class="form-check mb-2">
                <input class="form-check-input price-filter-mobile" type="checkbox" value="0-500" id="priceM1">
                <label class="form-check-label" for="priceM1">أقل من 500 ريال</label>
            </div>
            <div class="form-check mb-2">
                <input class="form-check-input price-filter-mobile" type="checkbox" value="500-1000" id="priceM2">
                <label class="form-check-label" for="priceM2">500 - 1000 ريال</label>
            </div>
            <div class="form-check mb-2">
                <input class="form-check-input price-filter-mobile" type="checkbox" value="1000-2000" id="priceM3">
                <label class="form-check-label" for="priceM3">1000 - 2000 ريال</label>
            </div>
            <div class="form-check">
                <input class="form-check-input price-filter-mobile" type="checkbox" value="2000+" id="priceM4">
                <label class="form-check-label" for="priceM4">أكثر من 2000 ريال</label>
            </div>
        </div>

        <div class="filter-group mb-4">
            <h6 class="fw-bold mb-3">ترتيب حسب</h6>
            <select class="form-select" id="sortFilterMobile">
                <option value="latest">الأحدث</option>
                <option value="price_low">السعر: من الأقل للأعلى</option>
                <option value="price_high">السعر: من الأعلى للأقل</option>
                <option value="name">الاسم</option>
            </select>
        </div>

        <button type="button" class="btn btn-outline-secondary w-100 mb-2" id="clearFiltersMobile">
            <i class="fas fa-redo me-2"></i>
            إعادة تعيين
        </button>
        <button type="button" class="btn btn-primary w-100" data-bs-dismiss="offcanvas">
            <i class="fas fa-check me-2"></i>
            تطبيق الفلاتر
        </button>
    </div>
</div>

<style>
/* Grid Layout */
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

.service-card .card-img-top {
    transition: transform 0.3s ease;
}

.service-card:hover .card-img-top {
    transform: scale(1.05);
}

/* Sidebar Sticky */
.filtered-sidebar .filter-sidebar {
    max-height: calc(100vh - 150px);
    overflow-y: auto;
    animation: none !important;
    transition: none !important;
}

.filter-sidebar {
    animation: none !important;
    transition: none !important;
}

/* إلغاء جميع تأثيرات الحركة في الـ sidebar */
.filter-sidebar *,
.filter-sidebar *:hover,
.filter-sidebar .list-group-item,
.filter-sidebar .list-group-item:hover,
.filter-sidebar .list-group-item-action:hover,
.filter-sidebar .form-check:hover,
.filter-sidebar .form-check-label:hover,
.filter-sidebar a:hover,
.filter-sidebar button:hover {
    animation: none !important;
    transition: none !important;
    transform: none !important;
}

/* Filter List Group */
.list-group-item.active {
    background-color: var(--primary-color, #667eea);
    border-color: var(--primary-color, #667eea);
}

/* Service Title Link */
.service-title-link {
    transition: color 0.2s ease;
}

.service-title-link:hover {
    color: var(--primary-color, #667eea) !important;
}

/* Pagination RTL Fix */
.pagination {
    display: flex;
    flex-direction: row;
    gap: 5px;
}

.pagination .page-item {
    direction: rtl;
}

.pagination .page-link {
    border-radius: 8px;
    padding: 10px 16px;
    border: 1px solid #dee2e6;
    color: var(--primary-color, #667eea);
    transition: all 0.3s ease;
    font-weight: 500;
}

.pagination .page-link:hover {
    background: var(--primary-color, #667eea);
    color: white;
    border-color: var(--primary-color, #667eea);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
}

.pagination .page-item.active .page-link {
    background: var(--primary-color, #667eea);
    border-color: var(--primary-color, #667eea);
    color: white;
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
}

.pagination .page-item.disabled .page-link {
    color: #6c757d;
    pointer-events: none;
    background-color: #f8f9fa;
}

/* Fix pagination arrows direction */
.pagination .page-link[rel="prev"]::before,
.pagination .page-link[rel="next"]::before {
    display: none;
}

.pagination .page-link[rel="prev"]::after {
    content: "←";
    margin-right: 5px;
}

.pagination .page-link[rel="next"]::after {
    content: "→";
    margin-left: 5px;
}

/* Add to Cart Button */
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

/* List View (Optional) */
.products.list-view {
    display: block;
}

.products.list-view .service-item {
    width: 100%;
}

.products.list-view .service-card {
    flex-direction: row;
}

.products.list-view .service-card img {
    width: 250px;
    height: 200px;
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

/* Scrollbar Styling */
.filter-sidebar::-webkit-scrollbar {
    width: 6px;
}

.filter-sidebar::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

.filter-sidebar::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 10px;
}

.filter-sidebar::-webkit-scrollbar-thumb:hover {
    background: #555;
}
</style>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const servicesContainer = document.getElementById('servicesContainer');
    const serviceItems = document.querySelectorAll('.service-item');
    
    // Price Filter
    document.querySelectorAll('.price-filter, .price-filter-mobile').forEach(checkbox => {
        checkbox.addEventListener('change', filterServices);
    });
    
    // Sort Filter
    document.getElementById('sortFilter').addEventListener('change', sortServices);
    document.getElementById('sortFilterMobile').addEventListener('change', sortServices);
    
    // Clear Filters
    document.getElementById('clearFilters').addEventListener('click', clearFilters);
    document.getElementById('clearFiltersMobile').addEventListener('click', clearFilters);
    
    // Grid/List View Toggle
    document.getElementById('gridView')?.addEventListener('click', function() {
        servicesContainer.classList.remove('list-view');
        this.classList.add('active');
        document.getElementById('listView').classList.remove('active');
    });
    
    document.getElementById('listView')?.addEventListener('click', function() {
        servicesContainer.classList.add('list-view');
        this.classList.add('active');
        document.getElementById('gridView').classList.remove('active');
    });
    
    function filterServices() {
        const selectedPrices = Array.from(document.querySelectorAll('.price-filter:checked, .price-filter-mobile:checked'))
            .map(cb => cb.value);
        
        serviceItems.forEach(item => {
            const price = parseFloat(item.dataset.price);
            let show = selectedPrices.length === 0;
            
            selectedPrices.forEach(range => {
                if (range === '0-500' && price < 500) show = true;
                else if (range === '500-1000' && price >= 500 && price < 1000) show = true;
                else if (range === '1000-2000' && price >= 1000 && price < 2000) show = true;
                else if (range === '2000+' && price >= 2000) show = true;
            });
            
            item.style.display = show ? 'block' : 'none';
        });
    }
    
    function sortServices() {
        const sortValue = document.getElementById('sortFilter').value;
        const itemsArray = Array.from(serviceItems);
        
        itemsArray.sort((a, b) => {
            if (sortValue === 'price_low') {
                return parseFloat(a.dataset.price) - parseFloat(b.dataset.price);
            } else if (sortValue === 'price_high') {
                return parseFloat(b.dataset.price) - parseFloat(a.dataset.price);
            } else if (sortValue === 'name') {
                return a.dataset.name.localeCompare(b.dataset.name, 'ar');
            }
            return 0;
        });
        
        itemsArray.forEach(item => servicesContainer.appendChild(item));
    }
    
    function clearFilters() {
        document.querySelectorAll('.price-filter, .price-filter-mobile').forEach(cb => {
            cb.checked = false;
        });
        document.getElementById('sortFilter').value = 'latest';
        document.getElementById('sortFilterMobile').value = 'latest';
        filterServices();
    }
    
    // Add to cart buttons
    const addToCartButtons = document.querySelectorAll('.add-to-cart-btn');
    
    addToCartButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const serviceId = this.dataset.serviceId;
            const serviceName = this.dataset.serviceName;
            const originalHtml = this.innerHTML;
            
            this.classList.add('loading');
            this.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>جاري الإضافة...';
            
            fetch(`/cart/add/${serviceId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ quantity: 1 })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // تحديث السلة بالكامل (العداد والقائمة المنسدلة)
                    if (typeof window.updateCartDropdown === 'function') {
                        window.updateCartDropdown();
                    } else {
                        // Fallback للتحديث التقليدي
                        updateCartCount(data.cart_count);
                    }
                    
                    this.classList.remove('loading');
                    this.classList.add('success');
                    this.innerHTML = '<i class="fas fa-check me-2"></i>تمت الإضافة!';
                    
                    showAlert('success', `تمت إضافة "${serviceName}" إلى السلة بنجاح`);
                    
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
        if (typeof window.updateCartCount === 'function') {
            window.updateCartCount(count);
        } else {
            // Fallback
            let cartBadge = document.getElementById('cart-count');
            if (cartBadge) {
                cartBadge.textContent = count;
            } else if (count > 0) {
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
    }
    
    function showAlert(type, message) {
        const existingAlert = document.querySelector('.cart-alert');
        if (existingAlert) existingAlert.remove();
        
        const alert = document.createElement('div');
        alert.className = `alert alert-${type} alert-dismissible fade show cart-alert`;
        alert.innerHTML = `
            <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        document.body.appendChild(alert);
        setTimeout(() => alert.remove(), 3000);
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
                showAlert('danger', 'حدث خطأ');
            });
        });
    });
});
</script>
@endpush
@endsection

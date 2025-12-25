@extends('layouts.app')

@section('title', 'قائمة الأمنيات') 'قائمة المفضلة - Your Events')

@section('content')
<div class="container py-5">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3 mb-4">
            <div class="card">
                <div class="card-body text-center">
                    <div class="avatar-lg mx-auto mb-3">
                        <div class="avatar-title bg-primary rounded-circle" style="width: 80px; height: 80px; display: flex; align-items: center; justify-content: center; font-size: 32px; margin: 0 auto;">
                            {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                        </div>
                    </div>
                    <h5 class="mb-1">{{ auth()->user()->name }}</h5>
                    <p class="text-muted small">{{ auth()->user()->email }}</p>
                    @if(auth()->user()->is_admin)
                        <span class="badge bg-danger">مدير النظام</span>
                    @endif
                </div>
                <div class="list-group list-group-flush">
                    <a href="{{ route('profile.show') }}" class="list-group-item list-group-item-action">
                        <i class="fas fa-user me-2"></i> البيانات الشخصية
                    </a>
                    <a href="{{ route('profile.edit') }}" class="list-group-item list-group-item-action">
                        <i class="fas fa-edit me-2"></i> تعديل البيانات
                    </a>
                    <a href="{{ route('profile.password') }}" class="list-group-item list-group-item-action">
                        <i class="fas fa-key me-2"></i> تغيير كلمة المرور
                    </a>
                    <a href="{{ route('wishlist.index') }}" class="list-group-item list-group-item-action active">
                        <i class="fas fa-heart me-2"></i> قائمة المفضلة
                        @if(auth()->user()->wishlists->count() > 0)
                            <span class="badge bg-danger rounded-pill float-end">{{ auth()->user()->wishlists->count() }}</span>
                        @endif
                    </a>
                    <a href="{{ route('quotes.index') }}" class="list-group-item list-group-item-action">
                        <i class="fas fa-file-invoice me-2"></i> عروض الأسعار
                    </a>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-md-9">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-heart me-2 text-danger"></i>قائمة المفضلة
                    </h5>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if($wishlists->count() > 0)
                        <div class="row">
                            @foreach($wishlists as $wishlist)
                                <div class="col-md-6 mb-4">
                                    <div class="card h-100 shadow-sm">
                                        <a href="{{ route('services.show', $wishlist->service) }}" style="text-decoration: none;">
                                            <img src="{{ $wishlist->service->thumbnail_url }}" 
                                                 class="card-img-top" 
                                                 alt="{{ $wishlist->service->name }}"
                                                 style="height: 200px; object-fit: cover; cursor: pointer; transition: transform 0.3s ease;"
                                                 onmouseover="this.style.transform='scale(1.05)'"
                                                 onmouseout="this.style.transform='scale(1)'">
                                        </a>
                                        
                                        <div class="card-body d-flex flex-column">
                                            <h5 class="card-title arabic-text">{{ $wishlist->service->name }}</h5>
                                            <p class="card-text arabic-text text-muted flex-grow-1">
                                                {{ Str::limit($wishlist->service->description, 100) }}
                                            </p>
                                            
                                            <div class="mb-3">
                                                <span class="h5 text-primary">
                                                    {{ number_format($wishlist->service->price) }} ر.س
                                                </span>
                                            </div>

                                            <div class="d-flex gap-2">
                                                <a href="{{ route('services.show', $wishlist->service) }}" 
                                                   class="btn btn-primary flex-grow-1">
                                                    <i class="fas fa-eye me-2"></i>عرض التفاصيل
                                                </a>
                                                <button type="button" 
                                                        class="btn btn-outline-danger remove-wishlist-btn" 
                                                        data-wishlist-id="{{ $wishlist->id }}"
                                                        data-service-name="{{ $wishlist->service->name }}">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-heart-broken" style="font-size: 5rem; color: #ddd;"></i>
                            <h4 class="mt-4 arabic-text text-muted">قائمة المفضلة فارغة</h4>
                            <p class="arabic-text text-muted">لم تقم بإضافة أي خدمات إلى قائمة المفضلة بعد</p>
                            <a href="{{ route('services.index') }}" class="btn btn-primary mt-3">
                                <i class="fas fa-search me-2"></i>تصفح الخدمات
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Remove from wishlist
    document.querySelectorAll('.remove-wishlist-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const wishlistId = this.dataset.wishlistId;
            const serviceName = this.dataset.serviceName;
            
            if (confirm(`هل تريد إزالة "${serviceName}" من قائمة المفضلة؟`)) {
                const deleteBtn = this;
                deleteBtn.disabled = true;
                deleteBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                
                fetch(`/wishlist/${wishlistId}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('فشل في الحذف');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        throw new Error(data.message || 'حدث خطأ');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('حدث خطأ أثناء الإزالة. الرجاء المحاولة مرة أخرى.');
                    deleteBtn.disabled = false;
                    deleteBtn.innerHTML = '<i class="fas fa-trash"></i>';
                });
            }
        });
    });
});
</script>
@endpush
@endsection

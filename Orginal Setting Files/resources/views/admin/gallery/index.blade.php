@extends('layouts.admin')

@section('title', 'إدارة المعرض - Your Events')
@section('page-title', 'إدارة المعرض')
@section('page-description', 'عرض وإدارة جميع صور وفيديوهات المعرض')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">المعرض</h2>
    <a href="{{ route('admin.gallery.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>إضافة عنصر جديد
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<!-- Filter Tabs -->
<div class="card mb-4">
    <div class="card-body">
        <ul class="nav nav-pills" id="gallery-filter" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="all-tab" data-bs-toggle="pill" data-bs-target="#all" type="button" role="tab">
                    <i class="fas fa-th me-2"></i>الكل ({{ $gallery->count() }})
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="images-tab" data-bs-toggle="pill" data-bs-target="#images" type="button" role="tab">
                    <i class="fas fa-image me-2"></i>الصور ({{ $gallery->where('type', 'image')->count() }})
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="videos-tab" data-bs-toggle="pill" data-bs-target="#videos" type="button" role="tab">
                    <i class="fas fa-video me-2"></i>الفيديوهات ({{ $gallery->where('type', 'video')->count() }})
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="featured-tab" data-bs-toggle="pill" data-bs-target="#featured" type="button" role="tab">
                    <i class="fas fa-star me-2"></i>المميزة ({{ $gallery->where('is_featured', true)->count() }})
                </button>
            </li>
        </ul>
    </div>
</div>

<div class="tab-content" id="gallery-content">
    <!-- All Items -->
    <div class="tab-pane fade show active" id="all" role="tabpanel">
        @include('admin.gallery.partials.gallery-grid', ['items' => $gallery])
    </div>
    
    <!-- Images Only -->
    <div class="tab-pane fade" id="images" role="tabpanel">
        @include('admin.gallery.partials.gallery-grid', ['items' => $gallery->where('type', 'image')])
    </div>
    
    <!-- Videos Only -->
    <div class="tab-pane fade" id="videos" role="tabpanel">
        @include('admin.gallery.partials.gallery-grid', ['items' => $gallery->where('type', 'video')])
    </div>
    
    <!-- Featured Only -->
    <div class="tab-pane fade" id="featured" role="tabpanel">
        @include('admin.gallery.partials.gallery-grid', ['items' => $gallery->where('is_featured', true)])
    </div>
</div>

<!-- Gallery Grid Template -->
@if($gallery->count() == 0)
    <div class="text-center py-5">
        <i class="fas fa-images fa-3x text-muted mb-3"></i>
        <h5>المعرض فارغ</h5>
        <p class="text-muted">ابدأ بإضافة صور أو فيديوهات للمعرض</p>
        <a href="{{ route('admin.gallery.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>إضافة عنصر جديد
        </a>
    </div>
@endif

<!-- Modal for Image Preview -->
<div class="modal fade" id="imageModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">معاينة الصورة</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <img id="modalImage" src="" alt="" class="img-fluid">
            </div>
        </div>
    </div>
</div>

<script>
// Image preview modal
document.addEventListener('DOMContentLoaded', function() {
    const imageModal = document.getElementById('imageModal');
    const modalImage = document.getElementById('modalImage');
    
    document.querySelectorAll('.gallery-image').forEach(img => {
        img.addEventListener('click', function() {
            modalImage.src = this.src;
            modalImage.alt = this.alt;
            new bootstrap.Modal(imageModal).show();
        });
    });
    
    // Toggle featured status
    document.querySelectorAll('.toggle-featured').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = this.href;
            
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = document.querySelector('meta[name="csrf-token"]').content;
            
            const methodField = document.createElement('input');
            methodField.type = 'hidden';
            methodField.name = '_method';
            methodField.value = 'PATCH';
            
            form.appendChild(csrfToken);
            form.appendChild(methodField);
            document.body.appendChild(form);
            form.submit();
        });
    });
});
</script>
@endsection
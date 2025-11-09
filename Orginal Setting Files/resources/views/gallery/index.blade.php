@extends('layouts.app')

@section('title', 'معرض أعمالنا - Your Events')

@section('content')
<!-- Page Header -->
<section class="hero-section" style="padding: 40px 0;">
    <div class="container">
        <div class="text-center">
            <h1 class="display-4 fw-bold mb-3" style="color: var(--primary-color);">معرض أعمالنا</h1>
            <p class="lead" style="color: var(--text-color);">شاهد بعض من أعمالنا المميزة وإنجازاتنا في تنظيم المناسبات</p>
        </div>
    </div>
</section>

<!-- Gallery Navigation -->
<section class="py-3 bg-light">
    <div class="container">
        <div class="text-center">
            <div class="btn-group" role="group" aria-label="Gallery filters">
                <input type="radio" class="btn-check" name="gallery-filter" id="filter-all" autocomplete="off" checked>
                <label class="btn btn-outline-primary" for="filter-all" onclick="filterGallery('all')">الكل</label>

                <input type="radio" class="btn-check" name="gallery-filter" id="filter-images" autocomplete="off">
                <label class="btn btn-outline-primary" for="filter-images" onclick="filterGallery('image')">الصور</label>

                <input type="radio" class="btn-check" name="gallery-filter" id="filter-videos" autocomplete="off">
                <label class="btn btn-outline-primary" for="filter-videos" onclick="filterGallery('video')">الفيديوهات</label>
            </div>
        </div>
    </div>
</section>

<!-- Gallery Grid -->
<section class="py-4">
    <div class="container">
        <div class="row" id="gallery-container">
            @if($images->count() > 0 || $videos->count() > 0)
                @foreach($images as $item)
                    <div class="col-lg-4 col-md-6 mb-4 gallery-item" data-type="image" data-aos="zoom-in" data-aos-delay="{{ $loop->index * 50 }}">
                        <div class="gallery-item-inner">
                            <img src="{{ Storage::url($item->file_path) }}" class="img-fluid w-100 rounded" 
                                 alt="{{ $item->title }}" style="height: 300px; object-fit: cover; cursor: pointer;"
                                 onclick="openLightbox('{{ Storage::url($item->file_path) }}', '{{ $item->title }}', 'image')">
                            <div class="gallery-overlay">
                                <i class="fas fa-search-plus"></i>
                            </div>
                            @if($item->title)
                                <div class="mt-2">
                                    <h6 class="mb-1">{{ $item->title }}</h6>
                                    @if($item->description)
                                        <p class="text-muted small">{{ Str::limit($item->description, 100) }}</p>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach

                @foreach($videos as $item)
                    <div class="col-lg-4 col-md-6 mb-4 gallery-item" data-type="video" data-aos="zoom-in" data-aos-delay="{{ ($images->count() + $loop->index) * 50 }}">
                        <div class="gallery-item-inner">
                            <div class="position-relative">
                                <video class="img-fluid w-100 rounded" style="height: 300px; object-fit: cover;" muted>
                                    <source src="{{ Storage::url($item->file_path) }}" type="video/mp4">
                                </video>
                                <div class="gallery-overlay" onclick="openLightbox('{{ Storage::url($item->file_path) }}', '{{ $item->title }}', 'video')">
                                    <i class="fas fa-play"></i>
                                </div>
                            </div>
                            @if($item->title)
                                <div class="mt-2">
                                    <h6 class="mb-1">{{ $item->title }}</h6>
                                    @if($item->description)
                                        <p class="text-muted small">{{ Str::limit($item->description, 100) }}</p>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            @else
                <div class="col-12">
                    <div class="text-center py-5">
                        <i class="fas fa-images fa-3x text-muted mb-3"></i>
                        <h4>لا توجد عناصر في المعرض حالياً</h4>
                        <p class="text-muted">نعمل على إضافة المزيد من الأعمال قريباً</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</section>

<!-- Lightbox Modal -->
<div class="modal fade" id="lightboxModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content bg-dark">
            <div class="modal-header border-0">
                <h5 class="modal-title text-white" id="lightboxTitle"></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center p-0">
                <img id="lightboxImage" class="img-fluid" style="max-height: 80vh; display: none;">
                <video id="lightboxVideo" class="img-fluid" style="max-height: 80vh; display: none;" controls>
                    <source id="lightboxVideoSource" type="video/mp4">
                </video>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
function filterGallery(type) {
    const items = document.querySelectorAll('.gallery-item');
    
    items.forEach(item => {
        if (type === 'all' || item.dataset.type === type) {
            item.style.display = 'block';
            // Re-trigger AOS animation
            item.classList.add('aos-animate');
        } else {
            item.style.display = 'none';
        }
    });
}

function openLightbox(src, title, type) {
    const modal = new bootstrap.Modal(document.getElementById('lightboxModal'));
    const lightboxTitle = document.getElementById('lightboxTitle');
    const lightboxImage = document.getElementById('lightboxImage');
    const lightboxVideo = document.getElementById('lightboxVideo');
    const lightboxVideoSource = document.getElementById('lightboxVideoSource');
    
    lightboxTitle.textContent = title || 'Your Events';
    
    if (type === 'image') {
        lightboxImage.src = src;
        lightboxImage.style.display = 'block';
        lightboxVideo.style.display = 'none';
    } else if (type === 'video') {
        lightboxVideoSource.src = src;
        lightboxVideo.load();
        lightboxVideo.style.display = 'block';
        lightboxImage.style.display = 'none';
    }
    
    modal.show();
}

// Pause video when modal is closed
document.getElementById('lightboxModal').addEventListener('hidden.bs.modal', function () {
    const lightboxVideo = document.getElementById('lightboxVideo');
    lightboxVideo.pause();
    lightboxVideo.currentTime = 0;
});
</script>
@endsection

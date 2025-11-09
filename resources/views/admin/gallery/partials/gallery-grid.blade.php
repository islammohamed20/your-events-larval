@if($items->count() > 0)
    <div class="row">
        @foreach($items as $item)
            <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                <div class="card gallery-item h-100">
                    <div class="position-relative">
                        @if($item->type == 'image')
                            <img src="{{ asset('storage/' . $item->file_path) }}" 
                                 alt="{{ $item->title }}" 
                                 class="card-img-top gallery-image" 
                                 style="height: 200px; object-fit: cover; cursor: pointer;">
                        @elseif($item->type == 'video')
                            <div class="video-thumbnail position-relative" style="height: 200px; background: #000;">
                                <video class="w-100 h-100" style="object-fit: cover;">
                                    <source src="{{ asset('storage/' . $item->file_path) }}" type="video/mp4">
                                </video>
                                <div class="position-absolute top-50 start-50 translate-middle">
                                    <i class="fas fa-play-circle fa-3x text-white opacity-75"></i>
                                </div>
                            </div>
                        @endif
                        
                        <!-- Featured Badge -->
                        @if($item->is_featured)
                            <span class="position-absolute top-0 start-0 m-2">
                                <span class="badge bg-warning">
                                    <i class="fas fa-star me-1"></i>مميز
                                </span>
                            </span>
                        @endif
                        
                        <!-- Type Badge -->
                        <span class="position-absolute top-0 end-0 m-2">
                            @if($item->type == 'image')
                                <span class="badge bg-info">
                                    <i class="fas fa-image me-1"></i>صورة
                                </span>
                            @else
                                <span class="badge bg-success">
                                    <i class="fas fa-video me-1"></i>فيديو
                                </span>
                            @endif
                        </span>
                    </div>
                    
                    <div class="card-body">
                        <h6 class="card-title mb-2">{{ $item->title ?: 'بدون عنوان' }}</h6>
                        @if($item->description)
                            <p class="card-text text-muted small mb-2">{{ Str::limit($item->description, 60) }}</p>
                        @endif
                        
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">
                                <i class="fas fa-calendar me-1"></i>
                                {{ $item->created_at->format('d/m/Y') }}
                            </small>
                            
                            <div class="btn-group" role="group">
                                <!-- Toggle Featured -->
                                <a href="{{ route('admin.gallery.toggle-featured', $item) }}" 
                                   class="btn btn-sm {{ $item->is_featured ? 'btn-warning' : 'btn-outline-warning' }} toggle-featured" 
                                   title="{{ $item->is_featured ? 'إلغاء التمييز' : 'تمييز' }}">
                                    <i class="fas fa-star"></i>
                                </a>
                                
                                <!-- Delete -->
                                <form method="POST" 
                                      action="{{ route('admin.gallery.destroy', $item) }}" 
                                      class="d-inline"
                                      onsubmit="return confirm('هل أنت متأكد من حذف هذا العنصر؟')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="btn btn-sm btn-outline-danger" 
                                            title="حذف">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@else
    <div class="text-center py-4">
        <i class="fas fa-folder-open fa-2x text-muted mb-2"></i>
        <p class="text-muted mb-0">لا توجد عناصر في هذا القسم</p>
    </div>
@endif
@extends('layouts.admin')

@section('title', 'إضافة عنصر جديد - المعرض - Your Events')
@section('page-title', 'إضافة عنصر جديد للمعرض')
@section('page-description', 'إضافة صورة أو فيديو جديد للمعرض')

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-plus me-2"></i>إضافة عنصر جديد
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.gallery.store') }}" enctype="multipart/form-data">
                    @csrf
                    
                    <!-- File Upload -->
                    <div class="mb-4">
                        <label for="file" class="form-label">الملف <span class="text-danger">*</span></label>
                        <input type="file" 
                               class="form-control @error('file') is-invalid @enderror" 
                               id="file" 
                               name="file" 
                               accept="image/*,video/*" 
                               required>
                        <div class="form-text">الصيغ المدعومة: JPG, PNG, GIF, MP4, MOV, AVI (الحد الأقصى: 50MB)</div>
                        @error('file')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        
                        <!-- File Preview -->
                        <div id="filePreview" class="mt-3" style="display: none;">
                            <div class="border rounded p-3">
                                <div id="imagePreview" style="display: none;">
                                    <img id="previewImg" src="" alt="معاينة" class="img-fluid" style="max-height: 200px;">
                                </div>
                                <div id="videoPreview" style="display: none;">
                                    <video id="previewVideo" controls class="img-fluid" style="max-height: 200px;">
                                        <source src="" type="video/mp4">
                                    </video>
                                </div>
                                <div id="fileInfo" class="mt-2">
                                    <small class="text-muted">
                                        <span id="fileName"></span> - 
                                        <span id="fileSize"></span>
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Title -->
                    <div class="mb-3">
                        <label for="title" class="form-label">العنوان</label>
                        <input type="text" 
                               class="form-control @error('title') is-invalid @enderror" 
                               id="title" 
                               name="title" 
                               value="{{ old('title') }}" 
                               placeholder="عنوان العنصر (اختياري)">
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <!-- Description -->
                    <div class="mb-3">
                        <label for="description" class="form-label">الوصف</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" 
                                  name="description" 
                                  rows="3" 
                                  placeholder="وصف العنصر (اختياري)">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <!-- Category -->
                    <div class="mb-3">
                        <label for="category" class="form-label">الفئة</label>
                        <select class="form-select @error('category') is-invalid @enderror" 
                                id="category" 
                                name="category">
                            <option value="">اختر الفئة</option>
                            <option value="events" {{ old('category') == 'events' ? 'selected' : '' }}>الفعاليات</option>
                            <option value="vr_experiences" {{ old('category') == 'vr_experiences' ? 'selected' : '' }}>تجارب الواقع الافتراضي</option>
                            <option value="behind_scenes" {{ old('category') == 'behind_scenes' ? 'selected' : '' }}>خلف الكواليس</option>
                            <option value="client_moments" {{ old('category') == 'client_moments' ? 'selected' : '' }}>لحظات العملاء</option>
                            <option value="equipment" {{ old('category') == 'equipment' ? 'selected' : '' }}>المعدات</option>
                            <option value="team" {{ old('category') == 'team' ? 'selected' : '' }}>الفريق</option>
                            <option value="other" {{ old('category') == 'other' ? 'selected' : '' }}>أخرى</option>
                        </select>
                        @error('category')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <!-- Featured -->
                    <div class="mb-4">
                        <div class="form-check">
                            <input class="form-check-input" 
                                   type="checkbox" 
                                   id="is_featured" 
                                   name="is_featured" 
                                   value="1" 
                                   {{ old('is_featured') ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_featured">
                                <i class="fas fa-star text-warning me-1"></i>
                                عنصر مميز
                            </label>
                            <div class="form-text">العناصر المميزة تظهر في المقدمة</div>
                        </div>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>حفظ العنصر
                        </button>
                        <a href="{{ route('admin.gallery.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>العودة للمعرض
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Sidebar -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="fas fa-info-circle me-2"></i>نصائح لإضافة المحتوى
                </h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <h6 class="text-primary">للصور:</h6>
                    <ul class="small text-muted mb-0">
                        <li>استخدم صور عالية الجودة (1920x1080 أو أعلى)</li>
                        <li>تأكد من وضوح الصورة وجودة الإضاءة</li>
                        <li>الصيغ المفضلة: JPG, PNG</li>
                        <li>الحد الأقصى: 10MB للصورة</li>
                    </ul>
                </div>
                
                <div class="mb-3">
                    <h6 class="text-success">للفيديوهات:</h6>
                    <ul class="small text-muted mb-0">
                        <li>جودة HD أو أعلى (1080p مفضل)</li>
                        <li>مدة مناسبة (30 ثانية - 5 دقائق)</li>
                        <li>الصيغ المدعومة: MP4, MOV, AVI</li>
                        <li>الحد الأقصى: 50MB للفيديو</li>
                    </ul>
                </div>
                
                <div class="alert alert-info">
                    <i class="fas fa-lightbulb me-2"></i>
                    <strong>نصيحة:</strong> العناصر المميزة تظهر في الصفحة الرئيسية وأعلى المعرض
                </div>
            </div>
        </div>
        
        <!-- Quick Stats -->
        <div class="card mt-3">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="fas fa-chart-bar me-2"></i>إحصائيات المعرض
                </h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6">
                        <div class="border-end">
                            <h4 class="text-primary mb-1">{{ \App\Models\Gallery::where('type', 'image')->count() }}</h4>
                            <small class="text-muted">صورة</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <h4 class="text-success mb-1">{{ \App\Models\Gallery::where('type', 'video')->count() }}</h4>
                        <small class="text-muted">فيديو</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('file');
    const filePreview = document.getElementById('filePreview');
    const imagePreview = document.getElementById('imagePreview');
    const videoPreview = document.getElementById('videoPreview');
    const previewImg = document.getElementById('previewImg');
    const previewVideo = document.getElementById('previewVideo');
    const fileName = document.getElementById('fileName');
    const fileSize = document.getElementById('fileSize');
    
    fileInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            // Show file info
            fileName.textContent = file.name;
            fileSize.textContent = formatFileSize(file.size);
            
            // Show preview
            filePreview.style.display = 'block';
            
            if (file.type.startsWith('image/')) {
                // Image preview
                imagePreview.style.display = 'block';
                videoPreview.style.display = 'none';
                
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                };
                reader.readAsDataURL(file);
            } else if (file.type.startsWith('video/')) {
                // Video preview
                imagePreview.style.display = 'none';
                videoPreview.style.display = 'block';
                
                const url = URL.createObjectURL(file);
                previewVideo.src = url;
            }
        } else {
            filePreview.style.display = 'none';
        }
    });
    
    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }
});
</script>
@endsection
@extends('layouts.admin')

@section('title', 'تعديل القسم')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">تعديل القسم: {{ $section->section_key }}</h1>
        <a href="{{ route('admin.homepage.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-right me-2"></i>رجوع
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">معلومات القسم</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.homepage.update', $section) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label">مفتاح القسم</label>
                            <input type="text" class="form-control" value="{{ $section->section_key }}" disabled>
                            <small class="text-muted">لا يمكن تعديل مفتاح القسم</small>
                        </div>

                        <div class="mb-3">
                            <label for="title" class="form-label">العنوان الرئيسي</label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                   id="title" name="title" value="{{ old('title', $section->title) }}">
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="subtitle" class="form-label">العنوان الفرعي</label>
                            <input type="text" class="form-control @error('subtitle') is-invalid @enderror" 
                                   id="subtitle" name="subtitle" value="{{ old('subtitle', $section->subtitle) }}">
                            @error('subtitle')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">وصف القسم / المحتوى النصي</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="4" 
                                      placeholder="اكتب وصفاً أو محتوى نصي للقسم...">{{ old('description', $section->content['description'] ?? '') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">يمكنك استخدام HTML للتنسيق</small>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="button_text" class="form-label">نص الزر</label>
                                <input type="text" class="form-control @error('button_text') is-invalid @enderror" 
                                       id="button_text" name="button_text" 
                                       value="{{ old('button_text', $section->content['button_text'] ?? '') }}"
                                       placeholder="عرض المزيد">
                                @error('button_text')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="button_link" class="form-label">رابط الزر</label>
                                <input type="text" class="form-control @error('button_link') is-invalid @enderror" 
                                       id="button_link" name="button_link" 
                                       value="{{ old('button_link', $section->content['button_link'] ?? '') }}"
                                       placeholder="/services">
                                @error('button_link')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="video_url" class="form-label">رابط الفيديو (اختياري)</label>
                            <input type="url" class="form-control @error('video_url') is-invalid @enderror" 
                                   id="video_url" name="video_url" value="{{ old('video_url', $section->video_url) }}"
                                   placeholder="https://www.youtube.com/watch?v=...">
                            @error('video_url')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">يدعم YouTube, Vimeo, وروابط الفيديو المباشرة</small>
                        </div>

                        <div class="mb-3">
                            <label for="image" class="form-label">صورة القسم</label>
                            @if($section->image)
                                <div class="mb-2">
                                    <img src="{{ Storage::url($section->image) }}" 
                                         alt="{{ $section->title }}" 
                                         class="img-thumbnail" 
                                         style="max-height: 150px;">
                                </div>
                            @endif
                            <input type="file" class="form-control @error('image') is-invalid @enderror" 
                                   id="image" name="image" accept="image/*">
                            @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">حد أقصى: 5MB</small>
                        </div>

                        <hr class="my-4">

                        <h6 class="mb-3">إعدادات الخلفية</h6>

                        <div class="mb-3">
                            <label class="form-label">نوع الخلفية</label>
                            <div class="btn-group w-100" role="group">
                                <input type="radio" class="btn-check" name="background_type" id="bg_color" 
                                       value="color" {{ $section->background_type == 'color' ? 'checked' : '' }}>
                                <label class="btn btn-outline-primary" for="bg_color">
                                    <i class="fas fa-palette"></i> لون
                                </label>

                                <input type="radio" class="btn-check" name="background_type" id="bg_gradient" 
                                       value="gradient" {{ $section->background_type == 'gradient' ? 'checked' : '' }}>
                                <label class="btn btn-outline-primary" for="bg_gradient">
                                    <i class="fas fa-fill-drip"></i> تدرج
                                </label>

                                <input type="radio" class="btn-check" name="background_type" id="bg_image" 
                                       value="image" {{ $section->background_type == 'image' ? 'checked' : '' }}>
                                <label class="btn btn-outline-primary" for="bg_image">
                                    <i class="fas fa-image"></i> صورة
                                </label>
                            </div>
                        </div>

                        <div id="color-input" class="mb-3" style="display: {{ $section->background_type == 'color' ? 'block' : 'none' }}">
                            <label for="background_value" class="form-label">اختر اللون</label>
                            <input type="color" class="form-control form-control-color" 
                                   id="background_value" name="background_value" 
                                   value="{{ $section->background_type == 'color' ? $section->background_value : '#ffffff' }}">
                        </div>

                        <div id="gradient-input" class="mb-3" style="display: {{ $section->background_type == 'gradient' ? 'block' : 'none' }}">
                            <label for="gradient_value" class="form-label">كود التدرج CSS</label>
                            <input type="text" class="form-control" 
                                   id="gradient_value" name="background_value" 
                                   value="{{ $section->background_type == 'gradient' ? $section->background_value : '' }}"
                                   placeholder="linear-gradient(135deg, #667eea 0%, #764ba2 100%)">
                            <small class="text-muted">مثال: linear-gradient(135deg, #667eea 0%, #764ba2 100%)</small>
                        </div>

                        <div id="image-input" class="mb-3" style="display: {{ $section->background_type == 'image' ? 'block' : 'none' }}">
                            @if($section->background_type == 'image' && $section->background_value)
                                <div class="mb-2">
                                    <img src="{{ Storage::url($section->background_value) }}" 
                                         alt="Background" 
                                         class="img-thumbnail" 
                                         style="max-height: 150px;">
                                </div>
                            @endif
                            <label for="background_image" class="form-label">اختر صورة الخلفية</label>
                            <input type="file" class="form-control" 
                                   id="background_image" name="background_image" accept="image/*">
                            <small class="text-muted">حد أقصى: 5MB</small>
                        </div>

                        <hr class="my-4">

                        @if($section->section_key === 'services')
                        <h6 class="mb-3">إعدادات خاصة بقسم الخدمات</h6>
                        
                        <div class="mb-3">
                            <label for="display_count" class="form-label">عدد الخدمات المعروضة</label>
                            <input type="number" class="form-control @error('settings.display_count') is-invalid @enderror" 
                                   id="display_count" name="settings[display_count]" 
                                   value="{{ old('settings.display_count', $section->settings['display_count'] ?? 6) }}" 
                                   min="1" max="12">
                            @error('settings.display_count')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">عدد الخدمات التي سيتم عرضها في الصفحة الرئيسية (افتراضي: 6)</small>
                        </div>

                        <hr class="my-4">
                        @endif

                        <div class="mb-3">
                            <label for="order" class="form-label">الترتيب</label>
                            <input type="number" class="form-control @error('order') is-invalid @enderror" 
                                   id="order" name="order" value="{{ old('order', $section->order) }}" min="0">
                            @error('order')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" 
                                   {{ $section->is_active ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                تفعيل القسم (إظهاره في الصفحة الرئيسية)
                            </label>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('admin.homepage.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>إلغاء
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>حفظ التغييرات
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">نصائح</h6>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <i class="fas fa-lightbulb text-warning me-2"></i>
                            استخدم عناوين واضحة وجذابة
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-lightbulb text-warning me-2"></i>
                            الصور يجب أن تكون بجودة عالية
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-lightbulb text-warning me-2"></i>
                            يمكنك استخدام مولدات التدرجات مثل 
                            <a href="https://cssgradient.io" target="_blank">cssgradient.io</a>
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-lightbulb text-warning me-2"></i>
                            للفيديو من YouTube انسخ الرابط الكامل
                        </li>
                    </ul>
                </div>
            </div>

            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">معاينة الخلفية</h6>
                </div>
                <div class="card-body">
                    <div id="bg-preview" style="height: 150px; border-radius: 8px; {{ $section->getBackgroundStyle() }}">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const bgTypeInputs = document.querySelectorAll('input[name="background_type"]');
    const colorInput = document.getElementById('color-input');
    const gradientInput = document.getElementById('gradient-input');
    const imageInput = document.getElementById('image-input');
    const bgPreview = document.getElementById('bg-preview');

    bgTypeInputs.forEach(input => {
        input.addEventListener('change', function() {
            colorInput.style.display = 'none';
            gradientInput.style.display = 'none';
            imageInput.style.display = 'none';

            if (this.value === 'color') {
                colorInput.style.display = 'block';
                updatePreview('color', document.getElementById('background_value').value);
            } else if (this.value === 'gradient') {
                gradientInput.style.display = 'block';
                updatePreview('gradient', document.getElementById('gradient_value').value);
            } else if (this.value === 'image') {
                imageInput.style.display = 'block';
            }
        });
    });

    // Update preview on color change
    document.getElementById('background_value')?.addEventListener('input', function() {
        updatePreview('color', this.value);
    });

    // Update preview on gradient change
    document.getElementById('gradient_value')?.addEventListener('input', function() {
        updatePreview('gradient', this.value);
    });

    function updatePreview(type, value) {
        if (type === 'color') {
            bgPreview.style.background = value;
            bgPreview.style.backgroundImage = 'none';
        } else if (type === 'gradient') {
            bgPreview.style.background = value;
        }
    }
    
    // Initialize TinyMCE for rich text editing
    if (document.getElementById('description')) {
        tinymce.init({
            selector: '#description',
            height: 300,
            menubar: false,
            directionality: 'rtl',
            language: 'ar',
            plugins: [
                'advlist', 'autolink', 'lists', 'link', 'image', 'charmap',
                'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
                'insertdatetime', 'media', 'table', 'help', 'wordcount'
            ],
            toolbar: 'undo redo | formatselect | bold italic underline | alignleft aligncenter alignright | bullist numlist | link image | code',
            content_style: 'body { font-family: Arial, sans-serif; font-size: 14px; direction: rtl; }'
        });
    }
});
</script>
@endpush
@endsection

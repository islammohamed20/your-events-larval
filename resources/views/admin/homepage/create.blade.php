@extends('layouts.admin')

@section('title', 'إضافة قسم جديد')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">إضافة قسم جديد</h1>
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
                    <form action="{{ route('admin.homepage.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-3">
                            <label for="section_key" class="form-label">مفتاح القسم (بالإنجليزية) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('section_key') is-invalid @enderror" 
                                   id="section_key" name="section_key" value="{{ old('section_key') }}" required
                                   placeholder="مثال: custom_section_1">
                            @error('section_key')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">يجب أن يكون فريداً، يستخدم في الكود</small>
                        </div>

                        <div class="mb-3">
                            <label for="title" class="form-label">العنوان الرئيسي</label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                   id="title" name="title" value="{{ old('title') }}">
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="subtitle" class="form-label">العنوان الفرعي</label>
                            <input type="text" class="form-control @error('subtitle') is-invalid @enderror" 
                                   id="subtitle" name="subtitle" value="{{ old('subtitle') }}">
                            @error('subtitle')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="video_url" class="form-label">رابط الفيديو (اختياري)</label>
                            <input type="url" class="form-control @error('video_url') is-invalid @enderror" 
                                   id="video_url" name="video_url" value="{{ old('video_url') }}"
                                   placeholder="https://www.youtube.com/watch?v=...">
                            @error('video_url')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">يدعم YouTube, Vimeo, وروابط الفيديو المباشرة</small>
                        </div>

                        <div class="mb-3">
                            <label for="image" class="form-label">صورة القسم</label>
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
                            <label class="form-label">نوع الخلفية <span class="text-danger">*</span></label>
                            <div class="btn-group w-100" role="group">
                                <input type="radio" class="btn-check" name="background_type" id="bg_color" 
                                       value="color" checked>
                                <label class="btn btn-outline-primary" for="bg_color">
                                    <i class="fas fa-palette"></i> لون
                                </label>

                                <input type="radio" class="btn-check" name="background_type" id="bg_gradient" 
                                       value="gradient">
                                <label class="btn btn-outline-primary" for="bg_gradient">
                                    <i class="fas fa-fill-drip"></i> تدرج
                                </label>

                                <input type="radio" class="btn-check" name="background_type" id="bg_image" 
                                       value="image">
                                <label class="btn btn-outline-primary" for="bg_image">
                                    <i class="fas fa-image"></i> صورة
                                </label>
                            </div>
                        </div>

                        <div id="color-input" class="mb-3">
                            <label for="background_value" class="form-label">اختر اللون</label>
                            <input type="color" class="form-control form-control-color" 
                                   id="background_value" name="background_value" value="#ffffff">
                        </div>

                        <div id="gradient-input" class="mb-3" style="display: none;">
                            <label for="gradient_value" class="form-label">كود التدرج CSS</label>
                            <input type="text" class="form-control" 
                                   id="gradient_value" name="background_value"
                                   placeholder="linear-gradient(135deg, #667eea 0%, #764ba2 100%)">
                            <small class="text-muted">مثال: linear-gradient(135deg, #667eea 0%, #764ba2 100%)</small>
                        </div>

                        <div id="image-input" class="mb-3" style="display: none;">
                            <label for="background_image" class="form-label">اختر صورة الخلفية</label>
                            <input type="file" class="form-control" 
                                   id="background_image" name="background_image" accept="image/*">
                            <small class="text-muted">حد أقصى: 5MB</small>
                        </div>

                        <hr class="my-4">

                        <div class="mb-3">
                            <label for="order" class="form-label">الترتيب</label>
                            <input type="number" class="form-control @error('order') is-invalid @enderror" 
                                   id="order" name="order" value="{{ old('order', 0) }}" min="0">
                            @error('order')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">الأقسام ذات الأرقام الأقل تظهر أولاً</small>
                        </div>

                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" checked>
                            <label class="form-check-label" for="is_active">
                                تفعيل القسم (إظهاره في الصفحة الرئيسية)
                            </label>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('admin.homepage.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>إلغاء
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>حفظ القسم
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
                            مفتاح القسم يجب أن يكون بالإنجليزية بدون مسافات
                        </li>
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
                    </ul>
                </div>
            </div>

            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">معاينة الخلفية</h6>
                </div>
                <div class="card-body">
                    <div id="bg-preview" style="height: 150px; border-radius: 8px; background: #ffffff;">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
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
});
</script>
@endpush
@endsection

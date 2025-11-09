@extends('layouts.admin')

@section('title', 'إضافة فئة جديدة')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">إضافة فئة جديدة</h1>
        <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-right me-2"></i>العودة للقائمة
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-body">
                    <form action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-3">
                            <label for="name" class="form-label">اسم الفئة <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name') }}" 
                                   required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="name_en" class="form-label">الاسم بالإنجليزية (اختياري)</label>
                            <input type="text" 
                                   class="form-control @error('name_en') is-invalid @enderror" 
                                   id="name_en" 
                                   name="name_en" 
                                   value="{{ old('name_en') }}">
                            @error('name_en')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">الوصف</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" 
                                      name="description" 
                                      rows="4">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="icon" class="form-label">الأيقونة (FontAwesome)</label>
                                <input type="text" 
                                       class="form-control @error('icon') is-invalid @enderror" 
                                       id="icon" 
                                       name="icon" 
                                       value="{{ old('icon') }}"
                                       placeholder="مثال: fas fa-calendar-alt">
                                <small class="form-text text-muted">
                                    <a href="https://fontawesome.com/icons" target="_blank">اختر من هنا</a>
                                </small>
                                @error('icon')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="color" class="form-label">اللون</label>
                                <input type="color" 
                                       class="form-control form-control-color @error('color') is-invalid @enderror" 
                                       id="color" 
                                       name="color" 
                                       value="{{ old('color', '#1f144a') }}">
                                @error('color')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="image" class="form-label">صورة الفئة</label>
                            <input type="file" 
                                   class="form-control @error('image') is-invalid @enderror" 
                                   id="image" 
                                   name="image"
                                   accept="image/*"
                                   onchange="previewImage(event)">
                            @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div id="imagePreview" class="mt-3" style="display: none;">
                                <img id="preview" src="" alt="Preview" class="img-thumbnail" style="max-width: 200px;">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="order" class="form-label">الترتيب</label>
                                <input type="number" 
                                       class="form-control @error('order') is-invalid @enderror" 
                                       id="order" 
                                       name="order" 
                                       value="{{ old('order', 0) }}"
                                       min="0">
                                <small class="form-text text-muted">الأرقام الأصغر تظهر أولاً</small>
                                @error('order')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="is_active" class="form-label">الحالة</label>
                                <select class="form-select @error('is_active') is-invalid @enderror" 
                                        id="is_active" 
                                        name="is_active">
                                    <option value="1" {{ old('is_active', '1') == '1' ? 'selected' : '' }}>نشطة</option>
                                    <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>معطلة</option>
                                </select>
                                @error('is_active')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>حفظ الفئة
                            </button>
                            <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">
                                إلغاء
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="card-title mb-0">معلومات إضافية</h5>
                </div>
                <div class="card-body">
                    <h6><i class="fas fa-info-circle text-primary me-2"></i>نصائح:</h6>
                    <ul class="small">
                        <li>اختر اسماً واضحاً ومميزاً للفئة</li>
                        <li>الأيقونة تساعد في التمييز البصري بين الفئات</li>
                        <li>الترتيب يحدد موقع الفئة في القوائم</li>
                        <li>يمكنك تعطيل الفئة مؤقتاً دون حذفها</li>
                    </ul>

                    <hr>

                    <h6><i class="fas fa-palette text-primary me-2"></i>أمثلة للأيقونات:</h6>
                    <div class="d-flex flex-wrap gap-2">
                        <span class="badge bg-light text-dark" onclick="setIcon('fas fa-calendar-alt')">
                            <i class="fas fa-calendar-alt"></i> أفراح
                        </span>
                        <span class="badge bg-light text-dark" onclick="setIcon('fas fa-briefcase')">
                            <i class="fas fa-briefcase"></i> مؤتمرات
                        </span>
                        <span class="badge bg-light text-dark" onclick="setIcon('fas fa-birthday-cake')">
                            <i class="fas fa-birthday-cake"></i> حفلات
                        </span>
                        <span class="badge bg-light text-dark" onclick="setIcon('fas fa-users')">
                            <i class="fas fa-users"></i> اجتماعية
                        </span>
                        <span class="badge bg-light text-dark" onclick="setIcon('fas fa-store')">
                            <i class="fas fa-store"></i> معارض
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function previewImage(event) {
    const preview = document.getElementById('preview');
    const previewDiv = document.getElementById('imagePreview');
    const file = event.target.files[0];
    
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            previewDiv.style.display = 'block';
        }
        reader.readAsDataURL(file);
    }
}

function setIcon(iconClass) {
    document.getElementById('icon').value = iconClass;
}
</script>
@endsection

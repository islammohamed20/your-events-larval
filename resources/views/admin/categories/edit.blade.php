@extends('layouts.admin')

@section('title', 'تعديل الفئة')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">تعديل الفئة: {{ $category->name }}</h1>
        <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-right me-2"></i>العودة للقائمة
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-body">
                    <form action="{{ route('admin.categories.update', $category) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="name" class="form-label">اسم الفئة <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name', $category->name) }}" 
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
                                   value="{{ old('name_en', $category->name_en) }}">
                            @error('name_en')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="supplier_form_name" class="form-label">اسم الفئة (الاسم الذي يظهر في فورم المورد)</label>
                            <input type="text"
                                   class="form-control @error('supplier_form_name') is-invalid @enderror"
                                   id="supplier_form_name"
                                   name="supplier_form_name"
                                   value="{{ old('supplier_form_name', $category->supplier_form_name) }}">
                            @error('supplier_form_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="book_from_service" class="form-label">الحجز من الخدمة نفسها</label>
                            <select class="form-select @error('book_from_service') is-invalid @enderror"
                                    id="book_from_service"
                                    name="book_from_service">
                                <option value="0" {{ old('book_from_service', (int) ($category->book_from_service ?? 0)) == '0' ? 'selected' : '' }}>غير مفعل</option>
                                <option value="1" {{ old('book_from_service', (int) ($category->book_from_service ?? 0)) == '1' ? 'selected' : '' }}>مفعل</option>
                            </select>
                            <small class="text-muted d-block mt-1">عند التفعيل: يظهر تقويم داخل صفحة الخدمة ويُمنع حجز نفس اليوم أكثر من مرة.</small>
                            @error('book_from_service')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">الوصف</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" 
                                      name="description" 
                                      rows="4">{{ old('description', $category->description) }}</textarea>
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
                                       value="{{ old('icon', $category->icon) }}"
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
                                       value="{{ old('color', $category->color) }}">
                                @error('color')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">أيقونة PNG</label>
                                @if($category->icon_png)
                                    <div class="mb-2 position-relative d-inline-block">
                                        <img src="{{ Storage::url($category->icon_png) }}" alt="PNG Icon" class="img-thumbnail" style="max-width: 64px;">
                                        <button type="button" 
                                                class="btn btn-danger btn-sm position-absolute top-0 end-0" 
                                                onclick="deletePngIcon()"
                                                style="margin: 5px;">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        <p class="text-muted small mt-2">الأيقونة الحالية</p>
                                    </div>
                                    <input type="hidden" name="delete_icon_png" id="delete_icon_png" value="0">
                                @endif
                                <input type="file" 
                                       class="form-control @error('icon_png') is-invalid @enderror" 
                                       id="icon_png" 
                                       name="icon_png"
                                       accept="image/png">
                                <small class="text-muted">يُقبل فقط PNG. يُفضّل 64×64 أو 128×128</small>
                                @error('icon_png')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="banner" class="form-label">صورة البانر (966×205 بكسل)</label>
                            @if($category->banner)
                                <div class="mb-2 position-relative d-inline-block w-100">
                                    <img src="{{ Storage::url($category->banner) }}" alt="{{ $category->name }} Banner" class="img-thumbnail" style="max-width: 100%; height: auto;">
                                    <button type="button" 
                                            class="btn btn-danger btn-sm position-absolute top-0 end-0" 
                                            onclick="deleteBanner()"
                                            style="margin: 5px;">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    <p class="text-muted small mt-2">البانر الحالي</p>
                                </div>
                                <input type="hidden" name="delete_banner" id="delete_banner" value="0">
                            @endif
                            <input type="file" 
                                   class="form-control @error('banner') is-invalid @enderror" 
                                   id="banner" 
                                   name="banner"
                                   accept="image/*"
                                   onchange="previewBanner(event)">
                            <small class="text-muted d-block mt-2">
                                <i class="fas fa-info-circle me-1"></i>الحجم الموصى به: 966×205 بكسل<br>
                                سيتم عرضه عند اختيار هذه الفئة من الفلاتر
                            </small>
                            @error('banner')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <div id="bannerPreview" class="mt-3" style="display: none;">
                                <img id="bannerImg" src="" alt="Banner Preview" class="img-thumbnail" style="max-width: 100%; height: auto;">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="image" class="form-label">صورة الفئة</label>
                            @if($category->image)
                                <div class="mb-2 position-relative d-inline-block">
                                    <img src="{{ Storage::url($category->image) }}" alt="{{ $category->name }}" class="img-thumbnail" style="max-width: 150px;">
                                    <button type="button" 
                                            class="btn btn-danger btn-sm position-absolute top-0 end-0" 
                                            onclick="deleteImage()"
                                            style="margin: 5px;">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    <p class="text-muted small mt-2">الصورة الحالية</p>
                                </div>
                                <input type="hidden" name="delete_image" id="delete_image" value="0">
                            @endif
                            <input type="file" 
                                   class="form-control @error('image') is-invalid @enderror" 
                                   id="image" 
                                   name="image"
                                   accept="image/*"
                                   onchange="previewImage(event)">
                            <small class="text-muted">اترك فارغاً للاحتفاظ بالصورة الحالية</small>
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
                                       value="{{ old('order', $category->order) }}"
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
                                    <option value="1" {{ old('is_active', $category->is_active) == '1' ? 'selected' : '' }}>نشطة</option>
                                    <option value="0" {{ old('is_active', $category->is_active) == '0' ? 'selected' : '' }}>معطلة</option>
                                </select>
                                @error('is_active')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>تحديث الفئة
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

function deleteImage() {
    if (confirm('هل أنت متأكد من حذف الصورة؟')) {
        document.getElementById('delete_image').value = '1';
        // Hide the image preview
        const imgContainer = event.target.closest('.position-relative');
        imgContainer.style.display = 'none';
        
        // Show confirmation message
        const label = document.querySelector('label[for="image"]');
        const confirmMsg = document.createElement('div');
        confirmMsg.className = 'alert alert-warning alert-dismissible fade show mt-2';
        confirmMsg.innerHTML = `
            <i class="fas fa-exclamation-triangle me-2"></i>
            سيتم حذف الصورة عند حفظ التعديلات
            <button type="button" class="btn-close" onclick="cancelDeleteImage(this)"></button>
        `;
        label.insertAdjacentElement('afterend', confirmMsg);
    }
}

function deletePngIcon() {
    if (confirm('هل أنت متأكد من حذف أيقونة PNG؟')) {
        document.getElementById('delete_icon_png').value = '1';
        const imgContainer = event.target.closest('.position-relative');
        imgContainer.style.display = 'none';
        const label = document.querySelector('label.form-label');
        const confirmMsg = document.createElement('div');
        confirmMsg.className = 'alert alert-warning alert-dismissible fade show mt-2';
        confirmMsg.innerHTML = '<i class="fas fa-info-circle me-2"></i> سيتم حذف الأيقونة عند الحفظ';
        imgContainer.parentElement.appendChild(confirmMsg);
    }
}

function cancelDeleteImage(btn) {
    document.getElementById('delete_image').value = '0';
    const imgContainer = document.querySelector('.position-relative.d-inline-block');
    if (imgContainer) {
        imgContainer.style.display = 'inline-block';
    }
    btn.closest('.alert').remove();
}

function previewBanner(event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('bannerImg').src = e.target.result;
            document.getElementById('bannerPreview').style.display = 'block';
        }
        reader.readAsDataURL(file);
    }
}

function deleteBanner() {
    if (confirm('هل أنت متأكد من حذف البانر؟')) {
        document.getElementById('delete_banner').value = '1';
        // Hide the banner preview
        const bannerContainer = event.target.closest('.position-relative');
        bannerContainer.style.display = 'none';
        
        // Show confirmation message
        const label = document.querySelector('label[for="banner"]');
        const confirmMsg = document.createElement('div');
        confirmMsg.className = 'alert alert-warning alert-dismissible fade show mt-2';
        confirmMsg.innerHTML = `
            <i class="fas fa-exclamation-triangle me-2"></i>سيتم حذف البانر عند الحفظ.
            <button type="button" onclick="cancelDeleteBanner(this)" class="btn btn-sm btn-link">تراجع</button>
        `;
        label.parentElement.appendChild(confirmMsg);
    }
}

function cancelDeleteBanner(btn) {
    document.getElementById('delete_banner').value = '0';
    const bannerContainer = document.querySelector('input[name="banner"]').previousElementSibling;
    if (bannerContainer && bannerContainer.classList.contains('position-relative')) {
        bannerContainer.style.display = 'inline-block';
    }
    btn.closest('.alert').remove();
}
</script>
@endsection

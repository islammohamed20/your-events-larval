@extends('layouts.admin')

@section('title', 'إضافة خدمة جديدة - Your Events')
@section('page-title', 'إضافة خدمة جديدة')
@section('page-description', 'إضافة خدمة جديدة للموقع')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-plus me-2"></i>إضافة خدمة جديدة
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.services.store') }}" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name" class="form-label">اسم الخدمة *</label>
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
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="price" class="form-label">السعر (ريال) *</label>
                                <input type="number" 
                                       class="form-control @error('price') is-invalid @enderror" 
                                       id="price" 
                                       name="price" 
                                       value="{{ old('price') }}" 
                                       min="0" 
                                       step="0.01" 
                                       required>
                                @error('price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="type" class="form-label">نوع الخدمة</label>
                                <select class="form-select @error('type') is-invalid @enderror" 
                                        id="type" 
                                        name="type">
                                    <option value="">اختر نوع الخدمة</option>
                                    <option value="تصوير" {{ old('type') == 'تصوير' ? 'selected' : '' }}>تصوير</option>
                                    <option value="تنظيم" {{ old('type') == 'تنظيم' ? 'selected' : '' }}>تنظيم</option>
                                    <option value="ديكور" {{ old('type') == 'ديكور' ? 'selected' : '' }}>ديكور</option>
                                    <option value="ضيافة" {{ old('type') == 'ضيافة' ? 'selected' : '' }}>ضيافة</option>
                                    <option value="ترفيه" {{ old('type') == 'ترفيه' ? 'selected' : '' }}>ترفيه</option>
                                    <option value="أخرى" {{ old('type') == 'أخرى' ? 'selected' : '' }}>أخرى</option>
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="duration" class="form-label">مدة الخدمة</label>
                                <input type="text" 
                                       class="form-control @error('duration') is-invalid @enderror" 
                                       id="duration" 
                                       name="duration" 
                                       value="{{ old('duration') }}" 
                                       placeholder="مثال: 4 ساعات">
                                @error('duration')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">وصف الخدمة *</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" 
                                  name="description" 
                                  rows="4" 
                                  required>{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="features" class="form-label">مميزات الخدمة</label>
                        <div id="features-container">
                            <div class="input-group mb-2">
                                <input type="text" class="form-control" name="features[]" placeholder="أدخل ميزة">
                                <button type="button" class="btn btn-outline-danger remove-feature" style="display: none;">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                        </div>
                        <button type="button" class="btn btn-outline-primary btn-sm" id="add-feature">
                            <i class="fas fa-plus me-1"></i>إضافة ميزة
                        </button>
                    </div>
                    
                    <div class="mb-3">
                        <label for="image" class="form-label">صورة الخدمة</label>
                        <input type="file" 
                               class="form-control @error('image') is-invalid @enderror" 
                               id="image" 
                               name="image" 
                               accept="image/*">
                        @error('image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">الحد الأقصى: 2MB، الصيغ المدعومة: JPG, PNG, GIF</div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" 
                                   type="checkbox" 
                                   id="is_active" 
                                   name="is_active" 
                                   value="1" 
                                   {{ old('is_active', true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                تفعيل الخدمة
                            </label>
                        </div>
                    </div>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>حفظ الخدمة
                        </button>
                        <a href="{{ route('admin.services.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>العودة
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="fas fa-info-circle me-2"></i>نصائح
                </h6>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    <li class="mb-2">
                        <i class="fas fa-check text-success me-2"></i>
                        اختر اسماً واضحاً ومميزاً للخدمة
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-check text-success me-2"></i>
                        اكتب وصفاً شاملاً يوضح تفاصيل الخدمة
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-check text-success me-2"></i>
                        حدد نوع الخدمة لتسهيل التصنيف
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-check text-success me-2"></i>
                        أضف صورة تمثل الخدمة
                    </li>
                    <li class="mb-0">
                        <i class="fas fa-check text-success me-2"></i>
                        حدد السعر والمدة بدقة
                    </li>
                </ul>
            </div>
        </div>
        
        <div class="card mt-3">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="fas fa-lightbulb me-2"></i>أمثلة على الخدمات
                </h6>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0 small">
                    <li class="mb-1">• تصوير فوتوغرافي احترافي</li>
                    <li class="mb-1">• تنظيم حفلات الزفاف</li>
                    <li class="mb-1">• ديكور وتنسيق المكان</li>
                    <li class="mb-1">• خدمات الضيافة والطعام</li>
                    <li class="mb-0">• برامج ترفيهية متنوعة</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const addFeatureBtn = document.getElementById('add-feature');
    const featuresContainer = document.getElementById('features-container');
    
    addFeatureBtn.addEventListener('click', function() {
        const newFeature = document.createElement('div');
        newFeature.className = 'input-group mb-2';
        newFeature.innerHTML = `
            <input type="text" class="form-control" name="features[]" placeholder="أدخل ميزة">
            <button type="button" class="btn btn-outline-danger remove-feature">
                <i class="fas fa-minus"></i>
            </button>
        `;
        
        featuresContainer.appendChild(newFeature);
        updateRemoveButtons();
    });
    
    featuresContainer.addEventListener('click', function(e) {
        if (e.target.closest('.remove-feature')) {
            e.target.closest('.input-group').remove();
            updateRemoveButtons();
        }
    });
    
    function updateRemoveButtons() {
        const removeButtons = featuresContainer.querySelectorAll('.remove-feature');
        removeButtons.forEach((btn, index) => {
            btn.style.display = featuresContainer.children.length > 1 ? 'block' : 'none';
        });
    }
});
</script>
@endsection
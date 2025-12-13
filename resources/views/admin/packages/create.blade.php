@extends('layouts.admin')

@section('title', 'إضافة باقة جديدة')
@section('page-title', 'إضافة باقة جديدة')
@section('page-description', 'إضافة باقة جديدة للموقع')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-plus me-2"></i>إضافة باقة جديدة
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.packages.store') }}" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name" class="form-label">اسم الباقة *</label>
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
                                <label for="persons_min" class="form-label">
                                    <i class="fas fa-users me-1"></i>عدد الأشخاص (من)
                                </label>
                                <input type="number" 
                                       class="form-control @error('persons_min') is-invalid @enderror" 
                                       id="persons_min" 
                                       name="persons_min" 
                                       value="{{ old('persons_min') }}" 
                                       min="1"
                                       placeholder="مثال: 50">
                                @error('persons_min')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="persons_max" class="form-label">
                                    <i class="fas fa-users me-1"></i>عدد الأشخاص (إلى)
                                </label>
                                <input type="number" 
                                       class="form-control @error('persons_max') is-invalid @enderror" 
                                       id="persons_max" 
                                       name="persons_max" 
                                       value="{{ old('persons_max') }}" 
                                       min="1"
                                       placeholder="مثال: 90">
                                @error('persons_max')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">مثال: 50 إلى 90 شخص</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">وصف الباقة *</label>
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
                        <label for="features" class="form-label">مميزات الباقة</label>
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
                    
                    <!-- خواص الباقة -->
                    <div class="mb-4">
                        <label class="form-label">
                            <i class="fas fa-list-alt me-1 text-primary"></i>
                            خواص الباقة (اختياري)
                        </label>
                        <p class="text-muted small mb-3">أضف خواص مفصلة للباقة مع وصف وتفاصيل كل خاصية</p>
                        
                        <div id="attributes-container">
                            <!-- سيتم إضافة الخواص هنا -->
                        </div>
                        
                        <button type="button" class="btn btn-outline-success btn-sm" id="add-attribute">
                            <i class="fas fa-plus me-1"></i>إضافة خاصية جديدة
                        </button>
                    </div>
                    
                    <div class="mb-3">
                        <label for="image" class="form-label">صورة الباقة الرئيسية</label>
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
                    
                    <!-- معرض الصور المتعددة -->
                    <div class="mb-4">
                        <label class="form-label">
                            <i class="fas fa-images me-1 text-primary"></i>
                            معرض صور الباقة (اختياري)
                        </label>
                        <p class="text-muted small mb-3">يمكنك إضافة عدة صور للباقة. أول صورة ستكون الصورة المصغرة</p>
                        
                        <input type="file" 
                               class="form-control @error('images') is-invalid @enderror @error('images.*') is-invalid @enderror" 
                               name="images[]" 
                               accept="image/*"
                               multiple>
                        @error('images')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        @error('images.*')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">يمكنك اختيار عدة صور. الحد الأقصى لكل صورة: 2MB</div>
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
                                تفعيل الباقة
                            </label>
                        </div>
                    </div>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>حفظ الباقة
                        </button>
                        <a href="{{ route('admin.packages.index') }}" class="btn btn-secondary">
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
                        اختر اسماً واضحاً ومميزاً للباقة
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-check text-success me-2"></i>
                        اكتب وصفاً شاملاً يوضح ما تتضمنه الباقة
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-check text-success me-2"></i>
                        أضف صورة عالية الجودة تمثل الباقة
                    </li>
                    <li class="mb-0">
                        <i class="fas fa-check text-success me-2"></i>
                        حدد السعر بدقة
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // === Features Management ===
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
    
    // === Attributes Management ===
    const addAttributeBtn = document.getElementById('add-attribute');
    const attributesContainer = document.getElementById('attributes-container');
    let attributeIndex = 0;
    
    function createAttributeCard(index, data = {}) {
        const card = document.createElement('div');
        card.className = 'card mb-3 attribute-card border-primary';
        const isVisible = data.visible !== false;
        card.innerHTML = `
            <div class="card-header bg-light d-flex justify-content-between align-items-center py-2">
                <span class="fw-bold text-primary">
                    <i class="fas fa-cog me-1"></i>
                    خاصية #${index + 1}
                </span>
                <button type="button" class="btn btn-sm btn-outline-danger remove-attribute">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-10 mb-3">
                        <label class="form-label">اسم الخاصية *</label>
                        <input type="text" 
                               class="form-control" 
                               name="attributes[${index}][name]" 
                               value="${data.name || ''}"
                               placeholder="مثال: الديكور، الضيافة، التصوير...">
                    </div>
                    <div class="col-md-2 mb-3">
                        <label class="form-label">الظهور</label>
                        <div class="form-check form-switch mt-2">
                            <input class="form-check-input" type="checkbox" 
                                   name="attributes[${index}][visible]" 
                                   value="1"
                                   ${isVisible ? 'checked' : ''}>
                            <label class="form-check-label small">إظهار</label>
                        </div>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label class="form-label">وصف الخاصية</label>
                        <input type="text" 
                               class="form-control" 
                               name="attributes[${index}][description]" 
                               value="${data.description || ''}"
                               placeholder="وصف مختصر للخاصية">
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">التفاصيل</label>
                        <textarea class="form-control" 
                                  name="attributes[${index}][details]" 
                                  rows="3"
                                  placeholder="تفاصيل إضافية عن هذه الخاصية...">${data.details || ''}</textarea>
                    </div>
                </div>
            </div>
        `;
        return card;
    }
    
    addAttributeBtn.addEventListener('click', function() {
        const card = createAttributeCard(attributeIndex);
        attributesContainer.appendChild(card);
        attributeIndex++;
    });
    
    attributesContainer.addEventListener('click', function(e) {
        if (e.target.closest('.remove-attribute')) {
            e.target.closest('.attribute-card').remove();
            // إعادة ترقيم الخواص
            reindexAttributes();
        }
    });
    
    function reindexAttributes() {
        const cards = attributesContainer.querySelectorAll('.attribute-card');
        cards.forEach((card, idx) => {
            card.querySelector('.card-header span').innerHTML = `
                <i class="fas fa-cog me-1"></i>
                خاصية #${idx + 1}
            `;
            card.querySelectorAll('input, textarea').forEach(input => {
                const name = input.getAttribute('name');
                if (name) {
                    input.setAttribute('name', name.replace(/attributes\[\d+\]/, `attributes[${idx}]`));
                }
            });
        });
        attributeIndex = cards.length;
    }
});
</script>
@endsection
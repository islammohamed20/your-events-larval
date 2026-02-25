@extends('layouts.admin')

@section('title', __('common.admin_create_package'))
@section('page-title', __('common.admin_create_package'))
@section('page-description', __('common.admin_create_package_description'))

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-plus me-2"></i>{{ __('common.admin_create_package') }}
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.packages.store') }}" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name" class="form-label">{{ __('common.package_name') }} *</label>
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
                                <label for="price" class="form-label">{{ __('common.price') }} ({{ __('common.currency') }}) *</label>
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
                                    <i class="fas fa-users me-1"></i>{{ __('common.persons_from') }}
                                </label>
                                <input type="number" 
                                       class="form-control @error('persons_min') is-invalid @enderror" 
                                       id="persons_min" 
                                       name="persons_min" 
                                       value="{{ old('persons_min') }}" 
                                       min="1"
                                       placeholder="{{ __('common.example') }}: 50">
                                @error('persons_min')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="persons_max" class="form-label">
                                    <i class="fas fa-users me-1"></i>{{ __('common.persons_to') }}
                                </label>
                                <input type="number" 
                                       class="form-control @error('persons_max') is-invalid @enderror" 
                                       id="persons_max" 
                                       name="persons_max" 
                                       value="{{ old('persons_max') }}" 
                                       min="1"
                                       placeholder="{{ __('common.example') }}: 90">
                                @error('persons_max')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">{{ __('common.example') }}: 50 {{ __('common.to') }} 90 {{ __('common.person') }}</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">{{ __('common.package_description') }} *</label>
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
                        <label for="features" class="form-label">{{ __('common.package_features') }}</label>
                        <div id="features-container">
                            <div class="input-group mb-2">
                                <input type="text" class="form-control" name="features[]" placeholder="{{ __('common.enter_feature') }}">
                                <button type="button" class="btn btn-outline-danger remove-feature" style="display: none;">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                        </div>
                        <button type="button" class="btn btn-outline-primary btn-sm" id="add-feature">
                            <i class="fas fa-plus me-1"></i>{{ __('common.add_feature') }}
                        </button>
                    </div>
                    
                    <!-- خواص الباقة -->
                    <div class="mb-4">
                        <label class="form-label">
                            <i class="fas fa-list-alt me-1 text-primary"></i>
                            {{ __('common.package_attributes_optional') }}
                        </label>
                        <p class="text-muted small mb-3">{{ __('common.package_attributes_hint') }}</p>
                        
                        <div id="attributes-container">
                            <!-- سيتم إضافة الخواص هنا -->
                        </div>
                        
                        <button type="button" class="btn btn-outline-success btn-sm" id="add-attribute">
                            <i class="fas fa-plus me-1"></i>{{ __('common.add_attribute') }}
                        </button>
                    </div>
                    
                    <div class="mb-3">
                        <label for="image" class="form-label">{{ __('common.package_main_image_legacy') }}</label>
                        <input type="file" 
                               class="form-control @error('image') is-invalid @enderror" 
                               id="image" 
                               name="image" 
                               accept="image/*">
                        @error('image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">{{ __('common.package_image_limits') }}</div>
                    </div>
                    
                    <!-- معرض الصور المتعددة -->
                    <div class="mb-4">
                        <label class="form-label">
                            <i class="fas fa-images me-1 text-primary"></i>
                            {{ __('common.package_images_gallery') }} ({{ __('common.optional') }})
                        </label>
                        <p class="text-muted small mb-3">{{ __('common.package_images_create_hint') }}</p>
                        
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
                        <div class="form-text">{{ __('common.select_multiple_images_hint') }}</div>
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
                                {{ __('common.activate_package') }}
                            </label>
                        </div>
                    </div>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>{{ __('common.save_package') }}
                        </button>
                        <a href="{{ route('admin.packages.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>{{ __('common.back') }}
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
                    <i class="fas fa-info-circle me-2"></i>{{ __('common.tips') }}
                </h6>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    <li class="mb-2">
                        <i class="fas fa-check text-success me-2"></i>
                        {{ __('common.tip_choose_package_name') }}
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-check text-success me-2"></i>
                        {{ __('common.tip_write_package_description') }}
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-check text-success me-2"></i>
                        {{ __('common.tip_add_high_quality_image') }}
                    </li>
                    <li class="mb-0">
                        <i class="fas fa-check text-success me-2"></i>
                        {{ __('common.tip_set_price_accurately') }}
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div id="package-translations"
     style="display: none;"
     data-enter-feature="{{ __('common.enter_feature') }}"
     data-attribute-number-template="{{ __('common.attribute_number', ['number' => ':number']) }}"
     data-attribute-name="{{ __('common.attribute_name') }}"
     data-attribute-name-example="{{ __('common.attribute_name_example') }}"
     data-visibility="{{ __('common.visibility') }}"
     data-show="{{ __('common.show') }}"
     data-attribute-description="{{ __('common.attribute_description') }}"
     data-attribute-description-placeholder="{{ __('common.attribute_description_placeholder') }}"
     data-details="{{ __('common.details') }}"
     data-attribute-details-placeholder="{{ __('common.attribute_details_placeholder') }}">
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const translationsEl = document.getElementById('package-translations');
    if (!translationsEl) {
        return;
    }

    const enterFeatureText = translationsEl.dataset.enterFeature || '';
    const attributeNumberText = translationsEl.dataset.attributeNumberTemplate || '';
    const attributeNameText = translationsEl.dataset.attributeName || '';
    const attributeNameExampleText = translationsEl.dataset.attributeNameExample || '';
    const visibilityText = translationsEl.dataset.visibility || '';
    const showText = translationsEl.dataset.show || '';
    const attributeDescriptionText = translationsEl.dataset.attributeDescription || '';
    const attributeDescriptionPlaceholderText = translationsEl.dataset.attributeDescriptionPlaceholder || '';
    const detailsText = translationsEl.dataset.details || '';
    const attributeDetailsPlaceholderText = translationsEl.dataset.attributeDetailsPlaceholder || '';
    // === Features Management ===
    const addFeatureBtn = document.getElementById('add-feature');
    const featuresContainer = document.getElementById('features-container');
    
    addFeatureBtn.addEventListener('click', function() {
        const newFeature = document.createElement('div');
        newFeature.className = 'input-group mb-2';
        newFeature.innerHTML = `
            <input type="text" class="form-control" name="features[]" placeholder="${enterFeatureText}">
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
                    ${attributeNumberText.replace(':number', String(index + 1))}
                </span>
                <button type="button" class="btn btn-sm btn-outline-danger remove-attribute">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-10 mb-3">
                        <label class="form-label">${attributeNameText} *</label>
                        <input type="text" 
                               class="form-control" 
                               name="attributes[${index}][name]" 
                               value="${data.name || ''}"
                               placeholder="${attributeNameExampleText}">
                    </div>
                    <div class="col-md-2 mb-3">
                        <label class="form-label">${visibilityText}</label>
                        <div class="form-check form-switch mt-2">
                            <input class="form-check-input" type="checkbox" 
                                   name="attributes[${index}][visible]" 
                                   value="1"
                                   ${isVisible ? 'checked' : ''}>
                            <label class="form-check-label small">${showText}</label>
                        </div>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label class="form-label">${attributeDescriptionText}</label>
                        <input type="text" 
                               class="form-control" 
                               name="attributes[${index}][description]" 
                               value="${data.description || ''}"
                               placeholder="${attributeDescriptionPlaceholderText}">
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">${detailsText}</label>
                        <textarea class="form-control" 
                                  name="attributes[${index}][details]" 
                                  rows="3"
                                  placeholder="${attributeDetailsPlaceholderText}">${data.details || ''}</textarea>
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
                ${attributeNumberText.replace(':number', String(idx + 1))}
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

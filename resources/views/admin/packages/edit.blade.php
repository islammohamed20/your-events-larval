@extends('layouts.admin')

@section('title', __('common.admin_edit_package'))
@section('page-title', __('common.admin_edit_package_with_name', ['name' => $package->name]))
@section('page-description', __('common.admin_edit_package_description'))

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-edit me-2"></i>{{ __('common.admin_edit_package') }}
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.packages.update', $package) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name" class="form-label">{{ __('common.package_name') }} *</label>
                                <input type="text" 
                                       class="form-control @error('name') is-invalid @enderror" 
                                       id="name" 
                                       name="name" 
                                       value="{{ old('name', $package->name) }}" 
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
                                       value="{{ old('price', $package->price) }}" 
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
                                       value="{{ old('persons_min', $package->persons_min) }}" 
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
                                       value="{{ old('persons_max', $package->persons_max) }}" 
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
                                  required>{{ old('description', $package->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="features" class="form-label">{{ __('common.package_features') }}</label>
                        <div id="features-container">
                            @if(old('features') || $package->features)
                                @foreach(old('features', $package->features ?? []) as $feature)
                                    <div class="input-group mb-2">
                                        <input type="text" class="form-control" name="features[]" value="{{ $feature }}" placeholder="{{ __('common.enter_feature') }}">
                                        <button type="button" class="btn btn-outline-danger remove-feature">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                    </div>
                                @endforeach
                            @else
                                <div class="input-group mb-2">
                                    <input type="text" class="form-control" name="features[]" placeholder="{{ __('common.enter_feature') }}">
                                    <button type="button" class="btn btn-outline-danger remove-feature" style="display: none;">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </div>
                            @endif
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
                            @if($package->attributes)
                                @foreach($package->attributes as $index => $attr)
                                <div class="card mb-3 attribute-card border-primary">
                                    <div class="card-header bg-light d-flex justify-content-between align-items-center py-2">
                                        <span class="fw-bold text-primary">
                                            <i class="fas fa-cog me-1"></i>
                                            {{ __('common.attribute_number', ['number' => $index + 1]) }}
                                        </span>
                                        <button type="button" class="btn btn-sm btn-outline-danger remove-attribute">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-10 mb-3">
                                                <label class="form-label">{{ __('common.attribute_name') }} *</label>
                                                <input type="text" 
                                                       class="form-control" 
                                                       name="attributes[{{ $index }}][name]" 
                                                       value="{{ $attr['name'] ?? '' }}"
                                                       placeholder="{{ __('common.attribute_name_example') }}">
                                            </div>
                                            <div class="col-md-2 mb-3">
                                                <label class="form-label">{{ __('common.visibility') }}</label>
                                                <div class="form-check form-switch mt-2">
                                                    <input class="form-check-input" type="checkbox" 
                                                           name="attributes[{{ $index }}][visible]" 
                                                           value="1"
                                                           {{ ($attr['visible'] ?? true) ? 'checked' : '' }}>
                                                    <label class="form-check-label small">{{ __('common.show') }}</label>
                                                </div>
                                            </div>
                                            <div class="col-md-12 mb-3">
                                                <label class="form-label">{{ __('common.attribute_description') }}</label>
                                                <input type="text" 
                                                       class="form-control" 
                                                       name="attributes[{{ $index }}][description]" 
                                                       value="{{ $attr['description'] ?? '' }}"
                                                       placeholder="{{ __('common.attribute_description_placeholder') }}">
                                            </div>
                                            <div class="col-md-12">
                                                <label class="form-label">{{ __('common.details') }}</label>
                                                <textarea class="form-control" 
                                                          name="attributes[{{ $index }}][details]" 
                                                          rows="3"
                                                          placeholder="{{ __('common.attribute_details_placeholder') }}">{{ $attr['details'] ?? '' }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            @endif
                        </div>
                        
                        <button type="button" class="btn btn-outline-success btn-sm" id="add-attribute">
                            <i class="fas fa-plus me-1"></i>{{ __('common.add_attribute') }}
                        </button>
                    </div>
                    
                    <div class="mb-3">
                        <label for="image" class="form-label">{{ __('common.package_main_image_legacy') }}</label>
                        @if($package->image)
                            <div class="mb-2">
                                <img src="{{ asset('storage/' . $package->image) }}" 
                                     alt="{{ $package->name }}" 
                                     class="img-thumbnail" 
                                     style="max-width: 200px;">
                                <div class="form-text">{{ __('common.current_image_legacy') }}</div>
                            </div>
                        @endif
                        <input type="file" 
                               class="form-control @error('image') is-invalid @enderror" 
                               id="image" 
                               name="image" 
                               accept="image/*">
                        @error('image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">{{ __('common.keep_empty_to_keep_current_image') }}</div>
                    </div>
                    
                    <!-- معرض الصور المتعددة -->
                    <div class="mb-4">
                        <label class="form-label">
                            <i class="fas fa-images me-1 text-primary"></i>
                            {{ __('common.package_images_gallery') }}
                        </label>
                        <p class="text-muted small mb-3">{{ __('common.package_images_gallery_hint') }}</p>
                        
                        @if($package->images->count() > 0)
                            <div class="row mb-3" id="images-gallery">
                                @foreach($package->images as $img)
                                    <div class="col-md-3 mb-3 image-item" data-image-id="{{ $img->id }}">
                                        <div class="card h-100 {{ $img->is_thumbnail ? 'border-success border-2' : '' }}">
                                            <img src="{{ $img->image_url }}" 
                                                 class="card-img-top" 
                                                 alt="{{ $img->alt_text }}"
                                                 style="height: 120px; object-fit: cover;">
                                            <div class="card-body p-2 text-center">
                                                @if($img->is_thumbnail)
                                                    <span class="badge bg-success mb-2">
                                                        <i class="fas fa-star me-1"></i>{{ __('common.thumbnail') }}
                                                    </span>
                                                @else
                                                    <button type="button" 
                                                            class="btn btn-sm btn-outline-success mb-2 set-thumbnail-btn"
                                                            data-image-id="{{ $img->id }}">
                                                        <i class="fas fa-star me-1"></i>{{ __('common.set_as_thumbnail') }}
                                                    </button>
                                                @endif
                                                <div>
                                                    <label class="form-check">
                                                        <input type="checkbox" 
                                                               name="delete_images[]" 
                                                               value="{{ $img->id }}" 
                                                               class="form-check-input">
                                                        <span class="form-check-label small text-danger">
                                                            <i class="fas fa-trash me-1"></i>{{ __('common.delete') }}
                                                        </span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                        
                        <div class="mb-3">
                            <label class="form-label">{{ __('common.add_new_images') }}</label>
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
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" 
                                   type="checkbox" 
                                   id="is_active" 
                                   name="is_active" 
                                   value="1" 
                                   {{ old('is_active', $package->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                {{ __('common.activate_package') }}
                            </label>
                        </div>
                    </div>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>{{ __('common.save_changes') }}
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
                    <i class="fas fa-info-circle me-2"></i>{{ __('common.package_information') }}
                </h6>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    <li class="mb-2">
                        <strong>{{ __('common.created_at') }}:</strong><br>
                        <small class="text-muted">{{ $package->created_at->format('d/m/Y H:i') }}</small>
                    </li>
                    <li class="mb-2">
                        <strong>{{ __('common.updated_at') }}:</strong><br>
                        <small class="text-muted">{{ $package->updated_at->format('d/m/Y H:i') }}</small>
                    </li>
                    <li class="mb-0">
                        <strong>{{ __('common.status') }}:</strong><br>
                        @if($package->is_active)
                            <span class="badge bg-success">{{ __('common.active') }}</span>
                        @else
                            <span class="badge bg-secondary">{{ __('common.inactive') }}</span>
                        @endif
                    </li>
                </ul>
            </div>
        </div>
        
        <div class="card mt-3">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="fas fa-chart-bar me-2"></i>{{ __('common.statistics') }}
                </h6>
            </div>
            <div class="card-body">
                <div class="text-center">
                    <h4 class="text-primary">{{ $package->bookings_count ?? 0 }}</h4>
                    <p class="text-muted mb-0">{{ __('common.total_bookings') }}</p>
                </div>
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
     data-attribute-details-placeholder="{{ __('common.attribute_details_placeholder') }}"
     data-error-occurred="{{ __('common.error_occurred') }}"
     data-connection-error="{{ __('common.connection_error') }}"
     data-attribute-count="{{ count($package->attributes ?? []) }}"
     data-package-id="{{ $package->id }}"
     data-csrf-token="{{ csrf_token() }}">
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
    const errorOccurredText = translationsEl.dataset.errorOccurred || '';
    const connectionErrorText = translationsEl.dataset.connectionError || '';
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
    
    // Initialize remove buttons visibility
    updateRemoveButtons();
    
    // === Attributes Management ===
    const addAttributeBtn = document.getElementById('add-attribute');
    const attributesContainer = document.getElementById('attributes-container');
    let attributeIndex = parseInt(translationsEl.dataset.attributeCount || '0', 10);
    
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
    
    // === Set Thumbnail ===
    document.querySelectorAll('.set-thumbnail-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const imageId = this.dataset.imageId;
            const packageId = translationsEl.dataset.packageId;
            const csrfToken = translationsEl.dataset.csrfToken;
            
            fetch(`/admin/packages/${packageId}/images/${imageId}/set-thumbnail`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Reload page to update UI
                    window.location.reload();
                } else {
                    alert(data.message || errorOccurredText);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert(connectionErrorText);
            });
        });
    });
});
</script>
@endsection

@extends('layouts.admin')

@section('title', 'تعديل الباقة - Your Events')
@section('page-title', 'تعديل الباقة: ' . $package->name)
@section('page-description', 'تعديل بيانات الباقة')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-edit me-2"></i>تعديل الباقة
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.packages.update', $package) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name" class="form-label">اسم الباقة *</label>
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
                                <label for="price" class="form-label">السعر (ريال) *</label>
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
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">وصف الباقة *</label>
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
                        <label for="features" class="form-label">مميزات الباقة</label>
                        <div id="features-container">
                            @if(old('features') || $package->features)
                                @foreach(old('features', $package->features ?? []) as $feature)
                                    <div class="input-group mb-2">
                                        <input type="text" class="form-control" name="features[]" value="{{ $feature }}" placeholder="أدخل ميزة">
                                        <button type="button" class="btn btn-outline-danger remove-feature">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                    </div>
                                @endforeach
                            @else
                                <div class="input-group mb-2">
                                    <input type="text" class="form-control" name="features[]" placeholder="أدخل ميزة">
                                    <button type="button" class="btn btn-outline-danger remove-feature" style="display: none;">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </div>
                            @endif
                        </div>
                        <button type="button" class="btn btn-outline-primary btn-sm" id="add-feature">
                            <i class="fas fa-plus me-1"></i>إضافة ميزة
                        </button>
                    </div>
                    
                    <div class="mb-3">
                        <label for="image" class="form-label">صورة الباقة</label>
                        @if($package->image)
                            <div class="mb-2">
                                <img src="{{ asset('storage/' . $package->image) }}" 
                                     alt="{{ $package->name }}" 
                                     class="img-thumbnail" 
                                     style="max-width: 200px;">
                                <div class="form-text">الصورة الحالية</div>
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
                        <div class="form-text">اتركه فارغاً للاحتفاظ بالصورة الحالية. الحد الأقصى: 2MB، الصيغ المدعومة: JPG, PNG, GIF</div>
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
                                تفعيل الباقة
                            </label>
                        </div>
                    </div>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>حفظ التغييرات
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
                    <i class="fas fa-info-circle me-2"></i>معلومات الباقة
                </h6>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    <li class="mb-2">
                        <strong>تاريخ الإنشاء:</strong><br>
                        <small class="text-muted">{{ $package->created_at->format('d/m/Y H:i') }}</small>
                    </li>
                    <li class="mb-2">
                        <strong>آخر تحديث:</strong><br>
                        <small class="text-muted">{{ $package->updated_at->format('d/m/Y H:i') }}</small>
                    </li>
                    <li class="mb-0">
                        <strong>الحالة:</strong><br>
                        @if($package->is_active)
                            <span class="badge bg-success">نشط</span>
                        @else
                            <span class="badge bg-secondary">غير نشط</span>
                        @endif
                    </li>
                </ul>
            </div>
        </div>
        
        <div class="card mt-3">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="fas fa-chart-bar me-2"></i>إحصائيات
                </h6>
            </div>
            <div class="card-body">
                <div class="text-center">
                    <h4 class="text-primary">{{ $package->bookings_count ?? 0 }}</h4>
                    <p class="text-muted mb-0">إجمالي الحجوزات</p>
                </div>
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
    
    // Initialize remove buttons visibility
    updateRemoveButtons();
});
</script>
@endsection
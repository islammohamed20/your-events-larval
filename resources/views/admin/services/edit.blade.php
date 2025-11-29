@extends('layouts.admin')

@section('title', 'تعديل الخدمة - Your Events')
@section('page-title', 'تعديل الخدمة: ' . $service->name)
@section('page-description', 'تعديل بيانات الخدمة')

@section('styles')
<style>
    .service-tabs .nav-link {
        border-radius: 10px 10px 0 0;
        font-weight: 500;
        padding: 12px 20px;
        color: #6c757d;
        border: 1px solid transparent;
    }
    
    .service-tabs .nav-link.active {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-color: #667eea;
    }
    
    .service-tabs .nav-link:hover:not(.active) {
        background-color: #f8f9fa;
    }
    
    .attributes-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 15px;
        margin-top: 15px;
    }
    
    .attribute-checkbox {
        border: 2px solid #e9ecef;
        padding: 15px;
        border-radius: 8px;
        transition: all 0.3s;
        cursor: pointer;
    }
    
    .attribute-checkbox:hover {
        border-color: #667eea;
        background-color: #f8f9ff;
    }
    
    .attribute-checkbox input:checked + label {
        border-color: #667eea;
        background-color: #f0f2ff;
    }
</style>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-edit me-2"></i>تعديل الخدمة
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.services.update', $service) }}" enctype="multipart/form-data" id="serviceForm" data-service-id="{{ $service->id }}">
                    @csrf
                    @method('PUT')
                    
                    @php
                        // تحديد نوع الخدمة الحالي - التعامل مع الخدمات القديمة
                        $currentServiceType = old('service_type', $service->service_type ?? ($service->attributes->count() > 0 ? 'variable' : 'simple'));
                        $selectedAttributes = old('attributes', $service->attributes->pluck('id')->toArray());
                        // تفعيل تبويب التسعير تلقائياً عند وجود أخطاء فيه
                        $activeTab = (
                            $errors->has('price') ||
                            $errors->has('service_type') ||
                            $errors->has('attributes')
                        ) ? 'pricing' : 'basic';
                    @endphp
                    
                    <!-- Tabs Navigation -->
                    <ul class="nav nav-tabs service-tabs mb-4" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link {{ $activeTab === 'basic' ? 'active' : '' }}" id="basic-tab" data-bs-toggle="tab" data-bs-target="#basic" type="button" role="tab">
                                <i class="fas fa-info-circle me-2"></i>المعلومات الأساسية
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link {{ $activeTab === 'pricing' ? 'active' : '' }}" id="pricing-tab" data-bs-toggle="tab" data-bs-target="#pricing" type="button" role="tab">
                                <i class="fas fa-dollar-sign me-2"></i>التسعير والخيارات
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="features-tab" data-bs-toggle="tab" data-bs-target="#features" type="button" role="tab">
                                <i class="fas fa-star me-2"></i>المميزات والحقول
                            </button>
                        </li>
                    </ul>

                    <!-- Tabs Content -->
                    <div class="tab-content">
                        <!-- Basic Info Tab -->
                        <div class="tab-pane fade {{ $activeTab === 'basic' ? 'show active' : '' }}" id="basic" role="tabpanel">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="category_id" class="form-label">الفئة</label>
                                        <select class="form-select @error('category_id') is-invalid @enderror" 
                                                id="category_id" 
                                                name="category_id">
                                            <option value="">-- اختر الفئة --</option>
                                            @foreach(\App\Models\Category::active()->ordered()->get() as $category)
                                                <option value="{{ $category->id }}" {{ old('category_id', $service->category_id) == $category->id ? 'selected' : '' }}>
                                                    {{ $category->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('category_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">اسم الخدمة <span class="text-danger">*</span></label>
                                        <input type="text" 
                                               class="form-control @error('name') is-invalid @enderror" 
                                               id="name" 
                                               name="name" 
                                               value="{{ old('name', $service->name) }}" 
                                               required>
                                        @error('name')
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
                                            <option value="تصوير" {{ old('type', $service->type) == 'تصوير' ? 'selected' : '' }}>تصوير</option>
                                            <option value="تنظيم" {{ old('type', $service->type) == 'تنظيم' ? 'selected' : '' }}>تنظيم</option>
                                            <option value="ديكور" {{ old('type', $service->type) == 'ديكور' ? 'selected' : '' }}>ديكور</option>
                                            <option value="ضيافة" {{ old('type', $service->type) == 'ضيافة' ? 'selected' : '' }}>ضيافة</option>
                                            <option value="ترفيه" {{ old('type', $service->type) == 'ترفيه' ? 'selected' : '' }}>ترفيه</option>
                                            <option value="أخرى" {{ old('type', $service->type) == 'أخرى' ? 'selected' : '' }}>أخرى</option>
                                        </select>
                                        @error('type')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="duration" class="form-label">مدة الخدمة (بالساعات)</label>
                                        <input type="number" 
                                               class="form-control @error('duration') is-invalid @enderror" 
                                               id="duration" 
                                               name="duration" 
                                               value="{{ old('duration', $service->duration) }}" 
                                               min="0" step="1" 
                                               placeholder="مثال: 4">
                                        @error('duration')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            
                            <!-- حقل العنوان الفرعي (يظهر فقط لفئة الألعاب) -->
                            <div class="mb-3" id="subtitle-field" style="display: none;">
                                <label for="subtitle" class="form-label">
                                    <i class="fas fa-heading text-info me-1"></i>عنوان فرعي (يوضح نوع الجهاز لخدمة SEO)
                                </label>
                                <input type="text" 
                                       class="form-control @error('subtitle') is-invalid @enderror" 
                                       id="subtitle" 
                                       name="subtitle" 
                                       value="{{ old('subtitle', $service->subtitle) }}" 
                                       placeholder="مثال: PlayStation 5, Xbox Series X, PC Gaming">
                                <small class="form-text text-muted">
                                    <i class="fas fa-info-circle me-1"></i>يساعد في تحسين ظهور الخدمة في محركات البحث
                                </small>
                                @error('subtitle')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="mb-3" id="description-field">
                                <label for="description" class="form-label">وصف الخدمة</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" 
                                          id="description" 
                                          name="description" 
                                          rows="3">{{ old('description', $service->description) }}</textarea>
                                <small class="form-text text-muted">وصف قصير للخدمة (للاستخدام الداخلي)</small>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- الحقول التسويقية الجديدة -->
                            <div class="card border-primary mb-3">
                                <div class="card-header bg-primary text-white">
                                    <h6 class="mb-0">
                                        <i class="fas fa-bullhorn me-2"></i>المحتوى التسويقي
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="marketing_description" class="form-label">
                                            <i class="fas fa-align-left text-primary me-1"></i>الوصف
                                        </label>
                                        <textarea class="form-control @error('marketing_description') is-invalid @enderror" 
                                                  id="marketing_description" 
                                                  name="marketing_description" 
                                                  rows="4" 
                                                  placeholder="اكتب وصفاً للخدمة...">{{ old('marketing_description', $service->marketing_description) }}</textarea>
                                        <small class="form-text text-muted">هذا الوصف سيظهر في صفحة تفاصيل الخدمة</small>
                                        @error('marketing_description')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="what_we_offer" class="form-label">
                                            <i class="fas fa-gift text-primary me-1"></i>وش نوفر؟
                                        </label>
                                        <textarea class="form-control @error('what_we_offer') is-invalid @enderror" 
                                                  id="what_we_offer" 
                                                  name="what_we_offer" 
                                                  rows="5" 
                                                  placeholder="اكتب ما نوفره للعملاء في هذه الخدمة...">{{ old('what_we_offer', $service->what_we_offer) }}</textarea>
                                        <small class="form-text text-muted">اذكر ما نقدمه من مميزات وخدمات</small>
                                        @error('what_we_offer')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="why_choose_us" class="form-label">
                                            <i class="fas fa-star text-primary me-1"></i>ليش تختار Your Events؟
                                        </label>
                                        <textarea class="form-control @error('why_choose_us') is-invalid @enderror" 
                                                  id="why_choose_us" 
                                                  name="why_choose_us" 
                                                  rows="5" 
                                                  placeholder="اكتب لماذا يجب على العملاء اختيار Your Events...">{{ old('why_choose_us', $service->why_choose_us) }}</textarea>
                                        <small class="form-text text-muted">اذكر المميزات التنافسية التي تميزنا</small>
                                        @error('why_choose_us')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="mb-0">
                                        <label for="meta_description" class="form-label">
                                            <i class="fas fa-search text-primary me-1"></i>وصف SEO (Meta Description)
                                        </label>
                                        <textarea class="form-control @error('meta_description') is-invalid @enderror" 
                                                  id="meta_description" 
                                                  name="meta_description" 
                                                  rows="2" 
                                                  maxlength="160"
                                                  placeholder="وصف مختصر يظهر في نتائج محركات البحث (160 حرف كحد أقصى)">{{ old('meta_description', $service->meta_description) }}</textarea>
                                        <small class="form-text text-muted">
                                            <span id="meta-char-count">{{ strlen($service->meta_description ?? '') }}</span>/160 حرف
                                        </small>
                                        @error('meta_description')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            
                            <!-- معرض الصور -->
                            <div class="mb-4">
                                <label class="form-label">معرض صور الخدمة</label>
                                
                                @if($service->images->count() > 0)
                                <div class="row g-3 mb-3" id="existing-images">
                                    @foreach($service->images as $img)
                                    <div class="col-md-3" id="image-{{ $img->id }}">
                                        <div class="card">
                                            <img src="{{ $img->image_url }}" class="card-img-top" alt="{{ $service->name }}" style="height: 150px; object-fit: cover;">
                                            <div class="card-body p-2">
                                                <div class="d-flex gap-1 justify-content-between align-items-center">
                                                    @if($img->is_thumbnail)
                                                        <span class="badge bg-success"><i class="fas fa-star"></i> مصغرة</span>
                                                    @else
                                                        <button type="button" class="btn btn-sm btn-outline-primary set-thumbnail-btn" data-image-id="{{ $img->id }}">
                                                            <i class="fas fa-star"></i> جعلها مصغرة
                                                        </button>
                                                    @endif
                                                    <button type="button" class="btn btn-sm btn-outline-danger delete-image-btn" data-image-id="{{ $img->id }}">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                                @else
                                <div class="alert alert-warning mb-3">
                                    <i class="fas fa-info-circle me-2"></i>لا توجد صور مرفوعة. أضف صوراً جديدة أدناه.
                                </div>
                                @endif

                                <!-- إضافة صور جديدة -->
                                <div class="mb-3">
                                    <label for="images" class="form-label">إضافة صور جديدة</label>
                                    <input type="file" 
                                           class="form-control @error('images.*') is-invalid @enderror" 
                                           id="images" 
                                           name="images[]" 
                                           accept="image/*"
                                           multiple>
                                    @error('images.*')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">يمكنك اختيار عدة صور مرة واحدة. الحد الأقصى: 2MB لكل صورة، الصيغ المدعومة: JPG, PNG, GIF</div>
                                </div>

                                <!-- معاينة الصور الجديدة -->
                                <div id="image-preview" class="row g-3 mt-2" style="display: none;"></div>
                            </div>

                            <!-- الصورة القديمة (للتوافق مع الخدمات القديمة) -->
                            @if($service->image && $service->images->count() === 0)
                            <div class="mb-3">
                                <label class="form-label">الصورة الحالية (النظام القديم)</label>
                                <div class="mb-2">
                                    <img src="{{ asset('storage/' . $service->image) }}" 
                                         alt="{{ $service->name }}" 
                                         class="img-thumbnail" 
                                         style="max-width: 200px;">
                                    <div class="form-text text-muted">سيتم استبدالها عند رفع صور جديدة</div>
                                </div>
                            </div>
                            @endif

                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" 
                                           type="checkbox" 
                                           id="is_active" 
                                           name="is_active" 
                                           value="1" 
                                           {{ old('is_active', $service->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        تفعيل الخدمة
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Pricing & Options Tab -->
                        <div class="tab-pane fade {{ $activeTab === 'pricing' ? 'show active' : '' }}" id="pricing" role="tabpanel">
                            <div class="row mb-4">
                                <div class="col-12">
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle me-2"></i>
                                        <strong>نوع التسعير:</strong> اختر هل الخدمة لها سعر ثابت أم أسعار متغيرة حسب الخيارات
                                    </div>
                                    
                                    @if($service->variations->count() > 0)
                                        <div class="alert alert-warning">
                                            <i class="fas fa-exclamation-triangle me-2"></i>
                                            <strong>تنبيه:</strong> هذه الخدمة تحتوي على {{ $service->variations->count() }} تنويعة موجودة مسبقاً. 
                                            تغيير نوع الخدمة قد يؤثر على التنويعات الحالية.
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <div class="card border-primary h-100">
                                        <div class="card-body">
                                            <div class="form-check mb-3">
                                                <input class="form-check-input" 
                                                       type="radio" 
                                                       name="service_type" 
                                                       id="simple_service" 
                                                       value="simple" 
                                                       {{ $currentServiceType == 'simple' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="simple_service">
                                                    <strong><i class="fas fa-tag me-2"></i>خدمة بسيطة - سعر ثابت</strong>
                                                </label>
                                            </div>
                                            <p class="text-muted small">مناسب للخدمات التي لها سعر واحد ثابت لا يتغير</p>
                                            
                                            <div id="simple_price_section" style="display: none;">
                                                <label for="price" class="form-label">السعر (ريال) <span class="text-danger">*</span></label>
                                                <input type="number" 
                                                       class="form-control @error('price') is-invalid @enderror" 
                                                       id="price" 
                                                       name="price" 
                                                       value="{{ old('price', $service->price) }}" 
                                                       min="0" 
                                                       step="0.01"
                                                       placeholder="مثال: 5000">
                                                @error('price')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="card border-success h-100">
                                        <div class="card-body">
                                            <div class="form-check mb-3">
                                                <input class="form-check-input" 
                                                       type="radio" 
                                                       name="service_type" 
                                                       id="variable_service" 
                                                       value="variable"
                                                       {{ $currentServiceType == 'variable' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="variable_service">
                                                    <strong><i class="fas fa-sliders-h me-2"></i>خدمة متغيرة - خيارات متعددة</strong>
                                                </label>
                                            </div>
                                            <p class="text-muted small">للخدمات التي يختلف سعرها حسب الخيارات (عدد الأشخاص، المدينة، إلخ)</p>
                                            
                                            @if($service->variations->count() > 0)
                                                <div class="mt-3">
                                                    <span class="badge bg-info">{{ $service->variations->count() }} تنويعة موجودة</span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Variable Service Options -->
                            <div id="variable_options_section" style="display: none;">
                                <div class="card bg-light">
                                    <div class="card-header bg-success text-white">
                                        <h6 class="mb-0">
                                            <i class="fas fa-tags me-2"></i>خصائص الخدمة المتغيرة
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <p class="mb-3">اختر الخصائص التي ستؤثر على سعر الخدمة:</p>
                                        
                                        @php
                                            $attributes = \App\Models\Attribute::active()->ordered()->with('values')->get();
                                        @endphp
                                        
                                        @if($attributes->count() > 0)
                                            <div class="attributes-grid">
                                                @foreach($attributes as $attribute)
                                                    <div class="attribute-checkbox">
                                                        <input type="checkbox" 
                                                               class="form-check-input me-2" 
                                                               name="attributes[]" 
                                                               value="{{ $attribute->id }}"
                                                               id="attr_{{ $attribute->id }}"
                                                               {{ in_array($attribute->id, $selectedAttributes) ? 'checked' : '' }}>
                                                        <label for="attr_{{ $attribute->id }}" class="form-check-label">
                                                            <strong>{{ $attribute->name }}</strong>
                                                            <br>
                                                            <small class="text-muted">{{ $attribute->values->count() }} قيمة</small>
                                                        </label>
                                                    </div>
                                                @endforeach
                                            </div>
                                            
                                            @if($service->variations->count() > 0)
                                                <div class="alert alert-info mt-3">
                                                    <i class="fas fa-info-circle me-2"></i>
                                                    <strong>الخصائص الحالية:</strong>
                                                    @foreach($service->attributes as $attr)
                                                        <span class="badge bg-primary me-1">{{ $attr->name }}</span>
                                                    @endforeach
                                                </div>
                                            @endif
                                        @else
                                            <div class="alert alert-info">
                                                <i class="fas fa-info-circle me-2"></i>
                                                لا توجد خصائص متاحة حالياً. 
                                                <a href="{{ route('admin.attributes.create') }}" target="_blank" class="alert-link">
                                                    انقر هنا لإنشاء خاصية جديدة
                                                </a>
                                                <br><small>مثال: عدد الأشخاص، المدينة، نوع القاعة</small>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Features & Fields Tab -->
                        <div class="tab-pane fade" id="features" role="tabpanel">
                            <div class="mb-4">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-star me-2"></i>مميزات الخدمة
                                </label>
                                <div id="features-container">
                                    @if(old('features') || $service->features)
                                        @foreach(old('features', $service->features ?? []) as $feature)
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
                                <div class="form-text">مثال: تصوير احترافي، معدات حديثة، ضمان الجودة</div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-list me-2"></i>الحقول المخصصة
                                </label>
                                <div id="custom-fields-container" data-initial-index="{{ count(old('custom_fields', $service->custom_fields ?? [])) }}">
                                    @php
                                        $customFields = old('custom_fields', $service->custom_fields ?? []);
                                    @endphp
                                    @if(!empty($customFields))
                                        @foreach($customFields as $index => $field)
                                            <div class="border rounded p-3 mb-2">
                                                <div class="row g-2">
                                                    <div class="col-md-4">
                                                        <input type="text" class="form-control" name="custom_fields[{{ $index }}][label]" value="{{ $field['label'] ?? '' }}" placeholder="اسم الحقل (مثال: اللون)">
                                                    </div>
                                                    <div class="col-md-3">
                                                        <select class="form-select" name="custom_fields[{{ $index }}][type]">
                                                            <option value="single" {{ ($field['type'] ?? '') == 'single' ? 'selected' : '' }}>اختيار واحد</option>
                                                            <option value="multiple" {{ ($field['type'] ?? '') == 'multiple' ? 'selected' : '' }}>عدة اختيارات</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-4">
                                                        @php
                                                            $optionsValue = $field['options'] ?? '';
                                                            if (is_array($optionsValue)) {
                                                                $optionsValue = implode(', ', $optionsValue);
                                                            }
                                                        @endphp
                                                        <input type="text" class="form-control" name="custom_fields[{{ $index }}][options]" value="{{ $optionsValue }}" placeholder="أحمر, أزرق, أخضر" dir="rtl">
                                                        <small class="text-muted d-block" dir="rtl">
                                                            اكتب الخيارات وافصل بينها بفاصلة إنجليزية (,) بدون مسافات<br>
                                                            <strong>مثال:</strong> <code dir="ltr">أحمر,أزرق,أخضر</code>
                                                        </small>
                                                    </div>
                                                    <div class="col-md-1 d-grid">
                                                        <button type="button" class="btn btn-outline-danger remove-custom-field" @if(count($customFields) <= 1) style="display:none" @endif>
                                                            <i class="fas fa-minus"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="border rounded p-3 mb-2">
                                            <div class="row g-2">
                                                <div class="col-md-4">
                                                    <input type="text" class="form-control" name="custom_fields[0][label]" placeholder="اسم الحقل (مثال: اللون)">
                                                </div>
                                                <div class="col-md-3">
                                                    <select class="form-select" name="custom_fields[0][type]">
                                                        <option value="single">اختيار واحد</option>
                                                        <option value="multiple">عدة اختيارات</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-4">
                                                    <input type="text" class="form-control" name="custom_fields[0][options]" placeholder="أحمر, أزرق, أخضر" dir="rtl">
                                                    <small class="text-muted d-block" dir="rtl">
                                                        اكتب الخيارات وافصل بينها بفاصلة إنجليزية (,) بدون مسافات<br>
                                                        <strong>مثال:</strong> <code dir="ltr">أحمر,أزرق,أخضر</code>
                                                    </small>
                                                </div>
                                                <div class="col-md-1 d-grid">
                                                    <button type="button" class="btn btn-outline-danger remove-custom-field" style="display:none">
                                                        <i class="fas fa-minus"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                <button type="button" class="btn btn-outline-primary btn-sm" id="add-custom-field">
                                    <i class="fas fa-plus me-1"></i>إضافة حقل
                                </button>
                                <div class="form-text">حقول إضافية يمكن للعميل تخصيصها عند الحجز</div>
                            </div>
                        </div>
                    </div>
                    
                    <hr class="my-4">
                    
                    <div class="d-flex justify-content-between">
                        <div>
                            <a href="{{ route('admin.services.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>العودة
                            </a>
                            @if($service->service_type === 'variable')
                                <a href="{{ route('admin.services.variations.index', $service) }}" class="btn btn-info">
                                    <i class="fas fa-layer-group me-2"></i>إدارة التنويعات
                                    @if($service->variations->count() > 0)
                                        <span class="badge bg-light text-dark">{{ $service->variations->count() }}</span>
                                    @endif
                                </a>
                            @endif
                        </div>
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-save me-2"></i>حفظ التغييرات
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Meta Description Character Counter
    const metaDescInput = document.getElementById('meta_description');
    const charCount = document.getElementById('meta-char-count');
    
    if (metaDescInput && charCount) {
        metaDescInput.addEventListener('input', function() {
            charCount.textContent = this.value.length;
            if (this.value.length > 160) {
                charCount.style.color = 'red';
            } else {
                charCount.style.color = '';
            }
        });
    }
    
    // Category-based field toggle (الألعاب category)
    const categorySelect = document.getElementById('category_id');
    const subtitleField = document.getElementById('subtitle-field');
    const descriptionField = document.getElementById('description-field');
    const descriptionInput = document.getElementById('description');
    // تم جعل الوصف اختيارياً دائماً؛ لا حاجة لمؤشر إلزام
    
    function toggleCategoryFields() {
        const selectedCategoryId = categorySelect.value;
        const gamesCategory = '2'; // ID فئة الألعاب
        
        if (selectedCategoryId === gamesCategory) {
            // إظهار حقل العنوان الفرعي
            subtitleField.style.display = 'block';
            // إخفاء حقل الوصف (اختياري دائماً)
            descriptionField.style.display = 'none';
        } else {
            // إخفاء حقل العنوان الفرعي
            subtitleField.style.display = 'none';
            // إظهار حقل الوصف (اختياري دائماً)
            descriptionField.style.display = 'block';
        }
    }
    
    if (categorySelect) {
        categorySelect.addEventListener('change', toggleCategoryFields);
        // Initialize on page load
        toggleCategoryFields();
    }
    
    // Service Type Toggle
    const simpleRadio = document.getElementById('simple_service');
    const variableRadio = document.getElementById('variable_service');
    const simplePriceSection = document.getElementById('simple_price_section');
    const variableOptionsSection = document.getElementById('variable_options_section');
    
    function toggleServiceType() {
        if (simpleRadio && simpleRadio.checked) {
            simplePriceSection.style.display = 'block';
            variableOptionsSection.style.display = 'none';
        } else if (variableRadio && variableRadio.checked) {
            simplePriceSection.style.display = 'none';
            variableOptionsSection.style.display = 'block';
        }
    }
    
    if (simpleRadio) simpleRadio.addEventListener('change', toggleServiceType);
    if (variableRadio) variableRadio.addEventListener('change', toggleServiceType);
    
    // Initialize on page load
    toggleServiceType();
    
    // Features Management
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
        updateFeatureRemoveButtons();
    });
    
    featuresContainer.addEventListener('click', function(e) {
        if (e.target.closest('.remove-feature')) {
            e.target.closest('.input-group').remove();
            updateFeatureRemoveButtons();
        }
    });
    
    function updateFeatureRemoveButtons() {
        const removeButtons = featuresContainer.querySelectorAll('.remove-feature');
        removeButtons.forEach((btn, index) => {
            btn.style.display = featuresContainer.children.length > 1 ? 'block' : 'none';
        });
    }
    
    // Initialize feature remove buttons on load
    updateFeatureRemoveButtons();
    
    // Custom Fields Management
    const container = document.getElementById('custom-fields-container');
    const addBtn = document.getElementById('add-custom-field');
    // قراءة العدد المبدئي للحقول المخصصة من خاصية بيانات لتجنّب استخدام توجيهات Blade داخل JavaScript
    let index = Number((container && container.dataset && container.dataset.initialIndex) ? container.dataset.initialIndex : 0);
    
    addBtn.addEventListener('click', function(){
        const wrapper = document.createElement('div');
        wrapper.className = 'border rounded p-3 mb-2';
        wrapper.innerHTML = `
            <div class="row g-2">
                <div class="col-md-4">
                    <input type="text" class="form-control" name="custom_fields[${index}][label]" placeholder="اسم الحقل">
                </div>
                <div class="col-md-3">
                    <select class="form-select" name="custom_fields[${index}][type]">
                        <option value="single">اختيار واحد</option>
                        <option value="multiple">عدة اختيارات</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <input type="text" class="form-control" name="custom_fields[${index}][options]" placeholder="خيارات مفصولة بفواصل">
                </div>
                <div class="col-md-1 d-grid">
                    <button type="button" class="btn btn-outline-danger remove-custom-field">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>`;
        container.appendChild(wrapper);
        index++;
        updateCustomFieldRemoveButtons();
    });
    
    container.addEventListener('click', function(e){
        if (e.target.closest('.remove-custom-field')) {
            e.target.closest('.border').remove();
            updateCustomFieldRemoveButtons();
        }
    });
    
    function updateCustomFieldRemoveButtons(){
        const buttons = container.querySelectorAll('.remove-custom-field');
        buttons.forEach(btn => btn.style.display = container.children.length > 1 ? 'block' : 'none');
    }
    
    // Initialize custom field remove buttons on load
    updateCustomFieldRemoveButtons();

    // تبديل عنوان حقل "وش نوفر؟" عند اختيار فئة الهدايا
    const offerLabel = document.querySelector('label[for="what_we_offer"]');
    const defaultOfferText = 'وش نوفر؟';
    const giftsOfferText = 'اهم المميزات؟';
    const offerIconHTML = '<i class="fas fa-gift text-primary me-1"></i>';

    function updateOfferLabel(){
        if (!categorySelect || !offerLabel) return;
        const selectedText = categorySelect.options[categorySelect.selectedIndex]?.text?.trim();
        const isGifts = selectedText === 'الهدايا';
        offerLabel.innerHTML = offerIconHTML + (isGifts ? giftsOfferText : defaultOfferText);
    }
    categorySelect?.addEventListener('change', updateOfferLabel);
    updateOfferLabel();

    // ===== معالجة الصور =====
    
    // معاينة الصور قبل الرفع
    const imagesInput = document.getElementById('images');
    const imagePreview = document.getElementById('image-preview');
    
    if (imagesInput) {
        imagesInput.addEventListener('change', function(e) {
            imagePreview.innerHTML = '';
            const files = Array.from(e.target.files);
            
            if (files.length > 0) {
                imagePreview.style.display = 'flex';
                files.forEach((file, index) => {
                    const reader = new FileReader();
                    reader.onload = function(event) {
                        const col = document.createElement('div');
                        col.className = 'col-md-3';
                        col.innerHTML = `
                            <div class="card">
                                <img src="${event.target.result}" class="card-img-top" style="height: 150px; object-fit: cover;">
                                <div class="card-body p-2 text-center">
                                    <small class="text-muted">صورة جديدة ${index + 1}</small>
                                </div>
                            </div>
                        `;
                        imagePreview.appendChild(col);
                    };
                    reader.readAsDataURL(file);
                });
            } else {
                imagePreview.style.display = 'none';
            }
        });
    }
    
    // حذف صورة
    document.querySelectorAll('.delete-image-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            if (!confirm('هل أنت متأكد من حذف هذه الصورة؟')) return;
            
            const imageId = this.dataset.imageId;
            const formEl = document.getElementById('serviceForm');
            const serviceId = formEl ? formEl.dataset.serviceId : null;
            if (!serviceId) {
                alert('معرّف الخدمة غير متوفر');
                return;
            }
            
            fetch(`/admin/services/${serviceId}/images/${imageId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById(`image-${imageId}`).remove();
                    alert(data.message);
                } else {
                    alert('حدث خطأ: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('حدث خطأ في الاتصال');
            });
        });
    });
    
    // تعيين صورة مصغرة
    document.querySelectorAll('.set-thumbnail-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const imageId = this.dataset.imageId;
            const formEl = document.getElementById('serviceForm');
            const serviceId = formEl ? formEl.dataset.serviceId : null;
            if (!serviceId) {
                alert('معرّف الخدمة غير متوفر');
                return;
            }
            
            fetch(`/admin/services/${serviceId}/images/${imageId}/set-thumbnail`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    location.reload(); // إعادة تحميل الصفحة لتحديث العرض
                } else {
                    alert('حدث خطأ: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('حدث خطأ في الاتصال');
            });
        });
    });
});
</script>

@endsection
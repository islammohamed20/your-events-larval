@extends('layouts.admin')

@section('title', isset($heroSlide) ? 'تعديل سلايد' : 'إضافة سلايد جديد')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ isset($heroSlide) ? 'تعديل السلايد' : 'إضافة سلايد جديد' }}</h1>
        <a href="{{ route('admin.hero-slides.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-right me-2"></i>رجوع
        </a>
    </div>

    <form action="{{ isset($heroSlide) ? route('admin.hero-slides.update', $heroSlide) : route('admin.hero-slides.store') }}" 
          method="POST" 
          enctype="multipart/form-data">
        @csrf
        @if(isset($heroSlide))
            @method('PUT')
        @endif

        <div class="row">
            <div class="col-lg-8">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">معلومات السلايد</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="title" class="form-label">العنوان الرئيسي <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control @error('title') is-invalid @enderror" 
                                   id="title" 
                                   name="title" 
                                   value="{{ old('title', $heroSlide->title ?? '') }}" 
                                   required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="subtitle" class="form-label">العنوان الفرعي</label>
                            <input type="text" 
                                   class="form-control @error('subtitle') is-invalid @enderror" 
                                   id="subtitle" 
                                   name="subtitle" 
                                   value="{{ old('subtitle', $heroSlide->subtitle ?? '') }}">
                            @error('subtitle')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">الوصف</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" 
                                      name="description" 
                                      rows="3">{{ old('description', $heroSlide->description ?? '') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="image" class="form-label">
                                صورة السلايد 
                                @if(!isset($heroSlide))
                                    <span class="text-danger">*</span>
                                @endif
                            </label>
                            @if(isset($heroSlide) && $heroSlide->image)
                                <div class="mb-2">
                                    <img src="{{ Storage::url($heroSlide->image) }}" 
                                         alt="{{ $heroSlide->title }}" 
                                         class="img-thumbnail" 
                                         style="max-height: 200px;">
                                </div>
                            @endif
                            <input type="file" 
                                   class="form-control @error('image') is-invalid @enderror" 
                                   id="image" 
                                   name="image" 
                                   accept="image/*"
                                   {{ !isset($heroSlide) ? 'required' : '' }}>
                            @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">حجم موصى به: 1920x800 بكسل | حد أقصى: 5MB</small>
                        </div>

                        <hr class="my-4">
                        <h6 class="mb-3">إعدادات الزر (Call to Action)</h6>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="button_text" class="form-label">نص الزر</label>
                                <input type="text" 
                                       class="form-control @error('button_text') is-invalid @enderror" 
                                       id="button_text" 
                                       name="button_text" 
                                       value="{{ old('button_text', $heroSlide->button_text ?? '') }}"
                                       placeholder="مثال: اكتشف المزيد">
                                @error('button_text')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="button_link" class="form-label">رابط الزر</label>
                                <input type="text" 
                                       class="form-control @error('button_link') is-invalid @enderror" 
                                       id="button_link" 
                                       name="button_link" 
                                       value="{{ old('button_link', $heroSlide->button_link ?? '') }}"
                                       placeholder="/services">
                                @error('button_link')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">نمط الزر</label>
                            <div class="btn-group w-100" role="group">
                                <input type="radio" 
                                       class="btn-check" 
                                       name="button_style" 
                                       id="style_primary" 
                                       value="primary" 
                                       {{ old('button_style', $heroSlide->button_style ?? 'primary') == 'primary' ? 'checked' : '' }}>
                                <label class="btn btn-outline-primary" for="style_primary">
                                    <i class="fas fa-circle me-1" style="color: #42347b;"></i> أساسي
                                </label>

                                <input type="radio" 
                                       class="btn-check" 
                                       name="button_style" 
                                       id="style_secondary" 
                                       value="secondary" 
                                       {{ old('button_style', $heroSlide->button_style ?? '') == 'secondary' ? 'checked' : '' }}>
                                <label class="btn btn-outline-secondary" for="style_secondary">
                                    <i class="fas fa-circle me-1" style="color: #6c757d;"></i> ثانوي
                                </label>

                                <input type="radio" 
                                       class="btn-check" 
                                       name="button_style" 
                                       id="style_accent" 
                                       value="accent" 
                                       {{ old('button_style', $heroSlide->button_style ?? '') == 'accent' ? 'checked' : '' }}>
                                <label class="btn btn-outline-danger" for="style_accent">
                                    <i class="fas fa-circle me-1" style="color: #EF4870;"></i> مميز
                                </label>
                            </div>
                        </div>

                        <hr class="my-4">
                        <h6 class="mb-3">تأثيرات الانتقال والتوقيت</h6>

                        <div class="mb-3">
                            <label class="form-label">نوع تأثير الانتقال <span class="text-danger">*</span></label>
                            <div class="row g-3">
                                <div class="col-6">
                                    <input type="radio" 
                                           class="btn-check" 
                                           name="transition_effect" 
                                           id="effect_fade" 
                                           value="fade" 
                                           {{ old('transition_effect', $heroSlide->transition_effect ?? 'fade') == 'fade' ? 'checked' : '' }}>
                                    <label class="btn btn-outline-primary w-100" for="effect_fade">
                                        <i class="fas fa-adjust me-1"></i> Fade
                                        <small class="d-block text-muted">تلاشي ناعم</small>
                                    </label>
                                </div>

                                <div class="col-6">
                                    <input type="radio" 
                                           class="btn-check" 
                                           name="transition_effect" 
                                           id="effect_slide" 
                                           value="slide" 
                                           {{ old('transition_effect', $heroSlide->transition_effect ?? '') == 'slide' ? 'checked' : '' }}>
                                    <label class="btn btn-outline-primary w-100" for="effect_slide">
                                        <i class="fas fa-arrows-alt-h me-1"></i> Slide
                                        <small class="d-block text-muted">انزلاق جانبي</small>
                                    </label>
                                </div>

                                <div class="col-6">
                                    <input type="radio" 
                                           class="btn-check" 
                                           name="transition_effect" 
                                           id="effect_zoom" 
                                           value="zoom" 
                                           {{ old('transition_effect', $heroSlide->transition_effect ?? '') == 'zoom' ? 'checked' : '' }}>
                                    <label class="btn btn-outline-primary w-100" for="effect_zoom">
                                        <i class="fas fa-search-plus me-1"></i> Zoom
                                        <small class="d-block text-muted">تكبير تدريجي</small>
                                    </label>
                                </div>

                                <div class="col-6">
                                    <input type="radio" 
                                           class="btn-check" 
                                           name="transition_effect" 
                                           id="effect_flip" 
                                           value="flip" 
                                           {{ old('transition_effect', $heroSlide->transition_effect ?? '') == 'flip' ? 'checked' : '' }}>
                                    <label class="btn btn-outline-primary w-100" for="effect_flip">
                                        <i class="fas fa-sync-alt me-1"></i> Flip
                                        <small class="d-block text-muted">تقليب ثلاثي الأبعاد</small>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="duration" class="form-label">مدة العرض (بالثواني) <span class="text-danger">*</span></label>
                            <input type="range" 
                                   class="form-range" 
                                   id="durationRange" 
                                   min="2" 
                                   max="15" 
                                   step="1" 
                                   value="{{ old('duration', isset($heroSlide) ? $heroSlide->duration / 1000 : 6) }}"
                                   oninput="updateDuration(this.value)">
                            <input type="hidden" 
                                   id="duration" 
                                   name="duration" 
                                   value="{{ old('duration', $heroSlide->duration ?? 6000) }}">
                            <div class="d-flex justify-content-between">
                                <small class="text-muted">2 ثانية (سريع)</small>
                                <span id="durationDisplay" class="badge bg-primary">{{ old('duration', isset($heroSlide) ? $heroSlide->duration / 1000 : 6) }} ثانية</span>
                                <small class="text-muted">15 ثانية (بطيء)</small>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="order" class="form-label">الترتيب</label>
                                <input type="number" 
                                       class="form-control @error('order') is-invalid @enderror" 
                                       id="order" 
                                       name="order" 
                                       value="{{ old('order', $heroSlide->order ?? 0) }}" 
                                       min="0">
                                @error('order')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label d-block">الحالة</label>
                                <div class="form-check form-switch mt-2">
                                    <input class="form-check-input" 
                                           type="checkbox" 
                                           id="is_active" 
                                           name="is_active" 
                                           {{ old('is_active', $heroSlide->is_active ?? true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        تفعيل السلايد (إظهاره في البانر)
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">نصائح التصميم</h6>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled small">
                            <li class="mb-2">
                                <i class="fas fa-lightbulb text-warning me-2"></i>
                                استخدم صوراً بجودة عالية ودقة 1920x800 بكسل
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-lightbulb text-warning me-2"></i>
                                اجعل النصوص قصيرة وواضحة
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-lightbulb text-warning me-2"></i>
                                استخدم زراً واحداً واضحاً (Call to Action)
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-lightbulb text-warning me-2"></i>
                                تأكد من تباين الألوان بين النص والخلفية
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-lightbulb text-warning me-2"></i>
                                يمكنك إضافة عدة سلايدات وسيتم عرضها بشكل دوّار
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-lightbulb text-warning me-2"></i>
                                اختر تأثير الانتقال المناسب لنوع المحتوى
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-lightbulb text-warning me-2"></i>
                                مدة العرض الموصى بها: 5-7 ثواني
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="card shadow">
                    <div class="card-header py-3 bg-light">
                        <h6 class="m-0 font-weight-bold text-primary">إجراءات</h6>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>حفظ السلايد
                            </button>
                            <a href="{{ route('admin.hero-slides.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>إلغاء
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
function updateDuration(seconds) {
    document.getElementById('duration').value = seconds * 1000;
    document.getElementById('durationDisplay').textContent = seconds + ' ثانية';
}
</script>
@endsection

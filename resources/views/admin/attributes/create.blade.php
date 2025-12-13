@extends('layouts.admin')

@section('title', 'إضافة خاصية جديدة')
@section('page-title', 'إضافة خاصية جديدة')
@section('page-description', 'إنشاء خاصية جديدة للاستخدام في الخدمات المتغيرة')

@section('content')
<div class="row">
    <div class="col-md-8">
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <h5 class="alert-heading"><i class="fas fa-exclamation-triangle me-2"></i>هناك أخطاء في النموذج:</h5>
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-tag me-2"></i>بيانات الخاصية
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.attributes.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="name" class="form-label">
                            اسم الخاصية <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               class="form-control @error('name') is-invalid @enderror" 
                               id="name" 
                               name="name" 
                               value="{{ old('name') }}"
                               placeholder="مثال: عدد الأشخاص"
                               required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">
                            الاسم الذي سيظهر في لوحة التحكم والواجهة الأمامية
                        </small>
                    </div>

                    <div class="mb-3">
                        <label for="slug" class="form-label">
                            Slug (اختياري)
                        </label>
                        <input type="text" 
                               class="form-control @error('slug') is-invalid @enderror" 
                               id="slug" 
                               name="slug" 
                               value="{{ old('slug') }}"
                               placeholder="سيتم إنشاؤه تلقائياً من الاسم">
                        @error('slug')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">
                            اسم نظيف بالإنجليزية للاستخدام في الكود (guests, city, etc). يتم إنشاؤه تلقائياً إذا تُرك فارغاً
                        </small>
                    </div>

                    <div class="mb-3">
                        <label for="type" class="form-label">
                            نوع الحقل <span class="text-danger">*</span>
                        </label>
                        <select class="form-select @error('type') is-invalid @enderror" 
                                id="type" 
                                name="type" 
                                required>
                            <option value="">اختر النوع</option>
                            <option value="select" {{ old('type') == 'select' ? 'selected' : '' }}>
                                قائمة منسدلة (Select)
                            </option>
                            <option value="radio" {{ old('type') == 'radio' ? 'selected' : '' }}>
                                اختيار واحد (Radio)
                            </option>
                            <option value="checkbox" {{ old('type') == 'checkbox' ? 'selected' : '' }}>
                                اختيارات متعددة (Checkbox)
                            </option>
                        </select>
                        @error('type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="order" class="form-label">
                            الترتيب
                        </label>
                        <input type="number" 
                               class="form-control @error('order') is-invalid @enderror" 
                               id="order" 
                               name="order" 
                               value="{{ old('order', 0) }}"
                               min="0">
                        @error('order')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">
                            ترتيب ظهور الخاصية (الأقل رقماً يظهر أولاً)
                        </small>
                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox" 
                               class="form-check-input" 
                               id="is_active" 
                               name="is_active"
                               {{ old('is_active', true) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">
                            نشط
                        </label>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>حفظ
                        </button>
                        <a href="{{ route('admin.attributes.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times me-2"></i>إلغاء
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card bg-light">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="fas fa-lightbulb me-2"></i>نصائح
                </h6>
            </div>
            <div class="card-body">
                <h6>أمثلة على الخصائص:</h6>
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <i class="fas fa-users text-primary me-2"></i>
                        <strong>عدد الأشخاص</strong><br>
                        <small class="text-muted">50-100، 100-200، 200-300</small>
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-map-marker-alt text-danger me-2"></i>
                        <strong>المدينة</strong><br>
                        <small class="text-muted">الرياض، جدة، الدمام</small>
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-building text-success me-2"></i>
                        <strong>نوع القاعة</strong><br>
                        <small class="text-muted">داخلية، خارجية، فندق</small>
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-clock text-warning me-2"></i>
                        <strong>المدة</strong><br>
                        <small class="text-muted">نصف يوم، يوم كامل، عدة أيام</small>
                    </li>
                </ul>

                <hr>

                <h6>بعد الحفظ:</h6>
                <p class="small text-muted">
                    ستتمكن من إضافة القيم المختلفة لهذه الخاصية (مثل: 50-100 شخص، 100-200 شخص، إلخ)
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

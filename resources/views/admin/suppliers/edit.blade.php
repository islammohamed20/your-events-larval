@extends('layouts.admin')

@section('title', 'تعديل بيانات المورد')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">تعديل بيانات المورد</h1>
            <p class="text-muted mb-0">قم بتحديث بيانات المورد ثم احفظ التغييرات</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.suppliers.show', $supplier) }}" class="btn btn-outline-secondary">
                <i class="fas fa-eye me-1"></i>عرض التفاصيل
            </a>
            <a href="{{ route('admin.suppliers.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-right me-1"></i>عودة للموردين
            </a>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.suppliers.update', $supplier) }}" enctype="multipart/form-data" class="card border-0 shadow-sm">
        @csrf
        @method('PUT')
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">نوع المورد</label>
                    <select name="supplier_type" class="form-select" required>
                        <option value="company" {{ old('supplier_type', $supplier->supplier_type) == 'company' ? 'selected' : '' }}>منشأة</option>
                        <option value="individual" {{ old('supplier_type', $supplier->supplier_type) == 'individual' ? 'selected' : '' }}>فرد</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">اسم المورد / المنشأة</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $supplier->name) }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">البريد الإلكتروني</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email', $supplier->email) }}" required>
                </div>

                <div class="col-md-4">
                    <label class="form-label">كلمة المرور (اتركها فارغة إن لم ترغب بالتغيير)</label>
                    <input type="password" name="password" class="form-control">
                </div>
                <div class="col-md-4">
                    <label class="form-label">الجوال الأساسي</label>
                    <input type="text" name="primary_phone" class="form-control" value="{{ old('primary_phone', $supplier->primary_phone) }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">جوال إضافي</label>
                    <input type="text" name="secondary_phone" class="form-control" value="{{ old('secondary_phone', $supplier->secondary_phone) }}">
                </div>

                <div class="col-md-4">
                    <label class="form-label">المدينة</label>
                    <input type="text" name="headquarters_city" class="form-control" value="{{ old('headquarters_city', $supplier->headquarters_city) }}">
                </div>
                <div class="col-md-8">
                    <label class="form-label">العنوان</label>
                    <input type="text" name="address" class="form-control" value="{{ old('address', $supplier->address) }}">
                </div>

                <div class="col-12">
                    <label class="form-label">نبذة</label>
                    <textarea name="description" class="form-control" rows="3">{{ old('description', $supplier->description) }}</textarea>
                </div>

                <div class="col-md-6">
                    <label class="form-label">السجل التجاري</label>
                    <input type="text" name="commercial_register" class="form-control" value="{{ old('commercial_register', $supplier->commercial_register) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">الرقم الضريبي</label>
                    <input type="text" name="tax_number" class="form-control" value="{{ old('tax_number', $supplier->tax_number) }}">
                </div>

                <div class="col-md-6">
                    <label class="form-label">ملف السجل التجاري (رفع جديد يستبدل الحالي)</label>
                    <input type="file" name="commercial_register_file" class="form-control" accept=".pdf,.jpg,.jpeg,.png,.webp">
                    @if($supplier->commercial_register_file)
                        <small class="text-muted d-block mt-1">ملف حالي: {{ basename($supplier->commercial_register_file) }}</small>
                    @endif
                </div>
                <div class="col-md-6">
                    <label class="form-label">ملف الشهادة الضريبية (رفع جديد يستبدل الحالي)</label>
                    <input type="file" name="tax_certificate_file" class="form-control" accept=".pdf,.jpg,.jpeg,.png,.webp">
                    @if($supplier->tax_certificate_file)
                        <small class="text-muted d-block mt-1">ملف حالي: {{ basename($supplier->tax_certificate_file) }}</small>
                    @endif
                </div>

                <div class="col-md-6">
                    <label class="form-label">الملف التعريفي (رفع جديد يستبدل الحالي)</label>
                    <input type="file" name="company_profile_file" class="form-control" accept=".pdf,.doc,.docx">
                    @if($supplier->company_profile_file)
                        <small class="text-muted d-block mt-1">ملف حالي: {{ basename($supplier->company_profile_file) }}</small>
                    @endif
                </div>
                <div class="col-md-6">
                    <label class="form-label">معرض الأعمال (تتم إضافة الملفات الجديدة للموجود)</label>
                    <input type="file" name="portfolio_files[]" class="form-control" multiple accept=".pdf,.jpg,.jpeg,.png,.webp">
                    @if(is_array($supplier->portfolio_files) && count($supplier->portfolio_files) > 0)
                        <small class="text-muted d-block mt-1">عدد الملفات الحالية: {{ count($supplier->portfolio_files) }}</small>
                    @endif
                </div>

                <div class="col-12">
                    <label class="form-label">روابط التواصل الاجتماعي</label>
                    @php($social = is_array($supplier->social_media) ? $supplier->social_media : [])
                    <div class="row g-2">
                        <div class="col-md-3">
                            <input type="url" name="social_media[twitter]" class="form-control" placeholder="رابط تويتر" value="{{ old('social_media.twitter', $social['twitter'] ?? '') }}">
                        </div>
                        <div class="col-md-3">
                            <input type="url" name="social_media[instagram]" class="form-control" placeholder="رابط إنستجرام" value="{{ old('social_media.instagram', $social['instagram'] ?? '') }}">
                        </div>
                        <div class="col-md-3">
                            <input type="url" name="social_media[snapchat]" class="form-control" placeholder="رابط سناب شات" value="{{ old('social_media.snapchat', $social['snapchat'] ?? '') }}">
                        </div>
                        <div class="col-md-3">
                            <input type="url" name="social_media[tiktok]" class="form-control" placeholder="رابط تيك توك" value="{{ old('social_media.tiktok', $social['tiktok'] ?? '') }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer d-flex justify-content-end gap-2">
            <a href="{{ route('admin.suppliers.show', $supplier) }}" class="btn btn-light">إلغاء</a>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-1"></i>حفظ التعديلات
            </button>
        </div>
    </form>
</div>
@endsection

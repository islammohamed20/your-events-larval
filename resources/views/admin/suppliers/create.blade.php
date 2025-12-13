@extends('layouts.admin')

@section('title', 'إضافة مورد جديد')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">إضافة مورد جديد</h1>
            <p class="text-muted mb-0">قم بتعبئة البيانات التالية لإضافة مورد إلى المنصة</p>
        </div>
        <a href="{{ route('admin.suppliers.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-right me-1"></i>عودة للموردين
        </a>
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

    <form method="POST" action="{{ route('admin.suppliers.store') }}" enctype="multipart/form-data" class="card border-0 shadow-sm">
        @csrf
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">نوع المورد</label>
                    <select name="supplier_type" class="form-select" required>
                        <option value="company" {{ old('supplier_type') == 'company' ? 'selected' : '' }}>منشأة</option>
                        <option value="individual" {{ old('supplier_type') == 'individual' ? 'selected' : '' }}>فرد</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">اسم المورد / المنشأة</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">البريد الإلكتروني</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                </div>

                <div class="col-md-4">
                    <label class="form-label">كلمة المرور</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">الجوال الأساسي</label>
                    <input type="text" name="primary_phone" class="form-control" value="{{ old('primary_phone') }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">جوال إضافي</label>
                    <input type="text" name="secondary_phone" class="form-control" value="{{ old('secondary_phone') }}">
                </div>

                <div class="col-md-4">
                    <label class="form-label">المدينة</label>
                    <input type="text" name="headquarters_city" class="form-control" value="{{ old('headquarters_city') }}">
                </div>
                <div class="col-md-8">
                    <label class="form-label">العنوان</label>
                    <input type="text" name="address" class="form-control" value="{{ old('address') }}">
                </div>

                <div class="col-12">
                    <label class="form-label">نبذة</label>
                    <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
                </div>

                <div class="col-md-6">
                    <label class="form-label">السجل التجاري</label>
                    <input type="text" name="commercial_register" class="form-control" value="{{ old('commercial_register') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">الرقم الضريبي</label>
                    <input type="text" name="tax_number" class="form-control" value="{{ old('tax_number') }}">
                </div>

                <div class="col-md-6">
                    <label class="form-label">ملف السجل التجاري</label>
                    <input type="file" name="commercial_register_file" class="form-control" accept=".pdf,.jpg,.jpeg,.png,.webp">
                </div>
                <div class="col-md-6">
                    <label class="form-label">ملف الشهادة الضريبية</label>
                    <input type="file" name="tax_certificate_file" class="form-control" accept=".pdf,.jpg,.jpeg,.png,.webp">
                </div>

                <div class="col-md-6">
                    <label class="form-label">الملف التعريفي (PDF/DOC)</label>
                    <input type="file" name="company_profile_file" class="form-control" accept=".pdf,.doc,.docx">
                </div>
                <div class="col-md-6">
                    <label class="form-label">معرض الأعمال (يمكن اختيار عدة ملفات)</label>
                    <input type="file" name="portfolio_files[]" class="form-control" multiple accept=".pdf,.jpg,.jpeg,.png,.webp">
                </div>

                <div class="col-12">
                    <label class="form-label">الفئات التي يقدمها المورد</label>
                    <select name="services_offered[]" class="form-select" multiple>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" @selected(collect(old('services_offered', []))->contains($category->id))>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    <small class="text-muted">أولاً اختر فئة أو أكثر. لاحقًا ستضيف الخدمات من هذه الفئات من صفحة تفاصيل المورد.</small>
                </div>

                <div class="col-12">
                    <label class="form-label">روابط التواصل الاجتماعي</label>
                    <div class="row g-2">
                        <div class="col-md-3">
                            <input type="url" name="social_media[twitter]" class="form-control" placeholder="رابط تويتر" value="{{ old('social_media.twitter') }}">
                        </div>
                        <div class="col-md-3">
                            <input type="url" name="social_media[instagram]" class="form-control" placeholder="رابط إنستجرام" value="{{ old('social_media.instagram') }}">
                        </div>
                        <div class="col-md-3">
                            <input type="url" name="social_media[snapchat]" class="form-control" placeholder="رابط سناب شات" value="{{ old('social_media.snapchat') }}">
                        </div>
                        <div class="col-md-3">
                            <input type="url" name="social_media[tiktok]" class="form-control" placeholder="رابط تيك توك" value="{{ old('social_media.tiktok') }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer d-flex justify-content-end gap-2">
            <a href="{{ route('admin.suppliers.index') }}" class="btn btn-light">إلغاء</a>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-1"></i>حفظ المورد
            </button>
        </div>
    </form>
</div>
@endsection

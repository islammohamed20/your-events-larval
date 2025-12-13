@extends('supplier.layouts.app')

@section('title', 'الملف الشخصي')
@section('page-title', 'الملف الشخصي')

@section('content')
<div class="row g-4">
    <!-- Profile Info -->
    <div class="col-lg-4">
        <div class="content-card">
            <div class="p-4 text-center">
                <div class="user-avatar mx-auto mb-3" style="width: 100px; height: 100px; font-size: 2.5rem; background: linear-gradient(135deg, #1f144a, #3d2a7a);">
                    {{ mb_substr($supplier->name, 0, 1) }}
                </div>
                <h4 class="fw-bold mb-1">{{ $supplier->name }}</h4>
                <p class="text-muted mb-3">
                    @if($supplier->supplier_type === 'company')
                        <i class="fas fa-building me-1"></i> منشأة
                    @else
                        <i class="fas fa-user me-1"></i> فرد
                    @endif
                </p>
                
                <div class="d-flex justify-content-center gap-2 mb-3">
                    @if($supplier->status === 'approved')
                        <span class="badge bg-success"><i class="fas fa-check me-1"></i>حساب موثق</span>
                    @endif
                    @if($supplier->email_verified_at)
                        <span class="badge bg-info"><i class="fas fa-envelope-check me-1"></i>بريد مؤكد</span>
                    @endif
                </div>
                
                <div class="border-top pt-3 mt-3">
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="fw-bold fs-4" style="color: #1f144a;">{{ $supplier->services->count() }}</div>
                            <small class="text-muted">خدمة</small>
                        </div>
                        <div class="col-6">
                            <div class="fw-bold fs-4" style="color: #2dbcae;">{{ $supplier->serviceCategories->count() }}</div>
                            <small class="text-muted">فئة</small>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Contact Info -->
            <div class="border-top p-4">
                <h6 class="fw-bold mb-3"><i class="fas fa-phone text-primary me-2"></i>معلومات التواصل</h6>
                <ul class="list-unstyled mb-0">
                    <li class="mb-2">
                        <i class="fas fa-envelope text-muted me-2"></i>
                        {{ $supplier->email }}
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-phone text-muted me-2"></i>
                        {{ $supplier->primary_phone }}
                    </li>
                    @if($supplier->secondary_phone)
                    <li class="mb-2">
                        <i class="fas fa-phone-alt text-muted me-2"></i>
                        {{ $supplier->secondary_phone }}
                    </li>
                    @endif
                    @if($supplier->address)
                    <li>
                        <i class="fas fa-map-marker-alt text-muted me-2"></i>
                        {{ $supplier->address }}
                    </li>
                    @endif
                </ul>
            </div>
        </div>
    </div>
    
    <!-- Edit Profile -->
    <div class="col-lg-8">
        <!-- Update Profile Form -->
        <div class="content-card mb-4">
            <div class="card-header-custom">
                <h5><i class="fas fa-user-edit me-2"></i>تعديل الملف الشخصي</h5>
            </div>
            <div class="p-4">
                <form method="POST" action="{{ route('supplier.profile.update') }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">الاسم <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                   value="{{ old('name', $supplier->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">رقم الجوال الأساسي <span class="text-danger">*</span></label>
                            <input type="tel" name="primary_phone" class="form-control @error('primary_phone') is-invalid @enderror" 
                                   value="{{ old('primary_phone', $supplier->primary_phone) }}" required>
                            @error('primary_phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">رقم جوال إضافي</label>
                            <input type="tel" name="secondary_phone" class="form-control @error('secondary_phone') is-invalid @enderror" 
                                   value="{{ old('secondary_phone', $supplier->secondary_phone) }}">
                            @error('secondary_phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">المدينة</label>
                            <input type="text" class="form-control" value="{{ $supplier->headquarters_city }}" disabled>
                            <small class="text-muted">للتغيير، تواصل مع الإدارة</small>
                        </div>
                        
                        <div class="col-12">
                            <label class="form-label fw-semibold">الوصف</label>
                            <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="3">{{ old('description', $supplier->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-12">
                            <label class="form-label fw-semibold">العنوان</label>
                            <textarea name="address" class="form-control @error('address') is-invalid @enderror" rows="2">{{ old('address', $supplier->address) }}</textarea>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <!-- Social Media -->
                        <div class="col-12">
                            <h6 class="fw-bold mt-3 mb-3"><i class="fas fa-share-alt me-2"></i>وسائل التواصل الاجتماعي</h6>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label"><i class="fab fa-twitter text-info me-1"></i>تويتر</label>
                            <input type="url" name="social_media[twitter]" class="form-control" 
                                   value="{{ old('social_media.twitter', $supplier->social_media['twitter'] ?? '') }}" 
                                   placeholder="https://twitter.com/username">
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label"><i class="fab fa-instagram text-danger me-1"></i>إنستجرام</label>
                            <input type="url" name="social_media[instagram]" class="form-control" 
                                   value="{{ old('social_media.instagram', $supplier->social_media['instagram'] ?? '') }}" 
                                   placeholder="https://instagram.com/username">
                        </div>
                        
                        <div class="col-12">
                            <button type="submit" class="btn btn-supplier-primary">
                                <i class="fas fa-save me-1"></i> حفظ التغييرات
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Change Password -->
        <div class="content-card">
            <div class="card-header-custom">
                <h5><i class="fas fa-lock me-2"></i>تغيير كلمة المرور</h5>
            </div>
            <div class="p-4">
                <form method="POST" action="{{ route('supplier.profile.password.update') }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">كلمة المرور الحالية <span class="text-danger">*</span></label>
                            <input type="password" name="current_password" class="form-control @error('current_password') is-invalid @enderror" required>
                            @error('current_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">كلمة المرور الجديدة <span class="text-danger">*</span></label>
                            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">تأكيد كلمة المرور <span class="text-danger">*</span></label>
                            <input type="password" name="password_confirmation" class="form-control" required>
                        </div>
                        
                        <div class="col-12">
                            <button type="submit" class="btn btn-outline-danger">
                                <i class="fas fa-key me-1"></i> تغيير كلمة المرور
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('title', 'تسجيل مورد جديد')

@section('content')
<div class="min-vh-100 bg-light py-5">
    <div class="container">
        <!-- Hero Section -->
        <div class="text-center mb-5">
            <!-- <div class="mb-4">
                <i class="fas fa-handshake display-1 text-warning"></i>
            </div> -->
            <h1 class="display-4 fw-bold text-dark mb-3">انضم إلى شبكة الموردين</h1>
            <p class="lead text-muted mx-auto" style="max-width: 700px;">
                هل أنت مزود خدمات فعاليات محترف؟ انضم إلى منصتنا وابدأ في تقديم خدماتك لآلاف العملاء الباحثين عن أفضل مقدمي الخدمات. نوفر لك منصة احترافية للوصول إلى عملاء جدد وتنمية أعمالك.
            </p>
        </div>

        <!-- Registration Form -->
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card shadow-lg border-0 rounded-4">
                    <div class="card-body p-4 p-md-5">
                        <form method="POST" action="{{ route('suppliers.store') }}" enctype="multipart/form-data" id="supplierRegisterForm">
                            @csrf

                            @if(session('error'))
                                <div class="alert alert-danger mb-4">
                                    <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
                                </div>
                            @endif

                            <!-- Section 1: معلومات المنشأة -->
                            <div class="mb-5">
                                <h3 class="h4 fw-bold text-dark mb-4 pb-2 border-bottom border-warning border-3">
                                    <i class="fas fa-building text-warning me-2"></i>معلومات المنشأة
                                </h3>

                                <!-- Supplier Type -->
                                <div class="mb-4">
                                    <label class="form-label fw-semibold text-dark">نوع المورد <span class="text-danger">*</span></label>
                                    <div class="d-flex gap-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="supplier_type" id="individual" value="individual" {{ old('supplier_type') == 'individual' ? 'checked' : '' }} required>
                                            <label class="form-check-label" for="individual">
                                                <i class="fas fa-user me-1"></i>فرد
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="supplier_type" id="company" value="company" {{ old('supplier_type') == 'company' ? 'checked' : '' }} required>
                                            <label class="form-check-label" for="company">
                                                <i class="fas fa-building me-1"></i>منشأة
                                            </label>
                                        </div>
                                    </div>
                                    @error('supplier_type')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Name -->
                                <div class="mb-4">
                                    <label for="name" class="form-label fw-semibold text-dark">اسم المورد / المنشأة <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control form-control-lg @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Commercial Register -->
                                <div class="mb-4" id="commercial_register_field" style="display: none;">
                                    <label for="commercial_register" class="form-label fw-semibold text-dark">رقم السجل التجاري <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control form-control-lg @error('commercial_register') is-invalid @enderror" id="commercial_register" name="commercial_register" value="{{ old('commercial_register') }}">
                                    <small class="text-muted">مطلوب للمنشآت فقط</small>
                                    @error('commercial_register')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Tax Number -->
                                <div class="mb-4">
                                    <label for="tax_number" class="form-label fw-semibold text-dark">الرقم الضريبي</label>
                                    <input type="text" class="form-control form-control-lg @error('tax_number') is-invalid @enderror" id="tax_number" name="tax_number" value="{{ old('tax_number') }}">
                                    <small class="text-muted">اختياري - في حال توفره</small>
                                    @error('tax_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Headquarters City -->
                                <div class="mb-4">
                                    <label for="headquarters_city" class="form-label fw-semibold text-dark">مقر المنشأة <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control form-control-lg @error('headquarters_city') is-invalid @enderror" id="headquarters_city" name="headquarters_city" value="{{ old('headquarters_city') }}" required>
                                    <small class="text-muted">المدينة الرئيسية للعمل</small>
                                    @error('headquarters_city')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Description -->
                                <div class="mb-4">
                                    <label for="description" class="form-label fw-semibold text-dark">نبذة عن المنشأة <span class="text-danger">*</span></label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="5" minlength="50" required>{{ old('description') }}</textarea>
                                    <small class="text-muted">وصف تفصيلي عن خدماتك وخبراتك (50 حرف على الأقل)</small>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Section 2: الفئات والخدمات -->
                            <div class="mb-5">
                                <h3 class="h4 fw-bold text-dark mb-4 pb-2 border-bottom border-warning border-3">
                                    <i class="fas fa-concierge-bell text-warning me-2"></i>اختر الفئات والخدمات
                                </h3>

                                <!-- اختيار الفئات -->
                                <div class="mb-5">
                                    <h5 class="fw-bold text-dark mb-3">
                                        <i class="fas fa-list me-2"></i> الخطوة 1: اختر الفئات <span class="text-danger">*</span>
                                    </h5>
                                    <p class="text-muted mb-4">اختر جميع الفئات التي تستطيع تقديم خدمات فيها</p>

                                    <div class="row g-4" id="categoriesContainer">
                                        @foreach($categories as $category)
                                        <div class="col-md-6">
                                            <div class="form-check border rounded-3 p-3 h-100 category-checkbox">
                                                <input class="form-check-input category-checkbox-input" 
                                                       type="checkbox" 
                                                       name="categories[]" 
                                                       value="{{ $category->id }}" 
                                                       id="category_{{ $category->id }}"
                                                       data-category-id="{{ $category->id }}"
                                                       {{ in_array($category->id, old('categories', [])) ? 'checked' : '' }}>
                                                <label class="form-check-label w-100 cursor-pointer" for="category_{{ $category->id }}">
                                                    <div class="d-flex align-items-start">
                                                        <div class="flex-shrink-0">
                                                            @if($category->icon_png)
                                                                <img src="{{ Storage::url($category->icon_png) }}" alt="{{ $category->name }}" width="40" height="40" class="me-3">
                                                            @elseif($category->icon)
                                                                <i class="{{ $category->icon }} text-warning fs-3 me-3 mt-1"></i>
                                                            @else
                                                                <i class="fas fa-cube text-warning fs-3 me-3 mt-1"></i>
                                                            @endif
                                                        </div>
                                                        <div>
                                                            <h5 class="mb-1">{{ $category->name }}</h5>
                                                            @if($category->supplier_form_name)
                                                                <div class="text-muted small mb-1">({{ $category->supplier_form_name }})</div>
                                                            @endif
                                                            <p class="text-muted small mb-0">{{ $category->description }}</p>
                                                        </div>
                                                    </div>
                                                </label>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                    @error('categories')
                                        <div class="text-danger small mt-3">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- اختيار الخدمات -->
                                <div class="mb-5">
                                    <h5 class="fw-bold text-dark mb-3">
                                        <i class="fas fa-cogs me-2"></i> الخطوة 2: اختر الخدمات <span class="text-danger">*</span>
                                    </h5>
                                    <p class="text-muted mb-3">ستظهر الخدمات بناءً على الفئات المختارة</p>

                                    <div id="servicesContainer">
                                        <div class="alert alert-info">
                                            <i class="fas fa-info-circle me-2"></i> اختر الفئات أولاً لعرض الخدمات المتاحة
                                        </div>
                                    </div>

                                    <div class="mt-3">
                                        <button type="button" class="btn btn-outline-warning" id="addCustomServiceBtn">
                                            <i class="fas fa-plus me-1"></i> إضافة خدمة غير موجودة
                                        </button>
                                        <small class="d-block text-muted mt-2">إذا لم تجد الخدمة في القائمة، يمكنك كتابة اسمها بعد اختيار الفئة من الخطوة الأولى.</small>
                                    </div>

                                    <div id="customServicesContainer" class="mt-3"></div>
                                    @error('services')
                                        <div class="text-danger small mt-3">{{ $message }}</div>
                                    @enderror
                                    @error('custom_services')
                                        <div class="text-danger small mt-2">{{ $message }}</div>
                                    @enderror
                                    @error('custom_services.*.name')
                                        <div class="text-danger small mt-2">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Section 3: المرفقات والوثائق -->
                            <div class="mb-5">
                                <h3 class="h4 fw-bold text-dark mb-4 pb-2 border-bottom border-warning border-3">
                                    <i class="fas fa-paperclip text-warning me-2"></i>المرفقات والوثائق
                                </h3>

                                <div class="row g-4">
                                    <!-- Commercial Register File -->
                                    <div class="col-md-6" id="commercial_register_file_field" style="display: none;">
                                        <label class="form-label fw-semibold text-dark">صورة السجل التجاري <span class="text-danger">*</span></label>
                                        <div class="upload-area border-2 border-dashed rounded-3 p-4 text-center bg-light">
                                            <input type="file" class="d-none" id="commercial_register_file" name="commercial_register_file" accept=".pdf,.jpg,.jpeg,.png">
                                            <label for="commercial_register_file" class="cursor-pointer d-block">
                                                <i class="fas fa-cloud-upload-alt fs-1 text-warning mb-2"></i>
                                                <p class="mb-1 fw-semibold">اضغط لرفع الملف</p>
                                                <small class="text-muted">PDF, JPG, PNG (حد أقصى 5MB)</small>
                                            </label>
                                            <div class="file-name text-success mt-2 d-none"></div>
                                        </div>
                                        @error('commercial_register_file')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Tax Certificate File -->
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-dark">صورة الشهادة الضريبية</label>
                                        <div class="upload-area border-2 border-dashed rounded-3 p-4 text-center bg-light">
                                            <input type="file" class="d-none" id="tax_certificate_file" name="tax_certificate_file" accept=".pdf,.jpg,.jpeg,.png">
                                            <label for="tax_certificate_file" class="cursor-pointer d-block">
                                                <i class="fas fa-cloud-upload-alt fs-1 text-warning mb-2"></i>
                                                <p class="mb-1 fw-semibold">اضغط لرفع الملف</p>
                                                <small class="text-muted">PDF, JPG, PNG (حد أقصى 5MB)</small>
                                            </label>
                                            <div class="file-name text-success mt-2 d-none"></div>
                                        </div>
                                        @error('tax_certificate_file')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Company Profile -->
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-dark">ملف تعريفي بالمنشأة</label>
                                        <div class="upload-area border-2 border-dashed rounded-3 p-4 text-center bg-light">
                                            <input type="file" class="d-none" id="company_profile_file" name="company_profile_file" accept=".pdf,.doc,.docx">
                                            <label for="company_profile_file" class="cursor-pointer d-block">
                                                <i class="fas fa-cloud-upload-alt fs-1 text-warning mb-2"></i>
                                                <p class="mb-1 fw-semibold">اضغط لرفع الملف</p>
                                                <small class="text-muted">PDF, DOC, DOCX (حد أقصى 10MB)</small>
                                            </label>
                                            <div class="file-name text-success mt-2 d-none"></div>
                                        </div>
                                        @error('company_profile_file')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Portfolio Files -->
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-dark">معرض الأعمال السابقة</label>
                                        <div class="upload-area border-2 border-dashed rounded-3 p-4 text-center bg-light">
                                            <input type="file" class="d-none" id="portfolio_files" name="portfolio_files[]" accept="image/*,.pdf" multiple>
                                            <label for="portfolio_files" class="cursor-pointer d-block">
                                                <i class="fas fa-images fs-1 text-warning mb-2"></i>
                                                <p class="mb-1 fw-semibold">اضغط لرفع الصور</p>
                                                <small class="text-muted">صور متعددة (حد أقصى 5MB لكل صورة)</small>
                                            </label>
                                            <div class="file-name text-success mt-2 d-none"></div>
                                        </div>
                                        @error('portfolio_files')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                        @error('portfolio_files.*')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="alert alert-warning mt-3 mb-0 small">
                                    <i class="fas fa-info-circle me-2"></i>
                                    الحد الآمن لإجمالي كل المرفقات في الطلب الواحد: <strong>25MB</strong>.
                                </div>
                            </div>

                            <!-- Section 4: معلومات التواصل -->
                            <div class="mb-5">
                                <h3 class="h4 fw-bold text-dark mb-4 pb-2 border-bottom border-warning border-3">
                                    <i class="fas fa-phone text-warning me-2"></i>معلومات التواصل
                                </h3>

                                <div class="row g-4">
                                    <!-- Email -->
                                    <div class="col-md-6">
                                        <label for="email" class="form-label fw-semibold text-dark">البريد الإلكتروني <span class="text-danger">*</span></label>
                                        <input type="email" class="form-control form-control-lg @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required>
                                        <small class="text-muted">سيتم استخدامه لتسجيل الدخول</small>
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Primary Phone -->
                                    <div class="col-md-6">
                                        <label for="primary_phone" class="form-label fw-semibold text-dark">رقم الجوال الأساسي <span class="text-danger">*</span></label>
                                        <input type="tel" inputmode="numeric" pattern="[+0-9]*" class="form-control form-control-lg @error('primary_phone') is-invalid @enderror" id="primary_phone" name="primary_phone" value="{{ old('primary_phone') ?: '+966' }}" required placeholder="+966XXXXXXXXX" dir="ltr">
                                        @error('primary_phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Password -->
                                    <div class="col-md-6">
                                        <label for="password" class="form-label fw-semibold text-dark">كلمة المرور <span class="text-danger">*</span></label>
                                        <input type="password" class="form-control form-control-lg @error('password') is-invalid @enderror" id="password" name="password" required>
                                        <small class="text-muted">8 أحرف على الأقل</small>
                                        @error('password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Secondary Phone -->
                                    <div class="col-md-6">
                                        <label for="secondary_phone" class="form-label fw-semibold text-dark">رقم جوال إضافي</label>
                                        <input type="tel" inputmode="numeric" pattern="[+0-9]*" class="form-control form-control-lg @error('secondary_phone') is-invalid @enderror" id="secondary_phone" name="secondary_phone" value="{{ old('secondary_phone') ?: '+966' }}" placeholder="+966XXXXXXXXX" dir="ltr">
                                        @error('secondary_phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Password Confirmation -->
                                    <div class="col-md-6">
                                        <label for="password_confirmation" class="form-label fw-semibold text-dark">تأكيد كلمة المرور <span class="text-danger">*</span></label>
                                        <input type="password" class="form-control form-control-lg @error('password_confirmation') is-invalid @enderror" id="password_confirmation" name="password_confirmation" required>
                                        <small class="text-muted">أعد إدخال كلمة المرور</small>
                                        @error('password_confirmation')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Spacer for new row -->
                                    <div class="col-12">
                                        <hr class="my-2">
                                        <h6 class="text-muted mb-3"><i class="fas fa-share-alt me-2"></i>وسائل التواصل الاجتماعي (اختياري)</h6>
                                    </div>

                                    <!-- Social Media -->
                                    <div class="col-md-6">
                                        <label for="social_twitter" class="form-label fw-semibold text-dark">
                                            <i class="fab fa-twitter text-info me-1"></i>حساب تويتر / X
                                        </label>
                                        <input type="url" class="form-control form-control-lg @error('social_media.twitter') is-invalid @enderror" id="social_twitter" name="social_media[twitter]" value="{{ old('social_media.twitter') }}" placeholder="https://twitter.com/username">
                                        @error('social_media.twitter')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label for="social_instagram" class="form-label fw-semibold text-dark">
                                            <i class="fab fa-instagram text-danger me-1"></i>حساب إنستجرام
                                        </label>
                                        <input type="url" class="form-control form-control-lg @error('social_media.instagram') is-invalid @enderror" id="social_instagram" name="social_media[instagram]" value="{{ old('social_media.instagram') }}" placeholder="https://instagram.com/username">
                                        @error('social_media.instagram')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label for="social_snapchat" class="form-label fw-semibold text-dark">
                                            <i class="fab fa-snapchat text-warning me-1"></i>حساب سناب شات
                                        </label>
                                        <input type="url" class="form-control form-control-lg @error('social_media.snapchat') is-invalid @enderror" id="social_snapchat" name="social_media[snapchat]" value="{{ old('social_media.snapchat') }}" placeholder="https://snapchat.com/add/username">
                                        @error('social_media.snapchat')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label for="social_tiktok" class="form-label fw-semibold text-dark">
                                            <i class="fab fa-tiktok text-dark me-1"></i>حساب تيك توك
                                        </label>
                                        <input type="url" class="form-control form-control-lg @error('social_media.tiktok') is-invalid @enderror" id="social_tiktok" name="social_media[tiktok]" value="{{ old('social_media.tiktok') }}" placeholder="https://tiktok.com/@username">
                                        @error('social_media.tiktok')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Address -->
                                    <div class="col-12">
                                        <label for="address" class="form-label fw-semibold text-dark">العنوان الوطني / العنوان التفصيلي</label>
                                        <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address" rows="3">{{ old('address') }}</textarea>
                                        @error('address')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Terms & Conditions -->
                            <div class="mb-4">
                                <div class="card bg-light border-0">
                                    <div class="card-body">
                                        <div class="form-check mb-3">
                                            <input class="form-check-input @error('terms_accepted') is-invalid @enderror" type="checkbox" name="terms_accepted" id="terms_accepted" value="1" {{ old('terms_accepted') ? 'checked' : '' }} required>
                                            <label class="form-check-label" for="terms_accepted">
                                                أوافق على <a href="{{ route('terms') }}" target="_blank" class="text-warning fw-semibold">شروط وأحكام</a> الانضمام كمورد في المنصة <span class="text-danger">*</span>
                                            </label>
                                            @error('terms_accepted')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input @error('privacy_accepted') is-invalid @enderror" type="checkbox" name="privacy_accepted" id="privacy_accepted" value="1" {{ old('privacy_accepted') ? 'checked' : '' }} required>
                                            <label class="form-check-label" for="privacy_accepted">
                                                أوافق على <a href="{{ route('privacy') }}" target="_blank" class="text-warning fw-semibold">سياسة الخصوصية</a> وشروط الاستخدام <span class="text-danger">*</span>
                                            </label>
                                            @error('privacy_accepted')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="text-center">
                                <button type="submit" class="btn btn-warning btn-lg px-5 py-3 fw-bold shadow-lg" id="submitBtn">
                                    <i class="fas fa-paper-plane me-2"></i>إرسال الطلب
                                    <span class="spinner-border spinner-border-sm ms-2 d-none" role="status" aria-hidden="true"></span>
                                </button>
                                <p class="text-muted mt-3 small">
                                    <i class="fas fa-info-circle me-1"></i>
                                    سيتم مراجعة طلبك خلال 3-5 أيام عمل وسنتواصل معك عبر البريد الإلكتروني
                                </p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .service-checkbox {
        transition: all 0.3s ease;
        cursor: pointer;
    }
    
    .service-checkbox:hover {
        border-color: #ffc107 !important;
        background-color: #fff9e6;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    
    .service-checkbox input:checked ~ label {
        color: #000;
    }

    .service-checkbox .form-check-input {
        float: none;
        margin: 0;
        flex: 0 0 auto;
    }

    .service-checkbox-main {
        min-width: 0;
    }

    .service-checkbox-label {
        line-height: 1.5;
        margin: 0;
    }

    .service-action-btn {
        white-space: nowrap;
    }

    @media (max-width: 767.98px) {
        .service-checkbox-content {
            flex-direction: column;
            align-items: stretch !important;
        }

        .service-action-btn {
            width: 100%;
        }
    }
    
    .service-checkbox:has(input:checked) {
        border-color: #ffc107 !important;
        background-color: #fff9e6;
        box-shadow: 0 0 0 3px rgba(255, 193, 7, 0.1);
    }
    
    .upload-area {
        transition: all 0.3s ease;
    }
    
    .upload-area:hover {
        border-color: #ffc107 !important;
        background-color: #fff9e6 !important;
    }
    
    .cursor-pointer {
        cursor: pointer;
    }
    
    .form-control:focus,
    .form-check-input:focus {
        border-color: #ffc107;
        box-shadow: 0 0 0 0.25rem rgba(255, 193, 7, 0.25);
    }
</style>
@endpush

@push('scripts')
<script type="application/json" id="allServicesData">@json($allServices ?? [])</script>
<script type="application/json" id="selectedCategoriesData">@json(old('categories', []))</script>
<script type="application/json" id="selectedServicesData">@json(old('services', []))</script>
<script type="application/json" id="customServicesData">@json(old('custom_services', []))</script>
<script type="application/json" id="categoriesData">@json($categories->map(fn($c) => ['id' => $c->id, 'name' => $c->name])->values())</script>
<script>
const allServices = JSON.parse(document.getElementById('allServicesData').textContent || '[]');
const selectedCategories = JSON.parse(document.getElementById('selectedCategoriesData').textContent || '[]').map(value => parseInt(value, 10)).filter(Number.isFinite);
const selectedServices = JSON.parse(document.getElementById('selectedServicesData').textContent || '[]').map(value => parseInt(value, 10)).filter(Number.isFinite);
const oldCustomServices = JSON.parse(document.getElementById('customServicesData').textContent || '[]');
const allCategories = JSON.parse(document.getElementById('categoriesData').textContent || '[]');

document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('supplierRegisterForm');
    const submitBtn = document.getElementById('submitBtn');
    const supplierTypeRadios = document.querySelectorAll('input[name="supplier_type"]');
    const commercialRegisterField = document.getElementById('commercial_register_field');
    const commercialRegisterFileField = document.getElementById('commercial_register_file_field');
    const commercialRegisterInput = document.getElementById('commercial_register');
    const commercialRegisterFileInput = document.getElementById('commercial_register_file');
    const primaryPhoneInput = document.getElementById('primary_phone');
    const secondaryPhoneInput = document.getElementById('secondary_phone');
    const maxTotalUploadBytes = 25 * 1024 * 1024;

    function sanitizePhoneValue(value) {
        if (typeof value !== 'string') return '';
        let v = value.replace(/[^0-9+]/g, '');
        const plus = v.startsWith('+') ? '+' : '';
        v = v.replace(/\+/g, '');
        return plus + v;
    }

    function attachPhoneSanitizer(input) {
        if (!input) return;
        input.value = sanitizePhoneValue(input.value);
        input.addEventListener('input', function() {
            const next = sanitizePhoneValue(input.value);
            if (next !== input.value) input.value = next;
        });
        input.addEventListener('paste', function() {
            setTimeout(function() {
                input.value = sanitizePhoneValue(input.value);
            }, 0);
        });
    }

    attachPhoneSanitizer(primaryPhoneInput);
    attachPhoneSanitizer(secondaryPhoneInput);

    // File size guard to avoid HTTP 413 before request is sent.
    const fileInputs = document.querySelectorAll('input[type="file"]');

    function getTotalSelectedFilesSize() {
        let total = 0;
        fileInputs.forEach((input) => {
            if (!input.files || input.files.length === 0) {
                return;
            }
            Array.from(input.files).forEach((file) => {
                total += (file.size || 0);
            });
        });
        return total;
    }

    function formatBytes(bytes) {
        return (bytes / (1024 * 1024)).toFixed(2) + ' MB';
    }

    // ===== إدارة الفئات والخدمات الديناميكية =====
    const categoryCheckboxes = document.querySelectorAll('.category-checkbox-input');
    
    categoryCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateServices);
    });

    function updateServices() {
        const currentSelectedServices = Array.from(document.querySelectorAll('.service-checkbox-input:checked'))
            .map(c => parseInt(c.value, 10))
            .filter(Number.isFinite);
        const effectiveSelectedServices = new Set([...selectedServices, ...currentSelectedServices]);

        // الحصول على الفئات المختارة
        const selected = Array.from(document.querySelectorAll('.category-checkbox-input:checked'))
            .map(c => parseInt(c.value));

        if (selected.length === 0) {
            document.getElementById('servicesContainer').innerHTML = `
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i> اختر الفئات أولاً لعرض الخدمات المتاحة
                </div>
            `;
            return;
        }

        // فلترة الخدمات حسب الفئات المختارة
        const filteredServices = allServices.filter(service => 
            selected.includes(service.category_id)
        );

        // تجميع الخدمات حسب الفئة
        const servicesByCategory = {};
        filteredServices.forEach(service => {
            if (!servicesByCategory[service.category_id]) {
                servicesByCategory[service.category_id] = [];
            }
            servicesByCategory[service.category_id].push(service);
        });

        // بناء HTML
        let html = '';
        selected.forEach(categoryId => {
            const categoryLabel = document.querySelector(`label[for="category_${categoryId}"]`);
            const categoryName = categoryLabel ? categoryLabel.textContent.trim().split('\n')[0] : 'غير محدد';
            const services = servicesByCategory[categoryId] || [];

            if (services.length > 0) {
                html += `<div class="mb-4">
                    <h6 class="text-muted mb-3 pb-2 border-bottom">
                        <i class="fas fa-folder me-2"></i> ${categoryName}
                    </h6>
                    <div class="row g-3">`;

                services.forEach(service => {
                    const isChecked = effectiveSelectedServices.has(service.id);
                    const displayText = service.subtitle && service.subtitle.trim() !== '' ? service.subtitle : service.name;
                    html += `
                        <div class="col-md-6">
                            <div class="form-check border rounded-3 p-3 service-checkbox">
                                <div class="d-flex justify-content-between align-items-start gap-3 service-checkbox-content">
                                    <div class="d-flex align-items-start gap-2 flex-grow-1 service-checkbox-main">
                                        <input class="form-check-input service-checkbox-input" 
                                               type="checkbox" 
                                               name="services[]" 
                                               value="${service.id}" 
                                               id="service_${service.id}"
                                               data-category-id="${service.category_id}"
                                               ${isChecked ? 'checked' : ''}>
                                        <label class="form-check-label cursor-pointer service-checkbox-label" for="service_${service.id}">
                                            <span class="fw-500">${displayText}</span>
                                        </label>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <a href="${service.url}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-primary service-action-btn">
                                            <i class="fas fa-up-right-from-square me-1"></i>تفاصيل الخدمة
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                });

                html += '</div></div>';
            }
        });

        document.getElementById('servicesContainer').innerHTML = html || 
            '<div class="alert alert-warning">لا توجد خدمات متاحة للفئات المختارة</div>';

        // منع اختيار خدمات من فئات غير مختارة
        validateServices();
    }

    function validateServices() {
        const selected = Array.from(document.querySelectorAll('.category-checkbox-input:checked'))
            .map(c => parseInt(c.value));

        document.querySelectorAll('.service-checkbox-input').forEach(checkbox => {
            const categoryId = parseInt(checkbox.getAttribute('data-category-id'));
            const isAllowed = selected.includes(categoryId);
            
            if (!isAllowed && checkbox.checked) {
                checkbox.checked = false;
            }
        });
    }

    // تهيئة عند التحميل
    if (selectedCategories.length > 0) {
        updateServices();
    }

    // إضافة خدمة مخصصة
    const customServicesContainer = document.getElementById('customServicesContainer');
    const addCustomServiceBtn = document.getElementById('addCustomServiceBtn');
    let customServiceIndex = 0;

    function getSelectedCategoryOptions() {
        const selectedIds = Array.from(document.querySelectorAll('.category-checkbox-input:checked')).map(el => parseInt(el.value, 10));
        const categories = allCategories.filter(c => selectedIds.includes(parseInt(c.id, 10)));
        if (categories.length === 0) {
            return '<option value="">اختر الفئة أولاً</option>';
        }

        return '<option value="">اختر الفئة</option>' + categories.map(c => `<option value="${c.id}">${c.name}</option>`).join('');
    }

    function escapeHtmlAttribute(value) {
        return String(value)
            .replace(/&/g, '&amp;')
            .replace(/"/g, '&quot;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;');
    }

    function addCustomServiceRow(initialData = {}) {
        const options = getSelectedCategoryOptions();
        const row = document.createElement('div');
        row.className = 'border rounded-3 p-3 mb-3 bg-white';
        const selectedCategoryId = initialData.category_id ? String(initialData.category_id) : '';
        const serviceName = initialData.name ? escapeHtmlAttribute(initialData.name) : '';
        row.innerHTML = `
            <div class="row g-2 align-items-end">
                <div class="col-md-5">
                    <label class="form-label small">الفئة</label>
                    <select class="form-select form-select-sm" name="custom_services[${customServiceIndex}][category_id]" required>
                        ${options}
                    </select>
                </div>
                <div class="col-md-5">
                    <label class="form-label small">اسم الخدمة</label>
                    <input type="text" class="form-control form-control-sm" name="custom_services[${customServiceIndex}][name]" value="${serviceName}" required>
                </div>
                <div class="col-md-2 text-end">
                    <button type="button" class="btn btn-sm btn-outline-danger remove-custom-service">حذف</button>
                </div>
            </div>
        `;

        customServicesContainer.appendChild(row);
        const select = row.querySelector('select[name*="[category_id]"]');
        if (selectedCategoryId && select && select.querySelector(`option[value="${selectedCategoryId}"]`)) {
            select.value = selectedCategoryId;
        }
        customServiceIndex += 1;
    }

    addCustomServiceBtn.addEventListener('click', addCustomServiceRow);
    customServicesContainer.addEventListener('click', (e) => {
        if (e.target.classList.contains('remove-custom-service')) {
            e.target.closest('.border').remove();
        }
    });

    // منع الإرسال إذا لم يتم اختيار خدمات
    form.addEventListener('submit', (e) => {
        const servicesChecked = document.querySelectorAll('.service-checkbox-input:checked').length > 0;
        const categoriesChecked = document.querySelectorAll('.category-checkbox-input:checked').length > 0;
        const customServicesCount = document.querySelectorAll('#customServicesContainer input[name*="[name]"]').length;

        if (!categoriesChecked) {
            e.preventDefault();
            alert('يرجى اختيار فئة واحدة على الأقل');
            form.scrollIntoView({ behavior: 'smooth', block: 'start' });
            return false;
        }

        if (!servicesChecked && customServicesCount === 0) {
            e.preventDefault();
            alert('يرجى اختيار خدمة واحدة على الأقل أو إضافة خدمة جديدة');
            document.getElementById('servicesContainer').scrollIntoView({ behavior: 'smooth', block: 'center' });
            return false;
        }

        const totalUploadSize = getTotalSelectedFilesSize();
        if (totalUploadSize > maxTotalUploadBytes) {
            e.preventDefault();
            alert(`إجمالي المرفقات كبير (${formatBytes(totalUploadSize)}). الحد الأقصى ${formatBytes(maxTotalUploadBytes)}. يرجى تقليل عدد/حجم الملفات.`);
            return false;
        }

        const spinner = submitBtn.querySelector('.spinner-border');
        submitBtn.disabled = true;
        spinner.classList.remove('d-none');
    });

    // ===== الكود الأصلي لإدارة نوع المورد =====
    function toggleCommercialRegister() {
        const supplierType = document.querySelector('input[name="supplier_type"]:checked')?.value;
        
        if (supplierType === 'company') {
            commercialRegisterField.style.display = 'block';
            commercialRegisterFileField.style.display = 'block';
            commercialRegisterInput.required = true;
            commercialRegisterFileInput.required = true;
        } else {
            commercialRegisterField.style.display = 'none';
            commercialRegisterFileField.style.display = 'none';
            commercialRegisterInput.required = false;
            commercialRegisterFileInput.required = false;
            commercialRegisterInput.value = '';
            commercialRegisterFileInput.value = '';
        }
    }

    supplierTypeRadios.forEach(radio => {
        radio.addEventListener('change', toggleCommercialRegister);
    });

    toggleCommercialRegister();

    // File upload preview
    fileInputs.forEach(input => {
        input.addEventListener('change', function(e) {
            const fileNameDiv = this.parentElement.querySelector('.file-name');
            
            if (this.files.length > 0) {
                if (this.multiple) {
                    fileNameDiv.textContent = `تم اختيار ${this.files.length} ملف`;
                } else {
                    fileNameDiv.textContent = `✓ ${this.files[0].name}`;
                }
                const totalUploadSize = getTotalSelectedFilesSize();
                if (totalUploadSize > maxTotalUploadBytes) {
                    fileNameDiv.classList.remove('text-success');
                    fileNameDiv.classList.add('text-danger');
                    fileNameDiv.textContent += ` — إجمالي المرفقات ${formatBytes(totalUploadSize)} (تجاوز الحد)`;
                } else {
                    fileNameDiv.classList.remove('text-danger');
                    fileNameDiv.classList.add('text-success');
                }
                fileNameDiv.classList.remove('d-none');
            } else {
                fileNameDiv.classList.add('d-none');
            }
        });
    });

    // Keep custom service category options in sync with selected categories.
    categoryCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const options = getSelectedCategoryOptions();
            customServicesContainer.querySelectorAll('select[name*="[category_id]"]').forEach(select => {
                const current = select.value;
                select.innerHTML = options;
                if (current && select.querySelector(`option[value="${current}"]`)) {
                    select.value = current;
                }
            });
        });
    });

    if (Array.isArray(oldCustomServices) && oldCustomServices.length > 0) {
        oldCustomServices.forEach((customService) => addCustomServiceRow(customService || {}));
    }
});
</script>
@endpush
@endsection

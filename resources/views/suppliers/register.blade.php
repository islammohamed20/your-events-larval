@extends('layouts.app')

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
                        <form method="POST" action="{{ route('suppliers.register') }}" enctype="multipart/form-data" id="supplierRegisterForm">
                            @csrf

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
                                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="5" required>{{ old('description') }}</textarea>
                                    <small class="text-muted">وصف تفصيلي عن خدماتك وخبراتك (500 حرف على الأقل)</small>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Section 2: الخدمات المقدمة -->
                            <div class="mb-5">
                                <h3 class="h4 fw-bold text-dark mb-4 pb-2 border-bottom border-warning border-3">
                                    <i class="fas fa-concierge-bell text-warning me-2"></i>الخدمات المقدمة
                                </h3>
                                <p class="text-muted mb-4">اختر الخدمات التي تقدمها (يمكنك اختيار أكثر من خدمة)</p>

                                <div class="row g-4">
                                    <!-- Photography -->
                                    <div class="col-md-6">
                                        <div class="form-check border rounded-3 p-3 h-100 service-checkbox">
                                            <input class="form-check-input" type="checkbox" name="services_offered[]" value="photography" id="service_photography" {{ in_array('photography', old('services_offered', [])) ? 'checked' : '' }}>
                                            <label class="form-check-label w-100 cursor-pointer" for="service_photography">
                                                <div class="d-flex align-items-start">
                                                    <i class="fas fa-camera text-warning fs-3 me-3 mt-1"></i>
                                                    <div>
                                                        <h5 class="mb-1">التصوير الفوتوغرافي</h5>
                                                        <p class="text-muted small mb-0">تصوير الفعاليات، حفلات الزفاف، المؤتمرات، والمناسبات الخاصة بجودة احترافية</p>
                                                    </div>
                                                </div>
                                            </label>
                                        </div>
                                    </div>

                                    <!-- Catering -->
                                    <div class="col-md-6">
                                        <div class="form-check border rounded-3 p-3 h-100 service-checkbox">
                                            <input class="form-check-input" type="checkbox" name="services_offered[]" value="catering" id="service_catering" {{ in_array('catering', old('services_offered', [])) ? 'checked' : '' }}>
                                            <label class="form-check-label w-100 cursor-pointer" for="service_catering">
                                                <div class="d-flex align-items-start">
                                                    <i class="fas fa-utensils text-warning fs-3 me-3 mt-1"></i>
                                                    <div>
                                                        <h5 class="mb-1">تقديم الطعام والضيافة</h5>
                                                        <p class="text-muted small mb-0">خدمات الكيترينج، بوفيهات مفتوحة، وجبات فاخرة، وضيافة متكاملة للفعاليات</p>
                                                    </div>
                                                </div>
                                            </label>
                                        </div>
                                    </div>

                                    <!-- Entertainment -->
                                    <div class="col-md-6">
                                        <div class="form-check border rounded-3 p-3 h-100 service-checkbox">
                                            <input class="form-check-input" type="checkbox" name="services_offered[]" value="entertainment" id="service_entertainment" {{ in_array('entertainment', old('services_offered', [])) ? 'checked' : '' }}>
                                            <label class="form-check-label w-100 cursor-pointer" for="service_entertainment">
                                                <div class="d-flex align-items-start">
                                                    <i class="fas fa-music text-warning fs-3 me-3 mt-1"></i>
                                                    <div>
                                                        <h5 class="mb-1">الترفيه والتنشيط</h5>
                                                        <p class="text-muted small mb-0">فرق موسيقية، منشطين، ألعاب، عروض حية، وأنشطة ترفيهية متنوعة</p>
                                                    </div>
                                                </div>
                                            </label>
                                        </div>
                                    </div>

                                    <!-- Gifts -->
                                    <div class="col-md-6">
                                        <div class="form-check border rounded-3 p-3 h-100 service-checkbox">
                                            <input class="form-check-input" type="checkbox" name="services_offered[]" value="gifts" id="service_gifts" {{ in_array('gifts', old('services_offered', [])) ? 'checked' : '' }}>
                                            <label class="form-check-label w-100 cursor-pointer" for="service_gifts">
                                                <div class="d-flex align-items-start">
                                                    <i class="fas fa-gift text-warning fs-3 me-3 mt-1"></i>
                                                    <div>
                                                        <h5 class="mb-1">الهدايا والتوزيعات</h5>
                                                        <p class="text-muted small mb-0">هدايا فاخرة، توزيعات مخصصة، سلال هدايا، وتغليف احترافي للمناسبات</p>
                                                    </div>
                                                </div>
                                            </label>
                                        </div>
                                    </div>

                                    <!-- Logistics -->
                                    <div class="col-md-6">
                                        <div class="form-check border rounded-3 p-3 h-100 service-checkbox">
                                            <input class="form-check-input" type="checkbox" name="services_offered[]" value="logistics" id="service_logistics" {{ in_array('logistics', old('services_offered', [])) ? 'checked' : '' }}>
                                            <label class="form-check-label w-100 cursor-pointer" for="service_logistics">
                                                <div class="d-flex align-items-start">
                                                    <i class="fas fa-truck text-warning fs-3 me-3 mt-1"></i>
                                                    <div>
                                                        <h5 class="mb-1">اللوجستيات والنقل</h5>
                                                        <p class="text-muted small mb-0">خدمات النقل، تأجير حافلات، تنسيق الانتقالات، وإدارة المواصلات للفعاليات</p>
                                                    </div>
                                                </div>
                                            </label>
                                        </div>
                                    </div>

                                    <!-- Handicrafts -->
                                    <div class="col-md-6">
                                        <div class="form-check border rounded-3 p-3 h-100 service-checkbox">
                                            <input class="form-check-input" type="checkbox" name="services_offered[]" value="handicrafts" id="service_handicrafts" {{ in_array('handicrafts', old('services_offered', [])) ? 'checked' : '' }}>
                                            <label class="form-check-label w-100 cursor-pointer" for="service_handicrafts">
                                                <div class="d-flex align-items-start">
                                                    <i class="fas fa-palette text-warning fs-3 me-3 mt-1"></i>
                                                    <div>
                                                        <h5 class="mb-1">الأعمال والحرف اليدوية</h5>
                                                        <p class="text-muted small mb-0">منتجات يدوية مخصصة، ديكورات فنية، لوحات، وأعمال حرفية تراثية وعصرية</p>
                                                    </div>
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                
                                @error('services_offered')
                                    <div class="text-danger small mt-2">{{ $message }}</div>
                                @enderror
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
                                        <input type="tel" class="form-control form-control-lg @error('primary_phone') is-invalid @enderror" id="primary_phone" name="primary_phone" value="{{ old('primary_phone') }}" required>
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
                                        <input type="tel" class="form-control form-control-lg @error('secondary_phone') is-invalid @enderror" id="secondary_phone" name="secondary_phone" value="{{ old('secondary_phone') }}">
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
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('supplierRegisterForm');
    const submitBtn = document.getElementById('submitBtn');
    const supplierTypeRadios = document.querySelectorAll('input[name="supplier_type"]');
    const commercialRegisterField = document.getElementById('commercial_register_field');
    const commercialRegisterFileField = document.getElementById('commercial_register_file_field');
    const commercialRegisterInput = document.getElementById('commercial_register');
    const commercialRegisterFileInput = document.getElementById('commercial_register_file');

    // Toggle commercial register visibility based on supplier type
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

    // Initialize on page load
    toggleCommercialRegister();

    // File upload preview
    const fileInputs = document.querySelectorAll('input[type="file"]');
    
    fileInputs.forEach(input => {
        input.addEventListener('change', function(e) {
            const fileNameDiv = this.parentElement.querySelector('.file-name');
            
            if (this.files.length > 0) {
                if (this.multiple) {
                    fileNameDiv.textContent = `تم اختيار ${this.files.length} ملف`;
                } else {
                    fileNameDiv.textContent = `✓ ${this.files[0].name}`;
                }
                fileNameDiv.classList.remove('d-none');
            } else {
                fileNameDiv.classList.add('d-none');
            }
        });
    });

    // Form submission with loading state
    form.addEventListener('submit', function(e) {
        const spinner = submitBtn.querySelector('.spinner-border');
        submitBtn.disabled = true;
        spinner.classList.remove('d-none');
    });

    // Service checkbox selection validation
    const serviceCheckboxes = document.querySelectorAll('input[name="services_offered[]"]');
    const servicesError = document.createElement('div');
    servicesError.className = 'text-danger small mt-2 d-none';
    servicesError.textContent = 'يرجى اختيار خدمة واحدة على الأقل';
    
    form.addEventListener('submit', function(e) {
        const checkedServices = document.querySelectorAll('input[name="services_offered[]"]:checked');
        
        if (checkedServices.length === 0) {
            e.preventDefault();
            const servicesContainer = document.querySelector('input[name="services_offered[]"]').closest('.mb-5');
            if (!servicesContainer.querySelector('.text-danger')) {
                servicesContainer.appendChild(servicesError);
            }
            servicesError.classList.remove('d-none');
            
            // Scroll to error
            servicesContainer.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    });

    serviceCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const checkedServices = document.querySelectorAll('input[name="services_offered[]"]:checked');
            if (checkedServices.length > 0) {
                servicesError.classList.add('d-none');
            }
        });
    });
});
</script>
@endpush
@endsection

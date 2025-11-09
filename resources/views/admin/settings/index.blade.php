@extends('layouts.admin')

@section('title', 'الإعدادات العامة - Your Events')
@section('page-title', 'الإعدادات العامة')
@section('page-description', 'إدارة إعدادات الموقع العامة والتخصيصات')

@section('content')
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="row">
    <!-- Settings Navigation -->
    <div class="col-lg-3">
        <div class="card">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="fas fa-cog me-2"></i>أقسام الإعدادات
                </h6>
            </div>
            <div class="list-group list-group-flush">
                <a href="#general" class="list-group-item list-group-item-action active" data-bs-toggle="pill">
                    <i class="fas fa-info-circle me-2"></i>معلومات عامة
                </a>
                <a href="#contact" class="list-group-item list-group-item-action" data-bs-toggle="pill">
                    <i class="fas fa-address-book me-2"></i>معلومات التواصل
                </a>
                <a href="#social" class="list-group-item list-group-item-action" data-bs-toggle="pill">
                    <i class="fas fa-share-alt me-2"></i>وسائل التواصل
                </a>
                <a href="#appearance" class="list-group-item list-group-item-action" data-bs-toggle="pill">
                    <i class="fas fa-palette me-2"></i>المظهر والألوان
                </a>
                <a href="#seo" class="list-group-item list-group-item-action" data-bs-toggle="pill">
                    <i class="fas fa-search me-2"></i>تحسين محركات البحث
                </a>
                <a href="#email" class="list-group-item list-group-item-action" data-bs-toggle="pill">
                    <i class="fas fa-envelope me-2"></i>إعدادات البريد
                </a>
                <a href="#maintenance" class="list-group-item list-group-item-action" data-bs-toggle="pill">
                    <i class="fas fa-tools me-2"></i>وضع الصيانة
                </a>
                <a href="#backup" class="list-group-item list-group-item-action" data-bs-toggle="pill">
                    <i class="fas fa-database me-2"></i>قاعدة البيانات والنسخ الاحتياطي
                </a>
            </div>
        </div>
    </div>
    
    <!-- Settings Content -->
    <div class="col-lg-9">
        <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="tab-content">
                <!-- General Settings -->
                <div class="tab-pane fade show active" id="general">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-info-circle me-2"></i>المعلومات العامة
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="site_name" class="form-label">اسم الموقع</label>
                                        <input type="text" 
                                               class="form-control @error('site_name') is-invalid @enderror" 
                                               id="site_name" 
                                               name="site_name" 
                                               value="{{ old('site_name', $settings['site_name'] ?? 'Your Events') }}">
                                        @error('site_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="site_tagline" class="form-label">شعار الموقع</label>
                                        <input type="text" 
                                               class="form-control @error('site_tagline') is-invalid @enderror" 
                                               id="site_tagline" 
                                               name="site_tagline" 
                                               value="{{ old('site_tagline', $settings['site_tagline'] ?? 'تجارب واقع افتراضي استثنائية') }}">
                                        @error('site_tagline')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="site_description" class="form-label">وصف الموقع</label>
                                <textarea class="form-control @error('site_description') is-invalid @enderror" 
                                          id="site_description" 
                                          name="site_description" 
                                          rows="3">{{ old('site_description', $settings['site_description'] ?? 'نقدم تجارب واقع افتراضي مبتكرة وفعاليات تفاعلية استثنائية') }}</textarea>
                                @error('site_description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="mb-3">
                                <label for="terms_and_conditions" class="form-label">
                                    <i class="fas fa-file-contract me-2"></i>الشروط والأحكام
                                </label>
                                <textarea class="form-control @error('terms_and_conditions') is-invalid @enderror" 
                                          id="terms_and_conditions" 
                                          name="terms_and_conditions" 
                                          rows="10"
                                          placeholder="اكتب الشروط والأحكام الخاصة بالموقع هنا...">{{ old('terms_and_conditions', $settings['terms_and_conditions'] ?? '') }}</textarea>
                                <div class="form-text">
                                    <i class="fas fa-info-circle me-1"></i>
                                    يمكنك استخدام الأسطر الجديدة لتنظيم المحتوى. سيتم عرضها في صفحة منفصلة يمكن الوصول إليها من تذييل الموقع.
                                </div>
                                @error('terms_and_conditions')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="site_logo" class="form-label">شعار الموقع</label>
                                        <input type="file" 
                                               class="form-control @error('site_logo') is-invalid @enderror" 
                                               id="site_logo" 
                                               name="site_logo" 
                                               accept="image/*">
                                        <div class="form-text">الصيغ المدعومة: PNG, JPG, SVG (مفضل)</div>
                                        @error('site_logo')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        @if(isset($settings['site_logo']) && $settings['site_logo'])
                                            <div class="mt-2">
                                                <img src="{{ asset('storage/' . $settings['site_logo']) }}" 
                                                     alt="الشعار الحالي" 
                                                     class="img-thumbnail" 
                                                     style="max-height: 60px;">
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="site_favicon" class="form-label">أيقونة الموقع (Favicon)</label>
                                        <input type="file" 
                                               class="form-control @error('site_favicon') is-invalid @enderror" 
                                               id="site_favicon" 
                                               name="site_favicon" 
                                               accept="image/*">
                                        <div class="form-text">الصيغ المدعومة: ICO, PNG (32x32 مفضل)</div>
                                        @error('site_favicon')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Contact Settings -->
                <div class="tab-pane fade" id="contact">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-address-book me-2"></i>معلومات التواصل
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="contact_phone" class="form-label">رقم الهاتف</label>
                                        <input type="text" 
                                               class="form-control @error('contact_phone') is-invalid @enderror" 
                                               id="contact_phone" 
                                               name="contact_phone" 
                                               value="{{ old('contact_phone', $settings['contact_phone'] ?? '+966 50 123 4567') }}">
                                        @error('contact_phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="contact_email" class="form-label">البريد الإلكتروني</label>
                                        <input type="email" 
                                               class="form-control @error('contact_email') is-invalid @enderror" 
                                               id="contact_email" 
                                               name="contact_email" 
                                               value="{{ old('contact_email', $settings['contact_email'] ?? 'info@yourevents.com') }}">
                                        @error('contact_email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="contact_address" class="form-label">العنوان</label>
                                <textarea class="form-control @error('contact_address') is-invalid @enderror" 
                                          id="contact_address" 
                                          name="contact_address" 
                                          rows="2">{{ old('contact_address', $settings['contact_address'] ?? 'الرياض، المملكة العربية السعودية') }}</textarea>
                                @error('contact_address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="working_hours" class="form-label">ساعات العمل</label>
                                        <input type="text" 
                                               class="form-control @error('working_hours') is-invalid @enderror" 
                                               id="working_hours" 
                                               name="working_hours" 
                                               value="{{ old('working_hours', $settings['working_hours'] ?? 'الأحد - الخميس: 9:00 ص - 6:00 م') }}">
                                        @error('working_hours')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="whatsapp_number" class="form-label">رقم الواتساب</label>
                                        <input type="text" 
                                               class="form-control @error('whatsapp_number') is-invalid @enderror" 
                                               id="whatsapp_number" 
                                               name="whatsapp_number" 
                                               value="{{ old('whatsapp_number', $settings['whatsapp_number'] ?? '+966501234567') }}">
                                        @error('whatsapp_number')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Social Media Settings -->
                <div class="tab-pane fade" id="social">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-share-alt me-2"></i>وسائل التواصل الاجتماعي
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="facebook_url" class="form-label">
                                            <i class="fab fa-facebook text-primary me-2"></i>فيسبوك
                                        </label>
                                        <input type="url" 
                                               class="form-control @error('facebook_url') is-invalid @enderror" 
                                               id="facebook_url" 
                                               name="facebook_url" 
                                               value="{{ old('facebook_url', $settings['facebook_url'] ?? '') }}" 
                                               placeholder="https://facebook.com/yourevents">
                                        @error('facebook_url')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="twitter_url" class="form-label">
                                            <i class="fab fa-x-twitter text-dark me-2"></i>X (تويتر سابقاً)
                                        </label>
                                        <input type="url" 
                                               class="form-control @error('twitter_url') is-invalid @enderror" 
                                               id="twitter_url" 
                                               name="twitter_url" 
                                               value="{{ old('twitter_url', $settings['twitter_url'] ?? '') }}" 
                                               placeholder="https://x.com/yourevents">
                                        @error('twitter_url')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="instagram_url" class="form-label">
                                            <i class="fab fa-instagram text-danger me-2"></i>إنستغرام
                                        </label>
                                        <input type="url" 
                                               class="form-control @error('instagram_url') is-invalid @enderror" 
                                               id="instagram_url" 
                                               name="instagram_url" 
                                               value="{{ old('instagram_url', $settings['instagram_url'] ?? '') }}" 
                                               placeholder="https://instagram.com/yourevents">
                                        @error('instagram_url')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="linkedin_url" class="form-label">
                                            <i class="fab fa-linkedin text-primary me-2"></i>لينكد إن
                                        </label>
                                        <input type="url" 
                                               class="form-control @error('linkedin_url') is-invalid @enderror" 
                                               id="linkedin_url" 
                                               name="linkedin_url" 
                                               value="{{ old('linkedin_url', $settings['linkedin_url'] ?? '') }}" 
                                               placeholder="https://linkedin.com/company/yourevents">
                                        @error('linkedin_url')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="youtube_url" class="form-label">
                                            <i class="fab fa-youtube text-danger me-2"></i>يوتيوب
                                        </label>
                                        <input type="url" 
                                               class="form-control @error('youtube_url') is-invalid @enderror" 
                                               id="youtube_url" 
                                               name="youtube_url" 
                                               value="{{ old('youtube_url', $settings['youtube_url'] ?? '') }}" 
                                               placeholder="https://youtube.com/c/yourevents">
                                        @error('youtube_url')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="tiktok_url" class="form-label">
                                            <i class="fab fa-tiktok text-dark me-2"></i>تيك توك
                                        </label>
                                        <input type="url" 
                                               class="form-control @error('tiktok_url') is-invalid @enderror" 
                                               id="tiktok_url" 
                                               name="tiktok_url" 
                                               value="{{ old('tiktok_url', $settings['tiktok_url'] ?? '') }}" 
                                               placeholder="https://tiktok.com/@yourevents">
                                        @error('tiktok_url')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            
                            <!-- WhatsApp Row -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="whatsapp_url" class="form-label">
                                            <i class="fab fa-whatsapp text-success me-2"></i>واتساب
                                        </label>
                                        <input type="url" 
                                               class="form-control @error('whatsapp_url') is-invalid @enderror" 
                                               id="whatsapp_url" 
                                               name="whatsapp_url" 
                                               value="{{ old('whatsapp_url', $settings['whatsapp_url'] ?? '') }}" 
                                               placeholder="https://wa.me/966501234567">
                                        @error('whatsapp_url')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Email Settings -->
                <div class="tab-pane fade" id="email">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-envelope me-2"></i>إعدادات البريد الإلكتروني
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="smtp_host" class="form-label">خادم SMTP</label>
                                        <input type="text" 
                                               class="form-control @error('smtp_host') is-invalid @enderror" 
                                               id="smtp_host" 
                                               name="smtp_host" 
                                               value="{{ old('smtp_host', $settings['smtp_host'] ?? '') }}"
                                               placeholder="smtp.gmail.com">
                                        @error('smtp_host')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="smtp_port" class="form-label">منفذ SMTP</label>
                                        <input type="number" 
                                               class="form-control @error('smtp_port') is-invalid @enderror" 
                                               id="smtp_port" 
                                               name="smtp_port" 
                                               value="{{ old('smtp_port', $settings['smtp_port'] ?? '587') }}"
                                               placeholder="587">
                                        @error('smtp_port')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="smtp_username" class="form-label">اسم المستخدم</label>
                                        <input type="text" 
                                               class="form-control @error('smtp_username') is-invalid @enderror" 
                                               id="smtp_username" 
                                               name="smtp_username" 
                                               value="{{ old('smtp_username', $settings['smtp_username'] ?? '') }}"
                                               placeholder="your-email@gmail.com">
                                        @error('smtp_username')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="smtp_password" class="form-label">كلمة المرور</label>
                                        <input type="password" 
                                               class="form-control @error('smtp_password') is-invalid @enderror" 
                                               id="smtp_password" 
                                               name="smtp_password" 
                                               value="{{ old('smtp_password', $settings['smtp_password'] ?? '') }}"
                                               placeholder="كلمة مرور البريد الإلكتروني">
                                        @error('smtp_password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="smtp_encryption" class="form-label">نوع التشفير</label>
                                        <select class="form-select @error('smtp_encryption') is-invalid @enderror" 
                                                id="smtp_encryption" 
                                                name="smtp_encryption">
                                            <option value="tls" {{ old('smtp_encryption', $settings['smtp_encryption'] ?? 'tls') == 'tls' ? 'selected' : '' }}>TLS</option>
                                            <option value="ssl" {{ old('smtp_encryption', $settings['smtp_encryption'] ?? 'tls') == 'ssl' ? 'selected' : '' }}>SSL</option>
                                        </select>
                                        @error('smtp_encryption')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">اختبار الإعدادات</label>
                                        <div>
                                            <button type="button" class="btn btn-outline-primary" id="test-email">
                                                <i class="fas fa-paper-plane me-2"></i>إرسال بريد تجريبي
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>ملاحظة:</strong> تأكد من تفعيل "تطبيقات أقل أماناً" في إعدادات Gmail أو استخدم كلمة مرور التطبيق.
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Appearance Settings -->
                <div class="tab-pane fade" id="appearance">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-palette me-2"></i>إعدادات المظهر والألوان
                            </h5>
                        </div>
                        <div class="card-body">
                            <h6 class="mb-3">الألوان الأساسية</h6>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="primary_color" class="form-label">اللون الأساسي</label>
                                        <input type="color" 
                                               class="form-control form-control-color @error('primary_color') is-invalid @enderror" 
                                               id="primary_color" 
                                               name="primary_color" 
                                               value="{{ old('primary_color', $settings['primary_color'] ?? '#1f144a') }}">
                                        @error('primary_color')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="secondary_color" class="form-label">اللون الثانوي</label>
                                        <input type="color" 
                                               class="form-control form-control-color @error('secondary_color') is-invalid @enderror" 
                                               id="secondary_color" 
                                               name="secondary_color" 
                                               value="{{ old('secondary_color', $settings['secondary_color'] ?? '#2dbcae') }}">
                                        @error('secondary_color')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="accent_color" class="form-label">لون التمييز</label>
                                        <input type="color" 
                                               class="form-control form-control-color @error('accent_color') is-invalid @enderror" 
                                               id="accent_color" 
                                               name="accent_color" 
                                               value="{{ old('accent_color', $settings['accent_color'] ?? '#ef4870') }}">
                                        @error('accent_color')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="gold_color" class="form-label">اللون الذهبي</label>
                                        <input type="color" 
                                               class="form-control form-control-color @error('gold_color') is-invalid @enderror" 
                                               id="gold_color" 
                                               name="gold_color" 
                                               value="{{ old('gold_color', $settings['gold_color'] ?? '#f0c71d') }}">
                                        @error('gold_color')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            
                            <hr>
                            <h6 class="mb-3">ألوان الخلفية والنص</h6>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="bg_light" class="form-label">خلفية فاتحة</label>
                                        <input type="color" 
                                               class="form-control form-control-color @error('bg_light') is-invalid @enderror" 
                                               id="bg_light" 
                                               name="bg_light" 
                                               value="{{ old('bg_light', $settings['bg_light'] ?? '#ffffff') }}">
                                        @error('bg_light')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="bg_secondary" class="form-label">خلفية ثانوية</label>
                                        <input type="color" 
                                               class="form-control form-control-color @error('bg_secondary') is-invalid @enderror" 
                                               id="bg_secondary" 
                                               name="bg_secondary" 
                                               value="{{ old('bg_secondary', $settings['bg_secondary'] ?? '#f8f9fa') }}">
                                        @error('bg_secondary')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="text_color" class="form-label">لون النص</label>
                                        <input type="color" 
                                               class="form-control form-control-color @error('text_color') is-invalid @enderror" 
                                               id="text_color" 
                                               name="text_color" 
                                               value="{{ old('text_color', $settings['text_color'] ?? '#222222') }}">
                                        @error('text_color')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            
                            <hr>
                            <h6 class="mb-3">الخطوط</h6>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="font_family_primary" class="form-label">الخط الأساسي</label>
                                        <input type="text" 
                                               class="form-control @error('font_family_primary') is-invalid @enderror" 
                                               id="font_family_primary" 
                                               name="font_family_primary" 
                                               value="{{ old('font_family_primary', $settings['font_family_primary'] ?? 'Tajawal') }}"
                                               placeholder="Tajawal">
                                        @error('font_family_primary')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="font_family_secondary" class="form-label">الخط الثانوي</label>
                                        <input type="text" 
                                               class="form-control @error('font_family_secondary') is-invalid @enderror" 
                                               id="font_family_secondary" 
                                               name="font_family_secondary" 
                                               value="{{ old('font_family_secondary', $settings['font_family_secondary'] ?? 'Amiri') }}"
                                               placeholder="Amiri">
                                        @error('font_family_secondary')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="font_family_english" class="form-label">الخط الإنجليزي</label>
                                        <input type="text" 
                                               class="form-control @error('font_family_english') is-invalid @enderror" 
                                               id="font_family_english" 
                                               name="font_family_english" 
                                               value="{{ old('font_family_english', $settings['font_family_english'] ?? 'Inter') }}"
                                               placeholder="Inter">
                                        @error('font_family_english')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- SEO Settings -->
                <div class="tab-pane fade" id="seo">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-search me-2"></i>إعدادات تحسين محركات البحث
                            </h5>
                        </div>
                        <div class="card-body">
                            <h6 class="mb-3">Meta Tags الأساسية</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="meta_title" class="form-label">عنوان الصفحة (Meta Title)</label>
                                        <input type="text" 
                                               class="form-control @error('meta_title') is-invalid @enderror" 
                                               id="meta_title" 
                                               name="meta_title" 
                                               value="{{ old('meta_title', $settings['meta_title'] ?? '') }}"
                                               maxlength="60"
                                               placeholder="Your Events - تجارب واقع افتراضي استثنائية">
                                        <small class="text-muted">الحد الأقصى 60 حرف</small>
                                        @error('meta_title')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="meta_keywords" class="form-label">الكلمات المفتاحية</label>
                                        <input type="text" 
                                               class="form-control @error('meta_keywords') is-invalid @enderror" 
                                               id="meta_keywords" 
                                               name="meta_keywords" 
                                               value="{{ old('meta_keywords', $settings['meta_keywords'] ?? '') }}"
                                               placeholder="واقع افتراضي, فعاليات, تجارب تفاعلية">
                                        @error('meta_keywords')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="meta_description" class="form-label">وصف الصفحة (Meta Description)</label>
                                <textarea class="form-control @error('meta_description') is-invalid @enderror" 
                                          id="meta_description" 
                                          name="meta_description" 
                                          rows="3"
                                          maxlength="160"
                                          placeholder="نقدم تجارب واقع افتراضي مبتكرة وفعاليات تفاعلية استثنائية في المملكة العربية السعودية">{{ old('meta_description', $settings['meta_description'] ?? '') }}</textarea>
                                <small class="text-muted">الحد الأقصى 160 حرف</small>
                                @error('meta_description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <hr>
                            <h6 class="mb-3">Open Graph (Facebook)</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="og_title" class="form-label">عنوان OG</label>
                                        <input type="text" 
                                               class="form-control @error('og_title') is-invalid @enderror" 
                                               id="og_title" 
                                               name="og_title" 
                                               value="{{ old('og_title', $settings['og_title'] ?? '') }}"
                                               placeholder="Your Events - تجارب واقع افتراضي">
                                        @error('og_title')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="og_image" class="form-label">صورة OG</label>
                                        <input type="url" 
                                               class="form-control @error('og_image') is-invalid @enderror" 
                                               id="og_image" 
                                               name="og_image" 
                                               value="{{ old('og_image', $settings['og_image'] ?? '') }}"
                                               placeholder="https://yourevents.com/images/og-image.jpg">
                                        @error('og_image')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="og_description" class="form-label">وصف OG</label>
                                <textarea class="form-control @error('og_description') is-invalid @enderror" 
                                          id="og_description" 
                                          name="og_description" 
                                          rows="2"
                                          placeholder="نقدم تجارب واقع افتراضي مبتكرة وفعاليات تفاعلية استثنائية">{{ old('og_description', $settings['og_description'] ?? '') }}</textarea>
                                @error('og_description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <hr>
                            <h6 class="mb-3">Twitter Cards</h6>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="twitter_card" class="form-label">نوع البطاقة</label>
                                        <select class="form-select @error('twitter_card') is-invalid @enderror" 
                                                id="twitter_card" 
                                                name="twitter_card">
                                            <option value="summary" {{ old('twitter_card', $settings['twitter_card'] ?? 'summary_large_image') == 'summary' ? 'selected' : '' }}>Summary</option>
                                            <option value="summary_large_image" {{ old('twitter_card', $settings['twitter_card'] ?? 'summary_large_image') == 'summary_large_image' ? 'selected' : '' }}>Summary Large Image</option>
                                        </select>
                                        @error('twitter_card')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="twitter_site" class="form-label">حساب الموقع</label>
                                        <input type="text" 
                                               class="form-control @error('twitter_site') is-invalid @enderror" 
                                               id="twitter_site" 
                                               name="twitter_site" 
                                               value="{{ old('twitter_site', $settings['twitter_site'] ?? '') }}"
                                               placeholder="@yourevents">
                                        @error('twitter_site')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="twitter_creator" class="form-label">حساب المنشئ</label>
                                        <input type="text" 
                                               class="form-control @error('twitter_creator') is-invalid @enderror" 
                                               id="twitter_creator" 
                                               name="twitter_creator" 
                                               value="{{ old('twitter_creator', $settings['twitter_creator'] ?? '') }}"
                                               placeholder="@creator">
                                        @error('twitter_creator')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            
                            <hr>
                            <h6 class="mb-3">أدوات التحليل</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="google_analytics_id" class="form-label">Google Analytics ID</label>
                                        <input type="text" 
                                               class="form-control @error('google_analytics_id') is-invalid @enderror" 
                                               id="google_analytics_id" 
                                               name="google_analytics_id" 
                                               value="{{ old('google_analytics_id', $settings['google_analytics_id'] ?? '') }}"
                                               placeholder="G-XXXXXXXXXX">
                                        @error('google_analytics_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="google_site_verification" class="form-label">Google Site Verification</label>
                                        <input type="text" 
                                               class="form-control @error('google_site_verification') is-invalid @enderror" 
                                               id="google_site_verification" 
                                               name="google_site_verification" 
                                               value="{{ old('google_site_verification', $settings['google_site_verification'] ?? '') }}"
                                               placeholder="كود التحقق من Google">
                                        @error('google_site_verification')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Maintenance Mode Settings -->
                <div class="tab-pane fade" id="maintenance">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-tools me-2"></i>وضع الصيانة
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                عند تفعيل وضع الصيانة، سيتم إظهار صفحة صيانة للزوار بينما يمكن للمديرين الوصول للموقع بشكل طبيعي.
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">حالة وضع الصيانة</label>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" 
                                                   type="checkbox" 
                                                   id="maintenance_mode" 
                                                   name="maintenance_mode" 
                                                   value="1"
                                                   {{ old('maintenance_mode', $settings['maintenance_mode'] ?? false) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="maintenance_mode">
                                                تفعيل وضع الصيانة
                                            </label>
                                        </div>
                                        <small class="text-muted">عند التفعيل، سيتم إظهار صفحة الصيانة للزوار</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="maintenance_end_time" class="form-label">وقت انتهاء الصيانة المتوقع</label>
                                        <input type="datetime-local" 
                                               class="form-control @error('maintenance_end_time') is-invalid @enderror" 
                                               id="maintenance_end_time" 
                                               name="maintenance_end_time" 
                                               value="{{ old('maintenance_end_time', $settings['maintenance_end_time'] ?? '') }}">
                                        @error('maintenance_end_time')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="maintenance_title" class="form-label">عنوان صفحة الصيانة</label>
                                <input type="text" 
                                       class="form-control @error('maintenance_title') is-invalid @enderror" 
                                       id="maintenance_title" 
                                       name="maintenance_title" 
                                       value="{{ old('maintenance_title', $settings['maintenance_title'] ?? 'الموقع تحت الصيانة') }}"
                                       placeholder="الموقع تحت الصيانة">
                                @error('maintenance_title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="mb-3">
                                <label for="maintenance_message" class="form-label">رسالة صفحة الصيانة</label>
                                <textarea class="form-control @error('maintenance_message') is-invalid @enderror" 
                                          id="maintenance_message" 
                                          name="maintenance_message" 
                                          rows="4"
                                          placeholder="نعتذر عن الإزعاج، الموقع تحت الصيانة حالياً لتحسين الخدمة. سنعود قريباً!">{{ old('maintenance_message', $settings['maintenance_message'] ?? 'نعتذر عن الإزعاج، الموقع تحت الصيانة حالياً لتحسين الخدمة. سنعود قريباً!') }}</textarea>
                                @error('maintenance_message')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="maintenance_contact_email" class="form-label">بريد التواصل أثناء الصيانة</label>
                                        <input type="email" 
                                               class="form-control @error('maintenance_contact_email') is-invalid @enderror" 
                                               id="maintenance_contact_email" 
                                               name="maintenance_contact_email" 
                                               value="{{ old('maintenance_contact_email', $settings['maintenance_contact_email'] ?? '') }}"
                                               placeholder="support@yourevents.com">
                                        @error('maintenance_contact_email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="maintenance_retry_after" class="form-label">إعادة المحاولة بعد (بالثواني)</label>
                                        <input type="number" 
                                               class="form-control @error('maintenance_retry_after') is-invalid @enderror" 
                                               id="maintenance_retry_after" 
                                               name="maintenance_retry_after" 
                                               value="{{ old('maintenance_retry_after', $settings['maintenance_retry_after'] ?? '3600') }}"
                                               min="60"
                                               placeholder="3600">
                                        <small class="text-muted">الافتراضي: 3600 ثانية (ساعة واحدة)</small>
                                        @error('maintenance_retry_after')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">عناوين IP المسموح لها بالوصول</label>
                                <textarea class="form-control @error('maintenance_allowed_ips') is-invalid @enderror" 
                                          id="maintenance_allowed_ips" 
                                          name="maintenance_allowed_ips" 
                                          rows="3"
                                          placeholder="192.168.1.1&#10;10.0.0.1&#10;127.0.0.1">{{ old('maintenance_allowed_ips', $settings['maintenance_allowed_ips'] ?? '') }}</textarea>
                                <small class="text-muted">ضع كل عنوان IP في سطر منفصل. هذه العناوين ستتمكن من الوصول للموقع حتى أثناء الصيانة.</small>
                                @error('maintenance_allowed_ips')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <strong>تنبيه:</strong> تأكد من إضافة عنوان IP الخاص بك في القائمة المسموحة قبل تفعيل وضع الصيانة لتجنب منع نفسك من الوصول.
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Backup & Database Settings -->
                <div class="tab-pane fade" id="backup">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-database me-2"></i>إعدادات قاعدة البيانات والنسخ الاحتياطي
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">معلومات قاعدة البيانات الحالية</label>
                                        <div class="p-3 bg-light border rounded">
                                            <div><strong>المضيف:</strong> {{ config('database.connections.mysql.host') }}</div>
                                            <div><strong>المنفذ:</strong> {{ config('database.connections.mysql.port') }}</div>
                                            <div><strong>قاعدة البيانات:</strong> {{ config('database.connections.mysql.database') }}</div>
                                            <div><strong>المستخدم:</strong> {{ config('database.connections.mysql.username') }}</div>
                                            <small class="text-muted d-block mt-2">
                                                لإدارة اتصال قاعدة البيانات يفضّل تعديل قيم البيئة (env). هذه المعلومات للعرض فقط.
                                            </small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">إعدادات النسخ الاحتياطي</label>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="backup_frequency" class="form-label">التكرار</label>
                                                    <select class="form-select @error('backup_frequency') is-invalid @enderror" id="backup_frequency" name="backup_frequency">
                                                        <option value="manual" {{ old('backup_frequency', $settings['backup_frequency'] ?? 'manual') == 'manual' ? 'selected' : '' }}>يدوي</option>
                                                        <option value="daily" {{ old('backup_frequency', $settings['backup_frequency'] ?? 'manual') == 'daily' ? 'selected' : '' }}>يومي</option>
                                                        <option value="weekly" {{ old('backup_frequency', $settings['backup_frequency'] ?? 'manual') == 'weekly' ? 'selected' : '' }}>أسبوعي</option>
                                                    </select>
                                                    @error('backup_frequency')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="backup_retention" class="form-label">عدد النسخ المحفوظة</label>
                                                    <input type="number" class="form-control @error('backup_retention') is-invalid @enderror" id="backup_retention" name="backup_retention" value="{{ old('backup_retention', $settings['backup_retention'] ?? '7') }}" min="1">
                                                    <small class="text-muted">سيتم حذف النسخ الأقدم تلقائياً</small>
                                                    @error('backup_retention')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-check form-switch mb-3">
                                            <input class="form-check-input" type="checkbox" id="backup_compress" name="backup_compress" value="1" {{ old('backup_compress', $settings['backup_compress'] ?? true) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="backup_compress">ضغط ملف النسخة (Gzip)</label>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">المسار المحلي للنسخ</label>
                                            <div class="form-control">storage/app/backups</div>
                                            <small class="text-muted">سيتم حفظ النسخ في هذا المسار داخل الخادم</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <hr>

                            <h6 class="mb-3"><i class="fas fa-cloud me-2"></i>الربط بالسحابة</h6>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" id="backup_cloud_enabled" name="backup_cloud_enabled" value="1" {{ old('backup_cloud_enabled', $settings['backup_cloud_enabled'] ?? false) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="backup_cloud_enabled">تفعيل رفع النسخ للسحابة</label>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <div class="mb-3">
                                        <label for="backup_cloud_provider" class="form-label">المزود</label>
                                        <select class="form-select @error('backup_cloud_provider') is-invalid @enderror" id="backup_cloud_provider" name="backup_cloud_provider">
                                            <option value="none" {{ old('backup_cloud_provider', $settings['backup_cloud_provider'] ?? 'none') == 'none' ? 'selected' : '' }}>بدون</option>
                                            <option value="google_drive" {{ old('backup_cloud_provider', $settings['backup_cloud_provider'] ?? 'none') == 'google_drive' ? 'selected' : '' }}>Google Drive</option>
                                            <option value="mega" {{ old('backup_cloud_provider', $settings['backup_cloud_provider'] ?? 'none') == 'mega' ? 'selected' : '' }}>Mega</option>
                                            <option value="onedrive" {{ old('backup_cloud_provider', $settings['backup_cloud_provider'] ?? 'none') == 'onedrive' ? 'selected' : '' }}>OneDrive</option>
                                        </select>
                                        @error('backup_cloud_provider')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div id="cloud-google" class="border rounded p-3 mb-3" style="display: none;">
                                <h6 class="mb-3"><i class="fab fa-google-drive me-2"></i>إعدادات Google Drive</h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="google_drive_client_id" class="form-label">Client ID</label>
                                            <input type="text" class="form-control" id="google_drive_client_id" name="google_drive_client_id" value="{{ old('google_drive_client_id', $settings['google_drive_client_id'] ?? '') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="google_drive_client_secret" class="form-label">Client Secret</label>
                                            <input type="text" class="form-control" id="google_drive_client_secret" name="google_drive_client_secret" value="{{ old('google_drive_client_secret', $settings['google_drive_client_secret'] ?? '') }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="google_drive_refresh_token" class="form-label">Refresh Token</label>
                                            <input type="text" class="form-control" id="google_drive_refresh_token" name="google_drive_refresh_token" value="{{ old('google_drive_refresh_token', $settings['google_drive_refresh_token'] ?? '') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="google_drive_folder_id" class="form-label">Folder ID (اختياري)</label>
                                            <input type="text" class="form-control" id="google_drive_folder_id" name="google_drive_folder_id" value="{{ old('google_drive_folder_id', $settings['google_drive_folder_id'] ?? '') }}">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div id="cloud-mega" class="border rounded p-3 mb-3" style="display: none;">
                                <h6 class="mb-3"><i class="fab fa-megaport me-2"></i>إعدادات Mega</h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="mega_email" class="form-label">البريد</label>
                                            <input type="email" class="form-control" id="mega_email" name="mega_email" value="{{ old('mega_email', $settings['mega_email'] ?? '') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="mega_password" class="form-label">كلمة المرور</label>
                                            <input type="password" class="form-control" id="mega_password" name="mega_password" value="{{ old('mega_password', $settings['mega_password'] ?? '') }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="alert alert-warning mb-0"><i class="fas fa-info-circle me-2"></i>سيتم دعم الرفع إلى Mega لاحقاً عبر التكامل المناسب.</div>
                            </div>

                            <div id="cloud-onedrive" class="border rounded p-3 mb-3" style="display: none;">
                                <h6 class="mb-3"><i class="fab fa-microsoft me-2"></i>إعدادات OneDrive</h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="onedrive_client_id" class="form-label">Client ID</label>
                                            <input type="text" class="form-control" id="onedrive_client_id" name="onedrive_client_id" value="{{ old('onedrive_client_id', $settings['onedrive_client_id'] ?? '') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="onedrive_client_secret" class="form-label">Client Secret</label>
                                            <input type="text" class="form-control" id="onedrive_client_secret" name="onedrive_client_secret" value="{{ old('onedrive_client_secret', $settings['onedrive_client_secret'] ?? '') }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="onedrive_tenant_id" class="form-label">Tenant ID</label>
                                            <input type="text" class="form-control" id="onedrive_tenant_id" name="onedrive_tenant_id" value="{{ old('onedrive_tenant_id', $settings['onedrive_tenant_id'] ?? '') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="onedrive_refresh_token" class="form-label">Refresh Token</label>
                                            <input type="text" class="form-control" id="onedrive_refresh_token" name="onedrive_refresh_token" value="{{ old('onedrive_refresh_token', $settings['onedrive_refresh_token'] ?? '') }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="onedrive_folder_path" class="form-label">مسار المجلد (اختياري)</label>
                                    <input type="text" class="form-control" id="onedrive_folder_path" name="onedrive_folder_path" value="{{ old('onedrive_folder_path', $settings['onedrive_folder_path'] ?? 'Backups') }}" placeholder="Backups">
                                </div>
                            </div>

                            <div class="row mt-4">
                                <div class="col-md-6">
                                    <form method="POST" action="{{ route('admin.settings.backup') }}">
                                        @csrf
                                        <button type="submit" class="btn btn-primary w-100">
                                            <i class="fas fa-download me-2"></i>تنفيذ نسخة احتياطية الآن
                                        </button>
                                        <small class="text-muted d-block mt-2">سيتم إنشاء ملف نسخة وحفظه محلياً، ورفعه للسحابة إذا كانت مفعلة</small>
                                    </form>
                                </div>
                                <div class="col-md-6">
                                    <form method="POST" action="{{ route('admin.settings.test-cloud') }}">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-secondary w-100">
                                            <i class="fas fa-plug me-2"></i>اختبار الاتصال بالسحابة
                                        </button>
                                        <small class="text-muted d-block mt-2">يتحقق من صحة الإعدادات ويجرب رفع ملف صغير</small>
                                    </form>
                                </div>
                            </div>

                            <div class="alert alert-info mt-4">
                                <i class="fas fa-info-circle me-2"></i>
                                لضمان نجاح النسخ الكامل، يجب توفر أداة <code>mysqldump</code> على الخادم.
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Save Button -->
                <div class="mt-4">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-save me-2"></i>حفظ جميع الإعدادات
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@push('scripts')
<script>
    (function() {
        function toggleCloudSections() {
            var provider = document.getElementById('backup_cloud_provider').value;
            var enabled = document.getElementById('backup_cloud_enabled').checked;
            document.getElementById('cloud-google').style.display = enabled && provider === 'google_drive' ? 'block' : 'none';
            document.getElementById('cloud-mega').style.display = enabled && provider === 'mega' ? 'block' : 'none';
            document.getElementById('cloud-onedrive').style.display = enabled && provider === 'onedrive' ? 'block' : 'none';
        }
        document.getElementById('backup_cloud_provider').addEventListener('change', toggleCloudSections);
        document.getElementById('backup_cloud_enabled').addEventListener('change', toggleCloudSections);
        toggleCloudSections();
    })();
</script>
@endpush
@endsection

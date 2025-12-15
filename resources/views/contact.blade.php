@extends('layouts.app')

@section('title', 'اتصل بنا - Your Events')

@section('content')
<!-- Page Header -->
<section class="hero-section hero-contact" style="padding: 40px 0; background: linear-gradient(135deg, var(--primary-color) 0%, #2d1a5e 50%, var(--purple-light) 100%) !important;">
    <div class="container">
        <div class="text-center">
            <h1 class="display-4 fw-bold mb-3">تواصل معنا</h1>
            <p class="lead">نحن في Your Events نؤمن إن كل فعالية مميّزة تبدأ بتواصل واضح</p>
        </div>
    </div>
</section>

<!-- Contact Section -->
<section class="py-4">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mb-5">
                <div class="card shadow-lg" data-aos="fade-right">
                    <div class="card-body p-5">
                        <h3 class="mb-4 text-primary">
                            <i class="fas fa-envelope me-2"></i>أرسل لنا رسالة
                        </h3>
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif
                        @if($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-circle me-2"></i>{{ $errors->first() }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif
                        <form method="POST" action="{{ route('contact.store') }}">
                            @csrf
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="name" class="form-label">الاسم الكامل *</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">البريد الإلكتروني *</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="phone" class="form-label">رقم الهاتف</label>
                                    <input type="tel" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone') }}">
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label class="form-label d-block">اختر الموضوع</label>
                                    <div class="d-flex flex-wrap gap-3" id="subject-group">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="subject" id="subjectBooking" value="booking" {{ old('subject') === 'booking' ? 'checked' : '' }} required>
                                            <label class="form-check-label" for="subjectBooking">استفسار عن الحجز</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="subject" id="subjectPackages" value="packages" {{ old('subject') === 'packages' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="subjectPackages">استفسار عن الباقات</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="subject" id="subjectServices" value="services" {{ old('subject') === 'services' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="subjectServices">استفسار عن الخدمات</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="subject" id="subjectComplaint" value="complaint" {{ old('subject') === 'complaint' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="subjectComplaint">شكوى</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="subject" id="subjectOther" value="other" {{ old('subject') === 'other' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="subjectOther">أخرى</label>
                                        </div>
                                    </div>
                                    @error('subject')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-12 mb-3">
                                    <label for="message" class="form-label">الرسالة *</label>
                                    <textarea class="form-control @error('message') is-invalid @enderror" id="message" name="message" rows="5" placeholder="اكتب رسالتك هنا..." required>{{ old('message') }}</textarea>
                                    @error('message')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="fas fa-paper-plane me-2"></i>إرسال الرسالة
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- Contact Information -->
                <div class="card shadow mb-4" data-aos="fade-left">
                    <div class="card-body p-4">
                        <h5 class="card-title mb-4 text-primary">
                            <i class="fas fa-info-circle me-2"></i>معلومات الاتصال
                        </h5>
                        <ul class="list-unstyled">
                            <li class="mb-3">
                                <i class="fas fa-map-marker-alt text-primary me-3"></i>
                                <div>
                                    <strong>العنوان:</strong><br>
                                    {{ \App\Models\Setting::get('contact_address', 'الرياض، المملكة العربية السعودية') }}
                                </div>
                            </li>
                            <li class="mb-3">
                                <i class="fas fa-phone text-primary me-3"></i>
                                <div>
                                    <strong>الهاتف:</strong><br>
                                    <a href="tel:{{ preg_replace('/\s+/', '', \App\Models\Setting::get('contact_phone', '+966 50 123 4567')) }}" class="phone-ltr" dir="ltr">
                                        <span>{{ \App\Models\Setting::get('contact_phone', '+966 50 123 4567') }}</span>
                                    </a>
                                </div>
                            </li>
                            <li class="mb-3">
                                <i class="fas fa-envelope text-primary me-3"></i>
                                <div>
                                    <strong>البريد الإلكتروني:</strong><br>
                                    <a href="mailto:{{ \App\Models\Setting::get('contact_email', 'hello@yourevents.sa') }}">{{ \App\Models\Setting::get('contact_email', 'hello@yourevents.sa') }}</a>
                                </div>
                            </li>
                            <li>
                                <i class="fas fa-clock text-primary me-3"></i>
                                <div>
                                    <strong>ساعات العمل:</strong><br>
                                    {{ \App\Models\Setting::get('working_hours', 'السبت - الخميس: 9:00 ص - 6:00 م') }}
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Social Media -->
                <div class="card shadow" data-aos="fade-left" data-aos-delay="100">
                    <div class="card-body p-4">
                        <h5 class="card-title mb-4 text-primary">
                            <i class="fas fa-share-alt me-2"></i>تابعنا على
                        </h5>
                        <div class="social-icons">
                            @php
                                $facebookUrl = \App\Models\Setting::get('facebook_url');
                                $twitterUrl = \App\Models\Setting::get('twitter_url');
                                $instagramUrl = \App\Models\Setting::get('instagram_url');
                                $linkedinUrl = \App\Models\Setting::get('linkedin_url');
                                $snapchatUrl = \App\Models\Setting::get('snapchat_url');
                                $tiktokUrl = \App\Models\Setting::get('tiktok_url');
                            @endphp
                            
                            @if($facebookUrl)
                                <a href="{{ $facebookUrl }}" target="_blank" rel="noopener noreferrer" class="social-icon facebook" title="Facebook">
                                    <i class="fab fa-facebook-f"></i>
                                </a>
                            @endif
                            
                            @if($instagramUrl)
                                <a href="{{ $instagramUrl }}" target="_blank" rel="noopener noreferrer" class="social-icon instagram" title="Instagram">
                                    <i class="fab fa-instagram"></i>
                                </a>
                            @endif
                            
                            @if($twitterUrl)
                                <a href="{{ $twitterUrl }}" target="_blank" rel="noopener noreferrer" class="social-icon twitter" title="X (Twitter)">
                                    <i class="fab fa-x-twitter"></i>
                                </a>
                            @endif
                            
                            @if($snapchatUrl)
                                <a href="{{ $snapchatUrl }}" target="_blank" rel="noopener noreferrer" class="social-icon snapchat" title="Snapchat">
                                    <i class="fab fa-snapchat-ghost"></i>
                                </a>
                            @endif
                            
                            @if($linkedinUrl)
                                <a href="{{ $linkedinUrl }}" target="_blank" rel="noopener noreferrer" class="social-icon linkedin" title="LinkedIn">
                                    <i class="fab fa-linkedin"></i>
                                </a>
                            @endif
                            
                            @if($tiktokUrl)
                                <a href="{{ $tiktokUrl }}" target="_blank" rel="noopener noreferrer" class="social-icon tiktok" title="TikTok">
                                    <i class="fab fa-tiktok"></i>
                                </a>
                            @endif
                        </div>
                        
                        <hr class="my-4">
                        
                        <div class="text-center">
                            <a href="https://wa.me/{{ preg_replace('/\D+/', '', \App\Models\Setting::get('contact_phone', '+966501234567')) }}" target="_blank" class="btn btn-success w-100">
                                <i class="fab fa-whatsapp me-2"></i>تواصل عبر الواتساب
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Map Section -->
<section class="py-5 bg-secondary-custom">
    <div class="container">
        <h2 class="section-title mb-5" data-aos="fade-up" style="background: none; -webkit-text-fill-color: #000000; color: #000000;">موقعنا</h2>
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card shadow" data-aos="fade-up" data-aos-delay="100">
                    <div class="card-body p-0">
                        <iframe 
                            src="https://www.google.com/maps?q=24.804660797879635,46.62951321534364&hl=ar&z=15&output=embed" 
                            style="width: 100%; height: 400px; border: 0; border-radius: 0.375rem;" 
                            allowfullscreen 
                            loading="lazy" 
                            referrerpolicy="no-referrer-when-downgrade">
                        </iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FAQ Section -->
<section class="py-5">
    <div class="container">
        <h2 class="section-title mb-5" data-aos="fade-up" style="background: none; -webkit-text-fill-color: #000000; color: #000000;">الأسئلة الشائعة</h2>
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="accordion" id="faqAccordion" data-aos="fade-up" data-aos-delay="100">
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                كم يستغرق التحضير للمناسبة؟
                            </button>
                        </h2>
                        <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                يعتمد وقت التحضير على حجم المناسبة وتعقيدها. عادة ما نحتاج إلى أسبوعين على الأقل للمناسبات الصغيرة، وشهر أو أكثر للمناسبات الكبيرة. ننصح بالحجز مبكراً لضمان توفر جميع الخدمات المطلوبة.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                هل يمكنني تخصيص الباقة حسب احتياجاتي؟
                            </button>
                        </h2>
                        <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                بالطبع! نوفر باقات قابلة للتخصيص وفقاً لاحتياجاتك الخاصة وميزانيتك. يمكنك إضافة أو إزالة خدمات من أي باقة، أو إنشاء باقة مخصصة بالكامل.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                ما هي طرق الدفع المتاحة؟
                            </button>
                        </h2>
                        <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                نقبل جميع طرق الدفع: النقد، التحويل البنكي، الفيزا، الماستركارد، مدى، وكذلك الدفع الإلكتروني عبر التطبيقات البنكية. يمكن تقسيط المبلغ على دفعات حسب الاتفاق.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                                هل تقدمون خدمات خارج الرياض؟
                            </button>
                        </h2>
                        <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                نعم، نقدم خدماتنا في جميع أنحاء المملكة العربية السعودية. قد تطبق رسوم إضافية للمناسبات خارج الرياض حسب المسافة والخدمات المطلوبة.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('styles')
<style>
    .text-primary {
        color: #1f144a !important;
    }
    .social-icons {
        display: flex;
        gap: 15px;
        flex-wrap: wrap;
    }
    
    .social-icon {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        transition: all 0.3s ease;
        text-decoration: none;
        color: white;
    }
    
    .social-icon:hover {
        transform: translateY(-5px) scale(1.1);
        box-shadow: 0 10px 20px rgba(0,0,0,0.2);
    }
    
    .social-icon.facebook {
        background: #1877F2;
    }
    
    .social-icon.facebook:hover {
        background: #0d5dbf;
    }
    
    .social-icon.instagram {
        background: linear-gradient(45deg, #f09433 0%,#e6683c 25%,#dc2743 50%,#cc2366 75%,#bc1888 100%);
    }
    
    .social-icon.instagram:hover {
        background: linear-gradient(45deg, #d07823 0%,#c6582c 25%,#bc1733 50%,#ac1356 75%,#9c0878 100%);
    }
    
    .social-icon.twitter {
        background: #000000ff;
    }
    
    .social-icon.twitter:hover {
        background: #1a1a1a;
    }
    
    .social-icon.snapchat {
        background: #FFFC00;
        color: #000000;
    }
    
    .social-icon.snapchat:hover {
        background: #e6e300;
    }
    
    .social-icon.linkedin {
        background: #0A66C2;
    }
    
    .social-icon.linkedin:hover {
        background: #084d92;
    }
    
    .social-icon.tiktok {
        background: #000000;
    }
    
    .social-icon.tiktok:hover {
        background: #ff0050;
    }
</style>
@endpush

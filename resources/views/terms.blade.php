@extends('layouts.app')

@section('title', 'الشروط والأحكام - ' . (setting('site_name') ?? 'Your Events'))

@section('content')
<div class="container py-5" style="margin-top: 80px;">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow-lg border-0" style="border-radius: 25px; overflow: hidden;">
                <!-- Header -->
                <div class="card-header text-white text-center py-4" style="background: linear-gradient(135deg, #1f144a 0%, #2d1a5e 50%, #7269b0 100%);">
                    <h1 class="mb-0" style="font-size: 2.5rem; font-weight: 700;">
                        <i class="fas fa-file-contract me-3"></i>الشروط والأحكام
                    </h1>
                    <p class="mb-0 mt-2" style="opacity: 0.9; font-size: 1.1rem;">
                        يرجى قراءة الشروط والأحكام بعناية قبل استخدام خدماتنا
                    </p>
                </div>

                <!-- Content -->
                <div class="card-body p-5">
                    @php
                        $termsContent = setting('terms_and_conditions');
                    @endphp

                    @if($termsContent)
                        <div class="terms-content" style="line-height: 2; font-size: 1.1rem; color: #333;">
                            {!! nl2br(e($termsContent)) !!}
                        </div>
                    @else
                        <div class="alert alert-info d-flex align-items-center" role="alert">
                            <i class="fas fa-info-circle fa-2x me-3"></i>
                            <div>
                                <h5 class="alert-heading mb-1">لم يتم إضافة الشروط والأحكام بعد</h5>
                                <p class="mb-0">سيتم إضافة الشروط والأحكام قريباً. يرجى مراجعة الصفحة لاحقاً.</p>
                            </div>
                        </div>
                        
                        <div class="text-center mt-4">
                            <a href="{{ route('home') }}" class="btn btn-primary btn-lg" style="border-radius: 25px; padding: 12px 40px;">
                                <i class="fas fa-home me-2"></i>العودة للرئيسية
                            </a>
                        </div>
                    @endif

                    @if($termsContent)
                        <!-- Last Update Date -->
                        <div class="mt-5 pt-4 border-top">
                            <p class="text-muted mb-0" style="font-size: 0.95rem;">
                                <i class="fas fa-calendar-alt me-2"></i>
                                <strong>آخر تحديث:</strong> {{ date('d/m/Y') }}
                            </p>
                        </div>

                        <!-- Contact Info -->
                        <div class="alert alert-light mt-4" style="border-radius: 15px; border-left: 5px solid #2dbcae;">
                            <h5 class="mb-3">
                                <i class="fas fa-question-circle me-2" style="color: #2dbcae;"></i>
                                لديك استفسار حول الشروط والأحكام؟
                            </h5>
                            <p class="mb-2">لا تتردد في التواصل معنا:</p>
                            <ul class="list-unstyled mb-0">
                                @if(setting('contact_email'))
                                <li class="mb-2">
                                    <i class="fas fa-envelope me-2" style="color: #ef4870;"></i>
                                    <a href="mailto:{{ setting('contact_email') }}" class="text-decoration-none">
                                        {{ setting('contact_email') }}
                                    </a>
                                </li>
                                @endif
                                @if(setting('contact_phone'))
                                <li class="mb-2">
                                    <i class="fas fa-phone me-2" style="color: #f0c71d;"></i>
                                    <a href="tel:{{ setting('contact_phone') }}" class="text-decoration-none">
                                        {{ setting('contact_phone') }}
                                    </a>
                                </li>
                                @endif
                                @if(setting('whatsapp_number'))
                                <li>
                                    <i class="fab fa-whatsapp me-2" style="color: #25D366;"></i>
                                    <a href="https://wa.me/{{ setting('whatsapp_number') }}" target="_blank" class="text-decoration-none">
                                        واتساب: {{ setting('whatsapp_number') }}
                                    </a>
                                </li>
                                @endif
                            </ul>
                        </div>

                        <!-- Back to Home Button -->
                        <div class="text-center mt-4">
                            <a href="{{ route('home') }}" class="btn btn-outline-primary btn-lg" style="border-radius: 25px; padding: 12px 40px; border-width: 2px;">
                                <i class="fas fa-arrow-right me-2"></i>العودة للرئيسية
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .terms-content {
        text-align: justify;
    }
    
    .terms-content p {
        margin-bottom: 1.5rem;
    }
    
    .card {
        animation: fadeInUp 0.6s ease-out;
    }
    
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .btn-primary:hover,
    .btn-outline-primary:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 25px rgba(31, 20, 74, 0.3);
        transition: all 0.3s ease;
    }
    
    .alert-light {
        background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
    }
</style>
@endsection

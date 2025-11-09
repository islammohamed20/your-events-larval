@extends('layouts.app')

@section('title', 'تواصل معنا - Your Events')

@section('content')
<!-- Page Header -->
<section class="hero-section" style="padding: 40px 0;">
    <div class="container">
        <div class="text-center">
            <h1 class="display-4 fw-bold mb-3" style="color: var(--primary-color);">تواصل معنا</h1>
            <p class="lead" style="color: var(--text-color);">نحن هنا للإجابة على جميع استفساراتك ومساعدتك في تنظيم مناسبتك المثالية</p>
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
                        <form>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="name" class="form-label">الاسم الكامل *</label>
                                    <input type="text" class="form-control" id="name" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">البريد الإلكتروني *</label>
                                    <input type="email" class="form-control" id="email" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="phone" class="form-label">رقم الهاتف</label>
                                    <input type="tel" class="form-control" id="phone">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="subject" class="form-label">الموضوع</label>
                                    <select class="form-select" id="subject">
                                        <option value="">اختر الموضوع</option>
                                        <option value="booking">استفسار عن الحجز</option>
                                        <option value="packages">استفسار عن الباقات</option>
                                        <option value="services">استفسار عن الخدمات</option>
                                        <option value="complaint">شكوى</option>
                                        <option value="other">أخرى</option>
                                    </select>
                                </div>
                                <div class="col-12 mb-3">
                                    <label for="message" class="form-label">الرسالة *</label>
                                    <textarea class="form-control" id="message" rows="5" 
                                              placeholder="اكتب رسالتك هنا..." required></textarea>
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
                                    الرياض، المملكة العربية السعودية<br>
                                    حي الملك فهد، شارع التخصصي
                                </div>
                            </li>
                            <li class="mb-3">
                                <i class="fas fa-phone text-primary me-3"></i>
                                <div>
                                    <strong>الهاتف:</strong><br>
                                    <a href="tel:+966501234567">+966 50 123 4567</a>
                                </div>
                            </li>
                            <li class="mb-3">
                                <i class="fas fa-envelope text-primary me-3"></i>
                                <div>
                                    <strong>البريد الإلكتروني:</strong><br>
                                    <a href="mailto:info@yourevents.com">info@yourevents.com</a>
                                </div>
                            </li>
                            <li>
                                <i class="fas fa-clock text-primary me-3"></i>
                                <div>
                                    <strong>ساعات العمل:</strong><br>
                                    السبت - الخميس: 9:00 ص - 6:00 م<br>
                                    الجمعة: مغلق
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
                            <a href="#" class="me-2">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                            <a href="#" class="me-2">
                                <i class="fab fa-instagram"></i>
                            </a>
                            <a href="#" class="me-2">
                                <i class="fab fa-twitter"></i>
                            </a>
                            <a href="#" class="me-2">
                                <i class="fab fa-snapchat-ghost"></i>
                            </a>
                            <a href="#" class="me-2">
                                <i class="fab fa-linkedin"></i>
                            </a>
                        </div>
                        
                        <hr class="my-4">
                        
                        <div class="text-center">
                            <a href="https://wa.me/966501234567" target="_blank" class="btn btn-success w-100">
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
        <h2 class="section-title mb-5" data-aos="fade-up">موقعنا</h2>
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card shadow" data-aos="fade-up" data-aos-delay="100">
                    <div class="card-body p-0">
                        <div style="height: 400px; background: #f8f9fa; border-radius: 0.375rem;" class="d-flex align-items-center justify-content-center">
                            <div class="text-center">
                                <i class="fas fa-map-marked-alt fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">خريطة الموقع</h5>
                                <p class="text-muted">الرياض، المملكة العربية السعودية</p>
                                <p class="small text-muted">
                                    ملاحظة: في التطبيق الفعلي، سيتم إدراج خريطة Google Maps أو خريطة تفاعلية أخرى هنا
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FAQ Section -->
<section class="py-5">
    <div class="container">
        <h2 class="section-title mb-5" data-aos="fade-up">الأسئلة الشائعة</h2>
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

@extends('layouts.admin')

@section('title', 'اختبار البريد الإلكتروني')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-envelope-open-text me-2"></i>
            اختبار البريد الإلكتروني
        </h1>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <!-- Email Configuration -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-cog me-2"></i>
                        إعدادات البريد الحالية
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="text-muted small">خادم SMTP</label>
                        <p class="mb-0 fw-bold">{{ config('mail.mailers.smtp.host') }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted small">المنفذ</label>
                        <p class="mb-0 fw-bold">{{ config('mail.mailers.smtp.port') }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted small">التشفير</label>
                        <p class="mb-0 fw-bold">{{ config('mail.mailers.smtp.encryption') ?? 'بدون تشفير' }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted small">اسم المستخدم</label>
                        <p class="mb-0 fw-bold">{{ config('mail.mailers.smtp.username') ?? 'غير محدد' }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted small">البريد الافتراضي للإرسال</label>
                        <p class="mb-0 fw-bold">{{ config('mail.from.address') }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted small">الاسم الافتراضي للإرسال</label>
                        <p class="mb-0 fw-bold">{{ config('mail.from.name') }}</p>
                    </div>

                    <hr>

                    <div class="alert alert-info mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        <small>
                            لتحديث هذه الإعدادات، قم بتعديل ملف <code>.env</code>
                        </small>
                    </div>
                </div>
            </div>

            <!-- Setup Guide -->
            <div class="card shadow mt-4">
                <div class="card-header bg-warning text-dark">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-book me-2"></i>
                        دليل الإعداد
                    </h6>
                </div>
                <div class="card-body">
                    <h6 class="fw-bold mb-3">
                        <i class="fas fa-shield-alt me-2 text-primary"></i>
                        لاستخدام Outlook SMTP:
                    </h6>
                    
                    <ol class="small ps-3">
                        <li class="mb-2">
                            فعّل المصادقة الثنائية (2FA) على حساب 
                            <code>sales@yourevents.sa</code>
                        </li>
                        <li class="mb-2">
                            أنشئ <strong>App Password</strong> من:
                            <br>
                            <a href="https://account.microsoft.com/security" target="_blank" class="text-decoration-none">
                                account.microsoft.com/security
                                <i class="fas fa-external-link-alt ms-1"></i>
                            </a>
                        </li>
                        <li class="mb-2">
                            استخدم App Password (16 حرف) بدلاً من كلمة المرور العادية في ملف <code>.env</code>
                        </li>
                        <li class="mb-2">
                            نفذ: <code>php artisan config:clear</code>
                        </li>
                    </ol>

                    <div class="alert alert-danger mb-0 mt-3">
                        <small>
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>مهم:</strong> لا تستخدم كلمة المرور العادية، استخدم App Password فقط!
                        </small>
                    </div>

                    <div class="mt-3">
                        <a href="/EMAIL-SETUP-GUIDE.md" target="_blank" class="btn btn-sm btn-outline-primary w-100">
                            <i class="fas fa-file-alt me-2"></i>
                            دليل الإعداد الكامل
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Send Test Email -->
        <div class="col-lg-8 mb-4">
            <div class="card shadow">
                <div class="card-header bg-success text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-paper-plane me-2"></i>
                        إرسال بريد تجريبي
                    </h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.email-test.send') }}" method="POST">
                        @csrf

                        <div class="mb-4">
                            <label for="to_email" class="form-label">
                                <i class="fas fa-at me-1"></i>
                                البريد الإلكتروني المستقبل
                            </label>
                            <input type="email" 
                                   class="form-control form-control-lg @error('to_email') is-invalid @enderror" 
                                   id="to_email" 
                                   name="to_email" 
                                   value="{{ old('to_email') }}"
                                   placeholder="example@domain.com"
                                   required>
                            @error('to_email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="subject" class="form-label">
                                <i class="fas fa-heading me-1"></i>
                                الموضوع
                            </label>
                            <input type="text" 
                                   class="form-control @error('subject') is-invalid @enderror" 
                                   id="subject" 
                                   name="subject" 
                                   value="{{ old('subject', 'بريد تجريبي من Your Events') }}"
                                   required>
                            @error('subject')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="message" class="form-label">
                                <i class="fas fa-comment-alt me-1"></i>
                                محتوى الرسالة
                            </label>
                            <textarea class="form-control @error('message') is-invalid @enderror" 
                                      id="message" 
                                      name="message" 
                                      rows="8"
                                      required>{{ old('message', "مرحباً،\n\nهذا بريد تجريبي من موقع Your Events.\n\nإذا وصلك هذا البريد، فهذا يعني أن إعدادات البريد الإلكتروني تعمل بشكل صحيح! ✅\n\nمع تحيات،\nفريق Your Events") }}</textarea>
                            @error('message')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="fas fa-paper-plane me-2"></i>
                                إرسال البريد التجريبي
                            </button>
                        </div>
                    </form>

                    <hr class="my-4">

                    <!-- Quick Test Templates -->
                    <h6 class="mb-3 fw-bold">
                        <i class="fas fa-flask me-2 text-info"></i>
                        قوالب الاختبار السريع
                    </h6>

                    <div class="row g-2">
                        <div class="col-md-4">
                            <button class="btn btn-outline-secondary btn-sm w-100" onclick="fillTemplate('booking')">
                                <i class="fas fa-calendar-check me-1"></i>
                                تأكيد حجز
                            </button>
                        </div>
                        <div class="col-md-4">
                            <button class="btn btn-outline-secondary btn-sm w-100" onclick="fillTemplate('welcome')">
                                <i class="fas fa-user-plus me-1"></i>
                                ترحيب
                            </button>
                        </div>
                        <div class="col-md-4">
                            <button class="btn btn-outline-secondary btn-sm w-100" onclick="fillTemplate('reset')">
                                <i class="fas fa-key me-1"></i>
                                إعادة تعيين كلمة المرور
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Common Errors -->
            <div class="card shadow mt-4">
                <div class="card-header bg-danger text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        الأخطاء الشائعة وحلولها
                    </h6>
                </div>
                <div class="card-body">
                    <div class="accordion" id="errorsAccordion">
                        <!-- Error 1 -->
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#error1">
                                    <i class="fas fa-times-circle text-danger me-2"></i>
                                    Authentication failed / Username and Password not accepted
                                </button>
                            </h2>
                            <div id="error1" class="accordion-collapse collapse" data-bs-parent="#errorsAccordion">
                                <div class="accordion-body">
                                    <p><strong>السبب:</strong> استخدام كلمة المرور العادية بدلاً من App Password</p>
                                    <p><strong>الحل:</strong></p>
                                    <ol class="small mb-0">
                                        <li>أنشئ App Password من account.microsoft.com/security</li>
                                        <li>استخدمه في MAIL_PASSWORD في ملف .env</li>
                                        <li>نفذ: <code>php artisan config:clear</code></li>
                                    </ol>
                                </div>
                            </div>
                        </div>

                        <!-- Error 2 -->
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#error2">
                                    <i class="fas fa-clock text-warning me-2"></i>
                                    Connection timeout / Could not open socket
                                </button>
                            </h2>
                            <div id="error2" class="accordion-collapse collapse" data-bs-parent="#errorsAccordion">
                                <div class="accordion-body">
                                    <p><strong>السبب:</strong> المنفذ 587 محجوب أو مشكلة في الفايروول</p>
                                    <p><strong>الحل:</strong></p>
                                    <ol class="small mb-0">
                                        <li>تأكد من فتح المنفذ 587 على السيرفر</li>
                                        <li>جرب المنفذ 25 أو 465 بدلاً من 587</li>
                                        <li>تأكد من تعطيل الفايروول مؤقتاً للاختبار</li>
                                    </ol>
                                </div>
                            </div>
                        </div>

                        <!-- Error 3 -->
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#error3">
                                    <i class="fas fa-lock text-info me-2"></i>
                                    Approve sign in request
                                </button>
                            </h2>
                            <div id="error3" class="accordion-collapse collapse" data-bs-parent="#errorsAccordion">
                                <div class="accordion-body">
                                    <p><strong>السبب:</strong> محاولة تسجيل الدخول بكلمة المرور العادية</p>
                                    <p><strong>الحل:</strong></p>
                                    <p class="mb-0 small">
                                        هذه هي المشكلة الأساسية! يجب استخدام App Password (16 حرف) 
                                        بدلاً من كلمة المرور العادية. راجع قسم "دليل الإعداد" على اليسار.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function fillTemplate(type) {
        const subjectInput = document.getElementById('subject');
        const messageInput = document.getElementById('message');

        const templates = {
            booking: {
                subject: 'تأكيد حجز من Your Events',
                message: `عزيزي العميل،

تم تأكيد حجزك بنجاح! 🎉

تفاصيل الحجز:
- رقم الحجز: #12345
- الخدمة: تصوير حفل زفاف
- التاريخ: 2025/11/15
- الوقت: 6:00 مساءً

شكراً لاختيارك Your Events!

فريق Your Events
sales@yourevents.sa`
            },
            welcome: {
                subject: 'مرحباً بك في Your Events! 🎊',
                message: `عزيزي العميل،

مرحباً بك في عائلة Your Events! 

نحن سعداء بانضمامك إلينا. يمكنك الآن:
✅ تصفح خدماتنا المميزة
✅ حجز الفعاليات والمناسبات
✅ الاستفادة من العروض الخاصة

إذا كان لديك أي استفسار، لا تتردد في التواصل معنا.

مع أطيب التحيات،
فريق Your Events`
            },
            reset: {
                subject: 'إعادة تعيين كلمة المرور - Your Events',
                message: `مرحباً،

تلقينا طلباً لإعادة تعيين كلمة المرور الخاصة بحسابك.

للمتابعة، يرجى النقر على الرابط التالي:
https://yourevents.sa/reset-password?token=XXXXXX

هذا الرابط صالح لمدة 60 دقيقة فقط.

إذا لم تطلب إعادة تعيين كلمة المرور، يرجى تجاهل هذا البريد.

تحياتنا،
فريق Your Events`
            }
        };

        if (templates[type]) {
            subjectInput.value = templates[type].subject;
            messageInput.value = templates[type].message;
        }
    }
</script>
@endsection

@extends('layouts.app')

@section('title', 'سياسة الخصوصية – Your Events')

@section('content')
<div class="container py-5" style="margin-top: 80px;">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow-lg border-0" style="border-radius: 25px; overflow: hidden;">
                <!-- Header -->
                <div class="card-header text-white text-center py-4" style="background: linear-gradient(135deg, #1f144a 0%, #2d1a5e 50%, #7269b0 100%);">
                    <h1 class="mb-0" style="font-size: 2.5rem; font-weight: 700;">
                        <i class="fas fa-user-shield me-3"></i>سياسة الخصوصية
                    </h1>
                    <p class="mb-0 mt-2" style="opacity: 0.9; font-size: 1.1rem;">
                        نوضح كيفية جمعنا واستخدامنا وحماية بياناتك
                    </p>
                </div>

                <!-- Content -->
                <div class="card-body p-5">
                    @php
                        $privacyContent = setting('privacy_policy');
                        $privacyPolicyHtml = $privacyContent
                            ? '<div class="privacy-content" style="line-height: 2; font-size: 1.1rem; color: #333;">' . nl2br(e($privacyContent)) . '</div>'
                            : '
                                <div class="privacy-content" style="line-height: 2; font-size: 1.1rem; color: #333;">
                                    <h2 class="mb-4" style="font-weight: 800; color: #1f144a;">سياسة الخصوصية – Your Events</h2>
                                    <p>تلتزم منصة Your Events (“نحن” أو “المنصة”) بحماية خصوصية مستخدميها، وتوضح هذه السياسة الأسس التي يتم بموجبها جمع البيانات الشخصية واستخدامها وتخزينها ومشاركتها، وذلك وفقًا لنظام حماية البيانات الشخصية المعمول به في المملكة العربية السعودية.</p>
                                    <p>باستخدامك للمنصة، فإنك تقر بموافقتك الصريحة والنهائية على جميع ما ورد في هذه السياسة دون قيد أو شرط.</p>
                                    <hr class="my-4">

                                    <h3 class="mt-4" style="font-weight: 800; color: #1f144a;">أولًا: نطاق تطبيق سياسة الخصوصية</h3>
                                    <p>تنطبق هذه السياسة على جميع المستخدمين والزوار والعملاء الذين يقومون باستخدام منصة Your Events أو أي من خدماتها أو نماذجها أو قنوات التواصل التابعة لها.</p>

                                    <hr class="my-4">
                                    <h3 class="mt-4" style="font-weight: 800; color: #1f144a;">ثانيًا: البيانات التي نقوم بجمعها</h3>
                                    <p>قد نقوم بجمع ومعالجة البيانات التالية، دون حصر:</p>
                                    <ol class="mb-0">
                                        <li class="mb-3">
                                            <strong>البيانات الشخصية:</strong>
                                            <ul class="mt-2">
                                                <li>الاسم</li>
                                                <li>رقم الجوال</li>
                                                <li>البريد الإلكتروني</li>
                                                <li>اسم الجهة أو الشركة</li>
                                                <li>الصفة الوظيفية (إن وُجدت)</li>
                                            </ul>
                                        </li>
                                        <li class="mb-3">
                                            <strong>بيانات الطلبات والمعاملات:</strong>
                                            <ul class="mt-2">
                                                <li>تفاصيل طلبات الخدمات أو عروض الأسعار</li>
                                                <li>معلومات التواصل المتعلقة بالطلب</li>
                                                <li>أي ملاحظات أو متطلبات يضيفها المستخدم</li>
                                            </ul>
                                        </li>
                                        <li class="mb-3">
                                            <strong>البيانات التقنية:</strong>
                                            <ul class="mt-2">
                                                <li>عنوان IP</li>
                                                <li>نوع المتصفح والجهاز</li>
                                                <li>بيانات الاستخدام داخل المنصة</li>
                                                <li>ملفات تعريف الارتباط (Cookies)</li>
                                            </ul>
                                        </li>
                                        <li><strong>أي بيانات أخرى</strong> يختار المستخدم تقديمها طوعًا عبر المنصة.</li>
                                    </ol>

                                    <hr class="my-4">
                                    <h3 class="mt-4" style="font-weight: 800; color: #1f144a;">ثالثًا: الغرض من جمع واستخدام البيانات</h3>
                                    <p>يتم جمع واستخدام البيانات للأغراض التالية:</p>
                                    <ul>
                                        <li>تشغيل المنصة وتقديم خدمات تجهيز الفعاليات</li>
                                        <li>معالجة الطلبات والتواصل مع المستخدم</li>
                                        <li>مشاركة البيانات مع الموردين أو مزودي الخدمات عند الحاجة لتنفيذ الطلب</li>
                                        <li>تحسين جودة الخدمات وتجربة المستخدم</li>
                                        <li>الامتثال للمتطلبات النظامية والتنظيمية</li>
                                        <li>حماية حقوق المنصة قانونيًا وتشغيليًا</li>
                                    </ul>
                                    <p class="mb-0">ويحق لـ Your Events استخدام البيانات للأغراض التشغيلية والتجارية المشروعة دون الحاجة لموافقة إضافية.</p>

                                    <hr class="my-4">
                                    <h3 class="mt-4" style="font-weight: 800; color: #1f144a;">رابعًا: الطبيعة الوسيطة للمنصة</h3>
                                    <p>يقر المستخدم ويوافق على أن:</p>
                                    <ul>
                                        <li>Your Events تعمل كوسيط فقط بين العميل ومزودي خدمات تجهيز الفعاليات.</li>
                                        <li>المنصة لا تقدم التنفيذ المباشر للخدمات ولا تتحمل أي مسؤولية عن جودة أو تأخير أو إخفاق أي طرف ثالث.</li>
                                        <li>أي مشاركة للبيانات مع الموردين تكون لغرض تنفيذ الطلب فقط، وتنتقل المسؤولية إليهم بعد ذلك.</li>
                                    </ul>

                                    <hr class="my-4">
                                    <h3 class="mt-4" style="font-weight: 800; color: #1f144a;">خامسًا: مشاركة البيانات مع أطراف ثالثة</h3>
                                    <p>يحق لـ Your Events مشاركة البيانات في الحالات التالية:</p>
                                    <ul>
                                        <li>مع الموردين ومزودي الخدمات لتنفيذ الطلبات</li>
                                        <li>مع الجهات الحكومية أو القضائية عند الطلب النظامي</li>
                                        <li>لحماية حقوق المنصة أو الدفاع عنها قانونيًا</li>
                                        <li>عند الاشتباه في إساءة استخدام المنصة أو الاحتيال</li>
                                    </ul>
                                    <p class="mb-0">ولا تتحمل المنصة أي مسؤولية عن استخدام الطرف الثالث للبيانات بعد مشاركتها ضمن الإطار النظامي.</p>

                                    <hr class="my-4">
                                    <h3 class="mt-4" style="font-weight: 800; color: #1f144a;">سادسًا: حماية البيانات وإخلاء المسؤولية</h3>
                                    <p>تتخذ Your Events التدابير التقنية والتنظيمية المناسبة لحماية البيانات، إلا أن المستخدم يقر ويوافق على أن:</p>
                                    <ul>
                                        <li>نقل البيانات عبر الإنترنت لا يمكن ضمان أمانه بنسبة 100%</li>
                                        <li>المنصة غير مسؤولة عن أي اختراقات خارجة عن نطاق سيطرتها</li>
                                        <li>لا تتحمل المنصة أي مسؤولية عن أضرار مباشرة أو غير مباشرة ناتجة عن استخدام البيانات</li>
                                    </ul>

                                    <hr class="my-4">
                                    <h3 class="mt-4" style="font-weight: 800; color: #1f144a;">سابعًا: ملفات تعريف الارتباط (Cookies)</h3>
                                    <p class="mb-0">يوافق المستخدم على استخدام المنصة لملفات تعريف الارتباط لأغراض تشغيلية وتحليلية وتسويقية، ويعد استمرار استخدام المنصة موافقة صريحة على ذلك.</p>

                                    <hr class="my-4">
                                    <h3 class="mt-4" style="font-weight: 800; color: #1f144a;">ثامنًا: الاحتفاظ بالبيانات</h3>
                                    <p>تحتفظ Your Events بالبيانات:</p>
                                    <ul>
                                        <li>طوال مدة الحاجة التشغيلية أو النظامية</li>
                                        <li>أو حتى بعد إلغاء الحساب أو الطلب</li>
                                        <li>طالما كان ذلك ضروريًا لحماية حقوق المنصة أو الامتثال للأنظمة</li>
                                    </ul>

                                    <hr class="my-4">
                                    <h3 class="mt-4" style="font-weight: 800; color: #1f144a;">تاسعًا: حقوق المستخدم</h3>
                                    <p>يحق للمستخدم، وفق الأنظمة:</p>
                                    <ul>
                                        <li>طلب الاطلاع على بياناته</li>
                                        <li>طلب تعديلها أو تحديثها</li>
                                    </ul>
                                    <p class="mb-0">ولا يحق للمستخدم المطالبة بحذف البيانات إذا كان الاحتفاظ بها يخدم مصلحة نظامية أو تعاقدية للمنصة.</p>

                                    <hr class="my-4">
                                    <h3 class="mt-4" style="font-weight: 800; color: #1f144a;">عاشرًا: الروابط الخارجية</h3>
                                    <p class="mb-0">لا تتحمل Your Events أي مسؤولية عن سياسات الخصوصية أو المحتوى الخاص بالمواقع أو المنصات الخارجية المرتبطة بها.</p>

                                    <hr class="my-4">
                                    <h3 class="mt-4" style="font-weight: 800; color: #1f144a;">الحادي عشر: التعديلات</h3>
                                    <p class="mb-0">تحتفظ Your Events بحق تعديل سياسة الخصوصية في أي وقت دون إشعار مسبق، ويعد استمرار استخدام المنصة بعد التعديل موافقة ضمنية على السياسة المحدثة.</p>

                                    <hr class="my-4">
                                    <h3 class="mt-4" style="font-weight: 800; color: #1f144a;">الثاني عشر: القانون الواجب التطبيق</h3>
                                    <p class="mb-0">تخضع هذه السياسة وتُفسر وفق أنظمة وقوانين المملكة العربية السعودية، وتكون المحاكم المختصة في المملكة هي الجهة الوحيدة للنظر في أي نزاع.</p>

                                    <hr class="my-4">
                                    <h3 class="mt-4" style="font-weight: 800; color: #1f144a;">الثالث عشر: التواصل</h3>
                                    <p class="mb-0">للاستفسارات المتعلقة بسياسة الخصوصية:</p>
                                    <ul class="mb-0">
                                        <li>البريد الإلكتروني: [يُضاف لاحقًا]</li>
                                        <li>أو عبر نموذج التواصل في منصة Your Events</li>
                                    </ul>
                                </div>
                            ';
                    @endphp

                    {!! $privacyPolicyHtml !!}

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
                            لديك استفسار حول سياسة الخصوصية؟
                        </h5>
                        <p class="mb-2">لا تتردد في التواصل معنا:</p>
                        @php
                            $contactPhoneDigits = preg_replace('/\D+/', '', (string) setting('contact_phone'));
                            $contactPhoneDisplay = $contactPhoneDigits ? ('+' . $contactPhoneDigits) : null;
                            $whatsappDigits = preg_replace('/\D+/', '', (string) setting('whatsapp_number'));
                            $whatsappDisplay = $whatsappDigits ? ('+' . $whatsappDigits) : null;
                        @endphp
                        <ul class="list-unstyled mb-0">
                            @if(setting('contact_email'))
                            <li class="mb-2">
                                <i class="fas fa-envelope me-2" style="color: #ef4870;"></i>
                                <a href="mailto:{{ setting('contact_email') }}" class="text-decoration-none">
                                    {{ setting('contact_email') }}
                                </a>
                            </li>
                            @endif
                            @if($contactPhoneDisplay)
                            <li class="mb-2">
                                <i class="fas fa-phone me-2" style="color: #f0c71d;"></i>
                                <a href="tel:{{ $contactPhoneDisplay }}" class="text-decoration-none">
                                    <span dir="ltr" style="unicode-bidi: isolate;">{{ $contactPhoneDisplay }}</span>
                                </a>
                            </li>
                            @endif
                            @if($whatsappDigits)
                            <li>
                                <i class="fab fa-whatsapp me-2" style="color: #25D366;"></i>
                                <a href="https://wa.me/{{ $whatsappDigits }}" target="_blank" class="text-decoration-none">
                                    واتساب: <span dir="ltr" style="unicode-bidi: isolate;">{{ $whatsappDisplay }}</span>
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
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .privacy-content {
        text-align: justify;
    }
    .privacy-content p {
        margin-bottom: 1.5rem;
    }
    .card {
        animation: fadeInUp 0.6s ease-out;
    }
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .btn-primary:hover,
    .btn-outline-primary:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 25px rgba(31, 20, 74, 0.3);
        transition: all 0.3s ease;
    }
    .alert-light { background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%); }
</style>
@endsection

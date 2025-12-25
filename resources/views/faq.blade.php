@extends('layouts.app')

@section('title', 'الأسئلة الشائعة - Your Events')

@section('content')
<!-- Page Header -->
<section class="hero-section" style="padding: 60px 0; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
    <div class="container">
        <div class="text-center">
            <h1 class="display-4 fw-bold mb-3 text-white">
                <i class="fas fa-clipboard-question me-3"></i>أسئلة شائعة
            </h1>
            <p class="lead text-white-50">كل ما تحتاج معرفته عن Your Events</p>
        </div>
    </div>
</section>

<!-- FAQ Section -->
<section class="py-5" style="background: linear-gradient(180deg, #f8f9fa 0%, #ffffff 100%);">
    <div class="container">
        <!-- Search Box -->
        <div class="row justify-content-center mb-5">
            <div class="col-lg-8">
                <div class="search-box-faq position-relative" data-aos="fade-up">
                    <input type="text" id="faqSearch" class="form-control form-control-lg" 
                           placeholder="ابحث في الأسئلة الشائعة..." 
                           style="border-radius: 50px; padding: 20px 30px; padding-right: 60px; border: 2px solid #e0e0e0; box-shadow: 0 5px 20px rgba(0,0,0,0.08);">
                    <i class="fas fa-search position-absolute" style="right: 25px; top: 50%; transform: translateY(-50%); color: #667eea; font-size: 1.2rem;"></i>
                </div>
            </div>
        </div>

        <!-- FAQ Stats -->
        <div class="row justify-content-center mb-5" data-aos="fade-up" data-aos-delay="100">
            <div class="col-lg-8">
                <div class="d-flex justify-content-center gap-4 flex-wrap">
                    <div class="faq-stat text-center px-4 py-3" style="background: white; border-radius: 15px; box-shadow: 0 3px 15px rgba(0,0,0,0.08);">
                        <span class="d-block h3 text-primary mb-1">30</span>
                        <span class="text-muted small">سؤال وجواب</span>
                    </div>
                    <div class="faq-stat text-center px-4 py-3" style="background: white; border-radius: 15px; box-shadow: 0 3px 15px rgba(0,0,0,0.08);">
                        <span class="d-block h3 text-primary mb-1">24/7</span>
                        <span class="text-muted small">دعم متواصل</span>
                    </div>
                    <div class="faq-stat text-center px-4 py-3" style="background: white; border-radius: 15px; box-shadow: 0 3px 15px rgba(0,0,0,0.08);">
                        <span class="d-block h3 text-primary mb-1">الرياض</span>
                        <span class="text-muted small">منطقة الخدمة</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- FAQ Accordion -->
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="accordion faq-accordion" id="faqAccordion">
                    
                    <!-- Question 1 -->
                    <div class="accordion-item faq-item" data-aos="fade-up" data-aos-delay="50">
                        <h2 class="accordion-header">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                <span class="faq-number">1</span>
                                <span class="faq-question">وش هو Your Events؟</span>
                            </button>
                        </h2>
                        <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                منصة سعودية تجمع كل خدمات تجهيز الفعاليات في مكان واحد… بدون تشتت وأسعار مناسبة.
                            </div>
                        </div>
                    </div>

                    <!-- Question 2 -->
                    <div class="accordion-item faq-item" data-aos="fade-up" data-aos-delay="100">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                <span class="faq-number">2</span>
                                <span class="faq-question">تنظمون الفعاليات؟</span>
                            </button>
                        </h2>
                        <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                لا، إحنا نجهز كل شيء… التنظيم عليك.
                            </div>
                        </div>
                    </div>

                    <!-- Question 3 -->
                    <div class="accordion-item faq-item" data-aos="fade-up" data-aos-delay="150">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                <span class="faq-number">3</span>
                                <span class="faq-question">الخدمات متوفرة وين؟</span>
                            </button>
                        </h2>
                        <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                حالياً في الرياض فقط.
                            </div>
                        </div>
                    </div>

                    <!-- Question 4 -->
                    <div class="accordion-item faq-item" data-aos="fade-up">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                                <span class="faq-number">4</span>
                                <span class="faq-question">كيف أطلب خدمة من Your Events؟</span>
                            </button>
                        </h2>
                        <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                اختار الخدمة، اضغط "اطلب الآن"، واستلم عرض سعر فوري على ايميلك.
                            </div>
                        </div>
                    </div>

                    <!-- Question 5 -->
                    <div class="accordion-item faq-item" data-aos="fade-up">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq5">
                                <span class="faq-number">5</span>
                                <span class="faq-question">أقدر أشوف السعر قبل الدفع؟</span>
                            </button>
                        </h2>
                        <div id="faq5" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                أكيد، عندنا ميزة عرض السعر الفوري والتلقائي عشان تعرف التكلفة قبل ما تدفع.
                            </div>
                        </div>
                    </div>

                    <!-- Question 6 -->
                    <div class="accordion-item faq-item" data-aos="fade-up">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq6">
                                <span class="faq-number">6</span>
                                <span class="faq-question">كيف أدفع بعد الموافقة على السعر؟</span>
                            </button>
                        </h2>
                        <div id="faq6" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                تقدر تدفع مباشرة عبر المنصة بأمان بعد موافقتك على السعر.
                            </div>
                        </div>
                    </div>

                    <!-- Question 7 -->
                    <div class="accordion-item faq-item" data-aos="fade-up">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq7">
                                <span class="faq-number">7</span>
                                <span class="faq-question">هل الأسعار ثابتة أو فيها مفاجآت؟</span>
                            </button>
                        </h2>
                        <div id="faq7" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                لا، كل الأسعار واضحة، منخفضة، وبدون أي تشتت.
                            </div>
                        </div>
                    </div>

                    <!-- Question 8 -->
                    <div class="accordion-item faq-item" data-aos="fade-up">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq8">
                                <span class="faq-number">8</span>
                                <span class="faq-question">أقدر أعدل طلبي بعد الموافقة؟</span>
                            </button>
                        </h2>
                        <div id="faq8" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                نعم، قبل تأكيد الحجز النهائي وبحسب توفر الخدمة.
                            </div>
                        </div>
                    </div>

                    <!-- Question 9 -->
                    <div class="accordion-item faq-item" data-aos="fade-up">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq9">
                                <span class="faq-number">9</span>
                                <span class="faq-question">كيف يتم متابعة طلبي؟</span>
                            </button>
                        </h2>
                        <div id="faq9" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                فريقنا يتابع معك ومع المورد خطوة بخطوة لضمان تجربة سلسة.
                            </div>
                        </div>
                    </div>

                    <!-- Question 10 -->
                    <div class="accordion-item faq-item" data-aos="fade-up">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq10">
                                <span class="faq-number">10</span>
                                <span class="faq-question">هل تقدمون دعم لوجستي في الموقع؟</span>
                            </button>
                        </h2>
                        <div id="faq10" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                نوفر تجهيز البوثات والألعاب وبعض خدمات الدعم المحددة، لكن ما ننظم الفعالية كاملة.
                            </div>
                        </div>
                    </div>

                    <!-- Question 11 -->
                    <div class="accordion-item faq-item" data-aos="fade-up">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq11">
                                <span class="faq-number">11</span>
                                <span class="faq-question">هل أقدر أشارك رأيي بعد الفعالية؟</span>
                            </button>
                        </h2>
                        <div id="faq11" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                أكيد، بعد كل رحلة يتم تقييم تجربتك لضمان التحسين المستمر.
                            </div>
                        </div>
                    </div>

                    <!-- Question 12 -->
                    <div class="accordion-item faq-item" data-aos="fade-up">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq12">
                                <span class="faq-number">12</span>
                                <span class="faq-question">هل تقدمون خدمات تصوير الفعاليات؟</span>
                            </button>
                        </h2>
                        <div id="faq12" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                نعم، نوفر تصوير احترافي للفعاليات مع تجهيز الصور والفيديوهات بشكل مرتب وجاهز.
                            </div>
                        </div>
                    </div>

                    <!-- Question 13 -->
                    <div class="accordion-item faq-item" data-aos="fade-up">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq13">
                                <span class="faq-number">13</span>
                                <span class="faq-question">هل فيه خدمات ألعاب وترفيه للفعاليات؟</span>
                            </button>
                        </h2>
                        <div id="faq13" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                أكيد، نوفر ألعاب وفعاليات ترفيهية تناسب جميع الأعمار وعدد الأشخاص.
                            </div>
                        </div>
                    </div>

                    <!-- Question 14 -->
                    <div class="accordion-item faq-item" data-aos="fade-up">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq14">
                                <span class="faq-number">14</span>
                                <span class="faq-question">أقدر أحجز أكثر من خدمة بنفس الوقت؟</span>
                            </button>
                        </h2>
                        <div id="faq14" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                نعم، تقدر تجمع كل خدماتك في طلب واحد بدون أي تعقيد.
                            </div>
                        </div>
                    </div>

                    <!-- Question 15 -->
                    <div class="accordion-item faq-item" data-aos="fade-up">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq15">
                                <span class="faq-number">15</span>
                                <span class="faq-question">هل تتعاملون مع الشركات والجهات الحكومية؟</span>
                            </button>
                        </h2>
                        <div id="faq15" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                نعم، خدماتنا موجهة للشركات، الجهات الحكومية، المعارض، والمؤتمرات.
                            </div>
                        </div>
                    </div>

                    <!-- Question 16 -->
                    <div class="accordion-item faq-item" data-aos="fade-up">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq16">
                                <span class="faq-number">16</span>
                                <span class="faq-question">هل ممكن أطلب هدايا أو تذكارات للفعالية؟</span>
                            </button>
                        </h2>
                        <div id="faq16" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                أكيد، نوفر تشكيلة متنوعة من الهدايا والتذكارات حسب نوع الفعالية وعدد الأشخاص.
                            </div>
                        </div>
                    </div>

                    <!-- Question 17 -->
                    <div class="accordion-item faq-item" data-aos="fade-up">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq17">
                                <span class="faq-number">17</span>
                                <span class="faq-question">هل فيه باقات خاصة لعدد كبير من الحضور؟</span>
                            </button>
                        </h2>
                        <div id="faq17" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                نعم، عندنا باقات متنوعة حسب عدد الأشخاص: 20، 40، 50 شخص… وأكثر.
                            </div>
                        </div>
                    </div>

                    <!-- Question 18 -->
                    <div class="accordion-item faq-item" data-aos="fade-up">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq18">
                                <span class="faq-number">18</span>
                                <span class="faq-question">هل الخدمات جاهزة للتسليم السريع؟</span>
                            </button>
                        </h2>
                        <div id="faq18" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                نعم، بعض الخدمات متاحة للتجهيز السريع حسب الطلب.
                            </div>
                        </div>
                    </div>

                    <!-- Question 19 -->
                    <div class="accordion-item faq-item" data-aos="fade-up">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq19">
                                <span class="faq-number">19</span>
                                <span class="faq-question">هل يمكنني مشاهدة أمثلة سابقة للفعاليات؟</span>
                            </button>
                        </h2>
                        <div id="faq19" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                أكيد، عندنا معرض صور وفيديوهات لتجارب سابقة تساعدك تختار الأنسب.
                            </div>
                        </div>
                    </div>

                    <!-- Question 20 -->
                    <div class="accordion-item faq-item" data-aos="fade-up">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq20">
                                <span class="faq-number">20</span>
                                <span class="faq-question">هل يوجد خدمة عملاء متاحة دائماً؟</span>
                            </button>
                        </h2>
                        <div id="faq20" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                نعم، فريقنا جاهز للرد على أي استفسار عبر الموقع أو أرقام التواصل.
                            </div>
                        </div>
                    </div>

                    <!-- Question 21 -->
                    <div class="accordion-item faq-item" data-aos="fade-up">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq21">
                                <span class="faq-number">21</span>
                                <span class="faq-question">كيف تضمنون جودة الخدمات؟</span>
                            </button>
                        </h2>
                        <div id="faq21" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                نتعامل مع أفضل الموردين، وكل خدمة يتم تجهيزها بعناية لضمان تجربة ممتازة.
                            </div>
                        </div>
                    </div>

                    <!-- Question 22 -->
                    <div class="accordion-item faq-item" data-aos="fade-up">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq22">
                                <span class="faq-number">22</span>
                                <span class="faq-question">هل أقدر أختار الخدمة حسب الميزانية؟</span>
                            </button>
                        </h2>
                        <div id="faq22" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                أكيد، نوفر خيارات متعددة تناسب كل الميزانيات بدون أي تشتت.
                            </div>
                        </div>
                    </div>

                    <!-- Question 23 -->
                    <div class="accordion-item faq-item" data-aos="fade-up">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq23">
                                <span class="faq-number">23</span>
                                <span class="faq-question">هل هناك خصومات أو عروض خاصة؟</span>
                            </button>
                        </h2>
                        <div id="faq23" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                نعم، عندنا عروض موسمية وباقات مخفضة تناسب العملاء.
                            </div>
                        </div>
                    </div>

                    <!-- Question 24 -->
                    <div class="accordion-item faq-item" data-aos="fade-up">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq24">
                                <span class="faq-number">24</span>
                                <span class="faq-question">هل يمكنني متابعة الطلب بعد الدفع؟</span>
                            </button>
                        </h2>
                        <div id="faq24" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                نعم، فريقنا يضمن متابعة كل خطوة لضمان وصول الخدمة مثل ما طلبت.
                            </div>
                        </div>
                    </div>

                    <!-- Question 25 -->
                    <div class="accordion-item faq-item" data-aos="fade-up">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq25">
                                <span class="faq-number">25</span>
                                <span class="faq-question">هل توفرون خدمات تجهيز البوثات والخيام؟</span>
                            </button>
                        </h2>
                        <div id="faq25" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                نعم، تجهيز البوثات والخيام جزء من خدماتنا الأساسية للفعاليات.
                            </div>
                        </div>
                    </div>

                    <!-- Question 26 -->
                    <div class="accordion-item faq-item" data-aos="fade-up">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq26">
                                <span class="faq-number">26</span>
                                <span class="faq-question">هل يمكنني طلب خدمة خاصة بفعالية معينة؟</span>
                            </button>
                        </h2>
                        <div id="faq26" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                أكيد، نوفر خدمات مخصصة حسب نوع الفعالية وعدد الضيوف.
                            </div>
                        </div>
                    </div>

                    <!-- Question 27 -->
                    <div class="accordion-item faq-item" data-aos="fade-up">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq27">
                                <span class="faq-number">27</span>
                                <span class="faq-question">هل يوجد خيارات للألعاب التفاعلية للموظفين؟</span>
                            </button>
                        </h2>
                        <div id="faq27" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                نعم، نوفر باقات ترفيهية وألعاب تفاعلية مناسبة للشركات والموظفين.
                            </div>
                        </div>
                    </div>

                    <!-- Question 28 -->
                    <div class="accordion-item faq-item" data-aos="fade-up">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq28">
                                <span class="faq-number">28</span>
                                <span class="faq-question">هل يتم إرسال تأكيد الطلب عبر الإيميل؟</span>
                            </button>
                        </h2>
                        <div id="faq28" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                نعم، كل تفاصيل الطلب والتأكيدات يتم إرسالها مباشرة على إيميلك.
                            </div>
                        </div>
                    </div>

                    <!-- Question 29 -->
                    <div class="accordion-item faq-item" data-aos="fade-up">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq29">
                                <span class="faq-number">29</span>
                                <span class="faq-question">لماذا أختار Your Events بدل الموردين المباشرين؟</span>
                            </button>
                        </h2>
                        <div id="faq29" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                لأن كل شيء متجمع في مكان واحد، أسعار واضحة، متابعة من فريقنا، وتجربة سلسة بدون تشتت… كل هذا بضغطة زر واحدة.
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- Still Have Questions -->
        <div class="row justify-content-center mt-5">
            <div class="col-lg-8">
                <div class="text-center p-5" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 20px; box-shadow: 0 10px 40px rgba(102, 126, 234, 0.3);" data-aos="fade-up">
                    <h3 class="text-white mb-3">
                        <i class="fas fa-headset me-2"></i>لم تجد إجابة سؤالك؟
                    </h3>
                    <p class="text-white-50 mb-4">فريقنا جاهز للرد على جميع استفساراتك</p>
                    <a href="{{ route('contact') }}" class="btn btn-light btn-lg px-5" style="border-radius: 50px;">
                        <i class="fas fa-envelope me-2"></i>تواصل معنا
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('styles')
<style>
    /* FAQ Accordion Styles */
    .faq-accordion .accordion-item {
        border: none;
        margin-bottom: 15px;
        border-radius: 15px !important;
        overflow: hidden;
        box-shadow: 0 3px 15px rgba(0,0,0,0.08);
        transition: all 0.3s ease;
    }
    
    .faq-accordion .accordion-item:hover {
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.15);
        transform: translateY(-2px);
    }
    
    .faq-accordion .accordion-button {
        background: linear-gradient(to right, #ffffff, #f8f9fa);
        padding: 20px 25px;
        font-size: 1.1rem;
        font-weight: 600;
        color: #1e1349;
        border: none;
        gap: 15px;
    }
    
    .faq-accordion .accordion-button:not(.collapsed) {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        box-shadow: none;
    }
    
    .faq-accordion .accordion-button:focus {
        box-shadow: none;
        border-color: transparent;
    }
    
    .faq-accordion .accordion-button::after {
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%23667eea'%3e%3cpath fill-rule='evenodd' d='M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z'/%3e%3c/svg%3e");
        transition: transform 0.3s ease;
    }
    
    .faq-accordion .accordion-button:not(.collapsed)::after {
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%23ffffff'%3e%3cpath fill-rule='evenodd' d='M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z'/%3e%3c/svg%3e");
    }
    
    .faq-number {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 35px;
        height: 35px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 50%;
        font-size: 0.9rem;
        font-weight: bold;
        flex-shrink: 0;
    }
    
    .accordion-button:not(.collapsed) .faq-number {
        background: rgba(255,255,255,0.2);
    }
    
    .faq-question {
        flex: 1;
    }
    
    .faq-accordion .accordion-body {
        padding: 20px 25px;
        background: #ffffff;
        font-size: 1.05rem;
        line-height: 1.9;
        color: #555;
        border-top: 1px solid #f0f0f0;
    }
    
    /* Search Box */
    .search-box-faq input:focus {
        border-color: #667eea;
        box-shadow: 0 5px 25px rgba(102, 126, 234, 0.2);
    }
    
    /* No results message */
    .no-results {
        display: none;
        text-align: center;
        padding: 40px;
        color: #666;
    }
    
    .no-results.show {
        display: block;
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        .faq-accordion .accordion-button {
            padding: 15px 20px;
            font-size: 1rem;
        }
        
        .faq-number {
            width: 30px;
            height: 30px;
            font-size: 0.8rem;
        }
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('faqSearch');
    const faqItems = document.querySelectorAll('.faq-item');
    
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase().trim();
        let hasResults = false;
        
        faqItems.forEach(function(item) {
            const question = item.querySelector('.faq-question').textContent.toLowerCase();
            const answer = item.querySelector('.accordion-body').textContent.toLowerCase();
            
            if (question.includes(searchTerm) || answer.includes(searchTerm)) {
                item.style.display = 'block';
                hasResults = true;
            } else {
                item.style.display = 'none';
            }
        });
        
        // Show/hide no results message
        let noResults = document.querySelector('.no-results');
        if (!noResults) {
            noResults = document.createElement('div');
            noResults.className = 'no-results';
            noResults.innerHTML = '<i class="fas fa-search fa-3x text-muted mb-3"></i><h4>لم يتم العثور على نتائج</h4><p>جرب البحث بكلمات مختلفة</p>';
            document.querySelector('.faq-accordion').appendChild(noResults);
        }
        
        if (!hasResults && searchTerm !== '') {
            noResults.classList.add('show');
        } else {
            noResults.classList.remove('show');
        }
    });
});
</script>
@endpush

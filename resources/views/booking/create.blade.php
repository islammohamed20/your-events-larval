@extends('layouts.app')

@section('title', 'إنشاء حجز جديد')

@section('content')
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="text-center mb-5" data-aos="fade-up">
                    <h1 class="display-5 fw-bold mb-3">احجز مناسبتك الآن</h1>
                    <p class="lead text-muted">املأ النموذج أدناه وسنتواصل معك خلال 24 ساعة لتأكيد الحجز</p>
                </div>

                <div class="card shadow-lg" data-aos="fade-up" data-aos-delay="100">
                    <div class="card-body p-5">
                        @if($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('booking.store') }}">
                            @csrf
                            
                            <!-- Package/Service Selection -->
                            <div class="row mb-4">
                                @if($selectedPackage)
                                    <div class="col-12">
                                        <div class="alert alert-info">
                                            <h5 class="alert-heading">الباقة المختارة:</h5>
                                            <strong>{{ $selectedPackage->name }}</strong> - {{ number_format($selectedPackage->price) }} ر.س
                                            <input type="hidden" name="package_id" value="{{ $selectedPackage->id }}">
                                        </div>
                                    </div>
                                @elseif($selectedService)
                                    <div class="col-12">
                                        <div class="alert alert-info">
                                            <h5 class="alert-heading">الخدمة المختارة:</h5>
                                            <strong>{{ $selectedService->name }}</strong>
                                            <input type="hidden" name="service_id" value="{{ $selectedService->id }}">
                                        </div>
                                    </div>
                                @else
                                    <div class="col-md-6 mb-3">
                                        <label for="package_id" class="form-label">اختر الباقة (اختياري)</label>
                                        <select class="form-select" name="package_id" id="package_id">
                                            <option value="">-- اختر الباقة --</option>
                                            @foreach($packages as $package)
                                                <option value="{{ $package->id }}" {{ old('package_id') == $package->id ? 'selected' : '' }}>
                                                    {{ $package->name }} - {{ number_format($package->price) }} ر.س
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="service_id" class="form-label">اختر الخدمة (اختياري)</label>
                                        <select class="form-select" name="service_id" id="service_id">
                                            <option value="">-- اختر الخدمة --</option>
                                            @foreach($services as $service)
                                                <option value="{{ $service->id }}" {{ old('service_id') == $service->id ? 'selected' : '' }}>
                                                    {{ $service->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endif
                            </div>

                            <!-- Client Information -->
                            <h5 class="mb-3 text-primary">
                                <i class="fas fa-user me-2"></i>بيانات العميل
                            </h5>
                            <div class="row mb-4">
                                <div class="col-md-6 mb-3">
                                    <label for="client_name" class="form-label">الاسم الكامل *</label>
                                    <input type="text" class="form-control" name="client_name" id="client_name" 
                                           value="{{ old('client_name', auth()->user()->name ?? '') }}" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="client_email" class="form-label">البريد الإلكتروني *</label>
                                    <input type="email" class="form-control" name="client_email" id="client_email" 
                                           value="{{ old('client_email', auth()->user()->email ?? '') }}" required>
                                </div>
                                <div class="col-12 mb-3">
                                    <label for="client_phone" class="form-label">رقم الهاتف *</label>
                                    <input type="tel" class="form-control" name="client_phone" id="client_phone" 
                                           value="{{ old('client_phone', auth()->user()->phone ?? '') }}" required>
                                </div>
                            </div>

                            <!-- Event Information -->
                            <h5 class="mb-3 text-primary">
                                <i class="fas fa-calendar me-2"></i>تفاصيل المناسبة
                            </h5>
                            <div class="row mb-4">
                                <div class="col-md-6 mb-3">
                                    <label for="event_date" class="form-label">تاريخ المناسبة *</label>
                                    <input type="date" class="form-control" name="event_date" id="event_date" 
                                           value="{{ old('event_date') }}" min="{{ date('Y-m-d', strtotime('+1 day')) }}" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="guests_count" class="form-label">عدد الضيوف المتوقع *</label>
                                    <input type="number" class="form-control" name="guests_count" id="guests_count" 
                                           value="{{ old('guests_count', 50) }}" min="1" required>
                                </div>
                                <div class="col-12 mb-3">
                                    <label for="event_location" class="form-label">مكان المناسبة *</label>
                                    <input type="text" class="form-control" name="event_location" id="event_location" 
                                           value="{{ old('event_location') }}" placeholder="الرياض، المملكة العربية السعودية" required>
                                </div>
                                <div class="col-12 mb-3">
                                    <label for="special_requests" class="form-label">طلبات خاصة (اختياري)</label>
                                    <textarea class="form-control" name="special_requests" id="special_requests" rows="3" 
                                              placeholder="أي متطلبات خاصة أو تفاصيل إضافية تريد ذكرها...">{{ old('special_requests') }}</textarea>
                                </div>
                            </div>

                            <!-- Terms and Conditions -->
                            <div class="mb-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="terms" required>
                                    <label class="form-check-label" for="terms">
                                        أوافق على <a href="{{ route('terms') }}" target="_blank" class="text-primary text-decoration-none fw-semibold">الشروط والأحكام</a> و <a href="{{ route('privacy') }}" target="_blank" class="text-primary text-decoration-none fw-semibold">سياسة الخصوصية</a>
                                    </label>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="text-center">
                                <button type="submit" class="btn btn-primary btn-lg px-5">
                                    <i class="fas fa-paper-plane me-2"></i>إرسال طلب الحجز
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Contact Information -->
                <div class="text-center mt-5" data-aos="fade-up" data-aos-delay="200">
                    <h5 class="mb-3">هل تحتاج للمساعدة؟</h5>
                    <p class="text-muted mb-3">تواصل معنا مباشرة عبر:</p>
                    <div class="d-flex justify-content-center gap-4 flex-wrap">
                        <a href="tel:{{ setting('contact_phone', '+966 50 123 4567') }}" class="btn btn-outline-primary me-2">
                            <i class="fas fa-phone me-2"></i>{{ setting('contact_phone', '+966 50 123 4567') }}
                        </a>
                        <a href="mailto:{{ setting('contact_email', 'info@yourevents.sa') }}" class="btn btn-outline-primary">
                            <i class="fas fa-envelope me-2"></i>{{ setting('contact_email', 'info@yourevents.sa') }}
                        </a>
                        <a href="https://wa.me/966501234567" target="_blank" class="btn btn-outline-success">
                            <i class="fab fa-whatsapp me-2"></i>واتساب
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Prevent selecting both package and service
    const packageSelect = document.getElementById('package_id');
    const serviceSelect = document.getElementById('service_id');
    
    if (packageSelect && serviceSelect) {
        packageSelect.addEventListener('change', function() {
            if (this.value) {
                serviceSelect.value = '';
            }
        });
        
        serviceSelect.addEventListener('change', function() {
            if (this.value) {
                packageSelect.value = '';
            }
        });
    }

    // Form validation
    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        const packageId = document.querySelector('[name="package_id"]')?.value;
        const serviceId = document.querySelector('[name="service_id"]')?.value;
        
        if (!packageId && !serviceId) {
            e.preventDefault();
            alert('يرجى اختيار باقة أو خدمة واحدة على الأقل');
        }
    });
});
</script>
@endsection

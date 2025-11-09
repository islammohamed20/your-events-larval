@extends('layouts.app')

@section('title', 'الدفع وتأكيد الحجز - Your Events')

@section('content')
<div class="container py-5" style="margin-top: 100px;">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card shadow-lg border-0 rounded-4 mb-4">
                <div class="card-header bg-gradient text-white py-4" style="background: linear-gradient(135deg, #ef4870 0%, #ff7ba3 100%);">
                    <h3 class="mb-0">
                        <i class="fas fa-credit-card me-2"></i>
                        الدفع وتأكيد الحجز
                    </h3>
                </div>
                <div class="card-body p-4">
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <!-- Quote Summary -->
                    <div class="alert alert-info border-0 mb-4">
                        <h5 class="mb-3">
                            <i class="fas fa-file-invoice me-2"></i>
                            ملخص عرض السعر #{{ $quote->quote_number }}
                        </h5>
                        <div class="row">
                            <div class="col-md-6">
                                <p class="mb-2">
                                    <strong>عدد الخدمات:</strong> {{ $quote->items->count() }} خدمة
                                </p>
                                <p class="mb-2">
                                    <strong>المجموع الفرعي:</strong> {{ number_format($quote->subtotal, 2) }} ريال
                                </p>
                                <p class="mb-2">
                                    <strong>الضريبة (15%):</strong> {{ number_format($quote->tax, 2) }} ريال
                                </p>
                            </div>
                            <div class="col-md-6 text-md-end">
                                @if($quote->discount > 0)
                                <p class="mb-2 text-success">
                                    <strong>الخصم:</strong> -{{ number_format($quote->discount, 2) }} ريال
                                </p>
                                @endif
                                <h4 class="text-primary mb-0">
                                    <strong>الإجمالي:</strong> {{ number_format($quote->total, 2) }} ريال
                                </h4>
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('quotes.process-payment', $quote) }}" method="POST" id="paymentForm">
                        @csrf

                        <!-- Event Details -->
                        <div class="card border mb-4">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">
                                    <i class="fas fa-calendar-alt me-2 text-primary"></i>
                                    تفاصيل الحدث
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">الاسم الكامل <span class="text-danger">*</span></label>
                                        <input type="text" name="client_name" class="form-control @error('client_name') is-invalid @enderror" 
                                               value="{{ old('client_name', auth()->user()->name) }}" required>
                                        @error('client_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">رقم الجوال <span class="text-danger">*</span></label>
                                        <input type="tel" name="client_phone" class="form-control @error('client_phone') is-invalid @enderror" 
                                               value="{{ old('client_phone', auth()->user()->phone) }}" 
                                               placeholder="05xxxxxxxx" required>
                                        @error('client_phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">تاريخ الحدث <span class="text-danger">*</span></label>
                                        <input type="date" name="event_date" class="form-control @error('event_date') is-invalid @enderror" 
                                               value="{{ old('event_date') }}" min="{{ date('Y-m-d', strtotime('+1 day')) }}" required>
                                        @error('event_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">عدد الضيوف <span class="text-danger">*</span></label>
                                        <input type="number" name="guests_count" class="form-control @error('guests_count') is-invalid @enderror" 
                                               value="{{ old('guests_count', 50) }}" min="1" required>
                                        @error('guests_count')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label">موقع الحدث <span class="text-danger">*</span></label>
                                        <input type="text" name="event_location" class="form-control @error('event_location') is-invalid @enderror" 
                                               value="{{ old('event_location') }}" placeholder="مثال: الرياض - حي العليا" required>
                                        @error('event_location')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label">طلبات خاصة (اختياري)</label>
                                        <textarea name="special_requests" class="form-control @error('special_requests') is-invalid @enderror" 
                                                  rows="3" placeholder="أي تفاصيل أو طلبات إضافية...">{{ old('special_requests') }}</textarea>
                                        @error('special_requests')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Method -->
                        <div class="card border mb-4">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">
                                    <i class="fas fa-wallet me-2 text-success"></i>
                                    طريقة الدفع
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label class="form-label">اختر طريقة الدفع <span class="text-danger">*</span></label>
                                        <div class="d-flex gap-3 flex-wrap">
                                            <div class="form-check payment-method-card">
                                                <input class="form-check-input" type="radio" name="payment_method" 
                                                       id="card" value="card" checked>
                                                <label class="form-check-label" for="card">
                                                    <i class="fas fa-credit-card me-2"></i>
                                                    بطاقة ائتمانية
                                                </label>
                                            </div>
                                            <div class="form-check payment-method-card">
                                                <input class="form-check-input" type="radio" name="payment_method" 
                                                       id="bank_transfer" value="bank_transfer">
                                                <label class="form-check-label" for="bank_transfer">
                                                    <i class="fas fa-university me-2"></i>
                                                    تحويل بنكي
                                                </label>
                                            </div>
                                            <div class="form-check payment-method-card">
                                                <input class="form-check-input" type="radio" name="payment_method" 
                                                       id="cash" value="cash">
                                                <label class="form-check-label" for="cash">
                                                    <i class="fas fa-money-bill-wave me-2"></i>
                                                    نقداً عند الاستلام
                                                </label>
                                            </div>
                                        </div>
                                        @error('payment_method')
                                            <div class="text-danger small mt-2">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Card Payment Details -->
                                    <div id="cardDetails" class="col-12">
                                        <div class="card bg-light border-0 mt-3">
                                            <div class="card-body">
                                                <h6 class="mb-3">معلومات البطاقة</h6>
                                                <div class="row g-3">
                                                    <div class="col-md-12">
                                                        <label class="form-label">نوع البطاقة</label>
                                                        <select name="card_type" class="form-select @error('card_type') is-invalid @enderror">
                                                            <option value="">اختر نوع البطاقة</option>
                                                            <option value="visa" {{ old('card_type') == 'visa' ? 'selected' : '' }}>
                                                                <i class="fab fa-cc-visa"></i> Visa
                                                            </option>
                                                            <option value="mastercard" {{ old('card_type') == 'mastercard' ? 'selected' : '' }}>
                                                                <i class="fab fa-cc-mastercard"></i> Mastercard
                                                            </option>
                                                            <option value="mada" {{ old('card_type') == 'mada' ? 'selected' : '' }}>
                                                                مدى (Mada)
                                                            </option>
                                                        </select>
                                                        @error('card_type')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>

                                                    <div class="col-md-12">
                                                        <label class="form-label">اسم حامل البطاقة</label>
                                                        <input type="text" name="card_holder_name" 
                                                               class="form-control @error('card_holder_name') is-invalid @enderror" 
                                                               value="{{ old('card_holder_name') }}" 
                                                               placeholder="الاسم كما يظهر على البطاقة">
                                                        @error('card_holder_name')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>

                                                    <div class="col-md-6">
                                                        <label class="form-label">آخر 4 أرقام من البطاقة</label>
                                                        <input type="text" name="card_last_four" 
                                                               class="form-control @error('card_last_four') is-invalid @enderror" 
                                                               value="{{ old('card_last_four') }}" 
                                                               placeholder="1234" maxlength="4" pattern="[0-9]{4}">
                                                        <small class="text-muted">للتحقق فقط - لن يتم تخزين رقم البطاقة الكامل</small>
                                                        @error('card_last_four')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>

                                                    <div class="col-md-3">
                                                        <label class="form-label">الشهر</label>
                                                        <select name="card_expiry_month" class="form-select @error('card_expiry_month') is-invalid @enderror">
                                                            <option value="">شهر</option>
                                                            @for($m = 1; $m <= 12; $m++)
                                                                <option value="{{ $m }}" {{ old('card_expiry_month') == $m ? 'selected' : '' }}>
                                                                    {{ sprintf('%02d', $m) }}
                                                                </option>
                                                            @endfor
                                                        </select>
                                                        @error('card_expiry_month')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>

                                                    <div class="col-md-3">
                                                        <label class="form-label">السنة</label>
                                                        <select name="card_expiry_year" class="form-select @error('card_expiry_year') is-invalid @enderror">
                                                            <option value="">سنة</option>
                                                            @for($y = date('Y'); $y <= date('Y') + 10; $y++)
                                                                <option value="{{ $y }}" {{ old('card_expiry_year') == $y ? 'selected' : '' }}>
                                                                    {{ $y }}
                                                                </option>
                                                            @endfor
                                                        </select>
                                                        @error('card_expiry_year')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="alert alert-warning border-0 mt-3 mb-0">
                                                    <i class="fas fa-shield-alt me-2"></i>
                                                    <small>
                                                        <strong>ملاحظة أمنية:</strong> نحن نلتزم بأعلى معايير الأمان. 
                                                        لن يتم حفظ معلومات البطاقة الكاملة على سيرفرنا.
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Bank Transfer Instructions -->
                                    <div id="bankDetails" class="col-12" style="display: none;">
                                        <div class="alert alert-info border-0 mt-3">
                                            <h6><i class="fas fa-info-circle me-2"></i>تعليمات التحويل البنكي</h6>
                                            <p class="mb-2">يرجى التحويل على الحساب التالي:</p>
                                            <ul class="mb-0">
                                                <li>اسم البنك: البنك الأهلي السعودي</li>
                                                <li>رقم الحساب: SA1234567890</li>
                                                <li>اسم المستفيد: Your Events</li>
                                                <li>المبلغ: {{ number_format($quote->total, 2) }} ريال</li>
                                            </ul>
                                            <p class="mt-2 mb-0">
                                                <small class="text-muted">
                                                    سيتم التواصل معك لتأكيد استلام التحويل خلال 24 ساعة
                                                </small>
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Cash Instructions -->
                                    <div id="cashDetails" class="col-12" style="display: none;">
                                        <div class="alert alert-success border-0 mt-3">
                                            <h6><i class="fas fa-check-circle me-2"></i>الدفع نقداً</h6>
                                            <p class="mb-0">
                                                سيتم التواصل معك لترتيب موعد استلام المبلغ نقداً قبل موعد الحدث.
                                                المبلغ المطلوب: <strong>{{ number_format($quote->total, 2) }} ريال</strong>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Terms & Conditions -->
                        <div class="form-check mb-4">
                            <input class="form-check-input" type="checkbox" id="terms" required>
                            <label class="form-check-label" for="terms">
                                أوافق على <a href="{{ route('terms') }}" target="_blank" class="text-primary text-decoration-none fw-semibold">الشروط والأحكام</a> و<a href="#" class="text-primary text-decoration-none fw-semibold">سياسة الخصوصية</a>
                                <span class="text-danger">*</span>
                            </label>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="fas fa-check-circle me-2"></i>
                                تأكيد الدفع والحجز
                            </button>
                            <a href="{{ route('quotes.show', $quote) }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-right me-2"></i>
                                العودة لعرض السعر
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Security Notice -->
            <div class="text-center text-muted small">
                <i class="fas fa-lock me-2"></i>
                جميع المعاملات محمية بتقنية SSL والتشفير من طرف إلى طرف
            </div>
        </div>
    </div>
</div>

<style>
.payment-method-card {
    flex: 1;
    min-width: 150px;
}

.payment-method-card .form-check-input:checked ~ .form-check-label {
    color: #ef4870;
    font-weight: bold;
}

.payment-method-card label {
    cursor: pointer;
    padding: 15px 20px;
    border: 2px solid #dee2e6;
    border-radius: 10px;
    display: block;
    text-align: center;
    transition: all 0.3s ease;
}

.payment-method-card input:checked ~ label {
    border-color: #ef4870;
    background-color: #fff5f7;
}

.payment-method-card label:hover {
    border-color: #ef4870;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(239, 72, 112, 0.2);
}

.btn-success {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    border: none;
    transition: all 0.3s ease;
}

.btn-success:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(40, 167, 69, 0.4);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const paymentMethods = document.querySelectorAll('input[name="payment_method"]');
    const cardDetails = document.getElementById('cardDetails');
    const bankDetails = document.getElementById('bankDetails');
    const cashDetails = document.getElementById('cashDetails');

    paymentMethods.forEach(method => {
        method.addEventListener('change', function() {
            cardDetails.style.display = 'none';
            bankDetails.style.display = 'none';
            cashDetails.style.display = 'none';

            if (this.value === 'card') {
                cardDetails.style.display = 'block';
            } else if (this.value === 'bank_transfer') {
                bankDetails.style.display = 'block';
            } else if (this.value === 'cash') {
                cashDetails.style.display = 'block';
            }
        });
    });

    // Form validation
    const form = document.getElementById('paymentForm');
    form.addEventListener('submit', function(e) {
        const paymentMethod = document.querySelector('input[name="payment_method"]:checked').value;
        
        if (paymentMethod === 'card') {
            const cardType = document.querySelector('select[name="card_type"]').value;
            const cardHolder = document.querySelector('input[name="card_holder_name"]').value;
            const cardLast4 = document.querySelector('input[name="card_last_four"]').value;
            const cardMonth = document.querySelector('select[name="card_expiry_month"]').value;
            const cardYear = document.querySelector('select[name="card_expiry_year"]').value;

            if (!cardType || !cardHolder || !cardLast4 || !cardMonth || !cardYear) {
                e.preventDefault();
                alert('يرجى إكمال جميع معلومات البطاقة');
                return false;
            }

            if (!/^\d{4}$/.test(cardLast4)) {
                e.preventDefault();
                alert('يرجى إدخال آخر 4 أرقام صحيحة من البطاقة');
                return false;
            }
        }
    });
});
</script>
@endsection

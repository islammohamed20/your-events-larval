@extends('layouts.app')

@section('title', 'الدفع - Your Events')

@section('content')
<div class="container py-5" style="margin-top: 100px;">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card shadow-lg border-0 rounded-4 mb-4">
                <div class="card-header bg-gradient text-white py-4" style="background: linear-gradient(135deg, #ef4870 0%, #ff7ba3 100%);">
                    <h3 class="mb-0">
                        <i class="fas fa-credit-card me-2"></i>
                        الدفع
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
                                    <strong>المجموع الفرعي:</strong> {{ number_format($quote->subtotal, 2) }} {{ __('common.currency') }}
                                </p>
                                <p class="mb-2">
                                    <strong>الضريبة (15%):</strong> {{ number_format($quote->tax, 2) }} {{ __('common.currency') }}
                                </p>
                            </div>
                            <div class="col-md-6 text-md-end">
                                @if($quote->discount > 0)
                                <p class="mb-2 text-success">
                                    <strong>الخصم:</strong> -{{ number_format($quote->discount, 2) }} {{ __('common.currency') }}
                                </p>
                                @endif
                                <h4 class="text-primary mb-0">
                                    <strong>الإجمالي:</strong> {{ number_format($quote->total, 2) }} {{ __('common.currency') }}
                                </h4>
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('quotes.process-payment', $quote) }}" method="POST" id="paymentForm">
                        @csrf
                        <input type="hidden" name="client_name" value="{{ old('client_name', $bookingData['client_name'] ?? auth()->user()->name) }}">
                        <input type="hidden" name="client_phone" value="{{ old('client_phone', $bookingData['client_phone'] ?? auth()->user()->phone) }}">
                        <input type="hidden" name="event_date" value="{{ old('event_date', $bookingData['event_date'] ?? '') }}">
                        <input type="hidden" name="guests_count" value="{{ old('guests_count', $bookingData['guests_count'] ?? '') }}">
                        <input type="hidden" name="event_location" value="{{ old('event_location', $bookingData['event_location'] ?? '') }}">
                        <input type="hidden" name="event_lat" value="{{ old('event_lat', $bookingData['event_lat'] ?? '') }}">
                        <input type="hidden" name="event_lng" value="{{ old('event_lng', $bookingData['event_lng'] ?? '') }}">
                        <input type="hidden" name="special_requests" value="{{ old('special_requests', $bookingData['special_requests'] ?? '') }}">

                        <div class="card border mb-4">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">
                                    <i class="fas fa-wallet me-2 text-success"></i>
                                    بيانات طريقة الدفع
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label class="form-label">طريقة الدفع</label>
                                        @php
                                            $selectedPaymentMethod = old('payment_method', 'mada');
                                            if ($selectedPaymentMethod === 'card') {
                                                $selectedPaymentMethod = 'mada';
                                            }
                                        @endphp
                                        <div class="payment-method-grid">
                                            <input class="btn-check" type="radio" name="payment_method" id="pm_mada" value="mada" {{ $selectedPaymentMethod === 'mada' ? 'checked' : '' }}>
                                            <label class="payment-method-pill" for="pm_mada">
                                                <span class="pm-icon"><i class="fas fa-id-card"></i></span>
                                                <span class="pm-text">مدى</span>
                                            </label>

                                            <input class="btn-check" type="radio" name="payment_method" id="pm_visa" value="visa" {{ $selectedPaymentMethod === 'visa' ? 'checked' : '' }}>
                                            <label class="payment-method-pill" for="pm_visa">
                                                <span class="pm-icon"><i class="fab fa-cc-visa"></i></span>
                                                <span class="pm-text">Visa</span>
                                            </label>

                                            <input class="btn-check" type="radio" name="payment_method" id="pm_mastercard" value="mastercard" {{ $selectedPaymentMethod === 'mastercard' ? 'checked' : '' }}>
                                            <label class="payment-method-pill" for="pm_mastercard">
                                                <span class="pm-icon"><i class="fab fa-cc-mastercard"></i></span>
                                                <span class="pm-text">Mastercard</span>
                                            </label>

                                            <input class="btn-check" type="radio" name="payment_method" id="pm_applepay" value="applepay" {{ $selectedPaymentMethod === 'applepay' ? 'checked' : '' }}>
                                            <label class="payment-method-pill" for="pm_applepay">
                                                <span class="pm-icon"><i class="fab fa-apple-pay"></i></span>
                                                <span class="pm-text">Apple Pay</span>
                                            </label>

                                            <input class="btn-check" type="radio" name="payment_method" id="pm_stcpay" value="stcpay" {{ $selectedPaymentMethod === 'stcpay' ? 'checked' : '' }}>
                                            <label class="payment-method-pill" for="pm_stcpay">
                                                <span class="pm-icon"><i class="fas fa-mobile-alt"></i></span>
                                                <span class="pm-text">STC Pay</span>
                                            </label>
                                        </div>
                                        @error('payment_method')
                                            <div class="text-danger small mt-2">{{ $message }}</div>
                                        @enderror
                                    </div>

                                </div>
                            </div>
                        </div>

                        <div class="form-check mb-4">
                            <input class="form-check-input" type="checkbox" id="terms" required>
                            <label class="form-check-label" for="terms">
                                أوافق على <a href="{{ route('terms') }}" target="_blank" class="text-primary text-decoration-none fw-semibold">الشروط والأحكام</a> و<a href="#" class="text-primary text-decoration-none fw-semibold">سياسة الخصوصية</a>
                                <span class="text-danger">*</span>
                            </label>
                        </div>

                        <div class="d-flex flex-column flex-md-row gap-2">
                            <button type="submit" class="btn btn-success btn-lg flex-fill">
                                <i class="fas fa-check-circle me-2"></i>
                                الانتقال للدفع
                            </button>
                            <a href="{{ route('quotes.show', $quote) }}" class="btn btn-outline-secondary flex-fill">
                                <i class="fas fa-arrow-right me-2"></i>
                                العودة لعرض السعر
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="text-center text-muted small">
                <i class="fas fa-lock me-2"></i>
                جميع المعاملات محمية بتقنية SSL والتشفير من طرف إلى طرف
            </div>
        </div>
    </div>
</div>

<style>
.payment-method-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(130px, 1fr));
    gap: 10px;
}

.payment-method-pill {
    cursor: pointer;
    user-select: none;
    border: 1px solid #dee2e6;
    border-radius: 12px;
    padding: 10px 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    background: #fff;
    font-weight: 700;
    color: #1f144a;
}

.payment-method-pill .pm-icon {
    font-size: 18px;
    line-height: 1;
}

.btn-check:checked + .payment-method-pill {
    border-color: #ef4870;
    color: #ef4870;
    box-shadow: 0 0 0 0.15rem rgba(239, 72, 112, 0.16);
}

@media (max-width: 575px) {
    .payment-method-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
    .payment-method-pill {
        padding: 9px 10px;
        gap: 8px;
    }
}

.payment-method-card input:checked ~ label {
    border-color: #ef4870;
    background-color: #fff5f7;
}

.payment-method-card label:hover {
    border-color: #ef4870;
}

.btn-success {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    border: none;
}

.btn-success:hover {
    transform: none;
    box-shadow: none;
}

.card.shadow-lg.border-0.rounded-4.mb-4 {
    transition: none !important;
}

.card.shadow-lg.border-0.rounded-4.mb-4::before {
    transition: none !important;
}

.card.shadow-lg.border-0.rounded-4.mb-4:hover {
    transform: none !important;
    box-shadow: inherit !important;
}

.card.shadow-lg.border-0.rounded-4.mb-4:hover::before {
    transform: none !important;
}
</style>

@endsection

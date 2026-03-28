@extends('layouts.app')

@section('title', 'استكمال بيانات الحجز والدفع - Your Events')

@section('content')
<div class="container py-5 complete-booking-container">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card shadow-lg border-0 rounded-4 mb-4">
                <div class="card-header bg-gradient text-white py-4" style="background: linear-gradient(135deg, #ef4870 0%, #ff7ba3 100%);">
                    <h3 class="mb-0">
                        <i class="fas fa-calendar-alt me-2"></i>
                        استكمال بيانات الحجز والدفع
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

                    <form action="{{ route('quotes.complete-booking.store', $quote) }}" method="POST" id="completeBookingPaymentForm">
                        @csrf

                        <div class="card border mb-4">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">
                                    <i class="fas fa-calendar-alt me-2 text-primary"></i>
                                    بيانات الحدث
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">الاسم الكامل <span class="text-danger">*</span></label>
                                        <input type="text" name="client_name" class="form-control @error('client_name') is-invalid @enderror"
                                               value="{{ old('client_name', $bookingData['client_name'] ?? auth()->user()->name) }}" required>
                                        @error('client_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">رقم الجوال <span class="text-danger">*</span></label>
                                        <input type="tel" name="client_phone" class="form-control @error('client_phone') is-invalid @enderror"
                                               value="{{ old('client_phone', $bookingData['client_phone'] ?? auth()->user()->phone) }}"
                                               placeholder="05xxxxxxxx" required>
                                        @error('client_phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    @php
                                        $prefillDate = old('event_date', $bookingData['event_date'] ?? $suggestedEventDate ?? '');
                                        $dateIsLocked = !old('event_date') && empty($bookingData['event_date']) && !empty($suggestedEventDate);
                                    @endphp
                                    <div class="col-md-6">
                                        <label class="form-label">تاريخ الحدث <span class="text-danger">*</span></label>
                                        <input type="date" name="event_date" class="form-control @error('event_date') is-invalid @enderror"
                                               value="{{ $prefillDate }}"
                                               min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                                               {{ $dateIsLocked ? 'readonly' : '' }}
                                               required>
                                        @if($dateIsLocked)
                                        <div class="form-text text-success">
                                            <i class="fas fa-calendar-check me-1"></i>تم تحديد التاريخ تلقائياً بناءً على اختيارك في صفحة الخدمة.
                                        </div>
                                        @endif
                                        @error('event_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">عدد الضيوف <span class="text-danger">*</span></label>
                                        <input type="number" name="guests_count" class="form-control @error('guests_count') is-invalid @enderror"
                                               value="{{ old('guests_count', $bookingData['guests_count'] ?? 50) }}" min="1" required>
                                        @error('guests_count')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-12">
                                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                                            <div class="small text-muted">اختر موقع الحدث من الخريطة</div>
                                            <button type="button" class="btn btn-outline-primary btn-sm" id="eventUseMyLocation">
                                                <i class="fas fa-location-arrow me-1"></i>استخدم موقعي
                                            </button>
                                        </div>
                                        <input type="hidden" name="event_location" value="{{ old('event_location', $bookingData['event_location'] ?? '') }}">
                                        <input type="hidden" name="event_lat" value="{{ old('event_lat', $bookingData['event_lat'] ?? '') }}">
                                        <input type="hidden" name="event_lng" value="{{ old('event_lng', $bookingData['event_lng'] ?? '') }}">
                                        <div id="eventLocationMap" class="border rounded mt-2"></div>
                                        <div id="eventLocationDisplay" class="small text-muted mt-2">
                                            {{ old('event_location', $bookingData['event_location'] ?? 'لم يتم اختيار موقع بعد') }}
                                        </div>
                                        <div id="eventLocationError" class="text-danger small mt-2 d-none">
                                            يرجى تحديد موقع الحدث من الخريطة
                                        </div>
                                        @error('event_location')
                                            <div class="text-danger small mt-2">{{ $message }}</div>
                                        @enderror
                                        @error('event_lat')
                                            <div class="text-danger small mt-2">{{ $message }}</div>
                                        @enderror
                                        @error('event_lng')
                                            <div class="text-danger small mt-2">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label">طلبات خاصة (اختياري)</label>
                                        <textarea name="special_requests" class="form-control @error('special_requests') is-invalid @enderror"
                                                  rows="3" placeholder="أي تفاصيل أو طلبات إضافية...">{{ old('special_requests', $bookingData['special_requests'] ?? '') }}</textarea>
                                        @error('special_requests')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

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
                                        <label class="form-label">طريقة الدفع <span class="text-danger">*</span></label>
                                        @php
                                            $selectedPaymentMethod = old('payment_method', $bookingData['payment_method'] ?? 'mada');
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
                            <button type="submit" class="btn btn-success btn-lg flex-fill" id="confirmPaymentBtn">
                                <i class="fas fa-check-circle me-2"></i>
                                تأكيد الحجز والدفع
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
#eventLocationMap {
    height: 320px;
    min-height: 320px;
    width: 100%;
}

.complete-booking-container {
    margin-top: 100px;
}

@media (max-width: 768px) {
    .complete-booking-container {
        margin-top: 20px;
    }
    #eventLocationMap {
        height: 220px;
        min-height: 220px;
    }
}

.leaflet-container img {
    max-width: none !important;
    max-height: none !important;
}

.leaflet-container .leaflet-tile {
    max-width: none !important;
    max-height: none !important;
    width: 256px !important;
    height: 256px !important;
}

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

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.css" crossorigin="anonymous" referrerpolicy="no-referrer">
@endpush

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.js" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const locationInput = document.querySelector('input[name="event_location"]');
    const locationDisplay = document.getElementById('eventLocationDisplay');
    const latInput = document.querySelector('input[name="event_lat"]');
    const lngInput = document.querySelector('input[name="event_lng"]');
    const useMyLocationBtn = document.getElementById('eventUseMyLocation');
    const mapEl = document.getElementById('eventLocationMap');
    const errorEl = document.getElementById('eventLocationError');

    if (!mapEl || typeof L === 'undefined') {
        if (useMyLocationBtn) useMyLocationBtn.style.display = 'none';
        return;
    }

    const defaultCenter = [24.7136, 46.6753];
    const initialLat = parseFloat(latInput?.value || '');
    const initialLng = parseFloat(lngInput?.value || '');
    const hasInitial = Number.isFinite(initialLat) && Number.isFinite(initialLng);

    const map = L.map(mapEl).setView(hasInitial ? [initialLat, initialLng] : defaultCenter, hasInitial ? 14 : 11);

    L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap &copy; CARTO',
    }).addTo(map);

    setTimeout(function() {
        map.invalidateSize();
    }, 50);

    window.addEventListener('load', function() {
        map.invalidateSize();
    });

    window.addEventListener('resize', function() {
        map.invalidateSize();
    });

    let marker = null;

    function setLocation(lat, lng, shouldPan) {
        if (errorEl) {
            errorEl.classList.add('d-none');
        }
        if (marker) {
            marker.setLatLng([lat, lng]);
        } else {
            marker = L.marker([lat, lng], { draggable: true }).addTo(map);
            marker.on('dragend', function(e) {
                const ll = e.target.getLatLng();
                setLocation(ll.lat, ll.lng, false);
            });
        }

        if (latInput) latInput.value = lat.toFixed(7);
        if (lngInput) lngInput.value = lng.toFixed(7);

        if (shouldPan) {
            map.setView([lat, lng], Math.max(map.getZoom(), 14));
        }

        const fallbackLocation = lat.toFixed(5) + ', ' + lng.toFixed(5);
        if (locationInput) {
            locationInput.value = fallbackLocation;
        }
        if (locationDisplay) {
            locationDisplay.textContent = fallbackLocation;
        }

        const url = 'https://nominatim.openstreetmap.org/reverse?format=jsonv2&accept-language=ar&lat=' + encodeURIComponent(lat) + '&lon=' + encodeURIComponent(lng);
        fetch(url, { headers: { 'Accept': 'application/json' } })
            .then(r => r.ok ? r.json() : null)
            .then(data => {
                const name = data && data.display_name ? String(data.display_name) : '';
                if (name && locationInput) {
                    locationInput.value = name;
                }
                if (name && locationDisplay) {
                    locationDisplay.textContent = name;
                }
            })
            .catch(() => {});
    }

    map.on('click', function(e) {
        setLocation(e.latlng.lat, e.latlng.lng, false);
    });

    if (hasInitial) {
        setLocation(initialLat, initialLng, true);
    }

    if (useMyLocationBtn && navigator.geolocation) {
        useMyLocationBtn.addEventListener('click', function() {
            useMyLocationBtn.disabled = true;
            navigator.geolocation.getCurrentPosition(
                function(pos) {
                    useMyLocationBtn.disabled = false;
                    setLocation(pos.coords.latitude, pos.coords.longitude, true);
                },
                function() {
                    useMyLocationBtn.disabled = false;
                },
                { enableHighAccuracy: true, timeout: 10000, maximumAge: 60000 }
            );
        });
    } else if (useMyLocationBtn) {
        useMyLocationBtn.style.display = 'none';
    }

    const form = document.getElementById('completeBookingPaymentForm');
    if (form) {
        form.addEventListener('submit', async function(e) {
            const latValue = parseFloat(latInput?.value || '');
            const lngValue = parseFloat(lngInput?.value || '');
            const hasLocation = Number.isFinite(latValue) && Number.isFinite(lngValue);

            if (!hasLocation) {
                e.preventDefault();
                if (errorEl) {
                    errorEl.classList.remove('d-none');
                }
                mapEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
                return;
            }

            e.preventDefault();

            const btn = document.getElementById('confirmPaymentBtn');
            const originalBtnHtml = btn ? btn.innerHTML : '';
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>جاري تحويلك للدفع...';
            }

            const existingAlert = form.querySelector('[data-payment-alert="1"]');
            if (existingAlert) existingAlert.remove();

            try {
                const formData = new FormData(form);
                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                    },
                    body: formData,
                    credentials: 'same-origin',
                });

                const contentType = response.headers.get('content-type') || '';
                const data = contentType.includes('application/json') ? await response.json() : null;

                if (response.ok && data && data.redirect_url) {
                    window.location.href = data.redirect_url;
                    return;
                }

                const message = (data && (data.message || data.error)) ? (data.message || data.error) : 'تعذر بدء عملية الدفع. حاول مرة أخرى.';
                const alertEl = document.createElement('div');
                alertEl.className = 'alert alert-danger mt-3';
                alertEl.setAttribute('data-payment-alert', '1');
                alertEl.innerHTML = '<i class="fas fa-exclamation-circle me-2"></i>' + message;
                form.prepend(alertEl);
            } catch (err) {
                const alertEl = document.createElement('div');
                alertEl.className = 'alert alert-danger mt-3';
                alertEl.setAttribute('data-payment-alert', '1');
                alertEl.innerHTML = '<i class="fas fa-exclamation-circle me-2"></i>حدث خطأ غير متوقع أثناء بدء الدفع.';
                form.prepend(alertEl);
            } finally {
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = originalBtnHtml || btn.innerHTML;
                }
            }
        });
    }

});
</script>
@endpush
@endsection

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
                                @php
                                    $defaultBookingType = old('booking_type');
                                    if (! $defaultBookingType) {
                                        $defaultBookingType = $selectedService ? 'service' : ($selectedPackage ? 'package' : '');
                                    }
                                    $selectedServiceId = old('service_id', $selectedService?->id);
                                    $selectedPackageId = old('package_id', $selectedPackage?->id);
                                    $serviceFromOld = $selectedServiceId ? $services->firstWhere('id', (int) $selectedServiceId) : null;
                                    $packageFromOld = $selectedPackageId ? $packages->firstWhere('id', (int) $selectedPackageId) : null;
                                @endphp

                                <div class="col-12 mb-3">
                                    <label for="booking_type" class="form-label">اختار نوع الحجز *</label>
                                    <select class="form-select" name="booking_type" id="booking_type" required>
                                        <option value="">-- اختار نوع الحجز --</option>
                                        <option value="service" {{ $defaultBookingType === 'service' ? 'selected' : '' }}>خدمة</option>
                                        <option value="package" {{ $defaultBookingType === 'package' ? 'selected' : '' }}>باقة</option>
                                    </select>
                                </div>

                                <div class="col-12" id="bookingTypeService" style="display:none;">
                                    <div class="border rounded-3 p-3" style="background:#f8fafc;">
                                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
                                            <div class="fw-semibold">الخدمة المختارة</div>
                                            <a href="{{ route('services.index') }}" class="btn btn-outline-primary btn-sm">
                                                <i class="fas fa-search me-1"></i>تصفح الخدمات
                                            </a>
                                        </div>

                                        <input type="hidden" name="service_id" id="service_id" value="{{ $selectedServiceId ?: '' }}">

                                        @if($serviceFromOld)
                                            <div class="alert alert-info mb-0">
                                                <strong>{{ $serviceFromOld->name }}</strong>
                                            </div>
                                        @else
                                            <div class="text-muted small">لم يتم اختيار خدمة بعد.</div>
                                        @endif
                                    </div>
                                </div>

                                <div class="col-12" id="bookingTypePackage" style="display:none;">
                                    <div class="border rounded-3 p-3" style="background:#f8fafc;">
                                        <label for="package_id" class="form-label">اختر الباقة *</label>
                                        <select class="form-select" name="package_id" id="package_id">
                                            <option value="">-- اختر الباقة --</option>
                                            @foreach($packages as $package)
                                                <option value="{{ $package->id }}" {{ (string) $selectedPackageId === (string) $package->id ? 'selected' : '' }}>
                                                    {{ $package->name }} - {{ number_format($package->price) }} {{ __('common.currency') }}
                                                </option>
                                            @endforeach
                                        </select>

                                        @if($packageFromOld)
                                            <div class="text-muted small mt-2">
                                                الباقة الحالية: <span class="fw-semibold">{{ $packageFromOld->name }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
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
                                           value="{{ old('event_date', $selectedEventDate ?? '') }}" min="{{ date('Y-m-d', strtotime('+1 day')) }}" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="guests_count" class="form-label">عدد الضيوف المتوقع *</label>
                                    <input type="number" class="form-control" name="guests_count" id="guests_count" 
                                           value="{{ old('guests_count', 50) }}" min="1" required>
                                </div>
                                <div class="col-12 mb-3">
                                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                                        <label for="event_location" class="form-label mb-0">مكان المناسبة *</label>
                                        <button type="button" class="btn btn-outline-primary btn-sm" id="eventUseMyLocation">
                                            <i class="fas fa-location-arrow me-1"></i>استخدم موقعي
                                        </button>
                                    </div>
                                    <input type="hidden" name="event_location" id="event_location" value="{{ old('event_location') }}">
                                    <input type="hidden" name="event_lat" value="{{ old('event_lat') }}">
                                    <input type="hidden" name="event_lng" value="{{ old('event_lng') }}">
                                    <div id="eventLocationMap" class="border rounded mt-2"></div>
                                    <div id="eventLocationDisplay" class="small text-muted mt-2">
                                        {{ old('event_location', 'لم يتم اختيار موقع بعد') }}
                                    </div>
                                    <div id="eventLocationError" class="text-danger small mt-2 d-none">
                                        يرجى تحديد موقع المناسبة من الخريطة
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

<style>
#eventLocationMap {
    height: 320px;
    min-height: 320px;
    width: 100%;
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
</style>

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.css" crossorigin="anonymous" referrerpolicy="no-referrer">
@endpush

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const bookingTypeSelect = document.getElementById('booking_type');
    const serviceSection = document.getElementById('bookingTypeService');
    const packageSection = document.getElementById('bookingTypePackage');
    const serviceInput = document.getElementById('service_id');
    const packageSelect = document.getElementById('package_id');

    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            const bookingType = bookingTypeSelect?.value || '';
            const packageId = packageSelect?.value || '';
            const serviceId = serviceInput?.value || '';

            if (!bookingType) {
                e.preventDefault();
                alert('يرجى اختيار نوع الحجز');
                return;
            }

            if (bookingType === 'service' && !serviceId) {
                e.preventDefault();
                alert('يرجى اختيار خدمة');
                return;
            }

            if (bookingType === 'package' && !packageId) {
                e.preventDefault();
                alert('يرجى اختيار باقة');
                return;
            }
        });
    }

    function updateBookingTypeUI() {
        const type = bookingTypeSelect?.value || '';
        if (serviceSection) serviceSection.style.display = type === 'service' ? 'block' : 'none';
        if (packageSection) packageSection.style.display = type === 'package' ? 'block' : 'none';

        if (type === 'service') {
            if (packageSelect) packageSelect.value = '';
        }

        if (type === 'package') {
            if (serviceInput) serviceInput.value = '';
        }
    }

    if (bookingTypeSelect) {
        bookingTypeSelect.addEventListener('change', updateBookingTypeUI);
        updateBookingTypeUI();
    }
});
</script>
@endsection

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

    const form = mapEl.closest('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            const latValue = parseFloat(latInput?.value || '');
            const lngValue = parseFloat(lngInput?.value || '');
            const hasLocation = Number.isFinite(latValue) && Number.isFinite(lngValue);

            if (!hasLocation) {
                e.preventDefault();
                if (errorEl) {
                    errorEl.classList.remove('d-none');
                }
                mapEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        });
    }
});
</script>
@endpush

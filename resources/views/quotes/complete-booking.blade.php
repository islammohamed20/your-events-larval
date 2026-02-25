@extends('layouts.app')

@section('title', 'استكمال بيانات الحجز - Your Events')

@section('content')
<div class="container py-5" style="margin-top: 100px;">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card shadow-lg border-0 rounded-4 mb-4">
                <div class="card-header bg-gradient text-white py-4" style="background: linear-gradient(135deg, #ef4870 0%, #ff7ba3 100%);">
                    <h3 class="mb-0">
                        <i class="fas fa-calendar-alt me-2"></i>
                        استكمال بيانات الحجز
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

                    <form action="{{ route('quotes.complete-booking.store', $quote) }}" method="POST">
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

                                    <div class="col-md-6">
                                        <label class="form-label">تاريخ الحدث <span class="text-danger">*</span></label>
                                        <input type="date" name="event_date" class="form-control @error('event_date') is-invalid @enderror"
                                               value="{{ old('event_date', $bookingData['event_date'] ?? '') }}" min="{{ date('Y-m-d', strtotime('+1 day')) }}" required>
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
        </div>
    </div>
</div>

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
@endsection

@extends('layouts.admin')

@section('title', __('common.admin_dashboard_title') . ' - Your Events')
@section('page-title', __('common.admin_dashboard_main'))
@section('page-description', __('common.admin_dashboard_overview'))

@section('content')
<div class="container-fluid" id="adminDashboardAutoRefresh">
    @php
        $canCustomers = $dashboardPermissions['customers'] ?? true;
        $canAdmins = $dashboardPermissions['admins'] ?? true;
        $canServices = $dashboardPermissions['services'] ?? true;
        $canPackages = $dashboardPermissions['packages'] ?? true;
        $canBookings = $dashboardPermissions['bookings'] ?? true;
        $canQuotes = $dashboardPermissions['quotes'] ?? true;
        $canEmails = $dashboardPermissions['emails'] ?? true;
        $canTraffic = $dashboardPermissions['traffic'] ?? true;
        $canOtp = $dashboardPermissions['otp'] ?? true;
        $canGallery = $dashboardPermissions['gallery'] ?? true;
        $canReviews = $dashboardPermissions['reviews'] ?? true;
        $canQuickSummary = $dashboardPermissions['quick_summary'] ?? true;
    @endphp

    <!-- Statistics Cards -->
    <div class="row g-3 mb-4">
        @if($canCustomers)
        <div class="col-xl-3 col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <h3 class="mb-0">{{ $stats['customers'] ?? 0 }}</h3>
                        <p class="mb-0 text-muted">{{ __('common.customers') }}</p>
                    </div>
                    <i class="fas fa-user-tie fa-2x text-primary opacity-75"></i>
                </div>
            </div>
        </div>
        @endif
        @if($canAdmins)
        <div class="col-xl-3 col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <h3 class="mb-0">{{ $stats['admin_users'] ?? 0 }}</h3>
                        <p class="mb-0 text-muted">{{ __('common.admins') }}</p>
                    </div>
                    <i class="fas fa-user-shield fa-2x text-secondary opacity-75"></i>
                </div>
            </div>
        </div>
        @endif
        @if($canServices)
        <div class="col-xl-3 col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <h3 class="mb-0">{{ $stats['services'] ?? 0 }}</h3>
                        <p class="mb-0 text-muted">{{ __('common.services') }}</p>
                    </div>
                    <i class="fas fa-cogs fa-2x text-info opacity-75"></i>
                </div>
            </div>
        </div>
        @endif
        @if($canPackages)
        <div class="col-xl-3 col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <h3 class="mb-0">{{ $stats['packages'] ?? 0 }}</h3>
                        <p class="mb-0 text-muted">{{ __('common.packages') }}</p>
                    </div>
                    <i class="fas fa-box fa-2x text-warning opacity-75"></i>
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- Second Row: Bookings -->
    <div class="row g-3 mb-4">
        @if($canBookings)
        <div class="col-xl-6 col-md-12">
            <div class="card shadow-sm h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <h3 class="mb-0">{{ $stats['bookings'] ?? 0 }}</h3>
                        <p class="mb-0 text-muted">{{ __('common.total_bookings') }}</p>
                        <small class="text-muted">{{ __('common.pending') }}: {{ $stats['pending_bookings'] ?? 0 }}</small>
                    </div>
                    <i class="fas fa-calendar-check fa-2x text-success opacity-75"></i>
                </div>
            </div>
        </div>
        @endif
        @if($canQuickSummary)
        <div class="col-xl-6 col-md-12">
            <div class="card shadow-sm h-100">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i>{{ __('common.quick_summary') }}</h5>
                </div>
                <div class="card-body">
                    @php
                        $summaryItems = [];
                        if ($canCustomers || $canAdmins) {
                            $summaryItems[] = [
                                'value' => $stats['total_users'] ?? 0,
                                'label' => __('common.total_users'),
                                'color' => 'text-primary',
                            ];
                        }
                        if ($canServices || $canPackages) {
                            $summaryItems[] = [
                                'value' => ($stats['services'] ?? 0) + ($stats['packages'] ?? 0),
                                'label' => __('common.total_products'),
                                'color' => 'text-info',
                            ];
                        }
                        if ($canBookings) {
                            $summaryItems[] = [
                                'value' => $stats['bookings'] ?? 0,
                                'label' => __('common.bookings'),
                                'color' => 'text-success',
                            ];
                        }

                        $summaryColClass = count($summaryItems) <= 1 ? 'col-12' : (count($summaryItems) === 2 ? 'col-6' : 'col-4');
                    @endphp
                    <div class="row text-center">
                        @foreach($summaryItems as $item)
                            <div class="{{ $summaryColClass }}">
                                <h4 class="{{ $item['color'] }}">{{ $item['value'] }}</h4>
                                <small class="text-muted">{{ $item['label'] }}</small>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- Quotes Statistics -->
    <div class="row g-3 mb-4">
        @if($canTraffic)
        <div class="col-xl-4 col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="card-title mb-1">{{ __('common.unique_visitors_today') }}</h6>
                        <h3 class="mb-0">{{ $stats['visits_today'] ?? 0 }}</h3>
                        <small class="text-muted">{{ __('common.last_7_days') }}: {{ $stats['visits_7d'] ?? 0 }} | {{ __('common.unique_visitors') }}: {{ $stats['unique_visitors_7d'] ?? 0 }}</small>
                    </div>
                    <i class="fas fa-users fa-2x text-primary opacity-75"></i>
                </div>
            </div>
        </div>
        @endif
        @if($canTraffic)
        <div class="col-xl-4 col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="card-title mb-1">{{ __('common.logins_today') }}</h6>
                        <h3 class="mb-0">{{ $stats['logins_today'] ?? 0 }}</h3>
                        <small class="text-muted">{{ __('common.last_7_days') }}: {{ $stats['logins_7d'] ?? 0 }}</small>
                    </div>
                    <i class="fas fa-sign-in-alt fa-2x text-success opacity-75"></i>
                </div>
            </div>
        </div>
        @endif
        @if($canTraffic)
        <div class="col-xl-4 col-md-12">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h6 class="card-title">{{ __('common.top_countries_7_days') }}</h6>
                    <ul class="list-unstyled mb-0">
                        @forelse (($stats['top_countries_7d'] ?? []) as $row)
                            <li class="d-flex justify-content-between">
                                <span>{{ $row->country }}</span>
                                <span class="text-muted">{{ $row->count }}</span>
                            </li>
                        @empty
                            <li class="text-muted">{{ __('common.no_sufficient_data') }}</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
        @endif
        @if($canQuotes)
        <div class="col-xl-3 col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <h3 class="mb-0">{{ $stats['quotes'] ?? 0 }}</h3>
                        <p class="mb-0 text-muted">{{ __('common.quotes') }}</p>
                    </div>
                    <i class="fas fa-file-invoice-dollar fa-2x text-primary opacity-75"></i>
                </div>
            </div>
        </div>
        @endif
        @if($canQuotes)
        <div class="col-xl-3 col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <h3 class="mb-0">{{ $stats['pending_quotes'] ?? 0 }}</h3>
                        <p class="mb-0 text-muted">{{ __('common.pending') }}</p>
                    </div>
                    <i class="fas fa-clock fa-2x text-warning opacity-75"></i>
                </div>
            </div>
        </div>
        @endif
        @if($canQuotes)
        <div class="col-xl-3 col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <h3 class="mb-0">{{ $stats['approved_quotes'] ?? 0 }}</h3>
                        <p class="mb-0 text-muted">{{ __('common.approved') }}</p>
                    </div>
                    <i class="fas fa-check-circle fa-2x text-success opacity-75"></i>
                </div>
            </div>
        </div>
        @endif
        @if($canQuotes)
        <div class="col-xl-3 col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <h3 class="mb-0">{{ $stats['rejected_quotes'] ?? 0 }}</h3>
                        <p class="mb-0 text-muted">{{ __('common.rejected') }}</p>
                    </div>
                    <i class="fas fa-times-circle fa-2x text-danger opacity-75"></i>
                </div>
            </div>
        </div>
        @endif
        @if($canQuotes)
        <div class="col-xl-3 col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <h3 class="mb-0">{{ $stats['completed_quotes'] ?? 0 }}</h3>
                        <p class="mb-0 text-muted">{{ __('common.completed') }}</p>
                    </div>
                    <i class="fas fa-check-double fa-2x text-info opacity-75"></i>
                </div>
            </div>
        </div>
        @endif
    </div>

    <div class="row g-3 mb-4">
        @if($canGallery)
        <div class="col-xl-6 col-md-12">
            <div class="card shadow-sm h-100">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="mb-0"><i class="fas fa-images me-2"></i>{{ __('common.gallery') }}</h5>
                    <a href="{{ route('admin.gallery.index') }}" class="btn btn-outline-info btn-sm">{{ __('common.manage_gallery') }}</a>
                </div>
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <h2 class="text-info mb-0">{{ $stats['gallery_items'] ?? 0 }}</h2>
                        <p class="text-muted mb-0">{{ __('common.photos_and_videos') }}</p>
                    </div>
                    <i class="fas fa-photo-video fa-2x text-info opacity-75"></i>
                </div>
            </div>
        </div>
        @endif
        @if($canReviews)
        <div class="col-xl-6 col-md-12">
            <div class="card shadow-sm h-100">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i>{{ __('common.additional_statistics') }}</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <h4 class="text-primary">{{ $stats['reviews'] ?? 0 }}</h4>
                            <p class="text-muted mb-2">{{ __('common.reviews') }}</p>
                        </div>
                        <div class="col-6">
                            <h4 class="text-warning">{{ $stats['pending_reviews'] ?? 0 }}</h4>
                            <p class="text-muted mb-2">{{ __('common.pending_reviews') }}</p>
                        </div>
                    </div>
                    <hr>
                    <div class="text-center">
                        <small class="text-muted">{{ __('common.last_updated') }}: {{ now()->format('d/m/Y H:i') }}</small>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- Email & OTP Statistics -->
    <div class="row g-3 mb-4">
        @if($canEmails)
        <div class="col-xl-4 col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="mb-0"><i class="fas fa-envelope-open-text me-2"></i>{{ __('common.email') }}</h5>
                    <a href="{{ route('admin.email-management.index') }}" class="btn btn-outline-primary btn-sm">{{ __('common.manage_email') }}</a>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <h4 class="mb-0">{{ $stats['email_templates_total'] ?? 0 }}</h4>
                            <small class="text-muted">{{ __('common.email_templates') }}</small>
                        </div>
                        <div>
                            <span class="badge bg-success">{{ __('common.active') }}: {{ $stats['email_templates_active'] ?? 0 }}</span>
                        </div>
                    </div>
                    <div class="text-muted small">{{ __('common.email_templates_manage_hint') }}</div>
                </div>
            </div>
        </div>
        @endif
        @if($canOtp)
        <div class="col-xl-4 col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-shield-alt me-2"></i>OTP</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-4">
                            <h4 class="text-primary mb-0">{{ $stats['otp_total'] ?? 0 }}</h4>
                            <small class="text-muted">{{ __('common.total') }}</small>
                        </div>
                        <div class="col-4">
                            <h4 class="text-success mb-0">{{ $stats['otp_verified'] ?? 0 }}</h4>
                            <small class="text-muted">{{ __('common.verified') }}</small>
                        </div>
                        <div class="col-4">
                            <h4 class="text-warning mb-0">{{ $stats['otp_pending'] ?? 0 }}</h4>
                            <small class="text-muted">{{ __('common.pending') }}</small>
                        </div>
                    </div>
                    <hr>
                    <div class="text-center">
                        <small class="text-muted">{{ __('common.success_rate') }}: {{ $stats['otp_success_rate'] ?? 0 }}%</small>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- Recent Quotes -->
    @if($canQuotes)
    <div class="row g-3 mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="mb-0"><i class="fas fa-file-invoice-dollar me-2"></i>{{ __('common.recent_quotes') }}</h5>
                    <a href="{{ route('admin.quotes.index') }}" class="btn btn-outline-secondary btn-sm">{{ __('common.view_all') }}</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('common.quote_number') }}</th>
                                    <th>{{ __('common.customer') }}</th>
                                    <th>{{ __('common.status') }}</th>
                                    <th>{{ __('common.total') }}</th>
                                    <th>{{ __('common.created_at') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recent_quotes as $quote)
                                    <tr>
                                        <td>{{ $quote->id }}</td>
                                        <td>{{ $quote->quote_number }}</td>
                                        <td>{{ optional($quote->user)->name ?? '-' }}</td>
                                        <td>
                                            <span class="badge bg-{{ $quote->status === 'pending' ? 'warning' : ($quote->status === 'approved' ? 'success' : ($quote->status === 'rejected' ? 'danger' : 'info')) }}">
                                                {{ $quote->status_text }}
                                            </span>
                                        </td>
                                        <td>{{ number_format($quote->total, 2) }}</td>
                                        <td>{{ $quote->created_at?->format('d/m/Y H:i') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">{{ __('common.no_recent_quotes') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Recent Bookings -->
    @if($canBookings)
    <div class="row g-3">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="mb-0"><i class="fas fa-history me-2"></i>{{ __('common.recent_bookings') }}</h5>
                    <a href="{{ route('admin.bookings.index') }}" class="btn btn-outline-secondary btn-sm">{{ __('common.view_all') }}</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('common.package') }}</th>
                                    <th>{{ __('common.service') }}</th>
                                    <th>{{ __('common.status') }}</th>
                                    <th>{{ __('common.created_at') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recent_bookings as $booking)
                                    <tr>
                                        <td>{{ $booking->id }}</td>
                                        <td>{{ optional($booking->package)->name ?? '-' }}</td>
                                        <td>{{ optional($booking->service)->name ?? '-' }}</td>
                                        <td>
                                            <span class="badge bg-{{ $booking->status === 'pending' ? 'warning' : ($booking->status === 'approved' ? 'success' : ($booking->status === 'rejected' ? 'danger' : 'info')) }}">
                                                {{ $booking->status }}
                                            </span>
                                        </td>
                                        <td>{{ $booking->created_at?->format('d/m/Y H:i') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">{{ __('common.no_recent_bookings') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var container = document.getElementById('adminDashboardAutoRefresh');
    if (!container) return;

    function refreshDashboard() {
        if (document.visibilityState !== 'visible') return;

        fetch(window.location.href, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            cache: 'no-store'
        })
        .then(response => response.text())
        .then(html => {
            var parser = new DOMParser();
            var doc = parser.parseFromString(html, 'text/html');
            var newContainer = doc.getElementById('adminDashboardAutoRefresh');
            if (newContainer) {
                container.innerHTML = newContainer.innerHTML;
            }
        })
        .catch(() => {});
    }

    setInterval(refreshDashboard, 10000); // كل 10 ثواني
});
</script>

{{-- Biometric Registration Prompt --}}
@if(session('admin_biometric_prompt'))
<script>
document.addEventListener('DOMContentLoaded', async function() {
    if (!window.PublicKeyCredential) return;
    try {
        const available = await PublicKeyCredential.isUserVerifyingPlatformAuthenticatorAvailable();
        if (!available) return;
    } catch(e) { return; }

    // Already registered on this device?
    if (localStorage.getItem('ye_admin_biometric_registered') === '1') return;

    // Show registration modal
    const modal = new bootstrap.Modal(document.getElementById('adminBiometricModal'));
    modal.show();
});

function base64ToArrayBuffer(base64) {
    const b64 = base64.replace(/-/g, '+').replace(/_/g, '/');
    const raw = atob(b64);
    const buf = new ArrayBuffer(raw.length);
    const view = new Uint8Array(buf);
    for (let i = 0; i < raw.length; i++) view[i] = raw.charCodeAt(i);
    return buf;
}
function arrayBufferToBase64(buffer) {
    const bytes = new Uint8Array(buffer);
    let binary = '';
    for (let i = 0; i < bytes.length; i++) binary += String.fromCharCode(bytes[i]);
    return btoa(binary);
}

async function registerAdminBiometric() {
    const btn = document.getElementById('adminBioRegBtn');
    const msg = document.getElementById('adminBioRegMsg');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>جارٍ التسجيل...';
    msg.style.display = 'none';

    try {
        // 1. Get registration options
        const optRes = await fetch('{{ route("biometric.register.options") }}', {
            method: 'POST',
            headers: {'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json'},
        });
        if (!optRes.ok) throw new Error((await optRes.json()).error || 'فشل في الحصول على خيارات التسجيل');
        const options = await optRes.json();

        // 2. Create credential
        const credential = await navigator.credentials.create({
            publicKey: {
                challenge: base64ToArrayBuffer(options.challenge),
                rp: options.rp,
                user: {
                    id: base64ToArrayBuffer(options.user.id),
                    name: options.user.name,
                    displayName: options.user.displayName,
                },
                pubKeyCredParams: options.pubKeyCredParams,
                authenticatorSelection: options.authenticatorSelection,
                timeout: options.timeout,
                attestation: options.attestation,
            }
        });

        // 3. Send to server
        const regRes = await fetch('{{ route("biometric.register") }}', {
            method: 'POST',
            headers: {'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json'},
            body: JSON.stringify({
                id: credential.id,
                rawId: arrayBufferToBase64(credential.rawId),
                response: {
                    clientDataJSON: arrayBufferToBase64(credential.response.clientDataJSON),
                    attestationObject: arrayBufferToBase64(credential.response.attestationObject),
                },
                type: credential.type,
                device_name: navigator.userAgent.match(/\(([^)]+)\)/)?.[1]?.split(';')[0] || navigator.platform,
            })
        });

        const result = await regRes.json();
        if (result.success) {
            localStorage.setItem('ye_admin_biometric_registered', '1');
            msg.className = 'alert alert-success small';
            msg.textContent = '✅ تم تسجيل البصمة بنجاح! يمكنك استخدامها للدخول مباشرة في المرات القادمة.';
            msg.style.display = 'block';
            btn.innerHTML = '<i class="fas fa-check me-2"></i>تم التسجيل';
            setTimeout(() => bootstrap.Modal.getInstance(document.getElementById('adminBiometricModal'))?.hide(), 2000);
        } else {
            throw new Error(result.error || 'فشل التسجيل');
        }
    } catch(err) {
        msg.className = 'alert alert-danger small';
        msg.textContent = err.name === 'NotAllowedError' ? 'تم إلغاء التسجيل' : ('خطأ: ' + err.message);
        msg.style.display = 'block';
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-fingerprint me-2"></i>نعم، فعّل البصمة';
    }
}
</script>

<!-- Biometric Registration Modal -->
<div class="modal fade" id="adminBiometricModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:15px;border:none;">
            <div class="modal-header border-0 pb-0" style="background:linear-gradient(135deg,#1f144a,#3b2d7a);border-radius:15px 15px 0 0;">
                <h5 class="modal-title text-white"><i class="fas fa-fingerprint me-2"></i>تفعيل الدخول بالبصمة</h5>
            </div>
            <div class="modal-body text-center py-4">
                <div style="font-size:60px;color:#1f144a;margin-bottom:15px;">
                    <i class="fas fa-fingerprint"></i>
                </div>
                <h5 class="fw-bold mb-2">هل تريد تفعيل الدخول السريع بالبصمة؟</h5>
                <p class="text-muted small">بعد التفعيل، يمكنك تسجيل الدخول لاحقاً بلمسة واحدة باستخدام بصمة الإصبع أو Face ID</p>
                <div id="adminBioRegMsg" class="mb-2" style="display:none;"></div>
                <button type="button" class="btn btn-primary w-100 mb-2" id="adminBioRegBtn" onclick="registerAdminBiometric()">
                    <i class="fas fa-fingerprint me-2"></i>نعم، فعّل البصمة
                </button>
                <button type="button" class="btn btn-light w-100" data-bs-dismiss="modal">
                    تخطي
                </button>
            </div>
        </div>
    </div>
</div>
@endif
@endpush

@extends('layouts.app')

@section('title', 'تسجيل دخول المورد')

@section('content')
<div class="min-vh-100 d-flex align-items-center" style="background: linear-gradient(135deg, #1f144a 0%, #2a1d5c 50%, #3d2a7a 100%);">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-5 col-md-7">
                <!-- Logo -->
                <div class="text-center mb-4">
                    <a href="{{ route('home') }}">
                        <img src="{{ asset('images/logo/logo-white.png') }}" alt="Your Events" style="max-width: 180px;" class="js-img-fallback" data-fallback-src="{{ asset('images/logo/logo.png') }}">
                    </a>
                </div>
                
                <div class="card border-0 shadow-lg" style="border-radius: 20px;">
                    <div class="card-body p-5">
                        <!-- Header -->
                        <div class="text-center mb-4">
                            <div class="mb-3">
                                <i class="fas fa-store" style="font-size: 3rem; color: #1f144a;"></i>
                            </div>
                            <h2 class="fw-bold text-dark mb-2">بوابة الموردين</h2>
                            <p class="text-muted">سجل دخولك للوصول إلى لوحة التحكم</p>
                        </div>
                        
                        @if(session('success'))
                            <div class="alert alert-success d-flex align-items-center" role="alert">
                                <i class="fas fa-check-circle me-2"></i>
                                <div>{{ session('success') }}</div>
                            </div>
                        @endif
                        
                        @if(session('error'))
                            <div class="alert alert-danger d-flex align-items-center" role="alert">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                <div>{{ session('error') }}</div>
                            </div>
                        @endif
                        
                        <form method="POST" action="{{ route('supplier.login.post') }}" id="supplierLoginForm">
                            @csrf
                            @if(config('services.recaptcha.site_key'))
                            <input type="hidden" name="recaptcha_token" id="recaptcha_token_supplier">
                            @endif
                            <input type="hidden" id="supplierRecaptchaSiteKey" value="{{ config('services.recaptcha.site_key') }}">
                            
                            <!-- Email -->
                            <div class="mb-4">
                                <label for="email" class="form-label fw-semibold">البريد الإلكتروني</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="fas fa-envelope text-muted"></i>
                                    </span>
                                    <input type="email" 
                                           class="form-control form-control-lg border-start-0 @error('email') is-invalid @enderror" 
                                           id="email" 
                                           name="email" 
                                           value="{{ old('email') }}" 
                                           placeholder="example@domain.com"
                                           required>
                                </div>
                                @error('email')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Password -->
                            <div class="mb-4">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label for="password" class="form-label fw-semibold">كلمة المرور</label>
                                    <a href="{{ route('supplier.forgot-password') }}" class="text-decoration-none small" style="color: #1f144a;">
                                        نسيت كلمة المرور؟
                                    </a>
                                </div>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="fas fa-lock text-muted"></i>
                                    </span>
                                    <input type="password" 
                                           class="form-control form-control-lg border-start-0 @error('password') is-invalid @enderror" 
                                           id="password" 
                                           name="password" 
                                           placeholder="••••••••"
                                           required>
                                    <button class="btn btn-light border border-start-0" type="button" onclick="togglePassword()">
                                        <i class="fas fa-eye text-muted" id="toggleIcon"></i>
                                    </button>
                                </div>
                                @error('password')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Remember Me -->
                            <div class="mb-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="remember">
                                        تذكرني
                                    </label>
                                </div>
                            </div>

                            @if(config('services.recaptcha.site_key'))
                            <div class="mb-4">
                                <div id="s-recaptcha-box" style="border:2px solid #e2e8f0;border-radius:10px;padding:14px 16px;display:flex;align-items:center;justify-content:space-between;background:#f8fafc;transition:border-color 0.3s,background 0.3s;">
                                    <div style="display:flex;align-items:center;gap:12px;">
                                        <div id="s-recaptcha-spinner" style="width:28px;height:28px;border:3px solid #e2e8f0;border-top-color:#1f144a;border-radius:50%;animation:rcSpin 0.9s linear infinite;flex-shrink:0;"></div>
                                        <div id="s-recaptcha-check" style="display:none;width:28px;height:28px;background:#34a853;border-radius:50%;align-items:center;justify-content:center;flex-shrink:0;">
                                            <i class="fas fa-check" style="color:white;font-size:13px;"></i>
                                        </div>
                                        <div>
                                            <div id="s-recaptcha-label" style="font-size:14px;color:#4a5568;font-weight:600;">جارٍ التحقق من هويتك...</div>
                                            <div id="s-recaptcha-sublabel" style="font-size:11px;color:#9ca3af;">لن تُزعج بأي صور أو اختبارات</div>
                                        </div>
                                    </div>
                                    <div style="display:flex;flex-direction:column;align-items:center;gap:2px;opacity:0.7;">
                                        <img src="https://www.gstatic.com/recaptcha/api2/logo_48.png" alt="reCAPTCHA" style="width:30px;height:30px;">
                                        <span style="font-size:9px;color:#9ca3af;font-family:sans-serif;">reCAPTCHA</span>
                                    </div>
                                </div>
                            </div>
                            <style>@keyframes rcSpin{to{transform:rotate(360deg)}}</style>
                            @endif

                            <!-- Submit -->
                            <button type="submit" class="btn btn-lg w-100 fw-bold text-white" id="supplierLoginBtn" style="background: linear-gradient(135deg, #1f144a, #3d2a7a); border-radius: 10px; padding: 14px;">
                                <i class="fas fa-sign-in-alt me-2"></i>تسجيل الدخول
                            </button>
                        </form>

                        {{-- زر البصمة --}}
                        <div class="mt-3" id="supplierBiometricSection" style="display:none;">
                            <button type="button" class="btn btn-lg w-100 fw-bold" id="supplierBiometricBtn"
                                    onclick="startSupplierBiometricAuth()"
                                    style="background:#f8f9fa; border:2px solid #1f144a; color:#1f144a; border-radius:10px; padding:14px;">
                                <i class="fas fa-fingerprint me-2" style="font-size:1.2rem;"></i>
                                الدخول بالبصمة / Face ID
                            </button>
                        </div>
                        <div id="supplierBioMsg" class="mt-2 text-center small" style="display:none;"></div>
                        
                        <!-- Register Link -->
                        <div class="text-center mt-4 pt-3 border-top">
                            <p class="text-muted mb-0">
                                ليس لديك حساب؟
                                <a href="{{ route('suppliers.register') }}" class="fw-semibold text-decoration-none" style="color: #1f144a;">
                                    سجل كمورد الآن
                                </a>
                            </p>
                        </div>
                    </div>
                </div>
                
                <!-- Back to Home -->
                <div class="text-center mt-4">
                    <a href="{{ route('home') }}" class="text-white text-decoration-none">
                        <i class="fas fa-arrow-right me-1"></i>
                        العودة للصفحة الرئيسية
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@if(config('services.recaptcha.site_key'))
{{-- يجب تحميل reCAPTCHA قبل كود JS --}}
<script src="https://www.google.com/recaptcha/api.js?render={{ config('services.recaptcha.site_key') }}"></script>
@endif
<script>
document.querySelectorAll('img.js-img-fallback').forEach(function(img) {
    img.addEventListener('error', function() {
        const fallback = img.getAttribute('data-fallback-src');
        if (fallback && img.getAttribute('src') !== fallback) {
            img.setAttribute('src', fallback);
        }
    }, { once: true });
});

function togglePassword() {
    const passwordInput = document.getElementById('password');
    const toggleIcon = document.getElementById('toggleIcon');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleIcon.classList.remove('fa-eye');
        toggleIcon.classList.add('fa-eye-slash');
    } else {
        passwordInput.type = 'password';
        toggleIcon.classList.remove('fa-eye-slash');
        toggleIcon.classList.add('fa-eye');
    }
}

// ─── reCAPTCHA v3 ─────────────────────────────────────────────────────────
const RECAPTCHA_SITE_KEY_SUPPLIER = document.getElementById('supplierRecaptchaSiteKey')?.value || '';
if (RECAPTCHA_SITE_KEY_SUPPLIER) {

// تعطيل الزر حتى يتحمل reCAPTCHA
const _sLoginBtn = document.getElementById('supplierLoginBtn');
_sLoginBtn.disabled = true;
_sLoginBtn.title = 'جارٍ تحميل التحقق الأمني...';

let _sRcReady = false;

function _sRcMarkReady() {
    if (_sRcReady) return;
    _sRcReady = true;
    const box     = document.getElementById('s-recaptcha-box');
    const spinner = document.getElementById('s-recaptcha-spinner');
    const check   = document.getElementById('s-recaptcha-check');
    const label   = document.getElementById('s-recaptcha-label');
    const sub     = document.getElementById('s-recaptcha-sublabel');
    if (spinner) spinner.style.display = 'none';
    if (check)   { check.style.display = 'flex'; }
    if (label)   label.textContent = 'تم التحقق من هويتك ✓';
    if (sub)     sub.textContent   = 'أنت لست روبوتاً';
    if (box)     { box.style.borderColor = '#34a853'; box.style.background = '#f0fdf4'; }
    _sLoginBtn.disabled = false;
    _sLoginBtn.title = '';
}

function _sRcMarkFailed() {
    if (_sRcReady) return;
    _sRcReady = true;
    const box     = document.getElementById('s-recaptcha-box');
    const spinner = document.getElementById('s-recaptcha-spinner');
    const label   = document.getElementById('s-recaptcha-label');
    const sub     = document.getElementById('s-recaptcha-sublabel');
    if (spinner) spinner.style.display = 'none';
    if (label)   label.textContent = 'تعذّر تحميل التحقق الأمني';
    if (sub)     { sub.textContent = 'تحقق من اتصال الإنترنت أو أعد تحميل الصفحة'; sub.style.color = '#ef4444'; }
    if (box)     { box.style.borderColor = '#ef4444'; box.style.background = '#fef2f2'; }
    _sLoginBtn.disabled = false;
    _sLoginBtn.title = '';
}

// انتظر حتى 15 ثانية بالـ polling
var _sRcPollCount = 0;
var _sRcPollTimer = setInterval(function() {
    _sRcPollCount++;
    if (typeof grecaptcha !== 'undefined' && typeof grecaptcha.ready === 'function') {
        clearInterval(_sRcPollTimer);
        grecaptcha.ready(function() { _sRcMarkReady(); });
    } else if (_sRcPollCount >= 30) { // 30 × 500ms = 15s
        clearInterval(_sRcPollTimer);
        _sRcMarkFailed();
    }
}, 500);

document.getElementById('supplierLoginForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const form = this;
    const btn  = document.getElementById('supplierLoginBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>جارٍ التحقق...';

    grecaptcha.ready(function() {
        grecaptcha.execute(RECAPTCHA_SITE_KEY_SUPPLIER, {action: 'supplier_login'}).then(function(token) {
            document.getElementById('recaptcha_token_supplier').value = token;
            form.submit();
        }).catch(function(err) {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-sign-in-alt me-2"></i>تسجيل الدخول';
            alert('فشل التحقق الأمني، أعد المحاولة.');
        });
    });
});
}

// ─── Biometric ────────────────────────────────────────────────────────────
function _yeSupplierHasCredentials() {
    const email = document.getElementById('email')?.value?.trim() || '';
    const password = document.getElementById('password')?.value?.trim() || '';
    return Boolean(email && password);
}

function _yeUpdateSupplierBiometricVisibility() {
    const section = document.getElementById('supplierBiometricSection');
    const btn = document.getElementById('supplierBiometricBtn');
    if (!section || !btn) return;
    const hasBiometric = localStorage.getItem('ye_supplier_biometric_registered') === '1';
    section.style.display = hasBiometric ? 'block' : 'none';
    btn.disabled = !(_yeSupplierHasCredentials());
}

if (window.PublicKeyCredential) {
    PublicKeyCredential.isUserVerifyingPlatformAuthenticatorAvailable().then(available => {
        if (available) {
            _yeUpdateSupplierBiometricVisibility();
        }
    });
}

document.getElementById('email')?.addEventListener('input', _yeUpdateSupplierBiometricVisibility);
document.getElementById('password')?.addEventListener('input', _yeUpdateSupplierBiometricVisibility);

async function startSupplierBiometricAuth() {
    const email = document.getElementById('email').value.trim();
    const password = document.getElementById('password').value.trim();
    if (!email || !password) {
        showSupplierBioMsg('يرجى إدخال البريد الإلكتروني وكلمة المرور أولاً', 'danger');
        return;
    }

    const btn = document.getElementById('supplierBiometricBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>جارٍ التحقق...';

    try {
        const preRes = await fetch('{{ route("biometric.precheck") }}', {
            method: 'POST',
            headers: {'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json'},
            body: JSON.stringify({email, password, user_type: 'supplier'})
        });

        if (!preRes.ok) {
            const err = await preRes.json();
            showSupplierBioMsg(err.error || 'بيانات الدخول غير صحيحة.', 'danger');
            resetSupplierBiometricBtn();
            return;
        }

        const optRes = await fetch('{{ route("biometric.auth.options") }}', {
            method: 'POST',
            headers: {'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json'},
            body: JSON.stringify({email, user_type: 'supplier'})
        });

        if (!optRes.ok) {
            const err = await optRes.json();
            showSupplierBioMsg(err.error || 'لا توجد بصمة مسجّلة لهذا الحساب', 'warning');
            resetSupplierBiometricBtn();
            return;
        }

        const options = await optRes.json();
        const challenge = base64ToArrayBuffer(options.challenge);
        const allowCredentials = options.allowCredentials.map(c => ({
            type: c.type,
            id: base64ToArrayBuffer(c.id),
        }));

        const credential = await navigator.credentials.get({
            publicKey: { challenge, rpId: options.rpId, allowCredentials, userVerification: 'preferred', timeout: 60000 }
        });

        const authRes = await fetch('{{ route("biometric.authenticate") }}', {
            method: 'POST',
            headers: {'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json'},
            body: JSON.stringify({
                id: credential.id,
                rawId: arrayBufferToBase64(credential.rawId),
                response: {
                    clientDataJSON: arrayBufferToBase64(credential.response.clientDataJSON),
                    authenticatorData: arrayBufferToBase64(credential.response.authenticatorData),
                    signature: arrayBufferToBase64(credential.response.signature),
                    userHandle: credential.response.userHandle ? arrayBufferToBase64(credential.response.userHandle) : null,
                },
                type: credential.type,
            })
        });

        const result = await authRes.json();
        if (result.success) {
            showSupplierBioMsg('✅ تم التحقق! جارٍ التوجيه...', 'success');
            setTimeout(() => window.location.href = result.redirect, 500);
        } else {
            showSupplierBioMsg(result.error || 'فشل التحقق', 'danger');
            resetSupplierBiometricBtn();
        }
    } catch (err) {
        if (err.name === 'NotAllowedError') {
            showSupplierBioMsg('تم إلغاء التحقق بالبصمة', 'warning');
        } else {
            showSupplierBioMsg('خطأ: ' + err.message, 'danger');
        }
        resetSupplierBiometricBtn();
    }
}

function resetSupplierBiometricBtn() {
    const btn = document.getElementById('supplierBiometricBtn');
    btn.disabled = false;
    btn.innerHTML = '<i class="fas fa-fingerprint me-2" style="font-size:1.2rem;"></i>الدخول بالبصمة / Face ID';
}

function showSupplierBioMsg(msg, type) {
    const el = document.getElementById('supplierBioMsg');
    el.className = 'mt-2 text-center small text-' + type;
    el.textContent = msg;
    el.style.display = 'block';
}

function base64ToArrayBuffer(base64) {
    const b64 = base64.replace(/-/g, '+').replace(/_/g, '/');
    const raw = atob(b64);
    const buf = new ArrayBuffer(raw.length);
    const view = new Uint8Array(buf);
    for (let i = 0; i < raw.length; i++) view[i] = raw.charCodeAt(i);
    return buf;
}

function arrayBufferToBase64(buf) {
    const bytes = new Uint8Array(buf);
    let str = '';
    for (let i = 0; i < bytes.byteLength; i++) str += String.fromCharCode(bytes[i]);
    return btoa(str);
}
</script>
@endsection

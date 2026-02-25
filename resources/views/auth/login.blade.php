@extends('layouts.app')

@section('title', __('auth.login_title'))

@section('content')
<style>
    .auth-section {
        min-height: 100vh;
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        position: relative;
        overflow: hidden;
        padding-top: 80px; /* Add padding to account for fixed navbar */
    }

    /* Fix Navbar on Login Page to match Home Page search box contrast */
    .navbar {
        background: linear-gradient(135deg, #1f1449 0%, #2d1a5e 100%);
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    }
    
    /* Animated Wave Background */
    .auth-section::before {
        content: '';
        position: absolute;
        width: 200%;
        height: 200%;
        top: -50%;
        left: -50%;
        background: 
            radial-gradient(circle at 20% 50%, rgba(31, 20, 74, 0.08) 0%, transparent 50%),
            radial-gradient(circle at 80% 50%, rgba(239, 72, 112, 0.08) 0%, transparent 50%),
            radial-gradient(circle at 40% 80%, rgba(45, 188, 174, 0.08) 0%, transparent 50%);
        animation: wave 15s ease-in-out infinite;
        z-index: 0;
    }
    
    .auth-section::after {
        content: '';
        position: absolute;
        width: 200%;
        height: 200%;
        bottom: -50%;
        right: -50%;
        background: 
            radial-gradient(circle at 60% 40%, rgba(240, 199, 29, 0.06) 0%, transparent 50%),
            radial-gradient(circle at 30% 70%, rgba(102, 126, 234, 0.06) 0%, transparent 50%);
        animation: wave-reverse 20s ease-in-out infinite;
        z-index: 0;
    }
    
    @keyframes wave {
        0%, 100% { transform: translate(0, 0) rotate(0deg); }
        25% { transform: translate(5%, 5%) rotate(1deg); }
        50% { transform: translate(10%, 0) rotate(0deg); }
        75% { transform: translate(5%, -5%) rotate(-1deg); }
    }
    
    @keyframes wave-reverse {
        0%, 100% { transform: translate(0, 0) rotate(0deg); }
        25% { transform: translate(-5%, -5%) rotate(-1deg); }
        50% { transform: translate(-10%, 0) rotate(0deg); }
        75% { transform: translate(-5%, 5%) rotate(1deg); }
    }
    
    .floating-icon {
        position: absolute;
        font-size: 70px;
        z-index: 1;
        animation: float-icon 8s ease-in-out infinite;
        filter: drop-shadow(0 4px 8px rgba(0,0,0,0.1));
    }
    
    .floating-icon:nth-child(1) {
        top: 10%;
        left: 5%;
        animation-delay: 0s;
        color: #1f1449;
        opacity: 0.15;
    }
    
    .floating-icon:nth-child(2) {
        top: 20%;
        right: 8%;
        animation-delay: 2s;
        color: #ef4870;
        opacity: 0.15;
    }
    
    .floating-icon:nth-child(3) {
        bottom: 15%;
        left: 10%;
        animation-delay: 4s;
        color: #2dbcae;
        opacity: 0.15;
    }
    
    .floating-icon:nth-child(4) {
        bottom: 20%;
        right: 5%;
        animation-delay: 1s;
        color: #f0c71d;
        opacity: 0.15;
    }
    
    .floating-icon:nth-child(5) {
        top: 50%;
        left: 15%;
        animation-delay: 3s;
        color: #667eea;
        opacity: 0.12;
        font-size: 50px;
    }
    
    .floating-icon:nth-child(6) {
        top: 60%;
        right: 12%;
        animation-delay: 5s;
        color: #1f1449;
        opacity: 0.12;
        font-size: 55px;
    }
    
    @keyframes float-icon {
        0%, 100% { transform: translateY(0px) rotate(0deg) scale(1); }
        25% { transform: translateY(-20px) rotate(5deg) scale(1.05); }
        50% { transform: translateY(-40px) rotate(0deg) scale(1); }
        75% { transform: translateY(-20px) rotate(-5deg) scale(1.05); }
    }
    
    .auth-container {
        position: relative;
        z-index: 1;
    }
    
    .auth-card {
        border: none;
        border-radius: 25px;
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        box-shadow: 0 20px 60px rgba(31, 20, 74, 0.15);
        transition: all 0.4s ease;
        border: 2px solid rgba(255, 255, 255, 0.8);
    }
    
    .auth-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 30px 80px rgba(31, 20, 74, 0.25);
    }
    
    .auth-header {
        background: linear-gradient(135deg, #1f1449 0%, #2d1a5e 100%);
        padding: 45px 35px;
        border-radius: 25px 25px 0 0;
        text-align: center;
        position: relative;
        overflow: hidden;
    }
    
    .auth-header::before {
        content: '';
        position: absolute;
        width: 200px;
        height: 200px;
        background: radial-gradient(circle, rgba(239, 72, 112, 0.1) 0%, transparent 70%);
        top: -100px;
        left: -50px;
        animation: pulse-glow 4s ease-in-out infinite;
    }
    
    .auth-header::after {
        content: '';
        position: absolute;
        width: 200px;
        height: 200px;
        background: radial-gradient(circle, rgba(45, 188, 174, 0.1) 0%, transparent 70%);
        top: -100px;
        right: -50px;
        animation: pulse-glow 4s ease-in-out infinite 2s;
    }
    
    @keyframes pulse-glow {
        0%, 100% { transform: scale(1); opacity: 0.5; }
        50% { transform: scale(1.2); opacity: 0.8; }
    }
    
    .auth-header h1 {
        font-size: 32px;
        font-weight: 800;
        margin-bottom: 12px;
        color: #ffffff;
        position: relative;
        z-index: 1;
    }
    
    .auth-header p {
        font-size: 16px;
        color: rgba(255, 255, 255, 0.9);
        margin: 0;
        position: relative;
        z-index: 1;
        font-weight: 500;
    }
    
    .input-group-text {
        background: #ffffff;
        border: 2px solid #e2e8f0;
        color: #667eea;
        padding: 0 15px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        font-weight: 600;
        font-size: 1.1rem;
    }
    
    .input-group:focus-within .input-group-text {
        border-color: #667eea;
        background: #f7fafc;
        color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }
    
    .form-control {
        border: 2px solid #e2e8f0;
        padding: 13px 15px;
        font-size: 15px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        background: #ffffff;
    }
    
    .form-control:focus {
        border-color: #ef4870;
        box-shadow: 0 0 0 3px rgba(239, 72, 112, 0.1);
        background: white;
    }
    
    .form-label {
        font-weight: 700;
        color: #1f1449;
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 15px;
    }
    
    .btn-primary {
        background: linear-gradient(135deg, #1f1449 0%, #ef4870 50%, #2dbcae 100%);
        background-size: 200% 200%;
        border: none;
        padding: 15px 35px;
        font-weight: 800;
        border-radius: 15px;
        transition: all 0.4s ease;
        box-shadow: 0 8px 25px rgba(31, 20, 74, 0.3);
        position: relative;
        overflow: hidden;
        animation: gradient-shift 3s ease infinite;
    }
    
    @keyframes gradient-shift {
        0%, 100% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
    }
    
    .btn-primary::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
        transition: left 0.5s ease;
    }
    
    .btn-primary:hover {
        transform: translateY(-3px) scale(1.02);
        box-shadow: 0 12px 35px rgba(239, 72, 112, 0.4);
    }
    
    .btn-primary:hover::before {
        left: 100%;
    }
    
    .btn-outline-primary {
        color: #ef4870;
        border: 2px solid #ef4870;
        padding: 12px 24px;
        font-weight: 700;
        border-radius: 12px;
        background: rgba(239, 72, 112, 0.05);
        transition: all 0.3s ease;
    }
    
    .btn-outline-primary:hover {
        background: linear-gradient(135deg, #ef4870 0%, #2dbcae 100%);
        border-color: transparent;
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(239, 72, 112, 0.3);
    }
    
    .form-check-input {
        border: 2px solid rgba(31, 20, 74, 0.2);
        width: 22px;
        height: 22px;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .form-check-input:checked {
        background: linear-gradient(135deg, #ef4870 0%, #2dbcae 100%);
        border-color: #ef4870;
    }
    
    .form-check-input:focus {
        box-shadow: 0 0 0 3px rgba(239, 72, 112, 0.1);
    }
    
    .divider {
        position: relative;
        margin: 30px 0;
        text-align: center;
        color: #666;
        font-weight: 600;
    }
    
    .divider::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 0;
        right: 0;
        height: 2px;
        background: linear-gradient(90deg, transparent, rgba(31, 20, 74, 0.1), transparent);
    }
    
    .divider span {
        position: relative;
        background: white;
        padding: 0 15px;
    }
    
    .text-link {
        color: #ef4870;
        text-decoration: none;
        font-weight: 700;
        position: relative;
        transition: all 0.3s ease;
    }
    
    .text-link::after {
        content: '';
        position: absolute;
        bottom: -2px;
        left: 0;
        width: 0;
        height: 2px;
        background: linear-gradient(90deg, #ef4870, #2dbcae);
        transition: width 0.3s ease;
    }
    
    .text-link:hover {
        color: #2dbcae;
    }
    
    .text-link:hover::after {
        width: 100%;
    }
    
    .back-link {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: #1f1449;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .back-link:hover {
        gap: 12px;
        color: #000000ff;
    }
</style>

<section class="auth-section py-5">
    <div class="container auth-container">
        <div class="row justify-content-center">
            <div class="col-lg-5 col-md-7">
                <div class="card auth-card" data-aos="fade-up">
                    <div class="auth-header">
                        <h1>{{ __('auth.welcome') }}</h1>
                        <p>{{ __('auth.welcome_message') }}</p>
                    </div>
                    
                    <div class="card-body p-5">
                        @if($errors->any())
                            <div class="alert alert-danger border-0 rounded-3" style="background-color: #ffe0e0;">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('login') }}" id="loginForm">
                            @csrf
                            @if(config('services.recaptcha.site_key'))
                            <input type="hidden" name="recaptcha_token" id="recaptcha_token">
                            @endif
                            
                            <div class="mb-4">
                                <label for="email" class="form-label">
                                    {{ __('auth.email') }}
                                </label>
                                <div class="input-group">
                                    <input type="email" class="form-control" name="email" id="email" 
                                           value="{{ old('email') }}" placeholder="hello@yourevents.sa" required autofocus>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="password" class="form-label">
                                    {{ __('auth.password') }}
                                </label>
                                <div class="input-group">
                                    <input type="password" class="form-control" name="password" id="password" placeholder="xxxxxxxxxxxxxxxx" required>
                                </div>
                            </div>

                            <div class="mb-4 form-check">
                                <input type="checkbox" class="form-check-input" name="remember" id="remember">
                                <label class="form-check-label" for="remember">
                                    {{ __('auth.remember_me') }}
                                </label>
                            </div>

                            @if(config('services.recaptcha.site_key'))
                            <div class="mb-4">
                                <div id="recaptcha-box" style="border:2px solid #e2e8f0;border-radius:12px;padding:14px 16px;display:flex;align-items:center;justify-content:space-between;background:#f8fafc;transition:border-color 0.3s,background 0.3s;">
                                    <div style="display:flex;align-items:center;gap:12px;">
                                        <div id="recaptcha-spinner" style="width:28px;height:28px;border:3px solid #e2e8f0;border-top-color:#4285f4;border-radius:50%;animation:rcSpin 0.9s linear infinite;flex-shrink:0;"></div>
                                        <div id="recaptcha-check" style="display:none;width:28px;height:28px;background:#34a853;border-radius:50%;align-items:center;justify-content:center;flex-shrink:0;">
                                            <i class="fas fa-check" style="color:white;font-size:13px;"></i>
                                        </div>
                                        <div>
                                            <div id="recaptcha-label" style="font-size:14px;color:#4a5568;font-weight:600;">جارٍ التحقق من هويتك...</div>
                                            <div id="recaptcha-sublabel" style="font-size:11px;color:#9ca3af;">لن تُزعج بأي صور أو اختبارات</div>
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

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg" id="loginBtn">
                                    {{ __('auth.login_button') }}
                                </button>
                            </div>
                        </form>

                        {{-- زر البصمة --}}
                        <div class="mt-3 d-grid" id="biometricSection" style="display:none!important;">
                            <button type="button" class="btn btn-outline-secondary btn-lg" id="biometricBtn"
                                    onclick="startBiometricAuth()"
                                    style="border-radius:15px; border:2px solid #1f1449; color:#1f1449; font-weight:700;">
                                <i class="fas fa-fingerprint me-2" style="font-size:1.2rem;"></i>
                                الدخول بالبصمة / Face ID
                            </button>
                        </div>
                        <div id="biometricMsg" class="mt-2 text-center small text-danger" style="display:none;"></div>

                        <div class="divider"><span>{{ __('auth.or') }}</span></div>

                        <div class="text-center mb-4">
                            <p class="mb-3" style="font-size: 15px;">
                                {{ __('auth.no_account') }}
                            </p>
                            <a href="{{ route('register') }}" class="btn btn-outline-primary w-100">
                                {{ __('auth.lets_start') }}
                            </a>
                        </div>

                        <div class="text-center mb-3">
                            <a href="{{ route('password.request') }}" class="text-link">
                                {{ __('auth.forgot_password') }}
                            </a>
                        </div>

                        <p class="text-muted small text-center" style="font-size: 13px; margin: 0;">
                            {{ __('auth.terms_agreement') }} <a href="{{ route('terms') }}" class="text-link">{{ __('auth.terms_of_use') }}</a>
                        </p>
                    </div>
                </div>

                <div class="text-center mt-4">
                    <a href="{{ route('home') }}" class="back-link">
                        {{ __('auth.back_home') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

@push('scripts')
@if(config('services.recaptcha.site_key'))
{{-- تحميل reCAPTCHA بدون async/defer حتى يكون جاهزاً قبل أي تفاعل --}}
<script src="https://www.google.com/recaptcha/api.js?render={{ config('services.recaptcha.site_key') }}"></script>
@endif
@if(config('services.recaptcha.site_key'))
<script>
// ─── reCAPTCHA v3 ─────────────────────────────────────────────────────────
const RECAPTCHA_SITE_KEY = '{{ config("services.recaptcha.site_key") }}';

// تعطيل الزر حتى يتحمل reCAPTCHA
const _loginBtn = document.getElementById('loginBtn');
_loginBtn.disabled = true;
_loginBtn.title = 'جارٍ تحميل التحقق الأمني...';

let _rcReady = false;

function _rcMarkReady() {
    if (_rcReady) return;
    _rcReady = true;
    const box     = document.getElementById('recaptcha-box');
    const spinner = document.getElementById('recaptcha-spinner');
    const check   = document.getElementById('recaptcha-check');
    const label   = document.getElementById('recaptcha-label');
    const sub     = document.getElementById('recaptcha-sublabel');
    if (spinner) spinner.style.display = 'none';
    if (check)   { check.style.display = 'flex'; }
    if (label)   label.textContent = 'تم التحقق من هويتك ✓';
    if (sub)     sub.textContent   = 'أنت لست روبوتاً';
    if (box)     { box.style.borderColor = '#34a853'; box.style.background = '#f0fdf4'; }
    _loginBtn.disabled = false;
    _loginBtn.title = '';
}

function _rcMarkFailed() {
    if (_rcReady) return;
    _rcReady = true;
    const box     = document.getElementById('recaptcha-box');
    const spinner = document.getElementById('recaptcha-spinner');
    const label   = document.getElementById('recaptcha-label');
    const sub     = document.getElementById('recaptcha-sublabel');
    if (spinner) spinner.style.display = 'none';
    if (label)   label.textContent = 'تعذّر تحميل التحقق الأمني';
    if (sub)     { sub.textContent = 'تحقق من اتصال الإنترنت أو أعد تحميل الصفحة'; sub.style.color = '#ef4444'; }
    if (box)     { box.style.borderColor = '#ef4444'; box.style.background = '#fef2f2'; }
    _loginBtn.disabled = false;
    _loginBtn.title = '';
}

// انتظر حتى 15 ثانية بالـ polling
var _rcPollCount = 0;
var _rcPollTimer = setInterval(function() {
    _rcPollCount++;
    if (typeof grecaptcha !== 'undefined' && typeof grecaptcha.ready === 'function') {
        clearInterval(_rcPollTimer);
        grecaptcha.ready(function() { _rcMarkReady(); });
    } else if (_rcPollCount >= 30) { // 30 × 500ms = 15s
        clearInterval(_rcPollTimer);
        _rcMarkFailed();
    }
}, 500);

document.getElementById('loginForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const form = this;
    const btn  = document.getElementById('loginBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>جارٍ التحقق...';

    grecaptcha.ready(function() {
        grecaptcha.execute(RECAPTCHA_SITE_KEY, {action: 'login'}).then(function(token) {
            document.getElementById('recaptcha_token').value = token;
            form.submit();
        }).catch(function(err) {
            btn.disabled = false;
            btn.innerHTML = '{{ __("auth.login_button") }}';
            alert('فشل التحقق الأمني، أعد المحاولة.');
        });
    });
});
</script>
@endif

<script>
// ─── Biometric / WebAuthn ─────────────────────────────────────────────────
// نتحقق أن المتصفح يدعم WebAuthn
if (window.PublicKeyCredential) {
    PublicKeyCredential.isUserVerifyingPlatformAuthenticatorAvailable().then(available => {
        if (available) {
            // نتحقق من localStorage: هل يوجد passkey مسجّل لهذا المتصفح؟
            const hasBiometric = localStorage.getItem('ye_biometric_registered');
            if (hasBiometric === '1') {
                document.getElementById('biometricSection').style.display = 'grid';
            }
        }
    });
}

// عند تغيير البريد الإلكتروني، أُظهر/أُخفي زر البصمة
document.getElementById('email')?.addEventListener('blur', function() {
    const hasBiometric = localStorage.getItem('ye_biometric_registered');
    if (hasBiometric === '1' && this.value) {
        document.getElementById('biometricSection').style.display = 'grid';
    }
});

async function startBiometricAuth() {
    const email = document.getElementById('email').value.trim();
    if (!email) {
        showBioMsg('يرجى إدخال البريد الإلكتروني أولاً', 'danger');
        return;
    }

    const btn = document.getElementById('biometricBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>جارٍ التحقق...';

    try {
        // 1. احصل على challenge من الخادم
        const optRes = await fetch('{{ route("biometric.auth.options") }}', {
            method: 'POST',
            headers: {'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json'},
            body: JSON.stringify({email, user_type: 'user'})
        });

        if (!optRes.ok) {
            const err = await optRes.json();
            showBioMsg(err.error || 'لا توجد بصمة مسجّلة لهذا الحساب', 'warning');
            resetBiometricBtn();
            return;
        }

        const options = await optRes.json();

        // 2. تحويل base64 → ArrayBuffer
        const challenge = base64ToArrayBuffer(options.challenge);
        const allowCredentials = options.allowCredentials.map(c => ({
            type: c.type,
            id: base64ToArrayBuffer(c.id),
        }));

        // 3. استدعاء مصادق الجهاز
        const credential = await navigator.credentials.get({
            publicKey: { challenge, rpId: options.rpId, allowCredentials, userVerification: 'preferred', timeout: 60000 }
        });

        // 4. إرسال النتيجة للخادم
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
            showBioMsg('✅ تم التحقق! جارٍ التوجيه...', 'success');
            setTimeout(() => window.location.href = result.redirect, 500);
        } else {
            showBioMsg(result.error || 'فشل التحقق', 'danger');
            resetBiometricBtn();
        }
    } catch (err) {
        if (err.name === 'NotAllowedError') {
            showBioMsg('تم إلغاء التحقق بالبصمة', 'warning');
        } else {
            showBioMsg('خطأ: ' + err.message, 'danger');
        }
        resetBiometricBtn();
    }
}

function resetBiometricBtn() {
    const btn = document.getElementById('biometricBtn');
    btn.disabled = false;
    btn.innerHTML = '<i class="fas fa-fingerprint me-2" style="font-size:1.2rem;"></i>الدخول بالبصمة / Face ID';
}

function showBioMsg(msg, type) {
    const el = document.getElementById('biometricMsg');
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
@endpush

@endsection

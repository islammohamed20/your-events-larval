<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('تسجيل دخول المسؤول') }} - Your Events</title>
    
    <!-- Favicon -->
    @php
        $faviconSetting = \App\Models\Setting::get('favicon') ?: \App\Models\Setting::get('site_favicon');
        $faviconUrlSetting = \App\Models\Setting::get('favicon_url');
        $fallbackFaviconUrl = asset('images/logo/logo.png');
        $faviconUrl = $faviconSetting
            ? (filter_var($faviconSetting, FILTER_VALIDATE_URL) ? $faviconSetting : url(Illuminate\Support\Facades\Storage::url($faviconSetting)))
            : ($faviconUrlSetting ? (filter_var($faviconUrlSetting, FILTER_VALIDATE_URL) ? $faviconUrlSetting : url($faviconUrlSetting)) : $fallbackFaviconUrl);
        $faviconPath = parse_url($faviconUrl, PHP_URL_PATH);
        $faviconExt = strtolower(pathinfo($faviconPath ?? $faviconUrl, PATHINFO_EXTENSION));
        $faviconType = $faviconExt === 'ico' ? 'image/x-icon' : 'image/png';
    @endphp
    <link rel="icon" type="{{ $faviconType }}" href="{{ $faviconUrl }}">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@200;300;400;500;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #1f144a;
            --accent-color: #ef4870;
            --secondary-color: #2dbcae;
            --gold-color: #f0c71d;
            --text-color: #222222;
        }
        body {
            font-family: 'Tajawal', sans-serif;
            background-color: #121212;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background-image: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
        }
        .login-card {
            background: #1f1f1f;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
            overflow: hidden;
            width: 100%;
            max-width: 450px;
            padding: 2rem;
            color: #ffffff;
            border: 1px solid #333;
        }
        .login-logo {
            max-width: 150px;
            height: auto;
            margin-bottom: 1rem;
            padding: 0;
            display: block;
            margin-left: auto;
            margin-right: auto;
        }
        .login-header h4 {
            color: var(--secondary-color) !important;
            margin-top: 1rem;
        }
        .text-muted {
            color: #aaaaaa !important;
        }
        .form-control {
            padding: 0.75rem 1rem;
            border-radius: 8px;
            border: 1px solid #444;
            background-color: #2c2c2c;
            color: #fff;
        }
        .form-control:focus {
            box-shadow: 0 0 0 3px rgba(45, 188, 174, 0.2);
            border-color: var(--secondary-color);
            background-color: #2c2c2c;
            color: #fff;
        }
        .input-group-text {
            background: #2c2c2c;
            border: 1px solid #444;
            border-left: none;
            border-radius: 0 8px 8px 0;
            color: #aaaaaa;
        }
        /* RTL Fixes for dark inputs */
        .input-group > .form-control {
            border-radius: 8px 0 0 8px !important;
            border-right: 0 !important;
            border-left: 1px solid #444 !important;
        }
        .input-group > .input-group-text {
            border-radius: 0 8px 8px 0 !important;
            border-left: 0 !important;
            border-right: 1px solid #444 !important;
        }
        .btn-primary {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
            padding: 0.75rem;
            border-radius: 8px;
            font-weight: 600;
            width: 100%;
            transition: all 0.3s;
            color: #1f1f1f;
        }
        .btn-primary:hover {
            background-color: #249e92;
            border-color: #249e92;
            transform: translateY(-2px);
            color: #fff;
        }
    </style>
</head>
<body>

    <div class="login-card">
        <div class="login-header">
            @php
                $logoSetting = \App\Models\Setting::get('site_logo') ?: \App\Models\Setting::get('logo');
                $logoUrlSetting = \App\Models\Setting::get('logo_url');
                $fallbackLogoUrl = asset('images/logo/logo.png');
                $logoUrl = $logoSetting
                    ? (filter_var($logoSetting, FILTER_VALIDATE_URL) ? $logoSetting : url(Illuminate\Support\Facades\Storage::url($logoSetting)))
                    : ($logoUrlSetting ? (filter_var($logoUrlSetting, FILTER_VALIDATE_URL) ? $logoUrlSetting : url($logoUrlSetting)) : $fallbackLogoUrl);
            @endphp
            <img src="{{ $logoUrl }}" alt="Your Events Logo" class="login-logo">
            <h4 class="fw-bold" style="color: var(--primary-color);">لوحة تحكم المسؤول</h4>
            <p class="text-muted small">قم بتسجيل الدخول للمتابعة</p>
        </div>

        <form method="POST" action="{{ route('admin.login') }}">
            @csrf

            <div class="mb-3">
                <label for="email" class="form-label text-muted small">البريد الإلكتروني</label>
                <div class="input-group">
                    <span class="input-group-text text-muted"><i class="fas fa-envelope"></i></span>
                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required autofocus placeholder="admin@example.com">
                </div>
                @error('email')
                    <span class="text-danger small mt-1 d-block">{{ $message }}</span>
                @enderror
            </div>

            <div class="mb-4">
                <label for="password" class="form-label text-muted small">كلمة المرور</label>
                <div class="input-group">
                    <span class="input-group-text text-muted"><i class="fas fa-lock"></i></span>
                    <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required placeholder="********">
                </div>
                @error('password')
                    <span class="text-danger small mt-1 d-block">{{ $message }}</span>
                @enderror
            </div>

            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="remember" name="remember">
                <label class="form-check-label small text-muted" for="remember">تذكرني</label>
            </div>

            <button type="submit" class="btn btn-primary mb-3">
                تسجيل الدخول <i class="fas fa-arrow-left me-2"></i>
            </button>
            
            <button type="button" id="biometric-login-btn" class="btn btn-outline-light w-100 mb-3" style="display: none;" onclick="loginBiometric()">
                <i class="fas fa-fingerprint me-2"></i> الدخول بالبصمة
            </button>
        </form>
        
        <div class="text-center mt-4">
            <a href="{{ route('home') }}" class="text-decoration-none small text-muted">
                <i class="fas fa-home me-1"></i> العودة للرئيسية
            </a>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', async () => {
            if (window.PublicKeyCredential && 
                PublicKeyCredential.isUserVerifyingPlatformAuthenticatorAvailable &&
                await PublicKeyCredential.isUserVerifyingPlatformAuthenticatorAvailable()) {
                
                const bioBtn = document.getElementById('biometric-login-btn');
                if (bioBtn) bioBtn.style.display = 'block';
            }

            const emailEl = document.getElementById('email');
            const passEl = document.getElementById('password');
            emailEl?.addEventListener('input', updateBiometricBtnState);
            passEl?.addEventListener('input', updateBiometricBtnState);
            updateBiometricBtnState();
        });

        function updateBiometricBtnState() {
            const bioBtn = document.getElementById('biometric-login-btn');
            if (!bioBtn) return;
            const email = (document.getElementById('email')?.value || '').trim();
            const password = (document.getElementById('password')?.value || '').trim();
            bioBtn.disabled = !(email && password);
        }

        async function loginBiometric() {
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            if (!email || !password) {
                alert('يرجى إدخال البريد الإلكتروني وكلمة المرور أولاً');
                return;
            }

            try {
                const preRes = await fetch("{{ route('biometric.precheck') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ email, password, user_type: 'user', context: 'admin' }),
                    credentials: 'same-origin'
                });

                if (!preRes.ok) {
                    const errorData = await preRes.json();
                    throw new Error(errorData.error || 'بيانات الدخول غير صحيحة.');
                }

                // 1. Get options
                const optionsRes = await fetch("{{ route('biometric.auth.options') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ email, user_type: 'user', context: 'admin' }),
                    credentials: 'same-origin'
                });

                if (!optionsRes.ok) {
                    const errorData = await optionsRes.json();
                    throw new Error(errorData.error || 'فشل في جلب خيارات المصادقة');
                }
                
                const options = await optionsRes.json();

                // 2. Decode options
                const challengeB64 = String(options.challenge || '').replace(/-/g, '+').replace(/_/g, '/');
                options.challenge = Uint8Array.from(atob(challengeB64), c => c.charCodeAt(0));
                options.allowCredentials.forEach(cred => {
                    const idB64 = String(cred.id || '').replace(/-/g, '+').replace(/_/g, '/');
                    cred.id = Uint8Array.from(atob(idB64), c => c.charCodeAt(0));
                });

                // 3. Get credential
                const credential = await navigator.credentials.get({ publicKey: options });

                // 4. Encode response
                const response = {
                    id: credential.id,
                    rawId: credential.id,
                    type: credential.type,
                    response: {
                        clientDataJSON: btoa(String.fromCharCode(...new Uint8Array(credential.response.clientDataJSON))),
                        authenticatorData: btoa(String.fromCharCode(...new Uint8Array(credential.response.authenticatorData))),
                        signature: btoa(String.fromCharCode(...new Uint8Array(credential.response.signature))),
                        userHandle: credential.response.userHandle ? btoa(String.fromCharCode(...new Uint8Array(credential.response.userHandle))) : null
                    }
                };

                // 5. Verify
                const verifyRes = await fetch("{{ route('biometric.authenticate') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ ...response, context: 'admin' }),
                    credentials: 'same-origin'
                });

                const verifyData = await verifyRes.json();
                if (verifyData.success) {
                    window.location.href = verifyData.redirect;
                } else {
                    alert(verifyData.error || 'فشل التحقق');
                }

            } catch (e) {
                console.error(e);
                alert('خطأ: ' + (e.message || e));
            }
        }
    </script>
</body>
</html>

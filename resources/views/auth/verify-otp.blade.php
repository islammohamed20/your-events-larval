@extends('layouts.app')

@section('title', 'التحقق من البريد الإلكتروني')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-lg border-0" style="border-radius: 15px;">
                <!-- Header -->
                <div class="card-header text-center py-4" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 15px 15px 0 0;">
                    <div style="font-size: 60px; margin-bottom: 10px;">🔐</div>
                    <h3 class="text-white mb-0">التحقق من البريد الإلكتروني</h3>
                </div>

                <div class="card-body p-4">
                    <!-- Success Message for Registration -->
                    @if(session('show_success_message'))
                    <div class="alert alert-success border-0 mb-4" style="border-right: 4px solid #28a745 !important;">
                        <div class="d-flex align-items-center">
                            <div class="me-3" style="font-size: 40px;">✅</div>
                            <div>
                                <h5 class="mb-1">
                                    <i class="fas fa-check-circle"></i>
                                    تم إنشاء حسابك بنجاح!
                                </h5>
                                <p class="mb-0">
                                    تم إرسال كود التحقق إلى بريدك الإلكتروني. يرجى إدخال الكود للمتابعة.
                                </p>
                            </div>
                        </div>
                    </div>
                    @endif
                    
                    <!-- Info Box -->
                    <div class="alert alert-info border-0" style="border-right: 4px solid #667eea !important;">
                        <i class="fas fa-info-circle"></i>
                        ولا عليك أمر، ادخل الكود المؤقت اللي أرسلناه لإيميلك:
                        <br>
                        <strong class="text-primary">{{ $email }}</strong>
                    </div>

                    <!-- OTP Form -->
                    <form id="otpForm">
                        @csrf
                        <input type="hidden" name="email" value="{{ $email }}">
                        <input type="hidden" name="type" value="{{ $type }}">

                        <div class="mb-4">
                            <label class="form-label fw-bold">
                                <i class="fas fa-key text-primary"></i> كود التحقق
                            </label>
                            <div class="otp-input-group d-flex justify-content-between" dir="ltr">
                                <input type="text" maxlength="1" class="otp-input form-control text-center" data-index="0">
                                <input type="text" maxlength="1" class="otp-input form-control text-center" data-index="1">
                                <input type="text" maxlength="1" class="otp-input form-control text-center" data-index="2">
                                <input type="text" maxlength="1" class="otp-input form-control text-center" data-index="3">
                                <input type="text" maxlength="1" class="otp-input form-control text-center" data-index="4">
                                <input type="text" maxlength="1" class="otp-input form-control text-center" data-index="5">
                            </div>
                            <input type="hidden" id="otpValue" name="otp">
                            <div class="invalid-feedback d-block" id="otpError"></div>
                        </div>

                        <!-- Timer -->
                        <div class="text-center mb-3">
                            <div id="timer" class="text-muted">
                                <i class="far fa-clock"></i>
                                سينتهي الكود خلال: <span id="countdown" class="fw-bold text-primary">10:00</span>
                            </div>
                        </div>

                        <!-- Verify Button -->
                        <button type="submit" class="btn btn-primary btn-lg w-100 mb-3" id="verifyBtn">
                            <i class="fas fa-check-circle"></i> تحقق من الكود
                        </button>

                        <!-- Resend -->
                        <div class="text-center">
                            <p class="text-muted mb-2">لم تستلم الكود؟</p>
                            <button type="button" class="btn btn-link" id="resendBtn" disabled>
                                <i class="fas fa-redo"></i> إعادة إرسال الكود
                            </button>
                            <div id="resendTimer" class="text-muted small">
                                يمكنك إعادة الإرسال بعد <span id="resendCountdown">60</span> ثانية
                            </div>
                        </div>
                    </form>

                    <!-- Tips -->
                    <div class="mt-4 p-3 bg-light rounded">
                        <h6 class="fw-bold mb-2">
                            <i class="fas fa-lightbulb text-warning"></i> نصائح:
                        </h6>
                        <ul class="small text-muted mb-0">
                            <li>تحقق من صندوق الوارد والبريد المزعج</li>
                            <li>الكود صالح لمدة 10 دقائق فقط</li>
                            <li>لديك 5 محاولات للتحقق</li>
                            <li>لا تشارك الكود مع أي شخص</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Back Link -->
            <div class="text-center mt-3">
                <a href="{{ route('register') }}" class="text-muted">
                    <i class="fas fa-arrow-right"></i> العودة للتسجيل
                </a>
            </div>
        </div>
    </div>
</div>

<style>
.otp-input {
    width: 50px;
    height: 60px;
    font-size: 24px;
    font-weight: bold;
    border: 2px solid #dee2e6;
    border-radius: 10px;
    transition: all 0.3s;
}

.otp-input:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    transform: scale(1.05);
}

.otp-input.filled {
    border-color: #667eea;
    background-color: #f0f3ff;
}

.otp-input.error {
    border-color: #dc3545;
    animation: shake 0.5s;
}

@keyframes shake {
    0%, 100% { transform: translateX(0); }
    25% { transform: translateX(-5px); }
    75% { transform: translateX(5px); }
}

@media (max-width: 576px) {
    .otp-input {
        width: 45px;
        height: 55px;
        font-size: 20px;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const otpInputs = document.querySelectorAll('.otp-input');
    const otpValue = document.getElementById('otpValue');
    const otpError = document.getElementById('otpError');
    const verifyBtn = document.getElementById('verifyBtn');
    const resendBtn = document.getElementById('resendBtn');
    const otpForm = document.getElementById('otpForm');
    
    let countdown = 600; // 10 minutes
    let resendCountdown = 60; // 1 minute

    // OTP Input Handling
    otpInputs.forEach((input, index) => {
        input.addEventListener('input', function(e) {
            const value = e.target.value;
            
            // Only allow numbers
            if (!/^\d*$/.test(value)) {
                e.target.value = '';
                return;
            }

            if (value) {
                e.target.classList.add('filled');
                // Move to next input
                if (index < otpInputs.length - 1) {
                    otpInputs[index + 1].focus();
                }
            } else {
                e.target.classList.remove('filled');
            }

            updateOtpValue();
        });

        input.addEventListener('keydown', function(e) {
            // Backspace handling
            if (e.key === 'Backspace' && !e.target.value && index > 0) {
                otpInputs[index - 1].focus();
                otpInputs[index - 1].value = '';
                otpInputs[index - 1].classList.remove('filled');
                updateOtpValue();
            }
            
            // Paste handling
            if (e.key === 'v' && (e.ctrlKey || e.metaKey)) {
                e.preventDefault();
                navigator.clipboard.readText().then(text => {
                    const numbers = text.replace(/\D/g, '').slice(0, 6);
                    numbers.split('').forEach((num, i) => {
                        if (otpInputs[i]) {
                            otpInputs[i].value = num;
                            otpInputs[i].classList.add('filled');
                        }
                    });
                    updateOtpValue();
                });
            }
        });
    });

    function updateOtpValue() {
        const otp = Array.from(otpInputs).map(input => input.value).join('');
        otpValue.value = otp;
        otpError.textContent = '';
        otpInputs.forEach(input => input.classList.remove('error'));
    }

    // Main Countdown Timer (10 minutes)
    const countdownInterval = setInterval(() => {
        countdown--;
        const minutes = Math.floor(countdown / 60);
        const seconds = countdown % 60;
        document.getElementById('countdown').textContent = 
            `${minutes}:${seconds.toString().padStart(2, '0')}`;

        if (countdown <= 0) {
            clearInterval(countdownInterval);
            otpError.textContent = 'انتهت صلاحية الكود. يرجى طلب كود جديد';
            verifyBtn.disabled = true;
        }
    }, 1000);

    // Resend Countdown (1 minute)
    const resendInterval = setInterval(() => {
        resendCountdown--;
        document.getElementById('resendCountdown').textContent = resendCountdown;

        if (resendCountdown <= 0) {
            clearInterval(resendInterval);
            resendBtn.disabled = false;
            document.getElementById('resendTimer').style.display = 'none';
        }
    }, 1000);

    // Verify OTP
    otpForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const otp = otpValue.value;
        
        if (otp.length !== 6) {
            otpError.textContent = 'الرجاء إدخال 6 أرقام';
            otpInputs.forEach(input => input.classList.add('error'));
            return;
        }

        verifyBtn.disabled = true;
        verifyBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري التحقق...';

        try {
            const formData = new FormData(otpForm);
            const response = await fetch('{{ route("otp.verify") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                // Success animation
                otpInputs.forEach(input => {
                    input.style.borderColor = '#28a745';
                    input.style.backgroundColor = '#d4edda';
                });
                
                // Show success message and redirect
                otpError.textContent = '';
                otpError.className = 'text-success text-center mt-3 fw-bold';
                otpError.textContent = '✓ ' + data.message + ' - جاري التحويل...';
                
                // Redirect after 1 second
                setTimeout(() => {
                    window.location.href = data.redirect;
                }, 1000);
            } else {
                otpError.textContent = data.message;
                otpInputs.forEach(input => input.classList.add('error'));
                verifyBtn.disabled = false;
                verifyBtn.innerHTML = '<i class="fas fa-check-circle"></i> تحقق من الكود';
            }
        } catch (error) {
            console.error('OTP Verification Error:', error);
            otpError.textContent = 'حدث خطأ: ' + error.message;
            verifyBtn.disabled = false;
            verifyBtn.innerHTML = '<i class="fas fa-check-circle"></i> تحقق من الكود';
        }
    });

    // Resend OTP
    resendBtn.addEventListener('click', async function() {
        resendBtn.disabled = true;
        resendBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري الإرسال...';

        try {
            const response = await fetch('{{ route("otp.resend") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    email: '{{ $email }}',
                    type: '{{ $type }}'
                })
            });

            const data = await response.json();

            if (data.success) {
                // Show success message
                otpError.textContent = '';
                otpError.className = 'text-success text-center mt-3 fw-bold';
                otpError.textContent = '✓ ' + data.message;

                // Reset countdown
                countdown = 600;
                resendCountdown = 60;
                document.getElementById('resendTimer').style.display = 'block';
                
                // Clear inputs
                otpInputs.forEach(input => {
                    input.value = '';
                    input.classList.remove('filled', 'error');
                });
                otpInputs[0].focus();
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'فشل الإرسال',
                    text: data.message
                });
            }
        } catch (error) {
            Swal.fire({
                icon: 'error',
                title: 'خطأ',
                text: 'حدث خطأ أثناء إعادة الإرسال'
            });
        } finally {
            resendBtn.innerHTML = '<i class="fas fa-redo"></i> إعادة إرسال الكود';
        }
    });

    // Focus first input on load
    otpInputs[0].focus();
});
</script>
@endsection

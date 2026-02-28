@extends('layouts.app')

@section('title', 'تحقق من البريد الإلكتروني')
@section('robotsMeta', 'noindex,nofollow')

@section('content')
<div class="min-vh-100 d-flex align-items-center bg-light py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-5 col-md-7">
                <div class="card shadow-lg border-0 rounded-4">
                    <div class="card-body p-5">
                        <!-- Icon & Title -->
                        <div class="text-center mb-4">
                            <div class="mb-3">
                                <img src="{{ asset('images/logo/White.png') }}" alt="Your Events" style="height: 60px; width: auto; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 15px; border-radius: 15px;">
                            </div>
                            <h2 class="fw-bold text-dark mb-2">تحقق من بريدك الإلكتروني</h2>
                            <p class="text-muted">
                                تم إرسال رمز التحقق إلى<br>
                                <strong class="text-dark">{{ session('supplier_email') }}</strong>
                            </p>
                        </div>

                        <!-- OTP Form -->
                        <form method="POST" action="{{ route('suppliers.verify-otp.post') }}" id="otpForm" data-resend-url="{{ \Illuminate\Support\Facades\Route::has('suppliers.resend-otp') ? route('suppliers.resend-otp') : route('otp.resend') }}">
                            @csrf
                            <input type="hidden" name="email" value="{{ session('supplier_email') }}">

                            @if(session('error'))
                                <div class="alert alert-danger d-flex align-items-center" role="alert">
                                    <i class="fas fa-exclamation-circle me-2"></i>
                                    <div>{{ session('error') }}</div>
                                </div>
                            @endif

                            @if(session('success'))
                                <div class="alert alert-success d-flex align-items-center" role="alert">
                                    <i class="fas fa-check-circle me-2"></i>
                                    <div>{{ session('success') }}</div>
                                </div>
                            @endif

                            <!-- OTP Input -->
                            <div class="mb-4">
                                <label for="otp" class="form-label fw-semibold text-dark text-center d-block">أدخل رمز التحقق</label>
                                <div class="otp-input-wrapper d-flex justify-content-center gap-2 mb-3">
                                    <input type="tel" inputmode="numeric" pattern="[0-9]*" autocomplete="one-time-code" class="otp-digit form-control form-control-lg text-center" maxlength="1" data-index="0">
                                    <input type="tel" inputmode="numeric" pattern="[0-9]*" class="otp-digit form-control form-control-lg text-center" maxlength="1" data-index="1">
                                    <input type="tel" inputmode="numeric" pattern="[0-9]*" class="otp-digit form-control form-control-lg text-center" maxlength="1" data-index="2">
                                    <input type="tel" inputmode="numeric" pattern="[0-9]*" class="otp-digit form-control form-control-lg text-center" maxlength="1" data-index="3">
                                    <input type="tel" inputmode="numeric" pattern="[0-9]*" class="otp-digit form-control form-control-lg text-center" maxlength="1" data-index="4">
                                    <input type="tel" inputmode="numeric" pattern="[0-9]*" class="otp-digit form-control form-control-lg text-center" maxlength="1" data-index="5">
                                </div>
                                <input type="hidden" name="otp" id="otp" required>
                                @error('otp')
                                    <div class="text-danger text-center small">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Timer -->
                            <div class="text-center mb-4">
                                <p class="text-muted small mb-2">
                                    <i class="fas fa-clock me-1"></i>
                                    الرمز صالح لمدة <span id="timer" class="fw-bold text-warning">10:00</span> دقيقة
                                </p>
                            </div>

                            <!-- Submit Button -->
                            <button type="submit" class="btn btn-warning btn-lg w-100 fw-bold mb-3" id="verifyBtn">
                                <i class="fas fa-check-circle me-2"></i>تحقق من الرمز
                                <span class="spinner-border spinner-border-sm ms-2 d-none" role="status"></span>
                            </button>

                            <!-- Resend Link -->
                            <div class="text-center">
                                <p class="text-muted small mb-2">لم تستلم الرمز؟</p>
                                <button type="button" class="btn btn-link text-warning fw-semibold text-decoration-none" id="resendBtn">
                                    <i class="fas fa-redo-alt me-1"></i>إعادة إرسال الرمز
                                </button>
                                <p class="text-success small d-none" id="resendSuccess">
                                    <i class="fas fa-check me-1"></i>تم إرسال رمز جديد بنجاح
                                </p>
                            </div>
                        </form>

                        <!-- Back Link -->
                        <div class="text-center mt-4 pt-3 border-top">
                            <a href="{{ route('suppliers.register') }}" class="text-muted text-decoration-none small">
                                <i class="fas fa-arrow-right me-1"></i>العودة إلى التسجيل
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Help Text -->
                <div class="card bg-transparent border-0 mt-3">
                    <div class="card-body text-center">
                        <p class="text-muted small mb-0">
                            <i class="fas fa-info-circle me-1"></i>
                            تأكد من فحص مجلد الرسائل غير المرغوب فيها (Spam) في بريدك الإلكتروني
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .otp-digit {
        width: 50px;
        height: 60px;
        font-size: 1.5rem;
        font-weight: bold;
        border: 2px solid #dee2e6;
        border-radius: 10px;
        transition: all 0.3s ease;
    }
    
    .otp-digit:focus {
        border-color: #ffc107;
        box-shadow: 0 0 0 0.25rem rgba(255, 193, 7, 0.25);
        transform: scale(1.05);
    }
    
    .otp-digit.filled {
        border-color: #ffc107;
        background-color: #fff9e6;
    }
    
    .card {
        animation: fadeInUp 0.5s ease;
    }
    
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    #resendBtn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('otpForm');
    const resendUrl = form.dataset.resendUrl;
    const verifyBtn = document.getElementById('verifyBtn');
    const resendBtn = document.getElementById('resendBtn');
    const resendSuccess = document.getElementById('resendSuccess');
    const otpDigits = document.querySelectorAll('.otp-digit');
    const otpHiddenInput = document.getElementById('otp');
    const timerElement = document.getElementById('timer');
    
    let timeLeft = 600; // 10 minutes in seconds
    let timerInterval;
    let resendCooldown = 0;

    // OTP Input Handling
    otpDigits.forEach((digit, index) => {
        digit.addEventListener('input', function(e) {
            const value = e.target.value;
            
            if (value.length === 1 && /^\d$/.test(value)) {
                this.classList.add('filled');
                
                // Move to next input
                if (index < otpDigits.length - 1) {
                    otpDigits[index + 1].focus();
                }
                
                // Update hidden input
                updateOtpValue();
            }
        });

        digit.addEventListener('keydown', function(e) {
            // Handle backspace
            if (e.key === 'Backspace') {
                if (this.value === '' && index > 0) {
                    otpDigits[index - 1].focus();
                    otpDigits[index - 1].value = '';
                    otpDigits[index - 1].classList.remove('filled');
                } else {
                    this.value = '';
                    this.classList.remove('filled');
                }
                updateOtpValue();
            }
            
            // Handle arrow keys
            if (e.key === 'ArrowLeft' && index > 0) {
                otpDigits[index - 1].focus();
            }
            if (e.key === 'ArrowRight' && index < otpDigits.length - 1) {
                otpDigits[index + 1].focus();
            }
        });

        // Handle paste
        digit.addEventListener('paste', function(e) {
            e.preventDefault();
            const pastedData = e.clipboardData.getData('text').replace(/\D/g, '').slice(0, 6);
            
            pastedData.split('').forEach((char, i) => {
                if (otpDigits[i]) {
                    otpDigits[i].value = char;
                    otpDigits[i].classList.add('filled');
                }
            });
            
            updateOtpValue();
            
            if (pastedData.length === 6) {
                otpDigits[5].focus();
            }
        });
    });

    function updateOtpValue() {
        const otp = Array.from(otpDigits).map(d => d.value).join('');
        otpHiddenInput.value = otp;
        
        // Auto-submit if all digits filled
        if (otp.length === 6) {
            // Optionally auto-submit
            // form.submit();
        }
    }

    // Timer Countdown
    function startTimer() {
        timerInterval = setInterval(function() {
            timeLeft--;
            
            const minutes = Math.floor(timeLeft / 60);
            const seconds = timeLeft % 60;
            timerElement.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;
            
            if (timeLeft <= 0) {
                clearInterval(timerInterval);
                timerElement.textContent = 'انتهت صلاحية الرمز';
                timerElement.classList.remove('text-warning');
                timerElement.classList.add('text-danger');
            } else if (timeLeft <= 60) {
                timerElement.classList.remove('text-warning');
                timerElement.classList.add('text-danger');
            }
        }, 1000);
    }

    startTimer();

    // Form Submission
    form.addEventListener('submit', function(e) {
        const otp = otpHiddenInput.value;
        
        if (otp.length !== 6) {
            e.preventDefault();
            alert('يرجى إدخال رمز التحقق المكون من 6 أرقام');
            return;
        }
        
        const spinner = verifyBtn.querySelector('.spinner-border');
        verifyBtn.disabled = true;
        spinner.classList.remove('d-none');
    });

    // Resend OTP
    resendBtn.addEventListener('click', function() {
        if (resendCooldown > 0) {
            return;
        }
        
        resendBtn.disabled = true;
        resendCooldown = 60; // 60 seconds cooldown
        
        // Update button text with countdown
        const originalText = resendBtn.innerHTML;
        const countdownInterval = setInterval(function() {
            resendCooldown--;
            resendBtn.innerHTML = `<i class="fas fa-clock me-1"></i>يمكنك إعادة الإرسال بعد ${resendCooldown} ثانية`;
            
            if (resendCooldown <= 0) {
                clearInterval(countdownInterval);
                resendBtn.disabled = false;
                resendBtn.innerHTML = originalText;
            }
        }, 1000);
        
        // Send AJAX request to resend OTP
        fetch(resendUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                email: '{{ session("supplier_email") }}',
                resend_otp: true
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                resendSuccess.classList.remove('d-none');
                setTimeout(() => {
                    resendSuccess.classList.add('d-none');
                }, 5000);
                
                // Reset timer
                clearInterval(timerInterval);
                timeLeft = 600;
                timerElement.classList.remove('text-danger');
                timerElement.classList.add('text-warning');
                startTimer();
                
                // Clear OTP inputs
                otpDigits.forEach(digit => {
                    digit.value = '';
                    digit.classList.remove('filled');
                });
                otpDigits[0].focus();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('حدث خطأ أثناء إعادة إرسال الرمز. يرجى المحاولة مرة أخرى.');
        });
    });

    // Focus first input on load
    otpDigits[0].focus();
});
</script>
@endpush
@endsection

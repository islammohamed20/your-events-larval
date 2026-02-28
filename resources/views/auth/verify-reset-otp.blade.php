@extends('layouts.app')

@section('title', 'تحقق من رمز التحقق') 'التحقق من كود OTP')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-lg border-0">
                <div class="card-body p-5">
                    <!-- Logo -->
                    <div class="text-center mb-4">
                        <img src="{{ asset('images/logo/logo.png') }}" alt="Your Events" style="max-height: 60px;">
                        <h3 class="mt-3 mb-2">التحقق من الكود</h3>
                        <p class="text-muted">تم إرسال كود التحقق إلى:</p>
                        <p class="fw-bold text-primary">{{ $email }}</p>
                    </div>

                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            @foreach($errors->all() as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('password.otp.verify.post') }}" id="otpForm">
                        @csrf
                        <input type="hidden" name="email" value="{{ $email }}">

                        <!-- OTP Input -->
                        <div class="mb-4">
                            <label class="form-label text-center d-block mb-3">
                                <i class="fas fa-shield-alt me-1"></i>
                                أدخل كود التحقق المكون من 6 أرقام
                            </label>
                            
                            <div class="otp-input-group d-flex justify-content-center gap-2 mb-3">
                                <input type="tel" inputmode="numeric" pattern="[0-9]*" autocomplete="one-time-code" class="form-control otp-box text-center" maxlength="1" data-index="0">
                                <input type="tel" inputmode="numeric" pattern="[0-9]*" class="form-control otp-box text-center" maxlength="1" data-index="1">
                                <input type="tel" inputmode="numeric" pattern="[0-9]*" class="form-control otp-box text-center" maxlength="1" data-index="2">
                                <input type="tel" inputmode="numeric" pattern="[0-9]*" class="form-control otp-box text-center" maxlength="1" data-index="3">
                                <input type="tel" inputmode="numeric" pattern="[0-9]*" class="form-control otp-box text-center" maxlength="1" data-index="4">
                                <input type="tel" inputmode="numeric" pattern="[0-9]*" class="form-control otp-box text-center" maxlength="1" data-index="5">
                            </div>
                            
                            <input type="hidden" name="otp" id="otpValue">
                        </div>

                        <!-- Timer -->
                        <div class="text-center mb-3">
                            <small class="text-muted">
                                صلاحية الكود: <span id="timer" class="fw-bold text-danger">10:00</span>
                            </small>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="btn btn-primary btn-lg w-100 mb-3" id="verifyBtn">
                            <i class="fas fa-check-circle me-2"></i>
                            تحقق من الكود
                        </button>

                        <!-- Resend -->
                        <div class="text-center">
                            <p class="mb-2 text-muted small">لم تستلم الكود؟</p>
                            <button type="button" class="btn btn-link" id="resendBtn">
                                <i class="fas fa-redo me-1"></i>
                                إعادة إرسال الكود
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Help -->
            <div class="text-center mt-4">
                <p class="text-muted small">
                    <i class="fas fa-info-circle me-1"></i>
                    تحقق من مجلد الرسائل غير المرغوب فيها (Spam)
                </p>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .card {
        border-radius: 15px;
    }
    
    .otp-box {
        width: 50px;
        height: 60px;
        font-size: 24px;
        font-weight: bold;
        border: 2px solid #dee2e6;
        border-radius: 10px;
        transition: all 0.3s ease;
    }
    
    .otp-box:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 0.2rem rgba(102, 16, 242, 0.25);
        outline: none;
    }
    
    .otp-box.filled {
        border-color: var(--primary-color);
        background-color: #f8f9ff;
    }
    
    .btn-primary {
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        border: none;
        border-radius: 10px;
        padding: 12px 20px;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(102, 16, 242, 0.3);
    }
    
    .btn-primary:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const otpBoxes = document.querySelectorAll('.otp-box');
    const otpValue = document.getElementById('otpValue');
    const form = document.getElementById('otpForm');
    const verifyBtn = document.getElementById('verifyBtn');
    const resendBtn = document.getElementById('resendBtn');
    const timerEl = document.getElementById('timer');
    
    // Timer
    let timeLeft = 600; // 10 minutes
    const timerInterval = setInterval(() => {
        timeLeft--;
        const minutes = Math.floor(timeLeft / 60);
        const seconds = timeLeft % 60;
        timerEl.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;
        
        if (timeLeft <= 0) {
            clearInterval(timerInterval);
            timerEl.textContent = 'انتهت الصلاحية';
            timerEl.classList.remove('text-danger');
            timerEl.classList.add('text-muted');
            otpBoxes.forEach(box => box.disabled = true);
            verifyBtn.disabled = true;
        }
    }, 1000);
    
    // OTP Input Handler
    otpBoxes.forEach((box, index) => {
        box.addEventListener('input', function(e) {
            const value = this.value.replace(/[^0-9]/g, '');
            this.value = value;
            
            if (value) {
                this.classList.add('filled');
                if (index < otpBoxes.length - 1) {
                    otpBoxes[index + 1].focus();
                }
            } else {
                this.classList.remove('filled');
            }
            
            updateOtpValue();
        });
        
        box.addEventListener('keydown', function(e) {
            if (e.key === 'Backspace' && !this.value && index > 0) {
                otpBoxes[index - 1].focus();
            }
        });
        
        box.addEventListener('paste', function(e) {
            e.preventDefault();
            const pastedData = e.clipboardData.getData('text').replace(/[^0-9]/g, '');
            const digits = pastedData.split('').slice(0, 6);
            
            digits.forEach((digit, i) => {
                if (otpBoxes[i]) {
                    otpBoxes[i].value = digit;
                    otpBoxes[i].classList.add('filled');
                }
            });
            
            if (digits.length > 0) {
                otpBoxes[Math.min(digits.length, 5)].focus();
            }
            
            updateOtpValue();
        });
    });
    
    function updateOtpValue() {
        const otp = Array.from(otpBoxes).map(box => box.value).join('');
        otpValue.value = otp;
        
        if (otp.length === 6) {
            verifyBtn.disabled = false;
        } else {
            verifyBtn.disabled = true;
        }
    }
    
    // Auto focus first box
    otpBoxes[0].focus();
    
    // Resend OTP
    resendBtn.addEventListener('click', function() {
        this.disabled = true;
        this.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> جارٍ الإرسال...';
        
        fetch('{{ route("password.email") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                email: '{{ $email }}'
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success || data.redirect) {
                // Reset timer
                timeLeft = 600;
                
                // Clear boxes
                otpBoxes.forEach(box => {
                    box.value = '';
                    box.classList.remove('filled');
                    box.disabled = false;
                });
                otpBoxes[0].focus();
                
                alert('تم إعادة إرسال الكود بنجاح');
            } else {
                alert(data.message || 'حدث خطأ. حاول مرة أخرى');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('حدث خطأ. حاول مرة أخرى');
        })
        .finally(() => {
            this.disabled = false;
            this.innerHTML = '<i class="fas fa-redo me-1"></i> إعادة إرسال الكود';
        });
    });
});
</script>
@endpush
@endsection

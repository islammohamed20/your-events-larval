@extends('layouts.app')

@section('title', 'اختبار OTP') 'اختبار نظام OTP')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="text-center mb-5">
                <h1 class="display-4">🔐 اختبار نظام OTP</h1>
                <p class="lead text-muted">اختبر جميع أنواع أكواد التحقق</p>
            </div>

            <!-- Test Cards -->
            <div class="row g-4">
                <!-- Email Verification -->
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 shadow-sm hover-card">
                        <div class="card-body text-center">
                            <div class="icon-circle mb-3" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                <i class="fas fa-envelope-open-text fa-2x text-white"></i>
                            </div>
                            <h5 class="card-title">التحقق من البريد</h5>
                            <p class="card-text text-muted small">للتسجيل الجديد</p>
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#otpModal" data-type="email_verification">
                                <i class="fas fa-paper-plane"></i> اختبار
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Login OTP -->
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 shadow-sm hover-card">
                        <div class="card-body text-center">
                            <div class="icon-circle mb-3" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                                <i class="fas fa-sign-in-alt fa-2x text-white"></i>
                            </div>
                            <h5 class="card-title">تسجيل الدخول</h5>
                            <p class="card-text text-muted small">دخول بدون كلمة مرور</p>
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#otpModal" data-type="login">
                                <i class="fas fa-paper-plane"></i> اختبار
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Password Reset -->
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 shadow-sm hover-card">
                        <div class="card-body text-center">
                            <div class="icon-circle mb-3" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">
                                <i class="fas fa-key fa-2x text-white"></i>
                            </div>
                            <h5 class="card-title">إعادة التعيين</h5>
                            <p class="card-text text-muted small">استعادة كلمة المرور</p>
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#otpModal" data-type="password_reset">
                                <i class="fas fa-paper-plane"></i> اختبار
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Booking Confirmation -->
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 shadow-sm hover-card">
                        <div class="card-body text-center">
                            <div class="icon-circle mb-3" style="background: linear-gradient(135deg, #30cfd0 0%, #330867 100%);">
                                <i class="fas fa-calendar-check fa-2x text-white"></i>
                            </div>
                            <h5 class="card-title">تأكيد الحجز</h5>
                            <p class="card-text text-muted small">قبل تفعيل الحجز</p>
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#otpModal" data-type="booking_confirmation">
                                <i class="fas fa-paper-plane"></i> اختبار
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Payment Confirmation -->
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 shadow-sm hover-card">
                        <div class="card-body text-center">
                            <div class="icon-circle mb-3" style="background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);">
                                <i class="fas fa-credit-card fa-2x text-white"></i>
                            </div>
                            <h5 class="card-title">تأكيد الدفع</h5>
                            <p class="card-text text-muted small">أمان المعاملات المالية</p>
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#otpModal" data-type="payment_confirmation">
                                <i class="fas fa-paper-plane"></i> اختبار
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Statistics -->
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 shadow-sm hover-card">
                        <div class="card-body text-center">
                            <div class="icon-circle mb-3" style="background: linear-gradient(135deg, #ff9a9e 0%, #fecfef 100%);">
                                <i class="fas fa-chart-bar fa-2x text-white"></i>
                            </div>
                            <h5 class="card-title">الإحصائيات</h5>
                            <p class="card-text text-muted small">عرض البيانات</p>
                            <button class="btn btn-primary" onclick="loadStats()">
                                <i class="fas fa-sync"></i> عرض
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistics Section -->
            <div class="row mt-5" id="statsSection" style="display: none;">
                <div class="col-12">
                    <div class="card shadow">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-chart-line"></i> إحصائيات OTP
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-md-3">
                                    <div class="stat-box">
                                        <h3 id="totalOtp" class="text-primary">-</h3>
                                        <p class="text-muted">إجمالي الأكواد</p>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="stat-box">
                                        <h3 id="verifiedOtp" class="text-success">-</h3>
                                        <p class="text-muted">تم التحقق</p>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="stat-box">
                                        <h3 id="pendingOtp" class="text-warning">-</h3>
                                        <p class="text-muted">قيد الانتظار</p>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="stat-box">
                                        <h3 id="expiredOtp" class="text-danger">-</h3>
                                        <p class="text-muted">منتهية الصلاحية</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Documentation -->
            <div class="row mt-5">
                <div class="col-12">
                    <div class="card shadow">
                        <div class="card-body">
                            <h5 class="card-title">
                                <i class="fas fa-book text-primary"></i> التوثيق
                            </h5>
                            <p>للحصول على دليل كامل لاستخدام نظام OTP، راجع:</p>
                            <a href="/OTP-SYSTEM-GUIDE.md" class="btn btn-outline-primary" target="_blank">
                                <i class="fas fa-external-link-alt"></i> فتح الدليل
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- OTP Modal -->
<div class="modal fade" id="otpModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-paper-plane"></i> إرسال كود التحقق
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="sendOtpForm">
                    @csrf
                    <input type="hidden" name="type" id="otpType">
                    
                    <div class="mb-3">
                        <label class="form-label">البريد الإلكتروني</label>
                        <input type="email" class="form-control" name="email" required>
                        <div class="form-text">سيُرسل الكود إلى هذا البريد</div>
                    </div>

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>ملاحظة:</strong> هذه صفحة اختبار. في التطبيق الفعلي، سيُرسل الكود تلقائياً.
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <button type="button" class="btn btn-primary" onclick="sendOtp()">
                    <i class="fas fa-paper-plane"></i> إرسال الكود
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.hover-card {
    transition: all 0.3s;
    cursor: pointer;
}

.hover-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.15) !important;
}

.icon-circle {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
}

.stat-box {
    padding: 20px;
    border-radius: 10px;
    background: #f8f9fa;
    margin: 10px 0;
}

.stat-box h3 {
    font-size: 2.5rem;
    font-weight: bold;
    margin: 0;
}

.stat-box p {
    margin: 5px 0 0 0;
    font-size: 0.9rem;
}
</style>

<script>
// Set OTP type when modal opens
document.getElementById('otpModal').addEventListener('show.bs.modal', function(event) {
    const button = event.relatedTarget;
    const type = button.getAttribute('data-type');
    document.getElementById('otpType').value = type;
});

// Send OTP
async function sendOtp() {
    const form = document.getElementById('sendOtpForm');
    const formData = new FormData(form);
    
    try {
        const response = await fetch('{{ route("otp.send") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: formData
        });

        const data = await response.json();

        if (data.success) {
            // Close modal
            bootstrap.Modal.getInstance(document.getElementById('otpModal')).hide();
            
            // Show success
            Swal.fire({
                icon: 'success',
                title: 'تم الإرسال!',
                html: data.message + '<br><br><strong>تحقق من بريدك الإلكتروني</strong>',
                showCancelButton: true,
                confirmButtonText: 'الانتقال للتحقق',
                cancelButtonText: 'حسناً'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '{{ route("otp.verify.form") }}';
                }
            });
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
            text: 'حدث خطأ أثناء الإرسال'
        });
    }
}

// Load statistics (dummy function - implement actual API call)
function loadStats() {
    document.getElementById('statsSection').style.display = 'block';
    document.getElementById('totalOtp').textContent = '150';
    document.getElementById('verifiedOtp').textContent = '120';
    document.getElementById('pendingOtp').textContent = '20';
    document.getElementById('expiredOtp').textContent = '10';
    
    // Scroll to stats
    document.getElementById('statsSection').scrollIntoView({ behavior: 'smooth' });
}
</script>
@endsection

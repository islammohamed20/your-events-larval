@extends('layouts.app')

@section('title', 'إنشاء حساب جديد')

@section('content')
<style>
    .auth-section {
        min-height: 100vh;
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        position: relative;
        overflow: hidden;
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
        padding: 40px 30px;
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
        font-size: 30px;
        font-weight: 800;
        margin-bottom: 10px;
        color: #ffffff;
        position: relative;
        z-index: 1;
    }
    
    .auth-header p {
        font-size: 15px;
        color: rgba(255, 255, 255, 0.9);
        margin: 0;
        line-height: 1.5;
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
        padding: 12px 15px;
        font-size: 14px;
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
        margin-bottom: 8px;
        font-size: 14px;
    }
    
    .btn-primary {
        background: linear-gradient(135deg, #1f1449 0%, #ef4870 50%, #2dbcae 100%);
        background-size: 200% 200%;
        border: none;
        padding: 14px 30px;
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
        margin: 25px 0;
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
        color: #ef4870;
    }
    
    .step-indicator {
        display: flex;
        gap: 12px;
        margin-bottom: 25px;
        justify-content: center;
        flex-wrap: wrap;
    }
    
    .step-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: linear-gradient(135deg, rgba(31, 20, 74, 0.1) 0%, rgba(239, 72, 112, 0.1) 100%);
        color: #1f1449;
        font-weight: 800;
        font-size: 14px;
        transition: all 0.4s ease;
        border: 2px solid rgba(31, 20, 74, 0.2);
    }
    
    .step-badge.active {
        background-color: #2d1a5e;
        color: white;
        box-shadow: 0 6px 20px rgba(239, 72, 112, 0.4);
        border-color: transparent;
        transform: scale(1.1);
        animation: pulse-step 2s ease-in-out infinite;
    }
    
    @keyframes pulse-step {
        0%, 100% { box-shadow: 0 6px 20px rgba(239, 72, 112, 0.4); }
        50% { box-shadow: 0 8px 25px rgba(239, 72, 112, 0.6); }
    }
</style>

<section class="auth-section py-5">
    <!-- Floating Icons -->
    <div class="floating-icon">🎂</div>
    <div class="floating-icon">🎁</div>
    <div class="floating-icon">🎊</div>
    <div class="floating-icon">🎯</div>
    <div class="floating-icon">🎨</div>
    <div class="floating-icon">🎉</div>
    
    <div class="container auth-container">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8">
                <div class="card auth-card" data-aos="fade-up">
                    <div class="auth-header">
                        <h1>✨ إنشاء حساب جديد</h1>
                        <p>بخطوات سريعة وسهلة لتبدأ رحلتك معنا</p>
                    </div>
                    
                    <div class="card-body p-5">
                        <div class="step-indicator">
                            <span class="step-badge active">1️⃣</span>
                            <span class="step-badge">2️⃣</span>
                            <span class="step-badge">3️⃣</span>
                        </div>

                        @if($errors->any())
                            <div class="alert alert-danger border-0 rounded-3" style="background-color: #ffe0e0;">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('register') }}">
                            @csrf
                            
                            <div class="mb-4">
                                <label for="name" class="form-label">👤 اسمك الكامل</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-id-card"></i>
                                    </span>
                                    <input type="text" class="form-control" name="name" id="name" 
                                           value="{{ old('name') }}" placeholder="محمد سعد احمد" required autofocus>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="company_name" class="form-label">
                                    🏢 اسم الجهة
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-building"></i>
                                    </span>
                                    <input type="text" class="form-control @error('company_name') is-invalid @enderror" 
                                           name="company_name" id="company_name" 
                                           value="{{ old('company_name') }}" 
                                           placeholder="مكان عملك أو شركتك"
                                           required>
                                </div>
                                @error('company_name')
                                    <div class="text-danger small mt-1"><i class="fas fa-exclamation-circle me-1"></i>{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="tax_number" class="form-label">
                                    📄 الرقم الضريبي <span class="text-muted" style="font-size: 12px;">(اختياري)</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-receipt"></i>
                                    </span>
                                    <input type="text" class="form-control @error('tax_number') is-invalid @enderror" 
                                           name="tax_number" id="tax_number" 
                                           value="{{ old('tax_number') }}"
                                           placeholder="311019444900003">
                                </div>
                                @error('tax_number')
                                    <div class="text-danger small mt-1"><i class="fas fa-exclamation-circle me-1"></i>{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="email" class="form-label">📧 البريد الإلكتروني</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-at"></i>
                                    </span>
                                    <input type="email" class="form-control" name="email" id="email" 
                                           value="{{ old('email') }}" placeholder="hello@yourevents.sa" required>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="phone" class="form-label">📱 رقم الجوال</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-mobile-alt"></i>
                                    </span>
                                    <input type="tel" class="form-control" name="phone" id="phone" 
                                           value="{{ old('phone') }}" placeholder="05XXXXXXXX" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label for="password" class="form-label">🔐 كلمة المرور</label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="fas fa-key"></i>
                                        </span>
                                        <input type="password" class="form-control" name="password" id="password" placeholder="••••••••" required>
                                    </div>
                                </div>

                                <div class="col-md-6 mb-4">
                                    <label for="password_confirmation" class="form-label">✅ تأكيد المرور</label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="fas fa-lock"></i>
                                        </span>
                                        <input type="password" class="form-control" name="password_confirmation" id="password_confirmation" placeholder="••••••••" required>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-4 form-check">
                                <input type="checkbox" class="form-check-input" id="terms" required>
                                <label class="form-check-label" for="terms" style="font-size: 14px;">
                                    
                                    أوافق على <a href="{{ route('terms') }}" target="_blank" class="text-link">الشروط والأحكام</a> و <a href="{{ route('privacy') }}" target="_blank" class="text-link">سياسة الخصوصية</a>
                                </label>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-user-plus me-2"></i>إنشاء الحساب الآن
                                </button>
                            </div>
                        </form>

                        <div class="divider"><span>أو</span></div>

                        <div class="text-center mb-3">
                            <p class="mb-3" style="font-size: 15px;">
                                <i class="fas fa-sign-in-alt me-2" style="color: #F5576C;"></i>لديك حساب بالفعل؟
                            </p>
                            <a href="{{ route('login') }}" class="btn btn-outline-primary w-100">
                                <i class="fas fa-sign-in-alt me-2"></i>تسجيل الدخول
                            </a>
                        </div>
                    </div>
                </div>

                <div class="text-center mt-4">
                    <a href="{{ route('home') }}" class="back-link" style="color: #1f1449;">
                        <i class="fas fa-arrow-right"></i>العودة للرئيسية
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

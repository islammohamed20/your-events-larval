@extends('layouts.app')

@section('title', 'تسجيل الدخول')

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
                        <h1>حيااااك الله</h1>
                        <p>سجّل دخولك وعيش رحلة تجهيز فعاليتك</p>
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

                        <form method="POST" action="{{ route('login') }}">
                            @csrf
                            
                            <div class="mb-4">
                                <label for="email" class="form-label">
                                    البريد الإلكتروني
                                </label>
                                <div class="input-group">
                                    <input type="email" class="form-control" name="email" id="email" 
                                           value="{{ old('email') }}" placeholder="hello@yourevents.sa" required autofocus>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="password" class="form-label">
                                    كلمة المرور
                                </label>
                                <div class="input-group">
                                    <input type="password" class="form-control" name="password" id="password" placeholder="xxxxxxxxxxxxxxxx" required>
                                </div>
                            </div>

                            <div class="mb-4 form-check">
                                <input type="checkbox" class="form-check-input" name="remember" id="remember">
                                <label class="form-check-label" for="remember">
                                    تذكرني
                                </label>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    تسجيل الدخول
                                </button>
                            </div>
                        </form>

                        <div class="divider"><span>أو</span></div>

                        <div class="text-center mb-4">
                            <p class="mb-3" style="font-size: 15px;">
                                ما عندك حساب؟
                            </p>
                            <a href="{{ route('register') }}" class="btn btn-outline-primary w-100">
                                تعال نبتدي معك
                            </a>
                        </div>

                        <div class="text-center mb-3">
                            <a href="{{ route('password.request') }}" class="text-link">
                                نسيت كلمة السر؟
                            </a>
                        </div>

                        <p class="text-muted small text-center" style="font-size: 13px; margin: 0;">
                            أستمرارك معنا، يعني أنت موافق على <a href="{{ route('terms') }}" class="text-link">شروط الاستخدام</a>
                        </p>
                    </div>
                </div>

                <div class="text-center mt-4">
                    <a href="{{ route('home') }}" class="back-link">
                        العودة للرئيسية
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

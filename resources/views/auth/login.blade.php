@extends('layouts.app')

@section('title', 'تسجيل الدخول - Your Events')

@section('content')
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-5 col-md-7">
                <div class="card shadow-lg" data-aos="fade-up">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <h1 class="h3 fw-bold text-primary">حيااااك الله</h1>
                            <p class="text-muted">و لا عليك أمر، سجّل دخولك وعيش رحلة تجهيز فعاليتك</p>
                        </div>

                        @if($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('login') }}">
                            @csrf
                            
                            <div class="mb-3">
                                <label for="email" class="form-label">البريد الإلكتروني</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-envelope"></i>
                                    </span>
                                    <input type="email" class="form-control" name="email" id="email" 
                                           value="{{ old('email') }}" placeholder="hello@yourevents.sa" required autofocus>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">كلمة المرور</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-lock"></i>
                                    </span>
                                    <input type="password" class="form-control" name="password" id="password" placeholder="xxxxxxxxxxxxxxxx" required>
                                </div>
                            </div>

                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" name="remember" id="remember">
                                <label class="form-check-label" for="remember">
                                    تذكرني
                                </label>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-sign-in-alt me-2"></i>تسجيل الدخول
                                </button>
                            </div>
                        </form>

                        <hr class="my-4">

                        <div class="text-center">
                            <p class="mb-2">ما عندك حساب؟</p>
                            <a href="{{ route('register') }}" class="btn btn-outline-primary">
                                <i class="fas fa-user-plus me-2"></i>تعال نبتدي معك
                            </a>
                            <p class="text-muted small mt-3 mb-0">
                                أستمرارك معنا، يعني أنت موافق على <a href="{{ route('terms') }}" class="text-decoration-none">شروط الاستخدام</a>
                            </p>
                        </div>

                        <div class="text-center mt-3">
                            <a href="{{ route('password.request') }}" class="text-decoration-none">
                                <i class="fas fa-key me-1"></i>
                                نسيت كلمة السر؟ ولا يهمك
                            </a>
                        </div>
                    </div>
                </div>

                <div class="text-center mt-4">
                    <a href="{{ route('home') }}" class="text-muted">
                        <i class="fas fa-arrow-right me-1"></i>العودة للرئيسية
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

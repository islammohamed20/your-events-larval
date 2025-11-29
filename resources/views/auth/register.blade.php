@extends('layouts.app')

@section('title', 'إنشاء حساب جديد - Your Events')

@section('content')
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8">
                <div class="card shadow-lg" data-aos="fade-up">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <h1 class="h3 fw-bold text-primary">يلا نسجّل حسابك الجديد بخطوات سريعة</h1>
                            <p class="text-muted">وخلك جاهز لتجربتك معنا، حيث نحرص على أن تكون كل خطوة بسيطة وممتعة لتسجّل حسابك الجديد بسهولة وسلاسة</p>
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

                        <form method="POST" action="{{ route('register') }}">
                            @csrf
                            
                            <div class="mb-3">
                                <label for="name" class="form-label">سجّل اسمك الكامل وابدأ <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-user"></i>
                                    </span>
                                    <input type="text" class="form-control" name="name" id="name" 
                                           value="{{ old('name') }}" placeholder="محمد سعد احمد" required autofocus>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="company_name" class="form-label">
                                    خلّنا نعرف اسم الجهة <span class="text-danger">*</span>
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
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="tax_number" class="form-label">
                                    أدخل رقمك الضريبي لو حاب <span class="text-muted small">(اختياري)</span>
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
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">ولا عليك أمر، أدخل بريدك الإلكتروني <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-envelope"></i>
                                    </span>
                                    <input type="email" class="form-control" name="email" id="email" 
                                           value="{{ old('email') }}" placeholder="hello@yourevents.sa" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="phone" class="form-label">ولا عليك أمر، أدخل رقم جوالك <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-phone"></i>
                                    </span>
                                    <input type="tel" class="form-control" name="phone" id="phone" 
                                           value="{{ old('phone') }}" placeholder="05XXXXXXXX" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="password" class="form-label">ولا عليك امر، سجل كلمة السر <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="fas fa-lock"></i>
                                        </span>
                                        <input type="password" class="form-control" name="password" id="password" placeholder="••••••••" required>
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="password_confirmation" class="form-label">أكد كلمة مرورك <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="fas fa-lock"></i>
                                        </span>
                                        <input type="password" class="form-control" name="password_confirmation" id="password_confirmation" placeholder="••••••••" required>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="terms" required>
                                <label class="form-check-label" for="terms">
                                    أوافق على <a href="{{ route('terms') }}" target="_blank" class="text-primary text-decoration-none fw-semibold">الشروط والأحكام</a> و <a href="#" class="text-primary text-decoration-none fw-semibold">سياسة الخصوصية</a>
                                </label>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-user-plus me-2"></i>إنشاء الحساب
                                </button>
                            </div>
                        </form>

                        <hr class="my-4">

                        <div class="text-center">
                            <p class="mb-2">هل لديك حساب بالفعل؟</p>
                            <a href="{{ route('login') }}" class="btn btn-outline-primary">
                                <i class="fas fa-sign-in-alt me-2"></i>تسجيل الدخول
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

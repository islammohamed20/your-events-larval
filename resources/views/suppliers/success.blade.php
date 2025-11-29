@extends('layouts.app')

@section('title', 'تم التسجيل بنجاح')

@section('content')
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8">
                <div class="card shadow-lg" data-aos="fade-up">
                    <div class="card-body p-5 text-center">
                        <div class="mb-3" style="font-size: 64px;">✅</div>
                        <h1 class="h3 fw-bold mb-2">تم التسجيل بنجاح</h1>

                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
                                <i class="fas fa-check-circle me-2"></i>
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @else
                            <p class="text-muted mt-2">تم إنشاء حساب المورد بنجاح.</p>
                        @endif

                        <p class="mt-4 text-muted">سيتم تحويلك تلقائياً إلى صفحة تسجيل الدخول خلال ثوانٍ...</p>

                        <a href="{{ route('login') }}" class="btn btn-primary mt-3">
                            <i class="fas fa-sign-in-alt me-2"></i>
                            الذهاب إلى تسجيل الدخول الآن
                        </a>
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

<script>
    // Auto-redirect to login after a short delay
    setTimeout(function() {
        window.location.href = "{{ route('login') }}";
    }, 5000); // 5 seconds
</script>
@endsection


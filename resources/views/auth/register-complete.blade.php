@extends('layouts.app')

@section('title', 'اكتمل التسجيل') 'إكمال التسجيل')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-lg border-0" style="border-radius: 15px;">
                <!-- Header -->
                <div class="card-header text-center py-4" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 15px 15px 0 0;">
                    <div style="font-size: 60px; margin-bottom: 10px;">✅</div>
                    <h3 class="text-white mb-0">إكمال التسجيل</h3>
                    <p class="text-white-50 mb-0 small">تم التحقق من بريدك بنجاح</p>
                </div>

                <div class="card-body p-4 text-center">
                    @if(session('error'))
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle"></i>
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="mb-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">جاري إنشاء الحساب...</span>
                        </div>
                        <p class="mt-3 text-muted">جاري إنشاء حسابك...</p>
                    </div>

                    <form method="POST" action="{{ route('register.complete.post') }}" id="completeForm">
                        @csrf
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // تقديم النموذج تلقائياً بعد ثانية واحدة
    setTimeout(function() {
        document.getElementById('completeForm').submit();
    }, 1000);
});
</script>
@endsection

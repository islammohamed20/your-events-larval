@extends('layouts.admin')

@section('title', 'تغيير كلمة المرور الإجباري')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-6 col-md-8">
            <div class="card mt-4">
                <div class="card-header">
                    <h4 class="card-title mb-0">تغيير كلمة المرور</h4>
                </div>
                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <div class="alert alert-warning">
                        <i class="fas fa-shield-alt"></i>
                        يجب تغيير كلمة المرور الخاصة بك قبل متابعة استخدام لوحة التحكم.
                    </div>

                    @if(auth()->user()->logout_other_devices)
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            بعد حفظ كلمة المرور الجديدة سيتم إنهاء الجلسات الأخرى لهذا الحساب.
                        </div>
                    @endif

                    <div class="alert alert-secondary">
                        <i class="fas fa-user-lock"></i>
                        هذا تغيير إلزامي لأول دخول، لذلك لن يُطلب منك إدخال كلمة المرور الحالية.
                    </div>

                    <form action="{{ route('admin.force-password.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="password" class="form-label">كلمة المرور الجديدة <span class="text-danger">*</span></label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required autofocus>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="password_confirmation" class="form-label">تأكيد كلمة المرور الجديدة <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                        </div>

                        <div class="d-flex justify-content-between gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> حفظ كلمة المرور الجديدة
                            </button>
                            <a href="#" class="btn btn-outline-secondary" onclick="event.preventDefault(); document.getElementById('force-password-logout-form').submit();">
                                <i class="fas fa-sign-out-alt"></i> تسجيل الخروج
                            </a>
                        </div>
                    </form>

                    <form id="force-password-logout-form" action="{{ route('admin.logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
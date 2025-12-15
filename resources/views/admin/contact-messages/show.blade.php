@extends('layouts.admin')

@section('title', 'تفاصيل الرسالة')
@section('page-title', 'تفاصيل الرسالة')
@section('page-description', 'عرض التفاصيل الكاملة لرسالة التواصل')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h4 mb-0"><i class="fas fa-envelope-open-text me-2"></i>رسالة رقم #{{ $message->id }}</h1>
        <a href="{{ route('admin.contact-messages.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> العودة
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">نص الرسالة</h5>
                </div>
                <div class="card-body">
                    <p class="mb-0" style="white-space: pre-wrap;">{{ $message->message }}</p>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">معلومات المرسل</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="text-muted">الاسم</div>
                        <strong>{{ $message->name }}</strong>
                    </div>
                    <div class="mb-3">
                        <div class="text-muted">البريد الإلكتروني</div>
                        <a href="mailto:{{ $message->email }}">{{ $message->email }}</a>
                    </div>
                    <div class="mb-3">
                        <div class="text-muted">الهاتف</div>
                        <span dir="ltr">{{ $message->phone ?? '-' }}</span>
                    </div>
                    <div class="mb-3">
                        <div class="text-muted">الموضوع</div>
                        <span>
                            @switch($message->subject)
                                @case('booking') استفسار عن الحجز @break
                                @case('packages') استفسار عن الباقات @break
                                @case('services') استفسار عن الخدمات @break
                                @case('complaint') شكوى @break
                                @default أخرى
                            @endswitch
                        </span>
                    </div>
                    <div class="mb-3">
                        <div class="text-muted">الحالة</div>
                        <form method="POST" action="{{ route('admin.contact-messages.update-status', $message->id) }}" class="d-flex gap-2">
                            @csrf
                            @method('PATCH')
                            <select name="status" class="form-select">
                                <option value="new" {{ $message->status==='new' ? 'selected' : '' }}>جديدة</option>
                                <option value="in_progress" {{ $message->status==='in_progress' ? 'selected' : '' }}>قيد المتابعة</option>
                                <option value="closed" {{ $message->status==='closed' ? 'selected' : '' }}>مغلقة</option>
                            </select>
                            <button class="btn btn-primary" type="submit"><i class="fas fa-save me-1"></i>تحديث</button>
                        </form>
                    </div>
                    <div class="d-flex gap-2">
                        <form method="POST" action="{{ route('admin.contact-messages.destroy', $message->id) }}">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-outline-danger" type="submit" onclick="return confirm('تأكيد حذف الرسالة؟')">
                                <i class="fas fa-trash me-1"></i>حذف
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


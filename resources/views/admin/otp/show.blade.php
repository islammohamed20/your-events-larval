@extends('layouts.admin')

@section('title', 'تفاصيل OTP')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">
                <i class="fas fa-info-circle text-primary"></i> تفاصيل كود التحقق #{{ $otp->id }}
            </h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">لوحة التحكم</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.otp.index') }}">إدارة OTP</a></li>
                    <li class="breadcrumb-item active">#{{ $otp->id }}</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="{{ route('admin.otp.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-right"></i> العودة
            </a>
            <form action="{{ route('admin.otp.destroy', $otp->id) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من الحذف؟')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-trash"></i> حذف
                </button>
            </form>
        </div>
    </div>

    <div class="row g-4">
        <!-- Main Info -->
        <div class="col-md-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-file-alt"></i> المعلومات الأساسية
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="text-muted small">البريد الإلكتروني</label>
                            <div class="d-flex align-items-center">
                                <i class="fas fa-envelope text-primary me-2"></i>
                                <strong>{{ $otp->email }}</strong>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="text-muted small">كود التحقق</label>
                            <div class="d-flex align-items-center">
                                <i class="fas fa-key text-primary me-2"></i>
                                <code class="fs-4 text-primary fw-bold">{{ $otp->otp }}</code>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="text-muted small">النوع</label>
                            <div>
                                @php
                                    $typeIcons = [
                                        'email_verification' => ['envelope-open-text', 'التحقق من البريد'],
                                        'login' => ['sign-in-alt', 'تسجيل الدخول'],
                                        'password_reset' => ['key', 'إعادة تعيين كلمة المرور'],
                                        'booking_confirmation' => ['calendar-check', 'تأكيد الحجز'],
                                        'payment_confirmation' => ['credit-card', 'تأكيد الدفع'],
                                    ];
                                    $typeInfo = $typeIcons[$otp->type] ?? ['question', $otp->type];
                                @endphp
                                <i class="fas fa-{{ $typeInfo[0] }} text-primary me-2"></i>
                                <strong>{{ $typeInfo[1] }}</strong>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="text-muted small">الحالة</label>
                            <div>
                                @if($otp->status == 'verified')
                                    <span class="badge bg-success fs-6">
                                        <i class="fas fa-check-circle"></i> تم التحقق
                                    </span>
                                @elseif($otp->status == 'pending')
                                    <span class="badge bg-warning fs-6">
                                        <i class="fas fa-clock"></i> قيد الانتظار
                                    </span>
                                @elseif($otp->status == 'expired')
                                    <span class="badge bg-danger fs-6">
                                        <i class="fas fa-times-circle"></i> منتهية الصلاحية
                                    </span>
                                @else
                                    <span class="badge bg-secondary fs-6">
                                        <i class="fas fa-ban"></i> فاشلة
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="text-muted small">عدد المحاولات</label>
                            <div>
                                <span class="badge {{ $otp->attempts >= 5 ? 'bg-danger' : 'bg-info' }} fs-6">
                                    {{ $otp->attempts }} من 5
                                </span>
                                @if($otp->attempts >= 5)
                                    <small class="text-danger">تم تجاوز الحد الأقصى</small>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="text-muted small">حالة الصلاحية</label>
                            <div>
                                @if($otp->isExpired())
                                    <span class="badge bg-danger fs-6">
                                        <i class="fas fa-exclamation-triangle"></i> منتهي
                                    </span>
                                @else
                                    <span class="badge bg-success fs-6">
                                        <i class="fas fa-check"></i> صالح
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Timeline -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="fas fa-history text-primary"></i> الجدول الزمني
                    </h5>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <!-- Created -->
                        <div class="timeline-item">
                            <div class="timeline-icon bg-primary">
                                <i class="fas fa-plus"></i>
                            </div>
                            <div class="timeline-content">
                                <h6 class="mb-1">تم الإنشاء</h6>
                                <p class="text-muted mb-0">
                                    <i class="far fa-clock"></i>
                                    {{ $otp->created_at->format('Y/m/d H:i:s') }}
                                    <small>({{ $otp->created_at->diffForHumans() }})</small>
                                </p>
                            </div>
                        </div>

                        <!-- Expires At -->
                        <div class="timeline-item">
                            <div class="timeline-icon {{ $otp->isExpired() ? 'bg-danger' : 'bg-warning' }}">
                                <i class="fas fa-hourglass-half"></i>
                            </div>
                            <div class="timeline-content">
                                <h6 class="mb-1">وقت الانتهاء</h6>
                                <p class="text-muted mb-0">
                                    <i class="far fa-clock"></i>
                                    {{ $otp->expires_at->format('Y/m/d H:i:s') }}
                                    <small>
                                        @if($otp->isExpired())
                                            <span class="text-danger">(منتهي)</span>
                                        @else
                                            (يبقى {{ $otp->expires_at->diffForHumans() }})
                                        @endif
                                    </small>
                                </p>
                            </div>
                        </div>

                        <!-- Verified -->
                        @if($otp->verified_at)
                            <div class="timeline-item">
                                <div class="timeline-icon bg-success">
                                    <i class="fas fa-check"></i>
                                </div>
                                <div class="timeline-content">
                                    <h6 class="mb-1">تم التحقق</h6>
                                    <p class="text-muted mb-0">
                                        <i class="far fa-clock"></i>
                                        {{ $otp->verified_at->format('Y/m/d H:i:s') }}
                                        <small>({{ $otp->verified_at->diffForHumans() }})</small>
                                    </p>
                                </div>
                            </div>
                        @endif

                        <!-- Updated -->
                        @if($otp->updated_at->ne($otp->created_at))
                            <div class="timeline-item">
                                <div class="timeline-icon bg-info">
                                    <i class="fas fa-sync"></i>
                                </div>
                                <div class="timeline-content">
                                    <h6 class="mb-1">آخر تحديث</h6>
                                    <p class="text-muted mb-0">
                                        <i class="far fa-clock"></i>
                                        {{ $otp->updated_at->format('Y/m/d H:i:s') }}
                                        <small>({{ $otp->updated_at->diffForHumans() }})</small>
                                    </p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-md-4">
            <!-- Security Info -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="fas fa-shield-alt text-primary"></i> معلومات الأمان
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="text-muted small">عنوان IP</label>
                        <div>
                            <i class="fas fa-network-wired text-primary me-2"></i>
                            <code>{{ $otp->ip_address ?? 'غير متوفر' }}</code>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="text-muted small">User Agent</label>
                        <div class="bg-light p-2 rounded">
                            <small class="text-muted">
                                {{ $otp->user_agent ?? 'غير متوفر' }}
                            </small>
                        </div>
                    </div>

                    @if($otp->ip_address)
                        <a href="https://www.ip-tracker.org/locator/ip-lookup.php?ip={{ $otp->ip_address }}" 
                           target="_blank" 
                           class="btn btn-sm btn-outline-primary w-100">
                            <i class="fas fa-map-marker-alt"></i> تتبع الموقع الجغرافي
                        </a>
                    @endif
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="fas fa-bolt text-primary"></i> إجراءات سريعة
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button class="btn btn-outline-primary" onclick="copyToClipboard('{{ $otp->otp }}')">
                            <i class="fas fa-copy"></i> نسخ الكود
                        </button>

                        <button class="btn btn-outline-info" onclick="copyToClipboard('{{ $otp->email }}')">
                            <i class="fas fa-envelope"></i> نسخ البريد
                        </button>

                        @if($otp->status == 'pending')
                            <button class="btn btn-outline-warning" onclick="alert('ميزة قيد التطوير')">
                                <i class="fas fa-redo"></i> إعادة إرسال
                            </button>
                        @endif

                        <hr>

                        <form action="{{ route('admin.otp.destroy', $otp->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من الحذف؟')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger w-100">
                                <i class="fas fa-trash"></i> حذف الكود
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Statistics -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-bar text-primary"></i> إحصائيات
                    </h5>
                </div>
                <div class="card-body">
                    @php
                        $totalMinutes = $otp->created_at->diffInMinutes($otp->expires_at);
                        $remainingMinutes = $otp->isExpired() ? 0 : now()->diffInMinutes($otp->expires_at);
                        $percentageRemaining = $totalMinutes > 0 ? ($remainingMinutes / $totalMinutes) * 100 : 0;
                    @endphp

                    <div class="mb-3">
                        <label class="text-muted small">الوقت المتبقي</label>
                        <div class="progress" style="height: 25px;">
                            <div class="progress-bar {{ $percentageRemaining > 50 ? 'bg-success' : ($percentageRemaining > 20 ? 'bg-warning' : 'bg-danger') }}" 
                                 style="width: {{ $percentageRemaining }}%">
                                {{ round($percentageRemaining) }}%
                            </div>
                        </div>
                        <small class="text-muted">
                            @if($otp->isExpired())
                                منتهي الصلاحية
                            @else
                                يتبقى {{ $remainingMinutes }} دقيقة
                            @endif
                        </small>
                    </div>

                    <div class="mb-3">
                        <label class="text-muted small">المحاولات</label>
                        <div class="progress" style="height: 25px;">
                            <div class="progress-bar {{ $otp->attempts < 3 ? 'bg-success' : ($otp->attempts < 5 ? 'bg-warning' : 'bg-danger') }}" 
                                 style="width: {{ ($otp->attempts / 5) * 100 }}%">
                                {{ $otp->attempts }}/5
                            </div>
                        </div>
                    </div>

                    <div class="text-center">
                        <small class="text-muted">
                            تم الإنشاء منذ {{ $otp->created_at->diffForHumans() }}
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.timeline {
    position: relative;
    padding: 20px 0;
}

.timeline-item {
    position: relative;
    padding-left: 50px;
    padding-bottom: 30px;
}

.timeline-item:last-child {
    padding-bottom: 0;
}

.timeline-item::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 30px;
    bottom: 0;
    width: 2px;
    background: #e9ecef;
}

.timeline-item:last-child::before {
    display: none;
}

.timeline-icon {
    position: absolute;
    left: 0;
    top: 0;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 14px;
}

.timeline-content {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
}
</style>

<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        alert('تم النسخ: ' + text);
    });
}
</script>
@endsection

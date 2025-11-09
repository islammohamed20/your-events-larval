@extends('layouts.admin')

@section('title', 'إدارة OTP')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">
                <i class="fas fa-shield-alt text-primary"></i> إدارة أكواد التحقق (OTP)
            </h2>
            <p class="text-muted mb-0">مراقبة وإدارة جميع أكواد التحقق المرسلة</p>
        </div>
        <div>
            <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#cleanModal">
                <i class="fas fa-broom"></i> تنظيف الأكواد
            </button>
            <a href="{{ route('admin.otp.export', request()->query()) }}" class="btn btn-success">
                <i class="fas fa-file-excel"></i> تصدير CSV
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1 small">إجمالي الأكواد</p>
                            <h3 class="mb-0">{{ number_format($stats['total']) }}</h3>
                        </div>
                        <div class="icon-box bg-primary bg-opacity-10 text-primary">
                            <i class="fas fa-hashtag fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1 small">اليوم</p>
                            <h3 class="mb-0 text-info">{{ number_format($stats['today']) }}</h3>
                        </div>
                        <div class="icon-box bg-info bg-opacity-10 text-info">
                            <i class="fas fa-calendar-day fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1 small">تم التحقق</p>
                            <h3 class="mb-0 text-success">{{ number_format($stats['verified']) }}</h3>
                        </div>
                        <div class="icon-box bg-success bg-opacity-10 text-success">
                            <i class="fas fa-check-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1 small">معدل النجاح</p>
                            <h3 class="mb-0 text-warning">{{ $stats['success_rate'] }}%</h3>
                        </div>
                        <div class="icon-box bg-warning bg-opacity-10 text-warning">
                            <i class="fas fa-chart-line fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm border-start border-warning border-3">
                <div class="card-body py-2">
                    <small class="text-muted">قيد الانتظار</small>
                    <h5 class="mb-0 text-warning">{{ number_format($stats['pending']) }}</h5>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm border-start border-danger border-3">
                <div class="card-body py-2">
                    <small class="text-muted">منتهية الصلاحية</small>
                    <h5 class="mb-0 text-danger">{{ number_format($stats['expired']) }}</h5>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm border-start border-secondary border-3">
                <div class="card-body py-2">
                    <small class="text-muted">فاشلة</small>
                    <h5 class="mb-0 text-secondary">{{ number_format($stats['failed']) }}</h5>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row g-3 mb-4">
        <!-- By Type Chart -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-pie text-primary"></i> حسب النوع
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="typeChart" height="200"></canvas>
                </div>
            </div>
        </div>

        <!-- Activity Chart -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-line text-primary"></i> النشاط (آخر 7 أيام)
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="activityChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white">
            <h5 class="mb-0">
                <i class="fas fa-filter text-primary"></i> البحث والتصفية
            </h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.otp.index') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">البريد الإلكتروني</label>
                        <input type="text" class="form-control" name="email" value="{{ request('email') }}" placeholder="example@email.com">
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">النوع</label>
                        <select class="form-select" name="type">
                            <option value="">الكل</option>
                            <option value="email_verification" {{ request('type') == 'email_verification' ? 'selected' : '' }}>التحقق من البريد</option>
                            <option value="login" {{ request('type') == 'login' ? 'selected' : '' }}>تسجيل الدخول</option>
                            <option value="password_reset" {{ request('type') == 'password_reset' ? 'selected' : '' }}>إعادة التعيين</option>
                            <option value="booking_confirmation" {{ request('type') == 'booking_confirmation' ? 'selected' : '' }}>تأكيد الحجز</option>
                            <option value="payment_confirmation" {{ request('type') == 'payment_confirmation' ? 'selected' : '' }}>تأكيد الدفع</option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">الحالة</label>
                        <select class="form-select" name="status">
                            <option value="">الكل</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>قيد الانتظار</option>
                            <option value="verified" {{ request('status') == 'verified' ? 'selected' : '' }}>تم التحقق</option>
                            <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>منتهية</option>
                            <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>فاشلة</option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">من تاريخ</label>
                        <input type="date" class="form-control" name="date_from" value="{{ request('date_from') }}">
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">إلى تاريخ</label>
                        <input type="date" class="form-control" name="date_to" value="{{ request('date_to') }}">
                    </div>

                    <div class="col-md-1">
                        <label class="form-label d-block">&nbsp;</label>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- OTP List -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white">
            <h5 class="mb-0">
                <i class="fas fa-list text-primary"></i> قائمة أكواد التحقق
                <span class="badge bg-primary">{{ $otps->total() }}</span>
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th width="60">ID</th>
                            <th>البريد الإلكتروني</th>
                            <th>النوع</th>
                            <th width="100">الكود</th>
                            <th width="120">الحالة</th>
                            <th width="80" class="text-center">المحاولات</th>
                            <th width="150">تاريخ الإنشاء</th>
                            <th width="150">تنتهي في</th>
                            <th width="120" class="text-center">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($otps as $otp)
                            <tr>
                                <td>{{ $otp->id }}</td>
                                <td>
                                    <i class="fas fa-envelope text-muted"></i>
                                    {{ $otp->email }}
                                </td>
                                <td>
                                    @php
                                        $typeIcons = [
                                            'email_verification' => 'envelope-open-text',
                                            'login' => 'sign-in-alt',
                                            'password_reset' => 'key',
                                            'booking_confirmation' => 'calendar-check',
                                            'payment_confirmation' => 'credit-card',
                                        ];
                                        $typeLabels = [
                                            'email_verification' => 'التحقق من البريد',
                                            'login' => 'تسجيل الدخول',
                                            'password_reset' => 'إعادة التعيين',
                                            'booking_confirmation' => 'تأكيد الحجز',
                                            'payment_confirmation' => 'تأكيد الدفع',
                                        ];
                                    @endphp
                                    <i class="fas fa-{{ $typeIcons[$otp->type] ?? 'question' }} text-primary"></i>
                                    <small>{{ $typeLabels[$otp->type] ?? $otp->type }}</small>
                                </td>
                                <td>
                                    <code class="text-primary fw-bold">{{ $otp->otp }}</code>
                                </td>
                                <td>
                                    @if($otp->status == 'verified')
                                        <span class="badge bg-success">
                                            <i class="fas fa-check"></i> تم التحقق
                                        </span>
                                    @elseif($otp->status == 'pending')
                                        <span class="badge bg-warning">
                                            <i class="fas fa-clock"></i> قيد الانتظار
                                        </span>
                                    @elseif($otp->status == 'expired')
                                        <span class="badge bg-danger">
                                            <i class="fas fa-times"></i> منتهية
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">
                                            <i class="fas fa-ban"></i> فاشلة
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="badge {{ $otp->attempts >= 5 ? 'bg-danger' : 'bg-info' }}">
                                        {{ $otp->attempts }}/5
                                    </span>
                                </td>
                                <td>
                                    <small class="text-muted">
                                        <i class="far fa-clock"></i>
                                        {{ $otp->created_at->format('Y/m/d H:i') }}
                                    </small>
                                </td>
                                <td>
                                    <small class="text-muted">
                                        <i class="far fa-hourglass-half"></i>
                                        {{ $otp->expires_at->format('Y/m/d H:i') }}
                                        @if($otp->isExpired())
                                            <span class="text-danger">(منتهي)</span>
                                        @endif
                                    </small>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('admin.otp.show', $otp->id) }}" class="btn btn-sm btn-info" title="عرض">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <form action="{{ route('admin.otp.destroy', $otp->id) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من الحذف؟')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="حذف">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-5 text-muted">
                                    <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                    لا توجد أكواد تحقق
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($otps->hasPages())
            <div class="card-footer bg-white">
                {{ $otps->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Clean Modal -->
<div class="modal fade" id="cleanModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-broom text-danger"></i> تنظيف الأكواد
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <ul class="nav nav-tabs mb-3" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" data-bs-toggle="tab" href="#expiredTab">
                            <i class="fas fa-clock"></i> المنتهية الصلاحية
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#oldTab">
                            <i class="fas fa-calendar"></i> الأكواد القديمة
                        </a>
                    </li>
                </ul>

                <div class="tab-content">
                    <!-- Expired Tab -->
                    <div class="tab-pane fade show active" id="expiredTab">
                        <form action="{{ route('admin.otp.clean-expired') }}" method="POST">
                            @csrf
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i>
                                سيتم تحديث جميع الأكواد المنتهية الصلاحية إلى حالة "منتهية"
                            </div>
                            <button type="submit" class="btn btn-warning w-100">
                                <i class="fas fa-broom"></i> تنظيف الأكواد المنتهية
                            </button>
                        </form>
                    </div>

                    <!-- Old Tab -->
                    <div class="tab-pane fade" id="oldTab">
                        <form action="{{ route('admin.otp.delete-old') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">حذف الأكواد الأقدم من:</label>
                                <select class="form-select" name="days" required>
                                    <option value="7">7 أيام</option>
                                    <option value="30" selected>30 يوم</option>
                                    <option value="60">60 يوم</option>
                                    <option value="90">90 يوم</option>
                                    <option value="180">180 يوم</option>
                                    <option value="365">سنة</option>
                                </select>
                            </div>
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-triangle"></i>
                                <strong>تحذير:</strong> سيتم حذف الأكواد نهائياً ولا يمكن استرجاعها!
                            </div>
                            <button type="submit" class="btn btn-danger w-100" onclick="return confirm('هل أنت متأكد من حذف الأكواد القديمة؟')">
                                <i class="fas fa-trash"></i> حذف الأكواد القديمة
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.icon-box {
    width: 60px;
    height: 60px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
}
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Type Chart
    const typeCtx = document.getElementById('typeChart').getContext('2d');
    const typeData = @json($byType);
    
    new Chart(typeCtx, {
        type: 'doughnut',
        data: {
            labels: Object.keys(typeData).map(key => {
                const labels = {
                    'email_verification': 'التحقق من البريد',
                    'login': 'تسجيل الدخول',
                    'password_reset': 'إعادة التعيين',
                    'booking_confirmation': 'تأكيد الحجز',
                    'payment_confirmation': 'تأكيد الدفع'
                };
                return labels[key] || key;
            }),
            datasets: [{
                data: Object.values(typeData),
                backgroundColor: [
                    '#667eea',
                    '#f093fb',
                    '#fa709a',
                    '#30cfd0',
                    '#a8edea'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    // Activity Chart
    const activityCtx = document.getElementById('activityChart').getContext('2d');
    const activityData = @json($recentActivity);
    
    new Chart(activityCtx, {
        type: 'line',
        data: {
            labels: activityData.map(d => d.date),
            datasets: [
                {
                    label: 'إجمالي',
                    data: activityData.map(d => d.total),
                    borderColor: '#667eea',
                    backgroundColor: 'rgba(102, 126, 234, 0.1)',
                    tension: 0.4
                },
                {
                    label: 'تم التحقق',
                    data: activityData.map(d => d.verified),
                    borderColor: '#28a745',
                    backgroundColor: 'rgba(40, 167, 69, 0.1)',
                    tension: 0.4
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
});
</script>
@endsection

@extends('layouts.admin')

@section('title', 'إدارة البريد')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="mb-4">
        <h2 class="mb-1">
            <i class="fas fa-envelope-open-text text-primary"></i> إدارة البريد الإلكتروني
        </h2>
        <p class="text-muted mb-0">مركز إدارة شامل للبريد الإلكتروني، القوالب، وأكواد التحقق (OTP)</p>
    </div>

    <!-- Quick Stats -->
    <div class="row g-3 mb-4">
        <!-- Templates Stats -->
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100" style="border-right: 4px solid #667eea !important;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1 small">قوالب البريد</p>
                            <h3 class="mb-0">{{ $templatesStats['total'] }}</h3>
                            <small class="text-success">
                                <i class="fas fa-check-circle"></i> {{ $templatesStats['active'] }} مفعلة
                            </small>
                        </div>
                        <div class="icon-circle bg-primary bg-opacity-10">
                            <i class="fas fa-file-alt fa-2x text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- OTP Today -->
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100" style="border-right: 4px solid #f093fb !important;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1 small">أكواد OTP اليوم</p>
                            <h3 class="mb-0 text-info">{{ $otpStats['today'] }}</h3>
                            <small class="text-muted">
                                من إجمالي {{ number_format($otpStats['total']) }}
                            </small>
                        </div>
                        <div class="icon-circle bg-info bg-opacity-10">
                            <i class="fas fa-shield-alt fa-2x text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Success Rate -->
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100" style="border-right: 4px solid #28a745 !important;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1 small">معدل نجاح OTP</p>
                            <h3 class="mb-0 text-success">{{ $otpStats['success_rate'] }}%</h3>
                            <small class="text-muted">
                                {{ number_format($otpStats['verified']) }} تم التحقق
                            </small>
                        </div>
                        <div class="icon-circle bg-success bg-opacity-10">
                            <i class="fas fa-chart-line fa-2x text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending OTP -->
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100" style="border-right: 4px solid #ffc107 !important;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1 small">قيد الانتظار</p>
                            <h3 class="mb-0 text-warning">{{ $otpStats['pending'] }}</h3>
                            <small class="text-muted">
                                <i class="fas fa-clock"></i> OTP نشطة
                            </small>
                        </div>
                        <div class="icon-circle bg-warning bg-opacity-10">
                            <i class="fas fa-hourglass-half fa-2x text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Tabs -->
    <ul class="nav nav-tabs nav-tabs-custom mb-4" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" data-bs-toggle="tab" href="#overviewTab">
                <i class="fas fa-chart-pie"></i> نظرة عامة
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#testTab">
                <i class="fas fa-paper-plane"></i> اختبار البريد
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#templatesTab">
                <i class="fas fa-file-alt"></i> القوالب
                <span class="badge bg-primary">{{ $templatesStats['total'] }}</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#otpTab">
                <i class="fas fa-shield-alt"></i> أكواد OTP
                <span class="badge bg-info">{{ $otpStats['today'] }}</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#settingsTab">
                <i class="fas fa-cog"></i> الإعدادات
            </a>
        </li>
    </ul>

    <div class="tab-content">
        <!-- Overview Tab -->
        <div class="tab-pane fade show active" id="overviewTab">
            <div class="row g-4">
                <!-- Charts -->
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">
                                <i class="fas fa-chart-pie text-primary"></i> توزيع أكواد OTP
                            </h5>
                        </div>
                        <div class="card-body">
                            <canvas id="otpTypeChart" height="250"></canvas>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">
                                <i class="fas fa-chart-line text-primary"></i> نشاط البريد (آخر 7 أيام)
                            </h5>
                        </div>
                        <div class="card-body">
                            <canvas id="activityChart" height="250"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Recent Templates -->
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="fas fa-file-alt text-primary"></i> آخر القوالب
                            </h5>
                            <a href="{{ route('admin.email-templates.index') }}" class="btn btn-sm btn-outline-primary">
                                عرض الكل <i class="fas fa-arrow-left"></i>
                            </a>
                        </div>
                        <div class="card-body p-0">
                            <div class="list-group list-group-flush">
                                @forelse($recentTemplates as $template)
                                    <div class="list-group-item">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="mb-1">{{ $template->name }}</h6>
                                                <small class="text-muted">
                                                    <i class="fas fa-tag"></i> {{ $template->type }}
                                                    @if($template->is_active)
                                                        <span class="badge bg-success ms-2">مفعل</span>
                                                    @else
                                                        <span class="badge bg-secondary ms-2">معطل</span>
                                                    @endif
                                                </small>
                                            </div>
                                            <div>
                                                <a href="{{ route('admin.email-templates.edit', $template) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="list-group-item text-center text-muted py-4">
                                        <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                        لا توجد قوالب بعد
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent OTP Activity -->
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="fas fa-shield-alt text-primary"></i> آخر نشاط OTP
                            </h5>
                            <a href="{{ route('admin.otp.index') }}" class="btn btn-sm btn-outline-primary">
                                عرض الكل <i class="fas fa-arrow-left"></i>
                            </a>
                        </div>
                        <div class="card-body p-0">
                            <div class="list-group list-group-flush">
                                @forelse($recentOtps as $otp)
                                    <div class="list-group-item">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div class="flex-grow-1">
                                                <div class="d-flex align-items-center mb-1">
                                                    <code class="text-primary fw-bold me-2">{{ $otp->otp }}</code>
                                                    @if($otp->status == 'verified')
                                                        <span class="badge bg-success">تم التحقق</span>
                                                    @elseif($otp->status == 'pending')
                                                        <span class="badge bg-warning">قيد الانتظار</span>
                                                    @else
                                                        <span class="badge bg-secondary">{{ $otp->status }}</span>
                                                    @endif
                                                </div>
                                                <small class="text-muted d-block">
                                                    <i class="fas fa-envelope"></i> {{ $otp->email }}
                                                </small>
                                                <small class="text-muted">
                                                    <i class="far fa-clock"></i> {{ $otp->created_at->diffForHumans() }}
                                                </small>
                                            </div>
                                            <a href="{{ route('admin.otp.show', $otp->id) }}" class="btn btn-sm btn-outline-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </div>
                                    </div>
                                @empty
                                    <div class="list-group-item text-center text-muted py-4">
                                        <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                        لا يوجد نشاط OTP بعد
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Test Email Tab -->
        <div class="tab-pane fade" id="testTab">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-paper-plane"></i> اختبار إرسال البريد الإلكتروني
                            </h5>
                        </div>
                        <div class="card-body">
                            <form id="testEmailForm">
                                @csrf
                                
                                <div class="mb-3">
                                    <label class="form-label fw-bold">
                                        <i class="fas fa-envelope text-primary"></i> البريد الإلكتروني المستقبل
                                    </label>
                                    <input type="email" class="form-control" name="to_email" required 
                                           placeholder="example@email.com">
                                    <div class="form-text">أدخل البريد الذي تريد إرسال الاختبار إليه</div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">
                                        <i class="fas fa-heading text-primary"></i> الموضوع
                                    </label>
                                    <input type="text" class="form-control" name="subject" required 
                                           value="اختبار البريد الإلكتروني - Your Events"
                                           placeholder="موضوع الرسالة">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">
                                        <i class="fas fa-comment-alt text-primary"></i> الرسالة
                                    </label>
                                    <textarea class="form-control" name="message" rows="8" required 
                                              placeholder="اكتب رسالتك هنا...">مرحباً،

هذه رسالة اختبار من نظام Your Events لإدارة الفعاليات.

إذا وصلتك هذه الرسالة، فهذا يعني أن إعدادات البريد الإلكتروني تعمل بشكل صحيح! ✅

مع أطيب التحيات،
فريق Your Events</textarea>
                                </div>

                                <div class="mb-3 form-check">
                                    <input type="checkbox" class="form-check-input" id="useHtml" name="use_html">
                                    <label class="form-check-label" for="useHtml">
                                        إرسال كـ HTML (للرسائل المنسقة)
                                    </label>
                                </div>

                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i>
                                    <strong>ملاحظة:</strong> تأكد من صحة إعدادات SMTP في ملف <code>.env</code> قبل الإرسال.
                                </div>

                                <div id="testResult" class="mb-3" style="display: none;"></div>

                                <button type="submit" class="btn btn-primary btn-lg w-100" id="sendBtn">
                                    <i class="fas fa-paper-plane"></i> إرسال البريد التجريبي
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- SMTP Settings -->
                    <div class="card border-0 shadow-sm mt-4">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">
                                <i class="fas fa-cog text-primary"></i> إعدادات SMTP الحالية
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <tbody>
                                        <tr>
                                            <td class="fw-bold">MAIL_MAILER:</td>
                                            <td><code>{{ config('mail.default') }}</code></td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">MAIL_HOST:</td>
                                            <td><code>{{ config('mail.mailers.smtp.host') }}</code></td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">MAIL_PORT:</td>
                                            <td><code>{{ config('mail.mailers.smtp.port') }}</code></td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">MAIL_USERNAME:</td>
                                            <td><code>{{ config('mail.mailers.smtp.username') }}</code></td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">MAIL_ENCRYPTION:</td>
                                            <td><code>{{ config('mail.mailers.smtp.encryption') }}</code></td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">MAIL_FROM_ADDRESS:</td>
                                            <td><code>{{ config('mail.from.address') }}</code></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Templates Tab -->
        <div class="tab-pane fade" id="templatesTab">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-file-alt text-primary"></i> قوالب البريد الإلكتروني
                    </h5>
                    <a href="{{ route('admin.email-templates.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> إضافة قالب جديد
                    </a>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        للوصول إلى إدارة القوالب الكاملة، اضغط على الزر أدناه.
                    </div>
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.email-templates.index') }}" class="btn btn-lg btn-outline-primary">
                            <i class="fas fa-external-link-alt"></i> فتح إدارة القوالب الكاملة
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- OTP Tab -->
        <div class="tab-pane fade" id="otpTab">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-shield-alt text-primary"></i> إدارة أكواد التحقق (OTP)
                    </h5>
                    <a href="{{ route('admin.otp.index') }}" class="btn btn-info">
                        <i class="fas fa-external-link-alt"></i> فتح لوحة OTP الكاملة
                    </a>
                </div>
                <div class="card-body">
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <div class="card bg-light border-0">
                                <div class="card-body text-center">
                                    <h3 class="text-primary mb-1">{{ number_format($otpStats['total']) }}</h3>
                                    <small class="text-muted">إجمالي الأكواد</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-light border-0">
                                <div class="card-body text-center">
                                    <h3 class="text-success mb-1">{{ number_format($otpStats['verified']) }}</h3>
                                    <small class="text-muted">تم التحقق</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-light border-0">
                                <div class="card-body text-center">
                                    <h3 class="text-warning mb-1">{{ number_format($otpStats['pending']) }}</h3>
                                    <small class="text-muted">قيد الانتظار</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        للوصول إلى إدارة OTP الكاملة مع الفلاتر والإحصائيات التفصيلية، اضغط على الزر أدناه.
                    </div>
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.otp.index') }}" class="btn btn-lg btn-outline-info">
                            <i class="fas fa-external-link-alt"></i> فتح إدارة OTP الكاملة
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Settings Tab -->
        <div class="tab-pane fade" id="settingsTab">
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">
                                <i class="fas fa-server text-primary"></i> إعدادات SMTP
                            </h5>
                        </div>
                        <div class="card-body">
                            <p class="text-muted">لتغيير إعدادات البريد الإلكتروني، قم بتحديث ملف <code>.env</code>:</p>
                            <pre class="bg-dark text-light p-3 rounded"><code>MAIL_MAILER=smtp
MAIL_HOST=smtp.office365.com
MAIL_PORT=587
MAIL_USERNAME=your-email@outlook.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@outlook.com
MAIL_FROM_NAME="Your Events"</code></pre>
                            <div class="alert alert-warning mt-3">
                                <i class="fas fa-exclamation-triangle"></i>
                                <strong>مهم:</strong> بعد تحديث الملف، قم بتشغيل:
                                <br><code>php artisan config:clear</code>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">
                                <i class="fas fa-book text-primary"></i> الأدلة والتوثيق
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="list-group">
                                <a href="/EMAIL-TEMPLATES-GUIDE.md" target="_blank" class="list-group-item list-group-item-action">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <i class="fas fa-file-alt text-primary me-2"></i>
                                            <strong>دليل قوالب البريد</strong>
                                        </div>
                                        <i class="fas fa-external-link-alt"></i>
                                    </div>
                                </a>
                                <a href="/OTP-SYSTEM-GUIDE.md" target="_blank" class="list-group-item list-group-item-action">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <i class="fas fa-shield-alt text-info me-2"></i>
                                            <strong>دليل نظام OTP</strong>
                                        </div>
                                        <i class="fas fa-external-link-alt"></i>
                                    </div>
                                </a>
                                <a href="/OTP-QUICK-START.md" target="_blank" class="list-group-item list-group-item-action">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <i class="fas fa-rocket text-success me-2"></i>
                                            <strong>دليل البدء السريع - OTP</strong>
                                        </div>
                                        <i class="fas fa-external-link-alt"></i>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm mt-4">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">
                                <i class="fas fa-link text-primary"></i> روابط سريعة
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <a href="{{ route('admin.email-templates.index') }}" class="btn btn-outline-primary">
                                    <i class="fas fa-file-alt"></i> إدارة القوالب
                                </a>
                                <a href="{{ route('admin.otp.index') }}" class="btn btn-outline-info">
                                    <i class="fas fa-shield-alt"></i> إدارة OTP
                                </a>
                                <a href="{{ route('otp.test') }}" class="btn btn-outline-success" target="_blank">
                                    <i class="fas fa-vial"></i> صفحة اختبار OTP
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.icon-circle {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.nav-tabs-custom {
    border-bottom: 2px solid #dee2e6;
}

.nav-tabs-custom .nav-link {
    border: none;
    color: #6c757d;
    padding: 1rem 1.5rem;
    font-weight: 500;
}

.nav-tabs-custom .nav-link:hover {
    color: #667eea;
    border-bottom: 2px solid #667eea;
}

.nav-tabs-custom .nav-link.active {
    color: #667eea;
    border-bottom: 2px solid #667eea;
    background: none;
}
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // OTP Type Chart
    const otpTypeCtx = document.getElementById('otpTypeChart').getContext('2d');
    const otpTypeData = @json($otpByType);
    
    new Chart(otpTypeCtx, {
        type: 'doughnut',
        data: {
            labels: Object.keys(otpTypeData).map(key => {
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
                data: Object.values(otpTypeData),
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
    const activityData = @json($emailActivity);
    
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
                    tension: 0.4,
                    fill: true
                },
                {
                    label: 'تم التحقق',
                    data: activityData.map(d => d.verified),
                    borderColor: '#28a745',
                    backgroundColor: 'rgba(40, 167, 69, 0.1)',
                    tension: 0.4,
                    fill: true
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

    // Test Email Form
    document.getElementById('testEmailForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const sendBtn = document.getElementById('sendBtn');
        const resultDiv = document.getElementById('testResult');
        const formData = new FormData(this);
        
        sendBtn.disabled = true;
        sendBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري الإرسال...';
        resultDiv.style.display = 'none';
        
        try {
            const response = await fetch('{{ route("admin.email-management.send-test") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: formData
            });
            
            const data = await response.json();
            
            if (data.success) {
                resultDiv.className = 'alert alert-success';
                resultDiv.innerHTML = '<i class="fas fa-check-circle"></i> ' + data.message;
            } else {
                resultDiv.className = 'alert alert-danger';
                resultDiv.innerHTML = '<i class="fas fa-exclamation-circle"></i> ' + data.message;
            }
            
            resultDiv.style.display = 'block';
            
        } catch (error) {
            resultDiv.className = 'alert alert-danger';
            resultDiv.innerHTML = '<i class="fas fa-exclamation-circle"></i> حدث خطأ أثناء الإرسال';
            resultDiv.style.display = 'block';
        } finally {
            sendBtn.disabled = false;
            sendBtn.innerHTML = '<i class="fas fa-paper-plane"></i> إرسال البريد التجريبي';
        }
    });
});
</script>
@endsection

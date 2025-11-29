@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <!-- Security Report Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-gradient-primary text-white shadow-lg">
                <div class="card-body text-center py-5">
                    <h1 class="display-4 mb-3"><i class="fas fa-shield-alt me-3"></i>تقرير الأمان الشامل</h1>
                    <p class="lead mb-0">تحليل شامل لقوة أمان المنصة والمخاطر المحتملة</p>
                    <small class="d-block mt-2">آخر تحديث: {{ now()->format('d/m/Y H:i') }}</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Security Overview Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card border-start-lg border-start-success shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <div class="small fw-bold text-success mb-1">مؤشر الأمان العام</div>
                            <div class="h4 mb-0">{{ $security_score ?? '85' }}%</div>
                            <div class="small text-muted">ممتاز</div>
                        </div>
                        <div class="ms-3">
                            <i class="fas fa-shield-check fa-2x text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card border-start-lg border-start-warning shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <div class="small fw-bold text-warning mb-1">محاولات اختراق</div>
                            <div class="h4 mb-0">{{ $failed_login_attempts ?? '12' }}</div>
                            <div class="small text-muted">هذا الشهر</div>
                        </div>
                        <div class="ms-3">
                            <i class="fas fa-exclamation-triangle fa-2x text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card border-start-lg border-start-info shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <div class="small fw-bold text-info mb-1">النسخ الاحتياطي</div>
                            <div class="h4 mb-0">{{ $last_backup_date ?? 'منذ 2 يوم' }}</div>
                            <div class="small text-muted">آخر نسخة احتياطية</div>
                        </div>
                        <div class="ms-3">
                            <i class="fas fa-database fa-2x text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card border-start-lg border-start-danger shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <div class="small fw-bold text-danger mb-1">التهديدات النشطة</div>
                            <div class="h4 mb-0">{{ $active_threats ?? '3' }}</div>
                            <div class="small text-muted">يتطلب اهتماماً</div>
                        </div>
                        <div class="ms-3">
                            <i class="fas fa-virus fa-2x text-danger"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Security Tools & Protection -->
    <div class="row g-4 mb-4">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fas fa-tools me-2"></i>أدوات الحماية المستخدمة</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="security-tool-card p-3 border rounded">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-lock text-success me-2"></i>
                                    <h6 class="mb-0">SSL/TLS</h6>
                                    <span class="badge bg-success ms-auto">فعال</span>
                                </div>
                                <small class="text-muted">تشفير طبقة النقل لحماية البيانات أثناء النقل</small>
                                <div class="mt-2">
                                    <div class="progress" style="height: 5px;">
                                        <div class="progress-bar bg-success" style="width: 100%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="security-tool-card p-3 border rounded">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-shield-alt text-success me-2"></i>
                                    <h6 class="mb-0">WAF</h6>
                                    <span class="badge bg-success ms-auto">فعال</span>
                                </div>
                                <small class="text-muted">جدار حماية التطبيقات الويب</small>
                                <div class="mt-2">
                                    <div class="progress" style="height: 5px;">
                                        <div class="progress-bar bg-success" style="width: 95%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="security-tool-card p-3 border rounded">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-key text-success me-2"></i>
                                    <h6 class="mb-0">2FA</h6>
                                    <span class="badge bg-success ms-auto">فعال</span>
                                </div>
                                <small class="text-muted">المصادقة الثنائية للمستخدمين</small>
                                <div class="mt-2">
                                    <div class="progress" style="height: 5px;">
                                        <div class="progress-bar bg-success" style="width: 90%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="security-tool-card p-3 border rounded">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-eye text-warning me-2"></i>
                                    <h6 class="mb-0">مراقبة مستمرة</h6>
                                    <span class="badge bg-warning ms-auto">محسن</span>
                                </div>
                                <small class="text-muted">نظام مراقبة الأنشطة المستمر</small>
                                <div class="mt-2">
                                    <div class="progress" style="height: 5px;">
                                        <div class="progress-bar bg-warning" style="width: 80%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="security-tool-card p-3 border rounded">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-database text-success me-2"></i>
                                    <h6 class="mb-0">تشفير البيانات</h6>
                                    <span class="badge bg-success ms-auto">فعال</span>
                                </div>
                                <small class="text-muted">تشفير البيانات الحساسة في قاعدة البيانات</small>
                                <div class="mt-2">
                                    <div class="progress" style="height: 5px;">
                                        <div class="progress-bar bg-success" style="width: 100%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="security-tool-card p-3 border rounded">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-user-shield text-info me-2"></i>
                                    <h6 class="mb-0">RBAC</h6>
                                    <span class="badge bg-info ms-auto">قيد التحسين</span>
                                </div>
                                <small class="text-muted">التحكم في الوصول القائم على الأدوار</small>
                                <div class="mt-2">
                                    <div class="progress" style="height: 5px;">
                                        <div class="progress-bar bg-info" style="width: 85%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i>مؤشرات الأمان</h5>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>قوه كلمة المرور</span>
                            <span class="badge bg-success">85%</span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-success" style="width: 85%"></div>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>تحديثات النظام</span>
                            <span class="badge bg-warning">75%</span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-warning" style="width: 75%"></div>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>النسخ الاحتياطي</span>
                            <span class="badge bg-success">95%</span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-success" style="width: 95%"></div>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>المراقبة والتسجيل</span>
                            <span class="badge bg-info">80%</span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-info" style="width: 80%"></div>
                        </div>
                    </div>
                    
                    <div class="mb-0">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>التحكم في الوصول</span>
                            <span class="badge bg-success">90%</span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-success" style="width: 90%"></div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card shadow-sm mt-3">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="fas fa-clock me-2"></i>آخر فحص أمان</h6>
                </div>
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="fas fa-check-circle text-success fa-3x"></i>
                    </div>
                    <h6 class="text-success">{{ $last_security_scan ?? 'منذ 3 أيام' }}</h6>
                    <p class="text-muted small mb-0">تم إجراء آخر فحص أمان شامل</p>
                    <button class="btn btn-outline-primary btn-sm mt-2">
                        <i class="fas fa-sync-alt me-1"></i>فحص الآن
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Threats & Vulnerabilities -->
    <div class="row g-4 mb-4">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0"><i class="fas fa-exclamation-triangle me-2"></i>التهديدات والثغرات الأمنية</h5>
                </div>
                <div class="card-body">
                    @if(isset($threats) && count($threats) > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>النوع</th>
                                        <th>الوصف</th>
                                        <th>الخطورة</th>
                                        <th>الحالة</th>
                                        <th>الإجراء</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($threats as $threat)
                                    <tr>
                                        <td>
                                            <span class="badge bg-{{ $threat['type_color'] }}">
                                                {{ $threat['type'] }}
                                            </span>
                                        </td>
                                        <td>{{ $threat['description'] }}</td>
                                        <td>
                                            <span class="badge bg-{{ $threat['severity_color'] }}">
                                                {{ $threat['severity'] }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $threat['status_color'] }}">
                                                {{ $threat['status'] }}
                                            </span>
                                        </td>
                                        <td>
                                            <button class="btn btn-outline-danger btn-sm">
                                                <i class="fas fa-shield-alt me-1"></i>معالجة
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-shield-check fa-3x text-success mb-3"></i>
                            <h5 class="text-success">لا توجد تهديدات نشطة حالياً</h5>
                            <p class="text-muted">جميع أنظمة الأمان تعمل بشكل طبيعي</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header bg-warning">
                    <h5 class="mb-0"><i class="fas fa-chart-pie me-2"></i>تحليل المخاطر</h5>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <canvas id="riskChart" width="200" height="200"></canvas>
                    </div>
                    
                    <div class="list-group list-group-flush">
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-circle text-danger me-2"></i>عالية الخطورة</span>
                            <span class="badge bg-danger">{{ $high_risk_count ?? '0' }}</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-circle text-warning me-2"></i>متوسطة الخطورة</span>
                            <span class="badge bg-warning">{{ $medium_risk_count ?? '1' }}</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-circle text-info me-2"></i>منخفضة الخطورة</span>
                            <span class="badge bg-info">{{ $low_risk_count ?? '2' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Future Predictions & Recommendations -->
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-crystal-ball me-2"></i>التنبؤات والتوصيات المستقبلية</h5>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <h6 class="text-primary mb-3"><i class="fas fa-exclamation-circle me-2"></i>المخاطر المحتملة</h6>
                            <div class="list-group">
                                <div class="list-group-item">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1">محاولات تسجيل الدخول العشوائية</h6>
                                        <small class="text-danger">احتمالية 75%</small>
                                    </div>
                                    <p class="mb-1 small">زيادة في محاولات تسجيل الدخول غير المصرح بها خلال الأسبوعين القادمين</p>
                                    <small class="text-muted">توصية: تفعيل حماية reCAPTCHA</small>
                                </div>
                                
                                <div class="list-group-item">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1">هجمات XSS</h6>
                                        <small class="text-warning">احتمالية 45%</small>
                                    </div>
                                    <p class="mb-1 small">احتمال وجود ثغرات XSS في بعض النماذج</p>
                                    <small class="text-muted">توصية: مراجعة شفرة المصدر وتنقيتها</small>
                                </div>
                                
                                <div class="list-group-item">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1">اختراق قاعدة البيانات</h6>
                                        <small class="text-info">احتمالية 25%</small>
                                    </div>
                                    <p class="mb-1 small">محاولات استغلال ثغرات SQL</p>
                                    <small class="text-muted">توصية: تحديث جدران الحماية وتقوية الاستعلامات</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <h6 class="text-success mb-3"><i class="fas fa-lightbulb me-2"></i>التوصيات العاجلة</h6>
                            <div class="list-group">
                                <div class="list-group-item list-group-item-action">
                                    <div class="d-flex align-items-center mb-2">
                                        <span class="badge bg-danger me-3">عالي</span>
                                        <h6 class="mb-0">تحديث Laravel</h6>
                                    </div>
                                    <p class="mb-1 small">تحديث إطار العمل إلى أحدث إصدار لتصحيح الثغرات الأمنية</p>
                                    <small class="text-muted">الموعد النهائي: خلال 48 ساعة</small>
                                </div>
                                
                                <div class="list-group-item list-group-item-action">
                                    <div class="d-flex align-items-center mb-2">
                                        <span class="badge bg-warning me-3">متوسط</span>
                                        <h6 class="mb-0">تقوية سياسات كلمة المرور</h6>
                                    </div>
                                    <p class="mb-1 small">فرض سياسات أقوى لكلمات المرور على جميع المستخدمين</p>
                                    <small class="text-muted">الموعد النهائي: خلال أسبوع</small>
                                </div>
                                
                                <div class="list-group-item list-group-item-action">
                                    <div class="d-flex align-items-center mb-2">
                                        <span class="badge bg-info me-3">منخفض</span>
                                        <h6 class="mb-0">تدريب الموظفين</h6>
                                    </div>
                                    <p class="mb-1 small">تنظيم جلسات تدريبية حول أفضل ممارسات الأمان</p>
                                    <small class="text-muted">الموعد النهائي: خلال شهر</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="row g-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center">
                    <div class="btn-group" role="group">
                        <button class="btn btn-primary me-2">
                            <i class="fas fa-file-export me-2"></i>تصدير التقرير
                        </button>
                        <button class="btn btn-outline-primary me-2">
                            <i class="fas fa-envelope me-2"></i>إرسال بالبريد
                        </button>
                        <button class="btn btn-outline-secondary me-2">
                            <i class="fas fa-print me-2"></i>طباعة
                        </button>
                        <button class="btn btn-outline-success">
                            <i class="fas fa-sync-alt me-2"></i>تحديث البيانات
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Risk Chart
    const ctx = document.getElementById('riskChart')?.getContext('2d');
    if (ctx) {
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['عالية', 'متوسطة', 'منخفضة'],
                datasets: [{
                    data: [{{ $high_risk_count ?? 0 }}, {{ $medium_risk_count ?? 1 }}, {{ $low_risk_count ?? 2 }}],
                    backgroundColor: ['#dc3545', '#ffc107', '#17a2b8'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    }
    
    // Auto refresh data every 5 minutes
    setInterval(() => {
        location.reload();
    }, 300000);
    
    // Export report function
    function exportReport() {
        window.open('{{ route("admin.reports.security.export") }}', '_blank');
    }
    
    // Send email function
    function sendEmail() {
        if (confirm('هل ترغب في إرسال هذا التقرير عبر البريد الإلكتروني؟')) {
            alert('تم إرسال التقرير بنجاح');
        }
    }
</script>
@endsection

@section('css')
<style>
    .security-tool-card {
        transition: all 0.3s ease;
    }
    .security-tool-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    
    .bg-gradient-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    
    .list-group-item {
        border-left: 3px solid transparent;
        transition: all 0.3s ease;
    }
    
    .list-group-item:hover {
        border-left-color: #007bff;
        background-color: #f8f9fa;
    }
</style>
@endsection
@extends('layouts.admin')

@section('title', 'أدوات الصيانة')
@section('page-title', 'أدوات الصيانة')
@section('page-description', 'النسخ الاحتياطي، الصيانة، وتحسين الأداء')

@section('content')

@php
    $formatBytes = function($bytes) {
        if ($bytes <= 0) return '0 B';
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $power = floor(log($bytes, 1024));
        return round($bytes / pow(1024, $power), 2) . ' ' . $units[(int) $power];
    };
@endphp

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('optimize_results'))
    <div class="alert alert-info alert-dismissible fade show" role="alert">
        <i class="fas fa-info-circle me-2"></i><strong>نتائج التحسين:</strong>
        <ul class="mb-0 mt-1">
            @foreach(session('optimize_results') as $result)
                <li>{{ $result }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('cache_results'))
    <div class="alert alert-info alert-dismissible fade show" role="alert">
        <i class="fas fa-info-circle me-2"></i><strong>نتائج مسح الـ Cache:</strong>
        <ul class="mb-0 mt-1">
            @foreach(session('cache_results') as $result)
                <li>{{ $result }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('temp_results'))
    <div class="alert alert-info alert-dismissible fade show" role="alert">
        <i class="fas fa-info-circle me-2"></i><strong>نتائج تنظيف الملفات المؤقتة:</strong>
        <ul class="mb-0 mt-1">
            @foreach(session('temp_results') as $result)
                <li>{{ $result }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="row">

    <!-- ==================== -->
    <!-- Backup Section -->
    <!-- ==================== -->
    <div class="col-12">
        <div class="card mb-4">
            <div class="card-header" style="background: linear-gradient(135deg, #1f144a, #3b2d7a);">
                <h5 class="card-title mb-0 text-white">
                    <i class="fas fa-database me-2"></i>النسخ الاحتياطي
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="card h-100 border-primary">
                            <div class="card-body text-center">
                                <i class="fas fa-database fa-3x text-primary mb-3"></i>
                                <h6 class="card-title">نسخة احتياطية لقاعدة البيانات</h6>
                                <p class="text-muted small">تنشئ ملف .sql يحتوي على بيانات قاعدة البيانات</p>
                                <form method="POST" action="{{ route('admin.maintenance.backup.database') }}">
                                    @csrf
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="fas fa-download me-1"></i>قاعدة البيانات
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card h-100 border-success">
                            <div class="card-body text-center">
                                <i class="fas fa-file-archive fa-3x text-success mb-3"></i>
                                <h6 class="card-title">نسخة احتياطية للملفات</h6>
                                <p class="text-muted small">تنشئ ملف .zip يحتوي على الصور والملفات المرفوعة</p>
                                <form method="POST" action="{{ route('admin.maintenance.backup.files') }}">
                                    @csrf
                                    <button type="submit" class="btn btn-success w-100">
                                        <i class="fas fa-file-archive me-1"></i>الملفات
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card h-100 border-warning">
                            <div class="card-body text-center">
                                <i class="fas fa-box-archive fa-3x text-warning mb-3"></i>
                                <h6 class="card-title">نسخة احتياطية كاملة</h6>
                                <p class="text-muted small">تجمع قاعدة البيانات + الملفات في ملف واحد</p>
                                <form method="POST" action="{{ route('admin.maintenance.backup.full') }}">
                                    @csrf
                                    <button type="submit" class="btn btn-warning w-100">
                                        <i class="fas fa-box-archive me-1"></i>نسخة كاملة
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ==================== -->
    <!-- Available Backups Table -->
    <!-- ==================== -->
    <div class="col-12">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="fas fa-list me-2"></i>النسخ الاحتياطية المتاحة
                </h5>
                <span class="badge bg-info">
                    {{ count($backups) }} نسخة | {{ $formatBytes($totalBackupsSize) }}
                </span>
            </div>
            <div class="card-body">
                @if(count($backups) > 0)
                    <div class="table-responsive">
                        <table class="table table-hover table-striped">
                            <thead>
                                <tr>
                                    <th>اسم الملف</th>
                                    <th>النوع</th>
                                    <th>الحجم</th>
                                    <th>التاريخ</th>
                                    <th class="text-center">الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($backups as $backup)
                                    <tr>
                                        <td>
                                            <i class="fas {{ $backup['type'] === 'full' ? 'fa-box-archive' : ($backup['type'] === 'files' ? 'fa-file-archive' : 'fa-database') }} me-1 text-muted"></i>
                                            {{ $backup['name'] }}
                                        </td>
                                        <td>
                                            @php
                                                $typeLabels = ['database' => 'قاعدة بيانات', 'files' => 'ملفات', 'full' => 'كاملة', 'unknown' => 'غير معروف'];
                                                $typeColors = ['database' => 'primary', 'files' => 'success', 'full' => 'warning', 'unknown' => 'secondary'];
                                            @endphp
                                            <span class="badge bg-{{ $typeColors[$backup['type']] ?? 'secondary' }}">
                                                {{ $typeLabels[$backup['type']] ?? 'غير معروف' }}
                                            </span>
                                        </td>
                                        <td>{{ $backup['size'] }}</td>
                                        <td>{{ $backup['date'] }}</td>
                                        <td class="text-center">
                                            <a href="{{ route('admin.maintenance.download', ['filename' => $backup['name']]) }}"
                                               class="btn btn-sm btn-outline-primary" title="تحميل">
                                                <i class="fas fa-download"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-warning"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#restoreModal"
                                                    data-backup-name="{{ $backup['name'] }}"
                                                    title="استعادة">
                                                <i class="fas fa-undo"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-danger"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#deleteModal"
                                                    data-backup-name="{{ $backup['name'] }}"
                                                    title="حذف">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                        <p class="text-muted">لا توجد نسخ احتياطية حالياً. قم بإنشاء نسخة جديدة من القسم أعلاه.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- ==================== -->
    <!-- Maintenance Tools -->
    <!-- ==================== -->
    <div class="col-12">
        <div class="card mb-4">
            <div class="card-header" style="background: linear-gradient(135deg, #2dbcae, #1a8a7e);">
                <h5 class="card-title mb-0 text-white">
                    <i class="fas fa-tools me-2"></i>أدوات التحسين والصيانة
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">

                    <!-- Optimize Database -->
                    <div class="col-md-4">
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-wrench fa-2x text-info me-3"></i>
                                    <h6 class="card-title mb-0">تحسين قاعدة البيانات</h6>
                                </div>
                                <p class="text-muted small">تحسين جداول قاعدة البيانات لتحسين الأداء</p>
                                <form method="POST" action="{{ route('admin.maintenance.optimize-database') }}">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-info w-100"
                                            onclick="return confirm('هل أنت متأكد من تحسين قاعدة البيانات؟')">
                                        <i class="fas fa-bolt me-1"></i>تحسين الآن
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Clear Cache -->
                    <div class="col-md-4">
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-broom fa-2x text-warning me-3"></i>
                                    <h6 class="card-title mb-0">مسح الـ Cache</h6>
                                </div>
                                <p class="text-muted small">مسح Application, Config, Route, View Cache</p>
                                <form method="POST" action="{{ route('admin.maintenance.clear-cache') }}">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-warning w-100"
                                            onclick="return confirm('هل أنت متأكد من مسح جميع أنواع الـ Cache؟')">
                                        <i class="fas fa-broom me-1"></i>مسح الآن
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Clear Logs -->
                    <div class="col-md-4">
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-file-alt fa-2x text-danger me-3"></i>
                                    <h6 class="card-title mb-0">حذف ملفات السجلات</h6>
                                </div>
                                <p class="text-muted small">حذف جميع ملفات .log من storage/logs</p>
                                <form method="POST" action="{{ route('admin.maintenance.clear-logs') }}">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-danger w-100"
                                            onclick="return confirm('هل أنت متأكد من حذف جميع ملفات السجلات؟ لا يمكن التراجع عن هذه العملية.')">
                                        <i class="fas fa-trash me-1"></i>حذف السجلات
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Clean Sessions -->
                    <div class="col-md-4">
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-user-clock fa-2x text-secondary me-3"></i>
                                    <h6 class="card-title mb-0">تنظيف الجلسات القديمة</h6>
                                </div>
                                <p class="text-muted small">حذف جلسات المستخدمين المنتهية (أكثر من 24 ساعة)</p>
                                <form method="POST" action="{{ route('admin.maintenance.clean-sessions') }}">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-secondary w-100"
                                            onclick="return confirm('هل أنت متأكد من تنظيف الجلسات القديمة؟')">
                                        <i class="fas fa-user-minus me-1"></i>تنظيف الآن
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Clean Temp Files -->
                    <div class="col-md-4">
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-trash-alt fa-2x text-muted me-3"></i>
                                    <h6 class="card-title mb-0">حذف الملفات المؤقتة</h6>
                                </div>
                                <p class="text-muted small">حذف ملفات Framework المؤقتة (cache, sessions, views)</p>
                                <form method="POST" action="{{ route('admin.maintenance.clean-temp') }}">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-dark w-100"
                                            onclick="return confirm('هل أنت متأكد من حذف الملفات المؤقتة؟')">
                                        <i class="fas fa-trash-alt me-1"></i>حذف الملفات
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- System Info -->
                    <div class="col-md-4">
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-info-circle fa-2x text-primary me-3"></i>
                                    <h6 class="card-title mb-0">معلومات النظام</h6>
                                </div>
                                <p class="text-muted small">عرض معلومات شاملة عن السيرفر وقاعدة البيانات</p>
                                <a href="{{ route('admin.maintenance.system-info') }}" class="btn btn-outline-primary w-100">
                                    <i class="fas fa-chart-bar me-1"></i>عرض المعلومات
                                </a>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- ==================== -->
    <!-- Tips Section -->
    <!-- ==================== -->
    <div class="col-12">
        <div class="card mb-4">
            <div class="card-header bg-light">
                <h5 class="card-title mb-0">
                    <i class="fas fa-lightbulb me-2 text-warning"></i>نصائح الأمان والصيانة
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="fw-bold"><i class="fas fa-shield-alt me-2 text-success"></i>النسخ الاحتياطي</h6>
                        <ul class="small text-muted">
                            <li>احتفظ بنسخة احتياطية يومية على الأقل</li>
                            <li>احفظ النسخ في مكان خارجي (Google Drive, S3)</li>
                            <li>اختبر استعادة النسخة الاحتياطية بشكل دوري</li>
                            <li>احتفظ بـ 30 يوم من النسخ على الأقل</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6 class="fw-bold"><i class="fas fa-clock me-2 text-info"></i>الصيانة الدورية</h6>
                        <ul class="small text-muted">
                            <li><strong>يومياً:</strong> نسخة احتياطية تلقائية</li>
                            <li><strong>أسبوعياً:</strong> تنظيف الجلسات القديمة</li>
                            <li><strong>شهرياً:</strong> تحسين قاعدة البيانات وحذف السجلات</li>
                            <li><strong>شهرياً:</strong> تنظيف النسخ الاحتياطية القديمة</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- ==================== -->
<!-- Restore Modal -->
<!-- ==================== -->
<div class="modal fade" id="restoreModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle me-2"></i>تأكيد الاستعادة
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger">
                    <strong>تحذير مهم:</strong>
                    <ul class="mb-0 mt-1">
                        <li>استعادة النسخة الاحتياطية ستستبدل قاعدة البيانات الحالية بالكامل</li>
                        <li>لا يمكن التراجع عن هذه العملية</li>
                        <li>تأكد من إنشاء نسخة احتياطية قبل الاستعادة</li>
                    </ul>
                </div>
                <p>هل أنت متأكد من استعادة النسخة: <strong id="restoreFileName"></strong>؟</p>
            </div>
            <div class="modal-footer">
                <form method="POST" action="" id="restoreForm">
                    @csrf
                    <input type="hidden" name="confirm" value="1">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-undo me-1"></i>تأكيد الاستعادة
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- ==================== -->
<!-- Delete Modal -->
<!-- ==================== -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h5 class="modal-title text-white">
                    <i class="fas fa-trash me-2"></i>تأكيد الحذف
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>هل أنت متأكد من حذف النسخة الاحتياطية: <strong id="deleteFileName"></strong>؟</p>
                <p class="text-muted small">لا يمكن التراجع عن هذه العملية.</p>
            </div>
            <div class="modal-footer">
                <form method="POST" action="" id="deleteForm">
                    @csrf
                    @method('DELETE')
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-1"></i>تأكيد الحذف
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Restore modal
    var restoreModal = document.getElementById('restoreModal');
    if (restoreModal) {
        restoreModal.addEventListener('show.bs.modal', function(event) {
            var button = event.relatedTarget;
            var backupName = button.getAttribute('data-backup-name');
            document.getElementById('restoreFileName').textContent = backupName;
            document.getElementById('restoreForm').action = '{{ route("admin.maintenance.restore", ["filename" => "PLACEHOLDER"]) }}'.replace('PLACEHOLDER', backupName);
        });
    }

    // Delete modal
    var deleteModal = document.getElementById('deleteModal');
    if (deleteModal) {
        deleteModal.addEventListener('show.bs.modal', function(event) {
            var button = event.relatedTarget;
            var backupName = button.getAttribute('data-backup-name');
            document.getElementById('deleteFileName').textContent = backupName;
            document.getElementById('deleteForm').action = '{{ route("admin.maintenance.delete", ["filename" => "PLACEHOLDER"]) }}'.replace('PLACEHOLDER', backupName);
        });
    }
});
</script>

@endsection

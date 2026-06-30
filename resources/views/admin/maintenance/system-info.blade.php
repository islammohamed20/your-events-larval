@extends('layouts.admin')

@section('title', 'معلومات النظام')
@section('page-title', 'معلومات النظام')
@section('page-description', 'معلومات شاملة عن السيرفر وقاعدة البيانات')

@section('content')

<div class="row">

    <!-- Server Info -->
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header" style="background: linear-gradient(135deg, #1f144a, #3b2d7a);">
                <h5 class="card-title mb-0 text-white">
                    <i class="fas fa-server me-2"></i>معلومات السيرفر
                </h5>
            </div>
            <div class="card-body">
                <table class="table table-sm table-borderless">
                    <tbody>
                        <tr>
                            <td class="fw-bold text-muted" style="width: 40%;">نظام التشغيل</td>
                            <td>{{ $info['server']['os'] }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">إصدار PHP</td>
                            <td><span class="badge bg-success">{{ $info['server']['php_version'] }}</span></td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">إصدار Laravel</td>
                            <td><span class="badge bg-info">{{ $info['server']['laravel_version'] }}</span></td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">خادم الويب</td>
                            <td>{{ $info['server']['web_server'] }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">IP السيرفر</td>
                            <td>{{ $info['server']['server_ip'] }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">وقت السيرفر</td>
                            <td>{{ $info['server']['server_time'] }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Database Info -->
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header" style="background: linear-gradient(135deg, #2dbcae, #1a8a7e);">
                <h5 class="card-title mb-0 text-white">
                    <i class="fas fa-database me-2"></i>معلومات قاعدة البيانات
                </h5>
            </div>
            <div class="card-body">
                <table class="table table-sm table-borderless">
                    <tbody>
                        <tr>
                            <td class="fw-bold text-muted" style="width: 40%;">النوع</td>
                            <td><span class="badge bg-primary">{{ $info['database']['driver'] }}</span></td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">الإصدار</td>
                            <td>{{ $info['database']['version'] }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">اسم قاعدة البيانات</td>
                            <td>{{ $info['database']['database_name'] }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">المضيف (Host)</td>
                            <td>{{ $info['database']['database_host'] }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">حجم قاعدة البيانات</td>
                            <td><span class="badge bg-warning text-dark">{{ $info['database']['database_size'] }}</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Storage Info -->
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header bg-light">
                <h5 class="card-title mb-0">
                    <i class="fas fa-hdd me-2"></i>معلومات التخزين
                </h5>
            </div>
            <div class="card-body">
                <table class="table table-sm table-borderless">
                    <tbody>
                        <tr>
                            <td class="fw-bold text-muted" style="width: 40%;">المساحة الكلية</td>
                            <td>{{ $info['storage']['total_space'] }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">المساحة المتاحة</td>
                            <td><span class="text-success fw-bold">{{ $info['storage']['free_space'] }}</span></td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">المساحة المستخدمة</td>
                            <td><span class="text-warning fw-bold">{{ $info['storage']['used_space'] }}</span></td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">حجم ملفات التطبيق</td>
                            <td>{{ $info['storage']['app_size'] }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">عدد النسخ الاحتياطية</td>
                            <td>{{ $info['storage']['backups_count'] }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">حجم النسخ الاحتياطية</td>
                            <td>{{ $info['storage']['backups_size'] }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- PHP Settings -->
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header bg-light">
                <h5 class="card-title mb-0">
                    <i class="fas fa-cog me-2"></i>إعدادات PHP
                </h5>
            </div>
            <div class="card-body">
                <table class="table table-sm table-borderless">
                    <tbody>
                        <tr>
                            <td class="fw-bold text-muted" style="width: 40%;">Memory Limit</td>
                            <td>{{ $info['php']['memory_limit'] }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Max Execution Time</td>
                            <td>{{ $info['php']['max_execution_time'] }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Upload Max Filesize</td>
                            <td>{{ $info['php']['upload_max_filesize'] }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Post Max Size</td>
                            <td>{{ $info['php']['post_max_size'] }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- PHP Extensions -->
    <div class="col-12">
        <div class="card mb-4">
            <div class="card-header bg-light">
                <h5 class="card-title mb-0">
                    <i class="fas fa-puzzle-piece me-2"></i>امتدادات PHP
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-2">
                    @php
                        $extensions = [
                            'gd' => ['GD', 'للصور'],
                            'curl' => ['cURL', 'للطلبات الخارجية'],
                            'zip' => ['ZIP', 'للضغط'],
                            'mbstring' => ['mbstring', 'للنصوص'],
                            'pdo' => ['PDO', 'لقاعدة البيانات'],
                            'pdo_mysql' => ['PDO MySQL', 'لقاعدة بيانات MySQL'],
                            'pdo_sqlite' => ['PDO SQLite', 'لقاعدة بيانات SQLite'],
                            'openssl' => ['OpenSSL', 'للتشفير'],
                            'fileinfo' => ['Fileinfo', 'لمعلومات الملفات'],
                        ];
                    @endphp
                    @foreach($extensions as $key => $ext)
                        <div class="col-md-4">
                            <div class="d-flex align-items-center">
                                @if($info['extensions'][$key])
                                    <i class="fas fa-check-circle text-success me-2"></i>
                                @else
                                    <i class="fas fa-times-circle text-danger me-2"></i>
                                @endif
                                <div>
                                    <strong>{{ $ext[0] }}</strong>
                                    <small class="text-muted d-block">{{ $ext[1] }}</small>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Back Button -->
    <div class="col-12">
        <a href="{{ route('admin.maintenance.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-right me-1"></i>العودة إلى أدوات الصيانة
        </a>
    </div>

</div>

@endsection

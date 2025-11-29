@extends('layouts.admin')

@section('title', 'تقرير الأمان')
@section('page-title', 'تقرير الأمان')
@section('page-description', 'نظرة عامة على سجلات الدخول ونشاط OTP')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h1 class="h4 mb-0"><i class="fas fa-user-shield me-2"></i>تقرير الأمان</h1>
            <div>
                <a href="{{ route('admin.reports.index') }}" class="btn btn-secondary">
                    <i class="fas fa-chart-line"></i> لوحة التقارير
                </a>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Summary Cards -->
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted">محاولات تسجيل الدخول</div>
                            <div class="h4">{{ $loginSummary['total'] }}</div>
                        </div>
                        <i class="fas fa-sign-in-alt text-primary fa-2x"></i>
                    </div>
                    <div class="mt-2 small">
                        ناجحة: {{ $loginSummary['successful'] }} | فاشلة: {{ $loginSummary['failed'] }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted">إجمالي OTP</div>
                            <div class="h4">{{ $otpSummary['total'] }}</div>
                        </div>
                        <i class="fas fa-shield-alt text-warning fa-2x"></i>
                    </div>
                    <div class="mt-2 small">
                        نجاح: {{ $otpSummary['verified'] }} | منتهٍ: {{ $otpSummary['expired'] }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="text-muted">معدل نجاح OTP</div>
                    <div class="h4">{{ $otpSummary['success_rate'] }}%</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="text-muted">الفترة</div>
                    <div class="h6">{{ $startDate->format('Y-m-d') }} → {{ $endDate->format('Y-m-d') }}</div>
                </div>
            </div>
        </div>

        <!-- Login Timeline -->
        <div class="col-12">
            <div class="card">
                <div class="card-header"><h6 class="mb-0">المخطط الزمني لتسجيل الدخول</h6></div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>التاريخ</th>
                                    <th>إجمالي</th>
                                    <th>ناجحة</th>
                                    <th>فاشلة</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($loginTimeline as $row)
                                    <tr>
                                        <td>{{ $row->date }}</td>
                                        <td>{{ $row->total }}</td>
                                        <td>{{ $row->successful }}</td>
                                        <td>{{ $row->failed }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">لا توجد بيانات للفترة المحددة</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- OTP Breakdown -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header"><h6 class="mb-0">OTP حسب النوع</h6></div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>النوع</th>
                                    <th>العدد</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($otpSummary['by_type'] as $row)
                                    <tr>
                                        <td>{{ $row->type }}</td>
                                        <td>{{ $row->count }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="text-center text-muted">لا توجد بيانات</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header"><h6 class="mb-0">OTP حسب الحالة</h6></div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>الحالة</th>
                                    <th>العدد</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($otpSummary['by_status'] as $row)
                                    <tr>
                                        <td>{{ $row->status }}</td>
                                        <td>{{ $row->count }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="text-center text-muted">لا توجد بيانات</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top failed IPs -->
        <div class="col-12">
            <div class="card">
                <div class="card-header"><h6 class="mb-0">أكثر عناوين IP فشلًا</h6></div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>عنوان IP</th>
                                    <th>عدد المحاولات الفاشلة</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topFailedIps as $row)
                                    <tr>
                                        <td>{{ $row->ip_address ?? 'غير معروف' }}</td>
                                        <td>{{ $row->fails }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="text-center text-muted">لا توجد بيانات فشل</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


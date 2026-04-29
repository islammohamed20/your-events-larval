@extends('layouts.admin')

@section('title', 'تقارير الواتساب')
@section('page-title', 'تقارير الواتساب')
@section('page-description', 'إحصائيات وتحليلات الأداء المتزامنة مع نظام Faalwa')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-chart-pie text-primary me-2"></i> ملخص أداء الموظفين (Flow Agent Summary)</h5>
                
                <form action="{{ route('admin.whatsapp.reports.index') }}" method="GET" class="d-flex">
                    <select name="range" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="yesterday" {{ $range == 'yesterday' ? 'selected' : '' }}>الأمس</option>
                        <option value="last_7_days" {{ $range == 'last_7_days' ? 'selected' : '' }}>آخر 7 أيام</option>
                        <option value="last_week" {{ $range == 'last_week' ? 'selected' : '' }}>الأسبوع الماضي</option>
                        <option value="last_30_days" {{ $range == 'last_30_days' ? 'selected' : '' }}>آخر 30 يوم</option>
                        <option value="last_month" {{ $range == 'last_month' ? 'selected' : '' }}>الشهر الماضي</option>
                        <option value="last_3_months" {{ $range == 'last_3_months' ? 'selected' : '' }}>آخر 3 أشهر</option>
                    </select>
                </form>
            </div>
            <div class="card-body">
                @if(empty($summary))
                    <div class="text-center py-5 text-muted">
                        <i class="fas fa-chart-bar fa-3x mb-3 text-light"></i>
                        <h5>لا توجد بيانات</h5>
                        <p>لا توجد بيانات إحصائية متاحة للفترة المحددة.</p>
                    </div>
                @else
                    <div class="row g-4 mb-5">
                        {{-- إجمالي المحادثات --}}
                        <div class="col-md-3">
                            <div class="border rounded p-3 text-center h-100 bg-light">
                                <h6 class="text-muted mb-2">إجمالي المحادثات</h6>
                                <h3 class="mb-0 text-primary">{{ $summary['total_conversations'] ?? 0 }}</h3>
                            </div>
                        </div>
                        
                        {{-- المحادثات المفتوحة --}}
                        <div class="col-md-3">
                            <div class="border rounded p-3 text-center h-100 bg-light">
                                <h6 class="text-muted mb-2">محادثات مفتوحة</h6>
                                <h3 class="mb-0 text-danger">{{ $summary['open_conversations'] ?? 0 }}</h3>
                            </div>
                        </div>

                        {{-- المحادثات المغلقة --}}
                        <div class="col-md-3">
                            <div class="border rounded p-3 text-center h-100 bg-light">
                                <h6 class="text-muted mb-2">محادثات مغلقة</h6>
                                <h3 class="mb-0 text-success">{{ $summary['closed_conversations'] ?? 0 }}</h3>
                            </div>
                        </div>

                        {{-- وقت الاستجابة --}}
                        <div class="col-md-3">
                            <div class="border rounded p-3 text-center h-100 bg-light">
                                <h6 class="text-muted mb-2">متوسط وقت الاستجابة</h6>
                                <h3 class="mb-0 text-info">
                                    {{ isset($summary['avg_response_time']) ? round($summary['avg_response_time'] / 60, 1) . ' دقيقة' : '-' }}
                                </h3>
                            </div>
                        </div>
                    </div>

                    <h6 class="mb-3">أداء الموظفين التفصيلي</h6>
                    <div class="table-responsive border rounded">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>الموظف</th>
                                    <th class="text-center">إجمالي المحادثات</th>
                                    <th class="text-center">مفتوحة</th>
                                    <th class="text-center">مغلقة</th>
                                    <th class="text-center">الرسائل المرسلة</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(isset($summary['agents']) && is_array($summary['agents']))
                                    @foreach($summary['agents'] as $agent)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar avatar-sm me-3 bg-secondary-subtle text-secondary rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width:30px; height:30px;">
                                                    <i class="fas fa-user"></i>
                                                </div>
                                                <span class="fw-semibold">{{ $agent['name'] ?? 'مجهول' }}</span>
                                            </div>
                                        </td>
                                        <td class="text-center">{{ $agent['total_conversations'] ?? 0 }}</td>
                                        <td class="text-center"><span class="badge bg-danger-subtle text-danger">{{ $agent['open_conversations'] ?? 0 }}</span></td>
                                        <td class="text-center"><span class="badge bg-success-subtle text-success">{{ $agent['closed_conversations'] ?? 0 }}</span></td>
                                        <td class="text-center">{{ $agent['messages_sent'] ?? 0 }}</td>
                                    </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-3">لا توجد بيانات تفصيلية للموظفين</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

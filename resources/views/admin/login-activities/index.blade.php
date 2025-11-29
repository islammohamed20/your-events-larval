@extends('layouts.admin')

@section('title', 'سجلات الدخول')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">
                <i class="fas fa-user-shield text-primary"></i> سجلات الدخول
            </h2>
            <p class="text-muted mb-0">مراقبة محاولات تسجيل الدخول وطرقها ونسبة النجاح</p>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <small class="text-muted">إجمالي السجلات</small>
                    <h3 class="mb-0">{{ number_format($stats['total']) }}</h3>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <small class="text-muted">اليوم</small>
                    <h3 class="mb-0 text-info">{{ number_format($stats['today']) }}</h3>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <small class="text-muted">ناجحة</small>
                    <h3 class="mb-0 text-success">{{ number_format($stats['successful']) }}</h3>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <small class="text-muted">فاشلة</small>
                    <h3 class="mb-0 text-danger">{{ number_format($stats['failed']) }}</h3>
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
            <form method="GET" action="{{ route('admin.login-activities.index') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">البريد الإلكتروني</label>
                        <input type="text" class="form-control" name="email" value="{{ request('email') }}" placeholder="example@email.com">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">الاسم</label>
                        <input type="text" class="form-control" name="name" value="{{ request('name') }}" placeholder="اسم المستخدم">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">الطريقة</label>
                        <select class="form-select" name="method">
                            <option value="">الكل</option>
                            @foreach($byMethod as $method => $count)
                                <option value="{{ $method }}" {{ request('method') === $method ? 'selected' : '' }}>{{ $method }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">النتيجة</label>
                        <select class="form-select" name="successful">
                            <option value="">الكل</option>
                            <option value="1" {{ request('successful') === '1' ? 'selected' : '' }}>ناجحة</option>
                            <option value="0" {{ request('successful') === '0' ? 'selected' : '' }}>فاشلة</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">IP</label>
                        <input type="text" class="form-control" name="ip" value="{{ request('ip') }}" placeholder="127.0.0.1">
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

    <!-- Activities Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white">
            <h5 class="mb-0">
                <i class="fas fa-list text-primary"></i> قائمة سجلات الدخول
                <span class="badge bg-primary">{{ $activities->total() }}</span>
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th width="60">ID</th>
                            <th>المستخدم</th>
                            <th>البريد الإلكتروني</th>
                            <th width="120">الطريقة</th>
                            <th width="100">النتيجة</th>
                            <th>IP</th>
                            <th>الدولة</th>
                            <th width="180">التاريخ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($activities as $activity)
                            <tr>
                                <td>{{ $activity->id }}</td>
                                <td>
                                    <i class="fas fa-user text-muted"></i> {{ $activity->user->name ?? '-' }}
                                </td>
                                <td>
                                    <i class="fas fa-envelope text-muted"></i> {{ $activity->user->email ?? '-' }}
                                </td>
                                <td>{{ $activity->method }}</td>
                                <td>
                                    @if($activity->successful)
                                        <span class="badge bg-success">ناجحة</span>
                                    @else
                                        <span class="badge bg-danger">فاشلة</span>
                                    @endif
                                </td>
                                <td>{{ $activity->ip_address ?? '-' }}</td>
                                <td>{{ $activity->country ?? '-' }}</td>
                                <td>{{ $activity->created_at->format('Y-m-d H:i:s') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">لا توجد سجلات</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white">
            {{ $activities->withQueryString()->links() }}
        </div>
    </div>
</div>
@endsection


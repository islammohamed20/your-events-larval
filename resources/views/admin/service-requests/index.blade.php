@extends('layouts.app')

@section('title', 'سجل الطلبات')

@section('content')
<div class="container-fluid py-5" id="adminServiceRequestsAutoRefresh">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h2 fw-bold mb-2">
                        <i class="fas fa-list-check me-2 text-primary"></i>سجل الطلبات
                    </h1>
                    <p class="text-muted mb-0">إدارة جميع طلبات الخدمات</p>
                </div>
                <a href="{{ route('admin.service-requests.create') }}" class="btn btn-primary btn-lg">
                    <i class="fas fa-plus me-2"></i>طلب جديد
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Filters Card -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.service-requests.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label fw-bold">الحالة</label>
                    <select name="status" class="form-select">
                        <option value="">الكل</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>قيد الانتظار</option>
                        <option value="accepted" {{ request('status') === 'accepted' ? 'selected' : '' }}>مقبول</option>
                        <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>مرفوض</option>
                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>مكتمل</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">الفئة</label>
                    <select name="category_id" class="form-select">
                        <option value="">الكل</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3"></div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">&nbsp;</label>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search me-2"></i>بحث
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Requests Table Card -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="px-4">الخدمة</th>
                            <th class="text-center">الكمية</th>
                            <th class="text-center">السعر</th>
                            <th>ملاحظات العميل</th>
                            <th class="text-center">الحالة</th>
                            <th class="text-center">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($serviceRequests as $request)
                        <tr>
                            <td class="px-4">
                                <div class="py-2">
                                    <h6 class="mb-1 fw-bold">{{ $request->service->name }}</h6>
                                    <small class="text-muted">
                                        <i class="fas fa-tag me-1"></i>{{ $request->category->name }}
                                    </small>
                                </div>
                            </td>
                            <td class="text-center py-2">
                                <span class="badge bg-light text-dark">{{ $request->quantity }}</span>
                            </td>
                            <td class="text-center py-2">
                                <strong class="text-success">{{ number_format($request->price, 2) }} {{ __('common.currency') }}</strong>
                            </td>
                            <td class="py-2">
                                <small class="text-muted d-block text-truncate">{{ $request->customer_notes }}</small>
                            </td>
                            <td class="text-center py-2">
                                @php
                                    $statusConfig = [
                                        'pending' => ['badge' => 'warning', 'text' => 'قيد الانتظار', 'icon' => 'hourglass-half'],
                                        'accepted' => ['badge' => 'success', 'text' => 'مقبول', 'icon' => 'check-circle'],
                                        'rejected' => ['badge' => 'danger', 'text' => 'مرفوض', 'icon' => 'times-circle'],
                                        'completed' => ['badge' => 'info', 'text' => 'مكتمل', 'icon' => 'clipboard-check']
                                    ];
                                    $config = $statusConfig[$request->status] ?? $statusConfig['pending'];
                                @endphp
                                <span class="badge bg-{{ $config['badge'] }}">
                                    <i class="fas fa-{{ $config['icon'] }} me-1"></i>{{ $config['text'] }}
                                </span>
                            </td>
                            <td class="text-center py-2">
                                <a href="{{ route('admin.service-requests.show', $request->id) }}" class="btn btn-sm btn-outline-primary text-white" title="عرض التفاصيل">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="fas fa-inbox" style="font-size: 3rem; opacity: 0.5; display: block; margin-bottom: 1rem;"></i>
                                    <p class="mb-0">لا توجد طلبات حالياً</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Pagination -->
    @if($serviceRequests->hasPages())
    <div class="mt-4 d-flex justify-content-center">
        {{ $serviceRequests->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var container = document.getElementById('adminServiceRequestsAutoRefresh');
    if (!container) return;

    function refreshServiceRequests() {
        if (document.visibilityState !== 'visible') return;

        fetch(window.location.href, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            cache: 'no-store'
        })
        .then(response => response.text())
        .then(html => {
            var parser = new DOMParser();
            var doc = parser.parseFromString(html, 'text/html');
            var newContainer = doc.getElementById('adminServiceRequestsAutoRefresh');
            if (newContainer) {
                container.innerHTML = newContainer.innerHTML;
            }
        })
        .catch(() => {});
    }

    setInterval(refreshServiceRequests, 5000); // كل 5 ثواني
});
</script>
@endpush

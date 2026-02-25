@extends('layouts.admin')

@section('title', 'إدارة الطلبات')

@section('content')
<div class="container-fluid mt-4" id="adminOrdersAutoRefresh">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h2 mb-0">
                <i class="fas fa-list-check me-2 text-primary"></i>إدارة الطلبات
            </h1>
            <p class="text-muted mb-0">عرض وإدارة جميع الطلبات المقدمة من العملاء</p>
        </div>
        <div>
            <span class="badge bg-primary">{{ $orders->total() }} طلب</span>
        </div>
    </div>

    <!-- Alerts -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <!-- Filters Card -->
    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.orders.index') }}" class="row g-3">
                <!-- Search Filter -->
                <div class="col-md-6">
                    <label for="search" class="form-label">🔍 البحث (الخدمة أو العميل)</label>
                    <input type="text" class="form-control" id="search" name="search" 
                           value="{{ request('search') }}" placeholder="ابحث عن خدمة أو عميل...">
                </div>

                <!-- Status Filter -->
                <div class="col-md-3">
                    <label for="status" class="form-label">📊 حالة الطلب</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">جميع الحالات</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>
                            ⏳ معلق
                        </option>
                        <option value="assigned" {{ request('status') == 'assigned' ? 'selected' : '' }}>
                            ✓ تم الإسناد
                        </option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>
                            ✅ مكتمل
                        </option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>
                            ❌ ملغي
                        </option>
                    </select>
                </div>

                <!-- Filter Button -->
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-filter me-2"></i>تطبيق الفلاتر
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Orders Table -->
    <div class="card shadow-sm">
        <div class="card-body">
            @if($orders->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>اسم الخدمة</th>
                            <th>الكمية</th>
                            <th>السعر</th>
                            <th>ملاحظة خاصة</th>
                            <th>ملاحظة عامة</th>
                            <th>اسم المورد</th>
                            <th>الحالة</th>
                            <th class="text-center">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $order)
                        <tr>
                            <td>
                                <strong>#{{ $order->id }}</strong>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark">{{ $order->service->name }}</span>
                                <br>
                                <small class="text-muted">{{ $order->category->name }}</small>
                            </td>
                            <td>
                                <span class="badge bg-info">{{ $order->quantity }}</span>
                            </td>
                            <td>
                                <span class="text-success fw-bold">{{ number_format($order->price, 2) }} {{ __('common.currency') }}</span>
                            </td>
                            <td>
                                @if($order->customer_notes)
                                    <span class="text-truncate d-inline-block" style="max-width: 150px;" 
                                          title="{{ $order->customer_notes }}">
                                        {{ Str::limit($order->customer_notes, 50) }}
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($order->general_notes)
                                    <span class="text-truncate d-inline-block" style="max-width: 150px;" 
                                          title="{{ $order->general_notes }}">
                                        {{ Str::limit($order->general_notes, 50) }}
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($order->supplier)
                                    <strong>{{ $order->supplier->name }}</strong>
                                    <br>
                                    <small class="text-muted">{{ $order->supplier->email }}</small>
                                @else
                                    <span class="text-muted">لم يتم الإسناد</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $statusConfig = [
                                        'pending' => ['badge' => 'warning', 'icon' => 'hourglass-half', 'text' => 'معلق'],
                                        'assigned' => ['badge' => 'success', 'icon' => 'check-circle', 'text' => 'تم الإسناد'],
                                        'completed' => ['badge' => 'info', 'icon' => 'clipboard-check', 'text' => 'مكتمل'],
                                        'cancelled' => ['badge' => 'danger', 'icon' => 'times-circle', 'text' => 'ملغي'],
                                    ];
                                    $config = $statusConfig[$order->status] ?? $statusConfig['pending'];
                                @endphp
                                <span class="badge bg-{{ $config['badge'] }}">
                                    <i class="fas fa-{{ $config['icon'] }} me-1"></i>{{ $config['text'] }}
                                </span>
                            </td>
                            <td class="text-center">
                                <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-info text-white" 
                                   title="عرض التفاصيل">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <nav aria-label="Page navigation" class="mt-4">
                {{ $orders->links('pagination::bootstrap-4') }}
            </nav>
            @else
            <!-- Empty State -->
            <div class="text-center py-5">
                <i class="fas fa-inbox" style="font-size: 3rem; opacity: 0.5;"></i>
                <p class="text-muted mt-3">لا توجد طلبات حالياً</p>
            </div>
            @endif
        </div>
    </div>
</div>

<style>
    .table-hover tbody tr:hover {
        background-color: #f8f9fa;
    }
    
    .badge {
        padding: 6px 10px;
        font-size: 12px;
    }
    
    .btn-sm {
        padding: 4px 8px;
        font-size: 12px;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var container = document.getElementById('adminOrdersAutoRefresh');
    if (!container) return;

    function refreshOrders() {
        if (document.visibilityState !== 'visible') return;

        fetch(window.location.href, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            cache: 'no-store'
        })
        .then(response => response.text())
        .then(html => {
            var parser = new DOMParser();
            var doc = parser.parseFromString(html, 'text/html');
            var newContainer = doc.getElementById('adminOrdersAutoRefresh');
            if (newContainer) {
                container.innerHTML = newContainer.innerHTML;
            }
        })
        .catch(() => {});
    }

    setInterval(refreshOrders, 5000); // كل 5 ثواني
});
</script>
@endsection

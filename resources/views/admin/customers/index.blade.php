@extends('layouts.admin')

@section('title', 'إدارة العملاء')

@section('content')
<div class="container-fluid py-4" id="adminCustomersAutoRefresh">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">إدارة العملاء</h4>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.customers.export') }}" class="btn btn-success">
                            <i class="fas fa-file-excel"></i> تصدير Excel
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Search Form -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <form method="GET" action="{{ route('admin.customers.index') }}">
                                <div class="input-group">
                                    <input type="text" name="search" class="form-control" 
                                           placeholder="البحث بالاسم أو البريد الإلكتروني..." 
                                           value="{{ request('search') }}">
                                    <button class="btn btn-primary" type="submit">
                                        <i class="fas fa-search"></i> بحث
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Analytics Cards -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h6 class="card-title">إجمالي العملاء</h6>
                                            <h3>{{ $stats['total_customers'] }}</h3>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-users fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h6 class="card-title">عروض الأسعار النشطة</h6>
                                            <h3>{{ $stats['active_quotes'] }}</h3>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-file-invoice fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h6 class="card-title">الحجوزات المكتملة</h6>
                                            <h3>{{ $stats['completed_bookings'] }}</h3>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-check-circle fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h6 class="card-title">إجمالي الإيرادات</h6>
                                            <h3>{{ number_format($stats['total_revenue'], 2) }} {{ __('common.currency') }}</h3>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-money-bill-wave fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Customers Table -->
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>رقم العميل</th>
                                    <th>الاسم</th>
                                    <th>البريد الإلكتروني</th>
                                    <th>رقم الهاتف</th>
                                    <th>اسم الشركة</th>
                                    <th>الحالة</th>
                                    <th>عروض الأسعار</th>
                                    <th>الحجوزات</th>
                                    <th>إجمالي المدفوعات</th>
                                    <th>تاريخ التسجيل</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($customers as $customer)
                                <tr>
                                    <td>{{ $customer->id }}</td>
                                    <td>{{ $customer->name }}</td>
                                    <td>{{ $customer->email }}</td>
                                    <td>{{ $customer->phone ?? 'غير محدد' }}</td>
                                    <td>{{ $customer->company_name ?? 'غير محدد' }}</td>
                                    <td>
                                        @if($customer->status == 'active')
                                            <span class="badge bg-success">نشط</span>
                                        @elseif($customer->status == 'inactive')
                                            <span class="badge bg-warning">غير نشط</span>
                                        @elseif($customer->status == 'suspended')
                                            <span class="badge bg-danger">معلق</span>
                                        @else
                                            <span class="badge bg-secondary">غير محدد</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-primary">{{ $customer->quotes_count }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-success">{{ $customer->bookings_count }}</span>
                                    </td>
                                    <td>
                                        {{ number_format($customer->bookings_sum_total_amount ?? 0, 2) }} {{ __('common.currency') }}
                                    </td>
                                    <td>{{ $customer->created_at->format('Y-m-d') }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.customers.show', $customer->id) }}" 
                                               class="btn btn-sm btn-info text-white" title="عرض التفاصيل">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.customers.edit', $customer->id) }}" 
                                               class="btn btn-sm btn-primary" title="تعديل البيانات">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="11" class="text-center">لا توجد عملاء</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center">
                        {{ $customers->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var container = document.getElementById('adminCustomersAutoRefresh');
    if (!container) return;

    function refreshCustomers() {
        if (document.visibilityState !== 'visible') return;

        fetch(window.location.href, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            cache: 'no-store'
        })
        .then(response => response.text())
        .then(html => {
            var parser = new DOMParser();
            var doc = parser.parseFromString(html, 'text/html');
            var newContainer = doc.getElementById('adminCustomersAutoRefresh');
            if (newContainer) {
                container.innerHTML = newContainer.innerHTML;
            }
        })
        .catch(() => {});
    }

    setInterval(refreshCustomers, 15000); // كل 15 ثانية
});
</script>
@endpush

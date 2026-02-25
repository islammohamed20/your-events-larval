@extends('layouts.admin')

@section('title', 'إدارة الموردين')

@section('content')
<div class="container-fluid py-4" id="adminSuppliersAutoRefresh">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h1 class="h4 mb-0">إدارة الموردين</h1>
        <a href="{{ route('admin.suppliers.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i>إضافة مورد
        </a>
    </div>

    <form method="GET" class="row g-3 mb-3">
        <div class="col-md-4">
            <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="بحث بالاسم أو البريد">
        </div>
        <div class="col-md-3">
            <select name="status" class="form-select">
                @php $status = request('status', 'all'); @endphp
                <option value="all" {{ $status==='all' ? 'selected' : '' }}>كل الحالات</option>
                <option value="pending" {{ $status==='pending' ? 'selected' : '' }}>قيد المراجعة</option>
                <option value="approved" {{ $status==='approved' ? 'selected' : '' }}>موافق عليه</option>
                <option value="rejected" {{ $status==='rejected' ? 'selected' : '' }}>مرفوض</option>
                <option value="suspended" {{ $status==='suspended' ? 'selected' : '' }}>موقوف</option>
            </select>
        </div>
        <div class="col-md-3">
            <select name="type" class="form-select">
                @php $type = request('type', 'all'); @endphp
                <option value="all" {{ $type==='all' ? 'selected' : '' }}>كل الأنواع</option>
                <option value="company" {{ $type==='company' ? 'selected' : '' }}>منشأة</option>
                <option value="individual" {{ $type==='individual' ? 'selected' : '' }}>فرد</option>
            </select>
        </div>
        <div class="col-md-2 d-grid">
            <button type="submit" class="btn btn-secondary">تصفية</button>
        </div>
    </form>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-striped mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>الاسم</th>
                        <th>البريد</th>
                        <th>النوع</th>
                        <th>الحالة</th>
                        <th>تاريخ الإنشاء</th>
                        <th class="text-center">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($suppliers as $supplier)
                        <tr>
                            <td>{{ $supplier->id }}</td>
                            <td>{{ $supplier->name }}</td>
                            <td>{{ $supplier->email }}</td>
                            <td>
                                @if($supplier->supplier_type === 'company')
                                    منشأة
                                @elseif($supplier->supplier_type === 'individual')
                                    فرد
                                @else
                                    <span class="text-muted">غير محدد</span>
                                @endif
                            </td>
                            <td>
                                @switch($supplier->status)
                                    @case('approved')<span class="badge bg-success">موافق عليه</span>@break
                                    @case('pending')<span class="badge bg-warning text-dark">قيد المراجعة</span>@break
                                    @case('rejected')<span class="badge bg-danger">مرفوض</span>@break
                                    @case('suspended')<span class="badge bg-secondary">موقوف</span>@break
                                    @default <span class="badge bg-light text-dark">غير معروف</span>
                                @endswitch
                            </td>
                            <td>{{ optional($supplier->created_at)->format('Y-m-d') }}</td>
                            <td class="text-center">
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.suppliers.show', $supplier) }}" 
                                       class="btn btn-sm btn-outline-info" 
                                       title="عرض">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.suppliers.edit', $supplier) }}" 
                                       class="btn btn-sm btn-outline-warning" 
                                       title="تعديل">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    @if($supplier->status === 'pending')
                                        <form action="{{ route('admin.suppliers.approve', $supplier) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-success" title="موافقة">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.suppliers.reject', $supplier) }}" method="POST" class="d-inline" onsubmit="return confirm('رفض هذا المورد؟')">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="رفض">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </form>
                                    @elseif($supplier->status === 'approved')
                                        <form action="{{ route('admin.suppliers.suspend', $supplier) }}" method="POST" class="d-inline" onsubmit="return confirm('إيقاف هذا المورد؟')">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-secondary" title="إيقاف">
                                                <i class="fas fa-pause"></i>
                                            </button>
                                        </form>
                                    @elseif($supplier->status === 'suspended')
                                        <form action="{{ route('admin.suppliers.activate', $supplier) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-success" title="تفعيل">
                                                <i class="fas fa-play"></i>
                                            </button>
                                        </form>
                                    @elseif($supplier->status === 'rejected')
                                        <form action="{{ route('admin.suppliers.approve', $supplier) }}" method="POST" class="d-inline" onsubmit="return confirm('إعادة الموافقة على هذا المورد؟')">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-success" title="إعادة الموافقة">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                    @endif

                                    <form action="{{ route('admin.suppliers.destroy', $supplier) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا المورد؟')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="حذف">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">لا توجد بيانات</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            {{ $suppliers->links() }}
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var container = document.getElementById('adminSuppliersAutoRefresh');
    if (!container) return;

    function refreshSuppliers() {
        if (document.visibilityState !== 'visible') return;

        fetch(window.location.href, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            cache: 'no-store'
        })
        .then(response => response.text())
        .then(html => {
            var parser = new DOMParser();
            var doc = parser.parseFromString(html, 'text/html');
            var newContainer = doc.getElementById('adminSuppliersAutoRefresh');
            if (newContainer) {
                container.innerHTML = newContainer.innerHTML;
            }
        })
        .catch(() => {});
    }

    setInterval(refreshSuppliers, 15000); // كل 15 ثانية
});
</script>
@endpush

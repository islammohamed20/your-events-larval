@extends('layouts.admin')

@section('title', 'إدارة الموردين')

@section('styles')
<style>
@media (min-width: 992px) { .mobile-only { display: none !important; } }
@media (max-width: 991.98px) {
    .desktop-only { display: none !important; }

    .supplier-card {
        background: #fff;
        border-radius: 16px;
        border: 1px solid #eef1f6;
        padding: 1rem;
        margin-bottom: 1rem;
        box-shadow: 0 4px 12px rgba(0,0,0,0.04);
    }

    .supplier-top {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: .75rem;
    }

    .supplier-avatar {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        background: linear-gradient(135deg, #1f144a, #6366f1);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        font-weight: 800;
        flex-shrink: 0;
    }

    .supplier-name { font-size: 1rem; font-weight: 800; color: #1f144a; margin-bottom: 2px; }
    .supplier-email { font-size: .78rem; color: #6b7280; }

    .supplier-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        margin-bottom: .75rem;
    }

    .s-chip {
        font-size: .72rem;
        padding: 3px 9px;
        border-radius: 8px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }
    .chip-type { background: #f0f9ff; color: #0369a1; }
    .chip-date { background: #f9fafb; color: #374151; }

    .supplier-actions {
        display: flex;
        gap: 8px;
        padding-top: .75rem;
        border-top: 1px solid #f3f4f6;
    }

    .s-action-btn {
        flex: 1;
        padding: 7px;
        border-radius: 10px;
        font-size: .78rem;
        font-weight: 600;
        text-align: center;
        text-decoration: none;
        border: none;
        cursor: pointer;
    }
    .btn-s-view   { background: #eff6ff; color: #1d4ed8; }
    .btn-s-edit   { background: #fefce8; color: #92400e; }
    .btn-s-approve { background: #f0fdf4; color: #166534; }
    .btn-s-suspend { background: #fef3c7; color: #78350f; }
    .btn-s-del     { background: #fff1f2; color: #be123c; }
    .btn-s-reject  { background: #fff1f2; color: #be123c; }
    .btn-s-activate { background: #f0fdf4; color: #166534; }
}
</style>
@endsection

@section('content')
<div class="container-fluid py-4" id="adminSuppliersAutoRefresh">

    <div class="d-flex align-items-center justify-content-between mb-4">
        <h1 class="h4 mb-0 fw-bold" style="color:#1f144a;">
            <i class="fas fa-store me-2 opacity-75"></i>إدارة الموردين
        </h1>
        <a href="{{ route('admin.suppliers.create') }}" class="btn btn-primary rounded-pill px-3">
            <i class="fas fa-plus me-1"></i><span class="d-none d-sm-inline">إضافة مورد</span>
        </a>
    </div>

    {{-- Filters --}}
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-body p-3">
            <form method="GET" class="row g-2">
                <div class="col-12 col-md-4">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" name="q" value="{{ request('q') }}" class="form-control border-start-0" placeholder="بحث بالاسم أو البريد">
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <select name="status" class="form-select form-select-sm">
                        @php $status = request('status', 'all'); @endphp
                        <option value="all" {{ $status==='all' ? 'selected' : '' }}>كل الحالات</option>
                        <option value="pending" {{ $status==='pending' ? 'selected' : '' }}>قيد المراجعة</option>
                        <option value="approved" {{ $status==='approved' ? 'selected' : '' }}>موافق عليه</option>
                        <option value="rejected" {{ $status==='rejected' ? 'selected' : '' }}>مرفوض</option>
                        <option value="suspended" {{ $status==='suspended' ? 'selected' : '' }}>موقوف</option>
                    </select>
                </div>
                <div class="col-6 col-md-3">
                    <select name="type" class="form-select form-select-sm">
                        @php $type = request('type', 'all'); @endphp
                        <option value="all" {{ $type==='all' ? 'selected' : '' }}>كل الأنواع</option>
                        <option value="company" {{ $type==='company' ? 'selected' : '' }}>منشأة</option>
                        <option value="individual" {{ $type==='individual' ? 'selected' : '' }}>فرد</option>
                    </select>
                </div>
                <div class="col-12 col-md-2">
                    <button type="submit" class="btn btn-secondary btn-sm w-100 rounded-3">تصفية</button>
                </div>
            </form>
        </div>
    </div>

    {{-- 🖥️ Desktop Table --}}
    <div class="card border-0 shadow-sm desktop-only">
        <div class="table-responsive">
            <table class="table table-striped mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th><th>الاسم</th><th>البريد</th><th>النوع</th><th>الحالة</th><th>تاريخ الإنشاء</th><th class="text-center">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($suppliers as $supplier)
                        <tr>
                            <td>{{ $supplier->id }}</td>
                            <td>{{ $supplier->name }}</td>
                            <td>{{ $supplier->email }}</td>
                            <td>
                                @if($supplier->supplier_type === 'company') منشأة
                                @elseif($supplier->supplier_type === 'individual') فرد
                                @else <span class="text-muted">غير محدد</span>
                                @endif
                            </td>
                            <td>
                                @switch($supplier->status)
                                    @case('approved') <span class="badge bg-success">موافق عليه</span> @break
                                    @case('pending') <span class="badge bg-warning text-dark">قيد المراجعة</span> @break
                                    @case('rejected') <span class="badge bg-danger">مرفوض</span> @break
                                    @case('suspended') <span class="badge bg-secondary">موقوف</span> @break
                                    @default <span class="badge bg-light text-dark">غير معروف</span>
                                @endswitch
                            </td>
                            <td>{{ optional($supplier->created_at)->format('Y-m-d') }}</td>
                            <td class="text-center">
                                <div class="btn-group">
                                    <a href="{{ route('admin.suppliers.show', $supplier) }}" class="btn btn-sm btn-outline-info" title="عرض"><i class="fas fa-eye"></i></a>
                                    <a href="{{ route('admin.suppliers.edit', $supplier) }}" class="btn btn-sm btn-outline-warning" title="تعديل"><i class="fas fa-edit"></i></a>
                                    @if($supplier->status === 'pending')
                                        <form action="{{ route('admin.suppliers.approve', $supplier) }}" method="POST" class="d-inline">@csrf<button class="btn btn-sm btn-outline-success" title="موافقة"><i class="fas fa-check"></i></button></form>
                                        <form action="{{ route('admin.suppliers.reject', $supplier) }}" method="POST" class="d-inline" onsubmit="return confirm('رفض هذا المورد؟')">@csrf<button class="btn btn-sm btn-outline-danger" title="رفض"><i class="fas fa-times"></i></button></form>
                                    @elseif($supplier->status === 'approved')
                                        <form action="{{ route('admin.suppliers.suspend', $supplier) }}" method="POST" class="d-inline" onsubmit="return confirm('إيقاف هذا المورد؟')">@csrf<button class="btn btn-sm btn-outline-secondary" title="إيقاف"><i class="fas fa-pause"></i></button></form>
                                    @elseif($supplier->status === 'suspended')
                                        <form action="{{ route('admin.suppliers.activate', $supplier) }}" method="POST" class="d-inline">@csrf<button class="btn btn-sm btn-outline-success" title="تفعيل"><i class="fas fa-play"></i></button></form>
                                    @elseif($supplier->status === 'rejected')
                                        <form action="{{ route('admin.suppliers.approve', $supplier) }}" method="POST" class="d-inline" onsubmit="return confirm('إعادة الموافقة؟')">@csrf<button class="btn btn-sm btn-outline-success" title="إعادة الموافقة"><i class="fas fa-check"></i></button></form>
                                    @endif
                                    <form action="{{ route('admin.suppliers.destroy', $supplier) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا المورد؟')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger" title="حذف"><i class="fas fa-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center py-4">لا توجد بيانات</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white">{{ $suppliers->links() }}</div>
    </div>

    {{-- 📱 Mobile Cards --}}
    <div class="mobile-only">
        @forelse($suppliers as $supplier)
            <div class="supplier-card">
                <div class="supplier-top">
                    <div class="supplier-avatar">{{ mb_substr($supplier->name, 0, 1) }}</div>
                    <div>
                        <div class="supplier-name">{{ $supplier->name }}</div>
                        <div class="supplier-email">{{ $supplier->email }}</div>
                    </div>
                    <div class="me-auto"></div>
                    @switch($supplier->status)
                        @case('approved') <span class="badge bg-success">موافق</span> @break
                        @case('pending') <span class="badge bg-warning text-dark">انتظار</span> @break
                        @case('rejected') <span class="badge bg-danger">مرفوض</span> @break
                        @case('suspended') <span class="badge bg-secondary">موقوف</span> @break
                        @default <span class="badge bg-light text-dark">غير معروف</span>
                    @endswitch
                </div>

                <div class="supplier-meta">
                    @if($supplier->supplier_type === 'company')
                        <span class="s-chip chip-type"><i class="fas fa-building"></i>منشأة</span>
                    @elseif($supplier->supplier_type === 'individual')
                        <span class="s-chip chip-type"><i class="fas fa-user"></i>فرد</span>
                    @endif
                    <span class="s-chip chip-date"><i class="fas fa-clock"></i>{{ optional($supplier->created_at)->format('Y-m-d') }}</span>
                </div>

                <div class="supplier-actions">
                    <a href="{{ route('admin.suppliers.show', $supplier) }}" class="s-action-btn btn-s-view">عرض</a>
                    <a href="{{ route('admin.suppliers.edit', $supplier) }}" class="s-action-btn btn-s-edit">تعديل</a>

                    @if($supplier->status === 'pending')
                        <form action="{{ route('admin.suppliers.approve', $supplier) }}" method="POST" style="flex:1;display:flex;">
                            @csrf<button type="submit" class="s-action-btn btn-s-approve w-100">موافقة</button>
                        </form>
                    @elseif($supplier->status === 'approved')
                        <form action="{{ route('admin.suppliers.suspend', $supplier) }}" method="POST" style="flex:1;display:flex;" onsubmit="return confirm('إيقاف؟')">
                            @csrf<button type="submit" class="s-action-btn btn-s-suspend w-100">إيقاف</button>
                        </form>
                    @elseif($supplier->status === 'suspended')
                        <form action="{{ route('admin.suppliers.activate', $supplier) }}" method="POST" style="flex:1;display:flex;">
                            @csrf<button type="submit" class="s-action-btn btn-s-activate w-100">تفعيل</button>
                        </form>
                    @endif

                    <form action="{{ route('admin.suppliers.destroy', $supplier) }}" method="POST" style="flex:1;display:flex;" onsubmit="return confirm('حذف المورد؟')">
                        @csrf @method('DELETE')
                        <button type="submit" class="s-action-btn btn-s-del w-100">حذف</button>
                    </form>
                </div>
            </div>
        @empty
            <div class="text-center py-5">
                <i class="fas fa-store fa-3x text-muted mb-3 opacity-25"></i>
                <p class="text-muted fw-bold">لا توجد بيانات</p>
            </div>
        @endforelse
        <div class="mt-3">{{ $suppliers->links() }}</div>
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
        fetch(window.location.href, { headers: { 'X-Requested-With': 'XMLHttpRequest' }, cache: 'no-store' })
        .then(r => r.text())
        .then(html => {
            var doc = new DOMParser().parseFromString(html, 'text/html');
            var newC = doc.getElementById('adminSuppliersAutoRefresh');
            if (newC) container.innerHTML = newC.innerHTML;
        }).catch(() => {});
    }
    setInterval(refreshSuppliers, 15000);
});
</script>
@endpush

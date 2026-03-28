@extends('layouts.admin')

@section('title', __('common.admin_packages_management'))
@section('page-title', __('common.admin_packages_management'))
@section('page-description', __('common.admin_packages_management_description'))

@section('styles')
<style>
/* ============================================================
   PACKAGES — GLOBAL & MOBILE STYLES
   ============================================================ */
:root {
    --admin-primary: #1f144a;
    --admin-accent: #ef4870;
}

/* التنسيق العام لسطح المكتب والموبايل */
.page-header-title { color: var(--admin-primary); font-weight: 800; }
.card-packages { border: none; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); overflow: hidden; }

/* إخفاء العروض غير المناسبة */
@media (min-width: 992px) {
    .mobile-only { display: none !important; }
}

@media (max-width: 991.98px) {
    .desktop-only { display: none !important; }
    
    .card-body { padding: 1rem !important; }
    
    .packages-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1.25rem;
    }

    .package-item-card {
        background: #fff;
        border-radius: 18px;
        border: 1px solid #f0f0f0;
        padding: 1rem;
        position: relative;
        transition: all 0.3s ease;
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .package-item-card.selected {
        border-color: var(--admin-accent);
        background-color: #fff5f7;
        box-shadow: 0 0 0 2px var(--admin-accent);
    }

    .package-item-header {
        display: flex;
        gap: 1rem;
        align-items: center;
    }

    .package-img-box {
        width: 70px;
        height: 70px;
        border-radius: 12px;
        overflow: hidden;
        flex-shrink: 0;
        position: relative;
        border: 1px solid #eee;
    }

    .package-img-box img { width: 100%; height: 100%; object-fit: cover; }

    .package-main-info { flex: 1; min-width: 0; }
    .package-title { font-weight: 700; color: var(--admin-primary); font-size: 1.05rem; margin-bottom: 2px; }
    .package-sub { font-size: 0.8rem; color: #718096; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }

    .package-meta-row {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        padding-top: 10px;
        border-top: 1px solid #f7f7f7;
    }

    .p-badge {
        font-size: 0.75rem;
        padding: 5px 10px;
        border-radius: 8px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .p-badge-price { background: #fdf2f8; color: #be185d; }
    .p-badge-users { background: #f0f9ff; color: #0369a1; }
    .p-badge-status { background: #ecfdf5; color: #047857; }
    .p-badge-inactive { background: #f9fafb; color: #4b5563; }

    /* أزرار الإجراءات السريعة */
    .package-actions {
        display: flex;
        gap: 8px;
        margin-top: 5px;
    }
    
    .action-btn {
        flex: 1;
        padding: 8px;
        border-radius: 10px;
        text-align: center;
        font-size: 0.85rem;
        font-weight: 600;
        text-decoration: none;
        border: none;
    }
    .btn-edit-lite { background: #f3f0ff; color: #6d28d9; }
    .btn-delete-lite { background: #fff1f2; color: #e11d48; }

    /* شريط العمليات الجماعية للموبايل */
    .mobile-bulk-bar {
        position: fixed;
        bottom: 20px;
        left: 20px;
        right: 20px;
        background: var(--admin-primary);
        color: white;
        padding: 12px 20px;
        border-radius: 15px;
        display: none;
        justify-content: space-between;
        align-items: center;
        z-index: 1050;
        box-shadow: 0 10px 25px rgba(0,0,0,0.2);
    }
    .mobile-bulk-bar.show { display: flex; animation: slideUp 0.3s ease; }
    
    @keyframes slideUp { from { transform: translateY(100px); } to { transform: translateY(0); } }
}

/* شريط البحث */
.search-card { border-radius: 15px; border: none; box-shadow: 0 4px 15px rgba(0,0,0,0.03); margin-bottom: 1.5rem; }
.search-input-group { background: #f8fafc; border-radius: 12px; padding: 5px 15px; border: 1px solid #e2e8f0; }
.search-input-group input { background: transparent; border: none; box-shadow: none; font-size: 0.95rem; }
</style>
@endsection

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="page-header-title mb-1">{{ __('common.packages') }}</h2>
            <p class="text-muted small mb-0 d-none d-sm-block">{{ __('common.admin_packages_management_description') }}</p>
        </div>
        <a href="{{ route('admin.packages.create') }}" class="btn btn-primary shadow-sm rounded-pill px-4">
            <i class="fas fa-plus me-2"></i><span class="d-none d-sm-inline">{{ __('common.add_new_package') }}</span>
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-4 mb-4" role="alert">
            <div class="d-flex align-items-center">
                <i class="fas fa-check-circle me-3 fs-4"></i>
                <div>{{ session('success') }}</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- 🔍 Search & Filters --}}
    <div class="card search-card">
        <div class="card-body p-3">
            <form action="{{ route('admin.packages.index') }}" method="GET" class="row g-3">
                <div class="col-12 col-md-8 text-end">
                    <div class="search-input-group d-flex align-items-center">
                        <i class="fas fa-search text-muted me-2"></i>
                        <input type="text" name="search" class="form-control text-end" placeholder="ابحث عن باقة بالاسم..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-6 col-md-2">
                    <select name="status" class="form-select rounded-3">
                        <option value="">كل الحالات</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>نشط</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>غير نشط</option>
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <button type="submit" class="btn btn-dark w-100 rounded-3">تطبيق</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card card-packages">
        {{-- 🛠️ Desktop Toolbar --}}
        <div class="card-header bg-white py-3 border-0 desktop-only">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <div class="form-check me-3">
                        <input class="form-check-input" type="checkbox" id="selectAll">
                        <label class="form-check-label small fw-bold text-muted" for="selectAll">تحديد الكل</label>
                    </div>
                    <div id="bulkActions" style="display: none;">
                        <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-3" onclick="applyBulkDelete()">
                            <i class="fas fa-trash me-1"></i> حذف المحدد
                        </button>
                    </div>
                </div>
                <div class="text-muted small">
                    إجمالي الباقات: <span class="fw-bold text-primary">{{ $packages->count() }}</span>
                </div>
            </div>
        </div>

        <div class="card-body p-0">
            @if($packages->count() > 0)
                {{-- 🖥️ Desktop Table --}}
                <div class="table-responsive desktop-only">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th width="40"></th>
                                <th width="100">{{ __('common.image') }}</th>
                                <th>{{ __('common.package_name') }}</th>
                                <th>{{ __('common.price') }}</th>
                                <th>{{ __('common.number_of_people') }}</th>
                                <th width="120">{{ __('common.status') }}</th>
                                <th width="150" class="text-center">{{ __('common.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($packages as $package)
                                <tr>
                                    <td class="ps-3">
                                        <input class="form-check-input package-checkbox" type="checkbox" value="{{ $package->id }}" onchange="updateBulkUI()">
                                    </td>
                                    <td>
                                        <img src="{{ Str::startsWith($package->thumbnail_url, 'http') ? $package->thumbnail_url : asset($package->thumbnail_url) }}" 
                                             class="rounded-3 shadow-sm" style="width: 70px; height: 70px; object-fit: cover;">
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark">{{ $package->name }}</div>
                                        <small class="text-muted d-block text-truncate" style="max-width: 200px;">{{ $package->description }}</small>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-success">{{ number_format($package->price) }} {{ __('common.currency') }}</div>
                                    </td>
                                    <td>
                                        <span class="text-primary small fw-bold">
                                            <i class="fas fa-users me-1 opacity-50"></i>
                                            {{ $package->persons_range ?? '-' }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($package->is_active)
                                            <span class="badge bg-success bg-opacity-10 text-success px-3 rounded-pill border border-success border-opacity-25">نشط</span>
                                        @else
                                            <span class="badge bg-secondary bg-opacity-10 text-secondary px-3 rounded-pill border border-secondary border-opacity-25">غير نشط</span>
                                        @endif
                                    </td>
                                    <td class="text-center pe-3">
                                        <div class="btn-group shadow-sm rounded-pill overflow-hidden bg-white border">
                                            <a href="{{ route('admin.packages.edit', $package) }}" class="btn btn-sm btn-white text-primary border-0"><i class="fas fa-edit"></i></a>
                                            <form action="{{ route('admin.packages.destroy', $package) }}" method="POST" onsubmit="return confirm('حذف الباقة؟')" class="d-inline">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-white text-danger border-0"><i class="fas fa-trash"></i></button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- 📱 Mobile View --}}
                <div class="card-body p-3 mobile-only">
                    <div class="packages-grid">
                        @foreach($packages as $package)
                            <div class="package-item-card shadow-sm" id="p-card-{{ $package->id }}">
                                <div class="package-item-header">
                                    <div class="form-check mb-0">
                                        <input class="form-check-input mobile-package-checkbox" type="checkbox" value="{{ $package->id }}" id="chk-{{ $package->id }}" onchange="onMobileCheck('{{ $package->id }}')">
                                    </div>
                                    <div class="package-img-box">
                                        <img src="{{ Str::startsWith($package->thumbnail_url, 'http') ? $package->thumbnail_url : asset($package->thumbnail_url) }}" alt="{{ $package->name }}">
                                    </div>
                                    <div class="package-main-info" onclick="document.getElementById('chk-{{ $package->id }}').click()">
                                        <div class="package-title">{{ $package->name }}</div>
                                        <div class="package-sub">{{ $package->description }}</div>
                                    </div>
                                </div>
                                <div class="package-meta-row">
                                    <div class="p-badge p-badge-price">
                                        <i class="fas fa-tag"></i> {{ number_format($package->price) }} {{ __('common.currency') }}
                                    </div>
                                    @if($package->persons_range)
                                    <div class="p-badge p-badge-users">
                                        <i class="fas fa-users"></i> {{ $package->persons_range }}
                                    </div>
                                    @endif
                                    <div class="p-badge {{ $package->is_active ? 'p-badge-status' : 'p-badge-inactive' }}">
                                        <i class="fas {{ $package->is_active ? 'fa-check-circle' : 'fa-times-circle' }}"></i>
                                        {{ $package->is_active ? 'نشط' : 'غير نشط' }}
                                    </div>
                                </div>
                                <div class="package-actions">
                                    <a href="{{ route('admin.packages.edit', $package) }}" class="action-btn btn-edit-lite">تعديل</a>
                                    <form action="{{ route('admin.packages.destroy', $package) }}" method="POST" onsubmit="return confirm('حذف؟')" style="flex:1; display:flex;">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="action-btn btn-delete-lite w-100">حذف</button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-box-open fa-3x text-muted mb-3 opacity-25"></i>
                    <h5 class="fw-bold">لا توجد باقات حالياً</h5>
                    <p class="text-muted small">ابدأ بإضافة باقة جديدة لعرضها للمستخدمين</p>
                    <a href="{{ route('admin.packages.create') }}" class="btn btn-primary rounded-pill px-4">إضافة باقة</a>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- 🚀 Mobile Bulk Bar --}}
<div class="mobile-bulk-bar" id="mobileBulkBar">
    <div class="d-flex align-items-center">
        <span class="badge bg-white text-dark rounded-pill me-2" id="mobileSelectedCount">0</span>
        <span class="small fw-bold">محدد</span>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-sm btn-outline-light rounded-pill px-3" onclick="clearMobileSelection()">إلغاء</button>
        <button class="btn btn-sm btn-danger rounded-pill px-3" onclick="applyBulkDelete()">حذف</button>
    </div>
</div>

{{-- Hidden form for bulk --}}
<form id="bulkForm" method="POST" action="" style="display:none;">
    @csrf @method('DELETE')
    <input type="hidden" name="ids" id="bulkIdsInput">
</form>

@endsection

@section('scripts')
<script>
    const desktopCheckboxes = document.querySelectorAll('.package-checkbox');
    const mobileCheckboxes = document.querySelectorAll('.mobile-package-checkbox');
    const selectAllBtn = document.getElementById('selectAll');
    const bulkIdsInput = document.getElementById('bulkIdsInput');
    const bulkForm = document.getElementById('bulkForm');

    // Desktop
    if(selectAllBtn) {
        selectAllBtn.addEventListener('change', function() {
            desktopCheckboxes.forEach(cb => cb.checked = this.checked);
            updateBulkUI();
        });
    }

    function updateBulkUI() {
        const selected = Array.from(desktopCheckboxes).filter(cb => cb.checked).map(cb => cb.value);
        const toolbar = document.getElementById('bulkActions');
        if(toolbar) toolbar.style.display = selected.length > 0 ? 'inline-block' : 'none';
        bulkIdsInput.value = selected.join(',');
    }

    // Mobile
    window.onMobileCheck = function(id) {
        const card = document.getElementById('p-card-' + id);
        const checkbox = document.getElementById('chk-' + id);
        if(card) card.classList.toggle('selected', checkbox.checked);
        
        const selected = Array.from(mobileCheckboxes).filter(cb => cb.checked).map(cb => cb.value);
        const bar = document.getElementById('mobileBulkBar');
        const count = document.getElementById('mobileSelectedCount');
        
        if(count) count.textContent = selected.length;
        if(bar) bar.classList.toggle('show', selected.length > 0);
        bulkIdsInput.value = selected.join(',');
    }

    window.clearMobileSelection = function() {
        mobileCheckboxes.forEach(cb => {
            cb.checked = false;
            const card = document.getElementById('p-card-' + cb.value);
            if(card) card.classList.remove('selected');
        });
        document.getElementById('mobileBulkBar').classList.remove('show');
    }

    window.applyBulkDelete = function() {
        if(!confirm('هل أنت متأكد من حذف الباقات المختارة؟')) return;
        bulkForm.action = "{{ route('admin.packages.index') }}/bulk-delete"; // Assuming a bulk route exists or we'll handle it
        // Note: Bulk delete route might need to be created if it doesn't exist
        bulkForm.submit();
    }
</script>
@endsection

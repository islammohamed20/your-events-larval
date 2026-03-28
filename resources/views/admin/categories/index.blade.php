@extends('layouts.admin')

@section('title', 'إدارة الفئات')

@section('styles')
<style>
@media (min-width: 992px) { .mobile-only { display: none !important; } }
@media (max-width: 991.98px) {
    .desktop-only { display: none !important; }

    .cat-card {
        background: #fff;
        border-radius: 16px;
        border: 1px solid #eef1f6;
        padding: 1rem;
        margin-bottom: 1rem;
        box-shadow: 0 4px 12px rgba(0,0,0,0.04);
    }

    .cat-card-top {
        display: flex;
        align-items: center;
        gap: .85rem;
        margin-bottom: .65rem;
    }

    .cat-img {
        width: 54px;
        height: 54px;
        border-radius: 12px;
        object-fit: cover;
        border: 2px solid #f0f0f0;
        flex-shrink: 0;
    }
    .cat-img-placeholder {
        width: 54px;
        height: 54px;
        border-radius: 12px;
        background: linear-gradient(135deg, #1f144a, #6366f1);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.4rem;
        flex-shrink: 0;
    }

    .cat-name { font-size: 1rem; font-weight: 800; color: #1f144a; }
    .cat-name-en { font-size: .78rem; color: #9ca3af; }

    .cat-chips {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        margin-bottom: .75rem;
    }
    .c-chip {
        font-size: .72rem;
        padding: 3px 9px;
        border-radius: 8px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }
    .chip-services { background: #f0f9ff; color: #0369a1; }
    .chip-order    { background: #f9fafb; color: #374151; }
    .chip-active   { background: #f0fdf4; color: #166534; }
    .chip-inactive { background: #fef2f2; color: #991b1b; }

    .cat-actions {
        display: flex;
        gap: 8px;
        padding-top: .75rem;
        border-top: 1px solid #f3f4f6;
    }
    .cat-btn {
        flex: 1;
        padding: 8px 4px;
        border-radius: 10px;
        font-size: .78rem;
        font-weight: 600;
        text-align: center;
        text-decoration: none;
        border: none;
        cursor: pointer;
    }
    .cat-btn-edit   { background: #fefce8; color: #92400e; }
    .cat-btn-toggle-on  { background: #f0fdf4; color: #166534; }
    .cat-btn-toggle-off { background: #fef2f2; color: #991b1b; }
    .cat-btn-del    { background: #fff1f2; color: #be123c; }
}
</style>
@endsection

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h4 mb-0 fw-bold" style="color:#1f144a;">
            <i class="fas fa-tags me-2 opacity-75"></i>إدارة فئات الخدمات
        </h1>
        <a href="{{ route('admin.categories.create') }}" class="btn btn-primary rounded-pill px-3">
            <i class="fas fa-plus me-1"></i><span class="d-none d-sm-inline">إضافة فئة</span>
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- 🖥️ Desktop Table --}}
    <div class="card shadow-sm border-0 rounded-4 desktop-only">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th><th>الصورة</th><th>الاسم</th><th>الوصف</th><th>الأيقونة</th>
                            <th>أيقونة PNG</th><th class="text-center">خدمات</th>
                            <th class="text-center">الترتيب</th><th class="text-center">الحالة</th><th class="text-center">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categories as $category)
                        <tr>
                            <td>{{ $category->id }}</td>
                            <td>
                                @if($category->image)
                                    <img src="{{ Storage::url($category->image) }}" alt="{{ $category->name }}" class="img-thumbnail" width="60" height="60">
                                @else
                                    <div class="bg-light d-flex align-items-center justify-content-center rounded" style="width:60px;height:60px;">
                                        <i class="fas fa-image text-muted"></i>
                                    </div>
                                @endif
                            </td>
                            <td>
                                <div class="fw-bold">{{ $category->name }}</div>
                                @if($category->supplier_form_name)
                                    <small class="text-muted">({{ $category->supplier_form_name }})</small><br>
                                @endif
                                @if($category->name_en)
                                    <small class="text-muted">{{ $category->name_en }}</small>
                                @endif
                            </td>
                            <td><small>{{ Str::limit($category->description, 80) }}</small></td>
                            <td class="text-center">
                                @if($category->icon)
                                    <i class="{{ $category->icon }} fa-2x"></i>
                                @else <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($category->icon_png)
                                    <img src="{{ Storage::url($category->icon_png) }}" alt="PNG Icon" class="img-thumbnail" width="40" height="40">
                                @else <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-center"><span class="badge bg-info">{{ $category->services_count }}</span></td>
                            <td class="text-center"><span class="badge bg-secondary">{{ $category->order }}</span></td>
                            <td class="text-center">
                                <form action="{{ route('admin.categories.toggle-active', $category) }}" method="POST" style="display:inline;">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="btn btn-sm {{ $category->is_active ? 'btn-success' : 'btn-secondary' }}">
                                        <i class="fas fa-{{ $category->is_active ? 'check' : 'times' }}"></i>
                                        {{ $category->is_active ? 'نشطة' : 'معطلة' }}
                                    </button>
                                </form>
                            </td>
                            <td class="text-center">
                                <div class="btn-group">
                                    <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-sm btn-outline-primary" title="تعديل"><i class="fas fa-edit"></i></a>
                                    <button type="button" class="btn btn-sm btn-outline-danger js-delete-btn" data-delete-id="{{ $category->id }}" title="حذف"><i class="fas fa-trash"></i></button>
                                </div>
                                <form id="delete-form-{{ $category->id }}" action="{{ route('admin.categories.destroy', $category) }}" method="POST" style="display:none;">
                                    @csrf @method('DELETE')
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center py-5">
                                <i class="fas fa-folder-open fa-3x text-muted mb-3 opacity-25"></i>
                                <p class="text-muted">لا توجد فئات حالياً</p>
                                <a href="{{ route('admin.categories.create') }}" class="btn btn-primary"><i class="fas fa-plus me-2"></i>إضافة أول فئة</a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($categories->hasPages())
            <div class="card-footer bg-white d-flex justify-content-center">{{ $categories->links() }}</div>
        @endif
    </div>

    {{-- 📱 Mobile Cards --}}
    <div class="mobile-only">
        @forelse($categories as $category)
            <div class="cat-card">
                <div class="cat-card-top">
                    @if($category->image)
                        <img src="{{ Storage::url($category->image) }}" alt="{{ $category->name }}" class="cat-img">
                    @else
                        <div class="cat-img-placeholder">
                            @if($category->icon)
                                <i class="{{ $category->icon }}"></i>
                            @else
                                <i class="fas fa-tags"></i>
                            @endif
                        </div>
                    @endif
                    <div class="flex-grow-1">
                        <div class="cat-name">{{ $category->name }}</div>
                        @if($category->name_en)
                            <div class="cat-name-en">{{ $category->name_en }}</div>
                        @endif
                    </div>
                </div>

                <div class="cat-chips">
                    <span class="c-chip chip-services"><i class="fas fa-layer-group"></i>{{ $category->services_count }} خدمة</span>
                    <span class="c-chip chip-order"><i class="fas fa-sort-numeric-up"></i>ترتيب: {{ $category->order }}</span>
                    @if($category->is_active)
                        <span class="c-chip chip-active"><i class="fas fa-check"></i>نشطة</span>
                    @else
                        <span class="c-chip chip-inactive"><i class="fas fa-times"></i>معطلة</span>
                    @endif
                </div>

                @if($category->description)
                    <p class="text-muted mb-2" style="font-size:.78rem;">{{ Str::limit($category->description, 60) }}</p>
                @endif

                <div class="cat-actions">
                    <a href="{{ route('admin.categories.edit', $category) }}" class="cat-btn cat-btn-edit">
                        <i class="fas fa-edit me-1"></i>تعديل
                    </a>
                    <form action="{{ route('admin.categories.toggle-active', $category) }}" method="POST" style="flex:1;display:flex;">
                        @csrf @method('PATCH')
                        <button type="submit" class="cat-btn w-100 {{ $category->is_active ? 'cat-btn-toggle-off' : 'cat-btn-toggle-on' }}">
                            <i class="fas fa-{{ $category->is_active ? 'times' : 'check' }} me-1"></i>{{ $category->is_active ? 'تعطيل' : 'تفعيل' }}
                        </button>
                    </form>
                    <button type="button" class="cat-btn cat-btn-del js-delete-btn" data-delete-id="{{ $category->id }}">
                        <i class="fas fa-trash me-1"></i>حذف
                    </button>
                    <form id="delete-form-{{ $category->id }}" action="{{ route('admin.categories.destroy', $category) }}" method="POST" style="display:none;">
                        @csrf @method('DELETE')
                    </form>
                </div>
            </div>
        @empty
            <div class="text-center py-5">
                <i class="fas fa-folder-open fa-3x text-muted mb-3 opacity-25"></i>
                <p class="text-muted fw-bold">لا توجد فئات حالياً</p>
                <a href="{{ route('admin.categories.create') }}" class="btn btn-primary rounded-pill px-4">
                    <i class="fas fa-plus me-2"></i>إضافة أول فئة
                </a>
            </div>
        @endforelse

        @if($categories->hasPages())
            <div class="d-flex justify-content-center mt-3">{{ $categories->links() }}</div>
        @endif
    </div>

</div>

<script>
function confirmDelete(id) {
    if (confirm('هل أنت متأكد من حذف هذه الفئة؟')) {
        document.getElementById('delete-form-' + id).submit();
    }
}
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.js-delete-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            confirmDelete(this.getAttribute('data-delete-id'));
        });
    });
});
</script>
@endsection

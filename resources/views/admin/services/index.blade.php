@extends('layouts.admin')

@section('title', 'إدارة الخدمات')
@section('page-title', 'إدارة الخدمات')
@section('page-description', 'عرض وإدارة جميع الخدمات المتاحة')

@section('styles')
<style>
/* ============================================================
   SHARED STYLES
   ============================================================ */
.services-toolbar { gap: 0.75rem; }
.services-search-form { gap: 0.5rem; }

.bulk-actions-wrap {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    flex-wrap: wrap;
}

/* ============================================================
   DESKTOP (≥ 992px): جدول كلاسيكي
   ============================================================ */
@media (min-width: 992px) {
    .services-toolbar { gap: 1rem; }
    /* إخفاء عرض البطاقات على الكمبيوتر */
    .mobile-cards-view { display: none !important; }
}

/* ============================================================
   TABLET / MOBILE (< 992px)
   ============================================================ */
@media (max-width: 991.98px) {
    /* إخفاء الجدول على الشاشات الصغيرة */
    .desktop-table-view { display: none !important; }

    .services-toolbar {
        align-items: stretch !important;
        flex-direction: column;
    }

    .services-toolbar > h5 {
        width: 100%;
        margin-bottom: 0.25rem;
    }

    .services-search-form {
        width: 100%;
        display: flex !important;
        flex-wrap: wrap;
        align-items: center;
    }

    .services-search-form .search-group {
        flex: 1 1 auto;
        min-width: 0 !important;
    }

    .services-search-form .search-submit {
        flex: 0 0 44px;
        width: 44px;
        padding: 0.375rem 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .services-search-form .submit-text { display: none; }

    .services-search-form .type-group {
        width: auto !important;
        min-width: 0 !important;
        flex: 0 0 auto;
    }

    .services-search-form .type-group .form-select {
        width: 140px;
        min-width: 0;
    }

    /* Bulk actions: شبكة عمودان */
    .bulk-actions-wrap {
        width: 100%;
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0.5rem;
    }

    .bulk-actions-wrap > form { width: 100%; }

    #bulkTypeToolbar {
        display: flex !important;
        align-items: center;
        gap: 0.5rem !important;
        width: 100%;
    }

    #bulkTypeToolbar .form-select {
        flex: 1 1 auto;
        min-width: 0;
        width: auto !important;
    }

    #bulkTypeToolbar .btn {
        flex: 0 0 auto;
        white-space: nowrap;
    }

    #bulkDeleteToolbar .btn { width: 100%; }
}

/* ============================================================
   MOBILE (< 768px)
   ============================================================ */
@media (max-width: 767.98px) {
    /* شريط الأزرار العلوي */
    .top-toolbar-buttons {
        width: 100%;
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0.5rem;
    }

    .top-toolbar-buttons .btn {
        font-size: 0.8rem;
        padding: 0.45rem 0.5rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .top-toolbar-buttons .btn i { display: none; }
}

/* ============================================================
   MOBILE CARDS VIEW
   ============================================================ */
.service-card {
    background: #fff;
    border-radius: 14px;
    box-shadow: 0 2px 10px rgba(31,20,74,0.08);
    overflow: hidden;
    transition: box-shadow 0.2s, transform 0.2s;
    position: relative;
    border: 2px solid transparent;
}

.service-card:active {
    transform: scale(0.99);
}

.service-card.selected {
    border-color: #1f144a;
    box-shadow: 0 4px 18px rgba(31,20,74,0.18);
}

/* شريط اللون العلوي */
.service-card-accent {
    height: 4px;
    background: linear-gradient(90deg, #1f144a, #7269b0);
    width: 100%;
}

/* checkbox في الزاوية */
.service-card-check {
    position: absolute;
    top: 14px;
    right: 14px;
    z-index: 2;
}

.service-card-check .form-check-input {
    width: 20px;
    height: 20px;
    cursor: pointer;
    border: 2px solid #1f144a;
    border-radius: 6px;
}

.service-card-check .form-check-input:checked {
    background-color: #1f144a;
    border-color: #1f144a;
}

/* ===== صورة مربعة صغيرة بجانب المعلومات ===== */
.service-card-img-wrap {
    position: relative;
    flex-shrink: 0;
    width: 80px;
    height: 80px;
}

.service-card-img-wrap img {
    width: 80px;
    height: 80px;
    border-radius: 12px;
    object-fit: cover;
    display: block;
    box-shadow: 0 2px 8px rgba(0,0,0,0.14);
}

.service-card-img-placeholder {
    width: 80px;
    height: 80px;
    border-radius: 12px;
    background: linear-gradient(135deg, #1f144a 0%, #7269b0 100%);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 4px;
}

.service-card-img-placeholder i {
    font-size: 26px;
    color: rgba(255,255,255,0.5);
}

.service-card-img-placeholder span {
    font-size: 0.6rem;
    color: rgba(255,255,255,0.4);
}

/* ===== زر الحذف: أيقونة عائمة فوق الصورة ===== */
.service-card-btn-delete {
    position: absolute;
    top: -6px;
    left: -6px;
    z-index: 10;
    width: 28px;
    height: 28px;
    border-radius: 50%;
    background: rgba(220, 38, 38, 0.92);
    border: 2px solid #fff;
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 11px;
    cursor: pointer;
    transition: background 0.2s, transform 0.15s;
    padding: 0;
    box-shadow: 0 2px 6px rgba(220,38,38,0.35);
}

.service-card-btn-delete:hover,
.service-card-btn-delete:active {
    background: #b91c1c;
    transform: scale(1.12);
    color: #fff;
}

/* ===== زر التعديل: أيقونة عائمة فوق الصورة ===== */
.service-card-btn-edit {
    position: absolute;
    top: -6px;
    right: -6px;
    z-index: 10;
    width: 28px;
    height: 28px;
    border-radius: 50%;
    background: rgba(255,255,255,0.95);
    border: 2px solid #1f144a;
    color: #1f144a;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 11px;
    cursor: pointer;
    transition: background 0.2s, transform 0.15s, color 0.2s;
    text-decoration: none;
    box-shadow: 0 2px 6px rgba(31,20,74,0.2);
}

.service-card-btn-edit:hover,
.service-card-btn-edit:active {
    background: #1f144a;
    color: #fff;
    transform: scale(1.12);
}

/* ===== جسم البطاقة ===== */
.service-card-body {
    padding: 14px;
}

.service-card-header-row {
    display: flex;
    align-items: flex-start;
    gap: 12px;
}

.service-card-info { flex: 1; min-width: 0; }

.service-card-name {
    font-size: 0.95rem;
    font-weight: 700;
    color: #1f144a;
    /* لا اقتصاص - الاسم يظهر كاملاً مع التفاف */
    white-space: normal;
    word-break: break-word;
    line-height: 1.3;
    margin-bottom: 3px;
}

.service-card-desc {
    font-size: 0.78rem;
    color: #888;
    /* وصف مختصر سطرين */
    overflow: hidden;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    line-height: 1.4;
}

/* الـ badges والسعر */
.service-card-meta {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 6px;
    margin-top: 10px;
}

.service-card-price {
    font-size: 0.88rem;
    font-weight: 700;
    color: #fff;
    background: linear-gradient(135deg, #0d9488, #14b8a6);
    border-radius: 20px;
    padding: 3px 12px;
}

/* بادج الحالة */
.badge-active {
    background: #d1fae5;
    color: #065f46;
    border-radius: 20px;
    padding: 2px 10px;
    font-size: 0.75rem;
    font-weight: 600;
}

.badge-inactive {
    background: #f3f4f6;
    color: #6b7280;
    border-radius: 20px;
    padding: 2px 10px;
    font-size: 0.75rem;
    font-weight: 600;
}

/* price badge في الجدول */
.price-badge { font-size: 0.85rem; }

/* شريط Bulk Selection على الموبايل */
.mobile-bulk-bar {
    display: none;
    position: sticky;
    top: 0;
    z-index: 50;
    background: #1f144a;
    color: #fff;
    padding: 10px 14px;
    border-radius: 12px;
    margin-bottom: 12px;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
    flex-wrap: wrap;
    box-shadow: 0 4px 12px rgba(31,20,74,0.25);
}

.mobile-bulk-bar.show { display: flex; }

.mobile-bulk-bar .selected-count {
    font-size: 0.88rem;
    font-weight: 600;
}

.mobile-bulk-bar .bulk-btns {
    display: flex;
    gap: 6px;
    flex-wrap: wrap;
}

/* حالة فارغة */
@media (max-width: 575.98px) {
    .top-toolbar-buttons {
        grid-template-columns: 1fr;
    }
    .bulk-actions-wrap {
        grid-template-columns: 1fr;
    }
}
</style>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <h2 class="mb-0">الخدمات</h2>
    <div class="d-flex gap-2 flex-wrap top-toolbar-buttons">
        <a href="{{ route('admin.services.template') }}" class="btn btn-outline-secondary">
            <i class="fas fa-file-download me-1"></i>نموذج إكسل
        </a>
        <a href="{{ route('admin.services.export') }}" class="btn btn-success">
            <i class="fas fa-file-excel me-1"></i>تصدير إكسل
        </a>
        <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#importModal">
            <i class="fas fa-file-upload me-1"></i>استيراد إكسل
        </button>
        <a href="{{ route('admin.services.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i>إضافة خدمة
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>{!! session('error') !!}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('warning'))
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-triangle me-2"></i>{!! session('warning') !!}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- Import Modal --}}
<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="importModalLabel">
                    <i class="fas fa-file-upload me-2"></i>استيراد خدمات من ملف Excel
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.services.import') }}" method="POST" enctype="multipart/form-data" id="importForm">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>ملاحظات هامة:</strong>
                        <ul class="mb-0 mt-2">
                            <li>قم بتحميل نموذج الخدمات أولاً</li>
                            <li>اترك عمود ID فارغاً لإنشاء خدمة جديدة، أو ضع رقمها لتحديثها</li>
                            <li>الحجم الأقصى للملف: 5 ميجابايت</li>
                            <li>الصيغ المدعومة: xlsx, xls, csv</li>
                        </ul>
                    </div>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        إذا تركت النافذة مفتوحة أكثر من 5 دقائق أغلقها وافتحها من جديد لتجنب خطأ 419.
                    </div>
                    <div class="mb-3">
                        <label for="excel_file" class="form-label">اختر ملف Excel</label>
                        <input type="file" class="form-control" id="excel_file" name="file" accept=".xlsx,.xls,.csv" required>
                    </div>
                    <div id="uploadProgress" style="display: none;">
                        <div class="progress">
                            <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 100%">
                                جاري الرفع والمعالجة...
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary" id="importBtn">
                        <i class="fas fa-upload me-2"></i>استيراد الآن
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center flex-wrap services-toolbar">
            <h5 class="mb-0">
                <i class="fas fa-cogs me-2"></i>قائمة الخدمات
                ({{ method_exists($services, 'total') ? $services->total() : $services->count() }})
            </h5>
            <form method="GET" action="{{ route('admin.services.index') }}" class="d-flex align-items-center services-search-form" id="servicesSearchForm">
                <div class="input-group search-group">
                    <span class="input-group-text bg-white"><i class="fas fa-search"></i></span>
                    <input type="text" name="q" value="{{ request('q', $search ?? '') }}" class="form-control" placeholder="ابحث باسم، سعر، نوع...">
                </div>
                <div class="input-group type-group">
                    <span class="input-group-text bg-white"><i class="fas fa-tags"></i></span>
                    <select name="type" class="form-select">
                        <option value="">كل الأنواع</option>
                        <option value="تصوير" {{ request('type') === 'تصوير' ? 'selected' : '' }}>تصوير</option>
                        <option value="تنظيم" {{ request('type') === 'تنظيم' ? 'selected' : '' }}>تنظيم</option>
                        <option value="ديكور" {{ request('type') === 'ديكور' ? 'selected' : '' }}>ديكور</option>
                        <option value="ضيافة" {{ request('type') === 'ضيافة' ? 'selected' : '' }}>ضيافة</option>
                        <option value="ترفيه" {{ request('type') === 'ترفيه' ? 'selected' : '' }}>ترفيه</option>
                        <option value="أخرى" {{ request('type') === 'أخرى' ? 'selected' : '' }}>أخرى</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-light search-submit">
                    <i class="fas fa-search"></i>
                    <span class="submit-text ms-1">بحث</span>
                </button>
                @if(($search ?? '') !== '')
                    <a href="{{ route('admin.services.index') }}" class="btn btn-outline-secondary btn-sm">مسح</a>
                @endif
                @if(request('type'))
                    <a href="{{ route('admin.services.index', array_filter(['q' => request('q')])) }}" class="btn btn-outline-secondary btn-sm">مسح النوع</a>
                @endif
            </form>

            {{-- Bulk Actions (Desktop) --}}
            @if($services->count() > 0)
            <div class="bulk-actions-wrap d-none d-lg-inline-flex">
                <form id="bulkDeleteToolbar" method="POST" action="{{ route('admin.services.bulk-delete') }}" onsubmit="return confirm('هل أنت متأكد من حذف الخدمات المحددة؟')">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" name="ids" id="bulk_ids_input">
                    <button type="submit" class="btn btn-danger" id="bulkDeleteBtn" disabled>
                        <i class="fas fa-trash me-1"></i>حذف المحدد
                    </button>
                </form>
                <form id="bulkTypeToolbar" method="POST" action="{{ route('admin.services.bulk-update-type') }}" class="d-inline-flex align-items-center gap-2">
                    @csrf
                    <input type="hidden" name="ids" id="bulk_type_ids_input">
                    <select name="service_type" id="bulkServiceTypeSelect" class="form-select form-select-sm" style="width:auto;">
                        <option value="">اختر نوع الخدمة</option>
                        <option value="simple">ثابت</option>
                        <option value="variable">متغير</option>
                    </select>
                    <button type="submit" class="btn btn-warning btn-sm" id="bulkTypeBtn" disabled>
                        <i class="fas fa-exchange-alt me-1"></i>تغيير النوع
                    </button>
                </form>
            </div>
            @endif
        </div>
    </div>

    <div class="card-body">
        @if($services->count() > 0)

            {{-- ===== شريط Bulk للموبايل ===== --}}
            <div class="mobile-bulk-bar d-lg-none" id="mobileBulkBar">
                <span class="selected-count"><span id="mobileSelectedCount">0</span> محدد</span>
                <div class="bulk-btns">
                    <form method="POST" action="{{ route('admin.services.bulk-delete') }}" onsubmit="return confirm('حذف الخدمات المحددة؟')" id="mobileBulkDeleteForm">
                        @csrf
                        @method('DELETE')
                        <input type="hidden" name="ids" id="mobile_bulk_ids">
                        <button type="submit" class="btn btn-sm btn-danger">
                            <i class="fas fa-trash me-1"></i>حذف
                        </button>
                    </form>
                    <button type="button" class="btn btn-sm btn-outline-light" onclick="clearAllMobileSelection()">
                        <i class="fas fa-times me-1"></i>إلغاء
                    </button>
                </div>
            </div>

            {{-- ===== عرض الجدول (Desktop ≥ 992px) ===== --}}
            <div class="desktop-table-view">
                <div class="table-responsive">
                    <form id="servicesBulkForm">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th style="width:4%">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="selectAll">
                                        </div>
                                    </th>
                                    <th>الصورة</th>
                                    <th>اسم الخدمة</th>
                                    <th>السعر</th>
                                    <th>النوع</th>
                                    <th>الفئة</th>
                                    <th>الحالة</th>
                                    <th>تاريخ الإنشاء</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($services as $service)
                                    <tr>
                                        <td>
                                            <div class="form-check">
                                                <input class="form-check-input service-checkbox" type="checkbox" value="{{ $service->id }}">
                                            </div>
                                        </td>
                                        <td>
                                            <img src="{{ Str::startsWith($service->thumbnail_url, 'http') ? $service->thumbnail_url : asset($service->thumbnail_url) }}" alt="{{ $service->name }}"
                                                 class="rounded-3 shadow-sm border" style="width:80px;height:80px;object-fit:cover;">
                                        </td>
                                        <td>
                                            <strong>{{ $service->name }}</strong>
                                            <br>
                                            <small class="text-muted">{{ Str::limit($service->description, 50) }}</small>
                                        </td>
                                        <td>
                                            <span class="badge bg-info price-badge">{{ number_format($service->price) }} {{ __('common.currency') }}</span>
                                        </td>
                                        <td><span class="badge bg-secondary">{{ $service->type ?? 'عام' }}</span></td>
                                        <td>
                                            @if($service->category)
                                                <span class="badge bg-primary">{{ $service->category->name }}</span>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($service->is_active)
                                                <span class="badge bg-success">نشط</span>
                                            @else
                                                <span class="badge bg-secondary">غير نشط</span>
                                            @endif
                                        </td>
                                        <td><small class="text-muted">{{ $service->created_at->format('d/m/Y') }}</small></td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('admin.services.edit', $service) }}" class="btn btn-sm btn-outline-primary" title="تعديل">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form method="POST" action="{{ route('admin.services.destroy', $service) }}" class="d-inline"
                                                      onsubmit="return confirm('هل أنت متأكد من حذف هذه الخدمة؟')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="حذف">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </form>
                </div>
            </div>

            {{-- ===== عرض البطاقات (Mobile < 992px) ===== --}}
            <div class="mobile-cards-view">
                <div class="d-flex flex-column gap-3">
                    @foreach($services as $service)
                    <div class="service-card" id="card-{{ $service->id }}">
                        <div class="service-card-accent"></div>

                        <div class="service-card-body">
                            <div class="service-card-header-row">

                                {{-- صورة مربعة صغيرة مع أزرار عائمة --}}
                                <div class="service-card-img-wrap">
                                    <img src="{{ Str::startsWith($service->thumbnail_url, 'http') ? $service->thumbnail_url : asset($service->thumbnail_url) }}"
                                         alt="{{ $service->name }}"
                                         loading="lazy">

                                    {{-- 🗑️ زر الحذف أعلى اليسار --}}
                                    <form method="POST" action="{{ route('admin.services.destroy', $service) }}"
                                          onsubmit="return confirm('هل أنت متأكد من حذف هذه الخدمة؟')"
                                          style="display:contents;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="service-card-btn-delete" title="حذف">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>

                                    {{-- ✏️ زر التعديل أعلى اليمين --}}
                                    <a href="{{ route('admin.services.edit', $service) }}"
                                       class="service-card-btn-edit"
                                       title="تعديل">
                                        <i class="fas fa-pen"></i>
                                    </a>
                                </div>

                                {{-- معلومات الخدمة --}}
                                <div class="service-card-info">
                                    <div class="service-card-name">{{ $service->name }}</div>
                                    @if($service->description)
                                        <div class="service-card-desc">{{ $service->description }}</div>
                                    @endif
                                </div>

                                {{-- ☑️ Checkbox --}}
                                <div class="flex-shrink-0 pt-1">
                                    <input class="form-check-input mobile-service-checkbox"
                                           type="checkbox"
                                           value="{{ $service->id }}"
                                           id="mob_service_{{ $service->id }}"
                                           onchange="onMobileCheckboxChange()"
                                           style="width:20px;height:20px;border:2px solid #1f144a;border-radius:6px;cursor:pointer;">
                                </div>
                            </div>

                            {{-- Meta: السعر + الحالة + النوع + الفئة --}}
                            <div class="service-card-meta">
                                <span class="service-card-price">
                                    {{ number_format($service->price) }} {{ __('common.currency') }}
                                </span>

                                @if($service->is_active)
                                    <span class="badge-active">
                                        <i class="fas fa-circle" style="font-size:7px;vertical-align:middle;"></i>
                                        نشط
                                    </span>
                                @else
                                    <span class="badge-inactive">
                                        <i class="fas fa-circle" style="font-size:7px;vertical-align:middle;"></i>
                                        غير نشط
                                    </span>
                                @endif

                                @if($service->type)
                                    <span class="badge bg-light text-dark border" style="font-size:0.75rem;">
                                        {{ $service->type }}
                                    </span>
                                @endif

                                @if($service->category)
                                    <span class="badge" style="background:#ede9fe;color:#5b21b6;font-size:0.75rem;">
                                        {{ $service->category->name }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Pagination --}}
            <div class="d-flex justify-content-between align-items-center mt-4 flex-wrap gap-2">
                <div class="text-muted small">
                    @if(($search ?? '') !== '')
                        نتائج: "{{ $search }}"
                    @endif
                    @if(request('type'))
                        <span class="ms-2">| النوع: "{{ request('type') }}"</span>
                    @endif
                </div>
                <div>{{ $services->links() }}</div>
            </div>

        @else
            <div class="text-center py-5">
                <i class="fas fa-cogs fa-3x text-muted mb-3"></i>
                <h5>لا توجد خدمات</h5>
                <p class="text-muted">ابدأ بإضافة خدمة جديدة</p>
                <a href="{{ route('admin.services.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>إضافة خدمة جديدة
                </a>
            </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    /* =====================================================
       DESKTOP BULK LOGIC
       ===================================================== */
    const selectAll       = document.getElementById('selectAll');
    const checkboxes      = Array.from(document.querySelectorAll('.service-checkbox'));
    const bulkDeleteBtn   = document.getElementById('bulkDeleteBtn');
    const bulkIdsInput    = document.getElementById('bulk_ids_input');
    const bulkTypeBtn     = document.getElementById('bulkTypeBtn');
    const bulkTypeIdsInput= document.getElementById('bulk_type_ids_input');
    const bulkServiceTypeSelect = document.getElementById('bulkServiceTypeSelect');

    function updateBulkState() {
        const selected = checkboxes.filter(cb => cb.checked).map(cb => cb.value);
        if (bulkDeleteBtn)   bulkDeleteBtn.disabled = selected.length === 0;
        if (bulkIdsInput)    bulkIdsInput.value = selected.join(',');
        if (bulkTypeIdsInput) bulkTypeIdsInput.value = selected.join(',');
        if (bulkTypeBtn) {
            bulkTypeBtn.disabled = !(selected.length > 0 && bulkServiceTypeSelect?.value);
        }
        if (selectAll) {
            if (selected.length === checkboxes.length && checkboxes.length > 0) {
                selectAll.checked = true; selectAll.indeterminate = false;
            } else if (selected.length === 0) {
                selectAll.checked = false; selectAll.indeterminate = false;
            } else {
                selectAll.checked = false; selectAll.indeterminate = true;
            }
        }
    }

    if (selectAll) {
        selectAll.addEventListener('change', function () {
            checkboxes.forEach(cb => { cb.checked = this.checked; });
            updateBulkState();
        });
    }
    checkboxes.forEach(cb => cb.addEventListener('change', updateBulkState));
    bulkServiceTypeSelect?.addEventListener('change', updateBulkState);

    const bulkDeleteToolbarForm = document.getElementById('bulkDeleteToolbar');
    const bulkTypeForm          = document.getElementById('bulkTypeToolbar');

    bulkDeleteToolbarForm?.addEventListener('submit', updateBulkState);
    bulkTypeForm?.addEventListener('submit', function (e) {
        updateBulkState();
        if (!bulkTypeIdsInput?.value || !bulkServiceTypeSelect?.value) {
            e.preventDefault();
            alert('يرجى تحديد خدمات ونوع خدمة قبل الإرسال');
        }
    });

    updateBulkState();

    /* =====================================================
       MOBILE BULK LOGIC
       ===================================================== */
    window.onMobileCheckboxChange = function () {
        const mobileCheckboxes = Array.from(document.querySelectorAll('.mobile-service-checkbox'));
        const selected = mobileCheckboxes.filter(cb => cb.checked).map(cb => cb.value);
        const bar = document.getElementById('mobileBulkBar');
        const countEl = document.getElementById('mobileSelectedCount');
        const idsInput = document.getElementById('mobile_bulk_ids');

        if (countEl) countEl.textContent = selected.length;
        if (idsInput) idsInput.value = selected.join(',');

        // تلوين البطاقة المحددة
        mobileCheckboxes.forEach(cb => {
            const card = document.getElementById('card-' + cb.value);
            if (card) card.classList.toggle('selected', cb.checked);
        });

        if (bar) bar.classList.toggle('show', selected.length > 0);
    };

    window.clearAllMobileSelection = function () {
        document.querySelectorAll('.mobile-service-checkbox').forEach(cb => {
            cb.checked = false;
            const card = document.getElementById('card-' + cb.value);
            if (card) card.classList.remove('selected');
        });
        const bar = document.getElementById('mobileBulkBar');
        if (bar) bar.classList.remove('show');
        const idsInput = document.getElementById('mobile_bulk_ids');
        if (idsInput) idsInput.value = '';
    };

    /* =====================================================
       IMPORT MODAL
       ===================================================== */
    const importForm  = document.getElementById('importForm');
    const importModal = document.getElementById('importModal');

    importForm?.addEventListener('submit', function () {
        const btn  = document.getElementById('importBtn');
        const prog = document.getElementById('uploadProgress');
        if (btn)  { btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>جاري المعالجة...'; }
        if (prog) { prog.style.display = 'block'; }
    });

    importModal?.addEventListener('show.bs.modal', function () {
        fetch('{{ route("admin.services.index") }}', { method: 'HEAD', credentials: 'same-origin' });
    });
});
</script>
@endsection

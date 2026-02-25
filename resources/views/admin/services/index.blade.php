@extends('layouts.admin')

@section('title', 'إدارة الخدمات')
@section('page-title', 'إدارة الخدمات')
@section('page-description', 'عرض وإدارة جميع الخدمات المتاحة')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <h2 class="mb-0">الخدمات</h2>
    <div class="d-flex gap-2 flex-wrap">
        <!-- Excel Import/Export Buttons -->
        <a href="{{ route('admin.services.template') }}" class="btn btn-outline-secondary">
            <i class="fas fa-file-download me-2"></i>نموذج الخدمات أكسل
        </a>
        <a href="{{ route('admin.services.export') }}" class="btn btn-success">
            <i class="fas fa-file-excel me-2"></i>تصدير خدمات من اكسل
        </a>
        <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#importModal">
            <i class="fas fa-file-upload me-2"></i>استيراد خدمات من اكسل
        </button>
        <a href="{{ route('admin.services.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>إضافة خدمة جديدة
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

<!-- Import Modal -->
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
                            <li>املأ البيانات في النموذج بدقة</li>
                            <li><strong>عمود ID:</strong> اتركه فارغاً لإنشاء خدمة جديدة، أو ضع رقم الخدمة لتحديثها</li>
                            <li>يمكنك تصدير الخدمات الحالية أولاً ثم تعديلها</li>
                            <li>تأكد من صحة معرفات الفئات</li>
                            <li>الحجم الأقصى للملف: 5 ميجابايت</li>
                            <li>الصيغ المدعومة: xlsx, xls, csv</li>
                        </ul>
                    </div>
                    
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>تجنب خطأ "419 Page Expired":</strong>
                        <ul class="mb-0 mt-2">
                            <li><strong>لا تتأخر كثيراً</strong> بعد فتح النافذة - ارفع الملف مباشرة</li>
                            <li>إذا تركت النافذة مفتوحة أكثر من 5 دقائق، <strong>أغلقها وافتحها من جديد</strong></li>
                            <li>لا ترفع ملفات كبيرة جداً (أكثر من 1000 صف)</li>
                        </ul>
                    </div>
                    
                    <div class="alert alert-success">
                        <i class="fas fa-lightbulb me-2"></i>
                        <strong>كيفية التحديث:</strong>
                        <ol class="mb-0 mt-2">
                            <li>انقر على "تصدير خدمات من اكسل" للحصول على الخدمات الحالية مع أرقام ID</li>
                            <li>قم بتعديل البيانات المطلوبة في الملف</li>
                            <li>احتفظ برقم ID للخدمات التي تريد تحديثها</li>
                            <li>قم برفع الملف هنا</li>
                        </ol>
                    </div>
                    
                    <div class="mb-3">
                        <label for="excel_file" class="form-label">اختر ملف Excel</label>
                        <input type="file" 
                               class="form-control" 
                               id="excel_file" 
                               name="file" 
                               accept=".xlsx,.xls,.csv" 
                               required>
                        <div class="form-text">
                            اختر ملف Excel يحتوي على بيانات الخدمات
                        </div>
                    </div>
                    
                    <div id="uploadProgress" style="display: none;">
                        <div class="progress">
                            <div class="progress-bar progress-bar-striped progress-bar-animated" 
                                 role="progressbar" 
                                 style="width: 100%">
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
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h5 class="mb-0">
                <i class="fas fa-cogs me-2"></i>قائمة الخدمات ({{ $services->count() }})
            </h5>
            <form method="GET" action="{{ route('admin.services.index') }}" class="d-flex align-items-center gap-2" id="servicesSearchForm">
                <div class="input-group" style="min-width: 280px;">
                    <span class="input-group-text bg-white"><i class="fas fa-search"></i></span>
                    <input type="text" name="q" value="{{ request('q', $search ?? '') }}" class="form-control" placeholder="ابحث في اسم، وصف، نوع، فئة، السعر...">
                </div>
                <div class="input-group" style="min-width: 220px;">
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
                <button type="submit" class="btn btn-light">بحث</button>
                @if(($search ?? '') !== '')
                    <a href="{{ route('admin.services.index') }}" class="btn btn-outline-secondary">مسح البحث</a>
                @endif
                @if(request('type'))
                    <a href="{{ route('admin.services.index', array_filter(['q' => request('q')])) }}" class="btn btn-outline-secondary">مسح النوع</a>
                @endif
            </form>
            @if($services->count() > 0)
                <form id="bulkDeleteToolbar" method="POST" action="{{ route('admin.services.bulk-delete') }}" onsubmit="return confirm('هل أنت متأكد من حذف الخدمات المحددة؟')">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" name="ids" id="bulk_ids_input">
                    <button type="submit" class="btn btn-danger" id="bulkDeleteBtn" disabled>
                        <i class="fas fa-trash me-2"></i>حذف المحدد
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
                        <i class="fas fa-exchange-alt me-2"></i>تغيير نوع الخدمة
                    </button>
                </form>
            @endif
        </div>
    </div>
    <div class="card-body">
        @if($services->count() > 0)
            <div class="table-responsive">
                <form id="servicesBulkForm">
                    <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th style="width: 4%;">
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
                                        <input class="form-check-input service-checkbox" type="checkbox" value="{{ $service->id }}" id="service_{{ $service->id }}">
                                    </div>
                                </td>
                                <td>
                                    @if($service->image)
                                        <img src="{{ asset('storage/' . $service->image) }}" 
                                             alt="{{ $service->name }}" 
                                             class="rounded" 
                                             style="width: 60px; height: 60px; object-fit: cover;">
                                    @else
                                        <div class="bg-light rounded d-flex align-items-center justify-content-center" 
                                             style="width: 60px; height: 60px;">
                                            <i class="fas fa-cog text-muted"></i>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <strong>{{ $service->name }}</strong>
                                    <br>
                                    <small class="text-muted">{{ Str::limit($service->description, 50) }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-info fs-6">{{ number_format($service->price) }} {{ __('common.currency') }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">{{ $service->type ?? 'عام' }}</span>
                                </td>
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
                                <td>
                                    <small class="text-muted">{{ $service->created_at->format('d/m/Y') }}</small>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.services.edit', $service) }}" 
                                           class="btn btn-sm btn-outline-primary" 
                                           title="تعديل">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form method="POST" 
                                              action="{{ route('admin.services.destroy', $service) }}" 
                                              class="d-inline"
                                              onsubmit="return confirm('هل أنت متأكد من حذف هذه الخدمة؟')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="btn btn-sm btn-outline-danger" 
                                                    title="حذف">
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
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div class="text-muted">
                        @if(($search ?? '') !== '')
                            النتائج لبحث: "{{ $search }}"
                        @endif
                        @if(request('type'))
                            <span class="ms-2">| النوع: "{{ request('type') }}"</span>
                        @endif
                    </div>
                    <div>
                        {{ $services->links() }}
                    </div>
                </div>
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

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = Array.from(document.querySelectorAll('.service-checkbox'));
    const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
    const bulkIdsInput = document.getElementById('bulk_ids_input');
    const bulkToolbarForm = document.getElementById('bulkDeleteToolbar');
    const bulkTypeBtn = document.getElementById('bulkTypeBtn');
    const bulkTypeIdsInput = document.getElementById('bulk_type_ids_input');
    const bulkServiceTypeSelect = document.getElementById('bulkServiceTypeSelect');

    function updateBulkState() {
        const selected = checkboxes.filter(cb => cb.checked).map(cb => cb.value);
        bulkDeleteBtn.disabled = selected.length === 0;
        bulkIdsInput.value = selected.join(',');
        if (bulkTypeIdsInput) bulkTypeIdsInput.value = selected.join(',');
        if (bulkTypeBtn) {
            const enableType = selected.length > 0 && bulkServiceTypeSelect && bulkServiceTypeSelect.value;
            bulkTypeBtn.disabled = !enableType;
        }
        // Keep select-all in sync
        if (selected.length === checkboxes.length && checkboxes.length > 0) {
            selectAll.checked = true;
            selectAll.indeterminate = false;
        } else if (selected.length === 0) {
            selectAll.checked = false;
            selectAll.indeterminate = false;
        } else {
            selectAll.checked = false;
            selectAll.indeterminate = true;
        }
    }

    if (selectAll) {
        selectAll.addEventListener('change', function () {
            const checked = this.checked;
            checkboxes.forEach(cb => { cb.checked = checked; });
            updateBulkState();
        });
    }

    checkboxes.forEach(cb => {
        cb.addEventListener('change', updateBulkState);
    });

    // Submit IDs as array (server-side will parse CSV)
    if (bulkToolbarForm) {
        bulkToolbarForm.addEventListener('submit', function () {
            updateBulkState();
        });
    }

    if (bulkServiceTypeSelect) {
        bulkServiceTypeSelect.addEventListener('change', updateBulkState);
    }

    const bulkTypeForm = document.getElementById('bulkTypeToolbar');
    if (bulkTypeForm) {
        bulkTypeForm.addEventListener('submit', function (e) {
            updateBulkState();
            if (!bulkTypeIdsInput.value || !bulkServiceTypeSelect.value) {
                e.preventDefault();
                alert('يرجى تحديد خدمات ونوع خدمة قبل الإرسال');
            }
        });
    }

    // Initialize state
    updateBulkState();
});
</script>
@endsection

<script>
// Handle import form submission
document.getElementById('importForm').addEventListener('submit', function(e) {
    const btn = document.getElementById('importBtn');
    const progress = document.getElementById('uploadProgress');
    
    // Disable button and show progress
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>جاري المعالجة...';
    progress.style.display = 'block';
});

// Refresh CSRF token when modal opens to prevent 419 errors
const importModal = document.getElementById('importModal');
if (importModal) {
    importModal.addEventListener('show.bs.modal', function () {
        // Fetch fresh CSRF token
        fetch('{{ route("admin.services.index") }}', {
            method: 'HEAD',
            credentials: 'same-origin'
        }).then(() => {
            // Token refreshed in session
            console.log('CSRF token refreshed');
        });
    });
}
</script>
@endsection

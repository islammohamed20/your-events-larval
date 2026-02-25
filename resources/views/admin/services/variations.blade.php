@extends('layouts.admin')

@section('title', 'إدارة تنويعات الخدمة')
@section('page-title', 'إدارة تنويعات الخدمة')
@section('page-description', $service->name)

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Back Button -->
        <div class="mb-3">
            <a href="{{ route('admin.services.edit', $service) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-right me-2"></i>العودة للخدمة
            </a>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Service Info Card -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-info-circle me-2"></i>معلومات الخدمة
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <h4>{{ $service->name }}</h4>
                        <p class="text-muted mb-2">{{ $service->description }}</p>
                        <div class="mt-3">
                            <span class="badge bg-{{ $service->service_type === 'variable' ? 'info' : 'primary' }}">
                                {{ $service->service_type === 'variable' ? 'خدمة متغيرة' : 'خدمة بسيطة' }}
                            </span>
                            @if($service->attributes->count() > 0)
                                <span class="badge bg-secondary">{{ $service->attributes->count() }} خاصية</span>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-4 text-end">
                        @if($service->image)
                            <img src="{{ Storage::url($service->image) }}" alt="{{ $service->name }}" class="img-thumbnail" style="max-height: 100px;">
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Attributes Info -->
        @if($service->attributes->count() > 0)
        <div class="card mb-4">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0">
                    <i class="fas fa-tags me-2"></i>خصائص الخدمة المتاحة
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($service->attributes as $attribute)
                        <div class="col-md-4 mb-3">
                            <div class="border rounded p-3">
                                <h6 class="text-primary">{{ $attribute->name }}</h6>
                                <div class="d-flex flex-wrap gap-1">
                                    @foreach($attribute->values as $value)
                                        <span class="badge bg-light text-dark border">{{ $value->value }}</span>
                                    @endforeach
                                </div>
                                <small class="text-muted">{{ $attribute->values->count() }} قيمة</small>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        @else
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle me-2"></i>
            لا توجد خصائص مرتبطة بهذه الخدمة. يجب إضافة خصائص أولاً من 
            <a href="{{ route('admin.services.edit', $service) }}" class="alert-link">صفحة تعديل الخدمة</a>
        </div>
        @endif

        <!-- Variations List -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-layer-group me-2"></i>التنويعات الحالية
                    <span class="badge bg-primary">{{ $variations->count() }}</span>
                </h5>
                <div>
                    @if($service->attributes->count() > 0)
                        <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addVariationModal">
                            <i class="fas fa-plus me-2"></i>إضافة تنويعة يدوياً
                        </button>
                        <button type="button" class="btn btn-primary btn-sm" onclick="generateAllVariations()">
                            <i class="fas fa-magic me-2"></i>توليد كل التنويعات تلقائياً
                        </button>
                    @endif
                </div>
            </div>
            <div class="card-body">
                @if($variations->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>SKU</th>
                                    <th>التنويعة</th>
                                    <th>السعر</th>
                                    <th>سعر التخفيض</th>
                                    <th>المخزون</th>
                                    <th>الحالة</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($variations as $variation)
                                    <tr>
                                        <td><code>{{ $variation->sku }}</code></td>
                                        <td>
                                            @php
                                                $attrs = is_array($variation->attributes) ? $variation->attributes : json_decode($variation->attributes, true);
                                            @endphp
                                            @if(is_array($attrs))
                                                @foreach($attrs as $key => $value)
                                                    <span class="badge bg-secondary">{{ $key }}: {{ $value }}</span>
                                                @endforeach
                                            @endif
                                        </td>
                                        <td><strong>{{ number_format($variation->price, 2) }} {{ __('common.currency') }}</strong></td>
                                        <td>
                                            @if($variation->sale_price)
                                                <span class="text-danger">{{ number_format($variation->sale_price, 2) }} {{ __('common.currency') }}</span>
                                            @else
                                                —
                                            @endif
                                        </td>
                                        <td>
                                            @if($variation->stock !== null)
                                                <span class="badge bg-{{ $variation->stock > 0 ? 'success' : 'danger' }}">
                                                    {{ $variation->stock }}
                                                </span>
                                            @else
                                                —
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $variation->is_active ? 'success' : 'secondary' }}">
                                                {{ $variation->is_active ? 'نشط' : 'غير نشط' }}
                                            </span>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-primary" onclick="editVariation({{ $variation->id }})">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <form action="{{ route('admin.services.variations.destroy', [$service, $variation]) }}" 
                                                  method="POST" 
                                                  class="d-inline"
                                                  onsubmit="return confirm('هل أنت متأكد من حذف هذه التنويعة؟')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                        <p class="text-muted">لا توجد تنويعات حتى الآن</p>
                        @if($service->attributes->count() > 0)
                            <button type="button" class="btn btn-primary" onclick="generateAllVariations()">
                                <i class="fas fa-magic me-2"></i>توليد التنويعات تلقائياً
                            </button>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Add Variation Modal -->
<div class="modal fade" id="addVariationModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('admin.services.variations.store', $service) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">إضافة تنويعة جديدة</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <!-- Select Attribute Values -->
                    @foreach($service->attributes as $attribute)
                        <div class="mb-3">
                            <label class="form-label">{{ $attribute->name }} <span class="text-danger">*</span></label>
                            <select name="attribute_values[{{ $attribute->id }}]" class="form-select" required>
                                <option value="">اختر {{ $attribute->name }}</option>
                                @foreach($attribute->values as $value)
                                    <option value="{{ $value->id }}">{{ $value->value }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endforeach

                    <hr>

                    <!-- SKU -->
                    <div class="mb-3">
                        <label class="form-label">SKU (كود المنتج)</label>
                        <input type="text" name="sku" class="form-control" placeholder="سيتم توليده تلقائياً">
                        <small class="text-muted">اتركه فارغاً للتوليد التلقائي</small>
                    </div>

                    <!-- Price -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">السعر <span class="text-danger">*</span></label>
                                <input type="number" name="price" class="form-control" step="0.01" min="0" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">سعر التخفيض</label>
                                <input type="number" name="sale_price" class="form-control" step="0.01" min="0">
                            </div>
                        </div>
                    </div>

                    <!-- Stock -->
                    <div class="mb-3">
                        <label class="form-label">المخزون</label>
                        <input type="number" name="stock" class="form-control" min="0">
                        <small class="text-muted">اتركه فارغاً لعدم تتبع المخزون</small>
                    </div>

                    <!-- Is Active -->
                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" name="is_active" class="form-check-input" id="is_active_new" value="1" checked>
                            <label class="form-check-label" for="is_active_new">نشط</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">حفظ التنويعة</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Variation Modal -->
<div class="modal fade" id="editVariationModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="editVariationForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">تعديل التنويعة</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="editVariationContent">
                    <!-- Will be loaded via AJAX -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">حفظ التغييرات</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
function generateAllVariations() {
    if (!confirm('هل تريد توليد جميع التنويعات الممكنة تلقائياً؟\n\nسيتم إنشاء تنويعة لكل مجموعة ممكنة من الخصائص.')) {
        return;
    }
    
    // Show loading
    const btn = event.target;
    const originalHTML = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>جاري التوليد...';
    
    fetch('{{ route('admin.services.variations.generate', $service) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert('خطأ: ' + (data.message || 'حدث خطأ غير متوقع'));
            btn.disabled = false;
            btn.innerHTML = originalHTML;
        }
    })
    .catch(error => {
        alert('حدث خطأ: ' + error.message);
        btn.disabled = false;
        btn.innerHTML = originalHTML;
    });
}

function editVariation(id) {
    // Load variation data and show modal
    fetch(`{{ route('admin.services.variations.index', $service) }}/${id}/edit`)
        .then(response => response.json())
        .then(data => {
            if (!data.success) {
                alert('حدث خطأ في تحميل البيانات');
                return;
            }
            
            const variation = data.variation;
            
            // Build HTML content
            let html = `
                <!-- Variation Info -->
                <div class="mb-3">
                    <label class="form-label fw-bold">معلومات التنويعة</label>
                    <div class="card bg-light">
                        <div class="card-body">
                            <p class="mb-1"><strong>SKU:</strong> ${variation.sku}</p>
                            <p class="mb-0"><strong>الخصائص:</strong></p>
                            <ul class="mb-0">`;
            
            for (const [key, value] of Object.entries(variation.attributes)) {
                html += `<li>${key}: <strong>${value}</strong></li>`;
            }
            
            html += `
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Price -->
                <div class="mb-3">
                    <label for="edit_price" class="form-label">السعر <span class="text-danger">*</span></label>
                    <input type="number" name="price" id="edit_price" class="form-control" 
                           step="0.01" min="0" value="${variation.price}" required>
                </div>

                <!-- Sale Price -->
                <div class="mb-3">
                    <label for="edit_sale_price" class="form-label">سعر التخفيض</label>
                    <input type="number" name="sale_price" id="edit_sale_price" class="form-control" 
                           step="0.01" min="0" value="${variation.sale_price || ''}">
                    <small class="text-muted">اتركه فارغاً إذا لم يكن هناك تخفيض</small>
                </div>

                <!-- Stock -->
                <div class="mb-3">
                    <label for="edit_stock" class="form-label">المخزون</label>
                    <input type="number" name="stock" id="edit_stock" class="form-control" 
                           min="0" value="${variation.stock || ''}">
                    <small class="text-muted">اتركه فارغاً لعدم تتبع المخزون</small>
                </div>

                <!-- Is Active -->
                <div class="mb-3">
                    <div class="form-check">
                        <input type="checkbox" name="is_active" class="form-check-input" 
                               id="edit_is_active" value="1" ${variation.is_active ? 'checked' : ''}>
                        <label class="form-check-label" for="edit_is_active">نشط</label>
                    </div>
                </div>
            `;
            
            document.getElementById('editVariationContent').innerHTML = html;
            document.getElementById('editVariationForm').action = 
                `{{ route('admin.services.variations.index', $service) }}/${id}`;
            new bootstrap.Modal(document.getElementById('editVariationModal')).show();
        })
        .catch(error => {
            console.error('Error:', error);
            alert('حدث خطأ في تحميل البيانات');
        });
}
</script>
@endsection

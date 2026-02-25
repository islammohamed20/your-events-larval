@extends('layouts.admin')

@section('title', 'تنويعات الخدمة')
@section('page-title', 'إدارة التنويعات: ' . $service->name)
@section('page-description', 'إضافة وتعديل وحذف تنويعات الخدمة')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-list me-2"></i>التنويعات الحالية</h5>
                <a href="{{ route('admin.services.edit', $service) }}" class="btn btn-sm btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i>عودة إلى تعديل الخدمة
                </a>
            </div>
            <div class="card-body">
                @if($service->variations->count() === 0)
                    <div class="alert alert-info">لا توجد تنويعات بعد.</div>
                @else
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>التركيبة</th>
                                    <th>السعر</th>
                                    <th>الحالة</th>
                                    <th>إجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($service->variations as $variation)
                                    <tr>
                                        <td>{{ $variation->id }}</td>
                                        <td>
                                            @php
                                                $labels = [];
                                                foreach ($variation->attribute_value_ids as $valId) {
                                                    $val = \App\Models\AttributeValue::find($valId);
                                                    if ($val) {
                                                        $labels[] = $val->attribute->name . ': ' . $val->value;
                                                    }
                                                }
                                            @endphp
                                            {{ implode('، ', $labels) }}
                                        </td>
                                        <td>{{ number_format($variation->price, 2) }} {{ __('common.currency') }}</td>
                                        <td>
                                            @if($variation->is_active)
                                                <span class="badge bg-success">نشط</span>
                                            @else
                                                <span class="badge bg-secondary">موقوف</span>
                                            @endif
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editVariationModal" data-variation='@json($variation)'>
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <form action="{{ route('admin.services.variations.destroy', [$service, $variation]) }}" method="POST" class="d-inline" onsubmit="return confirm('تأكيد الحذف؟')">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-plus me-2"></i>إضافة تنويع جديد</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.services.variations.store', $service) }}">
                    @csrf
                    <div class="row g-3">
                        @foreach($attributes as $attribute)
                            <div class="col-md-6">
                                <label class="form-label">{{ $attribute->name }}</label>
                                <select class="form-select" name="attribute_values[{{ $attribute->id }}]" required>
                                    <option value="">-- اختر {{ $attribute->name }} --</option>
                                    @foreach($attribute->values as $val)
                                        <option value="{{ $val->id }}">{{ $val->value }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endforeach
                        <div class="col-md-6">
                            <label class="form-label">السعر</label>
                            <input type="number" class="form-control" name="price" min="0" step="0.01" required>
                        </div>
                        <div class="col-md-6 d-flex align-items-center">
                            <div class="form-check mt-4">
                                <input class="form-check-input" type="checkbox" name="is_active" id="is_active_new" checked>
                                <label class="form-check-label" for="is_active_new">نشط</label>
                            </div>
                        </div>
                    </div>
                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>حفظ التنويع</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>إرشادات</h6>
            </div>
            <div class="card-body">
                <ul class="mb-0">
                    <li>التنويعات تمثل تركيبات من قيم الخصائص مع سعر محدد.</li>
                    <li>احرص على عدم تكرار تركيبة موجودة بالفعل.</li>
                    <li>يمكنك تعديل حالة التنويع إلى موقوف لإخفائه مؤقتاً.</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Edit Variation Modal -->
<div class="modal fade" id="editVariationModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">تعديل التنويع</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="editVariationForm" method="POST">
        @csrf
        @method('PUT')
        <div class="modal-body">
          <div class="row g-3">
            @foreach($attributes as $attribute)
              <div class="col-md-6">
                <label class="form-label">{{ $attribute->name }}</label>
                <select class="form-select" name="attribute_values[{{ $attribute->id }}]" required>
                  <option value="">-- اختر {{ $attribute->name }} --</option>
                  @foreach($attribute->values as $val)
                    <option value="{{ $val->id }}">{{ $val->value }}</option>
                  @endforeach
                </select>
              </div>
            @endforeach
            <div class="col-md-6">
              <label class="form-label">السعر</label>
              <input type="number" class="form-control" name="price" min="0" step="0.01" required>
            </div>
            <div class="col-md-6 d-flex align-items-center">
              <div class="form-check mt-4">
                <input class="form-check-input" type="checkbox" name="is_active" id="is_active_edit">
                <label class="form-check-label" for="is_active_edit">نشط</label>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
          <button type="submit" class="btn btn-primary">حفظ</button>
        </div>
      </form>
    </div>
  </div>
</div>

@push('scripts')
<script>
(function(){
  const modal = document.getElementById('editVariationModal');
  modal.addEventListener('show.bs.modal', function(event){
    const button = event.relatedTarget;
    const variation = JSON.parse(button.getAttribute('data-variation'));
    const form = document.getElementById('editVariationForm');
    form.action = `{{ url('/admin/services') }}/${variation.service_id}/variations/${variation.id}`;
    form.querySelector('input[name="price"]').value = variation.price;
    form.querySelector('input[name="is_active"]').checked = !!variation.is_active;

    // Select current value ids
    const current = variation.attribute_value_ids || [];
    const selects = form.querySelectorAll('select[name^="attribute_values["]');
    selects.forEach(function(sel){
      // pick the first match among current ids that belongs to this attribute
      const options = sel.querySelectorAll('option');
      options.forEach(opt => { opt.selected = current.includes(parseInt(opt.value)); });
    });
  });
})();
</script>
@endpush
@endsection

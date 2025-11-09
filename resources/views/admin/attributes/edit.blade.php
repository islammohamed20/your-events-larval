@extends('layouts.admin')

@section('title', 'تعديل الخاصية - Your Events')
@section('page-title', 'تعديل الخاصية: ' . $attribute->name)
@section('page-description', 'تعديل بيانات الخاصية وإدارة قيمها')

@section('content')
<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-edit me-2"></i>تعديل بيانات الخاصية
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.attributes.update', $attribute) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="name" class="form-label">
                            اسم الخاصية <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               class="form-control @error('name') is-invalid @enderror" 
                               id="name" 
                               name="name" 
                               value="{{ old('name', $attribute->name) }}"
                               required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="slug" class="form-label">
                            Slug
                        </label>
                        <input type="text" 
                               class="form-control @error('slug') is-invalid @enderror" 
                               id="slug" 
                               name="slug" 
                               value="{{ old('slug', $attribute->slug) }}">
                        @error('slug')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="type" class="form-label">
                            نوع الحقل <span class="text-danger">*</span>
                        </label>
                        <select class="form-select @error('type') is-invalid @enderror" 
                                id="type" 
                                name="type" 
                                required>
                            <option value="select" {{ old('type', $attribute->type) == 'select' ? 'selected' : '' }}>
                                قائمة منسدلة (Select)
                            </option>
                            <option value="radio" {{ old('type', $attribute->type) == 'radio' ? 'selected' : '' }}>
                                اختيار واحد (Radio)
                            </option>
                            <option value="checkbox" {{ old('type', $attribute->type) == 'checkbox' ? 'selected' : '' }}>
                                اختيارات متعددة (Checkbox)
                            </option>
                        </select>
                        @error('type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="order" class="form-label">
                            الترتيب
                        </label>
                        <input type="number" 
                               class="form-control @error('order') is-invalid @enderror" 
                               id="order" 
                               name="order" 
                               value="{{ old('order', $attribute->order) }}"
                               min="0">
                        @error('order')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox" 
                               class="form-check-input" 
                               id="is_active" 
                               name="is_active"
                               {{ old('is_active', $attribute->is_active) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">
                            نشط
                        </label>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>حفظ التعديلات
                        </button>
                        <a href="{{ route('admin.attributes.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-right me-2"></i>العودة
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-list me-2"></i>قيم الخاصية
                </h5>
                <button type="button" 
                        class="btn btn-sm btn-success" 
                        data-bs-toggle="modal" 
                        data-bs-target="#addValueModal">
                    <i class="fas fa-plus me-1"></i>إضافة قيمة
                </button>
            </div>
            <div class="card-body">
                @if($attribute->values->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>الترتيب</th>
                                    <th>القيمة</th>
                                    <th>Slug</th>
                                    <th>الحالة</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($attribute->values as $value)
                                    <tr>
                                        <td>{{ $value->order }}</td>
                                        <td><strong>{{ $value->value }}</strong></td>
                                        <td><code class="small">{{ $value->slug }}</code></td>
                                        <td>
                                            @if($value->is_active)
                                                <span class="badge bg-success">نشط</span>
                                            @else
                                                <span class="badge bg-danger">غير نشط</span>
                                            @endif
                                        </td>
                                        <td>
                                            <button type="button" 
                                                    class="btn btn-sm btn-outline-primary edit-value-btn"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#editValueModal{{ $value->id }}">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <form action="{{ route('admin.attributes.values.destroy', [$attribute, $value]) }}" 
                                                  method="POST" 
                                                  class="d-inline"
                                                  onsubmit="return confirm('هل أنت متأكد من حذف هذه القيمة؟')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>

                                    <!-- Edit Value Modal -->
                                    <div class="modal fade" id="editValueModal{{ $value->id }}" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form action="{{ route('admin.attributes.values.update', [$attribute, $value]) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">تعديل القيمة</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="mb-3">
                                                            <label class="form-label">القيمة <span class="text-danger">*</span></label>
                                                            <input type="text" class="form-control" name="value" value="{{ $value->value }}" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Slug</label>
                                                            <input type="text" class="form-control" name="slug" value="{{ $value->slug }}">
                                                            <small class="text-muted">اتركه فارغاً للتوليد التلقائي</small>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">الترتيب</label>
                                                            <input type="number" class="form-control" name="order" value="{{ $value->order }}" min="0">
                                                        </div>
                                                        <div class="form-check">
                                                            <input type="checkbox" class="form-check-input" name="is_active" {{ $value->is_active ? 'checked' : '' }}>
                                                            <label class="form-check-label">نشط</label>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                                                        <button type="submit" class="btn btn-primary">حفظ التعديلات</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-inbox fa-2x text-muted mb-2"></i>
                        <p class="text-muted">لا توجد قيم لهذه الخاصية</p>
                        <button type="button" 
                                class="btn btn-sm btn-primary" 
                                data-bs-toggle="modal" 
                                data-bs-target="#addValueModal">
                            <i class="fas fa-plus me-1"></i>إضافة أول قيمة
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Add Value Modal -->
<div class="modal fade" id="addValueModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.attributes.values.store', $attribute) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">إضافة قيمة جديدة</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">القيمة <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="value" placeholder="مثال: 50-100 شخص" required>
                        <small class="text-muted">القيمة التي ستظهر للمستخدم</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Slug (اختياري)</label>
                        <input type="text" class="form-control" name="slug" placeholder="50-100">
                        <small class="text-muted">اتركه فارغاً للتوليد التلقائي من القيمة</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">الترتيب</label>
                        <input type="number" class="form-control" name="order" value="0" min="0">
                    </div>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="add_is_active" name="is_active" checked>
                        <label class="form-check-label" for="add_is_active">نشط</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save me-1"></i>إضافة
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@if(session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }, 100);
        });
    </script>
@endif
@endsection

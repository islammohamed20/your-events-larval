@extends('layouts.admin')

@section('title', 'إدارة الفئات')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">إدارة فئات الخدمات</h1>
        <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>إضافة فئة جديدة
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 5%;">#</th>
                            <th style="width: 10%;">الصورة</th>
                            <th style="width: 20%;">الاسم</th>
                            <th style="width: 25%;">الوصف</th>
                            <th style="width: 10%;">الأيقونة</th>
                            <th style="width: 10%;" class="text-center">عدد الخدمات</th>
                            <th style="width: 5%;" class="text-center">الترتيب</th>
                            <th style="width: 10%;" class="text-center">الحالة</th>
                            <th style="width: 10%;" class="text-center">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categories as $category)
                        <tr>
                            <td>{{ $category->id }}</td>
                            <td>
                                @if($category->image)
                                    <img src="{{ Storage::url($category->image) }}" 
                                         alt="{{ $category->name }}" 
                                         class="img-thumbnail" 
                                         style="width: 60px; height: 60px; object-fit: cover;">
                                @else
                                    <div class="bg-light d-flex align-items-center justify-content-center" 
                                         style="width: 60px; height: 60px; border-radius: 8px;">
                                        <i class="fas fa-image text-muted"></i>
                                    </div>
                                @endif
                            </td>
                            <td>
                                <div class="fw-bold">{{ $category->name }}</div>
                                @if($category->name_en)
                                    <small class="text-muted">{{ $category->name_en }}</small>
                                @endif
                            </td>
                            <td>
                                <small>{{ Str::limit($category->description, 80) }}</small>
                            </td>
                            <td class="text-center">
                                @if($category->icon)
                                    <i class="{{ $category->icon }} fa-2x" style="color: {{ $category->color }}"></i>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge bg-info">{{ $category->services_count }}</span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-secondary">{{ $category->order }}</span>
                            </td>
                            <td class="text-center">
                                <form action="{{ route('admin.categories.toggle-active', $category) }}" method="POST" style="display: inline;">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-sm {{ $category->is_active ? 'btn-success' : 'btn-secondary' }}">
                                        <i class="fas fa-{{ $category->is_active ? 'check' : 'times' }}"></i>
                                        {{ $category->is_active ? 'نشطة' : 'معطلة' }}
                                    </button>
                                </form>
                            </td>
                            <td class="text-center">
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.categories.edit', $category) }}" 
                                       class="btn btn-sm btn-outline-primary"
                                       title="تعديل">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" 
                                            class="btn btn-sm btn-outline-danger"
                                            onclick="confirmDelete({{ $category->id }})"
                                            title="حذف">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                                <form id="delete-form-{{ $category->id }}" 
                                      action="{{ route('admin.categories.destroy', $category) }}" 
                                      method="POST" 
                                      style="display: none;">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-5">
                                <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                                <p class="text-muted">لا توجد فئات حالياً</p>
                                <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus me-2"></i>إضافة أول فئة
                                </a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($categories->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $categories->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<script>
function confirmDelete(id) {
    if (confirm('هل أنت متأكد من حذف هذه الفئة؟')) {
        document.getElementById('delete-form-' + id).submit();
    }
}
</script>
@endsection

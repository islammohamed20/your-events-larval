@extends('layouts.admin')

@section('title', 'إدارة الصفحة الرئيسية')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">إدارة الصفحة الرئيسية</h1>
        <div>
            <a href="{{ route('admin.homepage.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>إضافة قسم جديد
            </a>
            <a href="{{ route('admin.homepage.initialize') }}" class="btn btn-secondary" 
               onclick="return confirm('هل تريد تهيئة الأقسام الافتراضية؟')">
                <i class="fas fa-sync me-2"></i>تهيئة افتراضية
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">أقسام الصفحة الرئيسية</h6>
            <small class="text-muted">
                <i class="fas fa-info-circle"></i> اسحب الأقسام لإعادة ترتيبها
            </small>
        </div>
        <div class="card-body">
            @if($sections->isEmpty())
                <div class="alert alert-info text-center">
                    <i class="fas fa-info-circle fa-3x mb-3"></i>
                    <p class="mb-0">لا توجد أقسام حالياً. قم بإضافة قسم جديد أو استخدم التهيئة الافتراضية.</p>
                </div>
            @else
                <div id="sections-list" class="list-group">
                    @foreach($sections as $section)
                        <div class="list-group-item list-group-item-action section-item" 
                             data-id="{{ $section->id }}" 
                             style="cursor: move;">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <i class="fas fa-grip-vertical text-muted fa-lg"></i>
                                </div>
                                <div class="col-1">
                                    <span class="badge bg-secondary">{{ $section->order }}</span>
                                </div>
                                <div class="col-2">
                                    <strong>{{ $section->section_key }}</strong>
                                </div>
                                <div class="col-4">
                                    <div>
                                        <strong>{{ $section->title }}</strong>
                                        @if($section->subtitle)
                                            <br><small class="text-muted">{{ $section->subtitle }}</small>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-2 text-center">
                                    <div class="form-check form-switch d-inline-block">
                                        <input class="form-check-input status-toggle" 
                                               type="checkbox" 
                                               data-id="{{ $section->id }}"
                                               {{ $section->is_active ? 'checked' : '' }}>
                                        <label class="form-check-label">
                                            {{ $section->is_active ? 'مفعّل' : 'معطّل' }}
                                        </label>
                                    </div>
                                </div>
                                <div class="col-auto ms-auto">
                                    <div class="btn-group">
                                        <a href="{{ route('admin.homepage.edit', $section) }}" 
                                           class="btn btn-sm btn-outline-primary" 
                                           title="تعديل">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.homepage.destroy', $section) }}" 
                                              method="POST" 
                                              class="d-inline"
                                              onsubmit="return confirm('هل أنت متأكد من حذف هذا القسم؟')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="btn btn-sm btn-outline-danger" 
                                                    title="حذف">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Sortable for drag and drop
    const sectionsList = document.getElementById('sections-list');
    if (sectionsList) {
        new Sortable(sectionsList, {
            animation: 150,
            handle: '.fa-grip-vertical',
            onEnd: function(evt) {
                updateOrder();
            }
        });
    }

    // Update order after drag and drop
    function updateOrder() {
        const items = document.querySelectorAll('.section-item');
        const orders = Array.from(items).map((item, index) => item.dataset.id);

        fetch('{{ route("admin.homepage.update-order") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ orders: orders })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('success', data.message);
            }
        });
    }

    // Toggle active status
    document.querySelectorAll('.status-toggle').forEach(toggle => {
        toggle.addEventListener('change', function() {
            const sectionId = this.dataset.id;
            
            fetch(`/admin/homepage/${sectionId}/toggle`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast('success', data.message);
                    this.nextElementSibling.textContent = data.is_active ? 'مفعّل' : 'معطّل';
                }
            });
        });
    });

    // Toast notification
    function showToast(type, message) {
        const toast = document.createElement('div');
        toast.className = `alert alert-${type} alert-dismissible fade show position-fixed top-0 end-0 m-3`;
        toast.style.zIndex = '9999';
        toast.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        document.body.appendChild(toast);
        
        setTimeout(() => {
            toast.remove();
        }, 3000);
    }
});
</script>
@endpush
@endsection

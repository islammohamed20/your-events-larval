@extends('layouts.admin')

@section('title', 'إدارة سلايدات البانر الرئيسي')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">إدارة سلايدات البانر الرئيسي</h1>
        <a href="{{ route('admin.hero-slides.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>إضافة سلايد جديد
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">قائمة السلايدات</h6>
            <small class="text-muted">
                <i class="fas fa-info-circle"></i> اسحب السلايدات لإعادة ترتيبها
            </small>
        </div>
        <div class="card-body">
            @if($slides->isEmpty())
                <div class="alert alert-info text-center">
                    <i class="fas fa-images fa-3x mb-3"></i>
                    <p class="mb-0">لا توجد سلايدات حالياً. قم بإضافة سلايد جديد للبدء.</p>
                </div>
            @else
                <div id="slides-list" class="row g-3">
                    @foreach($slides as $slide)
                        <div class="col-md-6 col-lg-4 slide-item" data-id="{{ $slide->id }}">
                            <div class="card h-100 shadow-sm" style="cursor: move;">
                                <div class="position-relative">
                                    <i class="fas fa-grip-vertical position-absolute top-0 start-0 m-2 text-white bg-dark bg-opacity-75 p-2 rounded"></i>
                                    <img src="{{ Storage::url($slide->image) }}" 
                                         class="card-img-top" 
                                         alt="{{ $slide->title }}"
                                         style="height: 200px; object-fit: cover;">
                                    <span class="badge bg-secondary position-absolute top-0 end-0 m-2">
                                        #{{ $slide->order }}
                                    </span>
                                </div>
                                <div class="card-body">
                                    <h5 class="card-title">{{ $slide->title }}</h5>
                                    @if($slide->subtitle)
                                        <p class="card-text text-muted small">{{ Str::limit($slide->subtitle, 60) }}</p>
                                    @endif
                                    
                                    <div class="d-flex justify-content-between align-items-center mt-3">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input status-toggle" 
                                                   type="checkbox" 
                                                   data-id="{{ $slide->id }}"
                                                   {{ $slide->is_active ? 'checked' : '' }}>
                                            <label class="form-check-label small">
                                                {{ $slide->is_active ? 'مفعّل' : 'معطّل' }}
                                            </label>
                                        </div>
                                        
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('admin.hero-slides.edit', $slide) }}" 
                                               class="btn btn-outline-primary" 
                                               title="تعديل">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.hero-slides.destroy', $slide) }}" 
                                                  method="POST" 
                                                  class="d-inline"
                                                  onsubmit="return confirm('هل أنت متأكد من حذف هذا السلايد؟')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="btn btn-outline-danger" 
                                                        title="حذف">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
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
    const slidesList = document.getElementById('slides-list');
    if (slidesList) {
        new Sortable(slidesList, {
            animation: 150,
            handle: '.fa-grip-vertical',
            onEnd: function(evt) {
                updateOrder();
            }
        });
    }

    // Update order after drag and drop
    function updateOrder() {
        const items = document.querySelectorAll('.slide-item');
        const orders = Array.from(items).map((item, index) => item.dataset.id);

        fetch('{{ route("admin.hero-slides.update-order") }}', {
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
            const slideId = this.dataset.id;
            
            fetch(`/admin/hero-slides/${slideId}/toggle`, {
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

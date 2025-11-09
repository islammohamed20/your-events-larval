@extends('layouts.admin')

@section('title', 'قوالب البريد الإلكتروني')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-envelope me-2"></i>
            قوالب البريد الإلكتروني
        </h1>
        <div>
            <a href="{{ route('admin.email-test.index') }}" class="btn btn-outline-primary me-2">
                <i class="fas fa-paper-plane me-2"></i>اختبار البريد
            </a>
            <a href="{{ route('admin.email-templates.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>إضافة قالب جديد
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-list me-2"></i>
                        جميع القوالب
                    </h6>
                </div>
                <div class="card-body">
                    @if($templates->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th width="5%">#</th>
                                        <th width="20%">اسم القالب</th>
                                        <th width="15%">النوع</th>
                                        <th width="25%">الموضوع</th>
                                        <th width="15%">الحالة</th>
                                        <th width="10%">التاريخ</th>
                                        <th width="10%">الإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($templates as $template)
                                        <tr>
                                            <td>{{ $template->id }}</td>
                                            <td>
                                                <strong>{{ $template->name }}</strong>
                                                <br>
                                                <small class="text-muted">{{ $template->slug }}</small>
                                            </td>
                                            <td>
                                                @php
                                                    $typeLabels = [
                                                        'booking' => ['label' => 'تأكيد حجز', 'color' => 'success', 'icon' => 'calendar-check'],
                                                        'welcome' => ['label' => 'ترحيب', 'color' => 'info', 'icon' => 'user-plus'],
                                                        'reset_password' => ['label' => 'إعادة كلمة المرور', 'color' => 'warning', 'icon' => 'key'],
                                                        'invoice' => ['label' => 'فاتورة', 'color' => 'primary', 'icon' => 'file-invoice'],
                                                        'custom' => ['label' => 'مخصص', 'color' => 'secondary', 'icon' => 'cog'],
                                                    ];
                                                    $typeData = $typeLabels[$template->type] ?? $typeLabels['custom'];
                                                @endphp
                                                <span class="badge bg-{{ $typeData['color'] }}">
                                                    <i class="fas fa-{{ $typeData['icon'] }} me-1"></i>
                                                    {{ $typeData['label'] }}
                                                </span>
                                            </td>
                                            <td>{{ Str::limit($template->subject, 40) }}</td>
                                            <td>
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input toggle-active" 
                                                           type="checkbox" 
                                                           data-id="{{ $template->id }}"
                                                           {{ $template->is_active ? 'checked' : '' }}>
                                                    <label class="form-check-label">
                                                        <span class="status-text">
                                                            {{ $template->is_active ? 'مفعل' : 'معطل' }}
                                                        </span>
                                                    </label>
                                                </div>
                                            </td>
                                            <td>
                                                <small>{{ $template->created_at->format('Y/m/d') }}</small>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('admin.email-templates.show', $template) }}" 
                                                       class="btn btn-sm btn-info" 
                                                       title="معاينة">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('admin.email-templates.edit', $template) }}" 
                                                       class="btn btn-sm btn-primary" 
                                                       title="تعديل">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button type="button" 
                                                            class="btn btn-sm btn-success duplicate-btn" 
                                                            data-id="{{ $template->id }}"
                                                            title="نسخ">
                                                        <i class="fas fa-copy"></i>
                                                    </button>
                                                    <button type="button" 
                                                            class="btn btn-sm btn-danger delete-btn" 
                                                            data-id="{{ $template->id }}" 
                                                            data-name="{{ $template->name }}"
                                                            title="حذف">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-inbox fa-4x text-muted mb-3"></i>
                            <h5 class="text-muted">لا توجد قوالب بريد إلكتروني</h5>
                            <p class="text-muted">قم بإضافة قالب جديد للبدء</p>
                            <a href="{{ route('admin.email-templates.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>إضافة قالب جديد
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Info Cards -->
    <div class="row mt-4">
        <div class="col-md-3">
            <div class="card border-left-primary shadow h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">إجمالي القوالب</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $templates->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-envelope fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-left-success shadow h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">قوالب مفعلة</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $templates->where('is_active', true)->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-left-warning shadow h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">قوالب معطلة</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $templates->where('is_active', false)->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-times-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-left-info shadow h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">أنواع مختلفة</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $templates->unique('type')->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-list fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    تأكيد الحذف
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>هل أنت متأكد من حذف قالب البريد الإلكتروني: <strong id="template-name"></strong>؟</p>
                <p class="text-danger mb-0">
                    <i class="fas fa-info-circle me-2"></i>
                    هذا الإجراء لا يمكن التراجع عنه!
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <form id="delete-form" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-2"></i>حذف
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle Active Status
    document.querySelectorAll('.toggle-active').forEach(toggle => {
        toggle.addEventListener('change', function() {
            const templateId = this.dataset.id;
            const statusText = this.closest('td').querySelector('.status-text');
            
            fetch(`/admin/email-templates/${templateId}/toggle`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    statusText.textContent = data.is_active ? 'مفعل' : 'معطل';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                this.checked = !this.checked;
            });
        });
    });

    // Delete Template
    document.querySelectorAll('.delete-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const templateId = this.dataset.id;
            const templateName = this.dataset.name;
            
            document.getElementById('template-name').textContent = templateName;
            document.getElementById('delete-form').action = `/admin/email-templates/${templateId}`;
            
            new bootstrap.Modal(document.getElementById('deleteModal')).show();
        });
    });

    // Duplicate Template
    document.querySelectorAll('.duplicate-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            if (confirm('هل تريد نسخ هذا القالب؟')) {
                const templateId = this.dataset.id;
                
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/admin/email-templates/${templateId}/duplicate`;
                
                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = '{{ csrf_token() }}';
                
                form.appendChild(csrfInput);
                document.body.appendChild(form);
                form.submit();
            }
        });
    });
});
</script>
@endpush
@endsection

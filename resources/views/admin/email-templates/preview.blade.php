@extends('layouts.admin')

@section('title', 'معاينة القالب: ' . $template->name)

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-eye me-2"></i>
            معاينة القالب: {{ $template->name }}
        </h1>
        <div>
            <a href="{{ route('admin.email-templates.edit', $template) }}" class="btn btn-primary me-2">
                <i class="fas fa-edit me-2"></i>تعديل
            </a>
            <a href="{{ route('admin.email-templates.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-right me-2"></i>رجوع
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Preview -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-envelope me-2"></i>
                        معاينة البريد الإلكتروني
                    </h6>
                </div>
                <div class="card-body p-0">
                    <!-- Email Header -->
                    <div class="bg-light border-bottom p-3">
                        <div class="row g-2">
                            <div class="col-12">
                                <strong>من:</strong> {{ config('mail.from.name') }} &lt;{{ config('mail.from.address') }}&gt;
                            </div>
                            <div class="col-12">
                                <strong>إلى:</strong> customer@example.com
                            </div>
                            <div class="col-12">
                                <strong>الموضوع:</strong> <span class="text-primary">{{ $rendered['subject'] }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Email Body -->
                    <div class="p-4 bg-white" style="min-height: 400px;">
                        <div class="email-preview-content">
                            {!! $rendered['body'] !!}
                        </div>
                    </div>
                </div>
            </div>

            <!-- HTML Source Code -->
            <div class="card shadow mb-4">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-code me-2"></i>
                        كود HTML
                    </h6>
                </div>
                <div class="card-body">
                    <pre class="bg-dark text-light p-3 rounded" style="max-height: 400px; overflow-y: auto;"><code>{{ $rendered['body'] }}</code></pre>
                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="copyToClipboard()">
                        <i class="fas fa-copy me-2"></i>نسخ الكود
                    </button>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Template Info -->
            <div class="card shadow mb-4">
                <div class="card-header bg-info text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-info-circle me-2"></i>
                        معلومات القالب
                    </h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <th width="40%">الاسم:</th>
                            <td>{{ $template->name }}</td>
                        </tr>
                        <tr>
                            <th>المعرف:</th>
                            <td><code>{{ $template->slug }}</code></td>
                        </tr>
                        <tr>
                            <th>النوع:</th>
                            <td>
                                @php
                                    $typeLabels = [
                                        'booking' => ['label' => 'تأكيد حجز', 'color' => 'success'],
                                        'welcome' => ['label' => 'ترحيب', 'color' => 'info'],
                                        'reset_password' => ['label' => 'إعادة كلمة المرور', 'color' => 'warning'],
                                        'invoice' => ['label' => 'فاتورة', 'color' => 'primary'],
                                        'custom' => ['label' => 'مخصص', 'color' => 'secondary'],
                                    ];
                                    $typeData = $typeLabels[$template->type] ?? $typeLabels['custom'];
                                @endphp
                                <span class="badge bg-{{ $typeData['color'] }}">
                                    {{ $typeData['label'] }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>الحالة:</th>
                            <td>
                                @if($template->is_active)
                                    <span class="badge bg-success">مفعل</span>
                                @else
                                    <span class="badge bg-secondary">معطل</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>تاريخ الإنشاء:</th>
                            <td>{{ $template->created_at->format('Y/m/d H:i') }}</td>
                        </tr>
                        <tr>
                            <th>آخر تحديث:</th>
                            <td>{{ $template->updated_at->format('Y/m/d H:i') }}</td>
                        </tr>
                    </table>

                    @if($template->description)
                        <hr>
                        <p class="mb-0 small text-muted">
                            <strong>الوصف:</strong><br>
                            {{ $template->description }}
                        </p>
                    @endif
                </div>
            </div>

            <!-- Send Test Email -->
            <div class="card shadow mb-4">
                <div class="card-header bg-warning text-dark">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-paper-plane me-2"></i>
                        إرسال بريد تجريبي
                    </h6>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form action="{{ route('admin.email-templates.send-test', $template) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="to_email" class="form-label">
                                <i class="fas fa-at me-1"></i>
                                البريد الإلكتروني:
                            </label>
                            <input type="email" 
                                   class="form-control" 
                                   id="to_email" 
                                   name="to_email"
                                   placeholder="test@example.com"
                                   required>
                        </div>
                        <button type="submit" class="btn btn-warning w-100">
                            <i class="fas fa-paper-plane me-2"></i>
                            إرسال الآن
                        </button>
                    </form>

                    <div class="alert alert-info mt-3 mb-0">
                        <small>
                            <i class="fas fa-info-circle me-1"></i>
                            سيتم إرسال البريد بالبيانات التجريبية الموضحة في المعاينة
                        </small>
                    </div>
                </div>
            </div>

            <!-- Variables Used -->
            <div class="card shadow mb-4">
                <div class="card-header bg-secondary text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-code me-2"></i>
                        المتغيرات المستخدمة
                    </h6>
                </div>
                <div class="card-body">
                    @php
                        $variables = $template->getDefaultVariables();
                    @endphp

                    @if(count($variables) > 0)
                        <div class="list-group list-group-flush">
                            @foreach($variables as $key => $label)
                                <div class="list-group-item px-0 py-2 border-0">
                                    <code class="text-primary">@{{ $key }}</code>
                                    <br>
                                    <small class="text-muted">{{ $label }}</small>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted small mb-0">لا توجد متغيرات محددة</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function copyToClipboard() {
    const code = document.querySelector('pre code').textContent;
    navigator.clipboard.writeText(code).then(() => {
        const btn = event.target.closest('button');
        const originalText = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-check me-2"></i>تم النسخ!';
        btn.classList.remove('btn-outline-primary');
        btn.classList.add('btn-success');
        
        setTimeout(() => {
            btn.innerHTML = originalText;
            btn.classList.remove('btn-success');
            btn.classList.add('btn-outline-primary');
        }, 2000);
    });
}
</script>
@endpush

<style>
.email-preview-content {
    font-family: Arial, sans-serif;
    line-height: 1.6;
    color: #333;
}

.email-preview-content img {
    max-width: 100%;
    height: auto;
}

.email-preview-content table {
    width: 100%;
    border-collapse: collapse;
}

pre code {
    font-size: 0.85rem;
    line-height: 1.4;
}
</style>
@endsection

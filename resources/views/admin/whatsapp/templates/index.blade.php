@extends('layouts.admin')

@section('title', 'قوالب رسائل واتساب')

@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
    <div>
        <h1 class="h3 mb-1">قوالب رسائل واتساب</h1>
        <p class="text-muted mb-0">قوالب قابلة لإعادة الاستخدام داخل شاشة المحادثة قبل الإرسال عبر Faalwa.</p>
    </div>
    <a href="{{ route('admin.whatsapp.index') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-right me-1"></i>العودة إلى الدردشة
    </a>
</div>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">إضافة قالب جديد</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.whatsapp.templates.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">اسم القالب</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">النوع</label>
                        <select name="type" class="form-select" required>
                            <option value="utility">Utility</option>
                            <option value="marketing">Marketing</option>
                            <option value="authentication">Authentication</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">محتوى القالب</label>
                        <textarea name="content" class="form-control" rows="6" required>{{ old('content') }}</textarea>
                        <div class="form-text">استخدم @{{1}}، @{{2}}… لإدراج متغيرات. مثال: "مرحباً @{{1}}، رقم الحجز @{{2}}"</div>
                    </div>
                    <div class="mb-3" id="paramsSchemaContainer">
                        <label class="form-label">متغيرات القالب (Labels)</label>
                        <div id="paramsSchemaInputs">
                            <input type="text" name="params_schema[]" class="form-control mb-2" placeholder="المتغير 1 (مثال: اسم العميل)">
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="addParamInput()">+ إضافة متغير</button>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">حفظ القالب</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">القوالب الحالية</h5>
                <div class="d-flex gap-2">
                    <form action="{{ route('admin.whatsapp.templates.sync') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-success btn-sm">
                            <i class="fas fa-sync-alt me-1"></i> جلب القوالب من Faalwa
                        </button>
                    </form>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>الاسم</th>
                                <th>النوع</th>
                                <th>المتغيرات</th>
                                <th>المحتوى</th>
                                <th class="text-center">إجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($templates as $template)
                                <tr>
                                    <td>{{ $template->name }}</td>
                                    <td><span class="badge bg-info">{{ $template->type }}</span></td>
                                    <td>
                                        @if(is_array($template->params_schema) && count($template->params_schema))
                                            <span class="badge bg-secondary">{{ count($template->params_schema) }} متغير</span>
                                            <div class="small text-muted mt-1">{{ implode(', ', $template->params_schema) }}</div>
                                        @else
                                            <span class="badge bg-light text-dark">بدون متغيرات</span>
                                        @endif
                                    </td>
                                    <td class="small text-muted">{{ \Illuminate\Support\Str::limit($template->content, 80) }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('admin.whatsapp.templates.edit', $template) }}" class="btn btn-sm btn-outline-primary">تعديل</a>
                                        <form action="{{ route('admin.whatsapp.templates.destroy', $template) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('حذف هذا القالب؟')">حذف</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">لا توجد قوالب بعد.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{ $templates->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
let paramCount = 1;
function addParamInput() {
    paramCount++;
    const container = document.getElementById('paramsSchemaInputs');
    const div = document.createElement('div');
    div.className = 'mb-2 d-flex gap-2';
    div.innerHTML = `<input type="text" name="params_schema[]" class="form-control" placeholder="المتغير ${paramCount}"><button type="button" class="btn btn-sm btn-outline-danger" onclick="this.parentElement.remove()">×</button>`;
    container.appendChild(div);
}

// Auto-detect @{{N}} placeholders in content and suggest param count
document.querySelector('textarea[name="content"]')?.addEventListener('blur', function() {
    const text = this.value;
    const matches = text.match(/@{{\s*(\d+)\s*}}/g);
    if (matches) {
        const indices = matches.map(m => parseInt(m.replace(/[{}]/g, '').trim())).sort((a,b) => a-b);
        const maxIndex = indices[indices.length - 1];
        const container = document.getElementById('paramsSchemaInputs');
        const existing = container.querySelectorAll('input[name="params_schema[]"]').length;
        if (existing < maxIndex) {
            for (let i = existing + 1; i <= maxIndex; i++) {
                const div = document.createElement('div');
                div.className = 'mb-2 d-flex gap-2';
                div.innerHTML = `<input type="text" name="params_schema[]" class="form-control" placeholder="المتغير ${i}"><button type="button" class="btn btn-sm btn-outline-danger" onclick="this.parentElement.remove()">×</button>`;
                container.appendChild(div);
            }
            paramCount = maxIndex;
        }
    }
});
</script>
@endsection
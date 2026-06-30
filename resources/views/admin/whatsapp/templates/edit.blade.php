@extends('layouts.admin')

@section('title', 'تعديل قالب واتساب')

@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
    <div>
        <h1 class="h3 mb-1">تعديل قالب واتساب</h1>
        <p class="text-muted mb-0">حدّث القالب وسيظهر مباشرة داخل شاشة المحادثة.</p>
    </div>
    <a href="{{ route('admin.whatsapp.templates.index') }}" class="btn btn-outline-secondary">العودة للقوالب</a>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('admin.whatsapp.templates.update', $template) }}">
            @csrf
            @method('PUT')
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">اسم القالب</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $template->name) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">النوع</label>
                    <select name="type" class="form-select" required>
                        @foreach(['utility', 'marketing', 'authentication'] as $type)
                            <option value="{{ $type }}" {{ old('type', $template->type) === $type ? 'selected' : '' }}>{{ ucfirst($type) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label">المحتوى</label>
                    <textarea name="content" class="form-control" rows="8" required>{{ old('content', $template->content) }}</textarea>
                    <div class="form-text">استخدم @{{1}}، @{{2}}… لإدراج متغيرات. مثال: "مرحباً @{{1}}، رقم الحجز @{{2}}"</div>
                </div>
                <div class="col-12">
                    <label class="form-label">متغيرات القالب (Labels)</label>
                    <div id="paramsSchemaInputs">
                        @php
                            $params = old('params_schema', $template->params_schema ?? []);
                        @endphp
                        @forelse($params as $i => $param)
                            <div class="mb-2 d-flex gap-2">
                                <input type="text" name="params_schema[]" class="form-control" value="{{ $param }}" placeholder="المتغير {{ $i + 1 }}">
                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="this.parentElement.remove()">×</button>
                            </div>
                        @empty
                            <div class="mb-2 d-flex gap-2">
                                <input type="text" name="params_schema[]" class="form-control" placeholder="المتغير 1">
                            </div>
                        @endforelse
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="addParamInput()">+ إضافة متغير</button>
                </div>
            </div>

            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary">حفظ التغييرات</button>
                <a href="{{ route('admin.whatsapp.templates.index') }}" class="btn btn-secondary">إلغاء</a>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
let paramCount = {{ count($params ?? []) }};
if (paramCount === 0) paramCount = 1;

function addParamInput() {
    paramCount++;
    const container = document.getElementById('paramsSchemaInputs');
    const div = document.createElement('div');
    div.className = 'mb-2 d-flex gap-2';
    div.innerHTML = `<input type="text" name="params_schema[]" class="form-control" placeholder="المتغير ${paramCount}"><button type="button" class="btn btn-sm btn-outline-danger" onclick="this.parentElement.remove()">×</button>`;
    container.appendChild(div);
}
</script>
@endsection
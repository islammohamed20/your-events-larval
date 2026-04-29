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
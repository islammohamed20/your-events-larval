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
                <form action="{{ route('admin.whatsapp.templates.sync') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-success btn-sm">
                        <i class="fas fa-sync-alt me-1"></i> جلب القوالب من Faalwa
                    </button>
                </form>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>الاسم</th>
                                <th>النوع</th>
                                <th>المحتوى</th>
                                <th class="text-center">إجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($templates as $template)
                                <tr>
                                    <td>{{ $template->name }}</td>
                                    <td><span class="badge bg-info">{{ $template->type }}</span></td>
                                    <td class="small text-muted">{{ \Illuminate\Support\Str::limit($template->content, 100) }}</td>
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
                                    <td colspan="4" class="text-center text-muted py-4">لا توجد قوالب بعد.</td>
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
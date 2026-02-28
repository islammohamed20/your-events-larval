@extends('layouts.admin')

@section('title', 'إدارة البصمات')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-1">إدارة البصمات</h3>
            <div class="text-muted small">{{ $user->name }} — {{ $user->email }}</div>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.user-management.edit', $user) }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> رجوع
            </a>
            <form action="{{ route('admin.user-management.passkeys.destroy-all', $user) }}" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger" {{ $passkeys->isEmpty() ? 'disabled' : '' }} onclick="return confirm('تأكيد حذف جميع البصمات لهذا المستخدم؟')">
                    <i class="fas fa-trash"></i> حذف الكل
                </button>
            </form>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-fingerprint me-2"></i>الأجهزة المسجلة</h5>
            <span class="badge bg-secondary">{{ $passkeys->count() }}</span>
        </div>
        <div class="card-body p-0">
            @if($passkeys->isEmpty())
                <div class="p-4 text-center text-muted">لا توجد بصمات مسجلة لهذا المستخدم</div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>الاسم</th>
                                <th>آخر استخدام</th>
                                <th>تاريخ الإضافة</th>
                                <th class="text-end">إجراء</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($passkeys as $passkey)
                                <tr>
                                    <td class="fw-semibold">{{ $passkey->device_name ?: 'الجهاز' }}</td>
                                    <td>{{ $passkey->last_used_at ? $passkey->last_used_at->format('Y-m-d H:i') : '—' }}</td>
                                    <td>{{ $passkey->created_at ? $passkey->created_at->format('Y-m-d H:i') : '—' }}</td>
                                    <td class="text-end">
                                        <form action="{{ route('admin.user-management.passkeys.destroy', [$user, $passkey]) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('حذف هذه البصمة؟')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

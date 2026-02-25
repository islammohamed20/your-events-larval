@extends('layouts.admin')

@section('title', 'رسائل التواصل')
@section('page-title', 'رسائل التواصل')
@section('page-description', 'عرض وإدارة الرسائل الواردة من صفحة اتصل بنا')

@section('content')
<div class="container-fluid" id="adminContactMessagesAutoRefresh">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h4 mb-0"><i class="fas fa-envelope me-2"></i>رسائل التواصل</h1>
        <form method="GET" class="d-flex gap-2">
            <select name="status" class="form-select" style="width: 200px">
                @php $status = request('status', ''); @endphp
                <option value="" {{ $status==='' ? 'selected' : '' }}>كل الحالات</option>
                <option value="new" {{ $status==='new' ? 'selected' : '' }}>جديدة</option>
                <option value="in_progress" {{ $status==='in_progress' ? 'selected' : '' }}>قيد المتابعة</option>
                <option value="closed" {{ $status==='closed' ? 'selected' : '' }}>مغلقة</option>
            </select>
            <button class="btn btn-primary" type="submit"><i class="fas fa-filter me-2"></i>تصفية</button>
        </form>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">قائمة الرسائل</h5>
                <span class="badge bg-primary">{{ $messages->total() }} رسالة</span>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>الاسم</th>
                            <th>البريد</th>
                            <th>الهاتف</th>
                            <th>الموضوع</th>
                            <th>الحالة</th>
                            <th>تاريخ الإرسال</th>
                            <th>إجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($messages as $msg)
                            <tr>
                                <td>{{ $msg->id }}</td>
                                <td>{{ $msg->name }}</td>
                                <td><a href="mailto:{{ $msg->email }}">{{ $msg->email }}</a></td>
                                <td dir="ltr">{{ $msg->phone ?? '-' }}</td>
                                <td>
                                    @switch($msg->subject)
                                        @case('booking') استفسار عن الحجز @break
                                        @case('packages') استفسار عن الباقات @break
                                        @case('services') استفسار عن الخدمات @break
                                        @case('complaint') شكوى @break
                                        @default أخرى
                                    @endswitch
                                </td>
                                <td>
                                    @if($msg->status === 'new')
                                        <span class="badge bg-warning text-dark">جديدة</span>
                                    @elseif($msg->status === 'in_progress')
                                        <span class="badge bg-info text-dark">قيد المتابعة</span>
                                    @else
                                        <span class="badge bg-success">مغلقة</span>
                                    @endif
                                </td>
                                <td>{{ \Carbon\Carbon::parse($msg->created_at)->format('Y-m-d H:i') }}</td>
                                <td>
                                    <a href="{{ route('admin.contact-messages.show', $msg->id) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <form action="{{ route('admin.contact-messages.destroy', $msg->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('تأكيد حذف الرسالة؟')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">لا توجد رسائل حتى الآن</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            {{ $messages->links() }}
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var container = document.getElementById('adminContactMessagesAutoRefresh');
    if (!container) return;

    function refreshMessages() {
        if (document.visibilityState !== 'visible') return;

        fetch(window.location.href, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            cache: 'no-store'
        })
        .then(response => response.text())
        .then(html => {
            var parser = new DOMParser();
            var doc = parser.parseFromString(html, 'text/html');
            var newContainer = doc.getElementById('adminContactMessagesAutoRefresh');
            if (newContainer) {
                container.innerHTML = newContainer.innerHTML;
            }
        })
        .catch(() => {});
    }

    setInterval(refreshMessages, 10000); // كل 10 ثواني
});
</script>
@endpush


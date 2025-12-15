@extends('layouts.admin')

@section('title', 'عروض الأسعار')
@section('page-title', 'عروض الأسعار')
@section('page-description', 'عرض وإدارة جميع عروض الأسعار')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h4 mb-0"><i class="fas fa-file-invoice-dollar me-2"></i>عروض الأسعار</h1>
        <form method="GET" class="d-flex gap-2">
            <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="رقم العرض أو اسم/بريد العميل" style="min-width: 280px;">
            <select name="status" class="form-select" style="min-width: 180px;">
                @php $status = request('status', 'all'); @endphp
                <option value="all" {{ $status==='all' ? 'selected' : '' }}>كل الحالات</option>
                <option value="pending" {{ $status==='pending' ? 'selected' : '' }}>قيد الانتظار</option>
                <option value="under_review" {{ $status==='under_review' ? 'selected' : '' }}>قيد المراجعة</option>
                <option value="approved" {{ $status==='approved' ? 'selected' : '' }}>موافق عليه</option>
                <option value="rejected" {{ $status==='rejected' ? 'selected' : '' }}>مرفوض</option>
                <option value="completed" {{ $status==='completed' ? 'selected' : '' }}>مكتمل</option>
                <option value="paid" {{ $status==='paid' ? 'selected' : '' }}>تم الدفع</option>
            </select>
            <button class="btn btn-primary" type="submit"><i class="fas fa-search me-1"></i>بحث</button>
        </form>
    </div>

    @if(isset($stats))
    <div class="row g-3 mb-3">
        <div class="col-md-2">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="small">الإجمالي</div>
                        <div class="h4 mb-0">{{ $stats['total'] }}</div>
                    </div>
                    <i class="fas fa-list"></i>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="small text-muted">قيد الانتظار</div>
                    <div class="h5 mb-0">{{ $stats['pending'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="small text-muted">قيد المراجعة</div>
                    <div class="h5 mb-0">{{ $stats['under_review'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="small text-muted">موافق عليه</div>
                    <div class="h5 mb-0">{{ $stats['approved'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="small text-muted">مرفوض</div>
                    <div class="h5 mb-0">{{ $stats['rejected'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="small text-muted">تم الدفع</div>
                    <div class="h5 mb-0">{{ $stats['paid'] }}</div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">قائمة العروض</h5>
            <div class="text-muted small">
                يظهر أحدث العروض أولاً
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>رقم العرض</th>
                            <th>العميل</th>
                            <th>الحالة</th>
                            <th class="text-end">الإجمالي</th>
                            <th>تاريخ الإنشاء</th>
                            <th>إجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($quotes as $quote)
                            <tr>
                                <td>{{ $quote->id }}</td>
                                <td>{{ $quote->quote_number }}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="fas fa-user text-primary"></i>
                                        <div>
                                            <div class="fw-bold">{{ optional($quote->user)->name ?? '-' }}</div>
                                            <div class="text-muted small">{{ optional($quote->user)->email ?? '-' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>{!! $quote->status_badge !!}</td>
                                <td class="text-end">{{ number_format($quote->total, 2) }}</td>
                                <td>{{ optional($quote->created_at)->format('Y-m-d H:i') }}</td>
                                <td class="d-flex gap-2">
                                    <a href="{{ route('admin.quotes.show', $quote) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <form action="{{ route('admin.quotes.destroy', $quote) }}" method="POST" onsubmit="return confirm('تأكيد حذف العرض؟')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    <i class="fas fa-file-invoice fa-2x mb-2"></i>
                                    <div>لا توجد عروض أسعار</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            {{ $quotes->withQueryString()->links() }}
        </div>
    </div>
</div>
@endsection


@extends('layouts.admin')

@section('title', 'عروض الأسعار')
@section('page-title', 'عروض الأسعار')
@section('page-description', 'عرض وإدارة جميع عروض الأسعار')

@section('styles')
<style>
@media (min-width: 992px) { .mobile-only { display: none !important; } }
@media (max-width: 991.98px) {
    .desktop-only { display: none !important; }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 8px;
        margin-bottom: 1rem;
    }
    .stat-mini {
        background: #fff;
        border-radius: 12px;
        border: 1px solid #eef1f6;
        padding: .6rem .75rem;
        text-align: center;
        box-shadow: 0 2px 6px rgba(0,0,0,.04);
    }
    .stat-mini-label { font-size: .65rem; color: #9ca3af; font-weight: 600; text-transform: uppercase; }
    .stat-mini-val   { font-size: 1.2rem; font-weight: 800; color: #1f144a; }

    /* search bar */
    .search-wrap { display: flex; flex-direction: column; gap: 8px; margin-bottom: 1rem; }

    .quote-card {
        background: #fff;
        border-radius: 16px;
        border: 1px solid #eef1f6;
        padding: 1rem;
        margin-bottom: 1rem;
        box-shadow: 0 4px 12px rgba(0,0,0,.04);
    }
    .quote-card-top { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: .6rem; }
    .quote-num  { font-size: .85rem; font-weight: 800; color: #1f144a; }
    .quote-client { font-size: .8rem; color: #374151; font-weight: 600; }
    .quote-email  { font-size: .72rem; color: #9ca3af; }
    .quote-meta { display: flex; gap: 6px; flex-wrap: wrap; margin: .5rem 0; }
    .q-chip {
        font-size: .72rem; padding: 3px 9px; border-radius: 8px; font-weight: 600;
        display: inline-flex; align-items: center; gap: 4px;
    }
    .chip-amount { background: #f0fdf4; color: #166534; }
    .chip-date   { background: #f9fafb; color: #374151; }

    .quote-actions { display: flex; gap: 8px; padding-top: .75rem; border-top: 1px solid #f3f4f6; }
    .q-btn {
        flex: 1; padding: 8px; border-radius: 10px; font-size: .8rem; font-weight: 600;
        text-align: center; text-decoration: none; border: none; cursor: pointer;
    }
    .q-btn-view { background: #eff6ff; color: #1d4ed8; }
    .q-btn-del  { background: #fff1f2; color: #be123c; }
}
</style>
@endsection

@section('content')
<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h4 mb-0 fw-bold" style="color:#1f144a;">
            <i class="fas fa-file-invoice-dollar me-2 opacity-75"></i>عروض الأسعار
        </h1>
        {{-- Desktop: inline form --}}
        <form method="GET" class="d-none d-lg-flex gap-2">
            <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="رقم العرض أو اسم/بريد العميل" style="min-width:250px;">
            <select name="status" class="form-select" style="min-width:150px;">
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

    {{-- Mobile search --}}
    <form method="GET" class="search-wrap mobile-only">
        <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="ابحث عن عرض أو عميل...">
        <div class="d-flex gap-2">
            <select name="status" class="form-select form-select-sm">
                @php $status = request('status', 'all'); @endphp
                <option value="all" {{ $status==='all' ? 'selected' : '' }}>كل الحالات</option>
                <option value="pending" {{ $status==='pending' ? 'selected' : '' }}>انتظار</option>
                <option value="under_review" {{ $status==='under_review' ? 'selected' : '' }}>مراجعة</option>
                <option value="approved" {{ $status==='approved' ? 'selected' : '' }}>موافق</option>
                <option value="rejected" {{ $status==='rejected' ? 'selected' : '' }}>مرفوض</option>
                <option value="completed" {{ $status==='completed' ? 'selected' : '' }}>مكتمل</option>
                <option value="paid" {{ $status==='paid' ? 'selected' : '' }}>مدفوع</option>
            </select>
            <button class="btn btn-primary btn-sm px-3" type="submit"><i class="fas fa-search"></i></button>
        </div>
    </form>

    {{-- Stats --}}
    @if(isset($stats))
        {{-- Desktop stats --}}
        <div class="row g-3 mb-3 desktop-only">
            <div class="col-md-2">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="small text-muted">الإجمالي</div>
                        <div class="h4 mb-0">{{ $stats['total'] }}</div>
                    </div>
                </div>
            </div>
            @foreach(['pending' => 'قيد الانتظار', 'under_review' => 'قيد المراجعة', 'approved' => 'موافق عليه', 'rejected' => 'مرفوض', 'paid' => 'تم الدفع'] as $key => $label)
            <div class="col-md-2">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="small text-muted">{{ $label }}</div>
                        <div class="h5 mb-0">{{ $stats[$key] }}</div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Mobile mini stats --}}
        <div class="stats-grid mobile-only">
            <div class="stat-mini"><div class="stat-mini-label">الكل</div><div class="stat-mini-val">{{ $stats['total'] }}</div></div>
            <div class="stat-mini"><div class="stat-mini-label">انتظار</div><div class="stat-mini-val">{{ $stats['pending'] }}</div></div>
            <div class="stat-mini"><div class="stat-mini-label">موافق</div><div class="stat-mini-val">{{ $stats['approved'] }}</div></div>
            <div class="stat-mini"><div class="stat-mini-label">مراجعة</div><div class="stat-mini-val">{{ $stats['under_review'] }}</div></div>
            <div class="stat-mini"><div class="stat-mini-label">مرفوض</div><div class="stat-mini-val">{{ $stats['rejected'] }}</div></div>
            <div class="stat-mini"><div class="stat-mini-label">مدفوع</div><div class="stat-mini-val">{{ $stats['paid'] }}</div></div>
        </div>
    @endif

    <div id="adminQuotesAutoRefresh">

        {{-- 🖥️ Desktop Table --}}
        <div class="card border-0 shadow-sm desktop-only">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold" style="color:#1f144a;">قائمة العروض</h5>
                <div class="text-muted small">يظهر أحدث العروض أولاً</div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>#</th><th>رقم العرض</th><th>العميل</th>
                                <th>الحالة</th><th class="text-end">الإجمالي</th>
                                <th>تاريخ الإنشاء</th><th>إجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($quotes as $quote)
                                <tr>
                                    <td>{{ $quote->id }}</td>
                                    <td><strong>{{ $quote->quote_number }}</strong></td>
                                    <td>
                                        <div class="fw-bold">{{ optional($quote->user)->name ?? '-' }}</div>
                                        <div class="text-muted small">{{ optional($quote->user)->email ?? '-' }}</div>
                                    </td>
                                    <td>{!! $quote->status_badge !!}</td>
                                    <td class="text-end">{{ number_format($quote->total, 2) }}</td>
                                    <td>{{ optional($quote->created_at)->format('Y-m-d H:i') }}</td>
                                    <td class="d-flex gap-2">
                                        <a href="{{ route('admin.quotes.show', $quote) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <form action="{{ route('admin.quotes.destroy', $quote) }}" method="POST" onsubmit="return confirm('تأكيد حذف العرض؟')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-5">
                                        <i class="fas fa-file-invoice fa-2x mb-2 opacity-25 d-block"></i>لا توجد عروض أسعار
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-white">{{ $quotes->withQueryString()->links() }}</div>
        </div>

        {{-- 📱 Mobile Cards --}}
        <div class="mobile-only">
            @forelse($quotes as $quote)
                <div class="quote-card">
                    <div class="quote-card-top">
                        <div>
                            <div class="quote-num">{{ $quote->quote_number }}</div>
                            <div class="quote-client">{{ optional($quote->user)->name ?? '—' }}</div>
                            <div class="quote-email">{{ optional($quote->user)->email ?? '—' }}</div>
                        </div>
                        <div>{!! $quote->status_badge !!}</div>
                    </div>
                    <div class="quote-meta">
                        <span class="q-chip chip-amount"><i class="fas fa-money-bill"></i>{{ number_format($quote->total, 2) }} ر.س</span>
                        <span class="q-chip chip-date"><i class="fas fa-clock"></i>{{ optional($quote->created_at)->format('Y-m-d') }}</span>
                    </div>
                    <div class="quote-actions">
                        <a href="{{ route('admin.quotes.show', $quote) }}" class="q-btn q-btn-view">
                            <i class="fas fa-eye me-1"></i>عرض التفاصيل
                        </a>
                        <form action="{{ route('admin.quotes.destroy', $quote) }}" method="POST" style="flex:1;display:flex;" onsubmit="return confirm('حذف العرض؟')">
                            @csrf @method('DELETE')
                            <button type="submit" class="q-btn q-btn-del w-100">
                                <i class="fas fa-trash me-1"></i>حذف
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="text-center py-5">
                    <i class="fas fa-file-invoice fa-3x text-muted mb-3 opacity-25"></i>
                    <p class="text-muted fw-bold">لا توجد عروض أسعار</p>
                </div>
            @endforelse
            <div class="mt-3">{{ $quotes->withQueryString()->links() }}</div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var container = document.getElementById('adminQuotesAutoRefresh');
    if (!container) return;
    function refreshAdminQuotes() {
        if (document.visibilityState !== 'visible') return;
        fetch(window.location.href, { headers: { 'X-Requested-With': 'XMLHttpRequest' }, cache: 'no-store' })
            .then(r => r.text())
            .then(html => {
                var doc = new DOMParser().parseFromString(html, 'text/html');
                var newC = doc.getElementById('adminQuotesAutoRefresh');
                if (newC) container.innerHTML = newC.innerHTML;
            }).catch(() => {});
    }
    setInterval(refreshAdminQuotes, 5000);
});
</script>
@endpush

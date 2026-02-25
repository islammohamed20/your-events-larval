@extends('layouts.app')

@section('title', 'طلبات الأسعار') 'عروض الأسعار - Your Events')

@section('content')
<div class="container py-5" style="margin-top: 100px;">
    <div class="row">
        <div class="col-12" id="customerQuotesAutoRefresh">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>
                    <i class="fas fa-file-invoice-dollar me-2 text-primary"></i>
                    عروض الأسعار
                </h2>
                <a href="{{ route('services.index') }}" class="btn btn-outline-primary">
                    <i class="fas fa-plus me-2"></i>طلب عرض سعر جديد
                </a>
            </div>
            
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            
            @if($quotes->isEmpty())
                <div class="card shadow-lg border-0 rounded-4">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-file-invoice-dollar text-muted mb-4" style="font-size: 80px;"></i>
                        <h4 class="text-muted mb-3">لا توجد عروض أسعار</h4>
                        <p class="text-muted mb-4">ابدأ بإضافة خدمات إلى سلة التسوق وأنشئ عرض سعرك الأول</p>
                        <a href="{{ route('services.index') }}" class="btn btn-primary btn-lg">
                            <i class="fas fa-cogs me-2"></i>تصفح الخدمات
                        </a>
                    </div>
                </div>
            @else
                <div class="row">
                    @foreach($quotes as $quote)
                    <div class="col-md-12 mb-4">
                        <div class="card shadow-lg border-0 rounded-4 quote-card">
                            <div class="card-body p-4">
                                <div class="row align-items-center">
                                    <div class="col-md-2 text-center mb-3 mb-md-0">
                                        <div class="quote-icon">
                                            <i class="fas fa-file-invoice fa-3x text-primary"></i>
                                        </div>
                                        <small class="text-muted d-block mt-2">{{ $quote->quote_number }}</small>
                                    </div>
                                    <div class="col-md-3 mb-3 mb-md-0">
                                        <h5 class="mb-1">عرض سعر #{{ $quote->id }}</h5>
                                        <p class="text-muted small mb-1">
                                            <i class="fas fa-calendar me-1"></i>
                                            {{ $quote->created_at->format('Y/m/d') }}
                                        </p>
                                        <p class="text-muted small mb-0">
                                            <i class="fas fa-box me-1"></i>
                                            {{ $quote->items->count() }} خدمة
                                        </p>
                                    </div>
                                    <div class="col-md-2 text-center mb-3 mb-md-0">
                                        {!! $quote->status_badge !!}
                                    </div>
                                    <div class="col-md-3 text-center mb-3 mb-md-0">
                                        <h4 class="text-primary mb-0">{{ number_format($quote->total, 2) }} {{ __('common.currency') }}</h4>
                                        <small class="text-muted">شامل الضريبة</small>
                                    </div>
                                    <div class="col-md-2 text-center">
                                        <a href="{{ url('https://yourevents.sa' . route('quotes.show', $quote, false)) }}" class="btn btn-primary btn-sm w-100 mb-2">
                                            <i class="fas fa-eye me-1"></i>عرض
                                        </a>
                                        @if($quote->status === 'approved' && $quote->payment_status !== 'paid')
                                            <a href="{{ url('https://yourevents.sa' . route('quotes.complete-booking', $quote, false)) }}" class="btn btn-success btn-sm w-100 mb-2">
                                                <i class="fas fa-credit-card me-1"></i>استكمال بيانات الحجز والدفع
                                            </a>
                                        @elseif($quote->payment_status === 'paid' || $quote->status === 'paid')
                                            <button type="button" class="btn btn-outline-success btn-sm w-100 mb-2" disabled>
                                                <i class="fas fa-check-circle me-1"></i>تم الدفع
                                            </button>
                                        @else
                                            <button type="button" class="btn btn-outline-success btn-sm w-100 mb-2" disabled>
                                                <i class="fas fa-clock me-1"></i>استكمال بيانات الحجز والدفع
                                            </button>
                                        @endif
                                        <a href="{{ route('quotes.download', $quote) }}" class="btn btn-outline-secondary btn-sm w-100">
                                            <i class="fas fa-download me-1"></i>PDF
                                        </a>
                                    </div>
                                </div>
                                
                                @if($quote->customer_notes)
                                <div class="mt-3 pt-3 border-top">
                                    <small class="text-muted">
                                        <i class="fas fa-sticky-note me-1"></i>
                                        <strong>ملاحظاتك:</strong> {{ Str::limit($quote->customer_notes, 100) }}
                                    </small>
                                </div>
                                @endif
                                
                                @if($quote->admin_notes && $quote->status !== 'pending')
                                <div class="mt-2">
                                    <small class="text-info">
                                        <i class="fas fa-comment-dots me-1"></i>
                                        <strong>رد الإدارة:</strong> {{ Str::limit($quote->admin_notes, 100) }}
                                    </small>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                
                <div class="d-flex justify-content-center mt-4">
                    {{ $quotes->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<style>
.quote-card {
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

.quote-card:hover {
    border-color: #A855F7;
    transform: translateY(-5px);
    box-shadow: 0 15px 40px rgba(168, 85, 247, 0.3) !important;
}

.quote-icon {
    background: linear-gradient(135deg, #f3e8ff 0%, #e9d5ff 100%);
    padding: 20px;
    border-radius: 15px;
    display: inline-block;
}

.btn-primary {
    background: linear-gradient(135deg, #5B21B6 0%, #7C3AED 50%, #A855F7 100%);
    border: none;
}

.btn-primary:hover {
    transform: scale(1.05);
    box-shadow: 0 5px 15px rgba(168, 85, 247, 0.4);
}
</style>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var container = document.getElementById('customerQuotesAutoRefresh');
    if (!container) {
        return;
    }

    function refreshCustomerQuotes() {
        if (document.visibilityState !== 'visible') {
            return;
        }

        fetch(window.location.href, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            cache: 'no-store'
        })
            .then(function(response) {
                return response.text();
            })
            .then(function(html) {
                var parser = new DOMParser();
                var doc = parser.parseFromString(html, 'text/html');
                var newContainer = doc.getElementById('customerQuotesAutoRefresh');
                if (newContainer) {
                    container.innerHTML = newContainer.innerHTML;
                }
            })
            .catch(function() {});
    }

    setInterval(refreshCustomerQuotes, 5000);
});
</script>
@endpush

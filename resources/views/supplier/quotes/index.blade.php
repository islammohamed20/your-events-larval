@extends('supplier.layouts.app')

@section('title', __('common.quotes'))
@section('page-title', __('common.quotes'))

@push('styles')
<style>
    @keyframes pulse {
        0%, 100% { transform: scale(1); opacity: 1; }
        50% { transform: scale(1.05); opacity: 0.9; }
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="content-card">
                <div class="card-header-custom">
                    <h5><i class="fas fa-file-invoice-dollar me-2"></i>{{ __('common.quotes_related_to_my_services') }}</h5>
                    <form method="GET" class="d-flex align-items-center gap-2">
                        <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="{{ __('common.search_quote_placeholder') }}" style="max-width: 280px;">
                        <select name="status" class="form-select" style="max-width: 200px;">
                            @php $status = request('status', 'all'); @endphp
                            <option value="all" {{ $status==='all' ? 'selected' : '' }}>{{ __('common.all') }}</option>
                            <option value="pending" {{ $status==='pending' ? 'selected' : '' }}>{{ __('common.quote_status_pending') }}</option>
                            <option value="approved" {{ $status==='approved' ? 'selected' : '' }}>{{ __('common.quote_status_approved') }}</option>
                            <option value="rejected" {{ $status==='rejected' ? 'selected' : '' }}>{{ __('common.quote_status_rejected') }}</option>
                            <option value="completed" {{ $status==='completed' ? 'selected' : '' }}>{{ __('common.quote_status_completed') }}</option>
                        </select>
                        <button class="btn btn-supplier-primary" type="submit"><i class="fas fa-search me-1"></i>{{ __('common.search') }}</button>
                        <a href="{{ route('supplier.quotes.index') }}" class="btn btn-outline-secondary"><i class="fas fa-rotate-left me-1"></i>{{ __('common.reset') }}</a>
                    </form>
                </div>

                <div class="p-3">
                    <div class="table-responsive">
                        <table class="table table-custom">
                            <thead>
                                <tr>
                                    <th>{{ __('common.quote_number') }}</th>
                                    <th>{{ __('common.customer') }}</th>
                                    <th>{{ __('common.items_count') }}</th>
                                    <th>{{ __('common.total_for_my_items') }}</th>
                                    <th>{{ __('common.status') }}</th>
                                    <th>{{ __('common.date') }}</th>
                                    <th>{{ __('common.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($quotes as $quote)
                                    @php
                                        $items = $quote->items->whereIn('service_id', $supplierServiceIds);
                                        $supplierTotal = $items->sum('subtotal');
                                    @endphp
                                    <tr>
                                        <td>
                                            <strong>{{ $quote->quote_number ?? ('Q-' . $quote->id) }}</strong>
                                            @if($quote->status === 'approved' && !$quote->accepted_by_supplier_id)
                                                <span class="badge bg-danger ms-2" style="animation: pulse 2s infinite;">
                                                    {{ __('common.quote_available_to_accept') }}
                                                </span>
                                            @elseif($quote->accepted_by_supplier_id && $quote->accepted_by_supplier_id === auth()->guard('supplier')->id())
                                                <span class="badge bg-success ms-2">{{ __('common.quote_accepted_by_me') }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <i class="fas fa-user-circle text-secondary"></i>
                                                <div>
                                                    <div class="fw-bold">{{ $quote->user->name ?? __('common.customer') }}</div>
                                                    <div class="text-muted" style="font-size: 0.85rem;">{{ $quote->user->email ?? '-' }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ $items->count() }}</td>
                                        <td>{{ number_format($supplierTotal, 2) }} {{ __('common.currency') }}</td>
                                        <td>
                                            @php $statusClass = match($quote->status){
                                                'pending' => 'status-badge status-pending',
                                                'approved' => 'status-badge status-confirmed',
                                                'rejected' => 'status-badge status-cancelled',
                                                'completed' => 'status-badge status-completed',
                                                default => 'status-badge status-pending'
                                            }; @endphp
                                            @php
                                                $statusText = match($quote->status){
                                                    'pending' => __('common.quote_status_pending'),
                                                    'approved' => __('common.quote_status_approved'),
                                                    'rejected' => __('common.quote_status_rejected'),
                                                    'completed' => __('common.quote_status_completed'),
                                                    default => __('common.unknown'),
                                                };
                                            @endphp
                                            <span class="{{ $statusClass }}">{{ $statusText }}</span>
                                        </td>
                                        <td>{{ optional($quote->created_at)->format('Y-m-d H:i') }}</td>
                                        <td>
                                            <a href="{{ route('supplier.quotes.show', $quote) }}" class="btn btn-sm btn-supplier-gold">
                                                <i class="fas fa-eye me-1"></i>{{ __('common.view') }}
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">
                                            <i class="fas fa-file-invoice fa-2x mb-2"></i>
                                            <div>{{ __('common.no_quotes_for_services') }}</div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="p-3">
                    {{ $quotes->withQueryString()->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

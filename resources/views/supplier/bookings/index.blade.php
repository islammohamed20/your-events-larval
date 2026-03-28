@extends('supplier.layouts.app')

@section('title', __('common.bookings'))
@section('page-title', __('common.bookings'))

@push('styles')
<style>
    .nav-tabs .nav-link {
        color: #000 !important;
    }
    .nav-tabs .nav-link.active,
    .nav-tabs .nav-item.show .nav-link {
        color: #000 !important;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Navigation Tabs -->
    <ul class="nav nav-tabs mb-4" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="available-tab" data-bs-toggle="tab" href="#available" role="tab">
                <i class="fas fa-clock me-2"></i>{{ __('common.available_for_acceptance') }}
                @if($availableBookings->count() > 0)
                    <span class="badge bg-warning ms-2">{{ $availableBookings->count() }}</span>
                @endif
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="accepted-tab" data-bs-toggle="tab" href="#accepted" role="tab">
                <i class="fas fa-check-circle me-2"></i>{{ __('common.accepted') }}
                @if($acceptedBookings->count() > 0)
                    <span class="badge bg-success ms-2">{{ $acceptedBookings->count() }}</span>
                @endif
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="rejected-tab" data-bs-toggle="tab" href="#rejected" role="tab">
                <i class="fas fa-times-circle me-2"></i>{{ __('common.rejected_or_expired') }}
            </a>
        </li>
    </ul>

    <!-- Tab Content -->
    <div class="tab-content">
        <!-- Available Bookings -->
        <div class="tab-pane fade show active" id="available" role="tabpanel">
            @if($availableBookings->count() > 0)
                <div class="row">
                    @foreach($availableBookings as $booking)
                        <div class="col-lg-6 mb-4">
                            <div class="card border-warning shadow-sm">
                                <div class="card-header bg-warning text-dark d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0">
                                        <i class="fas fa-calendar-alt me-2"></i>
                                        {{ $booking->booking_reference }}
                                    </h6>
                                    <span class="badge bg-light text-dark">
                                        <i class="fas fa-clock me-1"></i>
                                        {{ __('common.ends_in') }}: {{ $booking->expires_at->diffForHumans() }}
                                    </span>
                                </div>
                                <div class="card-body">
                                    <!-- Customer Info (hidden per request) -->

                                    <!-- Services List -->
                                    @if($booking->quote && $booking->quote->items->count() > 0)
                                        <div class="mb-3">
                                            <h6 class="text-muted mb-2"><i class="fas fa-concierge-bell me-2"></i>{{ __('common.requested_services') }}</h6>
                                            <ul class="list-unstyled">
                                                @foreach($booking->quote->items as $item)
                                                    <li class="mb-1">
                                                        <i class="fas fa-check-circle text-success me-1"></i>
                                                        {{ $item->service_name }}
                                                        @if($item->quantity > 1)
                                                            <span class="badge bg-secondary">× {{ $item->quantity }}</span>
                                                        @endif
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif

                                    <!-- Amount -->
                                    <div class="mb-3">
                                        <h6 class="text-muted mb-2"><i class="fas fa-money-bill-wave me-2"></i>{{ __('common.total_amount') }}</h6>
                                        <h4 class="text-success mb-0">{{ number_format($booking->total_amount, 2) }} {{ __('common.currency') }}</h4>
                                        <small class="text-muted">{{ __('common.paid_in_advance') }} ✓</small>
                                    </div>

                                    <!-- Competition Info -->
                                    <div class="alert alert-info mb-3">
                                        <i class="fas fa-info-circle me-2"></i>
                                        <strong>{{ __('common.active_competition') }}:</strong> 
                                        {{ __('common.notifications_sent_to_suppliers', ['count' => $booking->notified_suppliers_count]) }}.
                                        @if($booking->views_count > 0)
                                            {{ __('common.booking_viewed_by_suppliers', ['count' => $booking->views_count]) }}.
                                        @endif
                                    </div>

                                    <!-- Actions -->
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('supplier.bookings.show', $booking->id) }}" class="btn btn-primary flex-fill">
                                            <i class="fas fa-eye me-2"></i><span style="color: white;">{{ __('common.view_details') }}</span>
                                        </a>
                                        <form action="{{ route('supplier.bookings.accept', $booking->id) }}" method="POST" class="flex-fill">
                                            @csrf
                                            <button type="submit" class="btn btn-success w-100" data-confirm-message="{{ __('common.confirm_accept_booking') }}" onclick="return window.confirm(this.dataset.confirmMessage)">
                                                <i class="fas fa-check me-2"></i><span style="color: white;">{{ __('common.accept_booking') }}</span>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="d-flex justify-content-center">
                    {{ $availableBookings->links() }}
                </div>
            @else
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    {{ __('common.no_available_bookings') }}
                </div>
            @endif
        </div>

        <!-- Accepted Bookings -->
        <div class="tab-pane fade" id="accepted" role="tabpanel">
            @if($acceptedBookings->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>{{ __('common.booking_number') }}</th>
                                <th>{{ __('common.customer') }}</th>
                                <th>{{ __('common.services') }}</th>
                                <th>{{ __('common.amount') }}</th>
                                <th>{{ __('common.status') }}</th>
                                <th>{{ __('common.accepted_at') }}</th>
                                <th>{{ __('common.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($acceptedBookings as $booking)
                                <tr>
                                    <td>{{ $booking->booking_reference }}</td>
                                    <td>—</td>
                                    <td>
                                        @if($booking->quote)
                                            {{ $booking->quote->items->count() }} {{ __('common.service') }}
                                        @endif
                                    </td>
                                    <td>{{ number_format($booking->total_amount, 2) }} {{ __('common.currency') }}</td>
                                    <td>
                                        @if($booking->status === 'confirmed')
                                            <span class="badge bg-success">{{ __('common.confirmed') }}</span>
                                        @elseif($booking->status === 'completed')
                                            <span class="badge bg-primary">{{ __('common.completed') }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $booking->accepted_at ? $booking->accepted_at->format('Y-m-d H:i') : '-' }}</td>
                                    <td>
                                        <a href="{{ route('supplier.bookings.show', $booking->id) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-center">
                    {{ $acceptedBookings->links() }}
                </div>
            @else
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    {{ __('common.no_accepted_bookings') }}
                </div>
            @endif
        </div>

        <!-- Rejected/Expired Bookings -->
        <div class="tab-pane fade" id="rejected" role="tabpanel">
            @if($rejectedBookings->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>{{ __('common.booking_number') }}</th>
                                <th>{{ __('common.customer') }}</th>
                                <th>{{ __('common.status') }}</th>
                                <th>{{ __('common.response_date') }}</th>
                                <th>{{ __('common.reason') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($rejectedBookings as $notification)
                                <tr>
                                    <td>{{ $notification->booking->booking_reference }}</td>
                                    <td>—</td>
                                    <td>
                                        @if($notification->response === 'rejected')
                                            <span class="badge bg-danger">{{ __('common.rejected') }}</span>
                                        @elseif($notification->response === 'expired')
                                            <span class="badge bg-secondary">{{ __('common.expired_taken') }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $notification->responded_at ? $notification->responded_at->format('Y-m-d H:i') : '-' }}</td>
                                    <td>{{ $notification->rejection_reason ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-center">
                    {{ $rejectedBookings->links() }}
                </div>
            @else
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    {{ __('common.no_rejected_or_expired_bookings') }}
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
// Auto-refresh available bookings every 30 seconds
setInterval(function() {
    if ($('#available-tab').hasClass('active')) {
        location.reload();
    }
}, 30000);
</script>
@endpush
@endsection

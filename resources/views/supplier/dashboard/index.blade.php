@extends('supplier.layouts.app')

@section('title', __('common.dashboard'))
@section('page-title', __('common.dashboard'))

@section('content')
<!-- Welcome Message -->
<div class="alert alert-light border-0 mb-4" style="background: linear-gradient(135deg, rgba(31, 20, 74, 0.05), rgba(45, 188, 174, 0.05)); border-radius: 15px;">
    <div class="d-flex align-items-center">
        <div class="me-3">
            <i class="fas fa-hand-wave" style="font-size: 2rem; color: #f0c71d;"></i>
        </div>
        <div>
            <h5 class="mb-1 fw-bold" style="color: #1f144a;">{{ __('common.hello_name', ['name' => $supplier->name]) }}!</h5>
            <p class="mb-0 text-muted">{{ __('common.today_activity_summary') }}</p>
        </div>
    </div>
</div>

<!-- Stats Cards -->
<div class="row g-4 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="stat-card">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="stat-value">{{ $stats['total_services'] }}</div>
                    <div class="stat-label">{{ __('common.total_services') }}</div>
                </div>
                <div class="stat-icon primary">
                    <i class="fas fa-concierge-bell"></i>
                </div>
            </div>
            <div class="mt-3">
                <small class="text-success">
                    <i class="fas fa-check-circle me-1"></i>
                    {{ $stats['active_services'] }} {{ __('common.active_services') }}
                </small>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6">
        <div class="stat-card">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="stat-value">{{ $stats['total_bookings'] }}</div>
                    <div class="stat-label">{{ __('common.total_bookings') }}</div>
                </div>
                <div class="stat-icon secondary">
                    <i class="fas fa-calendar-check"></i>
                </div>
            </div>
            <div class="mt-3">
                <small class="text-warning">
                    <i class="fas fa-clock me-1"></i>
                    {{ $stats['pending_bookings'] }} {{ __('common.pending_confirmation') }}
                </small>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6">
        <div class="stat-card">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="stat-value">{{ $stats['confirmed_bookings'] }}</div>
                    <div class="stat-label">{{ __('common.confirmed_bookings') }}</div>
                </div>
                <div class="stat-icon accent">
                    <i class="fas fa-check-double"></i>
                </div>
            </div>
            <div class="mt-3">
                <small class="text-info">
                    <i class="fas fa-trophy me-1"></i>
                    {{ $stats['completed_bookings'] }} {{ __('common.completed') }}
                </small>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6">
        <div class="stat-card">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="stat-value">{{ number_format($stats['total_revenue'], 0) }}</div>
                    <div class="stat-label">{{ __('common.total_revenue') }} ({{ __('common.currency') }})</div>
                </div>
                <div class="stat-icon gold">
                    <i class="fas fa-coins"></i>
                </div>
            </div>
            <div class="mt-3">
                <small class="text-success">
                    <i class="fas fa-arrow-up me-1"></i>
                    {{ __('common.from_completed_bookings') }}
                </small>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Recent Bookings -->
    <div class="col-lg-8">
        <div class="content-card">
            <div class="card-header-custom">
                <h5><i class="fas fa-calendar-alt me-2 text-primary"></i>{{ __('common.recent_bookings') }}</h5>
                <a href="{{ route('supplier.bookings.index') }}" class="btn btn-sm btn-outline-primary">{{ __('common.view_all') }}</a>
            </div>
            <div class="table-responsive">
                <table class="table table-custom">
                    <thead>
                        <tr>
                            <th>{{ __('common.customer') }}</th>
                            <th>{{ __('common.service') }}</th>
                            <th>{{ __('common.date') }}</th>
                            <th>{{ __('common.amount') }}</th>
                            <th>{{ __('common.status') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentBookings as $booking)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="user-avatar me-2" style="width: 35px; height: 35px; font-size: 0.8rem;">
                                        {{ mb_substr($booking->user->name ?? __('common.guest_initial'), 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="fw-semibold">{{ $booking->user->name ?? __('common.guest') }}</div>
                                        <small class="text-muted">{{ $booking->user->phone ?? '' }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $booking->service->name ?? '-' }}</td>
                            <td>{{ $booking->event_date ? $booking->event_date->format('Y/m/d') : '-' }}</td>
                            <td class="fw-bold">{{ number_format($booking->total_amount, 0) }} {{ __('common.currency') }}</td>
                            <td>
                                @php
                                    $statusClass = match($booking->status) {
                                        'pending' => 'status-pending',
                                        'confirmed' => 'status-confirmed',
                                        'completed' => 'status-completed',
                                        'cancelled' => 'status-cancelled',
                                        default => 'status-pending'
                                    };
                                    $statusText = match($booking->status) {
                                        'pending' => __('common.booking_status_pending'),
                                        'confirmed' => __('common.booking_status_confirmed'),
                                        'completed' => __('common.booking_status_completed'),
                                        'cancelled' => __('common.booking_status_cancelled'),
                                        default => __('common.unknown')
                                    };
                                @endphp
                                <span class="status-badge {{ $statusClass }}">{{ $statusText }}</span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">
                                <i class="fas fa-calendar-times fa-2x mb-2"></i>
                                <p class="mb-0">{{ __('common.no_bookings_yet') }}</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- My Services -->
    <div class="col-lg-4">
        <div class="content-card">
            <div class="card-header-custom">
                <h5><i class="fas fa-concierge-bell me-2 text-secondary"></i>{{ __('common.my_services') }}</h5>
                <a href="{{ route('supplier.services.index') }}" class="btn btn-sm btn-outline-secondary">{{ __('common.view_all') }}</a>
            </div>
            <div class="p-3">
                @forelse($services as $service)
                <div class="d-flex align-items-center p-3 mb-2 rounded-3" style="background: #f8f9fa;">
                    <div class="me-3">
                        <img src="{{ $service->thumbnail_url }}" alt="{{ $service->name }}" 
                             style="width: 50px; height: 50px; object-fit: cover; border-radius: 10px;">
                    </div>
                    <div class="flex-grow-1">
                        <div class="fw-semibold">{{ $service->name }}</div>
                        <small class="text-muted">{{ $service->category->name ?? '-' }}</small>
                    </div>
                    <div>
                        @if($service->is_active)
                            <span class="badge bg-success">{{ __('common.active') }}</span>
                        @else
                            <span class="badge bg-secondary">{{ __('common.inactive') }}</span>
                        @endif
                    </div>
                </div>
                @empty
                <div class="text-center py-4 text-muted">
                    <i class="fas fa-box-open fa-2x mb-2"></i>
                    <p class="mb-0">{{ __('common.no_registered_services') }}</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row mt-4">
    <div class="col-12">
        <div class="content-card">
            <div class="card-header-custom">
                <h5><i class="fas fa-bolt me-2 text-warning"></i>{{ __('common.quick_actions') }}</h5>
            </div>
            <div class="p-4">
                <div class="row g-3">
                    <div class="col-md-3 col-6">
                        <a href="{{ route('supplier.bookings.index') }}?status=pending" class="btn btn-light w-100 py-3 rounded-3">
                            <i class="fas fa-hourglass-half text-warning mb-2" style="font-size: 1.5rem;"></i>
                            <div class="fw-semibold">{{ __('common.pending_bookings') }}</div>
                            <small class="text-muted">{{ $stats['pending_bookings'] }} {{ __('common.booking') }}</small>
                        </a>
                    </div>
                    <div class="col-md-3 col-6">
                        <a href="{{ route('supplier.services.index') }}" class="btn btn-light w-100 py-3 rounded-3">
                            <i class="fas fa-cog text-primary mb-2" style="font-size: 1.5rem;"></i>
                            <div class="fw-semibold">{{ __('common.manage_services') }}</div>
                            <small class="text-muted">{{ $stats['total_services'] }} {{ __('common.service') }}</small>
                        </a>
                    </div>
                    <div class="col-md-3 col-6">
                        <a href="{{ route('supplier.customers.index') }}" class="btn btn-light w-100 py-3 rounded-3">
                            <i class="fas fa-users text-success mb-2" style="font-size: 1.5rem;"></i>
                            <div class="fw-semibold">{{ __('common.my_customers') }}</div>
                            <small class="text-muted">{{ __('common.view_customers') }}</small>
                        </a>
                    </div>
                    <div class="col-md-3 col-6">
                        <a href="{{ route('supplier.reports.index') }}" class="btn btn-light w-100 py-3 rounded-3">
                            <i class="fas fa-chart-pie text-info mb-2" style="font-size: 1.5rem;"></i>
                            <div class="fw-semibold">{{ __('common.reports') }}</div>
                            <small class="text-muted">{{ __('common.statistics') }}</small>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

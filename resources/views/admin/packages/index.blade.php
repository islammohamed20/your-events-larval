@extends('layouts.admin')

@section('title', __('common.admin_packages_management'))
@section('page-title', __('common.admin_packages_management'))
@section('page-description', __('common.admin_packages_management_description'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">{{ __('common.packages') }}</h2>
    <a href="{{ route('admin.packages.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>{{ __('common.add_new_package') }}
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">
            <i class="fas fa-box me-2"></i>{{ __('common.packages_list_with_count', ['count' => $packages->count()]) }}
        </h5>
    </div>
    <div class="card-body">
        @if($packages->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>{{ __('common.image') }}</th>
                            <th>{{ __('common.package_name') }}</th>
                            <th>{{ __('common.price') }}</th>
                            <th>{{ __('common.number_of_people') }}</th>
                            <th>{{ __('common.attributes') }}</th>
                            <th>{{ __('common.status') }}</th>
                            <th>{{ __('common.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($packages as $package)
                            <tr>
                                <td>
                                    @if($package->image)
                                        <img src="{{ asset('storage/' . $package->image) }}" 
                                             alt="{{ $package->name }}" 
                                             class="rounded" 
                                             style="width: 60px; height: 60px; object-fit: cover;">
                                    @else
                                        <div class="bg-light rounded d-flex align-items-center justify-content-center" 
                                             style="width: 60px; height: 60px;">
                                            <i class="fas fa-image text-muted"></i>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <strong>{{ $package->name }}</strong>
                                    <br>
                                    <small class="text-muted">{{ Str::limit($package->description, 50) }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-success fs-6">{{ number_format($package->price) }} {{ __('common.currency') }}</span>
                                </td>
                                <td>
                                    @if($package->persons_min || $package->persons_max)
                                        <span class="badge bg-info">
                                            <i class="fas fa-users me-1"></i>
                                            @if($package->persons_min && $package->persons_max)
                                                {{ $package->persons_min }} - {{ $package->persons_max }} {{ __('common.person') }}
                                            @elseif($package->persons_min)
                                                {{ $package->persons_min }} {{ __('common.person') }}
                                            @else
                                                {{ __('common.up_to') }} {{ $package->persons_max }} {{ __('common.person') }}
                                            @endif
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($package->attributes && count($package->attributes) > 0)
                                        <span class="badge bg-primary">
                                            <i class="fas fa-list me-1"></i>{{ __('common.attributes_count', ['count' => count($package->attributes)]) }}
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($package->is_active)
                                        <span class="badge bg-success">{{ __('common.active') }}</span>
                                    @else
                                        <span class="badge bg-secondary">{{ __('common.inactive') }}</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.packages.edit', $package) }}" 
                                           class="btn btn-sm btn-outline-primary" 
                                           title="{{ __('common.edit') }}">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form method="POST" 
                                              action="{{ route('admin.packages.destroy', $package) }}" 
                                              class="d-inline"
                                              data-confirm-message="{{ __('common.confirm_delete_package') }}"
                                              onsubmit="return window.confirm(this.dataset.confirmMessage)">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="btn btn-sm btn-outline-danger" 
                                                    title="{{ __('common.delete') }}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-box fa-3x text-muted mb-3"></i>
                <h5>{{ __('common.no_packages') }}</h5>
                <p class="text-muted">{{ __('common.no_packages_hint') }}</p>
                <a href="{{ route('admin.packages.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>{{ __('common.add_new_package') }}
                </a>
            </div>
        @endif
    </div>
</div>
@endsection

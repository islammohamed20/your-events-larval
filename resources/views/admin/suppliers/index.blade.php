@extends('layouts.admin')

@section('title', 'إدارة الموردين')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">إدارة الموردين</h1>
            <p class="text-muted mb-0">عرض وإدارة طلبات الموردين المسجلين في المنصة</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-primary bg-opacity-10 text-primary rounded p-3">
                                <i class="fas fa-users fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">إجمالي الموردين</h6>
                            <h3 class="mb-0">{{ $counts['all'] }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-warning bg-opacity-10 text-warning rounded p-3">
                                <i class="fas fa-clock fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">قيد المراجعة</h6>
                            <h3 class="mb-0">{{ $counts['pending'] }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-success bg-opacity-10 text-success rounded p-3">
                                <i class="fas fa-check fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">مقبول</h6>
                            <h3 class="mb-0">{{ $counts['approved'] }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-danger bg-opacity-10 text-danger rounded p-3">
                                <i class="fas fa-ban fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">مرفوض / معلق</h6>
                            <h3 class="mb-0">{{ $counts['rejected'] + $counts['suspended'] }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters & Search -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.suppliers.index') }}">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">البحث</label>
                        <input type="text" name="search" class="form-control" placeholder="ابحث بالاسم، البريد، أو السجل التجاري" value="{{ request('search') }}">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">الحالة</label>
                        <select name="status" class="form-select">
                            <option value="">جميع الحالات</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>قيد المراجعة</option>
                            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>مقبول</option>
                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>مرفوض</option>
                            <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>معلق</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">نوع المورد</label>
                        <select name="type" class="form-select">
                            <option value="">جميع الأنواع</option>
                            <option value="individual" {{ request('type') == 'individual' ? 'selected' : '' }}>فرد</option>
                            <option value="company" {{ request('type') == 'company' ? 'selected' : '' }}>منشأة</option>
                        </select>
                    </div>

                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search me-1"></i>بحث
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Suppliers Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            @if($suppliers->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>المورد</th>
                                <th>النوع</th>
                                <th>الخدمات</th>
                                <th>المدينة</th>
                                <th>الحالة</th>
                                <th>تاريخ التسجيل</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($suppliers as $supplier)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0">
                                                <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                    <i class="fas fa-{{ $supplier->supplier_type == 'company' ? 'building' : 'user' }}"></i>
                                                </div>
                                            </div>
                                            <div class="flex-grow-1 ms-3">
                                                <h6 class="mb-0">{{ $supplier->name }}</h6>
                                                <small class="text-muted">{{ $supplier->email }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $supplier->supplier_type == 'company' ? 'info' : 'secondary' }}">
                                            {{ $supplier->supplier_type == 'company' ? 'منشأة' : 'فرد' }}
                                        </span>
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ count($supplier->services_offered ?? []) }} خدمة</small>
                                    </td>
                                    <td>{{ $supplier->headquarters_city }}</td>
                                    <td>{!! $supplier->status_badge !!}</td>
                                    <td>
                                        <small class="text-muted">{{ $supplier->created_at->diffForHumans() }}</small>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.suppliers.show', $supplier) }}" class="btn btn-sm btn-outline-primary" title="عرض التفاصيل">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            
                                            @if($supplier->status == 'pending')
                                                <form action="{{ route('admin.suppliers.approve', $supplier) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-outline-success" title="قبول" onclick="return confirm('هل أنت متأكد من قبول هذا المورد؟')">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                </form>
                                                
                                                <form action="{{ route('admin.suppliers.reject', $supplier) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="رفض" onclick="return confirm('هل أنت متأكد من رفض هذا المورد؟')">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </form>
                                            @endif

                                            @if($supplier->status == 'approved')
                                                <form action="{{ route('admin.suppliers.suspend', $supplier) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-outline-warning" title="تعليق" onclick="return confirm('هل أنت متأكد من تعليق هذا المورد؟')">
                                                        <i class="fas fa-pause"></i>
                                                    </button>
                                                </form>
                                            @endif

                                            @if($supplier->status == 'suspended')
                                                <form action="{{ route('admin.suppliers.activate', $supplier) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-outline-success" title="إعادة تفعيل" onclick="return confirm('هل أنت متأكد من إعادة تفعيل هذا المورد؟')">
                                                        <i class="fas fa-play"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $suppliers->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-inbox fs-1 text-muted mb-3"></i>
                    <p class="text-muted">لا يوجد موردين مسجلين</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

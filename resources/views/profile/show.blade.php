@extends('layouts.app')

@section('title', 'الملف الشخصي')

@section('content')
<div class="container py-5">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3 mb-4">
            <div class="card">
                <div class="card-body text-center">
                    <div class="avatar-lg mx-auto mb-3">
                        <div class="avatar-title bg-primary rounded-circle" style="width: 80px; height: 80px; display: flex; align-items: center; justify-content: center; font-size: 32px; margin: 0 auto;">
                            {{ strtoupper(substr($user->name, 0, 2)) }}
                        </div>
                    </div>
                    <h5 class="mb-1">{{ $user->name }}</h5>
                    <p class="text-muted small">{{ $user->email }}</p>
                    @if($user->is_admin)
                        <span class="badge bg-danger">مدير النظام</span>
                    @endif
                </div>
                <div class="list-group list-group-flush">
                    <a href="{{ route('profile.show') }}" class="list-group-item list-group-item-action active">
                        <i class="fas fa-user me-2"></i> البيانات الشخصية
                    </a>
                    <a href="{{ route('profile.edit') }}" class="list-group-item list-group-item-action">
                        <i class="fas fa-edit me-2"></i> تعديل البيانات
                    </a>
                    <a href="{{ route('profile.password') }}" class="list-group-item list-group-item-action">
                        <i class="fas fa-key me-2"></i> تغيير كلمة المرور
                    </a>
                    <a href="{{ route('wishlist.index') }}" class="list-group-item list-group-item-action">
                        <i class="fas fa-heart me-2"></i> قائمة المفضلة
                        @if(auth()->user()->wishlists->count() > 0)
                            <span class="badge bg-danger rounded-pill float-end">{{ auth()->user()->wishlists->count() }}</span>
                        @endif
                    </a>
                    <a href="{{ route('quotes.index') }}" class="list-group-item list-group-item-action">
                        <i class="fas fa-file-invoice me-2"></i> عروض الأسعار
                    </a>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-md-9">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Personal Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-user-circle me-2"></i> البيانات الشخصية
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="text-muted small">الاسم الكامل</label>
                            <p class="mb-0 fw-bold">{{ $user->name }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">البريد الإلكتروني</label>
                            <p class="mb-0 fw-bold">{{ $user->email }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">رقم الهاتف</label>
                            <p class="mb-0 fw-bold">{{ $user->phone ?: 'غير محدد' }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">تاريخ التسجيل</label>
                            <p class="mb-0 fw-bold">{{ $user->created_at->format('Y-m-d') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Company Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-building me-2"></i> بيانات الجهة
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="text-muted small">اسم الجهة</label>
                            <p class="mb-0 fw-bold">{{ $user->company_name }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">الرقم الضريبي</label>
                            <p class="mb-0 fw-bold">{{ $user->tax_number ?: 'غير محدد' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-credit-card me-2"></i> معلومات الدفع
                    </h5>
                </div>
                <div class="card-body">
                    @if($user->card_type || $user->card_holder_name || $user->card_last_four)
                        <div class="row g-3">
                            @if($user->card_type)
                            <div class="col-md-6">
                                <label class="text-muted small">نوع البطاقة</label>
                                <p class="mb-0 fw-bold">
                                    @if($user->card_type == 'visa')
                                        <i class="fab fa-cc-visa text-primary"></i> فيزا (Visa)
                                    @elseif($user->card_type == 'mastercard')
                                        <i class="fab fa-cc-mastercard text-warning"></i> ماستر كارد (Mastercard)
                                    @elseif($user->card_type == 'mada')
                                        <i class="fas fa-credit-card text-success"></i> مدى (Mada)
                                    @endif
                                </p>
                            </div>
                            @endif
                            @if($user->card_holder_name)
                            <div class="col-md-6">
                                <label class="text-muted small">اسم حامل البطاقة</label>
                                <p class="mb-0 fw-bold">{{ $user->card_holder_name }}</p>
                            </div>
                            @endif
                            @if($user->card_last_four)
                            <div class="col-md-6">
                                <label class="text-muted small">آخر 4 أرقام من البطاقة</label>
                                <p class="mb-0 fw-bold">**** **** **** {{ $user->card_last_four }}</p>
                            </div>
                            @endif
                            @if($user->card_expiry_month && $user->card_expiry_year)
                            <div class="col-md-6">
                                <label class="text-muted small">تاريخ الانتهاء</label>
                                <p class="mb-0 fw-bold">{{ $user->card_expiry_month }}/{{ $user->card_expiry_year }}</p>
                            </div>
                            @endif
                        </div>
                    @else
                        <div class="text-center text-muted py-3">
                            <i class="fas fa-info-circle me-2"></i>
                            لم يتم إضافة معلومات الدفع بعد
                        </div>
                    @endif
                </div>
            </div>

            <!-- Bookings Statistics -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-line me-2"></i> إحصائيات الحجوزات
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-4">
                            <div class="p-3 border rounded">
                                <h3 class="text-primary mb-0">{{ $bookings->total() }}</h3>
                                <small class="text-muted">إجمالي الحجوزات</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 border rounded">
                                <h3 class="text-warning mb-0">{{ $bookings->where('status', 'pending')->count() }}</h3>
                                <small class="text-muted">قيد الانتظار</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 border rounded">
                                <h3 class="text-success mb-0">{{ $bookings->where('status', 'confirmed')->count() }}</h3>
                                <small class="text-muted">مؤكدة</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Bookings -->
            @if($bookings->count() > 0)
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-calendar-check me-2"></i> آخر الحجوزات
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>رقم الحجز</th>
                                    <th>الباقة</th>
                                    <th>التاريخ</th>
                                    <th>الحالة</th>
                                    <th>الإجمالي</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($bookings as $booking)
                                <tr>
                                    <td>#{{ $booking->id }}</td>
                                    <td>{{ $booking->package->name }}</td>
                                    <td>{{ $booking->event_date }}</td>
                                    <td>
                                        @if($booking->status == 'pending')
                                            <span class="badge bg-warning">قيد الانتظار</span>
                                        @elseif($booking->status == 'confirmed')
                                            <span class="badge bg-success">مؤكدة</span>
                                        @elseif($booking->status == 'cancelled')
                                            <span class="badge bg-danger">ملغاة</span>
                                        @endif
                                    </td>
                                    <td>{{ number_format($booking->total_price, 2) }} ر.س</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        {{ $bookings->links() }}
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<style>
.avatar-title {
    color: white;
    font-weight: bold;
}
.list-group-item.active {
    background-color: #0d6efd;
    border-color: #0d6efd;
}
</style>
@endsection

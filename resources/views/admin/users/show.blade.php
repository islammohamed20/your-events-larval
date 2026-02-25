@extends('layouts.admin')

@section('title', 'تفاصيل المستخدم')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">تفاصيل المستخدم</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> العودة للقائمة
                        </a>
                        <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> تعديل
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="text-center mb-4">
                                <div class="avatar-lg mx-auto mb-3">
                                    <div class="avatar-title bg-primary rounded-circle">
                                        {{ strtoupper(substr($user->name, 0, 2)) }}
                                    </div>
                                </div>
                                <h4>{{ $user->name }}</h4>
                                @if($user->is_admin)
                                    <span class="badge bg-danger fs-6">مدير النظام</span>
                                @else
                                    <span class="badge bg-secondary fs-6">مستخدم عادي</span>
                                @endif
                            </div>
                        </div>
                        
                        <div class="col-md-8">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">البريد الإلكتروني:</label>
                                        <p class="form-control-plaintext">{{ $user->email }}</p>
                                    </div>
                                </div>
                                
                                <div class="col-sm-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">اسم الجهة:</label>
                                        <p class="form-control-plaintext">{{ $user->company_name }}</p>
                                    </div>
                                </div>
                                
                                <div class="col-sm-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">الرقم الضريبي:</label>
                                        <p class="form-control-plaintext">{{ $user->tax_number ?: 'غير محدد' }}</p>
                                    </div>
                                </div>
                                
                                <div class="col-sm-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">تاريخ التسجيل:</label>
                                        <p class="form-control-plaintext">{{ $user->created_at->format('Y-m-d H:i') }}</p>
                                    </div>
                                </div>
                                
                                <div class="col-sm-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">آخر تحديث:</label>
                                        <p class="form-control-plaintext">{{ $user->updated_at->format('Y-m-d H:i') }}</p>
                                    </div>
                                </div>
                                
                                <div class="col-sm-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">عدد الحجوزات:</label>
                                        <p class="form-control-plaintext">
                                            <span class="badge bg-info fs-6">{{ $bookings->total() }}</span>
                                        </p>
                                    </div>
                                </div>
                                
                                <!-- Payment Information -->
                                <div class="col-12">
                                    <hr class="my-3">
                                    <h5 class="mb-3"><i class="fas fa-credit-card"></i> معلومات الدفع</h5>
                                </div>
                                
                                <div class="col-sm-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">نوع البطاقة:</label>
                                        <p class="form-control-plaintext">
                                            @if($user->card_type == 'visa')
                                                <i class="fab fa-cc-visa text-primary"></i> فيزا (Visa)
                                            @elseif($user->card_type == 'mastercard')
                                                <i class="fab fa-cc-mastercard text-warning"></i> ماستر كارد (Mastercard)
                                            @elseif($user->card_type == 'mada')
                                                <i class="fas fa-credit-card text-success"></i> مدى (Mada)
                                            @else
                                                غير محدد
                                            @endif
                                        </p>
                                    </div>
                                </div>
                                
                                <div class="col-sm-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">اسم حامل البطاقة:</label>
                                        <p class="form-control-plaintext">{{ $user->card_holder_name ?: 'غير محدد' }}</p>
                                    </div>
                                </div>
                                
                                <div class="col-sm-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">آخر 4 أرقام:</label>
                                        <p class="form-control-plaintext">
                                            @if($user->card_last_four)
                                                **** **** **** {{ $user->card_last_four }}
                                            @else
                                                غير محدد
                                            @endif
                                        </p>
                                    </div>
                                </div>
                                
                                <div class="col-sm-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">تاريخ الانتهاء:</label>
                                        <p class="form-control-plaintext">
                                            @if($user->card_expiry_month && $user->card_expiry_year)
                                                {{ $user->card_expiry_month }}/{{ $user->card_expiry_year }}
                                            @else
                                                غير محدد
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bookings Section -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">حجوزات المستخدم</h3>
                </div>
                <div class="card-body">
                    @if($bookings->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>#</th>
                                        <th>نوع الخدمة</th>
                                        <th>تاريخ الحدث</th>
                                        <th>المبلغ الإجمالي</th>
                                        <th>الحالة</th>
                                        <th>تاريخ الحجز</th>
                                        <th>الإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($bookings as $booking)
                                        <tr>
                                            <td>{{ $booking->id }}</td>
                                            <td>
                                                @if($booking->service)
                                                    <span class="badge bg-primary">خدمة</span>
                                                    {{ $booking->service->name }}
                                                @elseif($booking->package)
                                                    <span class="badge bg-success">باقة</span>
                                                    {{ $booking->package->name }}
                                                @endif
                                            </td>
                                            <td>{{ $booking->event_date }}</td>
                                            <td>
                                                <span class="fw-bold text-success">{{ number_format($booking->total_amount) }} {{ __('common.currency') }}</span>
                                            </td>
                                            <td>
                                                @switch($booking->status)
                                                    @case('pending')
                                                        <span class="badge bg-warning">في الانتظار</span>
                                                        @break
                                                    @case('confirmed')
                                                        <span class="badge bg-success">مؤكد</span>
                                                        @break
                                                    @case('cancelled')
                                                        <span class="badge bg-danger">ملغي</span>
                                                        @break
                                                    @case('completed')
                                                        <span class="badge bg-info">مكتمل</span>
                                                        @break
                                                @endswitch
                                            </td>
                                            <td>{{ $booking->created_at->format('Y-m-d') }}</td>
                                            <td>
                                                <a href="{{ route('admin.bookings.show', $booking) }}" 
                                                   class="btn btn-sm btn-outline-info" 
                                                   title="عرض التفاصيل">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        @if($bookings->hasPages())
                            <div class="d-flex justify-content-center mt-4">
                                {{ $bookings->links() }}
                            </div>
                        @endif
                    @else
                        <div class="text-center py-4">
                            <div class="text-muted">
                                <i class="fas fa-calendar-times fa-3x mb-3"></i>
                                <p>لا توجد حجوزات لهذا المستخدم</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.avatar-lg {
    width: 80px;
    height: 80px;
}

.avatar-title {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    font-weight: 600;
}
</style>
@endsection

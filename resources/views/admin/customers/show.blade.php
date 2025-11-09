@extends('layouts.admin')

@section('title', 'تفاصيل العميل - ' . $customer->name)

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>تفاصيل العميل: {{ $customer->name }}</h2>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.customers.edit', $customer->id) }}" 
                       class="btn btn-primary">
                        <i class="fas fa-edit"></i> تعديل بيانات العميل
                    </a>
                    <a href="{{ route('admin.customers.export-detail', $customer->id) }}" 
                       class="btn btn-success">
                        <i class="fas fa-file-excel"></i> تصدير بيانات العميل
                    </a>
                    <a href="{{ route('admin.customers.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> العودة
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Customer Info -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">معلومات العميل</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <td><strong>رقم العميل:</strong></td>
                            <td>{{ $customer->id }}</td>
                        </tr>
                        <tr>
                            <td><strong>الاسم:</strong></td>
                            <td>{{ $customer->name }}</td>
                        </tr>
                        <tr>
                            <td><strong>البريد الإلكتروني:</strong></td>
                            <td>{{ $customer->email }}</td>
                        </tr>
                        <tr>
                            <td><strong>رقم الهاتف:</strong></td>
                            <td>{{ $customer->phone ?? 'غير محدد' }}</td>
                        </tr>
                        <tr>
                            <td><strong>اسم الشركة:</strong></td>
                            <td>{{ $customer->company_name ?? 'غير محدد' }}</td>
                        </tr>
                        <tr>
                            <td><strong>الرقم الضريبي:</strong></td>
                            <td>{{ $customer->tax_number ?? 'غير محدد' }}</td>
                        </tr>
                        <tr>
                            <td><strong>حالة العميل:</strong></td>
                            <td>
                                @if($customer->status == 'active')
                                    <span class="badge bg-success">نشط</span>
                                @elseif($customer->status == 'inactive')
                                    <span class="badge bg-warning">غير نشط</span>
                                @elseif($customer->status == 'suspended')
                                    <span class="badge bg-danger">معلق</span>
                                @else
                                    <span class="badge bg-secondary">غير محدد</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td><strong>تاريخ التسجيل:</strong></td>
                            <td>{{ $customer->created_at->format('Y-m-d H:i:s') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">إحصائيات العميل</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="border-end">
                                <h3 class="text-primary">{{ $customer->quotes->count() }}</h3>
                                <p class="mb-0">عروض الأسعار</p>
                            </div>
                        </div>
                        <div class="col-6">
                            <h3 class="text-success">{{ $customer->bookings->count() }}</h3>
                            <p class="mb-0">الحجوزات</p>
                        </div>
                    </div>
                    <hr>
                    <div class="text-center">
                        <h4 class="text-warning">
                            {{ number_format($customer->bookings->where('status', 'confirmed')->sum('total_amount'), 2) }} ر.س
                        </h4>
                        <p class="mb-0">إجمالي المدفوعات</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quotes Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">عروض الأسعار ({{ $customer->quotes->count() }})</h5>
                </div>
                <div class="card-body">
                    @if($customer->quotes->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>رقم العرض</th>
                                    <th>الحالة</th>
                                    <th>المجموع الفرعي</th>
                                    <th>الضريبة</th>
                                    <th>الخصم</th>
                                    <th>الإجمالي</th>
                                    <th>تاريخ الإنشاء</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($customer->quotes as $quote)
                                <tr>
                                    <td>{{ $quote->quote_number }}</td>
                                    <td>
                                        <span class="badge bg-{{ $quote->status == 'approved' ? 'success' : ($quote->status == 'pending' ? 'warning' : 'danger') }}">
                                            {{ $quote->status }}
                                        </span>
                                    </td>
                                    <td>{{ number_format($quote->subtotal, 2) }} ر.س</td>
                                    <td>{{ number_format($quote->tax, 2) }} ر.س</td>
                                    <td>{{ number_format($quote->discount, 2) }} ر.س</td>
                                    <td><strong>{{ number_format($quote->total, 2) }} ر.س</strong></td>
                                    <td>{{ $quote->created_at->format('Y-m-d') }}</td>
                                    <td>
                                        <a href="{{ route('quotes.show', $quote->id) }}" 
                                           class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i> عرض
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-4">
                        <i class="fas fa-file-invoice fa-3x text-muted mb-3"></i>
                        <p class="text-muted">لا توجد عروض أسعار لهذا العميل</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Payments Section -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">المدفوعات المكتملة ({{ $customer->bookings->where('status', 'confirmed')->count() }})</h5>
                </div>
                <div class="card-body">
                    @if($customer->bookings->where('status', 'confirmed')->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>رقم المرجع</th>
                                    <th>اسم العميل</th>
                                    <th>تاريخ الحدث</th>
                                    <th>المبلغ</th>
                                    <th>الحالة</th>
                                    <th>تاريخ الحجز</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($customer->bookings->where('status', 'confirmed') as $booking)
                                <tr>
                                    <td>{{ $booking->booking_reference }}</td>
                                    <td>{{ $booking->client_name }}</td>
                                    <td>{{ $booking->event_date ? $booking->event_date->format('Y-m-d') : 'غير محدد' }}</td>
                                    <td><strong>{{ number_format($booking->total_amount, 2) }} ر.س</strong></td>
                                    <td>
                                        <span class="badge bg-success">{{ $booking->status }}</span>
                                    </td>
                                    <td>{{ $booking->created_at->format('Y-m-d') }}</td>
                                    <td>
                                        <a href="{{ route('bookings.show', $booking->id) }}" 
                                           class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i> عرض
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-4">
                        <i class="fas fa-credit-card fa-3x text-muted mb-3"></i>
                        <p class="text-muted">لا توجد مدفوعات مكتملة لهذا العميل</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
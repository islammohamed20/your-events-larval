@extends('layouts.admin')

@section('title', 'تفاصيل السعر')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <a href="{{ route('admin.quotes.index') }}" class="btn btn-outline-secondary mb-2">
                        <i class="fas fa-arrow-right me-2"></i>العودة للقائمة
                    </a>
                    <h2 class="mb-0">
                        <i class="fas fa-file-invoice-dollar me-2"></i>
                        عرض السعر #{{ $quote->quote_number }}
                    </h2>
                </div>
                <div>
                    {!! $quote->status_badge !!}
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Customer Info -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-black">
                    <h5 class="mb-0"><i class="fas fa-user me-2"></i>معلومات العميل</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">الاسم</label>
                            <p class="mb-0"><strong>{{ $quote->user->name }}</strong></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">اسم الجهة</label>
                            <p class="mb-0"><strong>{{ $quote->user->company_name }}</strong></p>
                        </div>
                        @if($quote->user->tax_number)
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">الرقم الضريبي</label>
                            <p class="mb-0"><strong>{{ $quote->user->tax_number }}</strong></p>
                        </div>
                        @endif
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">البريد الإلكتروني</label>
                            <p class="mb-0"><strong>{{ $quote->user->email }}</strong></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">رقم العرض</label>
                            <p class="mb-0"><strong>{{ $quote->quote_number }}</strong></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">تاريخ الإنشاء</label>
                            <p class="mb-0"><strong>{{ $quote->created_at->format('Y/m/d h:i A') }}</strong></p>
                        </div>
                        @if($quote->bookings()->exists())
                        @php $booking = $quote->bookings()->latest()->first(); @endphp
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">رقم الحجز</label>
                            <p class="mb-0">
                                <a href="{{ route('admin.bookings.show', $booking) }}">
                                    <strong>{{ $booking->booking_reference }}</strong>
                                </a>
                            </p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Services Details -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-black">
                    <h5 class="mb-0"><i class="fas fa-cogs me-2"></i>تفاصيل الخدمات</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>الخدمة</th>
                                    <th class="text-center">الكمية</th>
                                    <th class="text-end">السعر</th>
                                    <th class="text-end">المجموع</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($quote->items as $index => $item)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div>
                                                <strong>{{ $item->service->name ?? $item->service_name }}</strong>
                                                @php $desc = $item->service->description ?? $item->service_description; @endphp
                                                @if($desc)
                                                    <br><small class="text-muted">{{ Str::limit($desc, 60) }}</small>
                                                @endif
                                                @if($item->customer_notes)
                                                    <br><small class="text-info">
                                                        <i class="fas fa-sticky-note me-1"></i>{{ $item->customer_notes }}
                                                    </small>
                                                @endif

                                                {{-- عرض التنويعات المختارة --}}
                                                @php
                                                    $fieldsBySlug = [];
                                                    $fieldOptions = [];
                                                    if ($item->service && is_array($item->service->custom_fields)) {
                                                        foreach ($item->service->custom_fields as $field) {
                                                            $slug = \Illuminate\Support\Str::slug($field['label'] ?? '');
                                                            if ($slug) {
                                                                $fieldsBySlug[$slug] = $field['label'] ?? $slug;
                                                                $opts = $field['options'] ?? [];
                                                                if (is_string($opts)) {
                                                                    $opts = array_map('trim', explode(',', $opts));
                                                                }
                                                                $fieldOptions[$slug] = is_array($opts) ? array_values(array_filter($opts, fn($v) => $v !== null && $v !== '')) : [];
                                                            }
                                                        }
                                                    }
                                                @endphp
                                                @php
                                                    $validSelections = [];
                                                    if (is_array($item->selections)) {
                                                        foreach ($item->selections as $key => $val) {
                                                            if (str_starts_with($key, '_')) continue;
                                                            if (!array_key_exists($key, $fieldsBySlug)) continue;
                                                            $allowed = $fieldOptions[$key] ?? [];
                                                            if (is_array($val)) {
                                                                $vals = array_values(array_filter($val, fn($v) => in_array((string)$v, $allowed, true)));
                                                                if (count($vals) > 0) { $validSelections[$key] = $vals; }
                                                            } else {
                                                                if (in_array((string)$val, $allowed, true)) { $validSelections[$key] = [$val]; }
                                                            }
                                                        }
                                                    }
                                                @endphp
                                                @if(count($validSelections) > 0)
                                                    <div class="mt-2">
                                                        <small class="text-muted d-block">اختيارات العميل:</small>
                                                        <ul class="mb-0" style="padding-right: 18px;">
                                                            @foreach($validSelections as $key => $val)
                                                                @php 
                                                                    $label = $fieldsBySlug[$key];
                                                                @endphp
                                                                <li class="small">
                                                                    <strong>{{ $label }}:</strong>
                                                                    @if(is_array($val))
                                                                        {{ implode('، ', array_filter($val)) }}
                                                                    @else
                                                                        {{ (string)$val }}
                                                                    @endif
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-secondary">{{ $item->quantity }}</span>
                                    </td>
                                    <td class="text-end">{{ number_format($item->price, 2) }} {{ __('common.currency') }}</td>
                                    <td class="text-end"><strong>{{ number_format($item->subtotal, 2) }} {{ __('common.currency') }}</strong></td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <td colspan="4" class="text-end"><strong>المجموع الفرعي:</strong></td>
                                    <td class="text-end"><strong>{{ number_format($quote->subtotal, 2) }} {{ __('common.currency') }}</strong></td>
                                </tr>
                                <tr>
                                    <td colspan="4" class="text-end"><strong>الضريبة (15%):</strong></td>
                                    <td class="text-end"><strong>{{ number_format($quote->tax, 2) }} {{ __('common.currency') }}</strong></td>
                                </tr>
                                @if($quote->discount > 0)
                                <tr class="text-success">
                                    <td colspan="4" class="text-end"><strong>الخصم:</strong></td>
                                    <td class="text-end"><strong>-{{ number_format($quote->discount, 2) }} {{ __('common.currency') }}</strong></td>
                                </tr>
                                @endif
                                <tr class="table-primary">
                                    <td colspan="4" class="text-end"><h5 class="mb-0">الإجمالي:</h5></td>
                                    <td class="text-end"><h5 class="mb-0 text-primary">{{ number_format($quote->total, 2) }} {{ __('common.currency') }}</h5></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Notes -->
            @if($quote->customer_notes)
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-sticky-note me-2"></i>ملاحظات العميل</h5>
                </div>
                <div class="card-body">
                    <p class="mb-0">{{ $quote->customer_notes }}</p>
                </div>
            </div>
            @endif

            @if($quote->admin_notes)
            <div class="card border-0 shadow-sm mb-4 border-primary">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-comment-dots me-2"></i>ملاحظات الإدارة</h5>
                </div>
                <div class="card-body">
                    <p class="mb-0">{{ $quote->admin_notes }}</p>
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            {{-- Supplier Acceptance Status --}}
            @if($quote->accepted_by_supplier_id && $quote->acceptedBySupplier)
                <div class="card border-0 shadow-sm mb-4 border-success">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-user-check me-2"></i>المورد المقبول</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="text-muted small">اسم المورد</label>
                            <p class="mb-0 fw-bold">{{ $quote->acceptedBySupplier->name }}</p>
                        </div>
                        <div class="mb-3">
                            <label class="text-muted small">البريد الإلكتروني</label>
                            <p class="mb-0">
                                <a href="mailto:{{ $quote->acceptedBySupplier->email }}">
                                    {{ $quote->acceptedBySupplier->email }}
                                </a>
                            </p>
                        </div>
                        <div class="mb-3">
                            <label class="text-muted small">رقم الهاتف</label>
                            <p class="mb-0">
                                <a href="tel:{{ $quote->acceptedBySupplier->primary_phone }}">
                                    {{ $quote->acceptedBySupplier->primary_phone }}
                                </a>
                            </p>
                        </div>
                        <div class="mb-3">
                            <label class="text-muted small">تاريخ القبول</label>
                            <p class="mb-0">{{ optional($quote->supplier_accepted_at)->format('Y-m-d H:i') }}</p>
                        </div>
                        @if($quote->supplier_notes)
                            <div class="mb-0">
                                <label class="text-muted small">ملاحظات المورد</label>
                                <p class="mb-0" style="white-space: pre-wrap;">{{ $quote->supplier_notes }}</p>
                            </div>
                        @endif
                        <a href="{{ route('admin.suppliers.show', $quote->acceptedBySupplier) }}" class="btn btn-outline-success w-100 mt-3">
                            <i class="fas fa-eye me-2"></i>عرض ملف المورد
                        </a>
                    </div>
                </div>
            @elseif($quote->status === 'approved')
                <div class="alert alert-info mb-4">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>في انتظار قبول المورد</strong><br>
                    <small>تم إشعار الموردين المعنيين، في انتظار القبول السريع</small>
                </div>
            @endif

            <!-- Update Status -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-black">
                    <h5 class="mb-0"><i class="fas fa-edit me-2"></i>تحديث الحالة</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.quotes.update-status', $quote) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        
                        <div class="mb-3">
                            <label class="form-label">الحالة <span class="text-danger">*</span></label>
                            <select name="status" class="form-select" required>
                                <option value="under_review" {{ $quote->status == 'under_review' ? 'selected' : '' }}>قيد المراجعة</option>
                                <option value="approved" {{ $quote->status == 'approved' ? 'selected' : '' }}>موافق عليه</option>
                                <option value="rejected" {{ $quote->status == 'rejected' ? 'selected' : '' }}>مرفوض</option>
                                <option value="completed" {{ $quote->status == 'completed' ? 'selected' : '' }}>مكتمل</option>
                                <option value="paid" {{ $quote->status == 'paid' ? 'selected' : '' }}>تم الدفع</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">الخصم ({{ __('common.currency') }})</label>
                            <input type="number" name="discount" class="form-control" 
                                   value="{{ $quote->discount }}" min="0" step="0.01"
                                   placeholder="0.00">
                            <small class="text-muted">اترك فارغاً أو 0 لعدم وجود خصم</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">ملاحظات الإدارة</label>
                            <textarea name="admin_notes" class="form-control" rows="4" 
                                      placeholder="أضف ملاحظات للعميل...">{{ $quote->admin_notes }}</textarea>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-save me-2"></i>حفظ التحديثات
                        </button>
                    </form>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-black">
                    <h5 class="mb-0"><i class="fas fa-bolt me-2"></i>إجراءات سريعة</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @if(in_array($quote->status, ['pending', 'under_review']))
                        <form action="{{ route('admin.quotes.update-status', $quote) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="approved">
                            <button type="submit" class="btn btn-success w-100">
                                <i class="fas fa-check me-2"></i>موافقة سريعة
                            </button>
                        </form>
                        <form action="{{ route('admin.quotes.update-status', $quote) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="rejected">
                            <button type="submit" class="btn btn-danger w-100">
                                <i class="fas fa-times me-2"></i>رفض
                            </button>
                        </form>
                        @endif

                        @if($quote->status === 'approved')
                        <form action="{{ route('admin.quotes.update-status', $quote) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="completed">
                            <button type="submit" class="btn btn-info w-100">
                                <i class="fas fa-check-double me-2"></i>وضع علامة كمكتمل
                            </button>
                        </form>
                        @endif

                        @if($quote->status === 'paid' && !$quote->bookings()->exists())
                        <form action="{{ route('admin.quotes.convert-paid', $quote) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-exchange-alt me-2"></i>تحويل المدفوع إلى حجز تنافسي
                            </button>
                        </form>
                        @endif

                        <a href="{{ route('quotes.download', $quote) }}" class="btn btn-outline-primary w-100" target="_blank">
                            <i class="fas fa-download me-2"></i>تحميل PDF
                        </a>

                        <form action="{{ route('admin.quotes.send-email', $quote) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-warning w-100">
                                <i class="fas fa-envelope me-2"></i>إرسال بريد للعميل
                            </button>
                        </form>

                        <form action="{{ route('admin.quotes.destroy', $quote) }}" method="POST" 
                              onsubmit="return confirm('هل أنت متأكد من حذف عرض السعر هذا؟')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger w-100">
                                <i class="fas fa-trash me-2"></i>حذف العرض
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Timeline -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-black">
                    <h5 class="mb-0"><i class="fas fa-history me-2"></i>السجل الزمني</h5>
                </div>
                <div class="card-body">
                    <ul class="timeline">
                        <li class="timeline-item">
                            <i class="fas fa-plus-circle text-primary"></i>
                            <div>
                                <strong>تم الإنشاء</strong>
                                <br>
                                <small class="text-muted">{{ $quote->created_at->format('Y/m/d h:i A') }}</small>
                            </div>
                        </li>
                        @if($quote->approved_at)
                        <li class="timeline-item">
                            <i class="fas fa-check-circle text-success"></i>
                            <div>
                                <strong>تمت الموافقة</strong>
                                <br>
                                <small class="text-muted">{{ $quote->approved_at->format('Y/m/d h:i A') }}</small>
                            </div>
                        </li>
                        @endif
                        @if($quote->rejected_at)
                        <li class="timeline-item">
                            <i class="fas fa-times-circle text-danger"></i>
                            <div>
                                <strong>تم الرفض</strong>
                                <br>
                                <small class="text-muted">{{ $quote->rejected_at->format('Y/m/d h:i A') }}</small>
                            </div>
                        </li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@php $logs = $quote->activityLogs()->latest()->limit(25)->get(); @endphp
@include('admin.partials.activity-logs', ['logs' => $logs, 'title' => 'سجل نشاط عرض السعر'])

<style>
.timeline {
    list-style: none;
    padding: 0;
    margin: 0;
}

.timeline-item {
    display: flex;
    gap: 15px;
    padding-bottom: 20px;
    position: relative;
}

.timeline-item:not(:last-child)::before {
    content: '';
    position: absolute;
    left: 9px;
    top: 25px;
    bottom: -10px;
    width: 2px;
    background: #e9ecef;
}

.timeline-item i {
    font-size: 1.2rem;
    flex-shrink: 0;
    margin-top: 3px;
}
</style>
@endsection

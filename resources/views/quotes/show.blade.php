@extends('layouts.app')

@section('title', 'تفاصيل الطلب') 'عرض السعر #' . $quote->quote_number . ' - Your Events')

@section('content')
<div class="container py-5" style="margin-top: 100px;">
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-lg border-0 rounded-4 mb-4">
                <div class="card-header bg-gradient text-white py-4" style="background: linear-gradient(135deg, #1f144a 0%, #7269b0 100%);">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-1">عرض السعر #{{ $quote->quote_number }}</h3>
                            <small>تاريخ الإنشاء: {{ $quote->created_at->format('Y/m/d h:i A') }}</small>
                        </div>
                        <div>
                            {!! $quote->status_badge !!}
                        </div>
                    </div>
                </div>
                <div class="card-body p-4">
                    <h5 class="mb-4">تفاصيل الخدمات</h5>
                    
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>الخدمة</th>
                                    <th class="text-center">الكمية</th>
                                    <th class="text-center">السعر</th>
                                    <th class="text-end">المجموع</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($quote->items as $item)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($item->service && $item->service->image)
                                                <img src="{{ asset('storage/' . $item->service->image) }}" 
                                                     alt="{{ $item->service_name }}" 
                                                     class="rounded me-3"
                                                     style="width: 60px; height: 60px; object-fit: cover;">
                                            @endif
                                            <div>
                                                <strong>{{ $item->service_name }}</strong>
                                                @if($item->service_description)
                                                    <p class="text-muted small mb-0">{{ Str::limit($item->service_description, 80) }}</p>
                                                @endif
                                                
                                                {{-- عرض التنويعات المختارة --}}
                                                @php
                                                    $variationId = $item->getSelectedVariationId();
                                                    $variation = $variationId ? $item->getVariation() : null;
                                                @endphp
                                                @if($variation && $variation->attributeValuesList && $variation->attributeValuesList->count() > 0)
                                                    <ul class="small text-primary mb-1 mt-1">
                                                        <li class="fw-bold"><i class="fas fa-sliders-h me-1"></i>الخيارات المختارة:</li>
                                                        @foreach($variation->attributeValuesList as $value)
                                                            <li class="ms-3">
                                                                <strong>{{ $value->attribute->name }}:</strong> {{ $value->value }}
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                @endif
                                                
                                                @if(is_array($item->selections) && count($item->selections) > 0)
                                                    <ul class="small text-muted mb-0 mt-1">
                                                        @foreach($item->selections as $key => $values)
                                                            @php
                                                                // تجاهل المفاتيح الداخلية
                                                                if (str_starts_with($key, '_')) continue;
                                                                
                                                                $fieldLabel = $key;
                                                                if ($item->service && is_array($item->service->custom_fields)) {
                                                                    foreach ($item->service->custom_fields as $f) {
                                                                        $slug = \Illuminate\Support\Str::slug($f['label'] ?? '');
                                                                        if ($slug === $key) { $fieldLabel = $f['label']; break; }
                                                                    }
                                                                }
                                                            @endphp
                                                            <li>
                                                                <strong>{{ $fieldLabel }}:</strong>
                                                                @if(is_array($values))
                                                                    {{ implode(', ', $values) }}
                                                                @else
                                                                    {{ $values }}
                                                                @endif
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                @endif
                                                @if($item->customer_notes)
                                                    <small class="text-info d-block mt-1">
                                                        <i class="fas fa-sticky-note me-1"></i>{{ $item->customer_notes }}
                                                    </small>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center align-middle">
                                        <span class="badge bg-secondary">{{ $item->quantity }}</span>
                                    </td>
                                    <td class="text-center align-middle">ريال {{ number_format($item->price, 2) }}</td>
                                    <td class="text-end align-middle"><strong>ريال {{ number_format($item->subtotal, 2) }}</strong></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    @if($quote->customer_notes)
                    <div class="alert alert-light border mt-4">
                        <h6><i class="fas fa-sticky-note me-2 text-primary"></i>ملاحظاتك:</h6>
                        <p class="mb-0">{{ $quote->customer_notes }}</p>
                        
                        @if($quote->status === 'pending')
                        <button type="button" class="btn btn-sm btn-outline-primary mt-2" data-bs-toggle="modal" data-bs-target="#editNotesModal">
                            <i class="fas fa-edit me-1"></i>تعديل الملاحظات
                        </button>
                        @endif
                    </div>
                    @endif
                    
                    @if($quote->admin_notes)
                    <div class="alert alert-info border mt-3">
                        <h6><i class="fas fa-comment-dots me-2"></i>رد الإدارة:</h6>
                        <p class="mb-0">{{ $quote->admin_notes }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card shadow-lg border-0 rounded-4 sticky-top mb-4" style="top: 120px;">
                <div class="card-header bg-gradient text-white py-3" style="background: linear-gradient(135deg, #ef4870 0%, #ff7ba3 100%);">
                    <h5 class="mb-0">
                        <i class="fas fa-calculator me-2"></i>
                        ملخص العرض
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between mb-3">
                        <span>المجموع الفرعي:</span>
                        <strong>{{ number_format($quote->subtotal, 2) }} ريال</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span>الضريبة (15%):</span>
                        <strong>{{ number_format($quote->tax, 2) }} ريال</strong>
                    </div>
                    @if($quote->discount > 0)
                    <div class="d-flex justify-content-between mb-3 text-success">
                        <span><i class="fas fa-tag me-1"></i>الخصم:</span>
                        <strong>-{{ number_format($quote->discount, 2) }} ريال</strong>
                    </div>
                    @endif
                    <hr>
                    <div class="d-flex justify-contentBetween mb-4">
                        <h5>الإجمالي:</h5>
                        <h4 class="text-primary mb-0">{{ number_format($quote->total, 2) }} ريال</h4>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <a href="{{ route('quotes.download', $quote) }}" class="btn btn-primary btn-lg">
                            <i class="fas fa-download me-2"></i>
                            تحميل PDF
                        </a>
                        
                        @if($quote->status === 'approved')
                        <a href="{{ route('quotes.payment', $quote) }}" class="btn btn-success btn-lg">
                            <i class="fas fa-check-circle me-2"></i>
                            تأكيد عرض السعر والدفع
                        </a>
                        @endif
                        
                        @if($quote->status === 'booked')
                        <div class="alert alert-success mb-0">
                            <i class="fas fa-check-circle me-2"></i>
                            تم تأكيد الحجز والدفع بنجاح
                        </div>
                        @endif
                        
                        <a href="{{ route('quotes.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-right me-2"></i>
                            العودة لعروض الأسعار
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="card shadow border-0 rounded-4">
                <div class="card-body p-4">
                    <h6 class="mb-3"><i class="fas fa-info-circle me-2 text-info"></i>معلومات مهمة</h6>
                    <ul class="list-unstyled small text-muted">
                        <li class="mb-2"><i class="fas fa-check-circle me-2 text-success"></i>العرض صالح لمدة 30 يوم</li>
                        <li class="mb-2"><i class="fas fa-check-circle me-2 text-success"></i>الأسعار شاملة ضريبة القيمة المضافة</li>
                        <li class="mb-2"><i class="fas fa-check-circle me-2 text-success"></i>يمكن تعديل الملاحظات قبل الموافقة</li>
                        <li><i class="fas fa-check-circle me-2 text-success"></i>سيتم التواصل معك خلال 24 ساعة</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Notes Modal -->
<div class="modal fade" id="editNotesModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('quotes.update-notes', $quote) }}" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-header">
                    <h5 class="modal-title">تعديل الملاحظات</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">الملاحظات</label>
                        <textarea name="customer_notes" class="form-control" rows="5">{{ $quote->customer_notes }}</textarea>
                        <small class="text-muted">يمكنك تعديل ملاحظاتك فقط إذا كان العرض قيد الانتظار</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>حفظ التعديلات
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.table-hover tbody tr:hover {
    background-color: #f8f9fa;
}

.btn-primary {
    background: linear-gradient(135deg, #ef4870 0%, #ff7ba3 100%);
    border: none;
    transition: all 0.3s ease;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(239, 72, 112, 0.4);
}

.btn-success {
    background: linear-gradient(135deg, #2dbcae 0%, #4dd2c2 100%);
    border: none;
}

.btn-success:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(45, 188, 174, 0.4);
}
</style>
@endsection

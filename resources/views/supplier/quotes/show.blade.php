@extends('supplier.layouts.app')

@section('title', 'تفاصيل عرض السعر')
@section('page-title', 'تفاصيل عرض السعر')

@section('content')
<div class="container-fluid">
    <div class="row g-4">
        <div class="col-12">
            <div class="content-card">
                <div class="card-header-custom">
                    <h5>
                        <i class="fas fa-file-invoice-dollar me-2"></i>
                        عرض رقم: {{ $quote->quote_number ?? ('Q-' . $quote->id) }}
                    </h5>
                    <div class="d-flex align-items-center gap-2">
                        <span class="status-badge {{ match($quote->status){
                            'pending' => 'status-pending',
                            'approved' => 'status-confirmed',
                            'rejected' => 'status-cancelled',
                            'completed' => 'status-completed',
                            default => 'status-pending'
                        } }}">
                            {{ __($quote->status) }}
                        </span>
                        <a href="{{ route('supplier.quotes.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-right me-1"></i>رجوع للقائمة
                        </a>
                    </div>
                </div>
                <div class="p-3">
                    <div class="row g-4">
                        <div class="col-lg-4">
                            <div class="stat-card">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="stat-icon secondary">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <div>
                                        <div class="stat-label">العميل</div>
                                        <div class="stat-value" style="font-size: 1.2rem;">{{ $quote->user->name ?? 'عميل' }}</div>
                                        <div class="text-muted">{{ $quote->user->email ?? '-' }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="stat-card">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="stat-icon gold">
                                        <i class="fas fa-coins"></i>
                                    </div>
                                    <div>
                                        <div class="stat-label">إجمالي عناصر خدماتي</div>
                                        <div class="stat-value">{{ number_format($supplierSubtotal, 2) }} ر.س</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="stat-card">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="stat-icon accent">
                                        <i class="fas fa-calendar"></i>
                                    </div>
                                    <div>
                                        <div class="stat-label">تاريخ الإنشاء</div>
                                        <div class="stat-value" style="font-size: 1.2rem;">{{ optional($quote->created_at)->format('Y-m-d H:i') }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="content-card mt-4">
                        <div class="card-header-custom">
                            <h5>عناصر خدماتي ضمن هذا العرض</h5>
                        </div>
                        <div class="p-3">
                            <div class="table-responsive">
                                <table class="table table-custom">
                                    <thead>
                                        <tr>
                                            <th>الخدمة</th>
                                            <th>الوصف</th>
                                            <th>الكمية</th>
                                            <th>السعر</th>
                                            <th>الإجمالي</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($supplierItems as $item)
                                            <tr>
                                                <td>
                                                    <div class="fw-bold">{{ $item->service->name ?? 'خدمة' }}</div>
                                                    <div class="text-muted" style="font-size: 0.85rem;">#{{ $item->service_id }}</div>
                                                </td>
                                                <td>{{ $item->description ?? '-' }}</td>
                                                <td>{{ $item->quantity }}</td>
                                                <td>{{ number_format($item->price, 2) }} ر.س</td>
                                                <td>{{ number_format($item->subtotal, 2) }} ر.س</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center text-muted py-4">
                                                    لا توجد عناصر تابعة لك في هذا العرض.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    {{-- Quick Approval Section --}}
                    @if($quote->status === 'approved' && !$quote->accepted_by_supplier_id)
                        <div class="content-card mt-4" style="background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 100%); border: 2px solid #4caf50;">
                            <div class="p-4 text-center">
                                <h4 style="color: #2e7d32; margin-bottom: 15px;">
                                    <i class="fas fa-clock me-2"></i>عرض السعر متاح للقبول!
                                </h4>
                                <p style="color: #1b5e20; font-weight: 500;">
                                    ⚡ انتبه: الموردون يتنافسون على هذا العرض، أول مورد يقبل سيفوز بالعميل!
                                </p>
                                <form action="{{ route('supplier.quotes.accept', $quote) }}" method="POST" style="max-width: 500px; margin: 20px auto;" id="acceptForm">
                                    @csrf
                                    <div class="mb-3">
                                        <label class="form-label" style="color: #2e7d32; font-weight: bold;">ملاحظات إضافية للعميل (اختياري)</label>
                                        <textarea name="notes" class="form-control" rows="3" placeholder="أضف أي ملاحظات أو تفاصيل إضافية للعميل..."></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-lg" style="background-color: #4caf50; color: white; padding: 12px 40px; font-size: 18px; border: none; border-radius: 8px;">
                                        <i class="fas fa-check-circle me-2"></i>قبول العرض الآن
                                    </button>
                                </form>
                            </div>
                        </div>
                    @elseif($quote->accepted_by_supplier_id)
                        <div class="content-card mt-4" style="background-color: #fff3cd; border: 2px solid #ffc107;">
                            <div class="p-4 text-center">
                                <h5 style="color: #856404;">
                                    <i class="fas fa-info-circle me-2"></i>تم قبول هذا العرض
                                </h5>
                                @if($quote->accepted_by_supplier_id === auth()->guard('supplier')->id())
                                    <p style="color: #155724; font-weight: bold; font-size: 18px;">
                                        ✅ لقد قبلت هذا العرض! سيتواصل معك العميل قريباً.
                                    </p>
                                    @if($quote->supplier_notes)
                                        <div class="mt-3" style="background-color: white; padding: 15px; border-radius: 5px; text-align: right;">
                                            <strong>ملاحظاتك:</strong>
                                            <p class="mb-0 mt-2">{{ $quote->supplier_notes }}</p>
                                        </div>
                                    @endif
                                @else
                                    <p style="color: #856404;">
                                        تم قبول هذا العرض من قبل مورد آخر.
                                    </p>
                                @endif
                                <small class="text-muted d-block mt-2">
                                    تاريخ القبول: {{ optional($quote->supplier_accepted_at)->format('Y-m-d H:i') }}
                                </small>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


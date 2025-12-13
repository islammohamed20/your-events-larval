@extends('layouts.app')

@section('title', 'السلة') 'سلة التسوق - Your Events')

@section('content')
<div class="container py-5" style="margin-top: 100px;">
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-lg border-0 rounded-4 mb-4">
                <div class="card-header bg-gradient text-white py-4" style="background: linear-gradient(135deg, #1f144a 0%, #7269b0 100%);">
                    <h3 class="mb-0">
                        <i class="fas fa-shopping-cart me-2"></i>
                        سلة التسوق
                    </h3>
                </div>
                <div class="card-body p-4">
                    @if($cartItems->isEmpty())
                        <div class="text-center py-5">
                            <i class="fas fa-shopping-cart text-muted" style="font-size: 80px;"></i>
                            <h4 class="mt-4 text-muted">السلة فارغة</h4>
                            <p class="text-muted">ابدأ بإضافة خدمات إلى سلتك</p>
                            <a href="{{ route('services.index') }}" class="btn btn-primary mt-3">
                                <i class="fas fa-cogs me-2"></i>تصفح الخدمات
                            </a>
                        </div>
                    @else
                        <div class="cart-items">
                            @foreach($cartItems as $item)
                            <div class="cart-item border-bottom pb-4 mb-4" data-item-id="{{ $item->id }}">
                                <div class="row align-items-center">
                                    <div class="col-md-2 text-center mb-3 mb-md-0">
                                        @if($item->service->image)
                                            <img src="{{ asset('storage/' . $item->service->image) }}" 
                                                 alt="{{ $item->service->name }}" 
                                                 class="img-fluid rounded"
                                                 style="max-height: 80px; object-fit: cover;">
                                        @else
                                            <div class="service-icon-placeholder bg-light rounded d-flex align-items-center justify-content-center" 
                                                 style="height: 80px;">
                                                <i class="fas fa-image text-muted fa-2x"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="col-md-4 mb-3 mb-md-0">
                                        <h5 class="mb-1">{{ $item->service->name }}</h5>
                                        <p class="text-muted small mb-0">{{ $item->price }} ريال</p>
                                    </div>
                                    <div class="col-md-3 mb-3 mb-md-0">
                                        <div class="input-group" style="max-width: 150px;">
                                            <button class="btn btn-outline-secondary btn-sm qty-btn" type="button" data-action="minus">
                                                <i class="fas fa-minus"></i>
                                            </button>
                                            <input type="number" class="form-control text-center qty-input" 
                                                   value="{{ $item->quantity }}" min="1" max="100">
                                            <button class="btn btn-outline-secondary btn-sm qty-btn" type="button" data-action="plus">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="col-md-2 text-center mb-3 mb-md-0">
                                        <strong class="item-subtotal">{{ number_format($item->subtotal, 2) }} ريال</strong>
                                    </div>
                                    <div class="col-md-1 text-center">
                                        <button class="btn btn-sm btn-outline-danger remove-item" data-id="{{ $item->id }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                                
                                {{-- عرض التنويعات المختارة --}}
                                @php
                                    $variationId = $item->getSelectedVariationId();
                                    $variation = $variationId ? $item->getVariation() : null;
                                @endphp
                                @if($variation)
                                    <div class="mt-2">
                                        <small class="text-muted d-block mb-1">
                                            <i class="fas fa-sliders-h me-1"></i>
                                            الخيارات المختارة:
                                        </small>
                                        <ul class="small text-muted mb-0">
                                            @foreach($variation->attributeValuesList as $value)
                                                <li>
                                                    <strong>{{ $value->attribute->name }}:</strong> {{ $value->value }}
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                                
                                @if(is_array($item->selections) && count($item->selections) > 0)
                                <div class="mt-2">
                                    <small class="text-muted d-block mb-1">
                                        <i class="fas fa-list-alt me-1"></i>
                                        اختياراتك:
                                    </small>
                                    <ul class="small text-muted mb-0">
                                        @foreach($item->selections as $key => $values)
                                            @php
                                                // تجاهل المفاتيح التي تبدأ بـ underscore (حقول داخلية)
                                                if (str_starts_with($key, '_')) continue;
                                                
                                                $fieldLabel = $key;
                                                if (is_array($item->service->custom_fields)) {
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
                                </div>
                                @endif
                                @if($item->customer_notes)
                                <div class="mt-3">
                                    <small class="text-muted">
                                        <i class="fas fa-sticky-note me-1"></i>
                                        ملاحظات: {{ $item->customer_notes }}
                                    </small>
                                </div>
                                @endif
                            </div>
                            @endforeach
                        </div>
                        
                        <div class="text-end mt-4">
                            <form action="{{ route('cart.clear') }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من تفريغ السلة؟')">
                                @csrf
                                <button type="submit" class="btn btn-outline-danger">
                                    <i class="fas fa-trash me-2"></i>تفريغ السلة
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        @if(!$cartItems->isEmpty())
        <div class="col-lg-4">
            <div class="card shadow-lg border-0 rounded-4 sticky-top" style="top: 120px;">
                <div class="card-header bg-gradient text-white py-3" style="background: linear-gradient(135deg, #ef4870 0%, #ff7ba3 100%);">
                    <h5 class="mb-0">
                        <i class="fas fa-calculator me-2"></i>
                        ملخص الطلب
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between mb-3">
                        <span>المجموع الفرعي:</span>
                        <strong id="cart-subtotal">{{ number_format($total, 2) }} ريال</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span>الضريبة (15%):</span>
                        <strong id="cart-tax">{{ number_format($tax, 2) }} ريال</strong>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-4">
                        <h5>الإجمالي:</h5>
                        <h5 class="text-primary" id="cart-total">{{ number_format($grandTotal, 2) }} ريال</h5>
                    </div>
                    
                    @auth
                        <form action="{{ route('quotes.checkout') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">ملاحظات إضافية (اختياري)</label>
                                <textarea name="customer_notes" class="form-control" rows="3" 
                                          placeholder="أضف أي ملاحظات أو متطلبات خاصة..."></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary w-100 py-3 btn-lg">
                                <i class="fas fa-check-circle me-2"></i>
                                إنشاء عرض سعر
                            </button>
                        </form>
                        <p class="text-muted small text-center mt-3 mb-0">
                            <i class="fas fa-info-circle me-1"></i>
                            سيتم إرسال عرض السعر للمراجعة
                        </p>
                    @else
                        <a href="{{ route('login') }}?redirect={{ urlencode(route('cart.index')) }}" class="btn btn-primary w-100 py-3 btn-lg">
                            <i class="fas fa-sign-in-alt me-2"></i>
                            سجل دخولك للمتابعة
                        </a>
                        <p class="text-muted small text-center mt-3 mb-0">
                            <i class="fas fa-info-circle me-1"></i>
                            يجب تسجيل الدخول لإنشاء عرض سعر
                        </p>
                    @endauth
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<style>
.cart-item {
    transition: all 0.3s ease;
}

.cart-item:hover {
    background: #f8f9fa;
    border-radius: 10px;
    padding: 15px;
    margin: -15px;
    margin-bottom: 15px;
}

.qty-btn {
    width: 40px;
    padding: 0.25rem 0;
}

.qty-input {
    max-width: 70px;
    text-align: center;
    border-left: 0;
    border-right: 0;
}

.remove-item:hover {
    transform: scale(1.1);
    transition: transform 0.2s ease;
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
</style>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Update quantity
    document.querySelectorAll('.qty-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const cartItem = this.closest('.cart-item');
            const input = cartItem.querySelector('.qty-input');
            const itemId = cartItem.dataset.itemId;
            let newQty = parseInt(input.value);
            
            if (this.dataset.action === 'plus') {
                newQty++;
            } else if (this.dataset.action === 'minus' && newQty > 1) {
                newQty--;
            }
            
            input.value = newQty;
            updateCartItem(itemId, newQty);
        });
    });
    
    // Remove item
    document.querySelectorAll('.remove-item').forEach(btn => {
        btn.addEventListener('click', function() {
            const itemId = this.dataset.id;
            if (confirm('هل تريد حذف هذه الخدمة من السلة؟')) {
                removeCartItem(itemId);
            }
        });
    });
    
    function updateCartItem(itemId, quantity) {
        fetch(`/cart/${itemId}`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ quantity: quantity })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        })
        .catch(error => console.error('Error:', error));
    }
    
    function removeCartItem(itemId) {
        fetch(`/cart/${itemId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        })
        .catch(error => console.error('Error:', error));
    }
});
</script>
@endpush
@endsection

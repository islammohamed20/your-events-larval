@if($cartItems->count() > 0)
    <div class="cart-dropdown-header text-center">
        <h6 class="mb-0">
            <i class="fas fa-shopping-cart me-2"></i>
            سلة التسوق ({{ $cartItems->count() }})
        </h6>
    </div>
    
    <div class="cart-dropdown-items">
        @foreach($cartItems as $item)
        <div class="cart-dropdown-item">
            <div class="item-image">
                @if($item->service->image)
                    <img src="{{ Storage::url($item->service->image) }}" alt="{{ $item->service->name }}">
                @else
                    <img src="{{ asset('images/service-default.svg') }}" alt="{{ $item->service->name }}">
                @endif
            </div>
            <div class="item-details">
                <h6 class="item-name">{{ Str::limit($item->service->name, 30) }}</h6>
                <p class="item-quantity">الكمية: {{ $item->quantity }}</p>
                @if($item->variation)
                    <p class="item-variation">
                        @foreach($item->variation->attributeValuesList as $value)
                            <span class="badge bg-secondary">{{ $value->value }}</span>
                        @endforeach
                    </p>
                @endif
            </div>
            <div class="item-price">
                <strong>{{ number_format($item->subtotal) }} ر.س</strong>
            </div>
        </div>
        @endforeach
    </div>
    
    <div class="cart-dropdown-footer">
        <div class="cart-total mb-3">
            <span>المجموع:</span>
            <strong class="text-primary">{{ number_format($cartTotal) }} ر.س</strong>
        </div>
        <div class="text-center">
            <a href="{{ route('cart.index') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-shopping-cart me-1"></i>عرض السلة
            </a>
        </div>
    </div>
@else
    <div class="cart-dropdown-empty">
        <i class="fas fa-shopping-cart"></i>
        <p>سلة التسوق فارغة</p>
        <a href="{{ route('services.index') }}" class="btn btn-primary btn-sm">
            تصفح الخدمات
        </a>
    </div>
@endif

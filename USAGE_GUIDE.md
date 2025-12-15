# 🎯 دليل استخدام نظام الحجوزات التنافسية

## 📖 كيفية استخدام النظام

### للعميل (Customer)

#### 1. إنشاء عرض سعر
```php
// في QuoteController::checkout()
$quote = Quote::create([
    'user_id' => auth()->id(),
    'quote_number' => Quote::generateQuoteNumber(),
    'status' => 'pending',
    'payment_status' => 'unpaid',
    'subtotal' => $cartTotal,
    'tax' => $tax,
    'total' => $total,
]);

// نسخ عناصر السلة إلى quote items
foreach (auth()->user()->cartItems as $item) {
    QuoteItem::create([
        'quote_id' => $quote->id,
        'service_id' => $item->service_id,
        'service_name' => $item->service->name,
        'price' => $item->price,
        'quantity' => $item->quantity,
        'subtotal' => $item->subtotal,
    ]);
}
```

#### 2. دفع عرض السعر
```php
// في QuoteController::processPayment()
$quote->update([
    'payment_status' => 'paid',
    'payment_date' => now(),
    'payment_method' => $request->payment_method,
    'payment_reference' => $request->payment_reference,
    'status' => 'paid',
]);

// تحويل Quote إلى Booking
$booking = $quote->convertToBooking();

return redirect()->route('quotes.show', $quote)
    ->with('success', 'تم الدفع بنجاح! سيتم إشعار الموردين المؤهلين.');
```

#### 3. تتبع الحجز
```php
// في ProfileController أو BookingController
$myBookings = auth()->user()->bookings()
    ->with(['supplier', 'quote'])
    ->orderBy('created_at', 'desc')
    ->get();

foreach ($myBookings as $booking) {
    echo "الحجز: {$booking->booking_reference}\n";
    echo "الحالة: {$booking->status}\n";
    
    if ($booking->supplier) {
        echo "المورد: {$booking->supplier->company_name}\n";
    } else if ($booking->status === 'awaiting_supplier') {
        echo "⏰ بانتظار قبول المورد (ينتهي في {$booking->expires_at->diffForHumans()})\n";
    }
}
```

---

### للمورد (Supplier)

#### 1. عرض الحجوزات المتاحة
```php
// في supplier panel
Route::get('/supplier/bookings', function () {
    $supplier = auth('supplier')->user();
    
    $availableBookings = Booking::whereHas('notifications', function ($query) use ($supplier) {
        $query->where('supplier_id', $supplier->id)
              ->where('response', 'pending');
    })
    ->where('status', 'awaiting_supplier')
    ->whereNull('supplier_id')
    ->where('expires_at', '>', now())
    ->with(['user', 'quote.items'])
    ->get();
    
    return view('supplier.bookings.index', compact('availableBookings'));
});
```

#### 2. قبول الحجز
```php
// في SupplierBookingController::accept()
try {
    $booking->acceptBySupplier(auth('supplier')->user());
    
    return redirect()->route('supplier.bookings.show', $booking)
        ->with('success', 'تم قبول الحجز بنجاح! تم إشعار العميل.');
} catch (\Exception $e) {
    return back()->with('error', $e->getMessage());
}
```

#### 3. رفض الحجز
```php
// في SupplierBookingController::reject()
$booking->rejectBySupplier(
    auth('supplier')->user(),
    $request->rejection_reason
);

return redirect()->route('supplier.bookings.index')
    ->with('success', 'تم رفض الحجز.');
```

---

## 🔧 نماذج الاستخدام (Use Cases)

### Use Case 1: عميل يطلب 3 خدمات مختلفة
```php
// 1. العميل يضيف 3 خدمات للسلة
CartItem::create(['user_id' => $userId, 'service_id' => 1]);
CartItem::create(['user_id' => $userId, 'service_id' => 5]);
CartItem::create(['user_id' => $userId, 'service_id' => 8]);

// 2. العميل ينشئ Quote
$quote = Quote::create([...]);
// ينشأ 3 QuoteItems

// 3. العميل يدفع
$quote->update(['payment_status' => 'paid']);
$booking = $quote->convertToBooking();

// 4. النظام يبحث عن الموردين المؤهلين
// مثلاً:
// - Service 1: لديه موردين (A, B)
// - Service 5: لديه موردين (B, C)
// - Service 8: لديه موردين (A, C)
// النتيجة: الموردين المؤهلين = A, B, C (الذين لديهم واحد أو أكثر من الخدمات)

// 5. النظام يرسل إشعارات لـ A, B, C

// 6. المورد B يقبل أولاً
$booking->acceptBySupplier($supplierB);

// 7. يتم إلغاء إشعارات A و C تلقائياً
```

---

### Use Case 2: مورد يشاهد حجز لكن لا يقبله
```php
// 1. المورد يشاهد الحجز
$notification = BookingNotification::where('booking_id', $bookingId)
    ->where('supplier_id', $supplierId)
    ->first();

$notification->markAsViewed(); // viewed_at = now()

// 2. المورد يفكر ولكن لا يقبل

// 3. مورد آخر يقبل الحجز
$booking->acceptBySupplier($anotherSupplier);

// 4. إشعار المورد الأول يتحول إلى 'expired' تلقائياً
```

---

### Use Case 3: حجز ينتهي بدون قبول من أي مورد
```php
// بعد 24 ساعة من إنشاء الحجز:

// Cron Job يعمل:
Booking::where('status', 'awaiting_supplier')
    ->where('expires_at', '<', now())
    ->whereNull('supplier_id')
    ->update(['status' => 'expired']);

// تحديث الإشعارات:
BookingNotification::whereHas('booking', function ($query) {
    $query->where('status', 'expired');
})
->where('response', 'pending')
->update([
    'response' => 'expired',
    'responded_at' => now()
]);

// يمكن إرسال إيميل للعميل:
Mail::to($booking->user->email)->send(
    new BookingExpiredMail($booking)
);
```

---

## 📝 أمثلة Blade Templates

### للعميل: عرض حالة الحجز
```blade
@if($booking->status === 'awaiting_supplier')
    <div class="alert alert-warning">
        <i class="fas fa-clock"></i>
        <strong>بانتظار قبول المورد</strong>
        <p>تم إرسال إشعارات لـ {{ $booking->notified_suppliers_count }} موردين</p>
        <p>⏰ ينتهي التنافس في: {{ $booking->expires_at->diffForHumans() }}</p>
    </div>
@elseif($booking->status === 'confirmed' && $booking->supplier)
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i>
        <strong>تم قبول حجزك!</strong>
        <p>المورد: {{ $booking->supplier->company_name }}</p>
        <p>الهاتف: {{ $booking->supplier->phone }}</p>
    </div>
@elseif($booking->status === 'expired')
    <div class="alert alert-danger">
        <i class="fas fa-times-circle"></i>
        <strong>انتهت صلاحية الحجز</strong>
        <p>لم يقبل أي مورد الحجز خلال المهلة المحددة</p>
        <a href="{{ route('quotes.create') }}" class="btn btn-primary">
            إنشاء طلب جديد
        </a>
    </div>
@endif
```

---

### للمورد: عرض الحجز المتاح
```blade
<div class="booking-card">
    <div class="booking-header">
        <h3>{{ $booking->booking_reference }}</h3>
        <span class="badge badge-warning">
            ⏰ {{ $booking->expires_at->diffForHumans() }}
        </span>
    </div>
    
    <div class="booking-body">
        <p><strong>العميل:</strong> {{ $booking->client_name }}</p>
        <p><strong>المبلغ:</strong> {{ number_format($booking->total_amount, 2) }} ر.س</p>
        
        <h4>الخدمات المطلوبة:</h4>
        <ul>
            @foreach($booking->quote->items as $item)
                <li>
                    {{ $item->service_name }}
                    @if($item->quantity > 1)
                        ({{ $item->quantity }} × {{ $item->price }} ر.س)
                    @endif
                </li>
            @endforeach
        </ul>
        
        <div class="notification-info">
            <small>
                <i class="fas fa-users"></i>
                تم إرسال إشعارات لـ {{ $booking->notified_suppliers_count }} موردين
            </small>
            <br>
            <small>
                <i class="fas fa-eye"></i>
                شاهد الحجز {{ $booking->views_count }} موردين
            </small>
        </div>
    </div>
    
    <div class="booking-footer">
        <form method="POST" action="{{ route('supplier.bookings.accept', $booking) }}" style="display:inline;">
            @csrf
            <button type="submit" class="btn btn-success">
                <i class="fas fa-check"></i> قبول الحجز
            </button>
        </form>
        
        <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#rejectModal{{ $booking->id }}">
            <i class="fas fa-times"></i> رفض
        </button>
    </div>
</div>
```

---

## 🔍 Debugging & Troubleshooting

### تحقق من الموردين المؤهلين
```php
$booking = Booking::find(1);
$eligibleSuppliers = $booking->getEligibleSuppliers();

dd([
    'booking_id' => $booking->id,
    'services_in_quote' => $booking->quote->items->pluck('service_name'),
    'eligible_suppliers' => $eligibleSuppliers->map(function($s) {
        return [
            'id' => $s->id,
            'company' => $s->company_name,
            'services' => $s->services->pluck('name'),
        ];
    }),
]);
```

### تحقق من حالة الإشعارات
```php
$booking = Booking::find(1);
$notifications = $booking->notifications()->with('supplier')->get();

foreach ($notifications as $notification) {
    echo "المورد: {$notification->supplier->company_name}\n";
    echo "الحالة: {$notification->response}\n";
    echo "تم الإشعار: {$notification->notified_at}\n";
    echo "تم المشاهدة: " . ($notification->viewed_at ?? 'لا') . "\n";
    echo "تم الرد: " . ($notification->responded_at ?? 'لا') . "\n";
    echo "---\n";
}
```

### سجل الأحداث (Activity Logs)
```php
$booking = Booking::find(1);
$logs = $booking->activityLogs;

foreach ($logs as $log) {
    echo "[{$log->created_at}] {$log->description}\n";
    if ($log->properties) {
        print_r($log->properties);
    }
}
```

---

## ⚠️ ملاحظات مهمة

1. **Race Conditions:** تم التعامل معها باستخدام `lockForUpdate()`
2. **Email Failures:** يتم تسجيلها في Laravel Logs بدون إيقاف العملية
3. **Expired Bookings:** تحتاج إلى Cron Job لتحديثها تلقائياً
4. **Supplier Eligibility:** يجب أن يكون المورد `approved` و `email_verified_at` ليس null
5. **Notifications Uniqueness:** يوجد unique constraint على (booking_id, supplier_id)

---

## 🎨 تخصيص Email Templates

جميع templates في `resources/views/emails/`:
- `booking-notification.blade.php`
- `quote-payment-confirmation.blade.php`
- `booking-accepted-by-supplier.blade.php`

يمكن تعديل:
- الألوان
- النصوص
- التصميم
- الأيقونات

---

## 📊 إحصائيات مفيدة

```php
// إحصائيات للموردين
$supplier = auth('supplier')->user();

$stats = [
    'pending_bookings' => Booking::whereHas('notifications', function ($query) use ($supplier) {
        $query->where('supplier_id', $supplier->id)->where('response', 'pending');
    })->count(),
    
    'accepted_bookings' => $supplier->bookings()->where('status', 'confirmed')->count(),
    
    'total_revenue' => $supplier->bookings()->where('status', 'confirmed')->sum('total_amount'),
    
    'response_rate' => BookingNotification::where('supplier_id', $supplier->id)
        ->whereNotNull('responded_at')
        ->count() / max(1, BookingNotification::where('supplier_id', $supplier->id)->count()) * 100,
];
```

---

تم بحمد الله! 🚀

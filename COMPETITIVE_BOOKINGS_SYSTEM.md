# نظام الحجوزات التنافسية المدمج مع عروض الأسعار

## 📋 نظرة عامة

تم دمج نظام Competitive Orders مع نظام Bookings لإنشاء نظام حجوزات تنافسي متكامل يربط بين عروض الأسعار (Quotes) والحجوزات (Bookings).

### السيناريو الكامل

```
1. العميل ينشئ Quote (عرض سعر) ← الحالة: pending
2. العميل يدفع ← الحالة: paid
3. يتم تحويل Quote إلى Booking تلقائياً ← الحالة: awaiting_supplier
4. يتم إرسال إشعارات لجميع الموردين المؤهلين
5. الموردين يتنافسون (First-Come-First-Served)
6. أول مورد يقبل الحجز يحصل عليه ← الحالة: confirmed
7. يتم إشعار العميل بقبول المورد
8. يتم إلغاء باقي إشعارات الموردين الآخرين
```

---

## 🗄️ قاعدة البيانات

### 1. جدول `quotes` (محدّث)
```sql
-- الحقول الجديدة المضافة:
payment_status ENUM('unpaid', 'paid', 'refunded') DEFAULT 'unpaid'
payment_date TIMESTAMP NULL
payment_method VARCHAR(255) NULL
payment_reference VARCHAR(255) NULL
payment_notes TEXT NULL

-- Status محدّث:
status ENUM('pending', 'under_review', 'approved', 'rejected', 'completed', 'paid')
```

### 2. جدول `bookings` (محدّث)
```sql
-- الحقول الجديدة المضافة:
expires_at TIMESTAMP NULL                 -- وقت انتهاء التنافس (24 ساعة)
notified_suppliers_count INT DEFAULT 0    -- عدد الموردين المُشعرين
views_count INT DEFAULT 0                 -- عدد المشاهدات
accepted_at TIMESTAMP NULL                -- وقت القبول
supplier_id (تم تعديله ليصبح nullable)

-- Status محدّث:
status ENUM('pending', 'awaiting_supplier', 'confirmed', 'cancelled', 'expired', 'completed')
```

### 3. جدول `booking_notifications` (جديد)
```sql
CREATE TABLE booking_notifications (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    booking_id BIGINT NOT NULL,
    supplier_id BIGINT NOT NULL,
    notified_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    viewed_at TIMESTAMP NULL,
    responded_at TIMESTAMP NULL,
    response ENUM('pending', 'accepted', 'rejected', 'expired') DEFAULT 'pending',
    rejection_reason TEXT NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    
    UNIQUE KEY unique_booking_supplier (booking_id, supplier_id),
    INDEX idx_booking (booking_id),
    INDEX idx_supplier (supplier_id),
    INDEX idx_response_notified (response, notified_at),
    
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE,
    FOREIGN KEY (supplier_id) REFERENCES suppliers(id) ON DELETE CASCADE
);
```

---

## 📦 Models

### Quote Model
**الموقع:** `app/Models/Quote.php`

**الدوال الجديدة:**
```php
public function convertToBooking()
// تحويل عرض السعر المدفوع إلى حجز مع إرسال إشعارات للموردين

public function bookings()
// علاقة مع الحجوزات المنشأة من هذا العرض
```

**الحقول المضافة:**
- `payment_status`
- `payment_date`
- `payment_method`
- `payment_reference`
- `payment_notes`

---

### Booking Model
**الموقع:** `app/Models/Booking.php`

**الدوال الرئيسية:**
```php
// علاقة مع الإشعارات
public function notifications()

// الحصول على الموردين المؤهلين
public function getEligibleSuppliers()

// إرسال إشعارات لجميع الموردين المؤهلين
public function notifyEligibleSuppliers()

// قبول الحجز من قبل المورد (مع حماية من race conditions)
public function acceptBySupplier(Supplier $supplier, $notes = null)

// رفض الحجز من قبل المورد
public function rejectBySupplier(Supplier $supplier, $reason = null)

// التحقق من انتهاء صلاحية الحجز
public function isExpired()

// التحقق من أن الحجز لا يزال نشطاً
public function isActive()
```

**الحقول المضافة:**
- `expires_at`
- `notified_suppliers_count`
- `views_count`
- `accepted_at`

---

### BookingNotification Model
**الموقع:** `app/Models/BookingNotification.php`

**الدوال:**
```php
public function booking()           // علاقة مع الحجز
public function supplier()          // علاقة مع المورد
public function markAsViewed()      // تسجيل المشاهدة

// Scopes
public function scopePending($query)
public function scopeAccepted($query)
public function scopeRejected($query)
public function scopeViewed($query)
public function scopeUnviewed($query)
```

---

## 🎮 Controllers

### SupplierBookingController
**الموقع:** `app/Http/Controllers/Supplier/SupplierBookingController.php`

**الدوال:**
```php
// عرض قائمة الحجوزات (متاحة، مقبولة، مرفوضة)
public function index(Request $request)

// عرض تفاصيل حجز معين
public function show($id)

// قبول الحجز
public function accept(Request $request, $id)

// رفض الحجز
public function reject(Request $request, $id)

// عدد الحجوزات المعلقة (للـ badge)
public function pendingCount()
```

---

## 📧 Mail Classes

### 1. BookingNotificationMail
**الموقع:** `app/Mail/BookingNotificationMail.php`
**Template:** `resources/views/emails/booking-notification.blade.php`

**الغرض:** إشعار المورد بحجز جديد متاح للتنافس

**المحتوى:**
- تفاصيل الحجز
- العد التنازلي للوقت المتبقي
- قائمة الخدمات المطلوبة
- أزرار القبول/الرفض
- تحذير بأن الحجز متاح لجميع الموردين

---

### 2. QuotePaymentConfirmationMail
**الموقع:** `app/Mail/QuotePaymentConfirmationMail.php`
**Template:** `resources/views/emails/quote-payment-confirmation.blade.php`

**الغرض:** تأكيد استلام الدفع من العميل

**المحتوى:**
- تأكيد الدفع
- تفاصيل الدفع
- شرح الخطوات القادمة
- معلومات التنافس (24 ساعة)

---

### 3. BookingAcceptedBySupplierMail
**الموقع:** `app/Mail/BookingAcceptedBySupplierMail.php`
**Template:** `resources/views/emails/booking-accepted-by-supplier.blade.php`

**الغرض:** إشعار العميل بقبول المورد للحجز

**المحتوى:**
- تأكيد قبول الحجز
- معلومات المورد (الشركة، الهاتف، البريد)
- تفاصيل الحجز
- الخطوات القادمة

---

## 🛣️ Routes

### Routes للموردين (Supplier Panel)
```php
// في routes/web.php ضمن supplier middleware:

Route::get('/bookings', [SupplierBookingController::class, 'index'])
    ->name('supplier.bookings.index');

Route::get('/bookings/{booking}', [SupplierBookingController::class, 'show'])
    ->name('supplier.bookings.show');

Route::post('/bookings/{booking}/accept', [SupplierBookingController::class, 'accept'])
    ->name('supplier.bookings.accept');

Route::post('/bookings/{booking}/reject', [SupplierBookingController::class, 'reject'])
    ->name('supplier.bookings.reject');

Route::get('/bookings/count/pending', [SupplierBookingController::class, 'pendingCount'])
    ->name('supplier.bookings.pending-count');
```

---

## 🔄 سير العمل (Workflow)

### 1. إنشاء عرض السعر
```php
// العميل ينشئ Quote من السلة
$quote = Quote::create([
    'user_id' => auth()->id(),
    'quote_number' => Quote::generateQuoteNumber(),
    'status' => 'pending',
    'payment_status' => 'unpaid',
    // ... باقي الحقول
]);
```

### 2. عملية الدفع
```php
// عند الدفع، يتم تحديث Quote
$quote->update([
    'payment_status' => 'paid',
    'payment_date' => now(),
    'payment_method' => $paymentMethod,
    'payment_reference' => $reference,
    'status' => 'paid',
]);

// تحويل Quote إلى Booking
$booking = $quote->convertToBooking();
```

### 3. تحويل Quote إلى Booking
```php
// في Quote::convertToBooking()
$booking = Booking::create([
    'quote_id' => $this->id,
    'status' => 'awaiting_supplier',
    'expires_at' => now()->addHours(24),
    'payment_status' => 'paid',
    // ... باقي الحقول
]);

// إرسال إشعارات للموردين
$booking->notifyEligibleSuppliers();

// إرسال إيميل للعميل
Mail::to($this->user->email)->send(new QuotePaymentConfirmationMail($this));
```

### 4. إشعار الموردين
```php
// في Booking::notifyEligibleSuppliers()
$suppliers = $this->getEligibleSuppliers();

foreach ($suppliers as $supplier) {
    // إنشاء إشعار
    BookingNotification::create([
        'booking_id' => $this->id,
        'supplier_id' => $supplier->id,
    ]);
    
    // إرسال البريد الإلكتروني
    Mail::to($supplier->email)->send(
        new BookingNotificationMail($this, $supplier)
    );
}
```

### 5. قبول الحجز من المورد
```php
// في Booking::acceptBySupplier()
DB::transaction(function () use ($supplier) {
    // قفل الحجز لتجنب race conditions
    $booking = Booking::where('id', $this->id)->lockForUpdate()->first();
    
    // التحقق من الصلاحية
    if ($booking->isExpired() || $booking->supplier_id) {
        throw new Exception('...');
    }
    
    // قبول الحجز
    $booking->update([
        'supplier_id' => $supplier->id,
        'status' => 'confirmed',
        'accepted_at' => now(),
    ]);
    
    // تحديث إشعار المورد
    BookingNotification::where('booking_id', $booking->id)
        ->where('supplier_id', $supplier->id)
        ->update(['response' => 'accepted']);
    
    // إلغاء باقي الإشعارات
    BookingNotification::where('booking_id', $booking->id)
        ->where('supplier_id', '!=', $supplier->id)
        ->update(['response' => 'expired']);
    
    // إشعار العميل
    Mail::to($booking->user->email)->send(
        new BookingAcceptedBySupplierMail($booking)
    );
});
```

---

## 🎨 Email Templates

جميع قوالب البريد الإلكتروني تستخدم:
- **Theme:** Purple Gradient (#5B21B6 → #7C3AED → #A855F7)
- **Direction:** RTL (من اليمين لليسار)
- **Responsive Design:** متوافق مع الجوال
- **Animation:** تأثيرات حركية للعناصر المهمة

---

## 🔒 حماية Race Conditions

تم استخدام `lockForUpdate()` في دالة `acceptBySupplier()` لضمان:
1. عدم قبول نفس الحجز من موردين مختلفين في نفس الوقت
2. التحقق من الحالة داخل Transaction محمية
3. تحديث جميع الإشعارات ذرياً (atomically)

```php
DB::transaction(function () {
    $booking = Booking::where('id', $id)->lockForUpdate()->first();
    // ... باقي المنطق
});
```

---

## ⏰ انتهاء صلاحية الحجوزات

### Cron Job (اختياري)
يمكن إضافة cron job لإلغاء الحجوزات المنتهية:

```php
// في app/Console/Kernel.php
protected function schedule(Schedule $schedule)
{
    $schedule->call(function () {
        Booking::where('status', 'awaiting_supplier')
            ->where('expires_at', '<', now())
            ->whereNull('supplier_id')
            ->update(['status' => 'expired']);
            
        BookingNotification::whereHas('booking', function ($query) {
            $query->where('status', 'expired');
        })
        ->where('response', 'pending')
        ->update(['response' => 'expired', 'responded_at' => now()]);
    })->everyFiveMinutes();
}
```

---

## 🗑️ التغييرات - حذف Orders القديم

تم حذف النظام القديم:
- ❌ Routes: `admin.orders.*`
- ❌ Controller: `Admin/OrderController.php` (يمكن حذفه)
- ❌ Views: `resources/views/admin/orders/` (يمكن حذفها)

---

## 📊 الحالات (Statuses)

### Quote Statuses
- `pending` - قيد الانتظار
- `under_review` - قيد المراجعة
- `approved` - موافق عليه
- `rejected` - مرفوض
- `completed` - مكتمل
- `paid` - تم الدفع ✨ (جديد)

### Booking Statuses
- `pending` - قيد الانتظار
- `awaiting_supplier` - بانتظار قبول المورد ✨ (جديد)
- `confirmed` - مؤكد (تم قبوله من مورد)
- `cancelled` - ملغي
- `expired` - منتهي الصلاحية ✨ (جديد)
- `completed` - مكتمل

### Notification Response
- `pending` - بانتظار الرد
- `accepted` - تم القبول
- `rejected` - تم الرفض
- `expired` - منتهي (قبله مورد آخر)

---

## ✅ المزايا

1. **التنافس العادل:** First-Come-First-Served
2. **حماية من Race Conditions:** باستخدام Database Locking
3. **إشعارات فورية:** للموردين والعملاء
4. **تتبع كامل:** لكل إشعار ومشاهدة ورد
5. **مهلة زمنية:** 24 ساعة للتنافس
6. **إلغاء تلقائي:** للإشعارات غير المستخدمة
7. **تجربة مستخدم ممتازة:** تصميم بريد إلكتروني احترافي

---

## 🚀 الخطوات القادمة (اختيارية)

1. **Views للموردين:**
   - `supplier/bookings/index.blade.php`
   - `supplier/bookings/show.blade.php`

2. **Dashboard Widgets:**
   - عدد الحجوزات المتاحة للمورد
   - إحصائيات القبول/الرفض

3. **Real-time Notifications:**
   - Laravel Echo + Pusher
   - إشعارات فورية عند حجز جديد

4. **Admin Panel:**
   - إدارة الحجوزات التنافسية
   - إحصائيات المنافسة

---

## 📞 الدعم

للأسئلة أو المشاكل، يرجى فحص:
- Activity Logs في قاعدة البيانات
- Laravel Logs: `storage/logs/laravel.log`
- Email Logs في الجدول المناسب

---

تم بحمد الله! 🎉

# أمثلة الاستخدام - نظام الطلبات والموردين

## 🔧 أمثلة Artisan Commands

```bash
# فتح Tinker (Interactive Shell)
php artisan tinker
```

### مثال 1: إنشاء طلب بدون إرسال إيميل (للاختبار السريع)

```php
$order = App\Models\Order::create([
    'customer_id' => 1,
    'service_id' => 5,
    'category_id' => 2,
    'quantity' => 10,
    'price' => 5000.00,
    'customer_notes' => 'أريد الخدمة في الأسبوع القادم',
    'general_notes' => 'طلب من عميل جديد'
]);

echo $order->id;  // طباعة رقم الطلب
```

### مثال 2: إنشاء supplier_order_status يدويًا

```php
$order = App\Models\Order::find(1);
$supplierId = 3;

// إنشاء record للمورد
App\Models\SupplierOrderStatus::create([
    'order_id' => $order->id,
    'supplier_id' => $supplierId,
    'status' => 'pending',
]);

// عرض جميع الموردين للطلب
$order->supplierStatuses()->with('supplier')->get();
```

### مثال 3: محاكاة قبول الطلب

```php
$order = App\Models\Order::find(1);
$supplierId = 3;

// محاولة قبول الطلب
$success = $order->acceptBySupplier($supplierId);

if ($success) {
    echo "✓ تم قبول الطلب بنجاح";
} else {
    echo "✗ الطلب غير متاح أو مُسنَد بالفعل";
}

// عرض الطلب المُحدث
echo $order->fresh();
```

### مثال 4: محاكاة محاولة قبول نفس الطلب من مورد آخر

```php
$order = App\Models\Order::find(1);  // نفس الطلب السابق
$anotherSupplierId = 5;

// محاولة قبول الطلب من مورد آخر
$success = $order->acceptBySupplier($anotherSupplierId);

echo $success ? "✓ قبول" : "✗ الطلب مُسنَد بالفعل";
echo "المورد المُسنَد الحالي: " . $order->supplier_id;
```

### مثال 5: عرض جميع طلبات العميل

```php
$customerId = 1;
$orders = App\Models\Order::where('customer_id', $customerId)
    ->with(['service', 'category', 'supplier'])
    ->get();

foreach ($orders as $order) {
    echo "#{$order->id} - {$order->service->name} - {$order->status}\n";
}
```

### مثال 6: عرض الطلبات المسندة للمورد

```php
$supplierId = 3;
$orders = App\Models\Order::where('supplier_id', $supplierId)
    ->with(['customer', 'service'])
    ->get();

foreach ($orders as $order) {
    echo "#{$order->id} - {$order->customer->name} - {$order->service->name}\n";
}
```

### مثال 7: عرض الطلبات التي تم إرسالها للمورد (لم يرد عليها بعد)

```php
$supplierId = 3;
$pendingOrders = App\Models\SupplierOrderStatus::where('supplier_id', $supplierId)
    ->where('status', 'pending')
    ->with(['order.service', 'order.customer'])
    ->get();

foreach ($pendingOrders as $status) {
    echo "#{$status->order_id} - {$status->order->service->name}\n";
}
```

---

## 🌐 أمثلة API Requests

### باستخدام cURL

#### 1. إنشاء طلب

```bash
curl -X POST http://localhost:8000/api/orders \
  -H "Content-Type: application/json" \
  -d '{
    "customer_id": 1,
    "service_id": 5,
    "category_id": 2,
    "quantity": 10,
    "price": 5000.00,
    "customer_notes": "أريد الخدمة سريعة جداً",
    "general_notes": "عرس كبير"
  }'
```

**الاستجابة (201):**
```json
{
    "message": "تم إنشاء الطلب بنجاح",
    "order": {
        "id": 1,
        "customer_id": 1,
        "service_id": 5,
        "category_id": 2,
        "quantity": 10,
        "price": 5000,
        "customer_notes": "أريد الخدمة سريعة جداً",
        "general_notes": "عرس كبير",
        "status": "pending",
        "supplier_id": null,
        "assigned_at": null,
        "created_at": "2025-12-08T20:00:00.000000Z",
        "updated_at": "2025-12-08T20:00:00.000000Z"
    },
    "suppliers_count": 3
}
```

#### 2. قبول الطلب

```bash
curl -X GET "http://localhost:8000/api/orders/1/accept?supplier_id=3"
```

**الاستجابة (200) - نجاح:**
```json
{
    "message": "تم قبول الطلب بنجاح",
    "order": {
        "id": 1,
        "supplier_id": 3,
        "status": "assigned",
        "assigned_at": "2025-12-08T20:05:00.000000Z"
    },
    "status": "assigned"
}
```

**الاستجابة (409) - فشل (طلب مُسنَد):**
```json
{
    "message": "تم إسناد الطلب لمورد آخر بالفعل",
    "supplier_id": 3,
    "assigned_at": "2025-12-08T20:05:00.000000Z"
}
```

#### 3. عرض تفاصيل الطلب

```bash
curl -X GET http://localhost:8000/api/orders/1 \
  -H "Accept: application/json"
```

**الاستجابة:**
```json
{
    "id": 1,
    "customer_id": 1,
    "supplier_id": 3,
    "service_id": 5,
    "category_id": 2,
    "quantity": 10,
    "price": 5000,
    "customer_notes": "أريد الخدمة سريعة جداً",
    "general_notes": "عرس كبير",
    "status": "assigned",
    "assigned_at": "2025-12-08T20:05:00.000000Z",
    "created_at": "2025-12-08T20:00:00.000000Z",
    "updated_at": "2025-12-08T20:05:00.000000Z",
    "customer": {
        "id": 1,
        "name": "أحمد محمد",
        "email": "customer@example.com",
        "phone": "+966501234567"
    },
    "supplier": {
        "id": 3,
        "name": "شركة الأحلام",
        "email": "supplier@example.com",
        "phone": "+966509876543",
        "company_name": "أحلام للفعاليات"
    },
    "service": {
        "id": 5,
        "name": "ديكور الأعراس"
    },
    "category": {
        "id": 2,
        "name": "الزينة والديكور"
    },
    "supplierStatuses": [
        {
            "id": 1,
            "order_id": 1,
            "supplier_id": 3,
            "status": "accepted",
            "accepted_at": "2025-12-08T20:05:00.000000Z",
            "supplier": {
                "id": 3,
                "name": "شركة الأحلام"
            }
        },
        {
            "id": 2,
            "order_id": 1,
            "supplier_id": 5,
            "status": "pending",
            "accepted_at": null,
            "supplier": {
                "id": 5,
                "name": "شركة الأناقة"
            }
        }
    ]
}
```

#### 4. عرض جميع الطلبات

```bash
curl -X GET http://localhost:8000/api/orders \
  -H "Accept: application/json"
```

#### 5. تصفية الطلبات حسب الحالة

```bash
# جميع الطلبات المعلقة
curl -X GET http://localhost:8000/api/orders?status=pending

# جميع الطلبات المسندة
curl -X GET http://localhost:8000/api/orders?status=assigned

# طلبات عميل معين
curl -X GET http://localhost:8000/api/orders?customer_id=1

# طلبات مورد معين
curl -X GET http://localhost:8000/api/orders?supplier_id=3
```

---

## 🧪 سيناريو اختبار شامل

```bash
#!/bin/bash

# 1. إنشاء طلب
echo "1️⃣ إنشاء طلب جديد..."
ORDER_RESPONSE=$(curl -s -X POST http://localhost:8000/api/orders \
  -H "Content-Type: application/json" \
  -d '{
    "customer_id": 1,
    "service_id": 5,
    "category_id": 2,
    "quantity": 10,
    "price": 5000.00,
    "customer_notes": "طلب اختبار",
    "general_notes": "للتطوير فقط"
  }')

ORDER_ID=$(echo $ORDER_RESPONSE | grep -o '"id":[0-9]*' | head -1 | cut -d: -f2)
echo "رقم الطلب: $ORDER_ID"

sleep 2

# 2. عرض تفاصيل الطلب
echo ""
echo "2️⃣ عرض تفاصيل الطلب..."
curl -s -X GET http://localhost:8000/api/orders/$ORDER_ID | jq '.'

sleep 2

# 3. قبول الطلب من المورد الأول
echo ""
echo "3️⃣ قبول الطلب من المورد 3..."
curl -s -X GET http://localhost:8000/api/orders/$ORDER_ID/accept?supplier_id=3 | jq '.'

sleep 2

# 4. محاولة قبول نفس الطلب من مورد آخر
echo ""
echo "4️⃣ محاولة قبول الطلب من المورد 5 (يجب أن يفشل)..."
curl -s -X GET http://localhost:8000/api/orders/$ORDER_ID/accept?supplier_id=5 | jq '.'

sleep 2

# 5. عرض الطلب المُحدث
echo ""
echo "5️⃣ عرض الطلب المُحدث..."
curl -s -X GET http://localhost:8000/api/orders/$ORDER_ID | jq '.status, .supplier_id, .assigned_at'
```

---

## 📊 استعلامات قاعدة البيانات المفيدة

### SQL مباشر

```sql
-- 1. عرض جميع الطلبات مع تفاصيل العميل والمورد
SELECT 
    o.id,
    u1.name as customer_name,
    s.name as service_name,
    c.name as category_name,
    o.quantity,
    o.price,
    o.status,
    u2.name as supplier_name,
    o.assigned_at
FROM orders o
JOIN users u1 ON o.customer_id = u1.id
JOIN services s ON o.service_id = s.id
JOIN categories c ON o.category_id = c.id
LEFT JOIN users u2 ON o.supplier_id = u2.id
ORDER BY o.created_at DESC;

-- 2. عرض الطلبات المعلقة
SELECT * FROM orders WHERE status = 'pending';

-- 3. عرض الطلبات المسندة وحالة كل مورد
SELECT 
    o.id,
    o.supplier_id,
    u.name as supplier_name,
    sos.status,
    sos.accepted_at
FROM orders o
LEFT JOIN supplier_order_status sos ON o.id = sos.order_id
LEFT JOIN users u ON sos.supplier_id = u.id
WHERE o.id = 1;

-- 4. عرض الموردين الذين لم يردوا على الطلب بعد
SELECT DISTINCT u.id, u.name, u.email
FROM supplier_order_status sos
JOIN users u ON sos.supplier_id = u.id
WHERE sos.order_id = 1 AND sos.status = 'pending';

-- 5. إحصائيات الطلبات
SELECT 
    status,
    COUNT(*) as count,
    AVG(price) as avg_price,
    SUM(price) as total_price
FROM orders
GROUP BY status;

-- 6. أكثر الموردين استقبالاً للطلبات
SELECT 
    u.id,
    u.name,
    COUNT(o.id) as order_count
FROM orders o
JOIN users u ON o.supplier_id = u.id
GROUP BY u.id
ORDER BY order_count DESC;
```

---

## 🐛 استكشاف الأخطاء

### الطلب لا ينشئ بنجاح

```php
// تحقق من وجود الخدمة والفئة والعميل
User::find(1);  // العميل
Service::find(5);  // الخدمة
Category::find(2);  // الفئة

// تحقق من وجود SupplierService
SupplierService::where('service_id', 5)->where('category_id', 2)->count();
```

### الإيميل لا يرسل

```php
// تحقق من إعدادات الـ mail في .env
config('mail.from');  // يجب أن يكون موجود

// تحقق من سجلات الـ logs
tail -f storage/logs/laravel.log
```

### الطلب لا يُقبل

```php
// تحقق من حالة الطلب
Order::find(1)->status;  // يجب أن يكون 'pending'

// تحقق من وجود supplier_order_status
SupplierOrderStatus::where('order_id', 1)->where('supplier_id', 3)->first();
```

# نظام إدارة الطلبات والموردين - Your Events

## 📋 نظرة عامة

نظام متكامل يربط بين العملاء والموردين (Suppliers) يسمح للعملاء بإنشاء طلبات والموردين بقبول هذه الطلبات. النظام يضمن أن أول مورد يقبل الطلب هو من يحصل عليه.

---

## 🗄️ هيكل قاعدة البيانات

### 1. جدول `orders`
```sql
- id (Primary Key)
- customer_id (Foreign Key → users)
- supplier_id (Foreign Key → users, nullable)
- service_id (Foreign Key → services)
- category_id (Foreign Key → categories)
- quantity (integer)
- price (decimal)
- customer_notes (text, nullable)
- general_notes (text, nullable)
- status (enum: pending, assigned, completed, cancelled)
- assigned_at (timestamp, nullable)
- created_at, updated_at
```

### 2. جدول `supplier_order_status`
يتتبع حالة قبول كل مورد للطلب
```sql
- id (Primary Key)
- order_id (Foreign Key → orders)
- supplier_id (Foreign Key → users)
- status (enum: pending, accepted, rejected)
- accepted_at (timestamp, nullable)
- created_at, updated_at
- UNIQUE (order_id, supplier_id)
```

---

## 🔄 الموديلات (Models)

### Order Model
```php
// العلاقات
- customer() → User (العميل)
- supplier() → User (المورد المُسنَد)
- service() → Service
- category() → Category
- supplierStatuses() → SupplierOrderStatus (قوائم الموردين)

// الدوال المساعدة
- isAvailable() → bool (التحقق من توفر الطلب)
- acceptBySupplier(supplierId) → bool (قبول الطلب من مورد)
```

### SupplierOrderStatus Model
```php
- order() → Order
- supplier() → User
```

---

## 🔌 API Endpoints

### 1. إنشاء طلب جديد
```
POST /api/orders
Content-Type: application/json

{
    "customer_id": 1,
    "service_id": 5,
    "category_id": 2,
    "quantity": 10,
    "price": 5000.00,
    "customer_notes": "ملاحظات من العميل",
    "general_notes": "ملاحظات عامة"
}

Response (201):
{
    "message": "تم إنشاء الطلب بنجاح",
    "order": {...},
    "suppliers_count": 3
}
```

**العملية:**
1. يتم إنشاء record في جدول `orders`
2. البحث عن جميع الموردين الذين يقدمون نفس الخدمة والفئة
3. إنشاء record لكل مورد في جدول `supplier_order_status`
4. إرسال إيميل لكل مورد بزر "قبول عرض السعر"

### 2. قبول الطلب (Accept Order)
```
GET /api/orders/{order_id}/accept?supplier_id=XXX

Response (200):
{
    "message": "تم قبول الطلب بنجاح",
    "order": {...},
    "status": "assigned"
}

Response (409):
{
    "message": "تم إسناد الطلب لمورد آخر بالفعل",
    "supplier_id": 5,
    "assigned_at": "2025-12-08 19:50:00"
}
```

**العملية:**
1. التحقق من أن الطلب حالة `pending`
2. إذا كان متاح:
   - تحديث `orders.status` إلى `assigned`
   - تحديث `orders.supplier_id` بـ المورد
   - تحديث `orders.assigned_at` بالوقت الحالي
   - تحديث سجل المورد في `supplier_order_status` إلى `accepted`
3. إذا كان مُسنَد مسبقاً: إرجاع خطأ 409

### 3. عرض تفاصيل الطلب
```
GET /api/orders/{id}

Response:
{
    "id": 1,
    "customer": {...},
    "supplier": {...},
    "service": {...},
    "category": {...},
    "quantity": 10,
    "price": 5000.00,
    "status": "assigned",
    "supplierStatuses": [
        {
            "supplier": {...},
            "status": "accepted",
            "accepted_at": "2025-12-08 19:50:00"
        }
    ]
}
```

### 4. عرض جميع الطلبات
```
GET /api/orders
GET /api/orders?status=pending
GET /api/orders?customer_id=1
GET /api/orders?supplier_id=5

Response:
{
    "data": [...],
    "links": {...},
    "meta": {...}
}
```

---

## 📧 نموذج الإيميل

**المسار:** `resources/views/emails/order-request.blade.php`

يتضمن الإيميل:
- رقم الطلب والخدمة والفئة
- الكمية والسعر
- ملاحظات العميل والملاحظات العامة
- **زر "قبول عرض السعر"** يؤدي إلى:
  ```
  /api/orders/{order_id}/accept?supplier_id={supplier_id}
  ```

**مثال على الإيميل:**
```
من: Your Events <noreply@your-events.com>
إلى: supplier@example.com
الموضوع: طلب جديد - اسم الخدمة

---
السلام عليكم ورحمة الله وبركاته [اسم المورد],

تم تلقي طلب جديد يتطابق مع الخدمات التي تقدمها:

📋 تفاصيل الطلب:
- رقم الطلب: #123
- الخدمة: اسم الخدمة
- الفئة: اسم الفئة
- الكمية: 10
- السعر: 5000.00 ريال

[زر: ✓ قبول عرض السعر]

ملاحظة: هذا الطلب متاح للموردين الآخرين أيضاً. أول مورد يقبل العرض سيحصل على الطلب.
```

---

## 🎛️ لوحة التحكم (Admin Dashboard)

### مسار الوصول
`/admin/orders`

### الميزات

#### 1. قائمة الطلبات (Index)
```
Route: GET /admin/orders
Controller: Admin/OrderController@index
View: admin/orders/index.blade.php
```

**الفلاتر:**
- 🔍 البحث (اسم الخدمة أو العميل)
- 📊 حالة الطلب (معلق، تم الإسناد، مكتمل، ملغي)

**الجدول يعرض:**
| العمود | الوصف |
|--------|--------|
| # | رقم الطلب |
| اسم الخدمة | اسم الخدمة والفئة |
| الكمية | عدد الوحدات |
| السعر | سعر الطلب |
| ملاحظة خاصة | ملاحظات من العميل |
| ملاحظة عامة | ملاحظات عامة |
| اسم المورد | اسم المورد المُسنَد |
| الحالة | معلق/تم الإسناد/مكتمل/ملغي |
| الإجراءات | عرض التفاصيل |

**شارات الحالة:**
- ⏳ معلق (أصفر)
- ✓ تم الإسناد (أخضر)
- ✅ مكتمل (أزرق)
- ❌ ملغي (أحمر)

#### 2. تفاصيل الطلب (Show)
```
Route: GET /admin/orders/{id}
Controller: Admin/OrderController@show
View: admin/orders/show.blade.php
```

**الأقسام:**

**الجانب الأيسر:**
1. **معلومات الطلب**
   - الخدمة والفئة
   - الكمية والسعر
   - تاريخ الإنشاء والتحديث

2. **ملاحظات العميل** (إن وجدت)

3. **ملاحظات عامة**
   - حقل تحرير لإضافة ملاحظات جديدة
   - زر حفظ

4. **حالة الموردين**
   - جدول بجميع الموردين الذين تم إرسال الطلب لهم
   - حالة كل مورد (قيد الانتظار/مقبول/مرفوض)
   - وقت القبول

**الجانب الأيمن:**
1. **معلومات العميل**
   - الاسم والبريد والهاتف

2. **معلومات المورد**
   - الاسم والبريد والهاتف والشركة (إن وجدت)

3. **تحديث الحالة**
   - قائمة منسدلة لاختيار الحالة الجديدة
   - زر تحديث

4. **الإجراءات**
   - زر حذف الطلب (مع تأكيد)

---

## 🔐 التحكم بالوصول

- **API الطلبات:** متاح بدون مصادقة (يمكن إضافة Middleware)
- **لوحة التحكم:** متاح فقط للمسؤولين (Admin Middleware)

---

## 🔄 تدفق العملية

```
1. العميل ينشئ طلب
   └─> POST /api/orders

2. النظام يبحث عن الموردين المطابقين
   └─> SupplierService::where(['service_id', 'category_id'])

3. إنشاء records في supplier_order_status لكل مورد
   └─> status = 'pending'

4. إرسال إيميل لجميع الموردين
   └─> Mail::send('emails.order-request')
   └─> يتضمن رابط: /api/orders/{id}/accept?supplier_id={id}

5. المورد يضغط على "قبول عرض السعر"
   └─> GET /api/orders/{id}/accept?supplier_id={id}

6. التحقق من توفر الطلب
   ├─> إذا متاح (status = pending)
   │   ├─> تحديث orders.status = 'assigned'
   │   ├─> تحديث orders.supplier_id
   │   ├─> تحديث supplier_order_status.status = 'accepted'
   │   └─> Response 200 "تم قبول الطلب بنجاح"
   │
   └─> إذا مُسنَد مسبقاً
       └─> Response 409 "تم إسناد الطلب لمورد آخر"

7. الإدارة تتابع الطلب في لوحة التحكم
   └─> GET /admin/orders
   └─> GET /admin/orders/{id}
   └─> PUT /admin/orders/{id} (تحديث ملاحظات وحالة)
```

---

## 📝 أمثلة الاستخدام

### مثال 1: إنشاء طلب
```bash
curl -X POST http://your-domain.com/api/orders \
  -H "Content-Type: application/json" \
  -d '{
    "customer_id": 1,
    "service_id": 5,
    "category_id": 2,
    "quantity": 10,
    "price": 5000.00,
    "customer_notes": "أريد خدمة سريعة",
    "general_notes": "موضوع مهم"
  }'
```

### مثال 2: قبول الطلب
```bash
curl -X GET "http://your-domain.com/api/orders/1/accept?supplier_id=3"
```

### مثال 3: عرض تفاصيل الطلب
```bash
curl -X GET http://your-domain.com/api/orders/1
```

---

## 🛠️ الملفات المُنشأة/المحدثة

### Models
- `/app/Models/Order.php` ✅ جديد
- `/app/Models/SupplierOrderStatus.php` ✅ جديد

### Controllers
- `/app/Http/Controllers/Api/OrderController.php` ✅ جديد
- `/app/Http/Controllers/Admin/OrderController.php` ✅ جديد

### Migrations
- `/database/migrations/2025_12_08_194906_create_orders_table.php` ✅
- `/database/migrations/2025_12_08_194926_create_supplier_order_status_table.php` ✅

### Views
- `/resources/views/admin/orders/index.blade.php` ✅
- `/resources/views/admin/orders/show.blade.php` ✅
- `/resources/views/emails/order-request.blade.php` ✅

### Routes
- `/routes/api.php` ✅ تم إضافة endpoints
- `/routes/web.php` ✅ تم إضافة admin routes

---

## ⚙️ التثبيت والتشغيل

```bash
# 1. تشغيل الـ migrations
php artisan migrate

# 2. مسح الذاكرة المؤقتة
php artisan cache:clear && php artisan view:clear

# 3. زيارة لوحة التحكم
https://your-domain.com/admin/orders
```

---

## 🔍 ملاحظات مهمة

1. **ضمان الحصول لأول مورد:** يتم استخدام transaction لضمان atomic operation عند قبول الطلب
2. **لغة العربية:** جميع الواجهات والرسائل بالعربية
3. **الإيميل:** يحتوي على رابط مباشر للقبول
4. **الأمان:** يجب إضافة validation إضافية وحماية CSRF
5. **Logging:** يفضل إضافة event logging لتتبع جميع الإجراءات

---

## 🚀 التحسينات المستقبلية

- [ ] إضافة real-time notifications
- [ ] dashboard للموردين يعرض الطلبات المسندة
- [ ] تنبيهات تلقائية للطلبات المنتهية الصلاحية
- [ ] نظام تقييم الموردين
- [ ] تقارير وإحصائيات متقدمة
- [ ] نظام دفع متكامل

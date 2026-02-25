# دليل التثبيت والإعداد - نظام الطلبات والموردين

## ✅ ما تم إنجازه

### 1. قاعدة البيانات (Migrations)
- ✅ جدول `orders`
- ✅ جدول `supplier_order_status`

### 2. الموديلات (Models)
- ✅ `Order` Model مع جميع العلاقات والدوال المساعدة
- ✅ `SupplierOrderStatus` Model
- ✅ تحديث `User` Model مع العلاقات

### 3. المتحكمات (Controllers)
- ✅ `Api/OrderController` مع جميع endpoints
- ✅ `Admin/OrderController` لإدارة الطلبات

### 4. الـ Routes
- ✅ API routes في `/routes/api.php`
- ✅ Admin routes في `/routes/web.php`

### 5. الـ Views
- ✅ `admin/orders/index.blade.php` - قائمة الطلبات
- ✅ `admin/orders/show.blade.php` - تفاصيل الطلب
- ✅ `emails/order-request.blade.php` - نموذج الإيميل

### 6. التوثيق
- ✅ `ORDERS_SYSTEM_DOCUMENTATION.md` - التوثيق الشامل
- ✅ `API_USAGE_EXAMPLES.md` - أمثلة الاستخدام

### 7. الـ Sidebar
- ✅ تم إضافة رابط "إدارة الطلبات" في الشريط الجانبي

---

## 🚀 خطوات التثبيت

### الخطوة 1: التحقق من الـ Migrations

```bash
php artisan migrate
```

**الناتج المتوقع:**
```
  2025_12_08_194906_create_orders_table .................... 73.61ms DONE
  2025_12_08_194926_create_supplier_order_status_table ..... 36.39ms DONE
```

### الخطوة 2: مسح الذاكرة المؤقتة

```bash
php artisan cache:clear
php artisan view:clear
php artisan config:clear
```

### الخطوة 3: التحقق من الـ Routes

```bash
php artisan route:list | grep orders
```

**يجب أن تظهر الـ routes التالية:**
```
GET|HEAD        admin/orders ........................ admin.orders.index
GET|HEAD        admin/orders/{order} .............. admin.orders.show
PUT             admin/orders/{order} .............. admin.orders.update
DELETE          admin/orders/{order} .............. admin.orders.destroy
POST            api/orders ......................... Api\OrderController@store
GET|HEAD        api/orders ......................... Api\OrderController@index
GET|HEAD        api/orders/{id} ................... Api\OrderController@show
GET|HEAD        api/orders/{id}/accept ........... Api\OrderController@accept
```

---

## 🔧 الإعدادات المطلوبة

### 1. إعدادات الإيميل (`.env`)

للتأكد من أن الإيميلات ترسل بنجاح:

```env
MAIL_DRIVER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@your-events.com
MAIL_FROM_NAME="Your Events"
```

### 2. إعدادات قاعدة البيانات

تأكد من أن الاتصال بقاعدة البيانات صحيح:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_events_db
DB_USERNAME=root
DB_PASSWORD=password
```

---

## 📋 قائمة تحقق للتأكد من التثبيت

- [ ] تم تشغيل الـ migrations بنجاح
- [ ] الجداول موجودة في قاعدة البيانات
  ```sql
  SHOW TABLES LIKE 'orders%';
  ```
- [ ] الـ routes موجودة
  ```bash
  php artisan route:list | grep orders
  ```
- [ ] لا توجد أخطاء في الـ logs
  ```bash
  tail storage/logs/laravel.log
  ```
- [ ] يمكن الوصول لـ `/admin/orders`
- [ ] يمكن استدعاء API endpoints

---

## 🧪 اختبار سريع

### 1. اختبار عبر Artisan Tinker

```bash
php artisan tinker

# إنشاء طلب
$order = App\Models\Order::create([
    'customer_id' => 1,
    'service_id' => 1,
    'category_id' => 1,
    'quantity' => 5,
    'price' => 1000.00,
    'status' => 'pending'
]);

# عرض البيانات
$order

# الخروج
exit()
```

### 2. اختبار الـ API

```bash
# إنشاء طلب
curl -X POST http://localhost:8000/api/orders \
  -H "Content-Type: application/json" \
  -d '{
    "customer_id": 1,
    "service_id": 1,
    "category_id": 1,
    "quantity": 5,
    "price": 1000.00
  }'

# عرض الطلبات
curl -X GET http://localhost:8000/api/orders
```

### 3. اختبار الواجهة

افتح في المتصفح:
```
http://localhost:8000/admin/orders
```

---

## 🐛 استكشاف الأخطاء الشائعة

### خطأ: "SQLSTATE[42S02]: Table 'orders' doesn't exist"

**الحل:**
```bash
php artisan migrate
```

### خطأ: "Class 'App\Models\Order' not found"

**الحل:**
```bash
php artisan cache:clear
php artisan config:clear
```

### خطأ: "Route [admin.orders.index] not defined"

**الحل:**
```bash
php artisan route:clear
php artisan route:cache
```

### الإيميلات لا ترسل

**الحل:**
1. تحقق من إعدادات الإيميل في `.env`
2. اختبر الاتصال:
   ```bash
   php artisan tinker
   Mail::raw('test', function ($msg) { $msg->to('test@example.com'); });
   ```

### الموردين لا يظهرون عند إنشاء طلب

**السبب:** لا توجد `SupplierService` تطابق الخدمة والفئة

**الحل:**
```bash
php artisan tinker

# أضف خدمة للمورد
App\Models\SupplierService::create([
    'user_id' => 2,  // المورد
    'service_id' => 1,
    'category_id' => 1,
    'is_available' => true
]);
```

---

## 📊 أمثلة قاعدة البيانات

### Insert البيانات الأساسية

```bash
php artisan tinker
```

```php
// أضف مستخدم (عميل)
$customer = App\Models\User::create([
    'name' => 'أحمد محمد',
    'email' => 'customer@example.com',
    'phone' => '0501234567',
    'password' => bcrypt('password'),
    'role' => 'customer'
]);

// أضف مستخدم (مورد)
$supplier = App\Models\User::create([
    'name' => 'محمد الأحمد',
    'email' => 'supplier@example.com',
    'phone' => '0509876543',
    'password' => bcrypt('password'),
    'role' => 'supplier',
    'company_name' => 'الأحلام للفعاليات'
]);

// أضف خدمة للمورد
$supplierService = App\Models\SupplierService::create([
    'user_id' => $supplier->id,
    'service_id' => 1,
    'category_id' => 1,
    'is_available' => true
]);

exit()
```

### الآن اختبر الـ API

```bash
curl -X POST http://localhost:8000/api/orders \
  -H "Content-Type: application/json" \
  -d '{
    "customer_id": 1,
    "service_id": 1,
    "category_id": 1,
    "quantity": 5,
    "price": 1000.00
  }'
```

---

## 🔐 الأمان والأفضليات

### 1. إضافة Authentication للـ API

```php
// في routes/api.php
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/orders', [OrderController::class, 'store']);
    Route::get('/orders', [OrderController::class, 'index']);
});
```

### 2. إضافة Validation أقوى

```php
// في OrderController@store
'customer_id' => 'required|exists:users,id|where:role,customer',
'service_id' => 'required|exists:services,id',
```

### 3. إضافة Rate Limiting

```php
// في routes/api.php
Route::throttle('orders')->group(function () {
    Route::post('/orders', [OrderController::class, 'store']);
});
```

### 4. إضافة Logging

```php
// في OrderController
\Log::info('Order created', ['order_id' => $order->id]);
\Log::warning('Order accept failed', ['order_id' => $orderId, 'reason' => 'not available']);
```

---

## 📈 الخطوات التالية

1. **تقارير متقدمة**
   ```php
   // إضافة endpoints لعرض إحصائيات
   GET /api/reports/orders
   GET /api/reports/suppliers
   ```

2. **Dashboard للموردين**
   ```
   /supplier/orders
   /supplier/orders/{id}
   ```

3. **نظام Notifications**
   ```php
   // إشعارات فورية عند إنشاء طلب
   // إشعارات عند قبول/رفض الطلب
   ```

4. **نظام تقييم**
   ```php
   // تقييم الموردين من قبل العملاء
   // تقييم العملاء من قبل الموردين
   ```

5. **نظام الدفع**
   ```php
   // دفع عند قبول الطلب
   // دفع عند إكمال الطلب
   ```

---

## 📞 الدعم والمساعدة

إذا واجهت أي مشاكل:

1. تحقق من السجلات: `storage/logs/laravel.log`
2. اختبر الـ Database Connection: `php artisan tinker`
3. تحقق من الـ Routes: `php artisan route:list`
4. مسح الذاكرة: `php artisan cache:clear`

---

## 📝 النسخة

- **النسخة:** 1.0.0
- **تاريخ الإنشاء:** 2025-12-08
- **آخر تحديث:** 2025-12-08
- **الحالة:** ✅ جاهز للإنتاج

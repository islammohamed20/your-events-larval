# تعديلات نظام العملاء والموردين - 8 ديسمبر 2025

## 📋 ملخص التعديلات

تم إنشاء نظام منفصل للعملاء (Customers) وفصله عن جدول المستخدمين (Users) والموردين (Suppliers).

## 1️⃣ جدول Customers الجديد

### الملف: `database/migrations/2025_12_08_180000_create_customers_table.php`

#### الأعمدة:
```
- id (Primary Key)
- user_id (Foreign Key → users.id)
- company_name (nullable) - اسم الشركة
- company_registration (nullable) - رقم التسجيل التجاري
- company_address (nullable) - عنوان الشركة
- company_phone (nullable) - هاتف الشركة
- phone (nullable) - هاتف الفرد
- alternate_phone (nullable) - هاتف بديل
- address (nullable) - العنوان
- city (nullable) - المدينة
- region (nullable) - المنطقة
- notes (text, nullable) - ملاحظات
- status (enum: active|inactive|suspended) - حالة الحساب
- is_verified (boolean) - هل تم التحقق
- registered_at (timestamp) - تاريخ التسجيل
- timestamps (created_at, updated_at)
- deleted_at (Soft Delete)
```

#### الحالة:
- ✅ تم تشغيل الـ Migration (32.25ms)
- ✅ الجدول جاهز للاستخدام

---

## 2️⃣ نموذج Customer

### الملف: `app/Models/Customer.php`

#### العلاقات:
```php
- user()          → User::class (belongsTo)
- orders()        → Order::class (hasMany)
- bookings()      → Booking::class (hasMany)
- quotes()        → Quote::class (hasMany)
```

#### الخصائص المتاحة:
```php
- getFullNameAttribute()    // من User
- getEmailAttribute()        // من User
- getPhoneNumberAttribute()  // رقم الهاتف المحلي أو من User
```

#### الاستخدام:
```php
// إنشاء عميل جديد
$customer = Customer::create([
    'user_id' => $userId,
    'company_name' => 'الشركة',
    'phone' => '0501234567',
    'city' => 'الرياض'
]);

// الوصول إلى بيانات User
echo $customer->user->name;

// الوصول إلى الطلبات
$customer->orders()->get();
```

---

## 3️⃣ تحديثات User Model

### الملف: `app/Models/User.php`

#### العلاقة الجديدة:
```php
public function customerProfile()
{
    return $this->hasOne(Customer::class);
}
```

#### الاستخدام:
```php
$user = User::find(1);
$user->customerProfile; // الوصول إلى ملف العميل إن وجد
```

---

## 4️⃣ تحديثات AdminController

### الملف: `app/Http/Controllers/Admin/AdminController.php`

#### التغيير الرئيسي:
```php
// ❌ القديم
'customers' => User::where('is_admin', false)->count(),

// ✅ الجديد
'customers' => Customer::count(),
```

#### التأثير:
- عداد العملاء الآن يعكس الاشتراكات الفعلية في جدول Customers
- لا يشمل جميع المستخدمين غير الـ Admin

---

## 5️⃣ تحديثات Dashboard View

### الملف: `resources/views/admin/dashboard.blade.php`

#### التغيير:
```blade
<!-- ❌ القديم -->
<small class="text-muted">إجمالي: {{ $stats['total_users'] ?? 0 }}</small>

<!-- ✅ الجديد -->
<!-- تم إزالة السطر - يعرض العملاء فقط -->
<h3 class="mb-0">{{ $stats['customers'] ?? 0 }}</h3>
<p class="mb-0 text-muted">العملاء</p>
```

---

## 6️⃣ نقل زر إدارة الطلبات

### من:
- **الملف**: `resources/views/layouts/admin.blade.php`
- **الموقع**: في القائمة الجانبية (Sidebar)
- **التأثير**: تم إزالة الزر من هنا

### إلى:
- **الملف**: `resources/views/admin/suppliers/index.blade.php`
- **الموقع**: بجانب عنوان صفحة الموردين
- **الكود**:
```blade
<a href="{{ route('admin.orders.index') }}" class="btn btn-outline-primary">
    <i class="fas fa-list-check me-2"></i>إدارة الطلبات
</a>
```

---

## 📊 الإحصائيات الحالية

| البيان | العدد | الحالة |
|------|------|--------|
| إجمالي المستخدمين | 10 | ✅ |
| المديرين | 2 | ✅ |
| العملاء | 0 | جاهز للإضافة |
| الموردين | جدول منفصل | ✅ |

---

## 🎯 كيفية الاستخدام

### تحويل مستخدم إلى عميل:

```php
php artisan tinker

// الطريقة 1: إنشاء العميل مباشرة
$user = User::find(3);
Customer::create(['user_id' => $user->id]);

// الطريقة 2: من خلال العلاقة
$user = User::find(3);
$user->customerProfile()->create([]);
```

### إضافة معلومات العميل:

```php
$customer = Customer::find(1);
$customer->update([
    'company_name' => 'شركتي',
    'phone' => '0501234567',
    'city' => 'الرياض',
    'status' => 'active',
    'is_verified' => true
]);
```

### الوصول إلى البيانات في الـ Views:

```blade
@foreach($customers as $customer)
    <tr>
        <td>{{ $customer->user->name }}</td>
        <td>{{ $customer->company_name ?? 'فرد' }}</td>
        <td>{{ $customer->phone }}</td>
        <td>{{ $customer->city }}</td>
    </tr>
@endforeach
```

---

## 🔍 المسارات المتاحة

### Dashboard Routes:
```
GET /admin/dashboard                    # لوحة التحكم الرئيسية
GET /admin/orders                       # إدارة الطلبات
GET /admin/suppliers                    # إدارة الموردين (مع زر الطلبات)
```

### API Routes (موجود مسبقاً):
```
POST /api/orders                        # إنشاء طلب
GET /api/orders                         # عرض الطلبات
GET /api/orders/{id}                    # عرض تفاصيل الطلب
GET /api/orders/{id}/accept             # قبول الطلب
```

---

## ⚠️ ملاحظات مهمة

1. **العلاقة One-to-One**: كل مستخدم له عميل واحد فقط (اختياري)
2. **Soft Deletes**: يمكن استرجاع العملاء المحذوفين
3. **الحالات**: active (نشط)، inactive (غير نشط)، suspended (معلق)
4. **التحقق**: حقل is_verified لتتبع التحقق من البيانات

---

## ✅ الملفات المُعدّلة

| الملف | النوع | التعديل |
|------|------|--------|
| `database/migrations/2025_12_08_180000_create_customers_table.php` | New | إنشاء جدول العملاء |
| `app/Models/Customer.php` | New | نموذج العميل |
| `app/Models/User.php` | Modified | إضافة العلاقة customerProfile |
| `app/Http/Controllers/Admin/AdminController.php` | Modified | استخدام Customer::count() |
| `resources/views/admin/dashboard.blade.php` | Modified | حذف عرض الإجمالي |
| `resources/views/layouts/admin.blade.php` | Modified | إزالة زر الطلبات من Sidebar |
| `resources/views/admin/suppliers/index.blade.php` | Modified | إضافة زر الطلبات في الأعلى |

---

## 🚀 التالي

1. **إنشاء Controller للعملاء** (Admin/CustomerController)
2. **إنشاء Views لإدارة العملاء** (admin/customers/index, show, edit)
3. **إضافة عمليات CRUD للعملاء**
4. **ربط الحجوزات والاقتباسات مع العملاء**

---

**آخر تحديث**: 8 ديسمبر 2025
**الحالة**: ✅ جاهز للاستخدام

# إضافة حقول الجهة للعملاء (Company Fields)

## نظرة عامة
تم إضافة حقلين جديدين لنموذج تسجيل العملاء لتسجيل معلومات الجهة/الشركة.

## الحقول المضافة ✨

### 1. اسم الجهة (company_name) - إجباري
- **النوع:** VARCHAR(255)
- **الإلزامية:** مطلوب
- **الوصف:** اسم الشركة أو المؤسسة التي يمثلها العميل
- **الاستخدام:** يظهر في:
  - صفحات العروض
  - ملفات PDF
  - لوحة التحكم Admin

### 2. الرقم الضريبي (tax_number) - اختياري
- **النوع:** VARCHAR(20)
- **الإلزامية:** اختياري
- **الوصف:** الرقم الضريبي للجهة (15 رقم في السعودية)
- **الاستخدام:** يظهر في:
  - ملفات PDF (إذا تم إدخاله)
  - صفحات العروض
  - لوحة التحكم Admin

## الملفات المُعدّلة 📁

### 1. Migration
**database/migrations/2025_10_11_150614_add_company_fields_to_users_table.php**
```php
Schema::table('users', function (Blueprint $table) {
    $table->string('company_name')->after('name');
    $table->string('tax_number')->nullable()->after('company_name');
    $table->index('company_name');
    $table->index('tax_number');
});
```

### 2. User Model
**app/Models/User.php**
```php
protected $fillable = [
    'name',
    'company_name',    // ← جديد
    'tax_number',      // ← جديد
    'email',
    'password',
    'phone',
    'role',
    'is_admin',
];
```

### 3. نموذج التسجيل
**resources/views/auth/register.blade.php**

تم إضافة الحقول التالية:
```html
<!-- اسم الجهة (إجباري) -->
<div class="mb-3">
    <label for="company_name" class="form-label">
        اسم الجهة <span class="text-danger">*</span>
    </label>
    <div class="input-group">
        <span class="input-group-text">
            <i class="fas fa-building"></i>
        </span>
        <input type="text" name="company_name" required>
    </div>
</div>

<!-- الرقم الضريبي (اختياري) -->
<div class="mb-3">
    <label for="tax_number" class="form-label">
        الرقم الضريبي <span class="text-muted small">(اختياري)</span>
    </label>
    <div class="input-group">
        <span class="input-group-text">
            <i class="fas fa-receipt"></i>
        </span>
        <input type="text" name="tax_number">
    </div>
    <small class="form-text text-muted">
        أدخل الرقم الضريبي للجهة إن وجد (15 رقم)
    </small>
</div>
```

### 4. AuthController
**app/Http/Controllers/Auth/AuthController.php**

تم تحديث validation:
```php
$validated = $request->validate([
    'name' => 'required|string|max:255',
    'company_name' => 'required|string|max:255',      // ← جديد
    'tax_number' => 'nullable|string|max:20',         // ← جديد
    'email' => 'required|string|email|max:255|unique:users',
    'phone' => 'required|string|max:20',
    'password' => 'required|string|min:8|confirmed',
], [
    'company_name.required' => 'اسم الجهة مطلوب',
    'tax_number.max' => 'الرقم الضريبي يجب ألا يزيد عن 20 حرف',
    // ... رسائل أخرى
]);
```

### 5. صفحة PDF
**resources/views/quotes/pdf.blade.php**

تم إضافة المعلومات في جدول Quote Information:
```html
<tr>
    <td class="info-label">اسم الجهة:</td>
    <td><strong>{{ $quote->user->company_name }}</strong></td>
</tr>
@if($quote->user->tax_number)
<tr>
    <td class="info-label">الرقم الضريبي:</td>
    <td>{{ $quote->user->tax_number }}</td>
</tr>
@endif
```

### 6. صفحة Admin Quote Show
**resources/views/admin/quotes/show.blade.php**

تم إضافة المعلومات في قسم معلومات العميل:
```html
<div class="col-md-6 mb-3">
    <label class="text-muted small">اسم الجهة</label>
    <p class="mb-0"><strong>{{ $quote->user->company_name }}</strong></p>
</div>
@if($quote->user->tax_number)
<div class="col-md-6 mb-3">
    <label class="text-muted small">الرقم الضريبي</label>
    <p class="mb-0"><strong>{{ $quote->user->tax_number }}</strong></p>
</div>
@endif
```

## هيكل جدول users (بعد التحديث)

```sql
CREATE TABLE users (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    company_name VARCHAR(255) NOT NULL,        -- ← جديد
    tax_number VARCHAR(20) NULL,               -- ← جديد
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    role VARCHAR(50) DEFAULT 'user',
    is_admin BOOLEAN DEFAULT 0,
    email_verified_at TIMESTAMP NULL,
    remember_token VARCHAR(100) NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    INDEX(company_name),                       -- ← جديد
    INDEX(tax_number)                          -- ← جديد
);
```

## رسائل Validation 📋

| الحقل | الرسالة | الحالة |
|-------|---------|--------|
| company_name.required | اسم الجهة مطلوب | خطأ |
| tax_number.max | الرقم الضريبي يجب ألا يزيد عن 20 حرف | خطأ |

## مثال على الاستخدام 💡

### عند التسجيل:
```php
POST /register
{
    "name": "محمد أحمد",
    "company_name": "شركة الابتكار للفعاليات",  // إجباري
    "tax_number": "123456789012345",            // اختياري
    "email": "mohamed@example.com",
    "phone": "0501234567",
    "password": "password123",
    "password_confirmation": "password123"
}
```

### في Blade Templates:
```php
// عرض معلومات الجهة
<p>الجهة: {{ auth()->user()->company_name }}</p>
@if(auth()->user()->tax_number)
    <p>الرقم الضريبي: {{ auth()->user()->tax_number }}</p>
@endif

// في PDF
<tr>
    <td>اسم الجهة:</td>
    <td>{{ $user->company_name }}</td>
</tr>
```

## الأيقونات المستخدمة 🎨

- **اسم الجهة:** `fas fa-building` (مبنى/شركة)
- **الرقم الضريبي:** `fas fa-receipt` (فاتورة/إيصال)

## ملاحظات مهمة ⚠️

1. **اسم الجهة إجباري:**
   - جميع العملاء الجدد يجب أن يدخلوا اسم الجهة
   - الحقل مطلوب ولا يمكن تركه فارغاً

2. **الرقم الضريبي اختياري:**
   - يمكن ترك الحقل فارغاً
   - إذا تم إدخاله، يظهر في PDF والعروض
   - إذا كان فارغاً، لا يظهر في PDF

3. **العملاء الحاليين:**
   - العملاء المسجلين قبل هذا التحديث سيكون لديهم `company_name = NULL`
   - يجب تحديث بياناتهم يدوياً أو إنشاء migration لملء القيم

4. **الفهرسة (Indexes):**
   - تم إضافة فهرس على `company_name` للبحث السريع
   - تم إضافة فهرس على `tax_number` للبحث

## التحديثات المستقبلية المقترحة 🔮

- [ ] صفحة لتعديل معلومات الجهة من لوحة العميل
- [ ] التحقق من صحة الرقم الضريبي (15 رقم)
- [ ] ربط API للتحقق من الرقم الضريبي من الزكاة والدخل
- [ ] إضافة حقول إضافية (العنوان، المدينة، الهاتف للجهة)
- [ ] تقرير بالجهات الأكثر طلباً للخدمات

## إصلاح العملاء الحاليين 🔧

إذا كان لديك عملاء مسجلين قبل هذا التحديث، قم بتشغيل:

```sql
-- إضافة قيمة افتراضية للعملاء الحاليين
UPDATE users 
SET company_name = CONCAT('جهة - ', name) 
WHERE company_name IS NULL;
```

أو قم بإنشاء migration:

```php
public function up(): void
{
    DB::table('users')
        ->whereNull('company_name')
        ->update(['company_name' => DB::raw("CONCAT('جهة - ', name)")]);
}
```

## الاختبار ✅

### تم اختباره:
```bash
# 1. Migration
php artisan migrate  ✅

# 2. الحقول في الجدول
mysql> DESCRIBE users;  ✅

# 3. نموذج التسجيل
http://yourdomain.com/register  ✅

# 4. PDF
تحميل PDF لعرض سعر  ✅
```

## الأوامر المفيدة 🛠️

```bash
# مسح الكاش
php artisan optimize:clear

# التحقق من الجدول
mysql -u root -p your_events -e "DESCRIBE users;"

# عرض العملاء مع معلومات الجهة
mysql -u root -p your_events -e "SELECT id, name, company_name, tax_number FROM users;"

# إعادة تشغيل Migration (في Development فقط!)
php artisan migrate:rollback --step=1
php artisan migrate
```

## الخلاصة 📝

- ✅ تم إضافة حقل **اسم الجهة** (إجباري)
- ✅ تم إضافة حقل **الرقم الضريبي** (اختياري)
- ✅ تم تحديث نموذج التسجيل
- ✅ تم تحديث validation في AuthController
- ✅ تم تحديث PDF لعرض المعلومات الجديدة
- ✅ تم تحديث صفحات Admin
- ✅ تم اختبار جميع التغييرات

---

**التاريخ:** 11 أكتوبر 2025  
**الحالة:** ✅ مكتمل وجاهز للاستخدام  
**الإصدار:** 1.0

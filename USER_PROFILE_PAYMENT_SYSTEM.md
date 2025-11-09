# User Profile & Payment Information System
## نظام الملف الشخصي ومعلومات الدفع للمستخدمين

تاريخ التحديث: 11 أكتوبر 2025

---

## 📋 نظرة عامة

تم تطوير نظام شامل للملف الشخصي للمستخدمين يتضمن:
1. **صفحة الملف الشخصي** - عرض كامل لبيانات المستخدم
2. **تعديل البيانات الشخصية** - تحديث المعلومات
3. **تغيير كلمة المرور** - أمان محسّن
4. **معلومات الدفع** - بيانات بنكية للمستخدم
5. **عرض معلومات الجهة والضريبة** في لوحة التحكم

---

## 🗄️ قاعدة البيانات

### حقول جديدة في جدول `users`

```sql
-- Company Information (added previously)
company_name VARCHAR(255) NOT NULL
tax_number VARCHAR(20) NULL

-- Payment Information (added now)
bank_name VARCHAR(255) NULL
bank_account_number VARCHAR(50) NULL
iban VARCHAR(34) NULL

-- Indexes
KEY `users_company_name_index` (company_name)
KEY `users_tax_number_index` (tax_number)
KEY `users_bank_account_number_index` (bank_account_number)
KEY `users_iban_index` (iban)
```

### Migration

```bash
php artisan make:migration add_payment_info_to_users_table
php artisan migrate
```

---

## 🎯 الميزات المضافة

### 1. صفحة الملف الشخصي (`/profile`)

**المسار:** `GET /profile`
**Route Name:** `profile.show`
**View:** `resources/views/profile/show.blade.php`

#### الأقسام:
- ✅ **البيانات الشخصية**: الاسم، البريد، الهاتف، تاريخ التسجيل
- ✅ **بيانات الجهة**: اسم الجهة، الرقم الضريبي
- ✅ **معلومات الدفع**: البنك، رقم الحساب، IBAN
- ✅ **إحصائيات الحجوزات**: إجمالي، قيد الانتظار، مؤكدة
- ✅ **آخر الحجوزات**: جدول تفاعلي مع pagination

#### القائمة الجانبية:
- الملف الشخصي (نشط)
- تعديل البيانات
- تغيير كلمة المرور
- عروض الأسعار

---

### 2. تعديل البيانات (`/profile/edit`)

**المسار:** `GET /profile/edit` و `PUT /profile`
**Route Names:** `profile.edit` و `profile.update`
**View:** `resources/views/profile/edit.blade.php`

#### النموذج يتضمن:

**البيانات الشخصية:**
```html
- الاسم الكامل (required)
- البريد الإلكتروني (required, unique)
- رقم الهاتف (optional)
```

**بيانات الجهة:**
```html
- اسم الجهة (required)
- الرقم الضريبي (optional)
```

**معلومات الدفع:**
```html
- اسم البنك (optional)
- رقم الحساب (optional)
- IBAN (optional, placeholder: SA0000000000000000000000)
```

#### Validation Rules:
```php
'name' => 'required|string|max:255',
'company_name' => 'required|string|max:255',
'tax_number' => 'nullable|string|max:20',
'email' => 'required|email|max:255|unique:users,email,' . $user->id,
'phone' => 'nullable|string|max:20',
'bank_name' => 'nullable|string|max:255',
'bank_account_number' => 'nullable|string|max:50',
'iban' => 'nullable|string|max:34',
```

---

### 3. تغيير كلمة المرور (`/profile/password`)

**المسار:** `GET /profile/password` و `PUT /profile/password`
**Route Names:** `profile.password` و `profile.password.update`
**View:** `resources/views/profile/password.blade.php`

#### الحقول:
```html
- كلمة المرور الحالية (required)
- كلمة المرور الجديدة (required, min:8)
- تأكيد كلمة المرور (required, confirmed)
```

#### Validation Rules:
```php
'current_password' => 'required|current_password',
'password' => 'required|confirmed|Password::defaults()',
```

---

### 4. لوحة التحكم - عرض المستخدم

**المسار:** `/admin/users/{user}`
**View:** `resources/views/admin/users/show.blade.php`

#### الأقسام المضافة:
```html
<!-- اسم الجهة والرقم الضريبي -->
<div class="col-sm-6">
    <label>اسم الجهة:</label>
    <p>{{ $user->company_name }}</p>
</div>
<div class="col-sm-6">
    <label>الرقم الضريبي:</label>
    <p>{{ $user->tax_number ?: 'غير محدد' }}</p>
</div>

<!-- معلومات الدفع -->
<h5><i class="fas fa-credit-card"></i> معلومات الدفع</h5>
<div class="col-sm-6">
    <label>البنك:</label>
    <p>{{ $user->bank_name ?: 'غير محدد' }}</p>
</div>
<div class="col-sm-6">
    <label>رقم الحساب:</label>
    <p>{{ $user->bank_account_number ?: 'غير محدد' }}</p>
</div>
<div class="col-sm-6">
    <label>IBAN:</label>
    <p>{{ $user->iban ?: 'غير محدد' }}</p>
</div>
```

---

### 5. لوحة التحكم - تعديل المستخدم

**المسار:** `/admin/users/{user}/edit`
**View:** `resources/views/admin/users/edit.blade.php`

#### الحقول المضافة:
```html
<!-- بيانات الجهة -->
<h5><i class="fas fa-building"></i> بيانات الجهة</h5>
- اسم الجهة (required)
- الرقم الضريبي (optional)

<!-- معلومات الدفع -->
<h5><i class="fas fa-credit-card"></i> معلومات الدفع</h5>
- اسم البنك (optional)
- رقم الحساب (optional)
- IBAN (optional)
```

---

## 🎨 الواجهة

### التصميم
- **Bootstrap 5.3** مع RTL support
- **Font Awesome 6.4** للأيقونات
- **Responsive Design** - يعمل على جميع الشاشات
- **Color Scheme:**
  - Primary: `#0d6efd` (أزرق)
  - Success: `#198754` (أخضر)
  - Warning: `#ffc107` (أصفر)
  - Info: `#0dcaf0` (سماوي)

### الأيقونات المستخدمة:
```
fa-user-circle  → الملف الشخصي
fa-building     → بيانات الجهة
fa-credit-card  → معلومات الدفع
fa-university   → البنك
fa-hashtag      → رقم الحساب
fa-receipt      → الرقم الضريبي
fa-phone        → الهاتف
fa-envelope     → البريد
fa-key          → كلمة المرور
```

---

## 🔗 Routes الجديدة

```php
// Profile Routes (Authenticated Users Only)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/profile/password', [ProfileController::class, 'editPassword'])->name('profile.password');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
});
```

### التحقق من Routes:
```bash
php artisan route:list | grep profile
```

**النتيجة:**
```
GET|HEAD   profile ..................... profile.show
PUT        profile ................... profile.update
GET|HEAD   profile/edit ............... profile.edit
GET|HEAD   profile/password ......... profile.password
PUT        profile/password .. profile.password.update
```

---

## 📦 الملفات المُنشأة/المُحدّثة

### Controllers:
```
✅ app/Http/Controllers/ProfileController.php (جديد)
✅ app/Http/Controllers/Admin/UserController.php (محدّث)
```

### Models:
```
✅ app/Models/User.php (محدّث - أضيف fillable)
```

### Views:
```
✅ resources/views/profile/show.blade.php (جديد)
✅ resources/views/profile/edit.blade.php (جديد)
✅ resources/views/profile/password.blade.php (جديد)
✅ resources/views/admin/users/show.blade.php (محدّث)
✅ resources/views/admin/users/edit.blade.php (محدّث)
✅ resources/views/layouts/app.blade.php (محدّث - navbar)
```

### Migrations:
```
✅ 2025_10_11_155957_add_payment_info_to_users_table.php
```

### Routes:
```
✅ routes/web.php (محدّث)
```

---

## 🧪 الاختبار

### 1. اختبار الملف الشخصي:
```bash
# تسجيل الدخول كمستخدم عادي
1. انتقل إلى: http://your-domain.com/login
2. سجل الدخول
3. اضغط على اسمك في navbar → "الملف الشخصي"
4. تحقق من عرض جميع البيانات
```

### 2. اختبار تعديل البيانات:
```bash
1. من الملف الشخصي → "تعديل البيانات"
2. قم بتعديل:
   - الاسم
   - اسم الجهة
   - البريد
   - معلومات الدفع (البنك، رقم الحساب، IBAN)
3. اضغط "حفظ التغييرات"
4. تحقق من ظهور رسالة نجاح
5. تحقق من تحديث البيانات
```

### 3. اختبار تغيير كلمة المرور:
```bash
1. من القائمة → "تغيير كلمة المرور"
2. أدخل:
   - كلمة المرور الحالية
   - كلمة المرور الجديدة
   - تأكيد كلمة المرور
3. اضغط "تغيير كلمة المرور"
4. سجل خروج وحاول تسجيل الدخول بكلمة المرور الجديدة
```

### 4. اختبار لوحة التحكم:
```bash
# كمدير:
1. انتقل إلى: /admin/users
2. اضغط على أي مستخدم
3. تحقق من ظهور:
   - اسم الجهة
   - الرقم الضريبي
   - معلومات الدفع (البنك، رقم الحساب، IBAN)
4. اضغط "تعديل"
5. قم بتحديث بيانات الجهة ومعلومات الدفع
6. احفظ وتحقق من التحديث
```

---

## 💡 أمثلة الاستخدام

### عرض بيانات المستخدم في Blade:
```blade
<!-- Company Info -->
<p>الجهة: {{ auth()->user()->company_name }}</p>
<p>الرقم الضريبي: {{ auth()->user()->tax_number ?? 'غير محدد' }}</p>

<!-- Payment Info -->
<p>البنك: {{ auth()->user()->bank_name ?? 'غير محدد' }}</p>
<p>رقم الحساب: {{ auth()->user()->bank_account_number ?? 'غير محدد' }}</p>
<p>IBAN: {{ auth()->user()->iban ?? 'غير محدد' }}</p>
```

### التحقق من وجود بيانات الدفع:
```blade
@if($user->bank_name || $user->bank_account_number || $user->iban)
    <!-- عرض معلومات الدفع -->
@else
    <p class="text-muted">لم يتم إضافة معلومات الدفع</p>
@endif
```

### في Controller:
```php
// تحديث بيانات المستخدم
$user->update([
    'bank_name' => $request->bank_name,
    'bank_account_number' => $request->bank_account_number,
    'iban' => $request->iban,
]);

// استرجاع المستخدمين مع معلومات الدفع
$users = User::whereNotNull('bank_account_number')->get();
```

---

## 🚀 التكامل

### في PDF Quotes:
```php
// يمكن إضافة معلومات الدفع في عروض الأسعار
$user = auth()->user();

<p>البنك: {{ $user->bank_name }}</p>
<p>رقم الحساب: {{ $user->bank_account_number }}</p>
<p>IBAN: {{ $user->iban }}</p>
```

### في Emails:
```php
// إرسال بيانات الدفع في البريد الإلكتروني
Mail::to($user)->send(new PaymentInfoMail([
    'bank_name' => $user->bank_name,
    'account_number' => $user->bank_account_number,
    'iban' => $user->iban,
]));
```

---

## 🔒 الأمان

### Middleware:
- ✅ جميع routes محمية بـ `auth` middleware
- ✅ لا يمكن تعديل بيانات مستخدم آخر
- ✅ التحقق من كلمة المرور الحالية عند التغيير

### Validation:
```php
// تحقق من صحة IBAN (اختياري للتطوير المستقبلي)
'iban' => ['nullable', 'string', 'max:34', 'regex:/^[A-Z]{2}[0-9]{2}[A-Z0-9]+$/'],

// تحقق من رقم الحساب
'bank_account_number' => ['nullable', 'string', 'max:50', 'regex:/^[0-9]+$/'],
```

### حماية البيانات الحساسة:
```php
// لا تعرض IBAN كاملاً في بعض الأماكن
$maskedIban = substr($user->iban, 0, 8) . str_repeat('*', strlen($user->iban) - 8);
```

---

## 📊 إحصائيات

### قاعدة البيانات:
```sql
-- عدد المستخدمين مع معلومات دفع
SELECT COUNT(*) FROM users WHERE bank_name IS NOT NULL;

-- عدد المستخدمين مع IBAN
SELECT COUNT(*) FROM users WHERE iban IS NOT NULL;

-- المستخدمين بدون معلومات دفع
SELECT * FROM users WHERE bank_name IS NULL AND bank_account_number IS NULL;
```

---

## 🔄 التحديثات المستقبلية

### مقترحات:
1. ✨ **إضافة صورة للملف الشخصي** (Profile Picture Upload)
2. ✨ **تفعيل Two-Factor Authentication** (2FA)
3. ✨ **سجل النشاطات** (Activity Log)
4. ✨ **التحقق من IBAN** باستخدام API بنكية
5. ✨ **تصدير بيانات المستخدم** (GDPR Compliance)
6. ✨ **إشعارات** عند تعديل معلومات الدفع
7. ✨ **نماذج دفع متعددة** (Multiple Payment Methods)

---

## 🐛 استكشاف الأخطاء

### مشكلة: لا تظهر معلومات الدفع
```bash
# تحقق من Migration
php artisan migrate:status

# تحقق من الجدول
php artisan tinker
>>> User::first()->bank_name
```

### مشكلة: خطأ في Validation
```bash
# تحقق من fillable في Model
>>> User::create(['bank_name' => 'Test']) # يجب أن يعمل
```

### مشكلة: Route لا يعمل
```bash
# امسح Cache
php artisan optimize:clear

# تحقق من Routes
php artisan route:list | grep profile
```

---

## ✅ الخلاصة

تم بنجاح إضافة:
1. ✅ نظام الملف الشخصي الكامل للعملاء
2. ✅ صفحات تعديل البيانات الشخصية
3. ✅ صفحة تغيير كلمة المرور
4. ✅ معلومات الدفع (البنك، رقم الحساب، IBAN)
5. ✅ عرض اسم الجهة والرقم الضريبي في لوحة التحكم
6. ✅ تعديل بيانات المستخدمين من لوحة التحكم
7. ✅ رابط الملف الشخصي في Navbar (أول خيار في القائمة)
8. ✅ Routes محمية بـ authentication
9. ✅ Validation شامل لجميع البيانات
10. ✅ تصميم responsive وجذاب

---

**تم إنشاء هذا التوثيق في:** 11 أكتوبر 2025
**الإصدار:** 1.0.0
**المطور:** GitHub Copilot

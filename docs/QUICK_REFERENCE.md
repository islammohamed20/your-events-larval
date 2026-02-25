# 🚀 Quick Reference Guide
## دليل سريع للنظام الجديد

---

## ⚡ الوصول السريع

### 🎯 للعميل:
```
/profile          → الملف الشخصي
/profile/edit     → تعديل البيانات
/profile/password → تغيير كلمة المرور
```

### 🎯 للمدير:
```
/admin/users      → قائمة المستخدمين
/admin/users/{id} → عرض بيانات مستخدم
```

---

## 📋 الحقول الجديدة في Database

```sql
-- جدول users
company_name         VARCHAR(255) NOT NULL
tax_number          VARCHAR(20)   NULL
bank_name           VARCHAR(255)  NULL
bank_account_number VARCHAR(50)   NULL
iban                VARCHAR(34)   NULL
```

---

## 🔑 Route Names

```php
profile.show            → عرض الملف الشخصي
profile.edit            → صفحة التعديل
profile.update          → حفظ التعديلات (PUT)
profile.password        → صفحة تغيير الباسوورد
profile.password.update → حفظ الباسوورد (PUT)
```

---

## 💡 أمثلة الاستخدام

### في Blade:
```blade
<!-- عرض بيانات المستخدم -->
{{ auth()->user()->company_name }}
{{ auth()->user()->tax_number ?? 'غير محدد' }}
{{ auth()->user()->bank_name ?? 'غير محدد' }}
{{ auth()->user()->iban ?? 'غير محدد' }}

<!-- رابط الملف الشخصي -->
<a href="{{ route('profile.show') }}">الملف الشخصي</a>

<!-- التحقق من وجود بيانات -->
@if($user->bank_name)
    <p>البنك: {{ $user->bank_name }}</p>
@endif
```

### في Controller:
```php
// استرجاع المستخدم الحالي
$user = auth()->user();

// تحديث البيانات
$user->update([
    'bank_name' => $request->bank_name,
    'bank_account_number' => $request->bank_account_number,
    'iban' => $request->iban,
]);

// التحقق من البيانات
$validated = $request->validate([
    'company_name' => 'required|string|max:255',
    'tax_number' => 'nullable|string|max:20',
    'bank_name' => 'nullable|string|max:255',
    'bank_account_number' => 'nullable|string|max:50',
    'iban' => 'nullable|string|max:34',
]);
```

---

## 🧪 الاختبار السريع

```bash
# 1. تسجيل الدخول
curl -X POST http://your-domain.com/login \
  -d "email=user@example.com" \
  -d "password=password"

# 2. زيارة الملف الشخصي
curl http://your-domain.com/profile

# 3. التحقق من Routes
php artisan route:list | grep profile

# 4. التحقق من Database
mysql -u root -p'yourevent2025' your_events \
  -e "DESCRIBE users;"

# 5. مسح Cache
php artisan optimize:clear
```

---

## 📊 الملفات المهمة

```
Controllers:
  app/Http/Controllers/ProfileController.php
  app/Http/Controllers/Admin/UserController.php

Views:
  resources/views/profile/show.blade.php
  resources/views/profile/edit.blade.php
  resources/views/profile/password.blade.php
  resources/views/admin/users/show.blade.php
  resources/views/admin/users/edit.blade.php

Migration:
  database/migrations/2025_10_11_155957_add_payment_info_to_users_table.php

Model:
  app/Models/User.php

Routes:
  routes/web.php
```

---

## 🔧 Commands المفيدة

```bash
# مسح جميع Caches
php artisan optimize:clear

# عرض Routes
php artisan route:list

# عرض Migrations
php artisan migrate:status

# Rollback آخر Migration
php artisan migrate:rollback

# إعادة تشغيل Migrations
php artisan migrate:refresh

# تشغيل Tinker
php artisan tinker
>>> User::first()
>>> User::where('bank_name', '!=', null)->count()
```

---

## 🐛 Troubleshooting

### المشكلة: Route لا يعمل
```bash
php artisan optimize:clear
php artisan route:cache
```

### المشكلة: View لا يظهر
```bash
php artisan view:clear
chmod -R 755 resources/views/profile/
chown -R www-data:www-data resources/views/profile/
```

### المشكلة: خطأ في Database
```bash
php artisan migrate:status
php artisan migrate
php artisan db:show
```

### المشكلة: خطأ 500
```bash
# تحقق من logs
tail -f storage/logs/laravel.log

# تأكد من الصلاحيات
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

---

## ✅ Validation Rules

```php
// ProfileController@update
'name' => 'required|string|max:255',
'company_name' => 'required|string|max:255',
'tax_number' => 'nullable|string|max:20',
'email' => 'required|email|max:255|unique:users,email,{id}',
'phone' => 'nullable|string|max:20',
'bank_name' => 'nullable|string|max:255',
'bank_account_number' => 'nullable|string|max:50',
'iban' => 'nullable|string|max:34',

// ProfileController@updatePassword
'current_password' => 'required|current_password',
'password' => 'required|confirmed|min:8',
```

---

## 🎨 CSS Classes

```html
<!-- Bootstrap 5 -->
.btn-primary      → أزرق
.btn-success      → أخضر
.btn-warning      → أصفر
.btn-danger       → أحمر
.btn-secondary    → رمادي
.btn-info         → سماوي

<!-- Alerts -->
.alert-success    → رسالة نجاح
.alert-danger     → رسالة خطأ
.alert-warning    → رسالة تحذير
.alert-info       → رسالة معلومات

<!-- Form -->
.form-control     → حقل إدخال
.form-label       → عنوان الحقل
.is-invalid       → حقل خاطئ
.invalid-feedback → رسالة خطأ الحقل
```

---

## 📞 الدعم

### وثائق:
- `USER_PROFILE_PAYMENT_SYSTEM.md` - توثيق شامل
- `UPDATE_SUMMARY.md` - ملخص التحديثات
- `VISUAL_GUIDE.md` - دليل مرئي
- `QUICK_REFERENCE.md` - هذا الملف

### الحالة:
✅ **جاهز للإنتاج**
- Laravel 11
- PHP 8.2
- MySQL 8.0
- Bootstrap 5.3
- Font Awesome 6.4

---

**آخر تحديث:** 11 أكتوبر 2025

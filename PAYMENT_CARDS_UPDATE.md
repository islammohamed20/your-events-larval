# 💳 تحديث معلومات الدفع - بطاقات الائتمان السعودية
## Payment Information Update - Saudi Card Payment Methods

تاريخ التحديث: 11 أكتوبر 2025

---

## 📋 نظرة عامة

تم تحديث نظام معلومات الدفع من **البنك وIBAN** إلى **بطاقات الدفع الإلكترونية** المستخدمة في السعودية:
- 💳 **فيزا** (Visa)
- 💳 **ماستر كارد** (Mastercard)
- 💳 **مدى** (Mada)

---

## 🔄 التغييرات في قاعدة البيانات

### ❌ تم حذف الحقول القديمة:
```sql
-- Removed fields
bank_name VARCHAR(255)
bank_account_number VARCHAR(50)
iban VARCHAR(34)
```

### ✅ تم إضافة حقول جديدة:
```sql
-- New card payment fields
card_type ENUM('visa', 'mastercard', 'mada') NULL
card_holder_name VARCHAR(255) NULL
card_last_four VARCHAR(4) NULL  -- آخر 4 أرقام فقط للأمان
card_expiry_month VARCHAR(2) NULL  -- MM (01-12)
card_expiry_year VARCHAR(4) NULL  -- YYYY

-- Indexes
KEY `users_card_type_index` (card_type)
KEY `users_card_last_four_index` (card_last_four)
```

---

## 🗄️ Migration

**الملف:** `database/migrations/2025_10_11_180012_update_payment_fields_to_card_info.php`

```bash
# تشغيل Migration
php artisan migrate

# إذا كنت تريد التراجع
php artisan migrate:rollback
```

---

## 🎯 الحقول الجديدة

### 1. نوع البطاقة (card_type)
- **نوع البيانات:** ENUM
- **القيم المتاحة:** `visa`, `mastercard`, `mada`
- **إجباري:** لا (nullable)
- **مثال:** `"visa"`, `"mada"`

### 2. اسم حامل البطاقة (card_holder_name)
- **نوع البيانات:** VARCHAR(255)
- **إجباري:** لا (nullable)
- **مثال:** `"عبدالله محمد"`, `"Abdullah Mohammed"`

### 3. آخر 4 أرقام (card_last_four)
- **نوع البيانات:** VARCHAR(4)
- **إجباري:** لا (nullable)
- **مثال:** `"1234"`
- **ملاحظة:** 🔒 نحفظ آخر 4 أرقام فقط لأسباب أمنية

### 4. شهر الانتهاء (card_expiry_month)
- **نوع البيانات:** VARCHAR(2)
- **صيغة:** MM (01-12)
- **إجباري:** لا (nullable)
- **مثال:** `"01"`, `"12"`

### 5. سنة الانتهاء (card_expiry_year)
- **نوع البيانات:** VARCHAR(4)
- **صيغة:** YYYY
- **إجباري:** لا (nullable)
- **مثال:** `"2025"`, `"2030"`

---

## ✅ Validation Rules

### ProfileController & Admin\UserController:

```php
'card_type' => 'nullable|in:visa,mastercard,mada',
'card_holder_name' => 'nullable|string|max:255',
'card_last_four' => 'nullable|string|size:4|regex:/^[0-9]{4}$/',
'card_expiry_month' => 'nullable|string|size:2|regex:/^(0[1-9]|1[0-2])$/',
'card_expiry_year' => 'nullable|string|size:4|regex:/^[0-9]{4}$/',
```

### رسائل الأخطاء بالعربية:

```php
'card_type.in' => 'نوع البطاقة غير صحيح',
'card_last_four.size' => 'يجب إدخال 4 أرقام فقط',
'card_last_four.regex' => 'يجب أن تكون أرقام فقط',
'card_expiry_month.regex' => 'يجب إدخال شهر صحيح (01-12)',
'card_expiry_year.regex' => 'يجب إدخال سنة صحيحة (4 أرقام)',
```

---

## 🎨 واجهة المستخدم

### 1. صفحة عرض الملف الشخصي (`/profile`)

```blade
@if($user->card_type)
    <label>نوع البطاقة:</label>
    @if($user->card_type == 'visa')
        <i class="fab fa-cc-visa text-primary"></i> فيزا (Visa)
    @elseif($user->card_type == 'mastercard')
        <i class="fab fa-cc-mastercard text-warning"></i> ماستر كارد
    @elseif($user->card_type == 'mada')
        <i class="fas fa-credit-card text-success"></i> مدى (Mada)
    @endif
@endif

@if($user->card_holder_name)
    <label>اسم حامل البطاقة:</label>
    <p>{{ $user->card_holder_name }}</p>
@endif

@if($user->card_last_four)
    <label>آخر 4 أرقام:</label>
    <p>**** **** **** {{ $user->card_last_four }}</p>
@endif

@if($user->card_expiry_month && $user->card_expiry_year)
    <label>تاريخ الانتهاء:</label>
    <p>{{ $user->card_expiry_month }}/{{ $user->card_expiry_year }}</p>
@endif
```

### 2. صفحة تعديل الملف الشخصي (`/profile/edit`)

```blade
<!-- نوع البطاقة -->
<select name="card_type" class="form-select">
    <option value="">اختر نوع البطاقة</option>
    <option value="visa">فيزا (Visa)</option>
    <option value="mastercard">ماستر كارد (Mastercard)</option>
    <option value="mada">مدى (Mada)</option>
</select>

<!-- اسم حامل البطاقة -->
<input type="text" name="card_holder_name" 
       placeholder="الاسم كما في البطاقة">

<!-- آخر 4 أرقام -->
<input type="text" name="card_last_four" 
       placeholder="1234" maxlength="4" pattern="[0-9]{4}">

<!-- شهر الانتهاء -->
<select name="card_expiry_month" class="form-select">
    <option value="">شهر</option>
    @for($m = 1; $m <= 12; $m++)
        <option value="{{ sprintf('%02d', $m) }}">{{ sprintf('%02d', $m) }}</option>
    @endfor
</select>

<!-- سنة الانتهاء -->
<select name="card_expiry_year" class="form-select">
    <option value="">سنة</option>
    @for($y = date('Y'); $y <= date('Y') + 10; $y++)
        <option value="{{ $y }}">{{ $y }}</option>
    @endfor
</select>
```

---

## 🎨 الأيقونات المستخدمة

```html
<!-- Font Awesome Icons -->
<i class="fab fa-cc-visa text-primary"></i>       ← فيزا (أزرق)
<i class="fab fa-cc-mastercard text-warning"></i> ← ماستر كارد (أصفر)
<i class="fas fa-credit-card text-success"></i>   ← مدى (أخضر)
<i class="fas fa-user"></i>                       ← اسم حامل البطاقة
<i class="fas fa-hashtag"></i>                    ← أرقام البطاقة
<i class="fas fa-calendar"></i>                   ← تاريخ الانتهاء
```

---

## 📁 الملفات المُحدّثة

### Models:
```
✅ app/Models/User.php
   - تحديث fillable لإضافة الحقول الجديدة
```

### Controllers:
```
✅ app/Http/Controllers/ProfileController.php
   - تحديث validation rules
   
✅ app/Http/Controllers/Admin/UserController.php
   - تحديث store() validation
   - تحديث update() validation
```

### Views:
```
✅ resources/views/profile/show.blade.php
   - عرض معلومات البطاقة
   - أيقونات نوع البطاقة
   
✅ resources/views/profile/edit.blade.php
   - نماذج تعديل البطاقة
   - قوائم منسدلة للشهر والسنة
   
✅ resources/views/admin/users/show.blade.php
   - عرض معلومات بطاقة المستخدم
   
✅ resources/views/admin/users/edit.blade.php
   - نماذج تعديل بطاقة المستخدم
```

### Migrations:
```
✅ database/migrations/2025_10_11_180012_update_payment_fields_to_card_info.php
   - حذف حقول البنك القديمة
   - إضافة حقول البطاقة الجديدة
```

---

## 🔐 الأمان

### ✅ ما نحفظه:
- ✅ نوع البطاقة (visa/mastercard/mada)
- ✅ اسم حامل البطاقة
- ✅ **آخر 4 أرقام فقط** من رقم البطاقة
- ✅ تاريخ الانتهاء (شهر/سنة)

### ❌ ما لا نحفظه (لأسباب أمنية):
- ❌ رقم البطاقة الكامل (16 رقم)
- ❌ CVV/CVC (رمز الأمان)
- ❌ PIN (الرقم السري)

### 🔒 ملاحظة أمنية مهمة:
```
⚠️ نحن نحفظ فقط معلومات تعريفية للبطاقة
⚠️ لا نحفظ أي معلومات حساسة يمكن استخدامها في الدفع
⚠️ هذا يتوافق مع معايير PCI DSS للأمان
```

---

## 🧪 الاختبار

### 1. اختبار إضافة بطاقة جديدة:

```bash
# تسجيل الدخول
1. انتقل إلى: /profile/edit
2. اختر نوع البطاقة: مدى
3. أدخل الاسم: عبدالله محمد
4. أدخل آخر 4 أرقام: 1234
5. اختر الشهر: 12
6. اختر السنة: 2026
7. اضغط "حفظ التغييرات"
8. تحقق من ظهور البيانات في /profile
```

### 2. اختبار Validation:

```bash
# آخر 4 أرقام غير صحيحة
Input: "12" → Error: "يجب إدخال 4 أرقام فقط"
Input: "12ab" → Error: "يجب أن تكون أرقام فقط"

# شهر غير صحيح
Input: "13" → Error: "يجب إدخال شهر صحيح (01-12)"
Input: "00" → Error: "يجب إدخال شهر صحيح (01-12)"

# سنة غير صحيحة
Input: "25" → Error: "يجب إدخال سنة صحيحة (4 أرقام)"
Input: "20ab" → Error: "يجب إدخال سنة صحيحة (4 أرقام)"
```

### 3. اختبار عرض البطاقات:

```php
// في Tinker
php artisan tinker

>>> $user = User::first();
>>> $user->card_type = 'mada';
>>> $user->card_holder_name = 'عبدالله محمد';
>>> $user->card_last_four = '1234';
>>> $user->card_expiry_month = '12';
>>> $user->card_expiry_year = '2026';
>>> $user->save();

>>> // التحقق
>>> $user->refresh();
>>> $user->card_type; // "mada"
>>> $user->card_last_four; // "1234"
```

---

## 💡 أمثلة الاستخدام

### في Blade Templates:

```blade
<!-- عرض نوع البطاقة مع أيقونة -->
@if(auth()->user()->card_type)
    @switch(auth()->user()->card_type)
        @case('visa')
            <i class="fab fa-cc-visa"></i> فيزا
            @break
        @case('mastercard')
            <i class="fab fa-cc-mastercard"></i> ماستر كارد
            @break
        @case('mada')
            <i class="fas fa-credit-card"></i> مدى
            @break
    @endswitch
@endif

<!-- عرض آخر 4 أرقام بشكل آمن -->
@if(auth()->user()->card_last_four)
    <span class="card-number">**** **** **** {{ auth()->user()->card_last_four }}</span>
@endif

<!-- عرض تاريخ الانتهاء -->
@if(auth()->user()->card_expiry_month && auth()->user()->card_expiry_year)
    <span class="expiry-date">
        {{ auth()->user()->card_expiry_month }}/{{ auth()->user()->card_expiry_year }}
    </span>
@endif
```

### في Controller:

```php
// حفظ معلومات البطاقة
$user->update([
    'card_type' => 'mada',
    'card_holder_name' => 'عبدالله محمد',
    'card_last_four' => '1234',
    'card_expiry_month' => '12',
    'card_expiry_year' => '2026',
]);

// البحث عن مستخدمين بنوع بطاقة معين
$madaUsers = User::where('card_type', 'mada')->get();
$visaUsers = User::where('card_type', 'visa')->get();

// التحقق من انتهاء البطاقة
$expiredCards = User::where('card_expiry_year', '<', date('Y'))
    ->orWhere(function($q) {
        $q->where('card_expiry_year', '=', date('Y'))
          ->where('card_expiry_month', '<', date('m'));
    })
    ->get();
```

---

## 📊 إحصائيات

```sql
-- عدد المستخدمين حسب نوع البطاقة
SELECT card_type, COUNT(*) as count 
FROM users 
WHERE card_type IS NOT NULL 
GROUP BY card_type;

-- المستخدمين مع معلومات بطاقة كاملة
SELECT COUNT(*) FROM users 
WHERE card_type IS NOT NULL 
  AND card_holder_name IS NOT NULL 
  AND card_last_four IS NOT NULL;

-- المستخدمين بدون معلومات بطاقة
SELECT COUNT(*) FROM users 
WHERE card_type IS NULL;
```

---

## 🔄 Migration Rollback

إذا كنت تريد التراجع عن التحديث:

```bash
# التراجع عن آخر migration
php artisan migrate:rollback

# سيتم:
# ✅ حذف حقول البطاقة الجديدة
# ✅ إعادة حقول البنك القديمة
# ⚠️ ملاحظة: ستفقد بيانات البطاقات المحفوظة
```

---

## ✅ الخلاصة

### تم التحديث بنجاح:
1. ✅ حذف حقول البنك القديمة (bank_name, bank_account_number, iban)
2. ✅ إضافة حقول البطاقة الجديدة (5 حقول)
3. ✅ تحديث User Model (fillable)
4. ✅ تحديث Controllers (validation)
5. ✅ تحديث جميع الـ Views (4 ملفات)
6. ✅ إضافة أيقونات Font Awesome لأنواع البطاقات
7. ✅ قوائم منسدلة للشهر والسنة
8. ✅ Validation شامل مع رسائل عربية
9. ✅ حفظ آخر 4 أرقام فقط (أمان محسّن)

### الحالة:
🟢 **جاهز للاستخدام بنسبة 100%**

---

**تاريخ التحديث:** 11 أكتوبر 2025
**الإصدار:** 2.0.0
**نوع التحديث:** Major Update (Breaking Change)

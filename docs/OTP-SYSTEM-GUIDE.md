# 🔐 نظام OTP (كود التحقق) - دليل شامل

## ✅ تم التنفيذ بنجاح!

تم إنشاء نظام OTP متكامل لإرسال والتحقق من أكواد التحقق عبر البريد الإلكتروني.

---

## 📋 المحتويات

1. [ما هو OTP؟](#what-is-otp)
2. [الميزات](#features)
3. [كيف يعمل النظام؟](#how-it-works)
4. [أنواع OTP](#otp-types)
5. [الاستخدام للمستخدمين](#user-guide)
6. [الاستخدام للمطورين](#developer-guide)
7. [الأمان](#security)
8. [API Reference](#api-reference)
9. [استكشاف الأخطاء](#troubleshooting)

---

## 🎯 ما هو OTP؟ {#what-is-otp}

**OTP** = One-Time Password (كلمة مرور لمرة واحدة)

هو كود أمان مكون من 6 أرقام يُرسل عبر البريد الإلكتروني ويُستخدم لمرة واحدة فقط للتحقق من هوية المستخدم.

**مثال:** `456789`

---

## ✨ الميزات {#features}

### 🔒 الأمان
- ✅ كود عشوائي من 6 أرقام
- ✅ صلاحية محدودة (10 دقائق)
- ✅ استخدام واحد فقط
- ✅ حد أقصى 5 محاولات
- ✅ Rate Limiting لمنع الهجمات
- ✅ تسجيل IP و User Agent

### 📧 الإرسال
- ✅ قالب بريد احترافي بتصميم جذاب
- ✅ دعم RTL للعربية
- ✅ معلومات واضحة (الصلاحية، الغرض)
- ✅ تحذيرات أمنية

### 🎨 الواجهة
- ✅ إدخال سهل ومرن (6 خانات منفصلة)
- ✅ مؤقت للعد التنازلي
- ✅ إعادة إرسال مع تحديد الوقت
- ✅ رسائل خطأ واضحة
- ✅ متوافق مع الجوال

### ⚡ الأداء
- ✅ حذف تلقائي للأكواد المنتهية
- ✅ Indexes للاستعلامات السريعة
- ✅ تخزين فعّال في قاعدة البيانات

---

## 🔄 كيف يعمل النظام؟ {#how-it-works}

### 1️⃣ **طلب OTP**
```
المستخدم → يدخل البريد الإلكتروني
النظام → يولد كود عشوائي (6 أرقام)
النظام → يحفظ في قاعدة البيانات
النظام → يرسل البريد الإلكتروني
```

### 2️⃣ **التحقق من OTP**
```
المستخدم → يدخل الكود
النظام → يتحقق من:
  - وجود الكود
  - عدم انتهاء الصلاحية
  - عدم تجاوز المحاولات
  - مطابقة الكود
النظام → يحدث الحالة إلى "verified"
```

### 3️⃣ **بعد التحقق**
```
النظام → ينفذ الإجراء المطلوب:
  - إكمال التسجيل
  - تسجيل الدخول
  - إعادة تعيين كلمة المرور
  - تأكيد الحجز/الدفع
```

---

## 📝 أنواع OTP {#otp-types}

النظام يدعم 5 أنواع من OTP:

### 1. **التحقق من البريد الإلكتروني** (`email_verification`)
- **الاستخدام:** عند التسجيل لأول مرة
- **الهدف:** التأكد من صحة البريد الإلكتروني
- **بعد التحقق:** إكمال التسجيل (الاسم، كلمة المرور)

### 2. **تسجيل الدخول** (`login`)
- **الاستخدام:** تسجيل دخول آمن بدون كلمة مرور
- **الهدف:** التحقق الثنائي
- **بعد التحقق:** الدخول للحساب

### 3. **إعادة تعيين كلمة المرور** (`password_reset`)
- **الاستخدام:** نسيت كلمة المرور
- **الهدف:** التحقق قبل إعادة التعيين
- **بعد التحقق:** تعيين كلمة مرور جديدة

### 4. **تأكيد الحجز** (`booking_confirmation`)
- **الاستخدام:** قبل تأكيد الحجز
- **الهدف:** التأكد من الحجز
- **بعد التحقق:** تفعيل الحجز

### 5. **تأكيد الدفع** (`payment_confirmation`)
- **الاستخدام:** قبل إتمام الدفع
- **الهدف:** أمان إضافي للمعاملات المالية
- **بعد التحقق:** معالجة الدفع

---

## 👤 الاستخدام للمستخدمين {#user-guide}

### ✅ التسجيل الجديد بـ OTP

#### **الخطوة 1: إدخال البريد الإلكتروني**
1. اذهب إلى صفحة التسجيل
2. أدخل بريدك الإلكتروني
3. اضغط "التالي"

#### **الخطوة 2: التحقق من OTP**
1. افتح بريدك الإلكتروني
2. ستجد رسالة بعنوان "كود التحقق - Your Events"
3. انسخ الكود المكون من 6 أرقام
4. أدخله في الخانات المخصصة
5. اضغط "تحقق من الكود"

#### **الخطوة 3: إكمال التسجيل**
1. أدخل اسمك الكامل
2. أدخل كلمة المرور (8 أحرف على الأقل)
3. أكد كلمة المرور
4. أدخل رقم الهاتف (اختياري)
5. اضغط "إنشاء الحساب"

### 📧 إذا لم تستلم الكود؟

**تحقق من:**
- ✅ صندوق الوارد
- ✅ البريد المزعج (Spam)
- ✅ المجلد الترويجي (Promotions)
- ✅ صحة البريد الإلكتروني

**حلول:**
- 🔄 انتظر 60 ثانية ثم اضغط "إعادة إرسال"
- 📧 تحقق من إعدادات البريد الإلكتروني
- 🔧 جرب بريد إلكتروني آخر

### ⏱️ انتهت صلاحية الكود؟
- الكود صالح لمدة **10 دقائق** فقط
- بعد انتهاء الصلاحية، اطلب كود جديد
- اضغط "إعادة إرسال الكود"

### ❌ الكود غير صحيح؟
- تأكد من إدخال جميع الأرقام الستة
- تحقق من عدم وجود مسافات
- تأكد من نسخ الكود الصحيح
- لديك **5 محاولات** فقط

---

## 💻 الاستخدام للمطورين {#developer-guide}

### 📦 المكونات الرئيسية

#### **1. Migration**
```php
database/migrations/2025_10_23_082855_create_otp_verifications_table.php
```

**الحقول:**
- `email` - البريد الإلكتروني
- `otp` - الكود (6 أرقام)
- `type` - نوع OTP (enum)
- `status` - الحالة (pending/verified/expired/failed)
- `expires_at` - وقت انتهاء الصلاحية
- `verified_at` - وقت التحقق
- `attempts` - عدد المحاولات
- `ip_address` - عنوان IP
- `user_agent` - معلومات المتصفح

#### **2. Model**
```php
app/Models/OtpVerification.php
```

**الدوال الرئيسية:**

##### **إنشاء وإرسال OTP**
```php
OtpVerification::generate(
    string $email,
    string $type = 'email_verification',
    int $length = 6,
    int $expiryMinutes = 10
);
```

##### **التحقق من OTP**
```php
OtpVerification::verify(
    string $email,
    string $otp,
    string $type = 'email_verification'
);
```

**يرجع:**
```php
[
    'success' => true/false,
    'message' => 'رسالة النتيجة',
    'record' => OtpVerification // في حالة النجاح
]
```

##### **حذف الأكواد المنتهية**
```php
OtpVerification::cleanExpired();
```

#### **3. Controller**
```php
app/Http/Controllers/OtpController.php
```

**الدوال:**
- `showVerifyForm()` - عرض صفحة التحقق
- `sendOtp()` - إرسال OTP
- `verifyOtp()` - التحقق من OTP
- `resendOtp()` - إعادة إرسال OTP
- `completeRegistration()` - إكمال التسجيل
- `cleanExpired()` - تنظيف الأكواد القديمة

---

### 🔌 استخدام OTP في الكود

#### **مثال 1: إرسال OTP للتسجيل**

```php
use App\Models\OtpVerification;

// في صفحة التسجيل
public function register(Request $request)
{
    $request->validate([
        'email' => 'required|email|unique:users,email'
    ]);

    // إنشاء وإرسال OTP
    $otp = OtpVerification::generate(
        $request->email,
        'email_verification'
    );

    // حفظ البريد في الجلسة
    session(['otp_email' => $request->email]);

    return redirect()->route('otp.verify.form')
        ->with('success', 'تم إرسال كود التحقق');
}
```

#### **مثال 2: التحقق من OTP**

```php
public function verifyOtp(Request $request)
{
    $result = OtpVerification::verify(
        $request->email,
        $request->otp,
        'email_verification'
    );

    if ($result['success']) {
        // التحقق نجح
        session(['otp_verified' => true]);
        return redirect()->route('register.complete');
    } else {
        // التحقق فشل
        return back()->withErrors(['otp' => $result['message']]);
    }
}
```

#### **مثال 3: OTP لتأكيد الحجز**

```php
public function confirmBooking(Request $request, Booking $booking)
{
    // إرسال OTP
    OtpVerification::generate(
        $booking->user->email,
        'booking_confirmation',
        6,  // طول الكود
        15  // صلاحية 15 دقيقة
    );

    return view('bookings.verify-otp', compact('booking'));
}

public function verifyBookingOtp(Request $request, Booking $booking)
{
    $result = OtpVerification::verify(
        $booking->user->email,
        $request->otp,
        'booking_confirmation'
    );

    if ($result['success']) {
        // تأكيد الحجز
        $booking->update(['status' => 'confirmed']);
        return redirect()->route('bookings.show', $booking);
    }

    return back()->withErrors(['otp' => $result['message']]);
}
```

#### **مثال 4: OTP بدون كلمة مرور**

```php
// تسجيل دخول باستخدام OTP فقط
public function loginWithOtp(Request $request)
{
    $request->validate(['email' => 'required|email|exists:users,email']);

    // إرسال OTP
    OtpVerification::generate($request->email, 'login');

    return redirect()->route('otp.verify.form')
        ->with('success', 'تم إرسال كود تسجيل الدخول');
}

public function verifyLoginOtp(Request $request)
{
    $result = OtpVerification::verify(
        $request->email,
        $request->otp,
        'login'
    );

    if ($result['success']) {
        $user = User::where('email', $request->email)->first();
        Auth::login($user);
        return redirect()->route('dashboard');
    }

    return back()->withErrors(['otp' => $result['message']]);
}
```

---

### 📡 API Routes

#### **إرسال OTP**
```http
POST /otp/send
Content-Type: application/json

{
    "email": "user@example.com",
    "type": "email_verification"
}
```

**Response (Success):**
```json
{
    "success": true,
    "message": "تم إرسال كود التحقق إلى بريدك الإلكتروني",
    "expires_in": 10
}
```

**Response (Error):**
```json
{
    "success": false,
    "message": "البريد الإلكتروني مسجل مسبقاً"
}
```

#### **التحقق من OTP**
```http
POST /otp/verify
Content-Type: application/json

{
    "email": "user@example.com",
    "otp": "123456",
    "type": "email_verification"
}
```

**Response (Success):**
```json
{
    "success": true,
    "message": "تم التحقق بنجاح",
    "redirect": "/register/complete"
}
```

**Response (Error):**
```json
{
    "success": false,
    "message": "كود التحقق غير صحيح أو منتهي الصلاحية"
}
```

#### **إعادة إرسال OTP**
```http
POST /otp/resend
Content-Type: application/json

{
    "email": "user@example.com",
    "type": "email_verification"
}
```

---

### 🎨 واجهة إدخال OTP

**الواجهة موجودة في:**
```
resources/views/auth/verify-otp.blade.php
```

**المميزات:**
- 6 خانات منفصلة لإدخال الأرقام
- التنقل التلقائي بين الخانات
- دعم اللصق (Paste)
- مؤقت للعد التنازلي (10 دقائق)
- زر إعادة الإرسال (بعد 60 ثانية)
- رسائل خطأ ديناميكية
- تصميم responsive

**لاستخدامها:**
```php
return view('auth.verify-otp', [
    'email' => 'user@example.com',
    'type' => 'email_verification'
]);
```

---

## 🔒 الأمان {#security}

### 🛡️ التدابير الأمنية المطبقة

#### **1. Rate Limiting**
```php
// إرسال OTP: 3 محاولات كل 5 دقائق
Route::post('/otp/send')->middleware('throttle:3,5');

// التحقق من OTP: 5 محاولات كل دقيقة
Route::post('/otp/verify')->middleware('throttle:5,1');
```

#### **2. صلاحية محدودة**
- الكود صالح لـ **10 دقائق** فقط
- بعد الانتهاء، يصبح غير صالح تلقائياً

#### **3. استخدام واحد**
- كل كود يُستخدم **مرة واحدة** فقط
- بعد التحقق، يتحول إلى حالة "verified"

#### **4. حد المحاولات**
- **5 محاولات** كحد أقصى للتحقق
- بعدها، يتحول إلى حالة "failed"

#### **5. تسجيل الأنشطة**
- تسجيل IP Address
- تسجيل User Agent
- تسجيل وقت الإنشاء والتحقق

#### **6. كود عشوائي قوي**
```php
// توليد كود من 6 أرقام عشوائياً
$otp = random_int(100000, 999999);
```

#### **7. حذف الأكواد القديمة**
```php
// تنظيف الأكواد المنتهية
OtpVerification::cleanExpired();
```

**يمكن جدولته في:**
```php
// app/Console/Kernel.php
protected function schedule(Schedule $schedule)
{
    $schedule->call(function () {
        \App\Models\OtpVerification::cleanExpired();
    })->hourly();
}
```

---

## 📊 قاعدة البيانات

### جدول `otp_verifications`

| الحقل | النوع | الوصف |
|------|------|-------|
| `id` | bigint | المعرف الفريد |
| `email` | string | البريد الإلكتروني |
| `otp` | string(6) | كود التحقق |
| `type` | enum | نوع OTP |
| `status` | enum | الحالة |
| `expires_at` | timestamp | وقت انتهاء الصلاحية |
| `verified_at` | timestamp | وقت التحقق |
| `attempts` | integer | عدد المحاولات |
| `ip_address` | string(45) | عنوان IP |
| `user_agent` | text | معلومات المتصفح |
| `created_at` | timestamp | وقت الإنشاء |
| `updated_at` | timestamp | وقت التحديث |

### Indexes
```php
index('email')
index(['email', 'type'])
index(['email', 'status'])
index('expires_at')
```

---

## 🎯 أفضل الممارسات

### ✅ افعل

1. **استخدم HTTPS دائماً**
   ```
   لا ترسل OTP عبر HTTP غير آمن
   ```

2. **راجع الـ Rate Limiting**
   ```php
   // حدد عدد المحاولات بحكمة
   ->middleware('throttle:3,5')
   ```

3. **نظف البيانات القديمة**
   ```php
   // جدولة تنظيف الأكواد المنتهية
   OtpVerification::cleanExpired();
   ```

4. **سجل الأنشطة المشبوهة**
   ```php
   if ($otpRecord->attempts >= 5) {
       Log::warning('Too many OTP attempts', [
           'email' => $email,
           'ip' => request()->ip()
       ]);
   }
   ```

5. **استخدم رسائل واضحة**
   ```
   "الكود غير صحيح. لديك 3 محاولات متبقية"
   ```

### ❌ لا تفعل

1. **لا ترسل OTP عبر SMS دون تشفير**
2. **لا تستخدم أكواد يمكن تخمينها** (مثل 111111)
3. **لا تترك صلاحية غير محدودة**
4. **لا تحفظ OTP في localStorage**
5. **لا تعرض OTP في الـ URL**

---

## 🔧 استكشاف الأخطاء {#troubleshooting}

### ❌ "لم يصل البريد"

**الأسباب المحتملة:**
1. إعدادات SMTP غير صحيحة
2. البريد في مجلد Spam
3. البريد الإلكتروني غير موجود
4. مشكلة في خادم البريد

**الحلول:**
```bash
# 1. تحقق من إعدادات .env
MAIL_MAILER=smtp
MAIL_HOST=smtp.office365.com
MAIL_PORT=587
MAIL_USERNAME=your-email@outlook.com
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls

# 2. اختبر البريد
php artisan tinker
Mail::raw('Test', fn($msg) => $msg->to('test@example.com')->subject('Test'));

# 3. تحقق من logs
tail -f storage/logs/laravel.log
```

### ❌ "كود التحقق غير صحيح"

**الأسباب:**
1. إدخال الكود بشكل خاطئ
2. انتهت صلاحية الكود
3. تم استخدام الكود مسبقاً
4. تجاوز عدد المحاولات

**الحلول:**
```php
// التحقق من سجل OTP
$otp = OtpVerification::where('email', $email)
    ->where('type', $type)
    ->latest()
    ->first();

dd([
    'otp' => $otp->otp,
    'status' => $otp->status,
    'expires_at' => $otp->expires_at,
    'attempts' => $otp->attempts,
    'is_expired' => $otp->isExpired()
]);
```

### ❌ "تم تجاوز الحد الأقصى"

**السبب:** Rate Limiting

**الحل:**
```php
// في حالة الاختبار، عطّل Rate Limiting مؤقتاً
Route::post('/otp/send', [OtpController::class, 'sendOtp'])
    ->withoutMiddleware(['throttle']);

// أو امسح الـ cache
php artisan cache:clear
```

### ❌ "الكود منتهي الصلاحية"

**الحل:**
```php
// غيّر مدة الصلاحية عند الإنشاء
OtpVerification::generate($email, $type, 6, 30); // 30 دقيقة
```

---

## 📈 الإحصائيات والتقارير

### استعلامات مفيدة

#### **عدد OTP المرسلة اليوم**
```php
$count = OtpVerification::whereDate('created_at', today())->count();
```

#### **معدل النجاح**
```php
$total = OtpVerification::count();
$verified = OtpVerification::where('status', 'verified')->count();
$successRate = ($verified / $total) * 100;
```

#### **الأكواد المنتهية غير المستخدمة**
```php
$expired = OtpVerification::where('status', 'pending')
    ->where('expires_at', '<', now())
    ->count();
```

#### **المستخدمون الأكثر طلباً لـ OTP**
```php
$topUsers = OtpVerification::select('email', DB::raw('count(*) as total'))
    ->groupBy('email')
    ->orderBy('total', 'desc')
    ->limit(10)
    ->get();
```

---

## 🎉 الخلاصة

✅ **تم التنفيذ:**
- نظام OTP متكامل
- 5 أنواع مختلفة
- واجهات احترافية
- أمان عالي
- Rate Limiting
- تصميم responsive

✅ **جاهز للاستخدام في:**
- التسجيل
- تسجيل الدخول
- إعادة تعيين كلمة المرور
- تأكيد الحجوزات
- تأكيد المدفوعات

✅ **الملفات المنشأة:**
- `database/migrations/*_create_otp_verifications_table.php`
- `app/Models/OtpVerification.php`
- `app/Http/Controllers/OtpController.php`
- `resources/views/auth/verify-otp.blade.php`
- `resources/views/auth/register-complete.blade.php`
- Routes في `routes/web.php`

---

## 📞 الدعم

إذا واجهت أي مشكلة:
1. راجع قسم [استكشاف الأخطاء](#troubleshooting)
2. تحقق من الـ logs: `storage/logs/laravel.log`
3. اختبر إعدادات البريد الإلكتروني
4. تأكد من تفعيل جميع Routes

**استمتع بنظام OTP آمن وسهل الاستخدام!** 🔐✨

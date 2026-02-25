# 📧 حل مشكلة Outlook SMTP - "Approve sign in request"

## ⚠️ المشكلة
عند استخدام `sales@yourevents.sa` لإرسال الإيميلات، يطلب Microsoft موافقة على كل محاولة دخول:
```
❌ Approve sign in request
```

هذا يمنع الموقع من إرسال الإيميلات تلقائياً!

---

## ✅ الحل النهائي: App Password

### 📝 الخطوات بالتفصيل:

#### 1️⃣ تفعيل المصادقة الثنائية (2FA)

1. اذهب إلى: **https://account.microsoft.com/security**
2. سجل دخول بـ `sales@yourevents.sa` وكلمة المرور: `Yourevent@2025`
3. ابحث عن **"التحقق بخطوتين"** أو **"Two-step verification"**
4. اضغط **"تشغيل"** أو **"Turn on"**
5. اختر طريقة التحقق (تطبيق Authenticator أو رقم الهاتف)
6. اتبع التعليمات حتى تكتمل

---

#### 2️⃣ إنشاء App Password

1. ارجع إلى: **https://account.microsoft.com/security**
2. اضغط على **"خيارات الأمان المتقدمة"** (Advanced security options)
3. ابحث عن **"كلمات مرور التطبيق"** (App passwords)
4. اضغط **"إنشاء كلمة مرور جديدة"** (Create a new app password)
5. أدخل اسم مثل: **"YourEvents Website"**
6. سيظهر كود من **16 حرف** مثل:
   ```
   abcd-efgh-ijkl-mnop
   ```
7. **انسخه فوراً!** (لن تراه مرة أخرى)

---

#### 3️⃣ تحديث ملف .env

افتح الملف `/var/www/your-events/.env` وعدّل:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.office365.com
MAIL_PORT=587
MAIL_USERNAME=sales@yourevents.sa
MAIL_PASSWORD=abcd-efgh-ijkl-mnop    # <-- ضع App Password هنا
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="sales@yourevents.sa"
MAIL_FROM_NAME="Your Events"
```

**⚠️ مهم جداً:**
- استبدل `abcd-efgh-ijkl-mnop` بالكود الحقيقي الذي حصلت عليه
- لا تستخدم كلمة المرور العادية (`Yourevent@2025`) - لن تعمل!

---

#### 4️⃣ مسح الـ Cache

```bash
cd /var/www/your-events
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

---

#### 5️⃣ اختبار البريد

**من لوحة التحكم:**
1. اذهب إلى: **لوحة التحكم** → **اختبار البريد الإلكتروني**
2. أدخل بريدك الشخصي في حقل "البريد المستقبل"
3. اضغط **"إرسال البريد التجريبي"**
4. تحقق من صندوق البريد

**من Terminal:**
```bash
cd /var/www/your-events
php artisan tinker
```

ثم:
```php
Mail::raw('Test from Your Events', function ($message) {
    $message->to('your-email@example.com')
            ->subject('Test Email');
});
```

اضغط `Ctrl+C` للخروج.

---

## 📋 ملخص الإعدادات

| المتغير | القيمة |
|---------|--------|
| MAIL_HOST | smtp.office365.com |
| MAIL_PORT | 587 |
| MAIL_ENCRYPTION | tls |
| MAIL_USERNAME | sales@yourevents.sa |
| MAIL_PASSWORD | **App Password (16 حرف)** |

---

## 🔧 استكشاف الأخطاء

### ❌ "Authentication failed"
**السبب:** App Password خاطئ أو لم يتم استخدامه
**الحل:** 
- تأكد من نسخ App Password بشكل صحيح
- لا تضع مسافات في MAIL_PASSWORD
- تأكد من تفعيل 2FA

### ❌ "Connection timeout"
**السبب:** المنفذ 587 محجوب
**الحل:**
```bash
# تحقق من المنفذ
telnet smtp.office365.com 587

# إذا لم يعمل، جرب المنفذ 25
MAIL_PORT=25
```

### ❌ ما زال يطلب "Approve sign in"
**السبب:** تستخدم كلمة المرور العادية
**الحل:** استخدم App Password فقط!

---

## 🎯 بدائل أخرى (في حالة استمرار المشكلة)

### 1. Gmail SMTP
```env
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=yourevents@gmail.com
MAIL_PASSWORD=app-password-16-chars
```

### 2. SendGrid (مجاني حتى 100 إيميل/يوم)
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.sendgrid.net
MAIL_PORT=587
MAIL_USERNAME=apikey
MAIL_PASSWORD=your-sendgrid-api-key
```

### 3. Amazon SES (رخيص جداً)
- $0.10 لكل 1000 إيميل
- موثوق جداً
- يحتاج تسجيل في AWS

---

## 📞 الدعم الفني

إذا واجهت أي مشكلة:
- **Email:** support@yourevents.sa
- **Microsoft Support:** https://support.microsoft.com
- **صفحة الاختبار:** https://yourevents.sa/admin/email-test

---

**آخر تحديث:** 23 أكتوبر 2025
**تم الحل بواسطة:** GitHub Copilot ✅

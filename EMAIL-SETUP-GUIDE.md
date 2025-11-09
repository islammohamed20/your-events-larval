# دليل إعداد البريد الإلكتروني - Outlook SMTP

## المشكلة
عند استخدام حساب Outlook/Microsoft 365 لإرسال الإيميلات، يطلب Microsoft موافقة على كل محاولة تسجيل دخول (Approve sign in request)، مما يمنع الموقع من إرسال الإيميلات تلقائياً.

## الحل: استخدام App Password

### الخطوة 1️⃣: تفعيل المصادقة الثنائية (Two-Factor Authentication)

1. اذهب إلى: https://account.microsoft.com/security
2. سجل دخول بالحساب: `sales@yourevents.sa`
3. ابحث عن **"Two-step verification"** أو **"التحقق بخطوتين"**
4. اضغط **"Turn on"** لتفعيلها
5. اتبع الخطوات لربط رقم الهاتف أو تطبيق Authenticator

### الخطوة 2️⃣: إنشاء App Password

1. بعد تفعيل 2FA، ارجع إلى: https://account.microsoft.com/security
2. اضغط على **"Advanced security options"** (خيارات الأمان المتقدمة)
3. ابحث عن **"App passwords"** (كلمات مرور التطبيق)
4. اضغط **"Create a new app password"**
5. أدخل اسم للتطبيق (مثل: "YourEvents Website")
6. سيظهر لك كود من 16 حرف مثل: `abcd-efgh-ijkl-mnop`
7. **انسخ هذا الكود فوراً** (لن تستطيع رؤيته مرة أخرى)

### الخطوة 3️⃣: تحديث ملف .env

افتح ملف `.env` في مجلد المشروع وحدث القيم التالية:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.office365.com
MAIL_PORT=587
MAIL_USERNAME=sales@yourevents.sa
MAIL_PASSWORD=abcd-efgh-ijkl-mnop
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="sales@yourevents.sa"
MAIL_FROM_NAME="${APP_NAME}"
```

**ملاحظة مهمة**: استبدل `abcd-efgh-ijkl-mnop` بكلمة المرور الفعلية التي حصلت عليها من الخطوة 2

### الخطوة 4️⃣: مسح الـ Cache وإعادة تشغيل الخادم

```bash
cd /var/www/your-events
php artisan config:clear
php artisan cache:clear
```

### الخطوة 5️⃣: اختبار إرسال البريد

يمكنك اختبار إرسال البريد من خلال:

```bash
php artisan tinker
```

ثم اكتب:

```php
Mail::raw('This is a test email from YourEvents', function ($message) {
    $message->to('test@example.com')
            ->subject('Test Email');
});
```

---

## معلومات إضافية

### إعدادات Outlook SMTP المستخدمة:

| الإعداد | القيمة |
|--------|--------|
| **SMTP Server** | smtp.office365.com |
| **Port** | 587 |
| **Encryption** | TLS |
| **Username** | sales@yourevents.sa |
| **Password** | App Password (16 حرف) |

### بدائل أخرى (في حالة استمرار المشكلة):

#### 1. **Gmail SMTP**
- أسهل في الإعداد
- يدعم App Passwords بشكل أفضل
- SMTP: smtp.gmail.com:587

#### 2. **SendGrid** (مجاني حتى 100 إيميل/يوم)
- خدمة احترافية لإرسال الإيميلات
- لا يحتاج App Password
- موثوق أكثر

#### 3. **Amazon SES**
- رخيص جداً ($0.10 لكل 1000 إيميل)
- قابل للتوسع
- احترافي

---

## استكشاف الأخطاء

### ❌ خطأ: "Authentication failed"
- تأكد من أن App Password صحيح (16 حرف)
- تأكد من تفعيل 2FA على الحساب
- تأكد من عدم وجود مسافات في App Password

### ❌ خطأ: "Connection timeout"
- تأكد من Port 587 مفتوح على السيرفر
- جرب Port 25 بدلاً من 587
- تأكد من أن الفايروول لا يحجب الاتصال

### ❌ ما زال يطلب "Approve sign in request"
- تأكد من استخدام App Password وليس كلمة المرور العادية
- تأكد من أن 2FA مفعل
- حاول إنشاء App Password جديد

---

## جهة الاتصال

إذا واجهت أي مشكلة، تواصل مع:
- **الدعم الفني**: support@yourevents.sa
- **Microsoft Support**: https://support.microsoft.com

---

**آخر تحديث**: 23 أكتوبر 2025

# 📧 حل مشكلة إرسال البريد الإلكتروني

## المشكلة
```
❌ Approve sign in request
```

## الحل السريع

### 1️⃣ إنشاء App Password

اذهب إلى: https://account.microsoft.com/security
- سجل دخول بـ `sales@yourevents.sa`
- فعّل "التحقق بخطوتين" (2FA)
- أنشئ "كلمة مرور التطبيق" (App Password)
- انسخ الكود المكون من 16 حرف

### 2️⃣ تحديث الإعدادات

**خيار A: استخدام السكريبت التلقائي**
```bash
cd /var/www/your-events
./configure-email.sh
```

**خيار B: يدوياً**
افتح `.env` وعدّل:
```env
MAIL_HOST=smtp.office365.com
MAIL_PORT=587
MAIL_USERNAME=sales@yourevents.sa
MAIL_PASSWORD=your-16-char-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="sales@yourevents.sa"
```

ثم:
```bash
php artisan config:clear
```

### 3️⃣ اختبار الإعدادات

**من لوحة التحكم:**
https://yourevents.sa/admin/email-test

**من Terminal:**
```bash
./configure-email.sh
# اختر 'y' لإرسال بريد تجريبي
```

## 📚 ملفات المساعدة

- `OUTLOOK-SMTP-SOLUTION.md` - دليل سريع
- `EMAIL-SETUP-GUIDE.md` - دليل مفصل
- `configure-email.sh` - سكريبت تلقائي

## ✅ متى تعرف أن الإعدادات صحيحة؟

عند إرسال بريد تجريبي:
- ✅ لا يطلب "Approve sign in request"
- ✅ يصل البريد للمستلم خلال ثواني
- ✅ لا توجد أخطاء في logs

## 🆘 الدعم

إذا واجهت مشكلة، راجع:
1. `OUTLOOK-SMTP-SOLUTION.md` - حلول الأخطاء الشائعة
2. صفحة الاختبار: `/admin/email-test`
3. الـ logs: `storage/logs/laravel.log`

---

**تم التحديث:** 23 أكتوبر 2025

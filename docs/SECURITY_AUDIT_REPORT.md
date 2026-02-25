# 🔒 تقرير التدقيق الأمني الشامل
## Your Events - Laravel Application Security Audit
**تاريخ التدقيق:** January 18, 2026

---

## 📋 Executive Summary

| المعيار | الحالة |
|---------|--------|
| **التطبيق** | Laravel 11 (PHP 8.2.29) |
| **الخادم** | Ubuntu 24.04 + Apache 2.4.58 |
| **قاعدة البيانات** | MariaDB (محلي) |
| **مستوى الأمان العام** | ⚠️ **متوسط - يحتاج تحسينات** |

### ملخص النتائج:
| المستوى | العدد |
|---------|-------|
| 🔴 Critical | 3 |
| 🟠 High | 5 |
| 🟡 Medium | 6 |
| 🟢 Low | 4 |

---

## 🔴 Critical Findings (يجب الإصلاح فوراً)

### 1. APP_ENV=local في Production
**الموقع:** `.env`
```
APP_ENV=local  ❌
```

**التأثير:** 
- قد يتم عرض معلومات حساسة في رسائل الخطأ
- Laravel يعامل البيئة كبيئة تطوير

**الحل:**
```bash
# تغيير في .env
APP_ENV=production
```

---

### 2. ملف SQL Database في Web Directory
**الموقع:** `/var/www/your-events/your-events-database.sql`

**التأثير:**
- يمكن الوصول إليه عبر الويب مباشرة
- يحتوي على هيكل قاعدة البيانات وربما بيانات حساسة

**الحل:**
```bash
# نقل الملف خارج مجلد الويب
mv /var/www/your-events/your-events-database.sql /root/backups/
# أو حذفه إذا لم يكن مطلوباً
rm /var/www/your-events/your-events-database.sql
```

---

### 3. SSH Root Login مُفعّل
**الموقع:** `/etc/ssh/sshd_config`
```
PermitRootLogin yes  ❌
```

**التأثير:**
- يسمح بهجمات brute-force مباشرة على حساب root
- خطر أمني كبير

**الحل:**
```bash
# إنشاء مستخدم جديد بصلاحيات sudo
adduser admin_user
usermod -aG sudo admin_user

# تعديل /etc/ssh/sshd_config
PermitRootLogin no
PasswordAuthentication no

# إعادة تشغيل SSH
systemctl restart sshd
```

---

## 🟠 High Findings

### 4. SESSION_ENCRYPT=false
**الموقع:** `.env`

**التأثير:** بيانات الجلسة غير مشفرة في قاعدة البيانات

**الحل:**
```env
SESSION_ENCRYPT=true
```

---

### 5. API Routes بدون Rate Limiting
**الموقع:** `routes/api.php`

**التأثير:**
- يمكن إساءة استخدام الـ API
- هجمات DDoS و brute-force

**الحل:**
```php
// في bootstrap/app.php أو RouteServiceProvider
Route::middleware(['throttle:60,1'])->group(function () {
    // API routes
});
```

---

### 6. n8n Webhook على localhost
**الموقع:** `.env`
```
N8N_WEBHOOK_URL=http://localhost:5678/webhook/quote-created
```

**التأثير:**
- Port 5678 مفتوح للخارج في UFW
- n8n يمكن الوصول إليه من الإنترنت

**الحل:**
```bash
# إغلاق المنفذ من الخارج
ufw delete allow 5678/tcp
ufw delete allow 5678/udp

# إذا كان مطلوباً الوصول الخارجي، استخدم reverse proxy مع مصادقة
```

---

### 7. exec() في SettingsController
**الموقع:** `app/Http/Controllers/Admin/SettingsController.php:433`

**التأثير:** استخدام `exec()` لتنفيذ mysqldump - ثغرة محتملة للـ Command Injection

**الحل:**
```php
// استخدم Laravel Backup package بدلاً من exec
composer require spatie/laravel-backup

// أو تأكد من تعقيم المدخلات
$database = escapeshellarg(config('database.connections.mysql.database'));
```

---

### 8. FTP Port مفتوح (21)
**التأثير:** FTP غير آمن (النقل بنص واضح)

**الحل:**
```bash
# استخدم SFTP بدلاً من FTP
ufw delete allow 21/tcp

# أو استخدم FTPS
```

---

## 🟡 Medium Findings

### 9. .env Permissions (644)
**الحالي:** `-rw-r--r-- (644)`

**المشكلة:** يمكن قراءة الملف من قبل جميع المستخدمين

**الحل:**
```bash
chmod 600 /var/www/your-events/.env
```

---

### 10. CSP يسمح بـ unsafe-inline و unsafe-eval
**الموقع:** `app/Http/Middleware/SecurityHeaders.php`

**الحل:** تقليل استخدام unsafe بقدر الإمكان أو استخدام nonces

---

### 11. LOG_LEVEL=debug في Production

**الحل:**
```env
LOG_LEVEL=warning
```

---

### 12. fail2ban غير مثبت

**الحل:**
```bash
apt install fail2ban
systemctl enable fail2ban
systemctl start fail2ban

# إنشاء /etc/fail2ban/jail.local
[sshd]
enabled = true
port = 22
maxretry = 3
bantime = 3600
```

---

### 13. Apache بدون HTTPS Redirect

**الحل في VirtualHost:**
```apache
<VirtualHost *:80>
    ServerName yourevents.sa
    Redirect permanent / https://yourevents.sa/
</VirtualHost>
```

---

### 14. VNC Port مفتوح (5900)

**الحل:**
```bash
# إذا لم يكن مطلوباً
systemctl stop lightdm
ufw deny 5900
```

---

## 🟢 Low Findings

### 15. ملفات .md كثيرة في root directory
- 50+ ملف توثيق في مجلد التطبيق
- نقلها إلى `/docs`

### 16. Timezone UTC بدلاً من Asia/Riyadh
```env
APP_TIMEZONE=Asia/Riyadh
```

### 17. CSRF Protection جيد ✅
- 182 @csrf في 156 form

### 18. Mass Assignment Protection جيد ✅
- جميع Models تستخدم `$fillable`

---

## ✅ Laravel-Specific Positive Findings

| الميزة | الحالة |
|--------|--------|
| CSRF Protection | ✅ مُفعّل |
| Mass Assignment | ✅ محمي |
| Input Validation | ✅ مُستخدم |
| Security Headers | ✅ موجود |
| Session Secure Cookie | ✅ true |
| HTTP Only Cookies | ✅ true |
| Database Binding | ✅ 127.0.0.1 |

---

## 🛡️ Server Hardening Checklist

| المهمة | الحالة | الأولوية |
|--------|--------|----------|
| Disable Root SSH | ❌ | Critical |
| Install fail2ban | ❌ | High |
| Enable UFW | ✅ | - |
| Close unnecessary ports | ⚠️ | High |
| SSL/TLS configured | ⚠️ | High |
| MySQL local only | ✅ | - |
| Regular updates | ⚠️ | Medium |
| Log rotation | ⚠️ | Medium |

---

## 📅 Priority Fix Roadmap

### 🔥 Week 1 (Days 1-7) - Critical
```bash
# 1. Fix APP_ENV
sed -i 's/APP_ENV=local/APP_ENV=production/' /var/www/your-events/.env

# 2. Remove database dump
rm /var/www/your-events/your-events-database.sql

# 3. Disable root SSH (بعد إنشاء مستخدم بديل!)
# تأكد من وجود مفتاح SSH أولاً

# 4. Enable session encryption
sed -i 's/SESSION_ENCRYPT=false/SESSION_ENCRYPT=true/' /var/www/your-events/.env

# 5. Fix .env permissions
chmod 600 /var/www/your-events/.env
```

### ⚠️ Month 1 (Days 8-30) - High
```bash
# 1. Install fail2ban
apt update && apt install -y fail2ban

# 2. Close n8n external access
ufw delete allow 5678/tcp
ufw delete allow 5678/udp

# 3. Add rate limiting to API
# في config/app.php أو middleware

# 4. Configure SSL properly
certbot --apache -d yourevents.sa

# 5. Change LOG_LEVEL
sed -i 's/LOG_LEVEL=debug/LOG_LEVEL=warning/' /var/www/your-events/.env
```

### 📋 Quarter 1 (Days 31-90) - Medium/Low
1. Implement automated backups
2. Set up monitoring (Prometheus/Grafana)
3. Configure log aggregation
4. Review and clean CSP policy
5. Move documentation files
6. Implement 2FA for admin

---

## 🔧 Quick Fix Script

```bash
#!/bin/bash
# security-fixes.sh - تشغيل بحذر!

echo "Starting security fixes..."

# Fix .env
cd /var/www/your-events
sed -i 's/APP_ENV=local/APP_ENV=production/' .env
sed -i 's/SESSION_ENCRYPT=false/SESSION_ENCRYPT=true/' .env
sed -i 's/LOG_LEVEL=debug/LOG_LEVEL=warning/' .env
chmod 600 .env

# Remove sensitive file
[ -f your-events-database.sql ] && mv your-events-database.sql /root/

# Clear caches
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Install fail2ban
apt update && apt install -y fail2ban
systemctl enable fail2ban
systemctl start fail2ban

echo "Basic fixes applied. Review SSH config manually!"
```

---

## 📌 ملاحظات إضافية

### النسخ الاحتياطي
لا يوجد نظام نسخ احتياطي واضح - يُنصح بـ:
```bash
composer require spatie/laravel-backup
```

### المراقبة
لا يوجد نظام مراقبة - يُنصح بـ Laravel Telescope أو Sentry

### تحديثات الأمان
```bash
apt update && apt upgrade -y
composer update --no-dev
```

---

## 📊 OWASP Top 10 Assessment

| الثغرة | الحالة | ملاحظات |
|--------|--------|---------|
| A01:2021 - Broken Access Control | ⚠️ | يحتاج مراجعة policies |
| A02:2021 - Cryptographic Failures | ⚠️ | SESSION_ENCRYPT=false |
| A03:2021 - Injection | ✅ | Eloquent ORM يحمي |
| A04:2021 - Insecure Design | ⚠️ | exec() في backup |
| A05:2021 - Security Misconfiguration | ❌ | APP_ENV=local |
| A06:2021 - Vulnerable Components | ⚠️ | تحقق من composer audit |
| A07:2021 - Auth Failures | ✅ | Laravel auth جيد |
| A08:2021 - Software Integrity | ✅ | - |
| A09:2021 - Logging Failures | ⚠️ | LOG_LEVEL=debug |
| A10:2021 - SSRF | ✅ | لا توجد مشاكل واضحة |

---

## 🔐 Firewall Rules Review

### المنافذ المفتوحة حالياً:
| المنفذ | الخدمة | الحالة | التوصية |
|--------|--------|--------|---------|
| 22 | SSH | ✅ | مطلوب |
| 80 | HTTP | ✅ | إعادة توجيه لـ HTTPS |
| 443 | HTTPS | ✅ | مطلوب |
| 21 | FTP | ⚠️ | استخدم SFTP |
| 5678 | n8n | ❌ | إغلاق من الخارج |
| 5900 | VNC | ❌ | إغلاق |
| 38579 | Unknown | ⚠️ | مراجعة |
| 39000-40000 | FTP Passive | ⚠️ | إغلاق إذا لم يكن مطلوباً |

---

## ✍️ توقيع التدقيق

- **المدقق:** AI Security Assistant
- **التاريخ:** January 18, 2026
- **الإصدار:** 1.0
- **المراجعة القادمة:** April 2026

---

> ⚠️ **تنبيه:** هذا التقرير يحتوي على معلومات حساسة. يجب حفظه في مكان آمن وعدم مشاركته علناً.

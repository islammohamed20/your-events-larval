# 🚀 دليل سريع لتفعيل SSL لـ yourevents.sa

## ✅ المتطلبات (تحقق أولاً)

```bash
# 1. تحقق من DNS
ping yourevents.sa
# يجب أن يظهر: 72.61.154.100

# 2. تحقق من Apache يعمل
sudo systemctl status apache2
```

---

## 🎯 الطريقة السريعة (Automated)

### خطوة واحدة فقط:

```bash
cd /var/www/your-events
sudo ./install-ssl.sh
```

**هذا سيقوم بـ:**
1. ✓ فحص DNS
2. ✓ تثبيت Certbot
3. ✓ إعداد Apache
4. ✓ الحصول على SSL certificate
5. ✓ تحديث Laravel config
6. ✓ مسح الـ cache

**الوقت المتوقع:** 3-5 دقائق

---

## 📋 الطريقة اليدوية (Manual)

إذا كنت تفضل التحكم الكامل:

### 1. إعداد DNS (في لوحة تحكم الدومين)

```
Type    Name    Value               TTL
A       @       72.61.154.100       3600
A       www     72.61.154.100       3600
```

### 2. تثبيت Certbot

```bash
sudo apt update
sudo apt install certbot python3-certbot-apache -y
```

### 3. إعداد Apache

```bash
sudo cp /var/www/your-events/yourevents.sa.conf /etc/apache2/sites-available/
sudo a2ensite yourevents.sa.conf
sudo a2enmod rewrite ssl headers
sudo systemctl restart apache2
```

### 4. الحصول على SSL

```bash
sudo certbot --apache -d yourevents.sa -d www.yourevents.sa
```

**الإجابات:**
- Email: `admin@yourevents.sa`
- Agree to Terms: `Y`
- Share email: `N`
- Redirect HTTP to HTTPS: `2` (نعم)

### 5. تحديث Laravel

```bash
cd /var/www/your-events
nano .env
```

عدّل:
```env
APP_URL=https://yourevents.sa
SESSION_DOMAIN=.yourevents.sa
SESSION_SECURE_COOKIE=true
```

```bash
php artisan config:clear
php artisan cache:clear
php artisan config:cache
```

---

## 🧪 الاختبار

```bash
# 1. افتح في المتصفح
https://yourevents.sa

# 2. تحقق من القفل الأخضر

# 3. اختبار SSL Quality
# https://www.ssllabs.com/ssltest/analyze.html?d=yourevents.sa
```

---

## 🔄 التجديد التلقائي

```bash
# إعداد Cron Job
sudo crontab -e

# أضف السطر:
0 3 * * * certbot renew --quiet --post-hook "systemctl reload apache2"
```

---

## 🆘 حل المشاكل

### DNS لا يعمل؟
```bash
# انتظر 30 دقيقة وجرب مرة أخرى
ping yourevents.sa
```

### Certbot يفشل؟
```bash
# تأكد من Ports مفتوحة
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp

# جرب مرة أخرى
sudo certbot --apache -d yourevents.sa -d www.yourevents.sa
```

### الموقع لا يعمل؟
```bash
# تحقق من Apache logs
sudo tail -f /var/log/apache2/error.log

# تحقق من Laravel logs
tail -f storage/logs/laravel.log
```

---

## ✅ Checklist

```
□ DNS configured (@ and www point to server)
□ DNS propagated (ping works)
□ Certbot installed
□ Apache Virtual Host configured
□ SSL certificate obtained
□ HTTPS redirect working
□ Laravel .env updated
□ Cache cleared
□ Website loads on HTTPS
□ No mixed content warnings
□ Auto-renewal configured
```

---

## 📞 الدعم

للمساعدة، راجع:
- **SSL_INSTALLATION_GUIDE.md** - دليل شامل مفصل
- **Laravel Logs:** `storage/logs/laravel.log`
- **Apache Logs:** `/var/log/apache2/error.log`

---

**الدومين:** yourevents.sa  
**السيرفر:** 72.61.154.100  
**التاريخ:** 15 أكتوبر 2025

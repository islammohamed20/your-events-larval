# 🔐 دليل تفعيل SSL/HTTPS لـ yourevents.sa
## SSL Installation Guide with Let's Encrypt

تاريخ: 15 أكتوبر 2025
الدومين: **yourevents.sa**

---

## ✅ المتطلبات الأساسية

```
✓ Domain: yourevents.sa (تم الشراء)
✓ Server: 72.61.154.100
✓ Web Server: Apache
✓ Laravel Project: /var/www/your-events
```

---

## 📋 الخطوات الكاملة

### المرحلة 1️⃣: تحديث DNS Records

#### الخطوة 1: إعداد DNS في لوحة تحكم الدومين

اذهب إلى لوحة تحكم الدومين وأضف السجلات التالية:

```
Type    Name    Value               TTL
----    ----    -----               ---
A       @       72.61.154.100       3600
A       www     72.61.154.100       3600
```

**ملاحظة:** انتظر من 5-30 دقيقة حتى ينتشر DNS

#### الخطوة 2: التحقق من DNS

```bash
# تحقق من أن الدومين يشير للسيرفر
ping yourevents.sa
ping www.yourevents.sa

# يجب أن يظهر: 72.61.154.100
```

---

### المرحلة 2️⃣: تثبيت Certbot (Let's Encrypt)

```bash
# تحديث النظام
sudo apt update

# تثبيت Certbot و Apache plugin
sudo apt install certbot python3-certbot-apache -y

# التحقق من التثبيت
certbot --version
```

---

### المرحلة 3️⃣: إعداد Apache Virtual Host للدومين

#### الخطوة 1: نسخ الإعدادات الحالية

```bash
# نسخة احتياطية من الإعداد الحالي
sudo cp /etc/apache2/sites-available/apache-your-events.conf /etc/apache2/sites-available/apache-your-events.conf.backup

# إنشاء إعداد جديد للدومين
sudo nano /etc/apache2/sites-available/yourevents.sa.conf
```

#### الخطوة 2: محتوى الملف الجديد

```apache
<VirtualHost *:80>
    ServerName yourevents.sa
    ServerAlias www.yourevents.sa
    ServerAdmin admin@yourevents.sa

    DocumentRoot /var/www/your-events/public

    <Directory /var/www/your-events/public>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    # Laravel logs
    ErrorLog ${APACHE_LOG_DIR}/yourevents-error.log
    CustomLog ${APACHE_LOG_DIR}/yourevents-access.log combined

    # Environment
    SetEnv APP_ENV production
    SetEnv APP_DEBUG false
</VirtualHost>
```

احفظ الملف (Ctrl+O ثم Enter ثم Ctrl+X)

#### الخطوة 3: تفعيل الموقع

```bash
# تعطيل الإعداد القديم (اختياري)
sudo a2dissite apache-your-events.conf

# تفعيل الإعداد الجديد
sudo a2ensite yourevents.sa.conf

# تفعيل mod_rewrite و mod_ssl
sudo a2enmod rewrite
sudo a2enmod ssl

# إعادة تشغيل Apache
sudo systemctl restart apache2

# التحقق من الحالة
sudo systemctl status apache2
```

#### الخطوة 4: التحقق من عمل الدومين

```bash
# افتح في المتصفح
http://yourevents.sa
http://www.yourevents.sa

# يجب أن يظهر الموقع بدون SSL
```

---

### المرحلة 4️⃣: تثبيت SSL Certificate

#### الخطوة 1: الحصول على الشهادة

```bash
# الطريقة الأوتوماتيكية (مُوصى بها)
sudo certbot --apache -d yourevents.sa -d www.yourevents.sa

# أو الطريقة اليدوية
sudo certbot certonly --apache -d yourevents.sa -d www.yourevents.sa
```

#### الخطوة 2: الإجابة على الأسئلة

```
1. Enter email address: admin@yourevents.sa
2. Agree to Terms of Service: Y
3. Share email with EFF: N (اختياري)
4. Redirect HTTP to HTTPS: 2 (نعم - اختر هذا)
```

**النتيجة المتوقعة:**
```
Congratulations! You have successfully enabled HTTPS
Your certificate and chain have been saved at:
/etc/letsencrypt/live/yourevents.sa/fullchain.pem
Your key file has been saved at:
/etc/letsencrypt/live/yourevents.sa/privkey.pem
```

---

### المرحلة 5️⃣: تحديث Laravel Configuration

#### الخطوة 1: تحديث .env

```bash
cd /var/www/your-events
nano .env
```

**عدّل الأسطر التالية:**

```env
APP_NAME="Your Events"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourevents.sa

# Session & Cookie
SESSION_DOMAIN=.yourevents.sa
SESSION_SECURE_COOKIE=true

# n8n webhook (إذا كان على نفس السيرفر)
N8N_WEBHOOK_URL=https://yourevents.sa:5678/webhook/quote-created
# أو
N8N_WEBHOOK_URL=http://localhost:5678/webhook/quote-created
```

احفظ الملف

#### الخطوة 2: تحديث config/app.php (اختياري)

```bash
nano config/app.php
```

تأكد من:
```php
'url' => env('APP_URL', 'https://yourevents.sa'),
```

#### الخطوة 3: مسح Cache

```bash
# مسح جميع الـ cache
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# إعادة بناء الـ cache
php artisan config:cache
php artisan route:cache
php artisan view:cache

# تحسين autoload
composer dump-autoload --optimize
```

#### الخطوة 4: إصلاح الصلاحيات

```bash
# تأكد من الصلاحيات الصحيحة
sudo chown -R www-data:www-data /var/www/your-events
sudo chmod -R 755 /var/www/your-events
sudo chmod -R 775 /var/www/your-events/storage
sudo chmod -R 775 /var/www/your-events/bootstrap/cache
```

---

### المرحلة 6️⃣: إعداد التجديد التلقائي للشهادة

Let's Encrypt certificates expire after 90 days. نحتاج لإعداد التجديد التلقائي:

#### الخطوة 1: اختبار التجديد

```bash
# اختبار dry-run (لا يغير شيء)
sudo certbot renew --dry-run
```

**النتيجة المتوقعة:**
```
Congratulations, all simulated renewals succeeded
```

#### الخطوة 2: Cron Job للتجديد التلقائي

```bash
# فتح crontab
sudo crontab -e

# إضافة السطر التالي في نهاية الملف:
0 3 * * * certbot renew --quiet --post-hook "systemctl reload apache2"
```

هذا سيفحص ويجدد الشهادة يومياً الساعة 3 صباحاً

---

### المرحلة 7️⃣: تحديث Apache SSL Configuration (اختياري - للأمان المتقدم)

```bash
sudo nano /etc/apache2/sites-available/yourevents.sa-le-ssl.conf
```

**أضف هذه الإعدادات للأمان:**

```apache
<VirtualHost *:443>
    ServerName yourevents.sa
    ServerAlias www.yourevents.sa
    ServerAdmin admin@yourevents.sa

    DocumentRoot /var/www/your-events/public

    <Directory /var/www/your-events/public>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/yourevents-ssl-error.log
    CustomLog ${APACHE_LOG_DIR}/yourevents-ssl-access.log combined

    # SSL Configuration
    SSLEngine on
    SSLCertificateFile /etc/letsencrypt/live/yourevents.sa/fullchain.pem
    SSLCertificateKeyFile /etc/letsencrypt/live/yourevents.sa/privkey.pem

    # Security Headers
    Header always set Strict-Transport-Security "max-age=31536000; includeSubDomains"
    Header always set X-Frame-Options "SAMEORIGIN"
    Header always set X-Content-Type-Options "nosniff"
    Header always set X-XSS-Protection "1; mode=block"
    Header always set Referrer-Policy "strict-origin-when-cross-origin"

    # Modern SSL Configuration
    SSLProtocol all -SSLv2 -SSLv3 -TLSv1 -TLSv1.1
    SSLCipherSuite HIGH:!aNULL:!MD5:!3DES
    SSLHonorCipherOrder on
    SSLCompression off
    SSLSessionTickets off

    # OCSP Stapling
    SSLUseStapling on
    SSLStaplingResponderTimeout 5
    SSLStaplingReturnResponderErrors off
</VirtualHost>
```

```bash
# تفعيل headers module
sudo a2enmod headers

# إعادة تشغيل Apache
sudo systemctl restart apache2
```

---

### المرحلة 8️⃣: إعداد Force HTTPS Redirect

تأكد من أن الموقع دائماً يستخدم HTTPS:

#### في .htaccess

```bash
nano /var/www/your-events/public/.htaccess
```

**أضف في البداية (قبل Laravel rules):**

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    
    # Force HTTPS
    RewriteCond %{HTTPS} off
    RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
    
    # Force www to non-www (or vice versa)
    RewriteCond %{HTTP_HOST} ^www\.(.*)$ [NC]
    RewriteRule ^(.*)$ https://%1/$1 [R=301,L]
    
    # Laravel rules
    # ...existing rules...
</IfModule>
```

---

## 🧪 الاختبار الكامل

### 1. اختبار SSL Certificate

```bash
# في Terminal
openssl s_client -connect yourevents.sa:443 -servername yourevents.sa

# يجب أن يظهر:
# Verify return code: 0 (ok)
```

### 2. اختبار في المتصفح

افتح المتصفح وجرب:

```
✓ https://yourevents.sa
✓ https://www.yourevents.sa
✓ http://yourevents.sa (يجب أن يتحول تلقائياً لـ https)
✓ http://www.yourevents.sa (يجب أن يتحول تلقائياً لـ https)
```

**تحقق من:**
- ✓ قفل أخضر في شريط العنوان
- ✓ Certificate valid
- ✓ No mixed content warnings
- ✓ الموقع يعمل بشكل طبيعي

### 3. اختبار SSL Quality

```bash
# استخدم SSL Labs للفحص الشامل
https://www.ssllabs.com/ssltest/analyze.html?d=yourevents.sa
```

**الهدف:** الحصول على تقييم **A** أو **A+**

### 4. اختبار Laravel Routes

```bash
# في Terminal
curl -I https://yourevents.sa
curl -I https://yourevents.sa/services
curl -I https://yourevents.sa/login
```

يجب أن تعمل جميع الروابط بدون مشاكل

---

## 🔍 Troubleshooting

### مشكلة 1: DNS لا يزال يشير للـ IP القديم

**الحل:**
```bash
# انتظر 30 دقيقة للـ propagation
# تحقق من DNS
dig yourevents.sa
nslookup yourevents.sa
```

### مشكلة 2: Certbot يفشل في الحصول على الشهادة

**الحل:**
```bash
# تأكد من أن الدومين يعمل على HTTP أولاً
curl http://yourevents.sa

# تأكد من أن Port 80 و 443 مفتوحة
sudo ufw status
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp

# جرب مرة أخرى
sudo certbot --apache -d yourevents.sa -d www.yourevents.sa
```

### مشكلة 3: Mixed Content (HTTP + HTTPS)

**الحل:**
```bash
# في Laravel
# تأكد من استخدام asset() بدلاً من hardcoded URLs

# في .env
APP_URL=https://yourevents.sa

# مسح cache
php artisan config:clear
php artisan view:clear
```

### مشكلة 4: Apache لا يعيد التشغيل

**الحل:**
```bash
# تحقق من الأخطاء
sudo apache2ctl configtest

# إصلاح الأخطاء ثم
sudo systemctl restart apache2

# فحص logs
sudo tail -f /var/log/apache2/error.log
```

---

## 📊 ملخص الأوامر (Quick Reference)

```bash
# 1. تثبيت Certbot
sudo apt update && sudo apt install certbot python3-certbot-apache -y

# 2. إنشاء Virtual Host
sudo nano /etc/apache2/sites-available/yourevents.sa.conf

# 3. تفعيل الموقع
sudo a2ensite yourevents.sa.conf
sudo a2enmod rewrite ssl headers
sudo systemctl restart apache2

# 4. الحصول على SSL
sudo certbot --apache -d yourevents.sa -d www.yourevents.sa

# 5. تحديث Laravel
cd /var/www/your-events
nano .env  # عدّل APP_URL و SESSION_DOMAIN
php artisan config:clear && php artisan cache:clear

# 6. اختبار
curl -I https://yourevents.sa
```

---

## ✅ Checklist النهائي

```
✓ DNS يشير للسيرفر الصحيح (72.61.154.100)
✓ Apache Virtual Host معد للدومين
✓ Let's Encrypt SSL certificate مثبت
✓ HTTPS redirect يعمل (HTTP → HTTPS)
✓ www redirect يعمل (www → non-www أو العكس)
✓ Laravel .env محدّث بالدومين الجديد
✓ Cache تم مسحه
✓ الصلاحيات صحيحة
✓ Cron job للتجديد التلقائي معد
✓ Security headers معدة
✓ الموقع يعمل على HTTPS
✓ لا توجد mixed content warnings
✓ SSL Labs تقييم A أو A+
```

---

## 🎉 النتيجة النهائية

بعد إكمال جميع الخطوات:

```
✅ https://yourevents.sa - يعمل بأمان
✅ https://www.yourevents.sa - يعمل بأمان
✅ http:// تتحول تلقائياً لـ https://
✅ شهادة SSL صالحة لمدة 90 يوم
✅ تجديد تلقائي كل 90 يوم
✅ Security headers مفعّلة
✅ Laravel يعمل بشكل صحيح على الدومين الجديد
```

---

**تاريخ التوثيق:** 15 أكتوبر 2025
**الدومين:** yourevents.sa
**السيرفر:** 72.61.154.100
**الحالة:** جاهز للتنفيذ

---

## 📚 مصادر إضافية

- [Let's Encrypt Documentation](https://letsencrypt.org/docs/)
- [Certbot Documentation](https://certbot.eff.org/)
- [Apache SSL/TLS Configuration](https://httpd.apache.org/docs/2.4/ssl/)
- [Laravel Deployment](https://laravel.com/docs/deployment)
- [SSL Labs Testing](https://www.ssllabs.com/ssltest/)

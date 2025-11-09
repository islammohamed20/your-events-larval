# your-events (Laravel)

مشروع لإدارة الخدمات، العروض (Quotes)، والحجوزات بلغة PHP باستخدام إطار Laravel.

## المتطلبات
- PHP 8.1 أو أحدث (مع التمديدات: `mbstring`, `openssl`, `pdo_mysql`, `curl`, `json`)
- Composer 2.x
- MySQL/MariaDB (قاعدة بيانات فارغة)
- Node.js 18+ و npm (اختياري للأصول الأمامية)
- OpenSSL (لإنشاء مفتاح التطبيق)

## تنزيل المشروع
- باستخدام Git:
  
  ```bash
  git clone https://github.com/islammohamed20/your-events-larval.git
  cd your-events-larval
  ```

- أو تنزيل كملف ZIP من صفحة GitHub وفك الضغط ثم الدخول للمجلد.

## التثبيت والإعداد
1) تثبيت الاعتمادات عبر Composer:
   
   ```bash
   composer install
   ```

2) إنشاء ملف البيئة `.env` ونسخ الإعدادات الافتراضية:
   
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

3) ضبط اتصال قاعدة البيانات داخل `.env`:
   
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=your_events
   DB_USERNAME=root
   DB_PASSWORD=secret
   ```

4) تشغيل المهاجرات (وإن رغبت، البذور):
   
   ```bash
   php artisan migrate
   # اختياري إن كانت هناك بذور
   php artisan db:seed
   ```

5) ربط التخزين العام:
   
   ```bash
   php artisan storage:link
   ```

6) (اختياري) تثبيت وبناء الأصول الأمامية:
   
   ```bash
   npm install
   npm run build
   # أو أثناء التطوير
   npm run dev
   ```

## تشغيل محليًا
- بدء الخادم التطويري:
  
  ```bash
  php artisan serve
  ```
  
  ثم افتح: `http://127.0.0.1:8000/`

## إعداد البريد الإلكتروني (اختياري)
- حدث قيم البريد داخل `.env` وفق مزودك:
  
  ```env
  MAIL_MAILER=smtp
  MAIL_HOST=smtp.example.com
  MAIL_PORT=587
  MAIL_USERNAME=you@example.com
  MAIL_PASSWORD=app_password_or_token
  MAIL_ENCRYPTION=tls
  MAIL_FROM_ADDRESS=you@example.com
  MAIL_FROM_NAME="Your Events"
  ```
- راجع `EMAIL-SETUP-GUIDE.md` و `OUTLOOK-SMTP-SOLUTION.md` للملاحظات التفصيلية.

## استيراد قاعدة بيانات جاهزة (اختياري)
- يتوفر ملف `your-events-database.sql` داخل الجذر. يمكنك استيراده إلى قاعدة بياناتك بدل تشغيل المهاجرات:
  
  ```bash
  mysql -u root -p your_events < your-events-database.sql
  ```

## حساب المدير (Admin)
- إن لم يكن هناك مستخدم مدير، أنشئ مستخدمًا ثم حدث الحقل `is_admin` إلى `1` داخل جدول `users`.
- تأكد من إعداد الصلاحيات وفق `PERMISSIONS.md` إن لزم.

## مسارات مهمة
- لوحة الإدارة: `/admin`
- عروض الإدارة: `/admin/quotes`
- العروض للمستخدم: `/quotes/{quote}` (يتطلب تسجيل الدخول)

## مشاكل شائعة
- إن ظهرت رسالة "dubious ownership" أثناء أوامر Git:
  
  ```bash
  git config --global --add safe.directory /path/to/your-events
  ```

- ولحل أخطاء التخزين أو الصلاحيات على Linux:
  
  ```bash
  sudo chown -R $USER:www-data storage bootstrap/cache
  sudo chmod -R 775 storage bootstrap/cache
  ```

## موارد إضافية
- إعداد SSL: `README_SSL.md`, `SSL_QUICK_START.md`
- نظام OTP: `OTP-QUICK-START.md`
- نظام العروض والدفع: `QUOTE_PAYMENT_SYSTEM.md`
- توثيق الخدمات المتغيرة: `VARIABLE_SERVICES_DOCUMENTATION.md`
- تحسينات الواجهة: `FRONTEND-IMPROVEMENTS.md`

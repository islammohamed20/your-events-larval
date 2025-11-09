# حل مشكلة الصلاحيات في Laravel

## المشكلة
```
Permission denied
file_put_contents(storage/framework/views/...): Failed to open stream
```

## الحل السريع
```bash
# تشغيل سكريبت إصلاح الصلاحيات
./fix-permissions.sh
```

## الحل اليدوي
```bash
# 1. تغيير المالك
sudo chown -R www-data:www-data /var/www/your-events

# 2. صلاحيات المجلدات
sudo find /var/www/your-events -type d -exec chmod 755 {} \;

# 3. صلاحيات الملفات
sudo find /var/www/your-events -type f -exec chmod 644 {} \;

# 4. صلاحيات خاصة
sudo chmod -R 775 /var/www/your-events/storage
sudo chmod -R 775 /var/www/your-events/bootstrap/cache

# 5. مسح الـ cache
php artisan cache:clear
php artisan view:clear
```

## متى تحدث المشكلة؟

### ❌ أسباب المشكلة:
1. رفع ملفات بمستخدم غير `www-data`
2. تشغيل أوامر `php artisan` بـ `sudo`
3. إنشاء ملفات يدوياً
4. بعد `git pull` أو `deployment`

### ✅ الوقاية:
1. **دائماً استخدم** `fix-permissions.sh` بعد أي تغيير
2. **لا تستخدم** `sudo php artisan` أبداً
3. **استخدم** `deploy.sh` عند الـ deployment

## الصلاحيات الصحيحة

| المجلد/الملف | المالك | الصلاحيات |
|--------------|--------|-----------|
| المشروع بالكامل | www-data:www-data | - |
| المجلدات | www-data:www-data | 755 |
| الملفات | www-data:www-data | 644 |
| storage/ | www-data:www-data | 775 |
| bootstrap/cache/ | www-data:www-data | 775 |
| artisan | www-data:www-data | 755 (تنفيذ) |

## الأوامر المفيدة

```bash
# فحص صلاحيات storage
ls -la storage/

# فحص من يملك المجلد
ls -ld storage/framework/views

# التحقق من مستخدم الـ web server
ps aux | grep apache
# أو
ps aux | grep nginx
```

## Cron Job للمراقبة (اختياري)

```bash
# تشغيل كل ساعة للتأكد من الصلاحيات
0 * * * * /var/www/your-events/fix-permissions.sh > /dev/null 2>&1
```

## بعد كل Deployment

```bash
./deploy.sh
```

هذا السكريبت سيقوم بـ:
- ✅ تحديث المكتبات
- ✅ إصلاح الصلاحيات تلقائياً
- ✅ تحسين Laravel
- ✅ تشغيل الترحيلات

---

## ملاحظات مهمة

⚠️ **لا تستخدم**:
```bash
sudo php artisan cache:clear  # ❌
sudo composer install          # ❌
```

✅ **استخدم**:
```bash
php artisan cache:clear        # ✅
composer install               # ✅
./fix-permissions.sh           # ✅ بعد أي تغيير
```

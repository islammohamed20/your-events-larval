#!/bin/bash
# Laravel Permissions Fix Script
# لإصلاح صلاحيات Laravel تلقائياً

echo "🔧 إصلاح صلاحيات Laravel..."

# تغيير المالك لكامل المشروع
echo "📁 تغيير المالك إلى www-data..."
sudo chown -R www-data:www-data /var/www/your-events

# تعيين صلاحيات المجلدات
echo "📂 تعيين صلاحيات المجلدات (755)..."
sudo find /var/www/your-events -type d -exec chmod 755 {} \;

# تعيين صلاحيات الملفات
echo "📄 تعيين صلاحيات الملفات (644)..."
sudo find /var/www/your-events -type f -exec chmod 644 {} \;

# صلاحيات خاصة لمجلدات storage و bootstrap/cache
echo "🔐 صلاحيات خاصة لـ storage و bootstrap/cache..."
sudo chmod -R 775 /var/www/your-events/storage
sudo chmod -R 775 /var/www/your-events/bootstrap/cache

# صلاحيات تنفيذ لـ artisan
echo "⚡ صلاحيات تنفيذ لـ artisan..."
sudo chmod +x /var/www/your-events/artisan

# مسح الـ cache
echo "🗑️  مسح الـ cache..."
cd /var/www/your-events
php artisan cache:clear
php artisan view:clear
php artisan config:clear
php artisan route:clear

echo "✅ تم إصلاح الصلاحيات بنجاح!"
echo "🎉 الموقع جاهز للعمل"

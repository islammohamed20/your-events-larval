#!/bin/bash
# Deployment Hook - يُنفذ بعد كل deployment

echo "🚀 Post-Deployment Script..."

# الانتقال لمجلد المشروع
cd /var/www/your-events

# تحديث المكتبات
echo "📦 Updating dependencies..."
composer install --no-dev --optimize-autoloader

# إصلاح الصلاحيات
echo "🔧 Fixing permissions..."
./fix-permissions.sh

# تحسين Laravel
echo "⚡ Optimizing Laravel..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# ترحيلات قاعدة البيانات
echo "🗄️  Running migrations..."
php artisan migrate --force

echo "✅ Deployment completed successfully!"

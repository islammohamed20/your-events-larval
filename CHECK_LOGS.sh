#!/bin/bash
# بعد محاولة إنشاء خاصية، قم بتشغيل هذا الأمر:
cd /var/www/your-events
echo "=== Laravel Logs ==="
tail -50 storage/logs/laravel.log

echo ""
echo "=== Recent Attributes in Database ==="
mysql -u root -p"r4SpGAaS@9zw3!" your_events -e "SELECT id, name, slug, type, is_active, created_at FROM attributes ORDER BY id DESC LIMIT 5"

echo ""
echo "=== Session Data ==="
mysql -u root -p"r4SpGAaS@9zw3!" your_events -e "SELECT id, user_id, ip_address, last_activity FROM sessions ORDER BY last_activity DESC LIMIT 3"

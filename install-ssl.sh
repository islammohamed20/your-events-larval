#!/usr/bin/env bash
set -euo pipefail

DOMAIN_ROOT="yourevents.sa"
DOMAIN_WWW="www.yourevents.sa"
EMAIL="${SSL_EMAIL:-admin@yourevents.sa}"
PROJECT_DIR="/var/www/your-events"
VHOST_SRC="${PROJECT_DIR}/yourevents.sa.conf"
VHOST_DST="/etc/apache2/sites-available/yourevents.sa.conf"

if [[ "$(id -u)" != "0" ]]; then
  echo "شغّل السكربت بصلاحيات root: sudo ./install-ssl.sh"
  exit 1
fi

if [[ ! -f "${VHOST_SRC}" ]]; then
  echo "ملف إعداد Apache غير موجود: ${VHOST_SRC}"
  exit 1
fi

if ! command -v apache2 >/dev/null 2>&1; then
  echo "Apache غير مثبت (apache2 غير موجود)."
  exit 1
fi

echo "تثبيت certbot..."
export DEBIAN_FRONTEND=noninteractive
apt-get update -y
apt-get install -y certbot python3-certbot-apache

echo "نسخ إعداد VirtualHost..."
cp -f "${VHOST_SRC}" "${VHOST_DST}"

echo "تفعيل موديلات Apache..."
a2enmod rewrite ssl headers proxy proxy_fcgi setenvif >/dev/null

echo "تفعيل الموقع..."
a2ensite yourevents.sa.conf >/dev/null || true

if [[ -f /etc/apache2/sites-enabled/000-default.conf ]]; then
  a2dissite 000-default.conf >/dev/null || true
fi

systemctl restart apache2
systemctl is-active --quiet apache2

echo "طلب شهادة SSL (Let's Encrypt) + تحويل HTTP إلى HTTPS..."
certbot --apache -d "${DOMAIN_ROOT}" -d "${DOMAIN_WWW}" --non-interactive --agree-tos -m "${EMAIL}" --redirect

echo "تحديث إعدادات Laravel (.env) إن وُجد..."
ENV_FILE="${PROJECT_DIR}/.env"
if [[ -f "${ENV_FILE}" ]]; then
  if grep -qE '^APP_URL=' "${ENV_FILE}"; then
    sed -i "s|^APP_URL=.*|APP_URL=https://${DOMAIN_ROOT}|g" "${ENV_FILE}"
  else
    printf "\nAPP_URL=https://%s\n" "${DOMAIN_ROOT}" >> "${ENV_FILE}"
  fi

  if grep -qE '^SESSION_DOMAIN=' "${ENV_FILE}"; then
    sed -i "s|^SESSION_DOMAIN=.*|SESSION_DOMAIN=.${DOMAIN_ROOT}|g" "${ENV_FILE}"
  else
    printf "SESSION_DOMAIN=.%s\n" "${DOMAIN_ROOT}" >> "${ENV_FILE}"
  fi

  if grep -qE '^SESSION_SECURE_COOKIE=' "${ENV_FILE}"; then
    sed -i "s|^SESSION_SECURE_COOKIE=.*|SESSION_SECURE_COOKIE=true|g" "${ENV_FILE}"
  else
    printf "SESSION_SECURE_COOKIE=true\n" >> "${ENV_FILE}"
  fi
fi

echo "مسح وإعادة بناء Cache..."
cd "${PROJECT_DIR}"
if command -v php >/dev/null 2>&1; then
  php artisan config:clear || true
  php artisan cache:clear || true
  php artisan route:clear || true
  php artisan view:clear || true
  php artisan config:cache || true
fi

echo "تم تفعيل SSL بنجاح."

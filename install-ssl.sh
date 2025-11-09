#!/bin/bash

# 🔐 SSL Installation Script for yourevents.sa
# This script automates SSL certificate installation with Let's Encrypt

set -e  # Exit on error

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Variables
DOMAIN="yourevents.sa"
WWW_DOMAIN="www.yourevents.sa"
EMAIL="admin@yourevents.sa"
PROJECT_PATH="/var/www/your-events"
APACHE_CONF="/etc/apache2/sites-available/yourevents.sa.conf"

echo -e "${BLUE}═══════════════════════════════════════════════${NC}"
echo -e "${GREEN}   SSL Installation Script for yourevents.sa${NC}"
echo -e "${BLUE}═══════════════════════════════════════════════${NC}"
echo ""

# Step 1: Check DNS
echo -e "${YELLOW}📡 Step 1/8: Checking DNS...${NC}"
if ping -c 1 $DOMAIN &> /dev/null; then
    echo -e "${GREEN}✓ DNS is resolving correctly${NC}"
else
    echo -e "${RED}✗ DNS is not resolving yet. Please wait and try again.${NC}"
    exit 1
fi

# Step 2: Update system
echo -e "${YELLOW}📦 Step 2/8: Updating system packages...${NC}"
sudo apt update -qq

# Step 3: Install Certbot
echo -e "${YELLOW}🔧 Step 3/8: Installing Certbot...${NC}"
if ! command -v certbot &> /dev/null; then
    sudo apt install certbot python3-certbot-apache -y
    echo -e "${GREEN}✓ Certbot installed${NC}"
else
    echo -e "${GREEN}✓ Certbot already installed${NC}"
fi

# Step 4: Copy Apache configuration
echo -e "${YELLOW}📝 Step 4/8: Setting up Apache Virtual Host...${NC}"
if [ -f "$PROJECT_PATH/yourevents.sa.conf" ]; then
    sudo cp $PROJECT_PATH/yourevents.sa.conf $APACHE_CONF
    echo -e "${GREEN}✓ Apache configuration copied${NC}"
else
    echo -e "${RED}✗ Configuration file not found${NC}"
    exit 1
fi

# Step 5: Enable site and modules
echo -e "${YELLOW}🔌 Step 5/8: Enabling Apache modules and site...${NC}"
sudo a2ensite yourevents.sa.conf
sudo a2enmod rewrite ssl headers
sudo systemctl restart apache2
echo -e "${GREEN}✓ Apache configured and restarted${NC}"

# Step 6: Test HTTP access
echo -e "${YELLOW}🌐 Step 6/8: Testing HTTP access...${NC}"
if curl -s -o /dev/null -w "%{http_code}" http://$DOMAIN | grep -q "200\|301\|302"; then
    echo -e "${GREEN}✓ Website is accessible via HTTP${NC}"
else
    echo -e "${RED}✗ Website not accessible. Please check Apache configuration${NC}"
    exit 1
fi

# Step 7: Obtain SSL Certificate
echo -e "${YELLOW}🔐 Step 7/8: Obtaining SSL certificate...${NC}"
echo -e "${BLUE}This will take a minute...${NC}"
sudo certbot --apache -d $DOMAIN -d $WWW_DOMAIN --non-interactive --agree-tos --email $EMAIL --redirect

if [ $? -eq 0 ]; then
    echo -e "${GREEN}✓ SSL certificate obtained successfully!${NC}"
else
    echo -e "${RED}✗ Failed to obtain SSL certificate${NC}"
    exit 1
fi

# Step 8: Update Laravel configuration
echo -e "${YELLOW}⚙️  Step 8/8: Updating Laravel configuration...${NC}"
cd $PROJECT_PATH

# Backup .env
cp .env .env.backup.$(date +%Y%m%d_%H%M%S)

# Update APP_URL
sed -i 's|^APP_URL=.*|APP_URL=https://yourevents.sa|' .env

# Add SESSION_DOMAIN if not exists
if ! grep -q "SESSION_DOMAIN" .env; then
    echo "" >> .env
    echo "# Session Configuration" >> .env
    echo "SESSION_DOMAIN=.yourevents.sa" >> .env
    echo "SESSION_SECURE_COOKIE=true" >> .env
fi

# Clear caches
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Rebuild caches
php artisan config:cache
php artisan route:cache

echo -e "${GREEN}✓ Laravel configuration updated${NC}"

# Final checks
echo ""
echo -e "${BLUE}═══════════════════════════════════════════════${NC}"
echo -e "${GREEN}       ✅ SSL Installation Completed!${NC}"
echo -e "${BLUE}═══════════════════════════════════════════════${NC}"
echo ""
echo -e "${GREEN}🎉 Your website is now secured with HTTPS!${NC}"
echo ""
echo -e "${YELLOW}Test your website:${NC}"
echo -e "  🔗 https://yourevents.sa"
echo -e "  🔗 https://www.yourevents.sa"
echo ""
echo -e "${YELLOW}Certificate details:${NC}"
sudo certbot certificates
echo ""
echo -e "${YELLOW}Next steps:${NC}"
echo -e "  1. Test the website in your browser"
echo -e "  2. Check for mixed content warnings"
echo -e "  3. Test SSL quality at: https://www.ssllabs.com/ssltest/"
echo ""
echo -e "${BLUE}Auto-renewal is configured. Certificate will renew automatically.${NC}"
echo -e "${BLUE}═══════════════════════════════════════════════${NC}"

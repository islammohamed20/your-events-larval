#!/bin/bash

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

echo -e "${BLUE}================================${NC}"
echo -e "${BLUE}  Outlook SMTP Configuration    ${NC}"
echo -e "${BLUE}================================${NC}"
echo ""

# Check if .env exists
if [ ! -f ".env" ]; then
    echo -e "${RED}❌ Error: .env file not found!${NC}"
    exit 1
fi

echo -e "${YELLOW}Current Mail Configuration:${NC}"
echo ""
echo -e "MAIL_MAILER:      $(grep MAIL_MAILER .env | cut -d '=' -f2)"
echo -e "MAIL_HOST:        $(grep MAIL_HOST .env | cut -d '=' -f2)"
echo -e "MAIL_PORT:        $(grep MAIL_PORT .env | cut -d '=' -f2)"
echo -e "MAIL_USERNAME:    $(grep MAIL_USERNAME .env | cut -d '=' -f2)"
echo -e "MAIL_ENCRYPTION:  $(grep MAIL_ENCRYPTION .env | cut -d '=' -f2)"
echo -e "MAIL_FROM_ADDRESS: $(grep MAIL_FROM_ADDRESS .env | cut -d '=' -f2)"
echo ""

echo -e "${YELLOW}Do you want to update the configuration? (y/n)${NC}"
read -r update_config

if [ "$update_config" = "y" ]; then
    echo ""
    echo -e "${BLUE}Enter App Password (16 characters from Microsoft):${NC}"
    read -r app_password
    
    # Backup current .env
    cp .env .env.backup.$(date +%Y%m%d_%H%M%S)
    echo -e "${GREEN}✅ Backup created${NC}"
    
    # Update .env file
    sed -i "s/MAIL_HOST=.*/MAIL_HOST=smtp.office365.com/" .env
    sed -i "s/MAIL_PORT=.*/MAIL_PORT=587/" .env
    sed -i "s/MAIL_USERNAME=.*/MAIL_USERNAME=sales@yourevents.sa/" .env
    sed -i "s/MAIL_PASSWORD=.*/MAIL_PASSWORD=$app_password/" .env
    sed -i "s/MAIL_ENCRYPTION=.*/MAIL_ENCRYPTION=tls/" .env
    sed -i "s/MAIL_FROM_ADDRESS=.*/MAIL_FROM_ADDRESS=\"sales@yourevents.sa\"/" .env
    
    echo -e "${GREEN}✅ Configuration updated${NC}"
fi

echo ""
echo -e "${YELLOW}Clearing Laravel cache...${NC}"
php artisan config:clear
php artisan cache:clear
php artisan view:clear
echo -e "${GREEN}✅ Cache cleared${NC}"

echo ""
echo -e "${YELLOW}Do you want to send a test email? (y/n)${NC}"
read -r send_test

if [ "$send_test" = "y" ]; then
    echo ""
    echo -e "${BLUE}Enter recipient email:${NC}"
    read -r recipient_email
    
    echo -e "${YELLOW}Sending test email...${NC}"
    
    php artisan tinker --execute="
    use Illuminate\Support\Facades\Mail;
    
    try {
        Mail::raw('This is a test email from Your Events. If you receive this, your email configuration is working! ✅', function (\$message) {
            \$message->to('$recipient_email')
                    ->subject('Test Email - Your Events');
        });
        echo PHP_EOL . '✅ Email sent successfully!' . PHP_EOL;
    } catch (Exception \$e) {
        echo PHP_EOL . '❌ Error: ' . \$e->getMessage() . PHP_EOL;
    }
    "
fi

echo ""
echo -e "${GREEN}================================${NC}"
echo -e "${GREEN}Configuration Complete!${NC}"
echo -e "${GREEN}================================${NC}"
echo ""
echo -e "${YELLOW}Next Steps:${NC}"
echo -e "1. Visit: ${BLUE}https://yourevents.sa/admin/email-test${NC}"
echo -e "2. Send a test email from the admin panel"
echo -e "3. Check your inbox"
echo ""
echo -e "${YELLOW}Documentation:${NC}"
echo -e "- ${BLUE}OUTLOOK-SMTP-SOLUTION.md${NC} (Quick guide)"
echo -e "- ${BLUE}EMAIL-SETUP-GUIDE.md${NC} (Detailed guide)"
echo ""

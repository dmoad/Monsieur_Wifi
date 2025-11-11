#!/bin/bash

# Email Testing Script for Password Reset
# This script helps you quickly test the email functionality

echo "========================================="
echo "  Password Reset Email Testing Script"
echo "========================================="
echo ""

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Check if we're in the right directory
if [ ! -f "artisan" ]; then
    echo -e "${RED}Error: Must be run from Laravel root directory${NC}"
    exit 1
fi

echo -e "${BLUE}1. Checking Mail Configuration...${NC}"
echo ""

# Display current mail config
echo "MAIL_MAILER: $(grep MAIL_MAILER .env | cut -d '=' -f2)"
echo "MAIL_HOST: $(grep MAIL_HOST .env | cut -d '=' -f2)"
echo "MAIL_PORT: $(grep MAIL_PORT .env | cut -d '=' -f2)"
echo "MAIL_FROM_ADDRESS: $(grep MAIL_FROM_ADDRESS .env | cut -d '=' -f2)"
echo ""

# Check if using log driver
MAIL_MAILER=$(grep MAIL_MAILER .env | cut -d '=' -f2)

if [ "$MAIL_MAILER" == "log" ]; then
    echo -e "${YELLOW}⚠️  Using LOG mail driver - emails will be written to storage/logs/laravel.log${NC}"
    echo ""
elif [ "$MAIL_MAILER" == "smtp" ]; then
    echo -e "${GREEN}✓ Using SMTP mail driver${NC}"
    echo ""
else
    echo -e "${YELLOW}⚠️  Mail driver: $MAIL_MAILER${NC}"
    echo ""
fi

echo -e "${BLUE}2. Checking Log File...${NC}"
echo ""

if [ -f "storage/logs/laravel.log" ]; then
    LOG_SIZE=$(du -h storage/logs/laravel.log | cut -f1)
    echo -e "${GREEN}✓ Log file exists (Size: $LOG_SIZE)${NC}"
    
    # Check permissions
    LOG_PERMS=$(stat -c "%a" storage/logs/laravel.log)
    if [ "$LOG_PERMS" -ge "664" ]; then
        echo -e "${GREEN}✓ Log file is writable (Permissions: $LOG_PERMS)${NC}"
    else
        echo -e "${YELLOW}⚠️  Log file permissions may be too restrictive (Permissions: $LOG_PERMS)${NC}"
        echo "   Consider running: chmod 664 storage/logs/laravel.log"
    fi
else
    echo -e "${YELLOW}⚠️  Log file doesn't exist yet - it will be created on first use${NC}"
fi
echo ""

echo -e "${BLUE}3. Testing Options:${NC}"
echo ""
echo "Choose an option:"
echo "  1) Watch logs in real-time (tail -f)"
echo "  2) View last 50 log entries"
echo "  3) Search for password reset logs"
echo "  4) Send a test password reset email"
echo "  5) Check for failed email attempts"
echo "  6) Clear old logs (create backup first)"
echo "  7) Exit"
echo ""

read -p "Enter your choice (1-7): " choice

case $choice in
    1)
        echo ""
        echo -e "${GREEN}Watching logs in real-time...${NC}"
        echo -e "${YELLOW}Press Ctrl+C to stop${NC}"
        echo ""
        tail -f storage/logs/laravel.log
        ;;
    2)
        echo ""
        echo -e "${GREEN}Last 50 log entries:${NC}"
        echo ""
        tail -50 storage/logs/laravel.log
        ;;
    3)
        echo ""
        echo -e "${GREEN}Password reset logs:${NC}"
        echo ""
        grep "Password Reset" storage/logs/laravel.log | tail -20
        ;;
    4)
        echo ""
        read -p "Enter email address to test: " email
        echo ""
        
        # Get APP_URL from .env
        APP_URL=$(grep APP_URL .env | cut -d '=' -f2 | tr -d '"' | tr -d "'")
        
        if [ -z "$APP_URL" ]; then
            APP_URL="http://localhost"
            echo -e "${YELLOW}⚠️  APP_URL not found in .env, using: $APP_URL${NC}"
        else
            echo -e "${GREEN}Using APP_URL from .env: $APP_URL${NC}"
        fi
        
        echo -e "${YELLOW}Sending password reset request to: $email${NC}"
        echo ""
        
        # Use curl to send the request
        response=$(curl -s -X POST "$APP_URL/api/auth/password-reset" \
            -H "Content-Type: application/json" \
            -H "Accept: application/json" \
            -d "{\"email\":\"$email\"}")
        
        echo "Response: $response"
        echo ""
        echo -e "${GREEN}Check the logs above for details${NC}"
        echo ""
        echo "View logs with: tail -50 storage/logs/laravel.log"
        ;;
    5)
        echo ""
        echo -e "${RED}Failed email attempts:${NC}"
        echo ""
        grep -i "failed to send password reset email" storage/logs/laravel.log | tail -10
        
        if [ $? -ne 0 ]; then
            echo -e "${GREEN}No failed email attempts found!${NC}"
        fi
        ;;
    6)
        echo ""
        if [ -f "storage/logs/laravel.log" ]; then
            # Create backup
            BACKUP_NAME="laravel.log.backup.$(date +%Y%m%d_%H%M%S)"
            cp storage/logs/laravel.log "storage/logs/$BACKUP_NAME"
            echo -e "${GREEN}✓ Backup created: storage/logs/$BACKUP_NAME${NC}"
            
            # Clear the log
            > storage/logs/laravel.log
            echo -e "${GREEN}✓ Log file cleared${NC}"
        else
            echo -e "${YELLOW}No log file to clear${NC}"
        fi
        ;;
    7)
        echo ""
        echo -e "${GREEN}Goodbye!${NC}"
        exit 0
        ;;
    *)
        echo ""
        echo -e "${RED}Invalid choice${NC}"
        exit 1
        ;;
esac

echo ""
echo -e "${GREEN}Done!${NC}"
echo ""


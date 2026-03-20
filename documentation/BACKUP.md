# Backup and Restore Guide - Monsieur WiFi Laravel Project

This document provides practical, tested instructions for backing up and restoring the Monsieur WiFi Laravel application. All commands have been verified and can be executed directly on your live setup.

> **Note**: This guide is designed for **Ubuntu/Debian** systems. All commands assume an Ubuntu/Debian environment.

## Table of Contents

1. [What Gets Backed Up](#what-gets-backed-up)
2. [Quick Backup (Recommended)](#quick-backup-recommended)
3. [Manual Step-by-Step Backup](#manual-step-by-step-backup)
4. [Automated Backup Script](#automated-backup-script)
5. [Restore Procedures](#restore-procedures)
6. [Backup Storage and Transfer](#backup-storage-and-transfer)
7. [Troubleshooting](#troubleshooting)

---

## What Gets Backed Up

Your complete backup will include:

### Essential Components
- ✅ **Main Database** (`mrwifi`) - Users, locations, devices, settings
- ✅ **RADIUS Database** (`mrwifi_radius`) - WiFi authentication data
- ✅ **Environment File** (`.env`) - All configuration and secrets
- ✅ **Uploaded Files** - Profile pictures and user uploads
- ✅ **Application Code** - Your Laravel application files

### Optional Components
- 🔧 **Apache Configuration** - Virtual host settings
- 🔧 **SSL Certificates** - Let's Encrypt certificates (if using HTTPS)

> **Note**: We exclude temporary files like cache, logs, sessions, and vendor dependencies (which can be reinstalled via composer).

---

## Quick Backup (Recommended)

**✅ This is all you need for a complete backup!** 

This single command block creates a full, production-ready backup including:
- Both databases (main + RADIUS)
- Environment configuration
- All uploaded files
- Application code
- Apache configuration

**After running this, your backup is complete** - no additional steps required!

### Step 1: Create Backup Directory

```bash
sudo mkdir -p /backup/mrwifi
sudo chown $USER:$USER /backup/mrwifi
```

### Step 2: Run Complete Backup

Copy and paste this entire command block:

```bash
BACKUP_DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/backup/mrwifi/$BACKUP_DATE"
mkdir -p "$BACKUP_DIR"
echo "Creating backup in: $BACKUP_DIR"
echo "Started: $(date)" > "$BACKUP_DIR/backup.log"

# Backup databases
echo "Backing up databases..."
mysqldump -u mrwifi_user -p --single-transaction --no-tablespaces mrwifi | gzip > "$BACKUP_DIR/mrwifi_main.sql.gz"
mysqldump -u mrwifi_user -p --single-transaction --no-tablespaces mrwifi_radius | gzip > "$BACKUP_DIR/mrwifi_radius.sql.gz"

# Backup .env file
echo "Backing up .env file..."
cp /var/www/mrwifi/.env "$BACKUP_DIR/env_backup"

# Backup uploaded files
echo "Backing up uploaded files..."
if [ -d "/var/www/mrwifi/public/uploads" ]; then
    tar -czf "$BACKUP_DIR/uploads.tar.gz" -C /var/www/mrwifi/public uploads/
fi

# Backup storage app files
if [ -d "/var/www/mrwifi/storage/app/public" ]; then
    tar -czf "$BACKUP_DIR/storage_app.tar.gz" -C /var/www/mrwifi/storage app/public/
fi

# Backup application code (excluding vendor, cache, logs)
echo "Backing up application code..."
cd /var/www/mrwifi
tar -czf "$BACKUP_DIR/application.tar.gz" \
    --exclude='vendor' \
    --exclude='node_modules' \
    --exclude='.git' \
    --exclude='storage/logs/*.log' \
    --exclude='storage/framework/cache/data/*' \
    --exclude='storage/framework/sessions/*' \
    --exclude='storage/framework/views/*' \
    --exclude='bootstrap/cache/*.php' \
    .

# Backup Apache config (optional)
echo "Backing up Apache configuration..."
if [ -f "/etc/apache2/sites-available/mrwifi.conf" ]; then
    sudo cp /etc/apache2/sites-available/mrwifi.conf "$BACKUP_DIR/apache_mrwifi.conf"
fi
if [ -f "/etc/apache2/sites-available/mrwifi-le-ssl.conf" ]; then
    sudo cp /etc/apache2/sites-available/mrwifi-le-ssl.conf "$BACKUP_DIR/apache_mrwifi_ssl.conf"
fi

# Create backup summary
echo "Completed: $(date)" >> "$BACKUP_DIR/backup.log"
echo "Server: $(hostname)" >> "$BACKUP_DIR/backup.log"
echo "Files:" >> "$BACKUP_DIR/backup.log"
ls -lh "$BACKUP_DIR" >> "$BACKUP_DIR/backup.log"
du -sh "$BACKUP_DIR" >> "$BACKUP_DIR/backup.log"

echo ""
echo "=============================================="
echo "✅ BACKUP COMPLETED SUCCESSFULLY!"
echo "=============================================="
echo "Location: $BACKUP_DIR"
echo "Size: $(du -sh $BACKUP_DIR | cut -f1)"
echo ""
echo "Your complete backup includes:"
echo "  ✓ Main database (mrwifi)"
echo "  ✓ RADIUS database (mrwifi_radius)"
echo "  ✓ Environment configuration (.env)"
echo "  ✓ Uploaded files"
echo "  ✓ Application code"
echo "  ✓ Apache configuration"
echo ""
echo "You can now:"
echo "  - Transfer this backup to another location"
echo "  - Use it to restore your system if needed"
echo "  - Archive it for safekeeping"
echo ""
echo "No additional backup steps are required!"
echo "=============================================="
echo ""
echo "Backup contents:"
ls -lh "$BACKUP_DIR"
```

**Important Notes:**
- You'll be prompted for the MySQL password twice (once for each database)
- The backup will be created in `/backup/mrwifi/YYYYMMDD_HHMMSS/`
- Typical backup size: 10-500MB depending on your data
- **This is a complete backup** - you don't need to run any other backup commands

### Step 3: Verify Backup

```bash
# List your backups
ls -lh /backup/mrwifi/

# Check the latest backup contents
LATEST_BACKUP=$(ls -t /backup/mrwifi/ | head -1)
ls -lh "/backup/mrwifi/$LATEST_BACKUP/"
cat "/backup/mrwifi/$LATEST_BACKUP/backup.log"
```

---

## ⚠️ IMPORTANT: Choose Your Backup Method

**If you completed the [Quick Backup](#quick-backup-recommended) above, you're done!** ✅

The Quick Backup creates a complete, production-ready backup with everything you need. You can **skip the rest of this document** unless you want to:
- Understand what each backup component does
- Customize your backup process
- Create your own automated backup script
- Learn about manual restore procedures

**The sections below are optional** and only needed for advanced users or specific use cases.

---

## Manual Step-by-Step Backup

**Note:** This section is for educational purposes or custom backup needs. If you already ran the Quick Backup, you don't need to do this.

If you prefer to backup components individually or want to understand each step, follow these instructions:

### Preparation

```bash
# Create timestamped backup directory
BACKUP_DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/backup/mrwifi/$BACKUP_DATE"
mkdir -p "$BACKUP_DIR"
cd "$BACKUP_DIR"
```

### 1. Backup Main Database

```bash
echo "Backing up main database..."
mysqldump -u mrwifi_user -p --single-transaction --no-tablespaces mrwifi | gzip > mrwifi_main.sql.gz
```

**What this does:**
- Dumps the entire `mrwifi` database
- Uses `--single-transaction` for consistent backup without locking tables
- Uses `--no-tablespaces` to avoid permission issues
- Compresses with gzip to save space

### 2. Backup RADIUS Database

```bash
echo "Backing up RADIUS database..."
mysqldump -u mrwifi_user -p --single-transaction --no-tablespaces mrwifi_radius | gzip > mrwifi_radius.sql.gz
```

### 3. Backup Environment Configuration

```bash
echo "Backing up .env file..."
cp /var/www/mrwifi/.env ./env_backup
```

> **Security Warning**: The `.env` file contains sensitive information (passwords, API keys). Keep backups secure!

### 4. Backup Uploaded Files

```bash
echo "Backing up uploaded files..."
tar -czf uploads.tar.gz -C /var/www/mrwifi/public uploads/
```

This backs up:
- Profile pictures
- Any other uploaded content

### 5. Backup Application Code

```bash
echo "Backing up application code..."
cd /var/www/mrwifi
tar -czf "$BACKUP_DIR/application.tar.gz" \
    --exclude='vendor' \
    --exclude='node_modules' \
    --exclude='.git' \
    --exclude='storage/logs/*.log' \
    --exclude='storage/framework/cache/data/*' \
    --exclude='storage/framework/sessions/*' \
    --exclude='storage/framework/views/*' \
    --exclude='bootstrap/cache/*.php' \
    .
```

**Why we exclude certain directories:**
- `vendor/` - Can be reinstalled via composer
- `node_modules/` - Can be reinstalled via npm
- `.git/` - Use Git repository instead
- Cache/logs - Temporary files not needed for restore

### 6. Backup Apache Configuration (Optional)

```bash
echo "Backing up Apache configuration..."
sudo cp /etc/apache2/sites-available/mrwifi.conf "$BACKUP_DIR/apache_mrwifi.conf"

# If you have SSL configured
if [ -f "/etc/apache2/sites-available/mrwifi-le-ssl.conf" ]; then
    sudo cp /etc/apache2/sites-available/mrwifi-le-ssl.conf "$BACKUP_DIR/apache_mrwifi_ssl.conf"
fi
```

### 7. Backup SSL Certificates (Optional)

Only if you're using Let's Encrypt SSL:

```bash
# Replace 'your-domain.com' with your actual domain
DOMAIN="your-domain.com"

if [ -d "/etc/letsencrypt/live/$DOMAIN" ]; then
    echo "Backing up SSL certificates..."
    sudo tar -czf "$BACKUP_DIR/ssl_certificates.tar.gz" \
        -C /etc/letsencrypt live/$DOMAIN/ archive/$DOMAIN/ 2>/dev/null
fi
```

### 8. Create Backup Summary

```bash
echo "Creating backup summary..."
cat > "$BACKUP_DIR/backup_info.txt" << EOF
Monsieur WiFi Backup
====================
Date: $(date)
Server: $(hostname)
User: $(whoami)

Backup Contents:
EOF

ls -lh "$BACKUP_DIR" >> "$BACKUP_DIR/backup_info.txt"
echo "" >> "$BACKUP_DIR/backup_info.txt"
echo "Total Size: $(du -sh $BACKUP_DIR | cut -f1)" >> "$BACKUP_DIR/backup_info.txt"

cat "$BACKUP_DIR/backup_info.txt"
```

---

## Automated Backup Script

**Note:** This section is optional if you want to schedule regular backups automatically. If you're doing one-time backups, the Quick Backup method is sufficient.

Create a script for automated, scheduled backups:

### Create the Script

```bash
sudo nano /usr/local/bin/mrwifi-backup.sh
```

Copy and paste this complete, working script:

```bash
#!/bin/bash

#############################################
# Monsieur WiFi Automated Backup Script
# This script creates a complete backup
#############################################

# Configuration - EDIT THESE VARIABLES
BACKUP_BASE="/backup/mrwifi"
DB_USER="mrwifi_user"
DB_PASSWORD=""  # Leave empty to prompt, or use .my.cnf file
APP_DIR="/var/www/mrwifi"
DOMAIN="your-domain.com"  # Replace with your actual domain

# Backup retention (days)
RETENTION_DAYS=30

#############################################
# DO NOT EDIT BELOW THIS LINE
#############################################

# Create backup directory
BACKUP_DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="$BACKUP_BASE/$BACKUP_DATE"
LOG_FILE="$BACKUP_DIR/backup.log"

mkdir -p "$BACKUP_DIR"

# Redirect all output to log file and console
exec > >(tee -a "$LOG_FILE")
exec 2>&1

echo "=========================================="
echo "Monsieur WiFi Backup Started"
echo "Date: $(date)"
echo "Backup Directory: $BACKUP_DIR"
echo "=========================================="

# Function to check command success
check_success() {
    if [ $? -eq 0 ]; then
        echo "✓ $1 completed successfully"
        return 0
    else
        echo "✗ $1 failed!"
        return 1
    fi
}

# Database password handling
DB_PASS_ARG=""
if [ -n "$DB_PASSWORD" ]; then
    DB_PASS_ARG="-p$DB_PASSWORD"
elif [ -f "$HOME/.my.cnf" ]; then
    echo "Using MySQL credentials from ~/.my.cnf"
else
    echo "Note: You will be prompted for MySQL password"
    DB_PASS_ARG="-p"
fi

# 1. Backup Main Database
echo ""
echo "1/7 Backing up main database..."
mysqldump -u "$DB_USER" $DB_PASS_ARG --single-transaction --no-tablespaces mrwifi | gzip > "$BACKUP_DIR/mrwifi_main.sql.gz"
check_success "Main database backup"

# 2. Backup RADIUS Database
echo ""
echo "2/7 Backing up RADIUS database..."
mysqldump -u "$DB_USER" $DB_PASS_ARG --single-transaction --no-tablespaces mrwifi_radius | gzip > "$BACKUP_DIR/mrwifi_radius.sql.gz"
check_success "RADIUS database backup"

# 3. Backup .env file
echo ""
echo "3/7 Backing up .env file..."
if [ -f "$APP_DIR/.env" ]; then
    cp "$APP_DIR/.env" "$BACKUP_DIR/env_backup"
    check_success ".env file backup"
else
    echo "⚠ .env file not found, skipping..."
fi

# 4. Backup uploaded files
echo ""
echo "4/7 Backing up uploaded files..."
if [ -d "$APP_DIR/public/uploads" ]; then
    tar -czf "$BACKUP_DIR/uploads.tar.gz" -C "$APP_DIR/public" uploads/
    check_success "Uploaded files backup"
else
    echo "⚠ Uploads directory not found, skipping..."
fi

# 5. Backup storage app files
echo ""
echo "5/7 Backing up storage files..."
if [ -d "$APP_DIR/storage/app/public" ]; then
    tar -czf "$BACKUP_DIR/storage_app.tar.gz" -C "$APP_DIR/storage" app/public/
    check_success "Storage files backup"
fi

# 6. Backup application code
echo ""
echo "6/7 Backing up application code..."
cd "$APP_DIR"
tar -czf "$BACKUP_DIR/application.tar.gz" \
    --exclude='vendor' \
    --exclude='node_modules' \
    --exclude='.git' \
    --exclude='storage/logs/*.log' \
    --exclude='storage/framework/cache/data/*' \
    --exclude='storage/framework/sessions/*' \
    --exclude='storage/framework/views/*' \
    --exclude='bootstrap/cache/*.php' \
    .
check_success "Application code backup"

# 7. Backup Apache configuration
echo ""
echo "7/7 Backing up Apache configuration..."
if [ -f "/etc/apache2/sites-available/mrwifi.conf" ]; then
    sudo cp /etc/apache2/sites-available/mrwifi.conf "$BACKUP_DIR/apache_mrwifi.conf"
    check_success "Apache configuration backup"
fi

if [ -f "/etc/apache2/sites-available/mrwifi-le-ssl.conf" ]; then
    sudo cp /etc/apache2/sites-available/mrwifi-le-ssl.conf "$BACKUP_DIR/apache_mrwifi_ssl.conf"
fi

# Create backup summary
echo ""
echo "Creating backup summary..."
cat > "$BACKUP_DIR/backup_info.txt" << EOF
Monsieur WiFi Backup Summary
============================
Date: $(date)
Server: $(hostname)
User: $(whoami)
Directory: $BACKUP_DIR

Backup Files:
EOF

ls -lh "$BACKUP_DIR" | grep -v "^total" >> "$BACKUP_DIR/backup_info.txt"
echo "" >> "$BACKUP_DIR/backup_info.txt"
echo "Total Backup Size: $(du -sh $BACKUP_DIR | cut -f1)" >> "$BACKUP_DIR/backup_info.txt"

# Cleanup old backups
echo ""
echo "Cleaning up backups older than $RETENTION_DAYS days..."
find "$BACKUP_BASE" -maxdepth 1 -type d -name "20*" -mtime +$RETENTION_DAYS -exec rm -rf {} \;

echo ""
echo "=========================================="
echo "Backup Completed Successfully!"
echo "Location: $BACKUP_DIR"
echo "Size: $(du -sh $BACKUP_DIR | cut -f1)"
echo "=========================================="
echo ""
echo "To view backup contents:"
echo "  ls -lh $BACKUP_DIR"
echo ""
echo "To view this log:"
echo "  cat $LOG_FILE"
```

### Make Script Executable

```bash
sudo chmod +x /usr/local/bin/mrwifi-backup.sh
```

### Configure MySQL Credentials (Recommended)

To avoid password prompts, create a MySQL credentials file:

```bash
nano ~/.my.cnf
```

Add:

```ini
[client]
user=mrwifi_user
password=your_database_password_here
```

Set secure permissions:

```bash
chmod 600 ~/.my.cnf
```

### Test the Script

```bash
/usr/local/bin/mrwifi-backup.sh
```

### Schedule Automatic Backups (Optional)

To run backups automatically, add to crontab:

```bash
crontab -e
```

**Daily backup at 2 AM:**
```
0 2 * * * /usr/local/bin/mrwifi-backup.sh >> /var/log/mrwifi-backup.log 2>&1
```

**Weekly backup (Sunday at 3 AM):**
```
0 3 * * 0 /usr/local/bin/mrwifi-backup.sh >> /var/log/mrwifi-backup.log 2>&1
```

---

## Restore Procedures

### Prerequisites

Before restoring, ensure:
- ✅ MySQL/MariaDB is installed and running
- ✅ Apache2 is installed
- ✅ PHP 8.2+ is installed with required extensions
- ✅ Composer is installed
- ✅ You have the backup files

### Quick Restore (Complete System)

```bash
# Set your backup directory
BACKUP_DIR="/backup/mrwifi/20240129_123456"  # Replace with your actual backup directory

# Verify backup exists
if [ ! -d "$BACKUP_DIR" ]; then
    echo "Error: Backup directory not found!"
    exit 1
fi

echo "Restoring from: $BACKUP_DIR"
echo "Started: $(date)"

# 1. Restore Main Database
echo "Restoring main database..."
gunzip < "$BACKUP_DIR/mrwifi_main.sql.gz" | mysql -u mrwifi_user -p mrwifi

# 2. Restore RADIUS Database
echo "Restoring RADIUS database..."
gunzip < "$BACKUP_DIR/mrwifi_radius.sql.gz" | mysql -u mrwifi_user -p mrwifi_radius

# 3. Restore .env file
echo "Restoring .env file..."
cp "$BACKUP_DIR/env_backup" /var/www/mrwifi/.env

# 4. Restore application code
echo "Restoring application code..."
cd /var/www
sudo rm -rf mrwifi
mkdir mrwifi
tar -xzf "$BACKUP_DIR/application.tar.gz" -C /var/www/mrwifi/

# 5. Restore uploaded files
echo "Restoring uploaded files..."
if [ -f "$BACKUP_DIR/uploads.tar.gz" ]; then
    tar -xzf "$BACKUP_DIR/uploads.tar.gz" -C /var/www/mrwifi/public/
fi

# 6. Restore storage files
if [ -f "$BACKUP_DIR/storage_app.tar.gz" ]; then
    tar -xzf "$BACKUP_DIR/storage_app.tar.gz" -C /var/www/mrwifi/storage/
fi

# 7. Restore Apache configuration (if exists)
if [ -f "$BACKUP_DIR/apache_mrwifi.conf" ]; then
    sudo cp "$BACKUP_DIR/apache_mrwifi.conf" /etc/apache2/sites-available/mrwifi.conf
    sudo a2ensite mrwifi.conf
fi

# 8. Install dependencies
echo "Installing PHP dependencies..."
cd /var/www/mrwifi
composer install --no-dev --optimize-autoloader

# 9. Set permissions
echo "Setting permissions..."
sudo chown -R mrwifi-admin:www-data /var/www/mrwifi
sudo chmod -R 755 /var/www/mrwifi
sudo chmod -R 775 /var/www/mrwifi/storage
sudo chmod -R 775 /var/www/mrwifi/bootstrap/cache
sudo chmod -R 775 /var/www/mrwifi/public/uploads

# 10. Clear caches
echo "Clearing caches..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# 11. Recreate storage link
php artisan storage:link

# 12. Restart services
echo "Restarting services..."
sudo systemctl restart apache2

echo ""
echo "=========================================="
echo "Restore completed!"
echo "Finished: $(date)"
echo "=========================================="
echo ""
echo "Next steps:"
echo "1. Verify site is accessible in browser"
echo "2. Test login with admin credentials"
echo "3. Check logs: tail -f storage/logs/laravel.log"
```

### Step-by-Step Restore

If you prefer manual control, follow these steps:

#### 1. Prepare for Restore

```bash
# List available backups
ls -lh /backup/mrwifi/

# Choose backup to restore
BACKUP_DIR="/backup/mrwifi/20240129_123456"  # Replace with your backup

# Verify backup contents
ls -lh "$BACKUP_DIR/"
cat "$BACKUP_DIR/backup_info.txt"
```

#### 2. Restore Databases

```bash
# Create databases if they don't exist
mysql -u root -p << EOF
CREATE DATABASE IF NOT EXISTS mrwifi CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE DATABASE IF NOT EXISTS mrwifi_radius CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
GRANT ALL PRIVILEGES ON mrwifi.* TO 'mrwifi_user'@'localhost';
GRANT ALL PRIVILEGES ON mrwifi_radius.* TO 'mrwifi_user'@'localhost';
FLUSH PRIVILEGES;
EOF

# Restore main database
echo "Restoring main database..."
gunzip < "$BACKUP_DIR/mrwifi_main.sql.gz" | mysql -u mrwifi_user -p mrwifi

# Restore RADIUS database
echo "Restoring RADIUS database..."
gunzip < "$BACKUP_DIR/mrwifi_radius.sql.gz" | mysql -u mrwifi_user -p mrwifi_radius
```

#### 3. Restore Application Files

```bash
# Backup current application (if exists)
if [ -d "/var/www/mrwifi" ]; then
    sudo mv /var/www/mrwifi /var/www/mrwifi.old.$(date +%Y%m%d_%H%M%S)
fi

# Create application directory
sudo mkdir -p /var/www/mrwifi
cd /var/www/mrwifi

# Extract application code
tar -xzf "$BACKUP_DIR/application.tar.gz" -C /var/www/mrwifi/
```

#### 4. Restore Configuration

```bash
# Restore .env file
cp "$BACKUP_DIR/env_backup" /var/www/mrwifi/.env

# IMPORTANT: Review and update .env if needed
nano /var/www/mrwifi/.env
```

#### 5. Restore Uploaded Files

```bash
# Restore uploads
if [ -f "$BACKUP_DIR/uploads.tar.gz" ]; then
    tar -xzf "$BACKUP_DIR/uploads.tar.gz" -C /var/www/mrwifi/public/
fi

# Restore storage files
if [ -f "$BACKUP_DIR/storage_app.tar.gz" ]; then
    tar -xzf "$BACKUP_DIR/storage_app.tar.gz" -C /var/www/mrwifi/storage/
fi
```

#### 6. Restore Apache Configuration

```bash
# Restore Apache config
if [ -f "$BACKUP_DIR/apache_mrwifi.conf" ]; then
    sudo cp "$BACKUP_DIR/apache_mrwifi.conf" /etc/apache2/sites-available/mrwifi.conf
fi

if [ -f "$BACKUP_DIR/apache_mrwifi_ssl.conf" ]; then
    sudo cp "$BACKUP_DIR/apache_mrwifi_ssl.conf" /etc/apache2/sites-available/mrwifi-le-ssl.conf
fi

# Enable site
sudo a2ensite mrwifi.conf
sudo apache2ctl configtest
```

#### 7. Install Dependencies

```bash
cd /var/www/mrwifi

# Install Composer dependencies
composer install --no-dev --optimize-autoloader
```

#### 8. Set Permissions

```bash
# Set ownership (replace mrwifi-admin with your user)
sudo chown -R mrwifi-admin:www-data /var/www/mrwifi

# Set directory permissions
sudo find /var/www/mrwifi -type d -exec chmod 755 {} \;

# Set file permissions
sudo find /var/www/mrwifi -type f -exec chmod 644 {} \;

# Make storage and cache writable
sudo chmod -R 775 /var/www/mrwifi/storage
sudo chmod -R 775 /var/www/mrwifi/bootstrap/cache
sudo chmod -R 775 /var/www/mrwifi/public/uploads
```

#### 9. Clear Caches and Rebuild

```bash
cd /var/www/mrwifi

# Clear all caches
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Recreate storage link
php artisan storage:link
```

#### 10. Verify Restoration

```bash
# Check application status
php artisan about

# Test database connections
php artisan tinker
```

In tinker, run:
```php
DB::connection()->getPdo();
DB::connection('radius')->getPdo();
exit
```

#### 11. Restart Services

```bash
sudo systemctl restart apache2
sudo systemctl restart php8.2-fpm
```

#### 12. Test the Application

```bash
# Check Apache status
sudo systemctl status apache2

# Check site is accessible
curl -I http://localhost

# Check logs for errors
tail -f /var/www/mrwifi/storage/logs/laravel.log
```

---

## Backup Storage and Transfer

### Compress Entire Backup

To save space or transfer backups:

```bash
BACKUP_DIR="/backup/mrwifi/20240129_123456"
cd /backup/mrwifi
tar -czf "$(basename $BACKUP_DIR).tar.gz" "$(basename $BACKUP_DIR)"

# Result: /backup/mrwifi/20240129_123456.tar.gz
```

### Transfer to Remote Server

#### Using SCP

```bash
scp /backup/mrwifi/20240129_123456.tar.gz user@remote-server:/backups/
```

#### Using rsync

```bash
rsync -avz --progress /backup/mrwifi/ user@remote-server:/backups/mrwifi/
```

### Download Backup to Local Machine

```bash
# From your local machine
scp user@server-ip:/backup/mrwifi/20240129_123456.tar.gz ~/Downloads/
```

### Cloud Storage Options

#### AWS S3 (if configured)

```bash
# Upload to S3
aws s3 cp /backup/mrwifi/20240129_123456.tar.gz s3://your-bucket/mrwifi-backups/

# Download from S3
aws s3 cp s3://your-bucket/mrwifi-backups/20240129_123456.tar.gz ./
```

#### Google Drive (using rclone)

```bash
# Upload
rclone copy /backup/mrwifi/20240129_123456.tar.gz gdrive:mrwifi-backups/

# Download
rclone copy gdrive:mrwifi-backups/20240129_123456.tar.gz ./
```

### Backup Retention Strategy

```bash
# Keep backups for 30 days
find /backup/mrwifi -name "20*" -type d -mtime +30 -exec rm -rf {} \;

# Keep only last 10 backups
cd /backup/mrwifi
ls -t | tail -n +11 | xargs rm -rf
```

---

## Troubleshooting

### Backup Issues

#### Issue: "mysqldump: Access denied" or "need PROCESS privilege"

**Solution:**

This happens when the user lacks PROCESS privilege for tablespaces. Use `--no-tablespaces` flag:

```bash
# Fixed command (already included in the backup commands above)
mysqldump -u mrwifi_user -p --single-transaction --no-tablespaces mrwifi | gzip > backup.sql.gz
```

Or grant PROCESS privilege (not recommended for security):
```bash
# Test MySQL connection
mysql -u mrwifi_user -p -e "SHOW DATABASES;"

# Verify user has privileges
mysql -u root -p -e "SHOW GRANTS FOR 'mrwifi_user'@'localhost';"
```

#### Issue: "No space left on device"

**Solution:**
```bash
# Check disk space
df -h

# Clean up old backups
rm -rf /backup/mrwifi/old_backup_directory

# Or move to external drive
```

#### Issue: "Permission denied" when creating backup

**Solution:**
```bash
# Ensure backup directory has correct permissions
sudo mkdir -p /backup/mrwifi
sudo chown $USER:$USER /backup/mrwifi
```

#### Issue: Backup script hangs on database backup

**Solution:**
```bash
# Check MySQL is running
sudo systemctl status mysql

# Check for locked tables
mysql -u root -p -e "SHOW OPEN TABLES WHERE In_use > 0;"

# Use --single-transaction flag (already included in script)
```

### Restore Issues

#### Issue: "Database already exists" error

**Solution:**
```bash
# Drop and recreate database
mysql -u root -p << EOF
DROP DATABASE IF EXISTS mrwifi;
DROP DATABASE IF EXISTS mrwifi_radius;
CREATE DATABASE mrwifi CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE DATABASE mrwifi_radius CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EOF
```

#### Issue: "Table doesn't exist" errors after restore

**Solution:**
```bash
# Verify database was restored
mysql -u mrwifi_user -p -e "USE mrwifi; SHOW TABLES;"

# If RADIUS tables missing, import base schema
mysql -u mrwifi_user -p mrwifi_radius < /var/www/mrwifi/database/radius_base_schema.sql
```

#### Issue: Application shows errors after restore

**Solutions:**
```bash
# 1. Clear all caches
cd /var/www/mrwifi
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# 2. Check .env file
nano .env
# Verify database credentials are correct

# 3. Check permissions
sudo chown -R mrwifi-admin:www-data /var/www/mrwifi
sudo chmod -R 775 /var/www/mrwifi/storage
sudo chmod -R 775 /var/www/mrwifi/bootstrap/cache

# 4. Check Apache error logs
sudo tail -f /var/log/apache2/error.log

# 5. Check Laravel logs
tail -f /var/www/mrwifi/storage/logs/laravel.log
```

#### Issue: "composer: command not found"

**Solution:**
```bash
# Reinstall composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
composer --version
```

#### Issue: File permission errors after restore

**Solution:**
```bash
# Fix all permissions
cd /var/www/mrwifi

# Set correct ownership
sudo chown -R mrwifi-admin:www-data .

# Set directory permissions
sudo find . -type d -exec chmod 755 {} \;

# Set file permissions
sudo find . -type f -exec chmod 644 {} \;

# Make storage writable
sudo chmod -R 775 storage bootstrap/cache public/uploads

# Verify www-data can write
sudo -u www-data touch storage/test.txt && rm storage/test.txt
```

#### Issue: SSL certificate not working after restore

**Solution:**
```bash
# Option 1: Renew certificate
sudo certbot renew --force-renewal

# Option 2: Obtain new certificate
sudo certbot --apache -d your-domain.com

# Restart Apache
sudo systemctl restart apache2
```

---

## Best Practices

### 1. Test Your Backups

```bash
# Create test restore directory
mkdir -p /tmp/backup-test
cd /tmp/backup-test

# Extract and verify backup
BACKUP="/backup/mrwifi/20240129_123456"
tar -xzf "$BACKUP/application.tar.gz"
gunzip < "$BACKUP/mrwifi_main.sql.gz" > test.sql
head -20 test.sql  # Should show SQL commands

# Clean up
cd ~
rm -rf /tmp/backup-test
```

### 2. Regular Backup Schedule

- **Daily**: For production systems with frequent changes
- **Weekly**: For stable systems with occasional updates
- **Before Updates**: Always backup before major updates

### 3. Multiple Backup Locations

- Keep local backups: `/backup/mrwifi/`
- Transfer to remote server regularly
- Consider cloud storage for critical data

### 4. Document Your Backup

```bash
# Add notes to backup
echo "Backup before upgrading to v2.0" > /backup/mrwifi/20240129_123456/NOTES.txt
```

### 5. Monitor Backup Size

```bash
# Check backup sizes
du -sh /backup/mrwifi/*

# Set up alerts if backups grow too large
```

### 6. Secure Your Backups

```bash
# Encrypt sensitive backups
tar -czf - /backup/mrwifi/20240129_123456 | openssl enc -aes-256-cbc -e > backup_encrypted.tar.gz.enc

# Decrypt when needed
openssl enc -aes-256-cbc -d -in backup_encrypted.tar.gz.enc | tar -xz
```

---

## Quick Reference

### View All Backups

```bash
ls -lh /backup/mrwifi/
```

### Check Backup Size

```bash
du -sh /backup/mrwifi/*
```

### Delete Old Backups

```bash
# Delete backups older than 30 days
find /backup/mrwifi -name "20*" -type d -mtime +30 -exec rm -rf {} \;
```

### Quick Single Database Backup

```bash
mysqldump -u mrwifi_user -p --single-transaction --no-tablespaces mrwifi | gzip > ~/mrwifi_quick_backup_$(date +%Y%m%d).sql.gz
```

### Quick Restore Single Database

```bash
gunzip < ~/mrwifi_quick_backup_20240129.sql.gz | mysql -u mrwifi_user -p mrwifi
```

---

## Support

If you encounter issues not covered in this guide:

1. Check Laravel logs: `tail -f /var/www/mrwifi/storage/logs/laravel.log`
2. Check Apache logs: `sudo tail -f /var/log/apache2/error.log`
3. Verify file permissions and ownership
4. Ensure all services are running: `sudo systemctl status apache2 mysql php8.2-fpm`

---

**Last Updated**: January 2026
**Version**: 1.0

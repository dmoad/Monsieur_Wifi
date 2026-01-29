# Backup and Restore Guide - Monsieur WiFi Laravel Project

This document provides comprehensive instructions for backing up and restoring the Monsieur WiFi Laravel application, including databases, code, configuration files, uploaded files, and server settings.

> **Note**: This guide is designed for **Ubuntu/Debian** systems. All commands assume an Ubuntu/Debian environment.

## Table of Contents

1. [What to Backup](#what-to-backup)
2. [Manual Backup Procedures](#manual-backup-procedures)
3. [Automated Backup Script](#automated-backup-script)
4. [Restore Procedures](#restore-procedures)
5. [Backup Storage Recommendations](#backup-storage-recommendations)
6. [Troubleshooting](#troubleshooting)

---

## What to Backup

The following components need to be backed up:

### 1. **Application Code**
- Git repository (if using version control)
- Composer dependencies (`vendor/` directory)
- Application files in `/var/www/mrwifi/`

### 2. **Databases**
- **Main Database** (`mrwifi`) - Contains users, locations, devices, settings, etc.
- **RADIUS Database** (`mrwifi_radius`) - Contains WiFi authentication data

### 3. **Configuration Files**
- `.env` file (environment variables)
- Apache2 virtual host configuration (`/etc/apache2/sites-available/mrwifi.conf`)
- SSL certificates (Let's Encrypt certificates)

### 4. **Uploaded Files**
- Profile pictures (`/var/www/mrwifi/public/uploads/profile_pictures/`)
- Storage files (`/var/www/mrwifi/storage/app/`)
- Any other user-uploaded content

### 5. **Server Configuration** (Optional but Recommended)
- Apache2 configuration files
- PHP configuration
- System packages list

---

## Manual Backup Procedures

### 1. Create Backup Directory

Create a directory to store all backups:

```bash
sudo mkdir -p /backup/mrwifi
sudo chown $USER:$USER /backup/mrwifi
cd /backup/mrwifi
```

Create dated subdirectories:

```bash
BACKUP_DATE=$(date +%Y%m%d_%H%M%S)
mkdir -p $BACKUP_DATE
cd $BACKUP_DATE
```

### 2. Backup Databases

#### Backup Main Database

```bash
mysqldump -u mrwifi_user -p mrwifi > mrwifi_database_$(date +%Y%m%d_%H%M%S).sql
```

Or with compression:

```bash
mysqldump -u mrwifi_user -p mrwifi | gzip > mrwifi_database_$(date +%Y%m%d_%H%M%S).sql.gz
```

#### Backup RADIUS Database

```bash
mysqldump -u mrwifi_user -p mrwifi_radius > mrwifi_radius_database_$(date +%Y%m%d_%H%M%S).sql
```

Or with compression:

```bash
mysqldump -u mrwifi_user -p mrwifi_radius | gzip > mrwifi_radius_database_$(date +%Y%m%d_%H%M%S).sql.gz
```

#### Backup Both Databases Together

```bash
mysqldump -u mrwifi_user -p --databases mrwifi mrwifi_radius > both_databases_$(date +%Y%m%d_%H%M%S).sql
```

### 3. Backup Application Code

#### Option A: If Using Git

```bash
cd /var/www/mrwifi
git archive --format=tar.gz --output=/backup/mrwifi/$BACKUP_DATE/application_code.tar.gz HEAD
```

#### Option B: Full Application Backup (Including Vendor)

```bash
cd /var/www
tar -czf /backup/mrwifi/$BACKUP_DATE/application_full.tar.gz \
    --exclude='mrwifi/node_modules' \
    --exclude='mrwifi/.git' \
    --exclude='mrwifi/storage/logs/*' \
    --exclude='mrwifi/storage/framework/cache/*' \
    --exclude='mrwifi/storage/framework/sessions/*' \
    --exclude='mrwifi/storage/framework/views/*' \
    mrwifi/
```

#### Option C: Application Code Only (Excluding Vendor)

```bash
cd /var/www/mrwifi
tar -czf /backup/mrwifi/$BACKUP_DATE/application_code.tar.gz \
    --exclude='vendor' \
    --exclude='node_modules' \
    --exclude='.git' \
    --exclude='storage/logs/*' \
    --exclude='storage/framework/cache/*' \
    --exclude='storage/framework/sessions/*' \
    --exclude='storage/framework/views/*' \
    .
```

### 4. Backup Configuration Files

#### Backup .env File

```bash
cp /var/www/mrwifi/.env /backup/mrwifi/$BACKUP_DATE/.env
```

#### Backup Apache2 Configuration

```bash
sudo cp /etc/apache2/sites-available/mrwifi.conf /backup/mrwifi/$BACKUP_DATE/apache2_mrwifi.conf
```

#### Backup SSL Certificates (Let's Encrypt)

```bash
sudo tar -czf /backup/mrwifi/$BACKUP_DATE/letsencrypt_certs.tar.gz \
    /etc/letsencrypt/live/portal.monsieur-wifi.com/ \
    /etc/letsencrypt/archive/portal.monsieur-wifi.com/ 2>/dev/null || true
```

> **Note**: Replace `portal.monsieur-wifi.com` with your actual domain name.

### 5. Backup Uploaded Files

#### Backup Profile Pictures

```bash
tar -czf /backup/mrwifi/$BACKUP_DATE/uploads_profile_pictures.tar.gz \
    -C /var/www/mrwifi/public/uploads profile_pictures/
```

#### Backup Storage Files

```bash
tar -czf /backup/mrwifi/$BACKUP_DATE/storage_files.tar.gz \
    -C /var/www/mrwifi storage/app/
```

### 6. Backup Server Configuration (Optional)

#### Backup Installed Packages List

```bash
dpkg --get-selections > /backup/mrwifi/$BACKUP_DATE/installed_packages.txt
```

#### Backup Apache2 Configuration Directory

```bash
sudo tar -czf /backup/mrwifi/$BACKUP_DATE/apache2_config.tar.gz \
    /etc/apache2/sites-available/ \
    /etc/apache2/sites-enabled/ \
    /etc/apache2/apache2.conf \
    /etc/apache2/conf-available/ \
    /etc/apache2/conf-enabled/ 2>/dev/null || true
```

### 7. Create Backup Manifest

Create a file listing all backup contents:

```bash
cd /backup/mrwifi/$BACKUP_DATE
ls -lh > backup_manifest.txt
echo "Backup created: $(date)" >> backup_manifest.txt
echo "Server: $(hostname)" >> backup_manifest.txt
```

---

## Automated Backup Script

Create an automated backup script for regular backups:

### Create Backup Script

```bash
sudo nano /usr/local/bin/mrwifi-backup.sh
```

Add the following content:

```bash
#!/bin/bash

# Monsieur WiFi Backup Script
# This script creates a complete backup of the application

# Configuration
BACKUP_BASE_DIR="/backup/mrwifi"
DB_USER="mrwifi_user"
APP_DIR="/var/www/mrwifi"
DOMAIN="portal.monsieur-wifi.com"  # Replace with your domain

# Create backup directory with timestamp
BACKUP_DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="$BACKUP_BASE_DIR/$BACKUP_DATE"
mkdir -p "$BACKUP_DIR"

# Log file
LOG_FILE="$BACKUP_DIR/backup.log"
exec > >(tee -a "$LOG_FILE")
exec 2>&1

echo "=========================================="
echo "Monsieur WiFi Backup Started"
echo "Date: $(date)"
echo "Backup Directory: $BACKUP_DIR"
echo "=========================================="

# Function to check if command succeeded
check_success() {
    if [ $? -eq 0 ]; then
        echo "✓ $1 completed successfully"
    else
        echo "✗ $1 failed!"
        exit 1
    fi
}

# 1. Backup Main Database
echo "Backing up main database..."
mysqldump -u "$DB_USER" -p"$DB_PASSWORD" mrwifi | gzip > "$BACKUP_DIR/mrwifi_database.sql.gz"
check_success "Main database backup"

# 2. Backup RADIUS Database
echo "Backing up RADIUS database..."
mysqldump -u "$DB_USER" -p"$DB_PASSWORD" mrwifi_radius | gzip > "$BACKUP_DIR/mrwifi_radius_database.sql.gz"
check_success "RADIUS database backup"

# 3. Backup .env file
echo "Backing up .env file..."
cp "$APP_DIR/.env" "$BACKUP_DIR/.env"
check_success ".env file backup"

# 4. Backup Apache2 configuration
echo "Backing up Apache2 configuration..."
sudo cp /etc/apache2/sites-available/mrwifi.conf "$BACKUP_DIR/apache2_mrwifi.conf"
check_success "Apache2 configuration backup"

# 5. Backup SSL certificates
echo "Backing up SSL certificates..."
sudo tar -czf "$BACKUP_DIR/letsencrypt_certs.tar.gz" \
    "/etc/letsencrypt/live/$DOMAIN/" \
    "/etc/letsencrypt/archive/$DOMAIN/" 2>/dev/null
if [ $? -eq 0 ]; then
    echo "✓ SSL certificates backup completed"
else
    echo "⚠ SSL certificates backup skipped (may not exist)"
fi

# 6. Backup uploaded files
echo "Backing up uploaded files..."
if [ -d "$APP_DIR/public/uploads/profile_pictures" ]; then
    tar -czf "$BACKUP_DIR/uploads_profile_pictures.tar.gz" \
        -C "$APP_DIR/public/uploads" profile_pictures/
    check_success "Profile pictures backup"
else
    echo "⚠ Profile pictures directory not found, skipping..."
fi

# 7. Backup storage files
echo "Backing up storage files..."
if [ -d "$APP_DIR/storage/app" ]; then
    tar -czf "$BACKUP_DIR/storage_files.tar.gz" \
        -C "$APP_DIR/storage" app/
    check_success "Storage files backup"
else
    echo "⚠ Storage directory not found, skipping..."
fi

# 8. Backup application code (excluding vendor, node_modules, etc.)
echo "Backing up application code..."
cd "$APP_DIR"
tar -czf "$BACKUP_DIR/application_code.tar.gz" \
    --exclude='vendor' \
    --exclude='node_modules' \
    --exclude='.git' \
    --exclude='storage/logs/*' \
    --exclude='storage/framework/cache/*' \
    --exclude='storage/framework/sessions/*' \
    --exclude='storage/framework/views/*' \
    .
check_success "Application code backup"

# 9. Create backup manifest
echo "Creating backup manifest..."
cd "$BACKUP_DIR"
ls -lh > backup_manifest.txt
echo "Backup created: $(date)" >> backup_manifest.txt
echo "Server: $(hostname)" >> backup_manifest.txt
echo "Backup size: $(du -sh . | cut -f1)" >> backup_manifest.txt

# 10. Compress entire backup directory
echo "Compressing backup directory..."
cd "$BACKUP_BASE_DIR"
tar -czf "${BACKUP_DATE}.tar.gz" "$BACKUP_DATE"
check_success "Backup compression"

# Clean up uncompressed directory (optional)
# rm -rf "$BACKUP_DIR"

echo "=========================================="
echo "Backup completed successfully!"
echo "Backup location: ${BACKUP_BASE_DIR}/${BACKUP_DATE}.tar.gz"
echo "Backup size: $(du -sh ${BACKUP_BASE_DIR}/${BACKUP_DATE}.tar.gz | cut -f1)"
echo "=========================================="

# Optional: Remove backups older than 30 days
find "$BACKUP_BASE_DIR" -name "*.tar.gz" -type f -mtime +30 -delete
echo "Cleaned up backups older than 30 days"
```

### Make Script Executable

```bash
sudo chmod +x /usr/local/bin/mrwifi-backup.sh
```

### Configure Database Password

For automated backups, you can either:

**Option A: Use MySQL credentials file (Recommended)**

Create `.my.cnf` file:

```bash
sudo nano ~/.my.cnf
```

Add:

```ini
[client]
user=mrwifi_user
password=your_database_password_here
```

Set permissions:

```bash
chmod 600 ~/.my.cnf
```

Then modify the script to remove `-p"$DB_PASSWORD"`:

```bash
mysqldump -u "$DB_USER" mrwifi | gzip > ...
```

**Option B: Prompt for password**

Keep the script as-is and it will prompt for password each time.

### Set Up Cron Job for Automated Backups

Add to crontab for daily backups at 2 AM:

```bash
crontab -e
```

Add this line:

```
0 2 * * * /usr/local/bin/mrwifi-backup.sh
```

For weekly backups (every Sunday at 2 AM):

```
0 2 * * 0 /usr/local/bin/mrwifi-backup.sh
```

---

## Restore Procedures

### Prerequisites for Restore

1. Ensure you have a backup file or directory
2. Ensure MySQL/MariaDB is installed and running
3. Ensure Apache2 is installed
4. Ensure PHP 8.2+ is installed

### 1. Extract Backup

```bash
cd /backup/mrwifi
tar -xzf backup_YYYYMMDD_HHMMSS.tar.gz
cd backup_YYYYMMDD_HHMMSS
```

Or if using uncompressed backup directory:

```bash
cd /backup/mrwifi/backup_YYYYMMDD_HHMMSS
```

### 2. Restore Databases

#### Restore Main Database

```bash
# If compressed
gunzip < mrwifi_database_*.sql.gz | mysql -u mrwifi_user -p mrwifi

# If uncompressed
mysql -u mrwifi_user -p mrwifi < mrwifi_database_*.sql
```

#### Restore RADIUS Database

```bash
# If compressed
gunzip < mrwifi_radius_database_*.sql.gz | mysql -u mrwifi_user -p mrwifi_radius

# If uncompressed
mysql -u mrwifi_user -p mrwifi_radius < mrwifi_radius_database_*.sql
```

#### Restore Both Databases Together

```bash
mysql -u mrwifi_user -p < both_databases_*.sql
```

### 3. Restore Application Code

#### Option A: If Using Git

```bash
cd /var/www
rm -rf mrwifi
git clone <repository-url> mrwifi
cd mrwifi
git checkout <commit-hash>  # If needed
```

#### Option B: Restore from Backup Archive

```bash
cd /var/www
rm -rf mrwifi
tar -xzf /backup/mrwifi/backup_YYYYMMDD_HHMMSS/application_code.tar.gz
mv mrwifi /var/www/mrwifi
```

### 4. Restore Configuration Files

#### Restore .env File

```bash
cp /backup/mrwifi/backup_YYYYMMDD_HHMMSS/.env /var/www/mrwifi/.env
```

**Important**: Review and update the `.env` file if needed (database credentials, domain names, etc.)

#### Restore Apache2 Configuration

```bash
sudo cp /backup/mrwifi/backup_YYYYMMDD_HHMMSS/apache2_mrwifi.conf /etc/apache2/sites-available/mrwifi.conf
sudo a2ensite mrwifi.conf
sudo systemctl reload apache2
```

#### Restore SSL Certificates

```bash
sudo tar -xzf /backup/mrwifi/backup_YYYYMMDD_HHMMSS/letsencrypt_certs.tar.gz -C /
```

### 5. Restore Uploaded Files

#### Restore Profile Pictures

```bash
cd /var/www/mrwifi/public/uploads
tar -xzf /backup/mrwifi/backup_YYYYMMDD_HHMMSS/uploads_profile_pictures.tar.gz
```

#### Restore Storage Files

```bash
cd /var/www/mrwifi/storage
tar -xzf /backup/mrwifi/backup_YYYYMMDD_HHMMSS/storage_files.tar.gz
```

### 6. Restore Application Dependencies

```bash
cd /var/www/mrwifi
composer install --no-dev --optimize-autoloader
```

### 7. Set Permissions

```bash
sudo chown -R www-data:www-data /var/www/mrwifi
sudo chmod -R 755 /var/www/mrwifi
sudo chmod -R 775 /var/www/mrwifi/storage
sudo chmod -R 775 /var/www/mrwifi/bootstrap/cache
sudo chmod -R 775 /var/www/mrwifi/public/uploads
```

### 8. Clear Caches

```bash
cd /var/www/mrwifi
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

### 9. Recreate Storage Link

```bash
cd /var/www/mrwifi
php artisan storage:link
```

### 10. Verify Restore

```bash
# Check application status
php artisan about

# Test database connections
php artisan tinker
# Then run: DB::connection()->getPdo();
# And: DB::connection('radius')->getPdo();
```

### 11. Restart Services

```bash
sudo systemctl restart apache2
sudo systemctl restart php8.2-fpm
```

---

## Backup Storage Recommendations

### Local Storage

- **Location**: `/backup/mrwifi/` (or custom location)
- **Retention**: Keep daily backups for 7 days, weekly backups for 4 weeks, monthly backups for 12 months
- **Space**: Ensure sufficient disk space (backups can be large)

### Remote Storage Options

#### 1. **SCP/SFTP to Remote Server**

```bash
scp /backup/mrwifi/backup_*.tar.gz user@remote-server:/backup/mrwifi/
```

#### 2. **AWS S3**

Install AWS CLI and configure:

```bash
aws s3 cp /backup/mrwifi/backup_*.tar.gz s3://your-bucket-name/mrwifi-backups/
```

#### 3. **Google Cloud Storage**

```bash
gsutil cp /backup/mrwifi/backup_*.tar.gz gs://your-bucket-name/mrwifi-backups/
```

#### 4. **rsync to Remote Server**

```bash
rsync -avz /backup/mrwifi/ user@remote-server:/backup/mrwifi/
```

### Backup Rotation Script

Create a script to manage backup retention:

```bash
#!/bin/bash
# /usr/local/bin/mrwifi-backup-cleanup.sh

BACKUP_DIR="/backup/mrwifi"

# Keep daily backups for 7 days
find "$BACKUP_DIR" -name "*.tar.gz" -type f -mtime +7 -delete

# Keep weekly backups (Sunday backups) for 30 days
# This requires naming convention or modification date tracking

echo "Backup cleanup completed"
```

---

## Troubleshooting

### Backup Issues

#### Issue: "mysqldump: Access denied"

**Solution**: Check database credentials and permissions:

```bash
mysql -u mrwifi_user -p -e "SHOW DATABASES;"
```

#### Issue: "Permission denied" when backing up files

**Solution**: Use sudo for system files:

```bash
sudo tar -czf backup.tar.gz /etc/apache2/sites-available/
```

#### Issue: Backup file is too large

**Solution**: Exclude unnecessary files:

```bash
tar -czf backup.tar.gz --exclude='vendor' --exclude='node_modules' ...
```

### Restore Issues

#### Issue: "Database already exists" error

**Solution**: Drop and recreate database:

```sql
DROP DATABASE mrwifi;
CREATE DATABASE mrwifi CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

#### Issue: "Table doesn't exist" after restore

**Solution**: Ensure RADIUS schema is imported:

```bash
mysql -u mrwifi_user -p mrwifi_radius < /var/www/mrwifi/database/radius_base_schema.sql
```

#### Issue: Application shows errors after restore

**Solution**: 
1. Clear all caches: `php artisan config:clear && php artisan cache:clear`
2. Check `.env` file configuration
3. Verify file permissions
4. Check Apache2 error logs: `sudo tail -f /var/log/apache2/error.log`

#### Issue: SSL certificate errors after restore

**Solution**: Renew SSL certificate:

```bash
sudo certbot renew --force-renewal
sudo systemctl restart apache2
```

---

## Best Practices

1. **Regular Backups**: Set up automated daily backups
2. **Test Restores**: Periodically test restore procedures to ensure backups are valid
3. **Off-Site Storage**: Keep backups in multiple locations (local + remote)
4. **Documentation**: Keep track of backup locations and restore procedures
5. **Encryption**: Consider encrypting sensitive backups
6. **Monitoring**: Monitor backup script execution and disk space
7. **Version Control**: Use Git for application code to minimize code backup needs

---

## Quick Reference

### Create Full Backup (One Command)

```bash
BACKUP_DIR="/backup/mrwifi/$(date +%Y%m%d_%H%M%S)" && \
mkdir -p "$BACKUP_DIR" && \
mysqldump -u mrwifi_user -p mrwifi | gzip > "$BACKUP_DIR/mrwifi.sql.gz" && \
mysqldump -u mrwifi_user -p mrwifi_radius | gzip > "$BACKUP_DIR/mrwifi_radius.sql.gz" && \
cp /var/www/mrwifi/.env "$BACKUP_DIR/.env" && \
tar -czf "$BACKUP_DIR/app.tar.gz" -C /var/www mrwifi --exclude='vendor' --exclude='node_modules' && \
echo "Backup completed: $BACKUP_DIR"
```

### Restore from Backup (Quick)

```bash
BACKUP_DIR="/backup/mrwifi/backup_YYYYMMDD_HHMMSS"
gunzip < "$BACKUP_DIR/mrwifi.sql.gz" | mysql -u mrwifi_user -p mrwifi
gunzip < "$BACKUP_DIR/mrwifi_radius.sql.gz" | mysql -u mrwifi_user -p mrwifi_radius
cp "$BACKUP_DIR/.env" /var/www/mrwifi/.env
cd /var/www/mrwifi && composer install --no-dev
php artisan config:clear && php artisan cache:clear
```

---

**Last Updated**: January 2026

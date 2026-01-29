# Installation Guide - Monsieur WiFi Laravel Project

This document provides step-by-step instructions for installing and setting up the Monsieur WiFi Laravel application.

> **Note**: This installation guide is specifically designed for **Ubuntu/Debian** systems. All commands and configurations assume an Ubuntu/Debian environment.

## Table of Contents

1. [System Requirements](#system-requirements)
2. [Prerequisites](#prerequisites)
3. [Installation Steps](#installation-steps)
4. [Database Setup](#database-setup)
5. [Environment Configuration](#environment-configuration)
6. [Application Setup](#application-setup)
7. [Apache2 Configuration](#apache2-configuration)
8. [Storage & Permissions](#storage--permissions)
9. [SSL Certificate Setup (Let's Encrypt)](#ssl-certificate-setup-lets-encrypt)
10. [Verification](#verification)
11. [Production Optimization](#production-optimization)
12. [Troubleshooting](#troubleshooting)

---

## System Requirements

### Server Requirements

- **PHP**: >= 8.2
- **Composer**: Latest version
- **MySQL/MariaDB**: >= 5.7 or >= 10.3
- **Web Server**: Apache 2.4+
- **Memory**: Minimum 512MB RAM (1GB+ recommended)
- **Disk Space**: Minimum 500MB free space

### PHP Extensions Required

```bash
php -m | grep -E 'pdo_mysql|mbstring|xml|curl|zip|gd|openssl|json|bcmath|fileinfo'
```

Required PHP extensions:
- `pdo_mysql` - MySQL database driver
- `mbstring` - Multibyte string handling
- `xml` - XML parsing
- `curl` - HTTP client
- `zip` - Archive handling
- `gd` - Image processing
- `openssl` - Encryption
- `json` - JSON support
- `bcmath` - Arbitrary precision mathematics
- `fileinfo` - File type detection

---

## Prerequisites

### 1. Create a Sudo User (Recommended)

For security and best practices, create a dedicated user with sudo privileges to manage the server and handle the installation. Avoid running commands as the root user when possible.

#### Create a New User

```bash
sudo adduser mrwifi-admin
```

Follow the prompts to set a password and user information.

#### Add User to Sudo Group

```bash
sudo usermod -aG sudo mrwifi-admin
```

#### Verify Sudo Access

Switch to the new user and test sudo:

```bash
su - mrwifi-admin
sudo whoami
```

You should see `root` as the output, confirming sudo access works.

#### Switch to the New User

For the remainder of this installation guide, use the sudo user:

```bash
su - mrwifi-admin
```

> **Note**: All commands in this guide assume you're using a user with sudo privileges. If you're logged in as root, you can omit `sudo` from commands, but it's recommended to use a sudo user for better security.

### 2. Install PHP 8.2+ and Required Extensions

```bash
sudo apt update
sudo apt install -y software-properties-common
sudo add-apt-repository -y ppa:ondrej/php
sudo apt update
sudo apt install php8.2 php8.2-cli php8.2-fpm php8.2-mysql php8.2-mbstring \
    php8.2-xml php8.2-curl php8.2-zip php8.2-gd php8.2-common php8.2-bcmath \
    php8.2-fileinfo php8.2-intl libapache2-mod-php8.2
```

### 3. Install Composer

```bash
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
composer --version
```

### 4. Install Apache2

```bash
sudo apt install apache2
sudo a2enmod rewrite
sudo systemctl enable apache2
sudo systemctl start apache2
suod 
```

### 5. Install MySQL/MariaDB

```bash
sudo apt install mysql-server
sudo mysql_secure_installation
```

While running `mysql_secure_installation`, set password validation policy as 2 (Most secure) and answer "Yes" to all other questions.

### 6. Configure Firewall (UFW)

Configure the Uncomplicated Firewall (UFW) to allow necessary ports before proceeding with installation:

```bash
# Enable UFW if not already enabled
sudo ufw enable

# Allow SSH (IMPORTANT: Do this first to avoid losing connection)
sudo ufw allow 22/tcp

# Allow HTTP and HTTPS
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp

# Check UFW status
sudo ufw status
```

Expected output:
```
Status: active

To                         Action      From
--                         ------      ----
22/tcp                     ALLOW       Anywhere
80/tcp                     ALLOW       Anywhere
443/tcp                    ALLOW       Anywhere
```

> **Warning**: If you're connected via SSH, make sure to allow port 22 before enabling UFW to avoid being locked out of your server.

---

## Installation Steps

### 1. Clone the Repository

```bash
cd /var/www
sudo mkdir -p mrwifi
sudo chown mrwifi-admin:mrwifi-admin mrwifi
git clone https://github.com/kmrinal/mrwifi.git mrwifi
cd mrwifi
```

> **Note**: Replace `mrwifi-admin` with your actual sudo username if different.

### 2. Install PHP Dependencies

```bash
composer install --no-dev --optimize-autoloader
```

For development:
```bash
composer install
```

---

## Database Setup

This application requires **two separate databases**:
1. **Main Database** - Application data (users, locations, devices, etc.)
2. **RADIUS Database** - WiFi authentication data

### 1. Create Databases

Log into MySQL:
```bash
sudo mysql -u root -p
```

Create databases and users:
```sql
-- Create main database
CREATE DATABASE mrwifi CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Create RADIUS database
CREATE DATABASE mrwifi_radius CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Create database user (replace 'secure_password' with a strong password)
CREATE USER 'mrwifi_user'@'localhost' IDENTIFIED BY 'secure_password';

-- Grant privileges
GRANT ALL PRIVILEGES ON mrwifi.* TO 'mrwifi_user'@'localhost';
GRANT ALL PRIVILEGES ON mrwifi_radius.* TO 'mrwifi_user'@'localhost';

-- Apply changes
FLUSH PRIVILEGES;

-- Exit MySQL
EXIT;
```

### 2. Set Up RADIUS Database Tables

The RADIUS database requires specific tables for WiFi authentication. Import the provided RADIUS base schema before running migrations.

Import the RADIUS schema file:

```bash
mysql -u mrwifi_user -p mrwifi_radius < /var/www/mrwifi/database/radius_base_schema.sql
```

Or if you're already in the project directory:

```bash
mysql -u mrwifi_user -p mrwifi_radius < database/radius_base_schema.sql
```

This will create the following RADIUS tables:
- `radcheck` - User authentication credentials
- `radreply` - User reply attributes
- `radgroupcheck` - Group check attributes
- `radgroupreply` - Group reply attributes
- `radusergroup` - User-group associations
- `radacct` - Accounting records
- `radpostauth` - Post-authentication logging
- `nas` - Network Access Server information
- `nasreload` - NAS reload tracking

⚠️ **IMPORTANT**: This step must be completed **before** running Laravel migrations, as the application expects these tables to exist.

---

## Environment Configuration

### 1. Copy Environment File

```bash
cp env.example .env
```

### 2. Configure Environment Variables

Edit `.env` file and configure the following **REQUIRED** variables:

```env
# Application Configuration
APP_NAME=Monsieur-WiFi
APP_ENV=production  # or 'local' for development
APP_DEBUG=false     # Set to false in production
APP_URL=https://your-domain.com

# Database Configuration - Main Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=mrwifi
DB_USERNAME=mrwifi_user
DB_PASSWORD=your_secure_password_here

# Database Configuration - RADIUS Database
DB_CONNECTION_RADIUS=mysql
DB_HOST_RADIUS=127.0.0.1
DB_PORT_RADIUS=3306
DB_DATABASE_RADIUS=mrwifi_radius
DB_USERNAME_RADIUS=mrwifi_user
DB_PASSWORD_RADIUS=your_secure_password_here

# JWT Configuration
JWT_SECRET=your_jwt_secret_key_here  # Generate a secure random string

# Captive Portal Settings
VERIFICATION_CODE=your_verification_code_here
GUEST_LOGIN_URL=https://your-domain.com/guest-login
SOLUTION_URL=https://your-redirect-url.com
```

**Generate JWT Secret:**
```bash
php artisan jwt:secret
php artisan key:generate
```

### 3. Configure Optional Settings

#### SMS Gateway Configuration (Required if using SMS OTP)

If you plan to use SMS-based OTP authentication, configure the BulkSMS gateway:

```env
# SMS Gateway Configuration
BULKSMS_USERNAME=your_bulksms_username_here
BULKSMS_PASSWORD=your_bulksms_password_here
ENABLE_SMS_SENDING=true  # Set to false to disable SMS sending
```

**Note**: You'll need to sign up for a BulkSMS account at [bulksms.com](https://www.bulksms.com) to obtain your API credentials.

#### Branding & UI Configuration

Customize the application branding:

```env
# Branding Configuration for Facebook etc
APP_BRAND_NAME=monsieur-wifi
APP_BRAND_LOGO=/app-assets/mrwifi-assets/Mr-Wifi.PNG
APP_BRAND_WELCOME_MESSAGE="Welcome to {{APP_NAME}}"
APP_BRAND_TERMS_OF_SERVICE_URL=/terms-of-service
APP_BRAND_PRIVACY_POLICY_URL=/privacy-policy
```

#### Google Maps API (Required for location services)

If you need location services and maps functionality:

```env
GOOGLE_MAPS_KEY=your_google_maps_api_key_here
```

**Note**: Obtain a Google Maps API key from the [Google Cloud Console](https://console.cloud.google.com/). Enable the Maps JavaScript API and Geocoding API.

#### Captive Portal Whitelist Domains

Configure domain and server whitelists for captive portal functionality:

```env
# Guest Network Whitelists
GUEST_WHITELIST_SERVERS=127.0.0.1,your-server.com
GUEST_WHITELIST_DOMAINS=.example.com,.test.com

# Social Media Whitelists (for social login)
TWITTER_WHITELIST_DOMAINS=.twitter.com,.x.com,.twimg.com,.google.com,.googleapis.com,.gstatic.com
TWITTER_WHITELIST_SERVERS=twitter.com,x.com,twimg.com,google.com,googleapis.com,gstatic.com
FACEBOOK_WHITELIST_DOMAINS=.facebook.com,.fbcdn.net,.graph.facebook.com
FACEBOOK_WHITELIST_SERVERS=facebook.com,fbcdn.net,graph.facebook.com
GOOGLE_WHITELIST_DOMAINS=.google.com,.googleapis.com,.gstatic.com
GOOGLE_WHITELIST_SERVERS=google.com,googleapis.com,gstatic.com
```

These whitelists allow specified domains and servers to be accessible during the captive portal authentication process.

#### Mail Configuration (Optional)

If you need to send emails from the application:

```env
# Mail Configuration
MAIL_MAILER=smtp  # smtp, sendmail, mailgun, ses, postmark, log
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls  # tls, ssl, or null
MAIL_FROM_ADDRESS="noreply@your-domain.com"
MAIL_FROM_NAME="${APP_NAME}"
```

**Note**: For development, you can use `MAIL_MAILER=log` which saves emails to `storage/logs/laravel.log`.

---

## Application Setup

> **Note**: Ensure you have imported the RADIUS database schema (`database/radius_base_schema.sql`) as described in the [Database Setup](#database-setup) section before proceeding with migrations.

### 1. Generate Application Key

Generate the Laravel application encryption key. This **must** be done before running migrations:

```bash
php artisan key:generate
```

This creates a secure encryption key in your `.env` file (`APP_KEY`).

### 2. Run Database Migrations

```bash
php artisan migrate
```

This will create all necessary tables in the main database.

### 3. Seed Database

```bash
php artisan db:seed
```

This will:
- Create admin users (from `UserSeeder`)
- Create domain categories (from `CategorySeeder`)
- Create blocked domains (from `BlockedDomainSeeder`)

**Default Admin Users Created:**
- Email: `admin@monsieur-wifi.com`
- Password: `abcd1234`
- Email: `administrator@monsieur-wifi.com`
- Password: `abcd1234`

⚠️ **IMPORTANT**: Change these passwords immediately after first login!

### 4. Create Storage Link

```bash
php artisan storage:link
```

This creates a symbolic link from `public/storage` to `storage/app/public`.

---

## Storage & Permissions

### 1. Set Directory Permissions

After installation is complete, set proper ownership and permissions:

```bash
# First, ensure your user has ownership for running Composer and Artisan commands
sudo chown -R mrwifi-admin:www-data /var/www/mrwifi

# Set directory permissions
sudo find /var/www/mrwifi -type d -exec chmod 755 {} \;

# Set file permissions
sudo find /var/www/mrwifi -type f -exec chmod 644 {} \;

# Make storage and cache writable for web server
sudo chmod -R 775 /var/www/mrwifi/storage
sudo chmod -R 775 /var/www/mrwifi/bootstrap/cache

# Make public/uploads writable (for file uploads like profile pictures)
sudo chmod -R 775 /var/www/mrwifi/public/uploads

# Ensure www-data can write to these directories
sudo chgrp -R www-data /var/www/mrwifi/storage /var/www/mrwifi/bootstrap/cache /var/www/mrwifi/public/uploads
```

> **Note**: Replace `mrwifi-admin` with your actual sudo username and `www-data` with your web server user if different.

### 2. Create Required Directories

Make sure you're in the project directory first:

```bash
cd /var/www/mrwifi
mkdir -p storage/framework/{sessions,views,cache}
mkdir -p storage/logs
mkdir -p public/uploads/profile_pictures
```

---

## Apache2 Configuration

> **Note**: Throughout this section, `portal.monsieur-wifi.com` is used as an example domain. Replace it with your actual domain name in all configuration files and commands.

### 1. Create Apache Virtual Host (HTTP - Port 80)

Create a new virtual host configuration file:

```bash
sudo nano /etc/apache2/sites-available/mrwifi.conf
```

Add the following configuration for HTTP (port 80):

```apache
<VirtualHost *:80>
    ServerName portal.monsieur-wifi.com
    ServerAdmin webmaster@localhost
    DocumentRoot /var/www/mrwifi/public

    <Directory /var/www/mrwifi/public>
        Options FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/error.log
    CustomLog ${APACHE_LOG_DIR}/access.log combined
</VirtualHost>
```

> **Important**: `portal.monsieur-wifi.com` is an example domain. Replace it with your actual domain name throughout this configuration file.

### 2. Enable Required Apache Modules

```bash
sudo a2enmod rewrite
sudo a2enmod proxy
sudo a2enmod proxy_fcgi
sudo a2enmod setenvif
```

### 3. Enable the Site

```bash
sudo a2ensite mrwifi.conf
sudo systemctl restart apache2
```

### 4. Verify Apache Configuration

```bash
sudo apache2ctl configtest
```

### 5. Configure PHP-FPM (if not already configured)

Ensure PHP-FPM is installed and running:

```bash
sudo apt install php8.2-fpm
sudo systemctl enable php8.2-fpm
sudo systemctl start php8.2-fpm
```

---

## SSL Certificate Setup (Let's Encrypt)

> **Note**: This step is optional but highly recommended for production environments. You can skip this for local development or testing.

### 1. Install Certbot

```bash
sudo apt update
sudo apt install certbot python3-certbot-apache
```

### 2. Obtain SSL Certificate

Run Certbot to automatically obtain and configure SSL for your domain:

```bash
sudo certbot --apache -d portal.monsieur-wifi.com
```

> **Important**: `portal.monsieur-wifi.com` is an example domain. Replace it with your actual domain name. For example, if your domain is `example.com`, use `sudo certbot --apache -d example.com`.

Certbot will:
- Automatically obtain an SSL certificate from Let's Encrypt
- Configure Apache to use HTTPS
- Set up automatic certificate renewal
- Create a redirect from HTTP to HTTPS

Follow the interactive prompts:
- Enter your email address (for renewal notifications)
- Agree to the terms of service
- Choose whether to redirect HTTP to HTTPS (recommended: Yes)

### 3. Verify SSL Configuration

After Certbot completes, your Apache configuration will be updated. The HTTPS virtual host will look similar to this:

```apache
<IfModule mod_ssl.c>
<VirtualHost *:443>
    ServerName portal.monsieur-wifi.com
    ServerAdmin webmaster@localhost
    DocumentRoot /var/www/mrwifi/public

    <Directory /var/www/mrwifi/public>
        Options FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/error.log
    CustomLog ${APACHE_LOG_DIR}/access.log combined

    Include /etc/letsencrypt/options-ssl-apache.conf
    SSLCertificateFile /etc/letsencrypt/live/portal.monsieur-wifi.com/fullchain.pem
    SSLCertificateKeyFile /etc/letsencrypt/live/portal.monsieur-wifi.com/privkey.pem
</VirtualHost>
</IfModule>
```

> **Note**: Certbot will automatically replace `portal.monsieur-wifi.com` with your actual domain name in the configuration file and certificate paths.

### 4. Test Certificate Renewal

Let's Encrypt certificates expire after 90 days. Certbot sets up automatic renewal, but you can test it:

```bash
sudo certbot renew --dry-run
```

### 5. Verify SSL is Working

1. Restart Apache:
   ```bash
   sudo systemctl restart apache2
   ```

2. Test your site:
   - Visit `https://your-domain.com` in your browser (replace with your actual domain)
   - Verify the SSL certificate is valid (green padlock icon)
   - Check that HTTP redirects to HTTPS

### Troubleshooting SSL Setup

- **Domain not accessible**: Ensure your domain's DNS A record points to your server's IP address
- **Port 80/443 blocked**: Verify ports 80 and 443 are open in your firewall (should have been configured in Prerequisites step 6):
  ```bash
  sudo ufw status
  ```
- **Certificate renewal fails**: Check Certbot logs:
  ```bash
  sudo tail -f /var/log/letsencrypt/letsencrypt.log
  ```

---

## Verification

### 1. Check Application Status

```bash
php artisan about
```

### 2. Test Database Connections

```bash
php artisan tinker
```

Then in tinker:
```php
DB::connection()->getPdo();
DB::connection('radius')->getPdo();
exit
```

### 3. Access the Application

Open your browser and navigate to:
```
http://your-domain.com
```

Or if testing locally:
```
http://your-server-ip
```

### 4. Test Admin Login

This is the final verification step to confirm your setup is complete and working properly.

1. Navigate to the login page in your browser
2. Use one of the default admin credentials created by the seeder:
   - **Email**: `admin@monsieur-wifi.com` or `administrator@monsieur-wifi.com`
   - **Password**: `abcd1234`
3. Click "Login"

**If the login is successful and you can access the admin dashboard**, your setup is complete and ready to use! 🎉

**Expected Result:**
- You should be redirected to the admin dashboard
- You can see the navigation menu and main interface
- No error messages or warnings appear

**⚠️ IMPORTANT SECURITY NOTE**: 
- Change the default admin passwords immediately after first login!
- Navigate to your profile/settings to update the password
- Use a strong password with a mix of uppercase, lowercase, numbers, and special characters

---

## Production Optimization

For production environments, optimize Laravel's performance:

```bash
# Cache configuration
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Optimize autoloader
composer install --optimize-autoloader --no-dev
```

> **Note**: When you make changes to configuration files, routes, or environment variables, remember to clear the cache:
> ```bash
> php artisan config:clear
> php artisan route:clear
> php artisan view:clear
> ```

---

## Troubleshooting

### Common Issues

#### 1. "No application encryption key has been specified"

**Solution:**
```bash
php artisan key:generate
```

#### 2. Database Connection Errors

**Check:**
- Database credentials in `.env`
- MySQL service is running: `sudo systemctl status mysql`
- Database and user exist
- User has proper permissions

**Test connection:**
```bash
mysql -u mrwifi_user -p -h 127.0.0.1 mrwifi
```

#### 3. Permission Denied Errors

**Solution:**
```bash
sudo chown -R www-data:www-data /var/www/mrwifi
sudo chmod -R 775 /var/www/mrwifi/storage
sudo chmod -R 775 /var/www/mrwifi/bootstrap/cache
```

#### 4. JWT Secret Not Set

**Solution:**
```bash
php artisan jwt:secret
```

Or manually add to `.env`:
```env
JWT_SECRET=your_generated_secret_here
```

#### 5. Storage Link Not Working

**Solution:**
```bash
php artisan storage:link
```

#### 6. 500 Internal Server Error

**Check logs:**
```bash
tail -f storage/logs/laravel.log
```

**Common causes:**
- Missing `.env` file
- Incorrect permissions
- Database connection issues
- Missing PHP extensions

#### 7. RADIUS Database Connection Issues

**Verify:**
- RADIUS database exists
- Credentials are correct in `.env`
- Connection is tested: `php artisan tinker` then `DB::connection('radius')->getPdo();`

#### 8. Apache Not Serving PHP Files

**Check:**
- PHP-FPM is installed and running: `sudo systemctl status php8.2-fpm`
- Apache modules are enabled: `sudo a2enmod proxy_fcgi`
- Virtual host configuration includes PHP handler

**Solution:**
```bash
sudo a2enmod proxy_fcgi setenvif
sudo a2enconf php8.2-fpm
sudo systemctl restart apache2
```

---

## Development Setup

For local development:

1. Set `APP_ENV=local` and `APP_DEBUG=true` in `.env`
2. Install dev dependencies: `composer install` (without `--no-dev`)
3. Run development server: `php artisan serve`
4. Or use Laravel Sail: `./vendor/bin/sail up`

---

## Additional Resources

- [Laravel Documentation](https://laravel.com/docs)
- [JWT Auth Documentation](https://jwt-auth.readthedocs.io/)
- [Apache Documentation](https://httpd.apache.org/docs/)

---

## Support

For issues or questions:
1. Check the logs: `storage/logs/laravel.log`
2. Review the documentation in `/documentation` folder
3. Check GitHub issues (if applicable)

---

**Last Updated**: January 2026

# Password Reset Implementation Guide

This document explains how to set up and use the password reset functionality in Monsieur WiFi.

## Overview

The password reset system allows users to reset their password via email if they forget it. The implementation includes:

1. **Password Reset Request Page** (`/password-reset`) - Where users enter their email
2. **Email Notification** - An HTML email sent with a reset link
3. **Reset Password Page** (`/reset-password`) - Where users enter their new password
4. **Backend API** - Two endpoints for handling the reset flow

## Files Created/Modified

### Views
- `resources/views/password-reset.blade.php` - Password reset request page
- `resources/views/reset-password.blade.php` - New password entry page
- `resources/views/emails/password-reset.blade.php` - Email template
- `resources/views/login.blade.php` - Updated "Forgot Password" link

### Backend
- `app/Mail/PasswordResetMail.php` - Mailable class for sending reset emails
- `app/Http/Controllers/AuthController.php` - Added two methods:
  - `sendPasswordResetLink()` - Generates token and sends email
  - `resetPassword()` - Validates token and updates password

### Routes
- `routes/web.php` - Added web routes for password reset pages
- `routes/api.php` - Added API routes:
  - `POST /api/auth/password-reset` - Request password reset
  - `POST /api/auth/reset-password` - Reset password with token

## Mail Configuration

To send password reset emails, you need to configure your mail settings in `.env`:

### Option 1: Using SMTP (Recommended for Production)

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@mrwifi.com
MAIL_FROM_NAME="${APP_NAME}"
```

### Option 2: Using Log Driver (Development/Testing)

```env
MAIL_MAILER=log
MAIL_LOG_CHANNEL=stack
```

When using the log driver, emails will be written to `storage/logs/laravel.log` instead of being sent.

### Option 3: Using Mailgun, Postmark, or SES

Refer to Laravel's mail documentation for configuring these services.

## How It Works

### 1. User Requests Password Reset

1. User visits `/password-reset` and enters their email address
2. System checks if the email exists in the database
3. If email exists:
   - Generates a random 64-character token
   - Hashes the token and stores it in `password_reset_tokens` table
   - Sends an email with a reset link containing the token
4. Returns success message (same message whether email exists or not, for security)

### 2. User Receives Email

The email contains:
- A button/link to reset the password
- The link format: `/reset-password?token={token}&email={email}`
- Token expiration notice (60 minutes)
- Security warnings

### 3. User Resets Password

1. User clicks the link in the email
2. System loads the reset password page
3. User enters new password and confirms it
4. System validates:
   - Token exists and matches
   - Token hasn't expired (60 minutes)
   - Email matches the token
   - Password meets requirements (min 8 characters)
   - Password confirmation matches
5. If valid, updates the password and deletes the used token
6. Redirects to login page

## Security Features

1. **Token Hashing**: Tokens are hashed before storage using bcrypt
2. **Token Expiration**: Tokens expire after 60 minutes
3. **Single Use**: Tokens are deleted after successful password reset
4. **Email Enumeration Protection**: Same message returned whether email exists or not
5. **Rate Limiting**: Can be added via middleware if needed
6. **Password Strength**: Enforced minimum 8 characters (can be enhanced)

## Database Schema

The `password_reset_tokens` table is created by default in Laravel:

```php
Schema::create('password_reset_tokens', function (Blueprint $table) {
    $table->string('email')->primary();
    $table->string('token');
    $table->timestamp('created_at')->nullable();
});
```

## API Endpoints

### Request Password Reset

```http
POST /api/auth/password-reset
Content-Type: application/json

{
  "email": "user@example.com"
}
```

**Response (Success):**
```json
{
  "message": "If an account exists with this email, a password reset link has been sent."
}
```

**Response (Error):**
```json
{
  "error": "Failed to send password reset email. Please try again later."
}
```

### Reset Password

```http
POST /api/auth/reset-password
Content-Type: application/json

{
  "token": "abc123...",
  "email": "user@example.com",
  "password": "newpassword123",
  "password_confirmation": "newpassword123"
}
```

**Response (Success):**
```json
{
  "message": "Password has been reset successfully. You can now login with your new password."
}
```

**Response (Error):**
```json
{
  "error": "Invalid or expired reset token."
}
```

## Multi-Language Support

Both password reset pages support English and French:

- Detects browser language automatically
- Saves user's language preference in localStorage
- Language switcher in the UI
- Translatable email templates (currently English only, French can be added)

## Testing

### Manual Testing

1. **Test Email Sending**:
   ```bash
   # Set MAIL_MAILER=log in .env
   # Request a password reset
   # Check storage/logs/laravel.log for the email content
   ```

2. **Test Password Reset Flow**:
   - Go to `/login`
   - Click "Reset Password"
   - Enter a valid email address
   - Check email (or logs)
   - Click the reset link
   - Enter new password
   - Verify login with new password works

### Testing with Different Email Providers

#### Gmail
```env
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password  # Use App Password, not regular password
MAIL_ENCRYPTION=tls
```

#### Mailgun
```env
MAIL_MAILER=mailgun
MAILGUN_DOMAIN=your-domain.mailgun.org
MAILGUN_SECRET=your-secret-key
```

## Customization

### Change Token Expiration

Edit `AuthController.php`:

```php
// Change from 60 minutes to 30 minutes
if ($tokenCreatedAt->addMinutes(30)->isPast()) {
    // Token expired
}
```

### Customize Email Template

Edit `resources/views/emails/password-reset.blade.php` to match your branding.

### Add More Password Requirements

Edit `resources/views/reset-password.blade.php` and add more requirements:

```javascript
function checkPasswordRequirements(password) {
    // Check uppercase
    if (password.match(/[A-Z]/)) {
        $('#req-uppercase').addClass('met');
    }
    // Check numbers
    if (password.match(/[0-9]/)) {
        $('#req-numbers').addClass('met');
    }
    // etc.
}
```

Also update validation in `AuthController.php`:

```php
'password' => 'required|string|min:8|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/|confirmed',
```

## Troubleshooting

### Emails Not Sending

1. Check `.env` mail configuration
2. Check `storage/logs/laravel.log` for errors
3. Verify firewall isn't blocking SMTP port
4. Test with `MAIL_MAILER=log` first

### Token Not Found

1. Check database for token entry
2. Verify token hasn't expired
3. Check URL parameters are correct

### "Invalid Token" Error

1. Token might have expired (60 min default)
2. Token might have been used already
3. Check token in URL matches database (case-sensitive)

## Production Checklist

- [ ] Configure production SMTP settings
- [ ] Set `MAIL_FROM_ADDRESS` and `MAIL_FROM_NAME`
- [ ] Test email delivery
- [ ] Add rate limiting to prevent abuse
- [ ] Set up email monitoring/logging
- [ ] Configure SPF/DKIM records for your domain
- [ ] Test password reset flow end-to-end
- [ ] Monitor `password_reset_tokens` table size (cleanup old tokens)

## Future Enhancements

Consider adding:
- Rate limiting on password reset requests
- Two-factor authentication
- Password strength meter improvements
- Email verification before password reset
- Account lockout after failed attempts
- Notification to user when password is changed
- Scheduled cleanup of expired tokens


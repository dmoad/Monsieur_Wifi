# Email Logging Guide for Password Reset

## Overview

Comprehensive logging has been added to the password reset functionality to help you track whether emails are being sent successfully or not.

## Where to Find Logs

Laravel logs are located at:
```
storage/logs/laravel.log
```

## How to View Logs in Real-Time

### Option 1: Using `tail` (Recommended)
```bash
cd /var/www/mrwifi
tail -f storage/logs/laravel.log
```
This will continuously show new log entries as they appear.

### Option 2: Using `grep` to filter password reset logs
```bash
cd /var/www/mrwifi
grep "Password Reset" storage/logs/laravel.log
```

### Option 3: View recent entries
```bash
cd /var/www/mrwifi
tail -100 storage/logs/laravel.log
```

## What to Look For

### 1. Password Reset Request Logs

When a user requests a password reset, you'll see:

```
[timestamp] local.INFO: === Password Reset Request Started ===
[timestamp] local.INFO: Request Email: user@example.com
[timestamp] local.INFO: User found for password reset {"user_id":1,"user_name":"John Doe","user_email":"user@example.com"}
[timestamp] local.INFO: Generated new password reset token {"token_length":64,"token_preview":"abc1234567..."}
[timestamp] local.INFO: Password reset token stored in database for: user@example.com
[timestamp] local.INFO: Generated reset URL {"url_length":150}
[timestamp] local.INFO: Mail Configuration {"mailer":"smtp","host":"smtp.gmail.com","port":587,"from_address":"noreply@mrwifi.com","from_name":"Monsieur WiFi"}
[timestamp] local.INFO: Attempting to send password reset email to: user@example.com
```

### 2. Successful Email Delivery

If the email is sent successfully:
```
[timestamp] local.INFO: ✅ Password reset email sent successfully! {"recipient":"user@example.com","recipient_name":"John Doe","reset_url":"https://..."}
[timestamp] local.INFO: === Password Reset Request Completed ===
```

### 3. Failed Email Delivery

If the email fails to send:
```
[timestamp] local.ERROR: ❌ Failed to send password reset email {"recipient":"user@example.com","error_message":"Connection refused","error_class":"Swift_TransportException","error_file":"/path/to/file.php","error_line":123,"stack_trace":"..."}
[timestamp] local.INFO: === Password Reset Request Completed ===
```

### 4. Non-Existent User

If someone tries to reset password for a non-existent email:
```
[timestamp] local.WARNING: Password reset requested for non-existent email: nonexistent@example.com
```

### 5. Password Reset Submission Logs

When a user submits their new password:

```
[timestamp] local.INFO: === Password Reset Submission Started ===
[timestamp] local.INFO: Reset Request {"email":"user@example.com","has_token":true,"token_length":64,"has_password":true}
[timestamp] local.INFO: Password reset token found in database {"email":"user@example.com","token_created_at":"2025-01-01 12:00:00"}
[timestamp] local.INFO: Token expiration check {"created_at":"2025-01-01 12:00:00","expires_at":"2025-01-01 13:00:00","current_time":"2025-01-01 12:30:00","is_expired":false,"minutes_since_creation":30}
[timestamp] local.INFO: Token verification {"token_matches":true,"provided_token_preview":"abc1234567..."}
[timestamp] local.INFO: User found, updating password {"user_id":1,"user_email":"user@example.com","user_name":"John Doe"}
[timestamp] local.INFO: Password updated in database for user: user@example.com
[timestamp] local.INFO: Used password reset token deleted from database
[timestamp] local.INFO: ✅ Password successfully reset for user: user@example.com
[timestamp] local.INFO: === Password Reset Submission Completed Successfully ===
```

## Testing Email Configuration

### Test 1: Check if Email Driver is Configured

Run this command to see your current mail configuration:
```bash
cd /var/www/mrwifi
php artisan tinker
```

Then in tinker:
```php
config('mail.default')
config('mail.mailers.smtp.host')
config('mail.from.address')
exit
```

### Test 2: Use Log Driver for Testing

To test without actually sending emails, update your `.env`:
```env
MAIL_MAILER=log
```

Then request a password reset. The email content will be written to `storage/logs/laravel.log`.

Look for entries like:
```
[timestamp] local.INFO: Password Reset Request - Monsieur WiFi
```

The full email HTML will be in the log file.

### Test 3: Send a Test Email

You can test email sending directly with:
```bash
cd /var/www/mrwifi
php artisan tinker
```

Then:
```php
Mail::raw('Test email', function($message) {
    $message->to('your-email@example.com')
            ->subject('Test Email from Monsieur WiFi');
});
exit
```

Check the logs to see if it was sent successfully.

## Common Issues and Solutions

### Issue 1: Email Not Sending (Connection Refused)

**Log Entry:**
```
error_message: "Connection refused"
```

**Solution:**
- Check if SMTP host and port are correct in `.env`
- Verify firewall isn't blocking the SMTP port
- Test with `telnet smtp.gmail.com 587`

### Issue 2: Authentication Failed

**Log Entry:**
```
error_message: "Username and Password not accepted"
```

**Solution:**
- For Gmail: Use an App Password, not your regular password
- Verify MAIL_USERNAME and MAIL_PASSWORD in `.env`
- Enable "Less secure app access" (not recommended) or use OAuth2

### Issue 3: Email Sent but Not Received

**What to check:**
1. Look for "✅ Password reset email sent successfully!" in logs
2. Check recipient's spam/junk folder
3. Verify FROM address is valid
4. Check if your domain has proper SPF/DKIM records

### Issue 4: No Logs Appearing

**Solutions:**
```bash
# Check log file permissions
ls -la storage/logs/laravel.log

# If it doesn't exist, create it
touch storage/logs/laravel.log
chmod 664 storage/logs/laravel.log

# Check storage directory permissions
chmod -R 775 storage/
chown -R www-data:www-data storage/
```

## Quick Debugging Commands

### View last 50 lines of logs
```bash
tail -50 storage/logs/laravel.log
```

### Search for all password reset attempts today
```bash
grep "$(date +%Y-%m-%d)" storage/logs/laravel.log | grep "Password Reset"
```

### Count how many password reset emails were sent today
```bash
grep "$(date +%Y-%m-%d)" storage/logs/laravel.log | grep "Password reset email sent successfully" | wc -l
```

### Find failed email attempts
```bash
grep "Failed to send password reset email" storage/logs/laravel.log
```

### View only errors from logs
```bash
grep "ERROR" storage/logs/laravel.log | tail -20
```

### Clear old logs (use with caution)
```bash
# Backup first
cp storage/logs/laravel.log storage/logs/laravel.log.backup

# Clear the log
> storage/logs/laravel.log

# Or delete and let Laravel recreate it
rm storage/logs/laravel.log
```

## Example: Complete Testing Workflow

1. **Start watching logs:**
```bash
tail -f storage/logs/laravel.log
```

2. **In another terminal, request a password reset:**
   - Go to `/password-reset` in your browser
   - Enter an email address
   - Click "Send Reset Link"

3. **Watch the logs in real-time:**
   - You should see all the log entries listed above
   - Look for the ✅ or ❌ indicators

4. **If using MAIL_MAILER=log:**
   - Search for the email content in the log
   - Copy the reset URL
   - Paste it in your browser

5. **Test password reset submission:**
   - Enter new password
   - Watch logs for "Password Reset Submission" entries

## Log Levels Explained

- **INFO** (🔵): Normal operation, everything is working
- **WARNING** (🟡): Something unusual but not critical (e.g., non-existent email)
- **ERROR** (🔴): Something failed (e.g., email couldn't be sent)

## Production Recommendations

1. **Set up log rotation:**
```bash
# In /etc/logrotate.d/laravel
/var/www/mrwifi/storage/logs/*.log {
    daily
    rotate 14
    compress
    delaycompress
    notifempty
    create 0664 www-data www-data
    sharedscripts
}
```

2. **Monitor logs with a service:**
   - Use tools like Papertrail, Loggly, or Sentry
   - Set up alerts for ERROR level logs

3. **Regular cleanup:**
```bash
# Add to cron (daily at 3 AM)
0 3 * * * cd /var/www/mrwifi && php artisan auth:cleanup-password-resets >> /dev/null 2>&1
```

## Need Help?

If emails still aren't sending after checking the logs:

1. Share the relevant log entries (remove sensitive info)
2. Share your mail configuration (remove passwords):
   ```bash
   grep MAIL_ .env | sed 's/PASSWORD=.*/PASSWORD=***/'
   ```
3. Check Laravel's queue if using QUEUE_CONNECTION for emails

## Quick Reference: Important Log Messages

| Log Message | Meaning |
|-------------|---------|
| `Password Reset Request Started` | User initiated password reset |
| `User found for password reset` | Valid user, will send email |
| `Password reset requested for non-existent email` | Invalid email, no action taken |
| `✅ Password reset email sent successfully!` | **Email sent!** |
| `❌ Failed to send password reset email` | **Email failed!** Check error details |
| `Mail Configuration` | Shows your mail settings |
| `Token expiration check` | Shows if token is still valid |
| `Password updated in database` | Password successfully changed |


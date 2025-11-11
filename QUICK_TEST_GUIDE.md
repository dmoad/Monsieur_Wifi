# Quick Email Testing Guide

## 🚀 Quick Start - Test Email in 30 Seconds

### Option 1: Interactive Test Script (Easiest)
```bash
cd /var/www/mrwifi
./test-email.sh
```

### Option 2: Manual Quick Test
```bash
# 1. Watch logs in one terminal
cd /var/www/mrwifi
tail -f storage/logs/laravel.log

# 2. In browser, go to: http://your-domain/password-reset
# 3. Enter a valid email address and click "Send Reset Link"
# 4. Watch the logs - look for ✅ or ❌
```

## 📧 What You'll See in Logs

### ✅ Success (Email Sent)
```
[timestamp] local.INFO: === Password Reset Request Started ===
[timestamp] local.INFO: Request Email: user@example.com
[timestamp] local.INFO: User found for password reset
[timestamp] local.INFO: Mail Configuration {"mailer":"smtp",...}
[timestamp] local.INFO: Attempting to send password reset email to: user@example.com
[timestamp] local.INFO: ✅ Password reset email sent successfully!
[timestamp] local.INFO: === Password Reset Request Completed ===
```

### ❌ Failure (Email Not Sent)
```
[timestamp] local.INFO: === Password Reset Request Started ===
[timestamp] local.INFO: Request Email: user@example.com
[timestamp] local.INFO: User found for password reset
[timestamp] local.INFO: Attempting to send password reset email to: user@example.com
[timestamp] local.ERROR: ❌ Failed to send password reset email
                         {"error_message":"Connection refused",...}
[timestamp] local.INFO: === Password Reset Request Completed ===
```

## 🔍 Quick Commands

### View Latest Logs
```bash
cd /var/www/mrwifi
tail -50 storage/logs/laravel.log
```

### Watch Logs Live
```bash
cd /var/www/mrwifi
tail -f storage/logs/laravel.log
```

### Search Password Reset Logs Only
```bash
cd /var/www/mrwifi
grep "Password Reset" storage/logs/laravel.log | tail -20
```

### Find Email Send Success
```bash
cd /var/www/mrwifi
grep "Password reset email sent successfully" storage/logs/laravel.log
```

### Find Email Send Failures
```bash
cd /var/www/mrwifi
grep "Failed to send password reset email" storage/logs/laravel.log
```

## 🛠️ Common Issues & Quick Fixes

### Issue: "Connection refused"
**Quick Fix:**
```bash
# Check your .env file
grep MAIL_ .env

# Make sure these are set correctly:
# MAIL_MAILER=smtp
# MAIL_HOST=smtp.gmail.com
# MAIL_PORT=587
# MAIL_USERNAME=your-email@gmail.com
# MAIL_PASSWORD=your-app-password
```

### Issue: Not sure if emails are working?
**Quick Fix - Use Log Driver:**
```bash
# Edit .env and change:
MAIL_MAILER=log

# Now "send" an email and check:
tail -100 storage/logs/laravel.log

# You'll see the full email content in the log!
```

### Issue: Can't see any logs
**Quick Fix:**
```bash
cd /var/www/mrwifi

# Create log file if it doesn't exist
touch storage/logs/laravel.log

# Fix permissions
chmod 664 storage/logs/laravel.log
chown www-data:www-data storage/logs/laravel.log

# Fix storage directory
chmod -R 775 storage/
```

## 📱 Test from Command Line (API)

### Send Test Password Reset
```bash
# Replace with your APP_URL from .env
APP_URL="https://portal.monsieur-wifi.com"

curl -X POST "$APP_URL/api/auth/password-reset" \
  -H "Content-Type: application/json" \
  -d '{"email":"test@example.com"}'
```

Or use your actual domain:
```bash
curl -X POST https://portal.monsieur-wifi.com/api/auth/password-reset \
  -H "Content-Type: application/json" \
  -d '{"email":"test@example.com"}'
```

### Watch Response
```json
{
  "message": "If an account exists with this email, a password reset link has been sent."
}
```

Then immediately check logs:
```bash
tail -30 storage/logs/laravel.log
```

## 🎯 Log Markers to Look For

| Marker | Meaning |
|--------|---------|
| `===` | Start/End of a process |
| `✅` | Success! |
| `❌` | Failed! |
| `⚠️` | Warning (check it) |
| `Mail Configuration` | Shows your SMTP settings |
| `Attempting to send` | About to send email |
| `sent successfully` | Email sent! |

## 💡 Pro Tips

1. **Keep two terminals open:**
   - Terminal 1: `tail -f storage/logs/laravel.log`
   - Terminal 2: Run your tests

2. **Search by date:**
   ```bash
   grep "2025-01-15" storage/logs/laravel.log | grep "Password Reset"
   ```

3. **Count successful sends today:**
   ```bash
   grep "$(date +%Y-%m-%d)" storage/logs/laravel.log | \
   grep "sent successfully" | wc -l
   ```

4. **Export logs for analysis:**
   ```bash
   grep "Password Reset" storage/logs/laravel.log > password-reset-logs.txt
   ```

## 🔐 Verify Mail Configuration

```bash
cd /var/www/mrwifi
php artisan tinker

# Run these commands:
config('mail.default')
config('mail.mailers.smtp.host')
config('mail.from.address')

# Type 'exit' to quit
```

## 📋 Complete Test Checklist

- [ ] `.env` file has correct MAIL_* settings
- [ ] Log file exists and is writable
- [ ] Start watching logs: `tail -f storage/logs/laravel.log`
- [ ] Go to `/password-reset` page
- [ ] Enter valid email address
- [ ] Click "Send Reset Link"
- [ ] See "✅ Password reset email sent successfully!" in logs
- [ ] Check email inbox (or spam folder)
- [ ] Click reset link in email
- [ ] Enter new password
- [ ] See "✅ Password successfully reset" in logs
- [ ] Login with new password works

## 🆘 Still Not Working?

1. **Check the logs** - They will tell you exactly what's wrong
2. **Use log driver** to test without SMTP: `MAIL_MAILER=log`
3. **Verify user exists** in the database with that email
4. **Check spam folder** if using real SMTP
5. **Review** `EMAIL_LOGGING_GUIDE.md` for detailed troubleshooting

## 📞 Getting Help

When asking for help, provide:
```bash
# 1. Your mail configuration (hide passwords!)
grep MAIL_ .env | sed 's/PASSWORD=.*/PASSWORD=***/'

# 2. Recent password reset logs
grep "Password Reset" storage/logs/laravel.log | tail -10

# 3. Any error messages
grep "ERROR" storage/logs/laravel.log | tail -5
```

---

**Remember:** Logs are your friend! 🤝 Every step is logged, so you'll always know exactly what's happening.


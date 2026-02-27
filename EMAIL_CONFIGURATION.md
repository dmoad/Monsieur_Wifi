# Email System Configuration

## Overview
All emails in the MrWiFi system are now sent **synchronously** (immediately) without using queues.

## Email Delivery Method
- **SMTP Provider**: Brevo (formerly Sendinblue)
- **Delivery Mode**: Synchronous (no queue)
- **Configuration**: Direct SMTP connection

## SMTP Settings
```
Host: smtp-relay.brevo.com
Port: 587
Encryption: TLS
From: lmermet@citypassenger.com
From Name: Monsieur WiFi
```

## Email Types

### Authentication Emails (Synchronous)
1. **Email Verification** (`VerifyEmailMail`)
   - Sent when: User registers
   - Expires: 60 minutes
   - Template: `emails.verify-email`
   - Subject: "Verify Your Email Address - Monsieur WiFi"

2. **Password Reset** (`PasswordResetMail`)
   - Sent when: User requests password reset
   - Expires: 60 minutes
   - Template: `emails.password-reset`
   - Subject: "Password Reset Request - Monsieur WiFi"

### E-commerce Emails (Synchronous)
3. **Order Confirmation** (`OrderProcessedMail`)
   - Sent when: Order is placed
   - Templates: `emails.order-processed-{en|fr}`
   - Subject: "Order Confirmation - Monsieur WiFi"

4. **Shipping Notification** (`ShippingTrackingMail`)
   - Sent when: Order is shipped with tracking
   - Templates: `emails.shipping-tracking-{en|fr}`
   - Subject: "Your Order Has Been Shipped - Monsieur WiFi"

5. **Delivery Confirmation** (`OrderDeliveredMail`)
   - Sent when: Order is delivered
   - Templates: `emails.order-delivered-{en|fr}`
   - Subject: "Your Order Has Been Delivered - Monsieur WiFi"

6. **Payment Failed** (`PaymentFailedMail`)
   - Sent when: Payment processing fails
   - Templates: `emails.payment-failed-{en|fr}`
   - Subject: "Payment Failed - Monsieur WiFi"

7. **Cart Abandonment** (`CartAbandonmentMail`)
   - Sent when: Cart reminder triggered
   - Templates: `emails.cart-abandonment-{en|fr}`
   - Subject: "You Left Something Behind - Monsieur WiFi"

## Benefits of Synchronous Email Sending

### Advantages
✅ **Simplicity**: No need to manage queue workers or supervisor processes
✅ **Immediate Feedback**: Know instantly if email sending fails
✅ **No Setup**: Works out of the box without additional configuration
✅ **Debugging**: Easier to debug email issues in real-time
✅ **Reliability**: No risk of emails stuck in queue

### Considerations
⚠️ **Response Time**: API responses wait for email to send (~1-2 seconds)
⚠️ **Error Handling**: SMTP errors immediately affect API responses
⚠️ **Scalability**: For high-volume scenarios, consider switching to queues

## Email Testing

### Test Configuration
```bash
# Send test email
php artisan tinker
Mail::raw('Test email', function($msg) {
    $msg->to('your@email.com')->subject('Test');
});
```

### Check Logs
```bash
# View Laravel logs
tail -f storage/logs/laravel.log

# View queue jobs (if any)
php artisan queue:failed
```

## Troubleshooting

### Email Not Sending
1. Check SMTP credentials in `.env`
2. Verify Brevo account is active
3. Check Laravel logs: `storage/logs/laravel.log`
4. Test SMTP connection:
   ```bash
   php artisan tinker
   config('mail.mailers.smtp')
   ```

### Emails Going to Spam
1. Verify SPF/DKIM records in DNS
2. Check Brevo sender reputation
3. Ensure proper email headers
4. Use consistent From address

### Slow Email Sending
1. Check Brevo API limits
2. Verify network latency to SMTP server
3. Consider switching to queue-based sending for high volume

## Migration to Queue (If Needed)

If you need to switch back to queued emails in the future:

```php
// In each Mail class (e.g., OrderProcessedMail.php)
use Illuminate\Contracts\Queue\ShouldQueue;

class OrderProcessedMail extends Mailable implements ShouldQueue
{
    // ... rest of code
}
```

Then start queue worker:
```bash
php artisan queue:work --daemon
```

## Last Updated
Date: 2026-02-27
Configuration: All emails sending synchronously via Brevo SMTP

# Testing Order Email System

## Quick Test Commands

### Test with Existing Order
```bash
cd /var/www/mrwifi

# Test order confirmation email
php artisan email:test-order ORD-202602-00005
```

### Check Logs
```bash
# Watch logs in real-time
tail -f storage/logs/laravel.log

# Check for errors
grep "ERROR" storage/logs/laravel.log | tail -20
```

### Test Full Payment Flow

#### 1. Frontend Test (Real Stripe Payment)
1. Go to: https://mrwifi-temp.halowifi.com/en/shop
2. Add product to cart
3. Proceed to checkout
4. Use Stripe test card: `4242 4242 4242 4242`
5. Complete payment
6. Check email inbox
7. Verify email contains:
   - Customer name
   - Order items with pricing
   - Shipping address
   - Order total

#### 2. API Test (cURL)
```bash
# Get auth token first
TOKEN="your_jwt_token_here"

# Create order
curl -X POST https://mrwifi-temp.halowifi.com/api/v1/orders \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "shipping_address_id": 1,
    "billing_address_id": 1,
    "payment_method": "stripe"
  }'

# Complete payment
curl -X POST https://mrwifi-temp.halowifi.com/api/v1/orders/ORD-xxx/verify-payment \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json"
```

#### 3. Admin Panel Test
1. Login as admin
2. Go to Orders section
3. Select an unpaid order
4. Click "Confirm Payment"
5. Verify email is sent

### Test Other Email Types

#### Shipping Notification
```bash
# Via admin API
curl -X PUT https://mrwifi-temp.halowifi.com/api/v1/admin/orders/ORD-xxx/tracking \
  -H "Authorization: Bearer $ADMIN_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "shipping_provider": "FedEx",
    "tracking_id": "123456789"
  }'
```

#### Delivery Confirmation
```bash
# Via admin API
curl -X PUT https://mrwifi-temp.halowifi.com/api/v1/admin/orders/ORD-xxx/status \
  -H "Authorization: Bearer $ADMIN_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "status": "delivered"
  }'
```

## Expected Results

### ✅ Success Indicators
- Email arrives within 1-3 seconds
- No errors in Laravel logs
- Email contains all expected data:
  - Customer name (not null)
  - Order items with images
  - Shipping address (not null)
  - Correct pricing and totals
- API returns success response

### ❌ Failure Indicators
- Email not received after 30 seconds
- Error in logs: "Attempt to read property on null"
- API returns 500 error
- Email missing data (blank fields)

## Troubleshooting

### Email Not Sending
```bash
# Check Brevo SMTP credentials
grep MAIL_ /var/www/mrwifi/.env

# Test SMTP connection
php artisan tinker
>>> Mail::raw('Test', fn($m) => $m->to('your@email.com')->subject('Test'));
>>> exit
```

### Null Property Errors
```bash
# Check if relationships are loaded
php artisan tinker
>>> $order = Order::with(['user', 'items.productModel', 'shippingAddress', 'billingAddress'])->where('order_number', 'ORD-xxx')->first();
>>> $order->user; // Should not be null
>>> $order->shippingAddress; // Should not be null
>>> exit
```

### Cache Issues
```bash
# Clear ALL caches
php artisan optimize:clear

# Restart services
sudo systemctl restart apache2
sudo systemctl restart php8.3-fpm
```

## Monitoring

### Watch for Success
```bash
# Terminal 1: Watch logs
tail -f storage/logs/laravel.log | grep "Email sent\|Payment"

# Terminal 2: Watch Apache logs
sudo tail -f /var/log/apache2/error.log
```

### Metrics to Track
- Email send time (should be 1-3 seconds)
- Success rate (aim for 100%)
- Error rate (aim for 0%)
- API response time

## Rollback Procedure

If emails still fail after all fixes:

```bash
# 1. Revert to queued emails (if needed)
# Edit all Mail classes:
sed -i 's/class OrderProcessedMail extends Mailable$/class OrderProcessedMail extends Mailable implements ShouldQueue/' app/Mail/OrderProcessedMail.php
sed -i 's/class ShippingTrackingMail extends Mailable$/class ShippingTrackingMail extends Mailable implements ShouldQueue/' app/Mail/ShippingTrackingMail.php
sed -i 's/class OrderDeliveredMail extends Mailable$/class OrderDeliveredMail extends Mailable implements ShouldQueue/' app/Mail/OrderDeliveredMail.php
sed -i 's/class PaymentFailedMail extends Mailable$/class PaymentFailedMail extends Mailable implements ShouldQueue/' app/Mail/PaymentFailedMail.php
sed -i 's/class CartAbandonmentMail extends Mailable$/class CartAbandonmentMail extends Mailable implements ShouldQueue/' app/Mail/CartAbandonmentMail.php

# 2. Clear cache
php artisan optimize:clear

# 3. Start queue worker
php artisan queue:work --daemon &
```

## Production Checklist

Before deploying to production:

- [ ] All tests pass
- [ ] Emails sending successfully
- [ ] No errors in logs
- [ ] Cache cleared
- [ ] PHP-FPM restarted
- [ ] Tested with real Stripe payment
- [ ] Tested admin confirmations
- [ ] Tested webhook handling
- [ ] Email content looks correct
- [ ] All relationships load properly

## Contact

If issues persist after all fixes:
1. Check `/var/www/mrwifi/EMAIL_FIX_V2_COMPLETE.md` for detailed fix information
2. Review all controller changes in git diff
3. Verify OPcache has been cleared
4. Consider temporary rollback to queued emails

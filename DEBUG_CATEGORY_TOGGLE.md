# Debug: Category Toggle Not Saving

## Issue
Social Media category toggle doesn't save when enabled.

## Files Updated
1. `/public/assets/js/domain-blocking.js` - Added extensive logging
2. `/resources/views/layouts/app.blade.php` - Added CSRF token meta tag
3. `test-category-toggle.html` - Created test page

## Debugging Steps

### Step 1: Open Domain Blocking Page
Visit either:
- English: https://yoursite.com/en/domain-blocking
- French: https://yoursite.com/fr/domain-blocking

### Step 2: Open Browser Console (F12)
Check for these log messages on page load:

```
Loading categories with CSRF: present
Loading categories with Auth: present
Categories API Response: {success: true, categories: [...]}
Raw categories data: {...}
Category array: [6 categories]
Processing category: Adult Content, enabled: true, count: 1022
Processing category: Gambling, enabled: true, count: 856
Processing category: Malware, enabled: true, count: 2345
Processing category: Social Media, enabled: true, count: 342  <-- CHECK THIS
Processing category: Streaming, enabled: false, count: 0
Processing category: Custom List, enabled: true, count: 43
```

**Expected**: Social Media should show `enabled: true` (based on your API response)

### Step 3: Check Current Checkbox State
Run in console:

```javascript
// Check if Social Media checkbox is checked
const socialCheckbox = $('#category-social');
console.log('Social Media checkbox checked:', socialCheckbox.prop('checked'));
console.log('Social Media card has border:', socialCheckbox.closest('.card').hasClass('border-primary'));
```

**Expected**: Both should be `true` after page loads

### Step 4: Toggle Social Media
Click the Social Media toggle and watch console for:

```
Toggle clicked: Social Media, new state: false  <-- or true
Sending toggle request for category ID 4 to: /api/categories/4/toggle
Toggle API response: {success: true, message: "...", category: {...}}
Social Media (ID: 4) successfully toggled to: false  <-- or true
```

Then after 500ms delay:
```
Loading categories with CSRF: present
Categories API Response: {...}
Processing category: Social Media, enabled: false, count: 342  <-- Should match toggle
```

### Step 5: Check Network Tab
1. Open Network tab (F12 → Network)
2. Toggle Social Media
3. Look for request to `/api/categories/4/toggle`
4. Check:
   - **Status**: Should be `200 OK` (not 419, 401, 403, or 500)
   - **Request Headers**: 
     ```
     Authorization: Bearer eyJ0eXAiOiJKV1...
     X-CSRF-TOKEN: <token>
     Content-Type: application/json
     ```
   - **Response**:
     ```json
     {
       "success": true,
       "message": "Category 'Social Media' enabled and X device(s) configuration updated",
       "category": {
         "id": 4,
         "name": "Social Media",
         "is_enabled": true,  <-- Should be the new state
         ...
       },
       "devices_updated": X
     }
     ```

### Step 6: Check Laravel Logs
```bash
tail -f /var/www/mrwifi/storage/logs/laravel.log
```

Look for these log entries when you toggle:
```
[timestamp] local.INFO: CategoryController::toggle called
[timestamp] local.INFO: Category: {"id":4,"name":"Social Media",...}
[timestamp] local.INFO: Searching for category: {"category_id":4,...}
[timestamp] local.INFO: Found location settings: {"count":X}
[timestamp] local.INFO: Toggle completed: {"category_id":4,"new_status":true/false,...}
```

## Common Issues & Fixes

### Issue 1: CSRF Token Missing (419 Error)
**Symptom**: Network tab shows `419 Unauthorized`
**Check**: `$('meta[name="csrf-token"]').attr('content')` in console
**Fix**: ✅ Already added to `layouts/app.blade.php`

### Issue 2: Authentication Failed (401 Error)
**Symptom**: Network tab shows `401 Unauthorized`
**Check**: `localStorage.getItem('token')` in console
**Fix**: Re-login if token expired

### Issue 3: Route Not Found (404 Error)
**Symptom**: Network tab shows `404 Not Found`
**Check**: Verify route exists:
```bash
php artisan route:list | grep "categories.*toggle"
```
**Expected Output**:
```
POST   api/categories/{category}/toggle   categories.toggle › CategoryController@toggle
```

### Issue 4: Database Not Saving
**Symptom**: API returns success but next GET shows old state
**Check**: Run in database:
```sql
SELECT id, name, is_enabled FROM categories WHERE id = 4;
```
**Fix**: Check database permissions and constraints

### Issue 5: Frontend Not Updating
**Symptom**: Toggle works in API but UI doesn't reflect change
**Check**: Console logs show category data
**Fix**: ✅ Added `loadCategoriesData()` call after successful toggle

## Quick Test Script

Paste this into browser console on the domain-blocking page:

```javascript
// Test the full flow
async function testToggle() {
    console.clear();
    console.log('=== CATEGORY TOGGLE TEST ===\n');
    
    // 1. Get current state
    console.log('1. Getting current state...');
    const beforeResponse = await $.ajax({
        url: '/api/categories',
        type: 'GET',
        headers: {
            'Accept': 'application/json',
            'Authorization': 'Bearer ' + UserManager.getToken()
        }
    });
    
    const socialBefore = beforeResponse.categories.find(c => c.id === 4);
    console.log('Social Media BEFORE:', {
        is_enabled: socialBefore.is_enabled,
        count: socialBefore.blocked_domains_count
    });
    
    // 2. Toggle
    console.log('\n2. Toggling category...');
    const toggleResponse = await $.ajax({
        url: '/api/categories/4/toggle',
        type: 'POST',
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'Authorization': 'Bearer ' + UserManager.getToken(),
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    
    console.log('Toggle Response:', toggleResponse);
    console.log('New state from response:', toggleResponse.category.is_enabled);
    
    // 3. Verify
    console.log('\n3. Verifying save...');
    await new Promise(resolve => setTimeout(resolve, 1000)); // Wait 1 second
    
    const afterResponse = await $.ajax({
        url: '/api/categories',
        type: 'GET',
        headers: {
            'Accept': 'application/json',
            'Authorization': 'Bearer ' + UserManager.getToken()
        }
    });
    
    const socialAfter = afterResponse.categories.find(c => c.id === 4);
    console.log('Social Media AFTER:', {
        is_enabled: socialAfter.is_enabled,
        count: socialAfter.blocked_domains_count
    });
    
    // 4. Compare
    console.log('\n=== RESULT ===');
    if (socialBefore.is_enabled === socialAfter.is_enabled) {
        console.error('❌ TOGGLE FAILED - State unchanged!');
        console.error('Before:', socialBefore.is_enabled, 'After:', socialAfter.is_enabled);
    } else {
        console.log('✅ TOGGLE WORKED - State changed!');
        console.log('Before:', socialBefore.is_enabled, 'After:', socialAfter.is_enabled);
    }
}

// Run the test
testToggle();
```

## What to Report

After running the test, report:
1. All console log output
2. Network tab screenshot showing the toggle request
3. Any errors in console or network tab
4. The final result (✅ or ❌)

This will help identify exactly where the issue is occurring.

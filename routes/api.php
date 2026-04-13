<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SystemSettingController;
use App\Http\Controllers\GuestNetworkUserController;
use App\Http\Controllers\CaptivePortalDesignController;
use App\Http\Controllers\FirmwareController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CaptivePortalWorkingHourController;
use App\Http\Controllers\CaptivePortalHourlyScheduleController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DomainBlockingController;
use App\Http\Controllers\TempCaptivePortalDesignController;
use App\Http\Controllers\ZoneController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\LocationNetworkController;
use App\Http\Controllers\QosController;

// Public routes (no auth required)
Route::post('auth/login', [AuthController::class, 'login']);
Route::post('auth/register', [AuthController::class, 'register']);
Route::post('auth/register-with-design', [AuthController::class, 'register_with_design']);
Route::post('auth/password-reset', [AuthController::class, 'sendPasswordResetLink']);
Route::post('auth/reset-password', [AuthController::class, 'resetPassword']);
Route::post('auth/verify-email', [AuthController::class, 'verifyEmail']);
Route::post('auth/resend-verification', [AuthController::class, 'resendVerificationEmail']);
Route::post('temp-captive-portal-designs', [TempCaptivePortalDesignController::class, 'store']);
Route::get('temp-captive-portal-designs/{id}', [TempCaptivePortalDesignController::class, 'index']);

// Stripe webhook endpoints (public, no CSRF protection)
Route::post('payment-notifications', [PaymentController::class, 'handleWebhook']);
Route::post('stripe/webhook', [SubscriptionController::class, 'handleWebhook']);

// Webhook test endpoint (for debugging)
Route::get('webhook-test', function() {
    return response()->json([
        'success' => true,
        'message' => 'Webhook endpoint is reachable',
        'webhook_url' => config('app.url') . '/api/payment-notifications',
        'webhook_secret_configured' => !empty(config('services.stripe.webhook.secret')),
        'stripe_key_configured' => !empty(config('services.stripe.key')),
        'stripe_secret_configured' => !empty(config('services.stripe.secret')),
    ]);
});

// Protected routes (auth required)
Route::group(['middleware' => 'auth:api', 'prefix' => 'auth'], function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::get('me', [AuthController::class, 'me']);
    Route::put('me', [AuthController::class, 'update']);
    Route::post('upload-profile-picture', [AuthController::class, 'uploadProfilePicture']);
});

Route::group(['middleware' => 'auth:api', 'prefix' => 'accounts'], function () {
    Route::get('users', [AuthController::class, 'getUsers']);
    Route::put('users/{user}', [AuthController::class, 'updateUser']);
    Route::delete('users/{user}', [AuthController::class, 'deleteUser']);
    Route::post('users', [AuthController::class, 'createUser']);
});

Route::get('/test', function () {
    return response()->json(['message' => 'Hello, World!']);
});

Route::group(['prefix' => 'devices'], function () {
    Route::get('/', [DeviceController::class, 'index']);
    Route::post('/', [DeviceController::class, 'store']);
    Route::get('/{device}', [DeviceController::class, 'show']);
    Route::put('/{device}', [DeviceController::class, 'update']);
    Route::delete('/{device}', [DeviceController::class, 'destroy']);
    Route::post('/{device}/reboot', [DeviceController::class, 'reboot'])->middleware('auth:api');
});

Route::get('/devices/{device_key}/{device_secret}/settings', [DeviceController::class, 'getSettings']);
Route::get('/devices/{device_key}/{device_secret}/v2-settings', [DeviceController::class, 'getSettingsV2']);
Route::get('/devices/{device_key}/{device_secret}/heartbeat', [DeviceController::class, 'heartbeat']);
Route::get('/devices/{device_key}/{device_secret}/firmware', [FirmwareController::class, 'getDeviceFirmware']);
Route::post('/devices/{device_key}/{device_secret}/clients', [DeviceController::class, 'updateClientList']);

// Device scan update routes (called by devices themselves)
Route::post('/devices/{device_key}/{device_secret}/scan/{scan_id}/started', [DeviceController::class, 'updateScanStarted']);
Route::post('/devices/{device_key}/{device_secret}/scan/{scan_id}/2g-results', [DeviceController::class, 'update2GScanResults']);
Route::post('/devices/{device_key}/{device_secret}/scan/{scan_id}/5g-results', [DeviceController::class, 'update5GScanResults']);
Route::post('/devices/{device_key}/{device_secret}/scan/{scan_id}/failed', [DeviceController::class, 'markScanFailed']);

// Route::get('/devices/{mac_address}/{verification_code}/info', [DeviceController::class, 'info']);

Route::get('/devices/{mac_address}/{verification_code}/verify', [DeviceController::class, 'verify']);   

Route::group(['middleware' => 'auth:api', 'prefix' => 'locations'], function () {
    Route::get('/', [LocationController::class, 'index']);
    Route::post('/', [LocationController::class, 'store']);
    Route::get('/{id}', [LocationController::class, 'show']);
    Route::put('/{id}', [LocationController::class, 'update']);
    Route::delete('/{id}', [LocationController::class, 'destroy']);
    Route::post('/{id}/clone', [LocationController::class, 'clone']);
    Route::put('/{location_id}/general', [LocationController::class, 'updateGeneral']);
    // Location settings routes
    Route::get('/{id}/settings', [LocationController::class, 'getSettings']);
    Route::put('/{id}/settings', [LocationController::class, 'updateSettings']);
    // Geocoding test route
    Route::post('/test-geocode', [LocationController::class, 'testGeocode']);
    // Channel scan routes
    Route::post('/{id}/scan/initiate', [DeviceController::class, 'initiateScan']);
    Route::get('/{id}/scan/{scan_id}/status', [DeviceController::class, 'getScanStatus']);
    Route::get('/{id}/scan-results/latest', [DeviceController::class, 'getLatestScanResults']);
    Route::get('/{id}/scans', [DeviceController::class, 'getScanHistory']);
    Route::get('/{id}/channel-scan', [LocationController::class, 'channelScan']);
    // Firmware update route
    Route::post('/{id}/update-firmware', [LocationController::class, 'updateFirmware']);
    // MAC address update route
    Route::post('/{id}/update-mac-address', [LocationController::class, 'updateMacAddress']);
    // MAC address sync route
    Route::post('/{id}/sync-mac-addresses', [LocationController::class, 'syncMacAddressesToRadcheck']);
    // Accounting routes
    Route::get('/{id}/accounting', [LocationController::class, 'getAccounting']);
    Route::get('/{id}/user-sessions', [LocationController::class, 'getUserSessions']);
    // Online users route
    Route::get('/{id}/online-users', [LocationController::class, 'getOnlineUsers']);
    // Captive portal daily usage route
    Route::get('/{id}/captive-portal/daily-usage', [LocationController::class, 'getCaptivePortalDailyUsage']);
    // Captive portal working hours route
    Route::get('/{id}/captive-portal/working-hours', [CaptivePortalWorkingHourController::class, 'getWorkingHours']);
    Route::post('/{id}/captive-portal/working-hours', [CaptivePortalWorkingHourController::class, 'updateWorkingHours']);
    // Captive portal hourly schedule routes
    Route::get('/{id}/captive-portal/hourly-schedule', [CaptivePortalHourlyScheduleController::class, 'getHourlySchedule']);
    Route::post('/{id}/captive-portal/hourly-schedule', [CaptivePortalHourlyScheduleController::class, 'updateHourlySchedule']);
    Route::post('/{id}/captive-portal/hourly-schedule/{dayOfWeek}', [CaptivePortalHourlyScheduleController::class, 'updateDaySchedule']);
    Route::post('/{id}/captive-portal/hourly-schedule/{dayOfWeek}/bulk', [CaptivePortalHourlyScheduleController::class, 'bulkUpdateDay']);
    Route::post('/{id}/captive-portal/hourly-schedule/{dayOfWeek}/{hour}/toggle', [CaptivePortalHourlyScheduleController::class, 'toggleHour']);
    Route::post('/{id}/captive-portal/hourly-schedule/initialize', [CaptivePortalHourlyScheduleController::class, 'initializeFromWorkingHours']);
    Route::get('/{id}/captive-portal/hourly-schedule/status', [CaptivePortalHourlyScheduleController::class, 'checkCurrentStatus']);

    // Location QoS toggle
    Route::put('/{id}/settings/qos', [LocationController::class, 'updateQosSettings']);

    // Location networks (flexible multi-network support)
    Route::get('/{location_id}/networks', [LocationNetworkController::class, 'index']);
    Route::post('/{location_id}/networks', [LocationNetworkController::class, 'store']);
    Route::put('/{location_id}/networks/reorder', [LocationNetworkController::class, 'reorder']);
    Route::get('/{location_id}/networks/{network_id}', [LocationNetworkController::class, 'show']);
    Route::put('/{location_id}/networks/{network_id}', [LocationNetworkController::class, 'update']);
    Route::delete('/{location_id}/networks/{network_id}', [LocationNetworkController::class, 'destroy']);
});

Route::group(['middleware' => 'auth:api', 'prefix' => 'system-settings'], function () {
    Route::get('/', [SystemSettingController::class, 'index']);
    Route::post('/', [SystemSettingController::class, 'update']);
    Route::post('/test-email', [SystemSettingController::class, 'testEmail']);
});

Route::group(['middleware' => 'auth:api', 'prefix' => 'captive-portal-designs'], function () {
    Route::post('/', [CaptivePortalDesignController::class, 'get_all']);
    Route::get('/', [CaptivePortalDesignController::class, 'get_all']);
    Route::get('/{captivePortalDesign}', [CaptivePortalDesignController::class, 'show']);
    Route::post('/create', [CaptivePortalDesignController::class, 'create']);
    Route::put('/{captivePortalDesign}', [CaptivePortalDesignController::class, 'update']);
    Route::delete('/{captivePortalDesign}', [CaptivePortalDesignController::class, 'destroy']);
    Route::post('/{captivePortalDesign}/duplicate', [CaptivePortalDesignController::class, 'duplicate']);
    Route::post('/{captivePortalDesign}/change-owner', [CaptivePortalDesignController::class, 'changeOwner']);
});

// Firmware routes (protected with auth)
Route::group(['middleware' => 'auth:api', 'prefix' => 'firmware'], function () {
    Route::get('/', [FirmwareController::class, 'index']);
    Route::post('/', [FirmwareController::class, 'store']);
    Route::get('/enabled', [FirmwareController::class, 'enabled']);
    Route::get('/models', [FirmwareController::class, 'models']);
    Route::get('/defaults', [FirmwareController::class, 'getDefaults']);
    Route::get('/model/{model}', [FirmwareController::class, 'byModel']);
    Route::get('/{firmware}', [FirmwareController::class, 'show']);
    Route::put('/{firmware}', [FirmwareController::class, 'update']);
    Route::delete('/{firmware}', [FirmwareController::class, 'destroy']);
    Route::get('/{firmware}/download', [FirmwareController::class, 'download']);
    Route::post('/{firmware}/toggle-status', [FirmwareController::class, 'toggleStatus']);
    Route::post('/{firmware}/set-default', [FirmwareController::class, 'setDefault']);
    Route::post('/{firmware}/verify', [FirmwareController::class, 'verify']);
});

// QoS class routes (protected with auth)
// GET index + show: all authenticated users (admin/user get read-only view of classes + domains)
// POST/DELETE domains: SuperAdmin only (enforced inside QosController)
Route::group(['middleware' => 'auth:api', 'prefix' => 'qos'], function () {
    Route::get('/classes', [QosController::class, 'index']);
    Route::get('/classes/{classId}', [QosController::class, 'show']);
    Route::post('/classes/{classId}/domains', [QosController::class, 'addDomain']);
    Route::delete('/classes/{classId}/domains/{domain}', [QosController::class, 'removeDomain']);
});

// Category routes (protected with auth)
Route::group(['middleware' => 'auth:api', 'prefix' => 'categories'], function () {
    Route::get('/', [CategoryController::class, 'index']);
    Route::get('/enabled', [CategoryController::class, 'enabled']);
    Route::post('/{category}/toggle', [CategoryController::class, 'toggle'])->name('categories.toggle');
    Route::post('/reorder', [CategoryController::class, 'reorder'])->name('categories.reorder');
    Route::get('/{category}/stats', [CategoryController::class, 'stats'])->name('categories.stats');
});

// Domain Blocking API routes (protected with auth)
Route::group(['middleware' => 'auth:api', 'prefix' => 'blocked-domains'], function () {
    Route::get('/', [DomainBlockingController::class, 'index']);
    Route::post('/', [DomainBlockingController::class, 'store']);
    Route::get('/export', [DomainBlockingController::class, 'export']);
    Route::get('/{blockedDomain}', [DomainBlockingController::class, 'show']);
    Route::put('/{blockedDomain}', [DomainBlockingController::class, 'update']);
    Route::delete('/{blockedDomain}', [DomainBlockingController::class, 'destroy']);
});

// Additional Domain Blocking API routes
Route::prefix('domain-blocking')->group(function () {
    Route::post('/bulk-delete', [DomainBlockingController::class, 'bulkDelete'])->name('domain-blocking.bulk-delete');
    Route::post('/import', [DomainBlockingController::class, 'import'])->name('domain-blocking.import');
    Route::post('/categories/{category}/toggle', [DomainBlockingController::class, 'toggleCategory'])->name('domain-blocking.toggle-category');
    Route::get('/stats', [DomainBlockingController::class, 'stats'])->name('domain-blocking.stats');
    Route::post('/check-domain', [DomainBlockingController::class, 'checkDomain'])->name('domain-blocking.check-domain');
});

// Dashboard routes (protected with auth)
Route::group(['middleware' => 'auth:api', 'prefix' => 'dashboard'], function () {
    Route::get('/overview', [DashboardController::class, 'getOverview']);
    Route::get('/analytics', [DashboardController::class, 'getAnalytics']);
    Route::get('/data-usage-trends', [DashboardController::class, 'getDataUsageTrends']);
});

Route::post('/captive-portal/{network_id}/info', [GuestNetworkUserController::class, 'info']);
Route::get('/captive-portal/{network_id}/info', [GuestNetworkUserController::class, 'info']);
Route::post('/captive-portal/login', [GuestNetworkUserController::class, 'login']);
Route::post('/captive-portal/twitter-login', [GuestNetworkUserController::class, 'twitterLogin']);

// Guest Network User routes (protected with auth)
Route::group(['middleware' => 'auth:api', 'prefix' => 'locations'], function () {
    Route::get('/{location}/guest-users', [GuestNetworkUserController::class, 'index']);
    Route::get('/{location}/guest-users/export', [GuestNetworkUserController::class, 'export']);
});

Route::resource('/guest-users', GuestNetworkUserController::class);

// Guest Network User routes
Route::post('/network/{network_id}/guest/info', [GuestNetworkUserController::class, 'info']);
Route::post('/guest/login', [GuestNetworkUserController::class, 'login']);
Route::post('/guest/request-otp', [GuestNetworkUserController::class, 'requestOtp']);
Route::post('/guest/request-email-otp', [GuestNetworkUserController::class, 'requestEmailOtp']);

// Subscription routes
use App\Http\Controllers\SubscriptionController;

// Public subscription routes
Route::get('/subscription/plans', [SubscriptionController::class, 'plans']);

// Protected subscription routes
Route::group(['middleware' => 'auth:api', 'prefix' => 'subscription'], function () {
    Route::get('/status', [SubscriptionController::class, 'status']);
    Route::post('/checkout', [SubscriptionController::class, 'createCheckoutSession']);
    Route::post('/payment-intent', [SubscriptionController::class, 'createPaymentIntent']);
    Route::post('/cancel', [SubscriptionController::class, 'cancel']);
    Route::post('/resume', [SubscriptionController::class, 'resume']);
    Route::post('/confirm', [SubscriptionController::class, 'confirm']);
    Route::get('/billing-portal', [SubscriptionController::class, 'billingPortal']);
});

// E-commerce routes
use App\Http\Controllers\ShopController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\Admin\AdminOrderController;
use App\Http\Controllers\Admin\AdminShippingController;
use App\Http\Controllers\Admin\AdminInventoryController;
use App\Http\Controllers\Admin\AdminProductModelController;

// Public shop endpoints
Route::prefix('v1/shop')->group(function () {
    Route::get('/products', [ShopController::class, 'index']);
    Route::get('/products/{slug}', [ShopController::class, 'show']);
    Route::get('/shipping-rates', [ShopController::class, 'getShippingRates']);
});

// Protected shop endpoints
Route::middleware('auth:api')->prefix('v1')->group(function () {
    // Cart
    Route::get('/cart', [CartController::class, 'show']);
    Route::post('/cart/items', [CartController::class, 'addItem']);
    Route::put('/cart/items/{id}', [CartController::class, 'updateItem']);
    Route::delete('/cart/items/{id}', [CartController::class, 'removeItem']);
    Route::delete('/cart', [CartController::class, 'clear']);
    
    // Orders
    Route::get('/orders', [OrderController::class, 'index']);
    Route::get('/orders/{orderNumber}', [OrderController::class, 'show']);
    Route::get('/orders/{orderNumber}/invoice', [OrderController::class, 'downloadInvoice']);
    Route::post('/orders', [OrderController::class, 'store']);
    Route::post('/orders/{orderNumber}/payment-intent', [PaymentController::class, 'createPaymentIntent']);
    Route::post('/orders/{orderNumber}/verify-payment', [PaymentController::class, 'verifyAndConfirmPayment']);
    
    // Addresses
    Route::get('/addresses', [AddressController::class, 'index']);
    Route::post('/addresses', [AddressController::class, 'store']);
    Route::put('/addresses/{id}', [AddressController::class, 'update']);
    Route::delete('/addresses/{id}', [AddressController::class, 'destroy']);
    Route::post('/addresses/{id}/set-default', [AddressController::class, 'setDefault']);
    
    // Payment success endpoint (accessible by authenticated users)
    Route::get('/orders/{orderNumber}/success', [OrderController::class, 'success']);
    
    // Zones management
    Route::prefix('zones')->group(function () {
        Route::get('/', [ZoneController::class, 'index']);
        Route::post('/', [ZoneController::class, 'store']);
        Route::get('/{zone}', [ZoneController::class, 'show']);
        Route::put('/{zone}', [ZoneController::class, 'update']);
        Route::delete('/{zone}', [ZoneController::class, 'destroy']);
        Route::get('/{zone}/available-locations', [ZoneController::class, 'getAvailableLocations']);
        Route::post('/{zone}/locations/{location}', [ZoneController::class, 'addLocation']);
        Route::delete('/{zone}/locations/{location}', [ZoneController::class, 'removeLocation']);
        Route::put('/{zone}/primary/{location}', [ZoneController::class, 'setPrimaryLocation']);
    });
    
    // Device management
    Route::prefix('devices')->group(function () {
        Route::get('/', [DeviceController::class, 'apiIndex']);
        Route::get('/available-for-location', [DeviceController::class, 'getAvailableForLocation']);
        Route::get('/{id}', [DeviceController::class, 'apiShow']);
        Route::put('/{id}/owner', [DeviceController::class, 'updateOwner']);
    });
});

// Admin-only endpoints
Route::middleware('auth:api')->prefix('v1/admin')->group(function () {
    // Orders
    Route::get('/orders', [\App\Http\Controllers\Admin\AdminOrderController::class, 'index']);
    Route::get('/orders/{orderNumber}', [\App\Http\Controllers\Admin\AdminOrderController::class, 'show']);
    Route::get('/orders/{orderNumber}/invoice', [\App\Http\Controllers\Admin\AdminOrderController::class, 'downloadInvoice']);
    Route::put('/orders/{orderNumber}/tracking', [\App\Http\Controllers\Admin\AdminOrderController::class, 'updateTracking']);
    Route::put('/orders/{orderNumber}/status', [\App\Http\Controllers\Admin\AdminOrderController::class, 'updateStatus']);
    Route::post('/orders/{orderNumber}/resend-email', [\App\Http\Controllers\Admin\AdminOrderController::class, 'resendEmail']);
    Route::post('/orders/{orderNumber}/assign-inventory', [\App\Http\Controllers\Admin\AdminOrderController::class, 'assignInventory']);
    Route::post('/orders/{orderNumber}/confirm-payment', [\App\Http\Controllers\Admin\AdminOrderController::class, 'confirmPayment']);
    Route::post('/orders/{orderNumber}/confirm-stripe-payment', [\App\Http\Controllers\Admin\AdminOrderController::class, 'confirmStripePayment']);
    
    // Shipping rates
    Route::get('/shipping-rates', [AdminShippingController::class, 'index']);
    Route::put('/shipping-rates/{id}', [AdminShippingController::class, 'update']);
    Route::post('/shipping-rates/{id}/toggle', [AdminShippingController::class, 'toggle']);
    
    // Inventory management
    Route::get('/inventory', [AdminInventoryController::class, 'index']);
    Route::get('/inventory/summary', [AdminInventoryController::class, 'summary']);
    Route::get('/inventory/{id}', [AdminInventoryController::class, 'show']);
    Route::put('/inventory/{id}/quantity', [AdminInventoryController::class, 'updateQuantity']);
    Route::post('/inventory/{id}/adjust', [AdminInventoryController::class, 'adjustQuantity']);
    Route::put('/inventory/{id}/threshold', [AdminInventoryController::class, 'updateThreshold']);
    
    // Individual inventory items (devices)
    Route::get('/inventory/{id}/items', [AdminInventoryController::class, 'getItems']);
    Route::post('/inventory/{id}/items', [AdminInventoryController::class, 'addItem']);
    Route::post('/inventory/{id}/items/import-csv', [AdminInventoryController::class, 'importCsv']);
    Route::put('/inventory/{productId}/items/{itemId}', [AdminInventoryController::class, 'updateItem']);
    Route::delete('/inventory/{productId}/items/{itemId}', [AdminInventoryController::class, 'deleteItem']);
    
    // Product Models Management
    Route::get('/models', [AdminProductModelController::class, 'index']);
    Route::get('/models/{id}', [AdminProductModelController::class, 'show']);
    Route::post('/models', [AdminProductModelController::class, 'store']);
    Route::put('/models/{id}', [AdminProductModelController::class, 'update']);
    Route::delete('/models/{id}', [AdminProductModelController::class, 'destroy']);
    Route::post('/models/{id}/images', [AdminProductModelController::class, 'uploadImage']);
    Route::delete('/models/{modelId}/images/{imageId}', [AdminProductModelController::class, 'deleteImage']);
    Route::put('/models/{modelId}/images/{imageId}/primary', [AdminProductModelController::class, 'setPrimaryImage']);
});
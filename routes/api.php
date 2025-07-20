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
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DomainBlockingController;

// Public routes (no auth required)
Route::post('auth/login', [AuthController::class, 'login']);
Route::post('auth/register', [AuthController::class, 'register']);

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

// Category routes (protected with auth)
Route::group(['middleware' => 'auth:api', 'prefix' => 'categories'], function () {
    Route::get('/', [CategoryController::class, 'index']);
    Route::get('/enabled', [CategoryController::class, 'enabled']);
    Route::post('/{category}/toggle', [CategoryController::class, 'toggle'])->name('categories.toggle');
    Route::post('/reorder', [CategoryController::class, 'reorder'])->name('categories.reorder');
    Route::get('/{category}/stats', [CategoryController::class, 'stats'])->name('categories.stats');
});

// Additional Domain Blocking API routes
Route::prefix('domain-blocking')->group(function () {
    Route::post('/bulk-delete', [DomainBlockingController::class, 'bulkDelete'])->name('domain-blocking.bulk-delete');
    Route::post('/import', [DomainBlockingController::class, 'import'])->name('domain-blocking.import');
    Route::get('/export', [DomainBlockingController::class, 'export'])->name('domain-blocking.export');
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

Route::post('/captive-portal/{location_id}/info', [GuestNetworkUserController::class, 'info']);
Route::get('/captive-portal/{location_id}/info', [GuestNetworkUserController::class, 'info']);
Route::post('/captive-portal/login', [GuestNetworkUserController::class, 'login']);
Route::post('/captive-portal/twitter-login', [GuestNetworkUserController::class, 'twitterLogin']);

Route::get('/locations/{location}/guest-users', [GuestNetworkUserController::class, 'index']);
Route::resource('/guest-users', GuestNetworkUserController::class);

// Guest Network User routes
Route::post('/location/{location_id}/guest/info', [GuestNetworkUserController::class, 'info']);
Route::post('/guest/login', [GuestNetworkUserController::class, 'login']);
Route::post('/guest/request-otp', [GuestNetworkUserController::class, 'requestOtp']);
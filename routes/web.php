<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CaptivePortalController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DomainBlockingController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ShopController;

// Root login routes (language agnostic)
Route::get('/', function () {
    return view('login');
})->name('login.show');

Route::get('/register', function () {
    return view('register');
})->name('register');

Route::get('/login', function () {
    return view('login');
})->name('login');

Route::get('/password-reset', function () {
    return view('password-reset');
})->name('password-reset');

Route::get('/reset-password', function () {
    return view('reset-password');
})->name('reset-password');

Route::get('/verify-email', function () {
    return view('verify-email');
})->name('verify-email');

Route::get('/check-email', function () {
    return view('check-email');
})->name('check-email');

// Subscription routes
Route::get('/pricing', function () {
    return view('pricing');
})->name('pricing');

Route::get('/subscription/success', function () {
    return view('subscription.success');
})->name('subscription.success');

Route::get('/subscription/cancel', function () {
    return view('subscription.cancel');
})->name('subscription.cancel');

// Handle login submission (API endpoint - language agnostic)
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');

// Logout routes (API endpoints - language agnostic)
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:api')->name('logout');
Route::get('/logout', [AuthController::class, 'logout'])->middleware('auth:api');

// Localized routes. SetLocale middleware reads the URL prefix and calls
// app()->setLocale($locale), so handlers can resolve the right -en/-fr view
// from app()->getLocale(). Shared definitions below are registered once for
// each locale; per-locale branches carry the few asymmetries.
foreach (['en', 'fr'] as $loc) {
    Route::prefix($loc)->name($loc . '.')->group(function () use ($loc) {
        Route::get('/dashboard', function () {
            return view('dashboard');
        })->name('dashboard');

        Route::get('/devices', function () {
            return view('devices');
        })->name('devices');

        Route::get('/accounts', function () {
            return view('accounts');
        })->name('accounts');

        Route::get('/locations', function () {
            return view('locations');
        })->name('locations');

        Route::get('/locations/{location}', function ($location) {
            $locale = app()->getLocale();
            return view('location-details', compact('location', 'locale'));
        })->name('location-details');

        Route::get('/locations/{location}/networks', function ($location) {
            $locale = app()->getLocale();
            return view('location-networks', compact('location', 'locale'));
        })->name('location-networks');

        Route::get('/locations/analytics/{location_id}', function ($location_id) {
            return view('location-analytics', compact('location_id'));
        })->name('location-analytics');

        Route::get('/system-settings', function () {
            return view('system-settings');
        })->name('system-settings');

        Route::get('/profile', function () {
            return view('profile');
        })->name('profile');

        Route::get('/location-analytics', function () {
            return view('location-analytics');
        })->name('location-analytics');

        Route::get('/locations/{location}/guests', function ($location) {
            return view('location-guests', compact('location'));
        })->name('location-guests');

        Route::get('/zones', function () {
            return view('zones');
        })->name('zones');

        Route::get('/zones/{zone}', function ($zone) {
            return view('zone-details', compact('zone'));
        })->name('zone-details');

        Route::get('/firmware', function () {
            return view('firmware');
        })->name('firmware');

        Route::get('/analytics', function () {
            return view('analytics');
        })->name('analytics');

        Route::get('/captive-portals', function () {
            return view('captive-portals');
        })->name('captive-portals');

        Route::get('/domain-blocking', [DomainBlockingController::class, 'show_page'])
            ->defaults('locale', $loc)
            ->name('domain-blocking');

        Route::get('/blocked-domains/export', [DomainBlockingController::class, 'export'])->name('blocked-domains.export');
        Route::resource('blocked-domains', DomainBlockingController::class)->except(['create', 'edit']);
        Route::resource('categories', CategoryController::class)->except(['create', 'edit']);

        Route::get('/qos-settings', function () {
            return view('qos-settings');
        })->name('qos-settings');

        if ($loc === 'en') {
            Route::get('/v2/locations/{location}', function ($location) {
                return view('location-details-v2-en');
            })->name('location-details-v2');
        }

        // E-commerce
        Route::get('/shop', [ShopController::class, 'indexView'])->name('shop');
        Route::get('/shop/{slug}', [ShopController::class, 'detailView'])->name('product');
        Route::get('/cart', [CartController::class, 'view'])->name('cart');
        Route::get('/checkout', [OrderController::class, 'checkoutView'])->name('checkout');
        Route::get('/orders', [OrderController::class, 'listView'])->name('orders');
        Route::get('/orders/{orderNumber}', [OrderController::class, 'successView'])->name('order-success');

        // E-commerce admin (blades still per-locale pending merge)
        Route::get('/admin/orders', function () {
            return view('admin-orders');
        })->name('admin-orders');
        Route::get('/admin/inventory', function () {
            return view('admin-inventory');
        })->name('admin-inventory');
        Route::get('/admin/models', function () {
            return view('admin-models-' . app()->getLocale());
        })->name('admin-models');
    });
}

// Static pages (language agnostic)
Route::get('/tos', function () {
    return view('tos');
})->name('tos');

Route::get('/privacy-policy', function () {
    return view('privacy-policy');
})->name('privacy-policy');

// Legacy routes (redirect to English by default or handle backward compatibility)
Route::get('/dashboard', function () {
    return redirect('/en/dashboard');
});

Route::get('/devices', function () {
    return redirect('/en/devices');
});

Route::get('/accounts', function () {
    return redirect('/en/accounts');
});

Route::get('/locations', function () {
    return redirect('/en/locations');
});

Route::get('/system-settings', function () {
    return redirect('/en/system-settings');
});

Route::get('/profile', function () {
    return redirect('/en/profile');
});

Route::get('/firmware', function () {
    return redirect('/en/firmware');
});

Route::get('/captive-portals', function () {
    return redirect('/en/captive-portals');
});

Route::get('/domain-blocking', function () {
    return redirect('/en/domain-blocking');
});

Route::get('/guest-login', function () {
    $responseState = request('res', 'notyet');
    
    if ($responseState === 'success') {
        return view('guest-login-success');
    } else if ($responseState === 'failed') {
        return view('guest-login-failed');
    } else if ($responseState === 'already') {
        return view('already-logged-in');
    } else {
        // Default to 'notyet' state
        return view('guest-login');
    }
})->name('guest-login');

// New routes: include zone_id segment  /…/{network_id}/{zone_id}/{mac_address}
Route::get('/email-login/{location}/{zone_id}/{mac_address}', function () {
    return view('email-login');
})->name('email-login');

Route::get('/sms-login/{location}/{zone_id}/{mac_address}', function () {
    return view('sms-login');
})->name('sms-login');

Route::get('/social-login/facebook/{location}/{zone_id}/{mac_address}', function () {
    return view('facebook-login');
})->name('facebook-login');

Route::get('/social-login/facebook-callback', function () {
    return view('facebook-login-callback');
})->name('facebook-login-callback');

Route::get('/social-login/twitter/{location}/{zone_id}/{mac_address}', function () {
    return view('twitter-login');
})->name('twitter-login');

Route::get('/social-login/twitter-callback', function () {
    return view('twitter-login-callback');
})->name('twitter-login-callback');

Route::get('/social-login/google/{location}/{zone_id}/{mac_address}', function () {
    return view('google-login');
})->name('google-login');

Route::get('/social-login/google-callback', function () {
    return view('google-login-callback');
})->name('google-login-callback');

Route::get('/click-login/{location}/{zone_id}/{mac_address}', function () {
    return view('click-login');
})->name('click-login');

Route::get('/password-login/{location}/{zone_id}/{mac_address}', function () {
    return view('password-login');
})->name('password-login');

Route::get('/login-select/{location}/{zone_id}/{mac_address}', function () {
    return view('login-select');
})->name('login-select');

// Legacy routes without zone_id — kept for backward compatibility with older nasid formats
Route::get('/email-login/{location}/{mac_address}', function () {
    return view('email-login');
})->name('email-login-legacy');

Route::get('/sms-login/{location}/{mac_address}', function () {
    return view('sms-login');
})->name('sms-login-legacy');

Route::get('/social-login/facebook/{location}/{mac_address}', function () {
    return view('facebook-login');
})->name('facebook-login-legacy');

Route::get('/social-login/twitter/{location}/{mac_address}', function () {
    return view('twitter-login');
})->name('twitter-login-legacy');

Route::get('/social-login/google/{location}/{mac_address}', function () {
    return view('google-login');
})->name('google-login-legacy');

Route::get('/click-login/{location}/{mac_address}', function () {
    return view('click-login');
})->name('click-login-legacy');

Route::get('/password-login/{location}/{mac_address}', function () {
    return view('password-login');
})->name('password-login-legacy');

Route::get('/login-select/{location}/{mac_address}', function () {
    return view('login-select');
})->name('login-select-legacy');

// Captive Portal routes
Route::get('/captive-portal/{location_id}', [CaptivePortalController::class, 'showLoginPage']);
Route::post('/captive-portal/login', [CaptivePortalController::class, 'login']);

Route::get('/register-with-captive-portal', function () {
    return view('register-with-captive-portal');
})->name('register-with-captive-portal');


<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CaptivePortalController;
use App\Http\Controllers\DomainBlockingController;
use App\Http\Controllers\CategoryController;

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
            $locale = app()->getLocale();
            return view('accounts-' . $locale, compact('locale'));
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
            return view('system-settings-' . app()->getLocale());
        })->name('system-settings');

        Route::get('/profile', function () {
            $locale = app()->getLocale();
            return view('profile-' . $locale, compact('locale'));
        })->name('profile');

        Route::get('/location-analytics', function () {
            return view('location-analytics');
        })->name('location-analytics');

        // Asymmetric: EN returns the bare view, FR returns the -fr suffixed view.
        Route::get('/locations/{location}/guests', function ($location) {
            $view = app()->getLocale() === 'fr' ? 'location-guests-fr' : 'location-guests';
            return view($view, compact('location'));
        })->name('location-guests');

        Route::get('/zones', function () {
            return view('zones');
        })->name('zones');

        Route::get('/zones/{zone}', function ($zone) {
            return view('zone-details', compact('zone'));
        })->name('zone-details');

        Route::get('/firmware', function () {
            return view('firmware-' . app()->getLocale());
        })->name('firmware');

        Route::get('/analytics', function () {
            return view('analytics');
        })->name('analytics');

        Route::get('/captive-portals', function () {
            $locale = app()->getLocale();
            return view('captive-portals-' . $locale, compact('locale'));
        })->name('captive-portals');

        Route::get('/domain-blocking', [DomainBlockingController::class, 'show_page'])
            ->defaults('locale', $loc)
            ->name('domain-blocking');

        Route::get('/blocked-domains/export', [DomainBlockingController::class, 'export'])->name('blocked-domains.export');
        Route::resource('blocked-domains', DomainBlockingController::class)->except(['create', 'edit']);
        Route::resource('categories', CategoryController::class)->except(['create', 'edit']);

        if ($loc === 'en') {
            Route::get('/v2/locations/{location}', function ($location) {
                return view('location-details-v2-en');
            })->name('location-details-v2');

            Route::get('/qos-settings', function () {
                $locale = app()->getLocale();
                return view('qos-settings-' . $locale, compact('locale'));
            })->name('qos-settings');
        }

        if ($loc === 'fr') {
            // French-slug aliases for existing locations/guests URLs.
            Route::get('/emplacements', function () {
                return view('locations');
            });
            Route::get('/emplacements/{location}', function ($location) {
                $locale = 'fr';
                return view('location-details', compact('location', 'locale'));
            });
            Route::get('/emplacements/{location}/networks', function ($location) {
                $locale = 'fr';
                return view('location-networks', compact('location', 'locale'));
            });
            Route::get('/emplacements/{location}/guests', function ($location) {
                return view('location-guests-fr', compact('location'));
            });

            Route::get('/parametres-qos', function () {
                $locale = 'fr';
                return view('qos-settings-fr', compact('locale'));
            })->name('qos-settings');
        }
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

// E-commerce shop routes (public pages, auth handled by API)
use App\Http\Controllers\ShopController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;

// Shop listing
Route::get('/en/shop', [ShopController::class, 'indexView'])->name('shop.en');
Route::get('/fr/boutique', [ShopController::class, 'indexView'])->name('shop.fr');

// Product detail
Route::get('/en/shop/{slug}', [ShopController::class, 'detailView'])->name('product.en');
Route::get('/fr/boutique/{slug}', [ShopController::class, 'detailView'])->name('product.fr');

// Cart
Route::get('/en/cart', [CartController::class, 'view'])->name('cart.en');
Route::get('/fr/panier', [CartController::class, 'view'])->name('cart.fr');

// Checkout
Route::get('/en/checkout', [OrderController::class, 'checkoutView'])->name('checkout.en');
Route::get('/fr/commander', [OrderController::class, 'checkoutView'])->name('checkout.fr');

// My orders
Route::get('/en/orders', [OrderController::class, 'listView'])->name('orders.en');
Route::get('/fr/commandes', [OrderController::class, 'listView'])->name('orders.fr');

// Order success
Route::get('/en/orders/{orderNumber}', [OrderController::class, 'successView'])->name('order.success.en');
Route::get('/fr/commandes/{orderNumber}', [OrderController::class, 'successView'])->name('order.success.fr');

// Admin shop management routes (protected by admin role)
Route::get('/en/admin/orders', function () {
    return view('admin-orders-en');
})->name('admin.orders.en');

Route::get('/fr/admin/commandes', function () {
    return view('admin-orders-fr');
})->name('admin.orders.fr');

Route::get('/en/admin/inventory', function () {
    return view('admin-inventory-en');
})->name('admin.inventory.en');

Route::get('/fr/admin/inventaire', function () {
    return view('admin-inventory-fr');
})->name('admin.inventory.fr');

Route::get('/en/admin/models', function () {
    return view('admin-models-en');
})->name('admin.models.en');

Route::get('/fr/admin/modeles', function () {
    return view('admin-models-fr');
})->name('admin.models.fr');

// Devices
Route::get('/en/devices', function () {
    return view('devices-en');
})->name('devices');

Route::get('/fr/appareils', function () {
    return view('devices-fr');
})->name('fr.devices');
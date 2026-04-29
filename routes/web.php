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
        Route::get('/pricing', function () use ($loc) {
            return view('pricing', ['locale' => $loc]);
        })->name('pricing');

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

        Route::get('/locations/{location}/networks', function ($location) use ($loc) {
            return redirect("/{$loc}/locations/{$location}?tab=networks", 301);
        })->name('location-networks');

        Route::get('/system-settings', function () {
            return view('system-settings');
        })->name('system-settings');

        Route::get('/profile', function () {
            return view('profile');
        })->name('profile');

        Route::get('/locations/{location}/guests', function ($location) {
            return view('location-guests', compact('location'));
        })->name('location-guests');

        Route::get('/zones', function () use ($loc) {
            return redirect("/{$loc}/access-points?tab=zones");
        })->name('zones');

        Route::get('/access-points', function () {
            return view('access-points');
        })->name('access-points');

        Route::get('/zones/{zone}', function ($zone) {
            return view('zone-details', compact('zone'));
        })->name('zone-details');

        Route::get('/firmware', function () {
            return view('firmware');
        })->name('firmware');

        Route::get('/captive-portals', function () {
            return view('captive-portals');
        })->name('captive-portals');

        Route::get('/captive-portals/preview/new', function () {
            return view('captive-portal-preview', ['design' => null]);
        })->name('captive-portals.preview.new');

        Route::get('/captive-portals/{design_id}', [CaptivePortalController::class, 'showDesigner'])
            ->name('captive-portals.edit');

        Route::get('/captive-portals/{design_id}/preview', [CaptivePortalController::class, 'showPreview'])
            ->name('captive-portals.preview');

        Route::get('/domain-blocking', [DomainBlockingController::class, 'show_page'])
            ->defaults('locale', $loc)
            ->name('domain-blocking');

        Route::get('/blocked-domains/export', [DomainBlockingController::class, 'export'])->name('blocked-domains.export');
        Route::resource('blocked-domains', DomainBlockingController::class)->except(['create', 'edit']);
        Route::resource('categories', CategoryController::class)->except(['create', 'edit']);

        Route::get('/qos-settings', function () {
            return view('qos-settings');
        })->name('qos-settings');

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
            return view('admin-models');
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

// ── Email template previews — opt-in via env ───────────────────────────────
// Set MAIL_PREVIEW_ENABLED=true in .env to expose /dev/emails locally.
// Off by default so production deploys never expose previews even if APP_ENV slips.
if (filter_var(env('MAIL_PREVIEW_ENABLED', false), FILTER_VALIDATE_BOOLEAN)) {
    Route::prefix('dev/emails')->group(function () {
        // Stubs that quack like the Eloquent models the mailables expect.
        // load() and relationLoaded() are no-ops so the mailable's content() doesn't blow up.
        $stubFor = function () {
            $user = new class {
                public string $name = 'Johnny Sample';
                public string $email = 'johnny@example.com';
                public \Carbon\Carbon $created_at;
                public function __construct() { $this->created_at = now()->subHours(2); }
            };

            $shipping = (object) [
                'first_name' => 'Johnny', 'last_name' => 'Sample',
                'address_line1' => '12 rue Lafayette', 'address_line2' => 'Apt 4B',
                'city' => 'Paris', 'province' => 'Île-de-France',
                'postal_code' => '75009', 'country' => 'France',
            ];

            $items = collect([
                (object) ['productModel' => (object) ['name' => 'MW-AP-3000 — Wi-Fi 6 Access Point'], 'quantity' => 2, 'subtotal' => 359.98],
                (object) ['productModel' => (object) ['name' => 'PoE+ Injector 30W'],                  'quantity' => 1, 'subtotal' => 24.50],
            ]);

            $order = new class($user, $items, $shipping) {
                public $user; public $items; public $shippingAddress;
                public string $order_number    = 'MW-2026-0427';
                public float  $product_amount  = 384.48;
                public float  $shipping_cost   = 9.90;
                public float  $tax_amount      = 78.88;
                public float  $total           = 473.26;
                public string $shipping_provider = 'Chronopost';
                public string $tracking_id     = 'CP9X12345678FR';
                public function __construct($user, $items, $shipping)
                {
                    $this->user = $user; $this->items = $items; $this->shippingAddress = $shipping;
                }
                public function relationLoaded($r) { return true; }
                public function load($r) { return $this; }
            };

            $cart = new class($user, $items) {
                public $user; public $items;
                public function __construct($user, $items) { $this->user = $user; $this->items = $items; }
                public function getTotal() { return 384.48; }
            };

            $subscription = [
                'plan_name' => 'Pro Annual', 'amount' => '€199.00',
                'interval' => 'Annual',      'start_date' => '28/04/2026',
                'shipping_address' => [
                    'name' => 'Johnny Sample', 'line1' => '12 rue Lafayette', 'line2' => 'Apt 4B',
                    'postal_code' => '75009', 'city' => 'Paris',
                    'state' => 'Île-de-France', 'country' => 'France',
                ],
            ];

            return compact('user', 'order', 'cart', 'subscription');
        };

        $previews = [
            'verify-email' => ['label' => 'Verify Your Email', 'mailable' => function () {
                return new App\Mail\VerifyEmailMail('https://dev.monsieur-wifi.com/verify-email?token=PREVIEW_SAMPLE_TOKEN_123abc&email=preview@monsieur-wifi.com&set_password=1', 'Johnny', 60);
            }],
            'password-reset' => ['label' => 'Password Reset', 'mailable' => function () {
                return new App\Mail\PasswordResetMail('https://dev.monsieur-wifi.com/reset-password?token=PREVIEW_SAMPLE_TOKEN_xyz789', 'Johnny', 60);
            }],
            'guest-otp' => ['label' => 'Guest OTP', 'mailable' => function () {
                return new App\Mail\GuestOtpMail('123456');
            }],
            'cart-abandonment' => ['label' => 'Cart Abandonment', 'mailable' => function () use ($stubFor) {
                $s = $stubFor();
                $m = (new ReflectionClass(App\Mail\CartAbandonmentMail::class))->newInstanceWithoutConstructor();
                $m->cart = $s['cart']; $m->locale = 'en';
                return $m;
            }],
            'order-processed' => ['label' => 'Order Processed', 'mailable' => function () use ($stubFor) {
                $s = $stubFor();
                $m = (new ReflectionClass(App\Mail\OrderProcessedMail::class))->newInstanceWithoutConstructor();
                $m->order = $s['order']; $m->locale = 'en';
                return $m;
            }],
            'order-delivered' => ['label' => 'Order Delivered', 'mailable' => function () use ($stubFor) {
                $s = $stubFor();
                $m = (new ReflectionClass(App\Mail\OrderDeliveredMail::class))->newInstanceWithoutConstructor();
                $m->order = $s['order']; $m->locale = 'en';
                return $m;
            }],
            'payment-failed' => ['label' => 'Payment Failed', 'mailable' => function () use ($stubFor) {
                $s = $stubFor();
                $m = (new ReflectionClass(App\Mail\PaymentFailedMail::class))->newInstanceWithoutConstructor();
                $m->order = $s['order']; $m->locale = 'en';
                return $m;
            }],
            'shipping-tracking' => ['label' => 'Shipping Tracking', 'mailable' => function () use ($stubFor) {
                $s = $stubFor();
                $m = (new ReflectionClass(App\Mail\ShippingTrackingMail::class))->newInstanceWithoutConstructor();
                $m->order = $s['order']; $m->locale = 'en';
                return $m;
            }],
            'subscription-confirmed' => ['label' => 'Subscription Confirmed', 'mailable' => function () use ($stubFor) {
                $s = $stubFor();
                $m = (new ReflectionClass(App\Mail\SubscriptionConfirmedMail::class))->newInstanceWithoutConstructor();
                $m->user = $s['user']; $m->subscriptionData = $s['subscription']; $m->locale = 'en';
                return $m;
            }],
            'new-subscription-admin' => ['label' => 'New Subscription (Admin)', 'mailable' => function () use ($stubFor) {
                $s = $stubFor();
                $m = (new ReflectionClass(App\Mail\NewSubscriptionAdminNotification::class))->newInstanceWithoutConstructor();
                $m->user = $s['user']; $m->subscriptionData = $s['subscription'];
                return $m;
            }],
        ];

        Route::get('/', function () use ($previews) {
            $rows = collect($previews)->map(fn($p, $slug) =>
                "<li style='margin:6px 0;'><a href='/dev/emails/{$slug}' style='color:#6366F1; text-decoration:none; font-weight:500;'>{$p['label']}</a></li>"
            )->implode('');
            return "<!DOCTYPE html><html><body style='font-family:-apple-system,BlinkMacSystemFont,Segoe UI,sans-serif; padding:32px; background:#EDEEF2; color:#1A1A2E;'>"
                 . "<h1 style='margin:0 0 16px;'>Email previews</h1>"
                 . "<p style='color:#5C6370; margin:0 0 24px;'>Local-only. Reload after every edit to iterate.</p>"
                 . "<ul style='line-height:2; padding-left:20px;'>{$rows}</ul></body></html>";
        });

        foreach ($previews as $slug => $preview) {
            Route::get("/{$slug}", $preview['mailable']);
        }
    });
}



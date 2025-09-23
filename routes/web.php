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

Route::get('/login', function () {
    return view('login');
})->name('login');

// Handle login submission (API endpoint - language agnostic)
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');

// Logout routes (API endpoints - language agnostic)
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:api')->name('logout');
Route::get('/logout', [AuthController::class, 'logout'])->middleware('auth:api')->name('logout');

// English language routes group
Route::prefix('en')->name('en.')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard-en');
    })->name('dashboard');
    
    Route::get('/devices', function () {
        return view('devices');
    })->name('devices');
    
    Route::get('/accounts', function () {
        return view('accounts-en');
    })->name('accounts');
    
    Route::get('/locations', function () {
        return view('locations-en');
    })->name('locations');
    
    Route::get('/locations/{location}', function () {
        return view('location-details-en');
    })->name('location-details');
    
    Route::get('/locations/analytics/{location_id}', function ($location_id) {
        return view('location-analytics', compact('location_id'));
    })->name('location-analytics');
    
    Route::get('/system-settings', function () {
        return view('system-settings-en');
    })->name('system-settings');
    
    Route::get('/profile', function () {
        return view('profile-en');
    })->name('profile');
    
    Route::get('/location-details', function () {
        return view('location-details');
    })->name('location-details');
    
    Route::get('/location-analytics', function () {
        return view('location-analytics');
    })->name('location-analytics');
    
    Route::get('/firmware', function () {
        return view('firmware-en');
    })->name('firmware');
    
    Route::get('/analytics', function () {
        return view('analytics');
    })->name('analytics');
    
    Route::get('/captive-portals', function () {
        return view('captive-portals-en');
    })->name('captive-portals');
    
    Route::get('/domain-blocking', [DomainBlockingController::class, 'show_page'])->defaults('locale', 'en')->name('domain-blocking');
    
    // Domain Blocking Routes (English)
    Route::get('/blocked-domains/export', [DomainBlockingController::class, 'export'])->name('blocked-domains.export');
    Route::resource('blocked-domains', DomainBlockingController::class)->except(['create', 'edit']);
    Route::resource('categories', CategoryController::class)->except(['create', 'edit']);
});

// French language routes group
Route::prefix('fr')->name('fr.')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard-fr');
    })->name('dashboard');
    
    Route::get('/devices', function () {
        return view('devices');
    })->name('devices');
    
    Route::get('/accounts', function () {
        return view('accounts-fr');
    })->name('accounts');
    
    Route::get('/locations', function () {
        return view('locations-fr');
    })->name('locations');
    
    Route::get('/locations/{location}', function () {
        return view('location-details-fr');
    })->name('location-details');
    
    Route::get('/locations/analytics/{location_id}', function ($location_id) {
        return view('location-analytics', compact('location_id'));
    })->name('location-analytics');
    
    Route::get('/system-settings', function () {
        return view('system-settings-fr');
    })->name('system-settings');
    
    Route::get('/profile', function () {
        return view('profile-fr');
    })->name('profile');
    
    Route::get('/location-details', function () {
        return view('location-details');
    })->name('location-details');
    
    Route::get('/location-analytics', function () {
        return view('location-analytics');
    })->name('location-analytics');
    
    Route::get('/firmware', function () {
        return view('firmware-fr');
    })->name('firmware');
    
    Route::get('/analytics', function () {
        return view('analytics');
    })->name('analytics');
    
    Route::get('/captive-portals', function () {
        return view('captive-portals-fr');
    })->name('captive-portals');
    
    Route::get('/domain-blocking', [DomainBlockingController::class, 'show_page'])->defaults('locale', 'fr')->name('domain-blocking');
    
    // Domain Blocking Routes (French)
    Route::get('/blocked-domains/export', [DomainBlockingController::class, 'export'])->name('blocked-domains.export');
    Route::resource('blocked-domains', DomainBlockingController::class)->except(['create', 'edit']);
    Route::resource('categories', CategoryController::class)->except(['create', 'edit']);
});

// Static pages (language agnostic)
Route::get('/tos', function () {
    return view('tos');
})->name('tos');

Route::get('/privacy-policy', function () {
    return view('privacy-policy');
})->name('privacy-policy');

Route::get('/terms-of-service', function () {
    return view('terms-of-service');
})->name('terms-of-service');

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

Route::get('/analytics', function () {
    return redirect('/en/analytics');
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

Route::get('/email-login/{location}/{mac_address}', function () {
    return view('email-login');
})->name('email-login');

Route::get('/sms-login/{location}/{mac_address}', function () {
    return view('sms-login');
})->name('sms-login');

Route::get('/social-login/facebook/{location}/{mac_address}', function () {
    return view('facebook-login');
})->name('facebook-login');

Route::get('/social-login/facebook-callback', function () {
    return view('facebook-login-callback');
})->name('facebook-login-callback');

Route::get('/social-login/twitter/{location}/{mac_address}', function () {
    return view('twitter-login');
})->name('twitter-login');

Route::get('/social-login/twitter-callback', function () {
    return view('twitter-login-callback');
})->name('twitter-login-callback');

Route::get('/social-login/google/{location}/{mac_address}', function () {
    return view('google-login');
})->name('google-login');

Route::get('/social-login/google-callback', function () {
    return view('google-login-callback');
})->name('google-login-callback');


Route::get('/click-login/{location}/{mac_address}', function () {
    return view('click-login');
})->name('click-login');

Route::get('/password-login/{location}/{mac_address}', function () {
    return view('password-login');
})->name('password-login');



// Captive Portal routes
Route::get('/captive-portal/{location_id}', [CaptivePortalController::class, 'showLoginPage']);
Route::post('/captive-portal/login', [CaptivePortalController::class, 'login']);
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Location;
use App\Models\LocationSettingsV2;
use App\Models\CaptivePortalDesign;
use Illuminate\Support\Facades\Log;

class CaptivePortalController extends Controller
{
    /**
     * Display the captive portal login page for a location
     *
     * @param  int  $location_id
     * @return \Illuminate\Http\Response
     */
    public function showLoginPage($location_id)
    {
        // Find the location
        $location = Location::find($location_id);
        
        if (!$location) {
            return response()->view('errors.404', [], 404);
        }
        
        // Get the location settings
        $locationSettings = LocationSettingsV2::where('location_id', $location_id)->first();
        
        if (!$locationSettings) {
            return response()->view('errors.500', ['message' => 'Location settings not found'], 500);
        }
        
        // Determine the authentication method
        $authMethod = 'click-through'; // Default method
        
        if (isset($locationSettings->captive_auth_method)) {
            $authMethod = $locationSettings->captive_auth_method;
        } elseif ($locationSettings->social_login_enabled) {
            $authMethod = 'social';
        } elseif ($locationSettings->email_login_enabled) {
            $authMethod = 'email';
        } elseif ($locationSettings->verification_required) {
            $authMethod = 'sms';
        }
        
        // Get the captive portal design
        $design = null;
        if ($locationSettings->captive_portal_design) {
            $design = CaptivePortalDesign::find($locationSettings->captive_portal_design);
            Log::info('Loaded captive portal design: ' . json_encode($design));
        }
        
        // Prepare view data
        $viewData = [
            'location' => $location,
            'settings' => $locationSettings,
            'design' => $design,
            'authMethod' => $authMethod
        ];
        
        // Return the appropriate view based on the authentication method
        switch ($authMethod) {
            case 'social':
                return view('social-login', $viewData);
            case 'email':
                return view('email-login', $viewData);
            case 'sms':
                return view('sms-login', $viewData);
            case 'click-through':
            default:
                return view('click-login', $viewData);
        }
    }
    
    /**
     * Handle the login request
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        // This is just a redirect since we're using the API endpoint for actual auth
        return redirect()->to($request->input('redirect_url', '/'));
    }
} 
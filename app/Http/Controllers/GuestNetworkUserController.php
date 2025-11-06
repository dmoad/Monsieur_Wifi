<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Location;
use App\Models\LocationSettings;
use App\Models\GuestNetworkUser;
use App\Models\Radcheck;
use App\Models\OtpVerification;
use App\Services\SmsService;
use Validator;
use Log;

class GuestNetworkUserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, $location)
    {
        try {
            $locationModel = Location::find($location);
            
            if (!$locationModel) {
                return response()->json([
                    'success' => false,
                    'message' => 'Location not found'
                ], 404);
            }
            
            $query = GuestNetworkUser::where('location_id', $location)
                ->orderBy('created_at', 'desc');
            
            // Optional search filtering
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('mac_address', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%");
                });
            }
            
            $guests = $query->get();
            
            // Transform the data
            $transformedGuests = $guests->map(function ($guest) {
                return [
                    'id' => $guest->id,
                    'mac_address' => $guest->mac_address,
                    'email' => $guest->email,
                    'phone' => $guest->phone,
                    'expiration_time' => $guest->expiration_time ? $guest->expiration_time->format('Y-m-d H:i:s') : null,
                    'blocked' => $guest->blocked,
                    'created_at' => $guest->created_at->format('Y-m-d H:i:s'),
                ];
            });
            
            return response()->json([
                'success' => true,
                'data' => $transformedGuests,
                'total' => $transformedGuests->count()
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching guest users: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving guest users: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export guest users to CSV.
     */
    public function export(Request $request, $location)
    {
        try {
            $locationModel = Location::find($location);
            
            if (!$locationModel) {
                return response()->json([
                    'success' => false,
                    'message' => 'Location not found'
                ], 404);
            }
            
            $query = GuestNetworkUser::where('location_id', $location)
                ->orderBy('created_at', 'desc');
            
            // Optional search filtering
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('mac_address', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%");
                });
            }
            
            $guests = $query->get();
            
            $timestamp = now()->format('Y-m-d_H-i-s');
            $filename = "location_{$location}_guests_{$timestamp}.csv";
            
            // Create CSV content
            $content = "MAC Address,Email,Phone Number\n";
            foreach ($guests as $guest) {
                $content .= sprintf(
                    "%s,%s,%s\n",
                    $guest->mac_address ?? '',
                    $guest->email ?? '',
                    $guest->phone ?? ''
                );
            }
            
            return response($content)
                ->header('Content-Type', 'text/csv')
                ->header('Content-Disposition', "attachment; filename=\"{$filename}\"");
        } catch (\Exception $e) {
            Log::error('Error exporting guest users: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Export failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function info(Request $request, $location_id)
    {
        $request = $request->all();
        $request['location_id'] = $location_id;

        $validator = Validator::make($request, [
            'location_id' => 'required|exists:locations,id',
            'mac_address' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $locationId = $location_id;
        if (isset($request['mac_address'])) {
            $macAddress = $request['mac_address'];
            $user = GuestNetworkUser::where('location_id', $locationId)
            ->where('mac_address', $macAddress)
            ->first();  
        } else {
            $user = null;
        }

        $location = Location::with('settings')
            ->select('id', 'name', 'description')
            ->find($locationId);

        if (!$location) {
            return response()->json([
                'success' => false,
                'message' => 'Location not found'
            ], 404);
        }

        // Access captive portal settings
        $captivePortalSettings = $location->settings->only([
            'captive_portal_enabled',
            'captive_portal_ssid',
            'captive_portal_visible',
            'captive_auth_method',
            'session_timeout',
            'idle_timeout',
            'captive_portal_redirect',
            'terms_enabled',
            'terms_content',
            'theme_color',
            'logo_url',
            'welcome_message',
            'captive_portal_design',
            'captive_social_auth_method' 
        ]);



        // Fetch the complete captive portal design if a design ID is set
        $captivePortalDesign = null;
        if ($location->settings && $location->settings->captive_portal_design) {
            $captivePortalDesign = \App\Models\CaptivePortalDesign::find($location->settings->captive_portal_design);
        }

        Log::info("captivePortalDesign Id::");
        Log::info($location->settings->captive_portal_design);
        Log::info("captivePortalSettings::");
        Log::info($captivePortalDesign);

        // Get the captive portal IP address from location settings or use a default
        $captivePortalIp = $location->settings->captive_portal_ip ?? '10.1.0.1';
        
        // Generate a challenge for CHAP authentication
        $challenge = bin2hex(random_bytes(16));

        $brand = [
            'name' => env('APP_BRAND_NAME'),
            'logo_url' => env('APP_BRAND_LOGO'),
            'welcome_message' => env('APP_BRAND_WELCOME_MESSAGE'),
            'terms_of_service_url' => env('APP_BRAND_TERMS_OF_SERVICE_URL'),
            'privacy_policy_url' => env('APP_BRAND_PRIVACY_POLICY_URL'),
        ];
        // Create a filtered version of the location object
        $locationData = [
            'id' => $location->id,
            'name' => $location->name,
            'description' => $location->description,
            'settings' => $captivePortalSettings,
            'design' => $captivePortalDesign,
            'ip_address' => $captivePortalIp,
            'challenge' => $challenge
        ];

        return response()->json([
            'success' => true,
            'message' => 'User found',
            'location' => $locationData,
            'user' => $user,
            'brand' => $brand
        ]);
    }

    /**
     * Request an SMS OTP for WiFi login
     */
    public function requestOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'location_id' => 'required|exists:locations,id',
            'phone' => 'required|string|max:20',
            'mac_address' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $phone = $request->phone;
        $locationId = $request->location_id;
        $macAddress = $request->mac_address;

        // Generate a new OTP
        $otpVerification = OtpVerification::generateOtp($phone, $locationId, $macAddress);

        // Send the OTP via SMS
        $smsService = new SmsService();
        Log::info("Sending OTP ".$otpVerification->otp." to {$phone}");
        $smsSent = $smsService->sendOtp($phone, $otpVerification->otp);

        if (!$smsSent) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send OTP. Please try again later.'
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'OTP sent successfully',
            'expires_at' => $otpVerification->expires_at
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function login(Request $request)
    {
        $request = $request->all();
        Log::info($request);
        $validator = Validator::make($request, [
            'location_id' => 'required|exists:locations,id',
            'mac_address' => 'nullable|string|max:255',
            'login_method' => 'required|string|in:email,sms,social,click-through,password',
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:255',
            'social_platform' => 'nullable|string|max:255',
            'otp' => 'nullable|string|size:4',
            'challenge' => 'required|string|max:255',
            'ip_address' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            Log::info("Validation failed");
            Log::info($validator->errors());
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        if ($request['login_method'] == 'email') {
            if (!$request['email']) {   
                return response()->json([
                    'success' => false,
                    'message' => 'Email is required'
                ], 422);
            }
        } else if ($request['login_method'] == 'sms') {
            Log::info("Login method is SMS");
            if (!$request['phone'] || !$request['otp']) {
                Log::info("Phone and OTP are required");
                return response()->json([
                    'success' => false,
                    'message' => 'Phone and OTP are required'
                ], 422);
            }
            
            // Verify OTP
            if (!OtpVerification::verifyOtp($request['phone'], $request['otp'], $request['location_id'])) {
                Log::info("Invalid or expired OTP");
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid or expired OTP'
                ], 422);
            }
        } else if ($request['login_method'] == 'social') {
            if (!$request['social_platform']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Social platform is required'
                ], 422);
            }
        } else if ($request['login_method'] == 'password') {
            if (!$request['password']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Password is required'
                ], 422);
            }
        }
        
        $location_id = $request['location_id'];
        $mac_address = $request['mac_address'];
        $login_method = $request['login_method'];
        $location = Location::find($location_id);
        $location_settings = $location->settings;

        if (!$location_settings) {
            return response()->json([
                'success' => false,
                'message' => 'Location settings not found'
            ], 404);
        }

        if ($login_method == 'password') {
            if ($request['password'] != $location_settings->captive_portal_password) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid password'
                ], 422);
            }
        }
        // get if mac is already in the database
        $user = GuestNetworkUser::where('location_id', $location_id)
        ->where('mac_address', $mac_address)
        ->first();

        if (!$user) {
            $user = GuestNetworkUser::create([
                'location_id' => $location_id,
                'mac_address' => $mac_address,
                'blocked' => false,
            ]);
        }

        // Update user with email, phone, and social platform if it is as per login_method
        if ($login_method == 'email') {
            $user->email = $request['email'];
        } else if ($login_method == 'sms') {
            $user->phone = $request['phone'];
        }

        $user->expiration_time = now()->addMinutes($location_settings->session_timeout); 
        $user->download_bandwidth = $location_settings->download_limit;
        $user->upload_bandwidth = $location_settings->upload_limit;
        // $user->idle_timeout = $location_settings->idle_timeout;
        $user->save();

        $radcheck = Radcheck::updateOrCreateRecord($user->mac_address, 'Cleartext-Password', $user->mac_address, '==', [
            'location_id' => $location_id,
            'download_bandwidth' => $user->download_bandwidth,
            'upload_bandwidth' => $user->upload_bandwidth,
            'expiration_time' => $user->expiration_time,
            'idle_timeout' => $location_settings->idle_timeout
        ]);

        // Create login url 
        $challenge = $request['challenge'];
        $uamsecret = ''; // Get from environment or use empty string
        
        $username = $password = $request['mac_address'];
        Log::info("username::".$username);
        Log::info("password::".$password);
        Log::info("uamsecret::".$uamsecret);
        $hexchal = pack("H32", $challenge);
        
        // If strlen of $uamsecret is > 0 then apply MD5 hash with the secret
        // if (strlen($uamsecret) > 0) {
        //     $newchal = pack("H*", md5($hexchal . $uamsecret));
        // } else {
        // $newchal = $hexchal;
        $newchal = pack("H*", md5($hexchal . $uamsecret));
        // }
        
        $response = md5("\0" . $password . $newchal);
        
        // Get the captive portal IP address from the request
        $ip_address = $request['ip_address']; // This is the captive portal WiFi IP
        $redirect_url = env('SOLUTION_URL');

        // Build the login redirect URL
        $redirect_url = $location_settings->redirect_url ?? $redirect_url;
        $login_redirect_url = 'http://' . $ip_address . ':3990/logon?username=' . $username . '&response=' . $response . '&userurl=' . urlencode($redirect_url);

        return response()->json([
            'success' => true,
            'message' => 'User logged in',
            'user' => $user,
            'login_url' => $login_redirect_url,
            'chap_response' => $response
        ]);
        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}

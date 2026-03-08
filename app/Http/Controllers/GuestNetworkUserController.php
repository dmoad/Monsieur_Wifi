<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Location;
use App\Models\LocationNetwork;
use App\Models\GuestNetworkUser;
use App\Models\Radcheck;
use App\Models\OtpVerification;
use App\Services\SmsService;
use Validator;
use Log;

class GuestNetworkUserController extends Controller
{
    /**
     * Display a listing of guest users for a location (admin UI).
     */
    public function index(Request $request, $location)
    {
        try {
            $locationModel = Location::find($location);

            if (!$locationModel) {
                return response()->json(['success' => false, 'message' => 'Location not found'], 404);
            }

            $query = GuestNetworkUser::where('location_id', $location)
                ->orderBy('created_at', 'desc');

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('mac_address', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%");
                });
            }

            $guests = $query->get()->map(fn ($guest) => [
                'id'              => $guest->id,
                'mac_address'     => $guest->mac_address,
                'email'           => $guest->email,
                'phone'           => $guest->phone,
                'expiration_time' => $guest->expiration_time?->format('Y-m-d H:i:s'),
                'blocked'         => $guest->blocked,
                'created_at'      => $guest->created_at->format('Y-m-d H:i:s'),
            ]);

            return response()->json(['success' => true, 'data' => $guests, 'total' => $guests->count()]);
        } catch (\Exception $e) {
            Log::error('Error fetching guest users: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error retrieving guest users: ' . $e->getMessage()], 500);
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
                return response()->json(['success' => false, 'message' => 'Location not found'], 404);
            }

            $query = GuestNetworkUser::where('location_id', $location)->orderBy('created_at', 'desc');

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('mac_address', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%");
                });
            }

            $guests   = $query->get();
            $filename = "location_{$location}_guests_" . now()->format('Y-m-d_H-i-s') . '.csv';
            $content  = "MAC Address,Email,Phone Number\n";

            foreach ($guests as $guest) {
                $content .= sprintf("%s,%s,%s\n", $guest->mac_address ?? '', $guest->email ?? '', $guest->phone ?? '');
            }

            return response($content)
                ->header('Content-Type', 'text/csv')
                ->header('Content-Disposition', "attachment; filename=\"{$filename}\"");
        } catch (\Exception $e) {
            Log::error('Error exporting guest users: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Export failed: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Return network/captive portal info for the guest login page.
     * Route param is now network_id (LocationNetwork id).
     */
    public function info(Request $request, $network_id)
    {
        $params = array_merge($request->all(), ['network_id' => $network_id]);

        $validator = Validator::make($params, [
            'network_id'  => 'required|exists:location_networks,id',
            'mac_address' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        $network = LocationNetwork::with(['location', 'portalDesign'])->find($network_id);

        if (!$network || !$network->location) {
            return response()->json(['success' => false, 'message' => 'Network not found'], 404);
        }

        $user = null;
        if (!empty($params['mac_address'])) {
            $user = GuestNetworkUser::where('network_id', $network_id)
                ->where('mac_address', $params['mac_address'])
                ->first();
        }

        $captivePortalSettings = [
            'captive_portal_enabled'      => $network->enabled,
            'captive_portal_ssid'         => $network->ssid,
            'captive_portal_visible'      => $network->visible,
            'captive_auth_method'         => $network->auth_method,
            'session_timeout'             => $network->session_timeout,
            'idle_timeout'                => $network->idle_timeout,
            'captive_portal_redirect'     => $network->redirect_url,
            'captive_social_auth_method'  => $network->social_auth_method,
            'download_limit'              => $network->download_limit,
            'upload_limit'                => $network->upload_limit,
        ];

        $captivePortalIp = '10.1.0.1'; // Router provides this via uamip query param

        $brand = [
            'name'              => env('APP_BRAND_NAME'),
            'logo_url'          => env('APP_BRAND_LOGO'),
            'welcome_message'   => env('APP_BRAND_WELCOME_MESSAGE'),
            'terms_of_service_url' => env('APP_BRAND_TERMS_OF_SERVICE_URL'),
            'privacy_policy_url'   => env('APP_BRAND_PRIVACY_POLICY_URL'),
        ];

        $locationData = [
            'id'          => $network->id,           // network_id — used by JS for redirect URLs
            'location_id' => $network->location->id,
            'name'        => $network->location->name,
            'description' => $network->location->description ?? null,
            'settings'    => $captivePortalSettings,
            'design'      => $network->portalDesign,
            'ip_address'  => $captivePortalIp,
            'challenge'   => bin2hex(random_bytes(16)),
        ];

        return response()->json([
            'success'  => true,
            'message'  => 'Network info retrieved',
            'location' => $locationData,
            'user'     => $user,
            'brand'    => $brand,
        ]);
    }

    /**
     * Request an SMS OTP — scoped to a specific network.
     */
    public function requestOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'network_id'  => 'required|exists:location_networks,id',
            'phone'       => 'required|string|max:20',
            'mac_address' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        $phone      = $request->phone;
        $networkId  = $request->network_id;
        $macAddress = $request->mac_address;

        $otpVerification = OtpVerification::generateOtp($phone, $networkId, $macAddress);

        $smsService = new SmsService();
        Log::info("Sending OTP {$otpVerification->otp} to {$phone}");
        $smsSent = $smsService->sendOtp($phone, $otpVerification->otp);

        if (!$smsSent) {
            return response()->json(['success' => false, 'message' => 'Failed to send OTP. Please try again later.'], 500);
        }

        return response()->json(['success' => true, 'message' => 'OTP sent successfully', 'expires_at' => $otpVerification->expires_at]);
    }

    /**
     * Authenticate a guest device and return the CoovaChilli CHAP login URL.
     */
    public function login(Request $request)
    {
        $input = $request->all();
        Log::info($input);

        $validator = Validator::make($input, [
            'network_id'      => 'required|exists:location_networks,id',
            'mac_address'     => 'nullable|string|max:255',
            'login_method'    => 'required|string|in:email,sms,social,click-through,password',
            'name'            => 'nullable|string|max:255',
            'email'           => 'nullable|email|max:255',
            'phone'           => 'nullable|string|max:255',
            'social_platform' => 'nullable|string|max:255',
            'otp'             => 'nullable|string|size:4',
            'challenge'       => 'required|string|max:255',
            'ip_address'      => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            Log::info('Validation failed', $validator->errors()->toArray());
            return response()->json(['success' => false, 'message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        $networkId    = $input['network_id'];
        $macAddress   = $input['mac_address'];
        $loginMethod  = $input['login_method'];

        // ── Method-specific pre-checks ───────────────────────────────────────
        if ($loginMethod === 'email' && empty($input['email'])) {
            return response()->json(['success' => false, 'message' => 'Email is required'], 422);
        }

        if ($loginMethod === 'sms') {
            if (empty($input['phone']) || empty($input['otp'])) {
                return response()->json(['success' => false, 'message' => 'Phone and OTP are required'], 422);
            }
            if (!OtpVerification::verifyOtp($input['phone'], $input['otp'], $networkId)) {
                Log::info('Invalid or expired OTP');
                return response()->json(['success' => false, 'message' => 'Invalid or expired OTP'], 422);
            }
        }

        if ($loginMethod === 'social' && empty($input['social_platform'])) {
            return response()->json(['success' => false, 'message' => 'Social platform is required'], 422);
        }

        if ($loginMethod === 'password' && empty($input['password'])) {
            return response()->json(['success' => false, 'message' => 'Password is required'], 422);
        }

        // ── Load network settings ────────────────────────────────────────────
        $network = LocationNetwork::find($networkId);

        if ($loginMethod === 'password' && $input['password'] !== $network->portal_password) {
            return response()->json(['success' => false, 'message' => 'Invalid password'], 422);
        }

        // ── Upsert guest user ────────────────────────────────────────────────
        $user = GuestNetworkUser::firstOrCreate(
            ['network_id' => $networkId, 'mac_address' => $macAddress],
            ['location_id' => $network->location_id, 'blocked' => false]
        );

        if ($loginMethod === 'email') {
            $user->email = $input['email'];
        } elseif ($loginMethod === 'sms') {
            $user->phone = $input['phone'];
        }

        $user->expiration_time    = now()->addMinutes($network->session_timeout ?? 60);
        $user->download_bandwidth = $network->download_limit;
        $user->upload_bandwidth   = $network->upload_limit;
        $user->save();

        // ── Write radcheck record ────────────────────────────────────────────
        Radcheck::updateOrCreateRecord($macAddress, 'Cleartext-Password', $macAddress, '==', [
            'network_id'         => $networkId,
            'download_bandwidth' => $user->download_bandwidth,
            'upload_bandwidth'   => $user->upload_bandwidth,
            'expiration_time'    => $user->expiration_time,
            'idle_timeout'       => $network->idle_timeout ?? 0,
        ]);

        // ── Build CoovaChilli CHAP login URL ─────────────────────────────────
        $challenge  = $input['challenge'];
        $uamsecret  = '';
        $username   = $password = $macAddress;

        Log::info("username::{$username}");

        $hexchal  = pack('H32', $challenge);
        $newchal  = pack('H*', md5($hexchal . $uamsecret));
        $response = md5("\0" . $password . $newchal);

        $redirectUrl      = $network->redirect_url ?? env('SOLUTION_URL');
        $loginRedirectUrl = 'http://' . $input['ip_address'] . ':3990/logon'
            . '?username=' . $username
            . '&response=' . $response
            . '&userurl=' . urlencode($redirectUrl);

        return response()->json([
            'success'      => true,
            'message'      => 'User logged in',
            'user'         => $user,
            'login_url'    => $loginRedirectUrl,
            'chap_response' => $response,
        ]);
    }

    public function store(Request $request) {}
    public function show(string $id) {}
    public function edit(string $id) {}
    public function update(Request $request, string $id) {}
    public function destroy(string $id) {}
}

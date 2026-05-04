<?php

namespace App\Http\Controllers;

use App\Mail\GuestOtpMail;
use App\Models\GuestNetworkUser;
use App\Models\Location;
use App\Models\LocationNetwork;
use App\Models\OtpVerification;
use App\Models\Radcheck;
use App\Models\UserDeviceLoginSession;
use App\Services\SmsService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Log;
use Validator;

class GuestNetworkUserController extends Controller
{
    /**
     * Display a listing of guest users for a location (admin UI).
     */
    public function index(Request $request, $location)
    {
        try {
            $locationModel = Location::find($location);

            if (! $locationModel) {
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
                'id' => $guest->id,
                'mac_address' => $guest->mac_address,
                'email' => $guest->email,
                'phone' => $guest->phone,
                'expiration_time' => $guest->expiration_time?->format('Y-m-d H:i:s'),
                'blocked' => $guest->blocked,
                'created_at' => $guest->created_at->format('Y-m-d H:i:s'),
            ]);

            return response()->json(['success' => true, 'data' => $guests, 'total' => $guests->count()]);
        } catch (\Exception $e) {
            Log::error('Error fetching guest users: '.$e->getMessage());

            return response()->json(['success' => false, 'message' => 'Error retrieving guest users: '.$e->getMessage()], 500);
        }
    }

    /**
     * Export guest users to CSV (matches Analytics guest table columns; optional search filter).
     */
    public function export(Request $request, $location)
    {
        try {
            $locationModel = Location::find($location);

            $user = Auth::guard('api')->user();
            if (! $locationModel || ! $user || ! $locationModel->isAccessibleBy($user)) {
                return response()->json(['success' => false, 'message' => 'Location not found'], 404);
            }

            $query = GuestNetworkUser::query()
                ->select('guest_network_users.*')
                ->selectSub(
                    UserDeviceLoginSession::query()
                        ->selectRaw('COUNT(*)')
                        ->whereColumn('guest_network_user_id', 'guest_network_users.id'),
                    'session_count'
                )
                ->selectSub(
                    UserDeviceLoginSession::query()
                        ->selectRaw('MAX(connect_time)')
                        ->whereColumn('guest_network_user_id', 'guest_network_users.id'),
                    'last_seen'
                )
                ->where('guest_network_users.location_id', $location);

            if ($request->filled('search')) {
                $search = trim((string) $request->search);
                $like = '%'.$search.'%';
                $query->where(function ($q) use ($like) {
                    $q->where('guest_network_users.name', 'like', $like)
                        ->orWhere('guest_network_users.mac_address', 'like', $like)
                        ->orWhere('guest_network_users.email', 'like', $like)
                        ->orWhere('guest_network_users.phone', 'like', $like);
                });
            }

            $guests = $query->orderByDesc('guest_network_users.id')->get();

            $filename = "location_{$location}_guests_".now()->format('Y-m-d_H-i-s').'.csv';

            $stream = fopen('php://temp', 'w+');
            fputcsv($stream, [
                'Name',
                'MAC Address',
                'Email',
                'Phone',
                'Device Type',
                'OS',
                'Sessions',
                'Last Seen',
                'Status',
            ]);

            foreach ($guests as $guest) {
                $lastSeen = $guest->last_seen
                    ? Carbon::parse($guest->last_seen)->format('Y-m-d H:i:s')
                    : '';
                $status = $guest->blocked ? 'Blocked' : 'Active';

                fputcsv($stream, [
                    $guest->name ?? '',
                    $guest->mac_address ?? '',
                    $guest->email ?? '',
                    $guest->phone ?? '',
                    $guest->device_type ?? '',
                    $guest->os ?? '',
                    (string) ((int) ($guest->session_count ?? 0)),
                    $lastSeen,
                    $status,
                ]);
            }

            rewind($stream);
            $content = stream_get_contents($stream);
            fclose($stream);

            return response($content)
                ->header('Content-Type', 'text/csv; charset=UTF-8')
                ->header('Content-Disposition', "attachment; filename=\"{$filename}\"");
        } catch (\Exception $e) {
            Log::error('Error exporting guest users: '.$e->getMessage());

            return response()->json(['success' => false, 'message' => 'Export failed: '.$e->getMessage()], 500);
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
            'network_id' => 'required|exists:location_networks,id',
            'mac_address' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        $network = LocationNetwork::with(['location', 'portalDesign'])->find($network_id);

        if (! $network || ! $network->location) {
            return response()->json(['success' => false, 'message' => 'Network not found'], 404);
        }

        $user = null;
        if (! empty($params['mac_address'])) {
            $user = GuestNetworkUser::where('network_id', $network_id)
                ->where('mac_address', $params['mac_address'])
                ->first();
        }

        // Resolve the ordered list of enabled auth methods. When auth_methods (array) is set
        // that takes precedence; otherwise fall back to the legacy single auth_method string.
        $authMethodsArray = $network->auth_methods ?? [$network->auth_method ?? 'click-through'];

        $captivePortalSettings = [
            'captive_portal_enabled' => $network->enabled,
            'captive_portal_ssid' => $network->ssid,
            'captive_portal_visible' => $network->visible,
            'captive_auth_method' => $network->auth_method,   // kept for backward compat
            'captive_auth_methods' => $authMethodsArray,       // multi-method array
            'email_require_otp' => $network->email_require_otp ?? true,
            'session_timeout' => $network->session_timeout,
            'idle_timeout' => $network->idle_timeout,
            'captive_portal_redirect' => $network->redirect_url,
            'captive_social_auth_method' => $network->social_auth_method,
            'download_limit' => $network->download_limit,
            'upload_limit' => $network->upload_limit,
        ];

        $captivePortalIp = '10.1.0.1'; // Router provides this via uamip query param

        $brand = [
            'name' => env('APP_BRAND_NAME'),
            'logo_url' => env('APP_BRAND_LOGO'),
            'welcome_message' => env('APP_BRAND_WELCOME_MESSAGE'),
            'terms_of_service_url' => env('APP_BRAND_TERMS_OF_SERVICE_URL'),
            'privacy_policy_url' => env('APP_BRAND_PRIVACY_POLICY_URL'),
        ];

        $locationData = [
            'id' => $network->id,           // network_id — used by JS for redirect URLs
            'location_id' => $network->location->id,
            'name' => $network->location->name,
            'description' => $network->location->description ?? null,
            'settings' => $captivePortalSettings,
            'design' => $network->portalDesign,
            'ip_address' => $captivePortalIp,
            'challenge' => bin2hex(random_bytes(16)),
        ];

        return response()->json([
            'success' => true,
            'message' => 'Network info retrieved',
            'location' => $locationData,
            'user' => $user,
            'brand' => $brand,
        ]);
    }

    /**
     * Request an SMS OTP — scoped to a specific network.
     */
    public function requestOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'network_id' => 'required|exists:location_networks,id',
            'phone' => 'required|string|max:20',
            'mac_address' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        $phone = $request->phone;
        $networkId = $request->network_id;
        $macAddress = $request->mac_address;

        $otpVerification = OtpVerification::generateOtp($phone, $networkId, $macAddress);

        $smsService = new SmsService;
        Log::info("Sending OTP {$otpVerification->otp} to {$phone}");
        $smsSent = $smsService->sendOtp($phone, $otpVerification->otp);

        if (! $smsSent) {
            return response()->json(['success' => false, 'message' => 'Failed to send OTP. Please try again later.'], 500);
        }

        return response()->json(['success' => true, 'message' => 'OTP sent successfully', 'expires_at' => $otpVerification->expires_at]);
    }

    /**
     * Request an email OTP — scoped to a specific network.
     */
    public function requestEmailOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'network_id' => 'required|exists:location_networks,id',
            'email' => 'required|email|max:255',
            'mac_address' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        $email = $request->email;
        $networkId = $request->network_id;
        $macAddress = $request->mac_address;

        // Re-use OtpVerification with email as the identifier (stored in the phone column)
        $otpVerification = OtpVerification::generateOtp($email, $networkId, $macAddress);

        $brandName = env('APP_BRAND_NAME', 'Monsieur WiFi');
        $locale = $request->input('locale', 'en');

        Log::info("Sending email OTP {$otpVerification->otp} to {$email}");

        try {
            Mail::to($email)->send(new GuestOtpMail($otpVerification->otp, $brandName, $locale));
        } catch (\Exception $e) {
            Log::error("Failed to send email OTP to {$email}: ".$e->getMessage());

            return response()->json(['success' => false, 'message' => 'Failed to send verification code. Please try again later.'], 500);
        }

        return response()->json(['success' => true, 'message' => 'Verification code sent', 'expires_at' => $otpVerification->expires_at]);
    }

    /**
     * Authenticate a guest device and return the CoovaChilli CHAP login URL.
     */
    public function login(Request $request)
    {
        $input = $request->all();
        Log::info($input);

        $validator = Validator::make($input, [
            'network_id' => 'required|exists:location_networks,id',
            'zone_id' => 'nullable|integer|min:0',
            'mac_address' => 'nullable|string|max:255',
            'login_method' => 'required|string|in:email,sms,social,click-through,password',
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:255',
            'social_platform' => 'nullable|string|max:255',
            'otp' => 'nullable|string|size:4',
            'challenge' => 'required|string|max:255',
            'ip_address' => 'required|string|max:255',
            'location_id' => 'nullable|integer|exists:locations,id',
            'os' => 'nullable|string|max:255',
            'device_type' => 'nullable|string|in:Phone,Tablet,Laptop,Other',
        ]);

        if ($validator->fails()) {
            Log::info('Validation failed', $validator->errors()->toArray());

            return response()->json(['success' => false, 'message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        $networkId = $input['network_id'];
        $zoneId = (int) ($input['zone_id'] ?? 0);
        $macAddress = $input['mac_address'];
        $loginMethod = $input['login_method'];

        // ── Method-specific pre-checks ───────────────────────────────────────
        if ($loginMethod === 'email') {
            if (empty($input['email'])) {
                return response()->json(['success' => false, 'message' => 'Email address is required'], 422);
            }
            $emailNetwork = LocationNetwork::find($networkId);
            $requireOtp = $emailNetwork ? ($emailNetwork->email_require_otp ?? true) : true;
            if ($requireOtp) {
                if (empty($input['otp'])) {
                    return response()->json(['success' => false, 'message' => 'Email and verification code are required'], 422);
                }
                if (! OtpVerification::verifyOtp($input['email'], $input['otp'], $networkId)) {
                    Log::info('Invalid or expired email OTP');

                    return response()->json(['success' => false, 'message' => 'Invalid or expired verification code'], 422);
                }
            }
        }

        if ($loginMethod === 'sms') {
            if (empty($input['phone']) || empty($input['otp'])) {
                return response()->json(['success' => false, 'message' => 'Phone and OTP are required'], 422);
            }
            if (! OtpVerification::verifyOtp($input['phone'], $input['otp'], $networkId)) {
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

        // Prefer the location_id sent by the client (derived from nas_id in loading.js);
        // fall back to the network's own location for legacy / direct-URL flows.
        $locationId = (int) ($input['location_id'] ?? $network->location_id);

        // ── Upsert guest user ────────────────────────────────────────────────
        $user = GuestNetworkUser::firstOrCreate(
            ['network_id' => $networkId, 'mac_address' => $macAddress],
            ['location_id' => $locationId, 'zone_id' => $zoneId, 'blocked' => false]
        );

        if ($loginMethod === 'email') {
            $user->email = $input['email'];
        } elseif ($loginMethod === 'sms') {
            $user->phone = $input['phone'];
        }

        // $user->expiration_time = now()->addMinutes($network->session_timeout ?? 60);
        $user->expiration_time = now('UTC')->addMinutes($network->session_timeout ?? 60);
        $user->download_bandwidth = $network->download_limit;
        $user->upload_bandwidth = $network->upload_limit;

        if (! empty($input['os'])) {
            $user->os = $input['os'];
        }
        if (! empty($input['device_type'])) {
            $user->device_type = $input['device_type'];
        }

        $user->save();

        if ($macAddress !== null && $macAddress !== '') {
            UserDeviceLoginSession::create([
                'guest_network_user_id' => $user->id,
                'mac_address' => $macAddress,
                'location_id' => $locationId,
                'network_id' => $networkId,
                'zone_id' => $zoneId,
                'login_type' => $this->mapGuestPortalLoginType($loginMethod, $input['social_platform'] ?? null),
            ]);
        }

        // ── Write radcheck record ────────────────────────────────────────────
        // Read bandwidth directly from the network to avoid any stale/null
        // values that may still be on the $user object from a previous session.
        Radcheck::updateOrCreateRecord($macAddress, 'Cleartext-Password', $macAddress, '==', [
            'network_id' => $networkId,
            'zone_id' => $zoneId,
            'download_bandwidth' => $network->download_limit,
            'upload_bandwidth' => $network->upload_limit,
            'expiration_time' => now('UTC')->addMinutes($network->session_timeout ?? 60), // $user->expiration_time,
            'idle_timeout' => $network->idle_timeout ?? 0,
        ]);

        // ── Build CoovaChilli CHAP login URL ─────────────────────────────────
        $challenge = $input['challenge'];
        $uamsecret = '';
        $username = $password = $macAddress;

        Log::info("username::{$username}");

        $hexchal = pack('H32', $challenge);
        $newchal = pack('H*', md5($hexchal.$uamsecret));
        $response = md5("\0".$password.$newchal);

        $redirectUrl = $network->redirect_url ?? env('SOLUTION_URL');
        $loginRedirectUrl = 'http://'.$input['ip_address'].':3990/logon'
            .'?username='.$username
            .'&response='.$response
            .'&userurl='.urlencode($redirectUrl);

        return response()->json([
            'success' => true,
            'message' => 'User logged in',
            'user' => $user,
            'login_url' => $loginRedirectUrl,
            'chap_response' => $response,
        ]);
    }

    /**
     * Stable labels for captive portal views (click-login, email-login, …).
     */
    protected function mapGuestPortalLoginType(string $loginMethod, ?string $socialPlatform): string
    {
        return match ($loginMethod) {
            'click-through' => 'click-login',
            'email' => 'email-login',
            'sms' => 'sms-login',
            'password' => 'password-login',
            'social' => match (strtolower((string) $socialPlatform)) {
                'google' => 'google-login',
                'facebook' => 'facebook-login',
                'twitter' => 'twitter-login',
                default => 'oauth-login',
            },
            default => 'unknown-login',
        };
    }

    public function store(Request $request) {}

    public function show(string $id) {}

    public function edit(string $id) {}

    public function update(Request $request, string $id) {}

    public function destroy(string $id) {}
}

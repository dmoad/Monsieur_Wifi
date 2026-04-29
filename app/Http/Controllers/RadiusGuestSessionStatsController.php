<?php

namespace App\Http\Controllers;

use App\Http\Requests\RadiusGuestSessionStatsRequest;
use App\Services\UserDeviceLoginSessionStatsService;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;
use Log;

class RadiusGuestSessionStatsController extends Controller
{
    public function __construct(
        protected UserDeviceLoginSessionStatsService $sessionStats
    ) {}

    /**
     * Ingest RADIUS accounting-derived stats for a captive-portal row in `user_device_login_sessions`.
     */
    public function store(RadiusGuestSessionStatsRequest $request): JsonResponse
    {
        $validated = $request->validated();
        Log::info('Validated data:===============================================>>>>>>> ');
        Log::info(json_encode($validated));

        $status = $this->sessionStats->normalizeStatus($validated['acct_status_type']);

        if ($status === null) {
            return response()->json([
                'success' => false,
                'message' => 'Unknown or unsupported Acct-Status-Type',
            ], 422);
        }

        $session = $this->sessionStats->findSession($validated);

        if ($session === null) {
            if ($status === 'interim') {
                return response()->json([
                    'success' => false,
                    'message' => 'UserDeviceLoginSession not found',
                ], 404);
            }
            // for start and end time we just use the current time
            if ($status === 'start') {
                $validated['acct_start_time'] = Carbon::now();
            }
            if ($status === 'stop') {
                $validated['acct_stop_time'] = Carbon::now();
            }
            // start or stop with no prior session: backfill a roaming row so accounting data is not lost
            $session = $this->sessionStats->createRoamingSession($validated);
        }

        $session = $this->sessionStats->applyStats($session, $status, $validated);

        return response()->json([
            'success' => true,
            'message' => 'OK',
            'data'    => [
                'id'                  => $session->id,
                'radius_session_id'   => $session->radius_session_id,
                'total_download'      => $session->total_download,
                'total_upload'        => $session->total_upload,
                'session_duration'    => $session->session_duration,
                'disconnect_time'     => $session->disconnect_time?->toIso8601String(),
            ],
        ]);
    }
}

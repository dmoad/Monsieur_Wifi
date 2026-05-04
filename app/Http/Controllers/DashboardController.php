<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\UserDeviceLoginSession;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // API routes are already protected with 'auth:api' middleware in routes/api.php
        // Web route for dashboard view doesn't need auth since it's handled on frontend
    }

    /**
     * Show the dashboard view (static)
     *
     * @return View
     */
    public function index()
    {
        return view('dashboard');
    }

    /**
     * Resolve locations visible to the authenticated user (same rules everywhere below).
     *
     * @return Collection<int, Location>
     */
    protected function locationsVisibleToUser($user)
    {
        if ($user->isAdminOrAbove()) {
            return Location::with('device')->get();
        }

        return Location::with('device')->where(function ($q) use ($user) {
            $q->where('owner_id', $user->id)
                ->orWhereJsonContains('shared_users', ['user_id' => $user->id]);
        })->get();
    }

    /**
     * When true, overview JSON includes debug_open_sessions_for_active_users (same rows counted for Active Users).
     */
    protected function shouldExposeActiveSessionsDebug(Request $request, $user): bool
    {
        if (config('app.debug')) {
            return true;
        }
        if (filter_var(env('DASHBOARD_DEBUG_ACTIVE_SESSIONS', false), FILTER_VALIDATE_BOOLEAN)) {
            return true;
        }
        if ($user->isAdminOrAbove() && $request->boolean('debug_active_sessions')) {
            return true;
        }

        return false;
    }

    /**
     * One debug row per MAC (newest open session kept); matches Active Users (distinct MAC,
     * connect within {@see UserDeviceLoginSession::ACTIVE_SESSION_MAX_CONNECT_AGE_HOURS}h).
     *
     * @param  array<int, int|string>  $locationIds
     * @return list<array<string, mixed>>
     */
    protected function activeSessionsCountedForOverview(array $locationIds): array
    {
        if ($locationIds === []) {
            return [];
        }

        return UserDeviceLoginSession::query()
            ->successful()
            ->forLocations($locationIds)
            ->openSessions()
            ->connectStartedWithinActiveWindow()
            ->with(['location:id,name'])
            ->orderByDesc('connect_time')
            ->orderByDesc('id')
            ->get()
            ->unique(fn (UserDeviceLoginSession $s) => UserDeviceLoginSession::normalizedMacKey($s->mac_address))
            ->sortBy(fn (UserDeviceLoginSession $s) => [$s->location_id, $s->id])
            ->values()
            ->map(static fn (UserDeviceLoginSession $s): array => [
                'id' => $s->id,
                'location_id' => $s->location_id,
                'location_name' => $s->location?->name,
                'guest_network_user_id' => $s->guest_network_user_id,
                'mac_address' => $s->mac_address,
                'login_type' => $s->login_type,
                'connect_time' => $s->connect_time?->toIso8601String(),
                'login_success' => (bool) $s->login_success,
                'disconnect_time' => $s->disconnect_time?->toIso8601String(),
            ])
            ->values()
            ->all();
    }

    /**
     * Get dashboard overview data via API
     *
     * @return JsonResponse
     */
    public function getOverview(Request $request)
    {
        $user = Auth::user();
        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated',
            ], 401);
        }
        try {
            $locations = $this->locationsVisibleToUser($user);
            $locationIds = $locations->pluck('id')->all();

            $todayStart = Carbon::now()->startOfDay();
            $todayEnd = Carbon::now()->endOfDay();

            $openCounts = UserDeviceLoginSession::openSessionCountsByLocation($locationIds);
            $todayStats = UserDeviceLoginSession::dayConnectStatsByLocation($locationIds, $todayStart, $todayEnd);

            $totalLocations = $locations->count();
            $onlineLocations = 0;
            $offlineLocations = 0;

            $locationsData = $locations->map(function ($location) use (&$onlineLocations, &$offlineLocations, $openCounts, $todayStats) {
                $locationData = $location->toArray();
                $locationData['online_status'] = 'offline';

                if ($location->device && $location->device->last_seen) {
                    $lastSeen = new \DateTime($location->device->last_seen);
                    $now = new \DateTime;
                    $interval = $now->getTimestamp() - $lastSeen->getTimestamp();

                    if ($interval <= 90) {
                        $locationData['online_status'] = 'online';
                        $onlineLocations++;
                    } else {
                        $offlineLocations++;
                    }
                } else {
                    $offlineLocations++;
                }

                $lid = $location->id;
                $locationData['users'] = (int) $openCounts->get($lid, 0);

                $dayRow = $todayStats->get($lid);
                $locationData['unique_users_today'] = $dayRow ? $dayRow->unique_users : 0;
                $locationData['data_usage_gb'] = $dayRow && $dayRow->bytes > 0
                    ? round($dayRow->bytes / (1024 ** 3), 2)
                    : 0;
                $locationData['total_sessions'] = $dayRow ? $dayRow->total_sessions : 0;
                $locationData['device'] = $location->device;

                return $locationData;
            });

            $totalActiveUsers = UserDeviceLoginSession::openDistinctMacCountForLocations($locationIds);

            $totalBytesToday = $todayStats->sum(fn ($row) => $row->bytes);
            $totalDataUsageGB = round($totalBytesToday / (1024 ** 3), 2);

            $uptimePercentage = $totalLocations > 0 ? round(($onlineLocations / $totalLocations) * 100, 1) : 0;

            $payload = [
                'locations' => [
                    'total' => $totalLocations,
                    'online' => $onlineLocations,
                    'offline' => $offlineLocations,
                    'data' => $locationsData,
                ],
                'network_stats' => [
                    'routers_online' => $onlineLocations,
                    'routers_total' => $totalLocations,
                    'active_users' => $totalActiveUsers,
                    'data_used_gb' => $totalDataUsageGB,
                    'data_used_tb' => round($totalDataUsageGB / 1024, 4),
                    'uptime_percentage' => $uptimePercentage,
                ],
            ];

            if ($this->shouldExposeActiveSessionsDebug($request, $user)) {
                $payload['debug_open_sessions_for_active_users'] = $this->activeSessionsCountedForOverview($locationIds);
            }

            return response()->json([
                'success' => true,
                'data' => $payload,
            ]);

        } catch (\Exception $e) {
            Log::error('Error loading dashboard overview data: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error loading dashboard data: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get network analytics data via API
     *
     * @return JsonResponse
     */
    public function getAnalytics(Request $request)
    {
        $user = Auth::user();
        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated',
            ], 401);
        }
        try {
            $period = $request->input('period', '7');

            $endDate = Carbon::now();
            switch ($period) {
                case '1':
                    $startDate = Carbon::now()->startOfDay();
                    break;
                case '7':
                    $startDate = Carbon::now()->subDays(7);
                    break;
                case '30':
                    $startDate = Carbon::now()->subDays(30);
                    break;
                case '90':
                    $startDate = Carbon::now()->subDays(90);
                    break;
                default:
                    $startDate = Carbon::now()->subDays(7);
            }

            $locations = $this->locationsVisibleToUser($user);
            $locationIds = $locations->pluck('id')->all();

            $agg = UserDeviceLoginSession::aggregatePeriodTotals($locationIds, $startDate, $endDate);

            $totalDataGb = round($agg->bytes / (1024 ** 3), 2);

            $onlineCount = 0;
            foreach ($locations as $location) {
                if ($location->device && $location->device->last_seen) {
                    $lastSeen = new \DateTime($location->device->last_seen);
                    $now = new \DateTime;
                    $interval = $now->getTimestamp() - $lastSeen->getTimestamp();

                    if ($interval <= 90) {
                        $onlineCount++;
                    }
                }
            }

            $uptime = $locations->count() > 0
                ? round(($onlineCount / $locations->count()) * 100, 1)
                : 0;

            return response()->json([
                'success' => true,
                'data' => [
                    'period' => $period,
                    'date_range' => [
                        'start' => $startDate->format('Y-m-d'),
                        'end' => $endDate->format('Y-m-d'),
                    ],
                    'analytics' => [
                        'total_users' => $agg->distinct_guests,
                        'data_usage_gb' => $totalDataGb,
                        'total_sessions' => $agg->sessions,
                        'uptime' => $uptime,
                    ],
                ],
            ]);

        } catch (\Exception $e) {
            Log::error('Error loading dashboard analytics data: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error loading analytics data: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get data usage trends from user_device_login_sessions (by connect_time).
     *
     * @return JsonResponse
     */
    public function getDataUsageTrends(Request $request)
    {
        $user = Auth::user();
        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated',
            ], 401);
        }
        try {
            $period = $request->input('period', '7');

            $endDate = Carbon::now();
            switch ($period) {
                case '7':
                    $startDate = Carbon::now()->subDays(6);
                    break;
                case '30':
                    $startDate = Carbon::now()->subDays(29);
                    break;
                case '365':
                    $startDate = Carbon::now()->subDays(364);
                    break;
                default:
                    $startDate = Carbon::now()->subDays(6);
            }

            $locations = $user->isAdminOrAbove()
                ? Location::all()
                : Location::where(function ($q) use ($user) {
                    $q->where('owner_id', $user->id)
                        ->orWhereJsonContains('shared_users', ['user_id' => $user->id]);
                })->get();

            $locationIds = $locations->pluck('id')->all();

            $byDay = UserDeviceLoginSession::dailyDownloadUploadBytesByCalendarDay($locationIds, $startDate, $endDate);

            $dailyUsage = [];
            $totalDownloadGB = 0;
            $totalUploadGB = 0;

            $currentDate = $startDate->copy()->startOfDay();
            $endDay = $endDate->copy()->startOfDay();

            while ($currentDate->lte($endDay)) {
                $key = $currentDate->format('Y-m-d');
                $row = $byDay->get($key);

                $dlBytes = $row ? $row->download_bytes : 0;
                $ulBytes = $row ? $row->upload_bytes : 0;

                $dayDownloadGB = round($dlBytes / (1024 ** 3), 2);
                $dayUploadGB = round($ulBytes / (1024 ** 3), 2);

                $dailyUsage[] = [
                    'date' => $key,
                    'download_gb' => $dayDownloadGB,
                    'upload_gb' => $dayUploadGB,
                ];

                $totalDownloadGB += $dayDownloadGB;
                $totalUploadGB += $dayUploadGB;

                $currentDate->addDay();
            }

            $totalUsageGB = $totalDownloadGB + $totalUploadGB;

            return response()->json([
                'success' => true,
                'data' => [
                    'period' => $period,
                    'date_range' => [
                        'start' => $startDate->format('Y-m-d'),
                        'end' => $endDate->format('Y-m-d'),
                    ],
                    'total_usage_gb' => round($totalUsageGB, 2),
                    'total_download_gb' => round($totalDownloadGB, 2),
                    'total_upload_gb' => round($totalUploadGB, 2),
                    'daily_usage' => $dailyUsage,
                ],
            ]);

        } catch (\Exception $e) {
            Log::error('Error loading data usage trends: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error loading data usage trends: '.$e->getMessage(),
            ], 500);
        }
    }
}

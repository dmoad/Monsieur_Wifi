<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class UserDeviceLoginSession extends Model
{
    protected $table = 'user_device_login_sessions';

    protected $fillable = [
        'guest_network_user_id',
        'mac_address',
        'location_id',
        'network_id',
        'zone_id',
        'download_data',
        'upload_data',
        'login_type',
        'radius_session_id',
        'connect_time',
        'disconnect_time',
        'total_download',
        'total_upload',
        'session_duration',
        'login_success',
    ];

    protected $casts = [
        'zone_id' => 'integer',
        'network_id' => 'integer',
        'connect_time' => 'datetime',
        'disconnect_time' => 'datetime',
        'login_success' => 'boolean',
    ];

    public function guestNetworkUser(): BelongsTo
    {
        return $this->belongsTo(GuestNetworkUser::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function network(): BelongsTo
    {
        return $this->belongsTo(LocationNetwork::class);
    }

    /** @param  array<int, int|string>  $locationIds */
    public function scopeForLocations(Builder $query, array $locationIds): Builder
    {
        if ($locationIds === []) {
            return $query->whereRaw('0 = 1');
        }

        return $query->whereIn('location_id', $locationIds);
    }

    public function scopeSuccessful(Builder $query): Builder
    {
        return $query->where('login_success', true);
    }

    public function scopeOpenSessions(Builder $query): Builder
    {
        return $query->whereNull('disconnect_time');
    }

    /**
     * Open sessions considered “active” must have started within this many hours (rolling window).
     */
    public const ACTIVE_SESSION_MAX_CONNECT_AGE_HOURS = 24;

    /** Exclude sessions whose connect_time is older than the active-user rolling window. */
    public function scopeConnectStartedWithinActiveWindow(Builder $query): Builder
    {
        return $query->where(
            'connect_time',
            '>',
            Carbon::now()->subHours(self::ACTIVE_SESSION_MAX_CONNECT_AGE_HOURS)
        );
    }

    public function scopeConnectedBetween(Builder $query, Carbon $start, Carbon $end): Builder
    {
        return $query->whereBetween('connect_time', [$start, $end]);
    }

    /** Calendar day key expression for GROUP BY (MySQL DATE vs SQLite strftime). */
    protected static function calendarDaySql(string $column = 'connect_time'): string
    {
        return match (DB::connection()->getDriverName()) {
            'sqlite' => "strftime('%Y-%m-%d', {$column})",
            default => "DATE({$column})",
        };
    }

    /**
     * Normalize MAC for dedupe keys (hex only, lowercased). Matches {@see normalizedMacSql} for typical stored formats.
     */
    public static function normalizedMacKey(?string $mac): string
    {
        if ($mac === null || $mac === '') {
            return '';
        }

        return strtolower((string) preg_replace('/[^a-fA-F0-9]/', '', $mac));
    }

    /**
     * SQL expression comparable to {@see normalizedMacKey}: strip common separators and lowercase.
     */
    protected static function normalizedMacSql(string $column = 'mac_address'): string
    {
        return "REPLACE(REPLACE(REPLACE(LOWER(TRIM({$column})), ':', ''), '-', ''), '.', '')";
    }

    /**
     * Successful open sessions per location_id (disconnect_time IS NULL), distinct by MAC,
     * connect_time within {@see ACTIVE_SESSION_MAX_CONNECT_AGE_HOURS}.
     *
     * @param  array<int, int|string>  $locationIds
     * @return Collection<string|int, int> location_id => count
     */
    public static function openSessionCountsByLocation(array $locationIds): Collection
    {
        if ($locationIds === []) {
            return collect();
        }

        $macExpr = static::normalizedMacSql('mac_address');

        return static::query()
            ->successful()
            ->forLocations($locationIds)
            ->openSessions()
            ->connectStartedWithinActiveWindow()
            ->selectRaw("location_id, COUNT(DISTINCT {$macExpr}) as c")
            ->groupBy('location_id')
            ->pluck('c', 'location_id');
    }

    /**
     * Distinct MAC count across locations for open successful sessions (same device never counted twice),
     * connect_time within {@see ACTIVE_SESSION_MAX_CONNECT_AGE_HOURS}.
     *
     * @param  array<int, int|string>  $locationIds
     */
    public static function openDistinctMacCountForLocations(array $locationIds): int
    {
        if ($locationIds === []) {
            return 0;
        }

        $macExpr = static::normalizedMacSql('mac_address');

        return (int) static::query()
            ->successful()
            ->forLocations($locationIds)
            ->openSessions()
            ->connectStartedWithinActiveWindow()
            ->selectRaw("COUNT(DISTINCT {$macExpr}) as cnt")
            ->value('cnt');
    }

    /**
     * Stats for sessions whose connect_time falls in [dayStart, dayEnd], grouped by location.
     *
     * @param  array<int, int|string>  $locationIds
     * @return Collection<string|int, object{total_sessions:int, unique_users:int, bytes:int}>
     */
    public static function dayConnectStatsByLocation(array $locationIds, Carbon $dayStart, Carbon $dayEnd): Collection
    {
        if ($locationIds === []) {
            return collect();
        }

        return static::query()
            ->successful()
            ->forLocations($locationIds)
            ->connectedBetween($dayStart, $dayEnd)
            ->selectRaw(
                'location_id, COUNT(*) as total_sessions, COUNT(DISTINCT guest_network_user_id) as unique_users, '.
                'SUM(COALESCE(total_download, 0) + COALESCE(total_upload, 0)) as bytes'
            )
            ->groupBy('location_id')
            ->get()
            ->keyBy('location_id')
            ->map(fn ($row) => (object) [
                'total_sessions' => (int) $row->total_sessions,
                'unique_users' => (int) $row->unique_users,
                'bytes' => (int) $row->bytes,
            ]);
    }

    /**
     * Single aggregate row for dashboard analytics period (all locations).
     *
     * @param  array<int, int|string>  $locationIds
     * @return object{sessions: int, distinct_guests: int, bytes: int}
     */
    public static function aggregatePeriodTotals(array $locationIds, Carbon $start, Carbon $end): object
    {
        if ($locationIds === []) {
            return (object) ['sessions' => 0, 'distinct_guests' => 0, 'bytes' => 0];
        }

        $row = static::query()
            ->successful()
            ->forLocations($locationIds)
            ->connectedBetween($start, $end)
            ->selectRaw(
                'COUNT(*) as sessions, COUNT(DISTINCT guest_network_user_id) as distinct_guests, '.
                'SUM(COALESCE(total_download, 0) + COALESCE(total_upload, 0)) as bytes'
            )
            ->first();

        if ($row === null) {
            return (object) ['sessions' => 0, 'distinct_guests' => 0, 'bytes' => 0];
        }

        return (object) [
            'sessions' => (int) ($row->sessions ?? 0),
            'distinct_guests' => (int) ($row->distinct_guests ?? 0),
            'bytes' => (int) ($row->bytes ?? 0),
        ];
    }

    /**
     * Daily download/upload totals (GB) keyed by Y-m-d for chart fill-in.
     *
     * @param  array<int, int|string>  $locationIds
     * @return Collection<string, object{download_bytes:int, upload_bytes:int}> date => sums
     */
    public static function dailyDownloadUploadBytesByCalendarDay(array $locationIds, Carbon $rangeStart, Carbon $rangeEnd): Collection
    {
        if ($locationIds === []) {
            return collect();
        }

        $start = $rangeStart->copy()->startOfDay();
        $end = $rangeEnd->copy()->endOfDay();

        $dayExpr = static::calendarDaySql('connect_time');

        return static::query()
            ->successful()
            ->forLocations($locationIds)
            ->connectedBetween($start, $end)
            ->selectRaw(
                "{$dayExpr} as day, ".
                'SUM(COALESCE(total_download, 0)) as download_bytes, '.
                'SUM(COALESCE(total_upload, 0)) as upload_bytes'
            )
            ->groupByRaw($dayExpr)
            ->orderBy('day')
            ->get()
            ->keyBy('day')
            ->map(fn ($row) => (object) [
                'download_bytes' => (int) $row->download_bytes,
                'upload_bytes' => (int) $row->upload_bytes,
            ]);
    }
}

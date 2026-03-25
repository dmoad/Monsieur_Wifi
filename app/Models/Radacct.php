<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class Radacct extends Model
{
    use HasFactory;

    /**
     * The connection name for the model.
     *
     * @var string
     */
    protected $connection = 'radius';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'radacct';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'radacctid';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'acctsessionid',
        'acctuniqueid',
        'username',
        'realm',
        'nasipaddress',
        'nasportid',
        'nasporttype',
        'acctstarttime',
        'acctupdatetime',
        'acctstoptime',
        'acctinterval',
        'acctsessiontime',
        'acctauthentic',
        'connectinfo_start',
        'connectinfo_stop',
        'acctinputoctets',
        'acctoutputoctets',
        'calledstationid',
        'callingstationid',
        'acctterminatecause',
        'servicetype',
        'framedprotocol',
        'framedipaddress',
        'framedipv6address',
        'framedipv6prefix',
        'framedinterfaceid',
        'delegatedipv6prefix',
        'class',
        'location_id',
        'zone_id',
        'network_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'acctstarttime' => 'datetime',
        'acctupdatetime' => 'datetime',
        'acctstoptime' => 'datetime',
        'acctinputoctets' => 'integer',
        'acctoutputoctets' => 'integer',
        'acctsessiontime' => 'integer',
        'acctinterval' => 'integer',
        'location_id' => 'integer',
        'zone_id'     => 'integer',
        'network_id'  => 'integer',
    ];

    /**
     * Get the location associated with this accounting record.
     */
    public function location(): BelongsTo
    {
        return $this->setConnection('mysql')->belongsTo(Location::class, 'location_id');
    }

    /**
     * Get accounting records by username
     *
     * @param string $username
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getByUsername(string $username): Collection
    {
        return self::where('username', $username)->get();
    }

    /**
     * Get accounting records by username and location
     *
     * @param string $username
     * @param int $locationId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getByUsernameAndLocation(string $username, int $locationId): Collection
    {
        return self::where('username', $username)
            ->where('location_id', $locationId)
            ->get();
    }

    /**
     * Get accounting records for a location
     *
     * @param int $locationId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getByLocation(int $locationId): Collection
    {
        return self::where('location_id', $locationId)->get();
    }

    /**
     * Get accounting records for a network.
     */
    public static function getByNetwork(int $networkId): Collection
    {
        return self::where('network_id', $networkId)->get();
    }

    /**
     * Get accounting records for a zone.
     */
    public static function getByZone(int $zoneId): Collection
    {
        return self::where('zone_id', $zoneId)->get();
    }

    /**
     * Get accounting records for a specific network within a zone.
     */
    public static function getByNetworkAndZone(int $networkId, int $zoneId): Collection
    {
        return self::where('network_id', $networkId)
            ->where('zone_id', $zoneId)
            ->get();
    }

    /**
     * Get total data usage for a user
     *
     * @param string $username
     * @param int|null $locationId
     * @return array
     */
    public static function getUserDataUsage(string $username, ?int $locationId = null): array
    {
        $query = self::where('username', $username);
        
        if ($locationId) {
            $query->where('location_id', $locationId);
        }
        
        $records = $query->get();
        
        $totalInput = $records->sum('acctinputoctets');
        $totalOutput = $records->sum('acctoutputoctets');
        $totalBytes = $totalInput + $totalOutput;
        
        return [
            'total_bytes' => $totalBytes,
            'total_input_bytes' => $totalInput,
            'total_output_bytes' => $totalOutput,
            'total_mb' => round($totalBytes / (1024 * 1024), 2),
            'total_gb' => round($totalBytes / (1024 * 1024 * 1024), 2),
            'sessions_count' => $records->count()
        ];
    }

    /**
     * Get total data usage for a location
     *
     * @param int $locationId
     * @param Carbon|null $startDate
     * @param Carbon|null $endDate
     * @return array
     */
    public static function getLocationDataUsage(int $locationId, ?Carbon $startDate = null, ?Carbon $endDate = null): array
    {
        $query = self::where('location_id', $locationId);
        
        if ($startDate) {
            $query->where('acctstarttime', '>=', $startDate);
        }
        
        // If endDate is null, default to current time to avoid unbounded queries
        if ($endDate) {
            $query->where('acctstarttime', '<=', $endDate->endOfDay());
        } else {
            $query->where('acctstarttime', '<=', Carbon::now()->endOfDay());
        }
        
        $records = $query->get();
        
        $totalInput = $records->sum('acctinputoctets');
        $totalOutput = $records->sum('acctoutputoctets');
        $totalBytes = $totalInput + $totalOutput;
        $uniqueUsers = $records->pluck('username')->unique()->count();
        $totalSessionTime = $records->sum('acctsessiontime');
        
        return [
            'total_bytes' => $totalBytes,
            'total_input_bytes' => $totalInput,
            'total_output_bytes' => $totalOutput,
            'total_mb' => round($totalBytes / (1024 * 1024), 2),
            'total_gb' => round($totalBytes / (1024 * 1024 * 1024), 2),
            'unique_users' => $uniqueUsers,
            'total_sessions' => $records->count(),
            'total_session_time_seconds' => $totalSessionTime,
            'total_session_time_hours' => round($totalSessionTime / 3600, 2)
        ];
    }

    /**
     * Get active sessions for a location
     *
     * @param int $locationId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getActiveSessions(int $locationId): Collection
    {
        return self::where('location_id', $locationId)
            ->whereNull('acctstoptime')
            ->orderBy('acctstarttime', 'desc')
            ->get();
    }

    /**
     * Get recent sessions for a location
     *
     * @param int $locationId
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getRecentSessions(int $locationId, int $limit = 50): Collection
    {
        return self::where('location_id', $locationId)
            ->orderBy('acctstarttime', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get session statistics for a date range
     *
     * @param int $locationId
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @return array
     */
    public static function getSessionStats(int $locationId, Carbon $startDate, Carbon $endDate): array
    {
        $sessions = self::where('location_id', $locationId)
            ->whereBetween('acctstarttime', [$startDate, $endDate])
            ->get();
        
        $dailyStats = [];
        $current = $startDate->copy();
        
        while ($current <= $endDate) {
            $dayStart = $current->copy()->startOfDay();
            $dayEnd = $current->copy()->endOfDay();
            
            $daySessions = $sessions->whereBetween('acctstarttime', [$dayStart, $dayEnd]);
            
            $dailyStats[$current->format('Y-m-d')] = [
                'date' => $current->format('Y-m-d'),
                'sessions' => $daySessions->count(),
                'unique_users' => $daySessions->pluck('username')->unique()->count(),
                'total_bytes' => $daySessions->sum(function($session) {
                    return ($session->acctinputoctets ?? 0) + ($session->acctoutputoctets ?? 0);
                }),
                'total_session_time' => $daySessions->sum('acctsessiontime')
            ];
            
            $current->addDay();
        }
        
        return $dailyStats;
    }

    /**
     * Calculate total bytes transferred (upload + download)
     *
     * @return int
     */
    public function getTotalBytesAttribute(): int
    {
        return ($this->acctinputoctets ?? 0) + ($this->acctoutputoctets ?? 0);
    }

    /**
     * Get formatted total data usage
     *
     * @return string
     */
    public function getFormattedDataUsageAttribute(): string
    {
        $bytes = $this->total_bytes;
        
        if ($bytes >= 1073741824) {
            return round($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return round($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return round($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' B';
        }
    }

    /**
     * Get formatted session duration
     *
     * @return string
     */
    public function getFormattedSessionTimeAttribute(): string
    {
        if (!$this->acctsessiontime) {
            return '0m';
        }
        
        $seconds = $this->acctsessiontime;
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $remainingSeconds = $seconds % 60;
        
        if ($hours > 0) {
            return sprintf('%dh %dm', $hours, $minutes);
        } elseif ($minutes > 0) {
            return sprintf('%dm %ds', $minutes, $remainingSeconds);
        } else {
            return sprintf('%ds', $remainingSeconds);
        }
    }
} 
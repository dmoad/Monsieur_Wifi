<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Radcheck extends Model
{
    use HasFactory;

    protected $connection = 'radius';

    protected $table = 'radcheck';

    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = [
        'username',
        'attribute',
        'op',
        'value',
        'network_id',
        'zone_id',
        'download_bandwidth',
        'upload_bandwidth',
        'expiration_time',
        'idle_timeout',
        'access_control',
    ];

    protected $casts = [
        'expiration_time' => 'datetime',
    ];

    const ACCESS_CONTROL_NONE        = 'none';
    const ACCESS_CONTROL_WHITELISTED = 'whitelisted';
    const ACCESS_CONTROL_BLACKLISTED = 'blacklisted';

    public static function getAccessControlOptions(): array
    {
        return [
            self::ACCESS_CONTROL_NONE        => 'None',
            self::ACCESS_CONTROL_WHITELISTED => 'Whitelisted',
            self::ACCESS_CONTROL_BLACKLISTED => 'Blacklisted',
        ];
    }

    public function isWhitelisted(): bool
    {
        return $this->access_control === self::ACCESS_CONTROL_WHITELISTED;
    }

    public function isBlacklisted(): bool
    {
        return $this->access_control === self::ACCESS_CONTROL_BLACKLISTED;
    }

    public function hasNoAccessControl(): bool
    {
        return $this->access_control === self::ACCESS_CONTROL_NONE;
    }

    public function scopeAccessControl($query, string $accessControl)
    {
        return $query->where('access_control', $accessControl);
    }

    public function scopeWhitelisted($query)
    {
        return $query->accessControl(self::ACCESS_CONTROL_WHITELISTED);
    }

    public function scopeBlacklisted($query)
    {
        return $query->accessControl(self::ACCESS_CONTROL_BLACKLISTED);
    }

    public static function getByUsername(string $username)
    {
        return self::where('username', $username)->get();
    }

    public static function getByUsernameAndNetwork(string $username, int $networkId)
    {
        return self::where('username', $username)
            ->where('network_id', $networkId)
            ->get();
    }

    /**
     * Scope: username + network + expiration window.
     * Mirrors the index (username, network_id, expiration_time) for maximum performance.
     *
     * Matches the primary query:
     *   SELECT * FROM radcheck WHERE username=? AND network_id=? AND expiration_time > NOW()+30s
     *
     * Usage:
     *   Radcheck::activeForUser($username, $networkId)->get();
     *   Radcheck::activeForUser($username, $networkId, 60)->first();
     *
     * @param  string  $username
     * @param  int     $networkId
     * @param  int     $bufferSeconds  only rows expiring further than this many seconds from now are returned
     */
    public function scopeActiveForUser($query, string $username, int $networkId, int $bufferSeconds = 30)
    {
        return $query->where('username', $username)
                     ->where('network_id', $networkId)
                     ->where('expiration_time', '>', now()->addSeconds($bufferSeconds));
    }

    /**
     * Scope: network + expiration window (no username filter).
     * Uses the (username, network_id, expiration_time) index via partial prefix.
     *
     * @param  int  $networkId
     * @param  int  $bufferSeconds
     */
    public function scopeActiveInNetwork($query, int $networkId, int $bufferSeconds = 30)
    {
        return $query->where('network_id', $networkId)
                     ->where('expiration_time', '>', now()->addSeconds($bufferSeconds));
    }

    /**
     * Update or create a radcheck record.
     * Pass `network_id` in $additional to scope the upsert to a specific network.
     */
    public static function updateOrCreateRecord(
        string $username,
        string $attribute,
        string $value,
        string $op = '==',
        array $additional = []
    ): self {
        $data = array_merge(['value' => $value, 'op' => $op], $additional);

        $conditions = ['username' => $username, 'attribute' => $attribute];

        if (isset($additional['network_id'])) {
            $conditions['network_id'] = $additional['network_id'];
        }

        return self::updateOrCreate($conditions, $data);
    }

    public static function deleteByUsername(string $username): int
    {
        return self::where('username', $username)->delete();
    }

    public static function deleteByUsernameAndNetwork(string $username, int $networkId): int
    {
        return self::where('username', $username)
            ->where('network_id', $networkId)
            ->delete();
    }
}

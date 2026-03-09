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

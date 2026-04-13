<?php

namespace App\Models;

use App\Models\Device;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Zone extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'owner_id',
        'primary_location_id',
        'is_active',
        'roaming_enabled',
        'shared_users',
    ];

    protected $casts = [
        'is_active'       => 'boolean',
        'roaming_enabled' => 'boolean',
        'shared_users'    => 'array',
    ];

    protected $appends = [
        'location_count',
    ];

    /**
     * Get the owner of the zone.
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * Get all locations in this zone.
     */
    public function locations()
    {
        return $this->hasMany(Location::class);
    }

    /**
     * Increment configuration_version on every device attached to locations in this zone
     * so routers pull updated settings (e.g. after roaming toggle changes).
     */
    public function bumpConfigurationVersionForAllDevices(): void
    {
        $deviceIds = $this->locations()
            ->whereNotNull('device_id')
            ->pluck('device_id')
            ->unique();

        if ($deviceIds->isEmpty()) {
            return;
        }

        Device::whereIn('id', $deviceIds)->update([
            'configuration_version' => DB::raw('COALESCE(configuration_version, 0) + 1'),
        ]);
    }

    /**
     * Get the primary location for this zone.
     */
    public function primaryLocation()
    {
        return $this->belongsTo(Location::class, 'primary_location_id');
    }

    /**
     * Get the primary location with settings loaded.
     */
    public function getPrimaryLocation()
    {
        return $this->primaryLocation()->with('settings')->first();
    }

    /**
     * Get the count of locations in this zone.
     */
    public function getLocationCountAttribute()
    {
        return $this->locations()->count();
    }

    /**
     * Validate that the primary location is in the zone.
     */
    public function syncPrimaryLocationSettings()
    {
        if ($this->primary_location_id) {
            $primaryLocation = Location::find($this->primary_location_id);
            
            if (!$primaryLocation || $primaryLocation->zone_id !== $this->id) {
                $this->primary_location_id = null;
                $this->save();
                return false;
            }
        }
        
        return true;
    }

    /**
     * Set a location as the primary for this zone.
     */
    public function setPrimary(Location $location)
    {
        if ($location->zone_id !== $this->id) {
            throw new \Exception('Location must be in the zone to be set as primary.');
        }

        $this->primary_location_id = $location->id;
        $this->save();

        return true;
    }

    /**
     * Check if this zone has a primary location set.
     */
    public function hasPrimary()
    {
        return !is_null($this->primary_location_id);
    }

    /**
     * Scope to filter active zones.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Return the shared_users array, always as an array (never null).
     */
    public function sharedUsers(): array
    {
        return $this->shared_users ?? [];
    }

    /**
     * Check if the given user may access this zone.
     * Admins and superadmins always pass. For other roles the user must be
     * the owner or appear in the shared_users JSON array.
     */
    public function isAccessibleBy(User $user): bool
    {
        if (in_array($user->role, ['admin', 'superadmin'])) {
            return true;
        }

        if ((int) $user->id === (int) $this->owner_id) {
            return true;
        }

        foreach ($this->sharedUsers() as $entry) {
            if ((int) ($entry['user_id'] ?? 0) === (int) $user->id) {
                return true;
            }
        }

        return false;
    }

    /**
     * Scope to filter zones by owner.
     */
    public function scopeOwnedBy($query, $userId)
    {
        return $query->where('owner_id', $userId);
    }
}

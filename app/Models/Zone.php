<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Zone extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'owner_id',
        'organization_id',
        'primary_location_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
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

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Get all locations in this zone.
     */
    public function locations()
    {
        return $this->hasMany(Location::class);
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
     * Scope to filter zones by owner.
     */
    public function scopeOwnedBy($query, $userId)
    {
        return $query->where('owner_id', $userId);
    }
}

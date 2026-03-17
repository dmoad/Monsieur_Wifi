<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\LocationSettingsV2;
use App\Models\LocationNetwork;
use App\Models\Radacct;
class Location extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'address',
        'city',
        'state',
        'country',
        'postal_code',
        'latitude',
        'longitude',
        'description',
        'manager_name',
        'contact_email',
        'contact_phone',
        'status',
        'device_id',
        'user_id',
        'owner_id',
        'organization_id',
        'zone_id',
    ];

    /**
     * Get the device associated with the location.
     */
    public function device()
    {
        return $this->belongsTo(Device::class);
    }

    /**
     * Get the user that manages this location.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function settings()
    {
        return $this->hasOne(LocationSettingsV2::class);
    }

    public function networks()
    {
        return $this->hasMany(LocationNetwork::class)->orderBy('sort_order');
    }

    /**
     * Get the accounting records for this location.
     */
    public function radacct()
    {
        return $this->setConnection('radius')->hasMany(Radacct::class, 'location_id');
    }

    /**
     * Get active sessions for this location.
     */
    public function activeSessions()
    {
        return $this->setConnection('radius')->hasMany(Radacct::class, 'location_id')
            ->whereNull('acctstoptime')
            ->orderBy('acctstarttime', 'desc');
    }

    /**
     * Get the owner of this location.
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * Get the zone this location belongs to.
     */
    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function zone()
    {
        return $this->belongsTo(Zone::class);
    }

    /**
     * Get the effective settings for this location.
     * If in a zone and not primary, returns primary location's settings.
     * Otherwise, returns own settings.
     */
    public function getEffectiveSettings()
    {
        if ($this->zone_id && !$this->isPrimaryInZone()) {
            $zone = $this->zone()->with('primaryLocation.settings')->first();
            if ($zone && $zone->primaryLocation && $zone->primaryLocation->settings) {
                return $zone->primaryLocation->settings;
            }
        }
        
        return $this->settings;
    }

    /**
     * Check if this location is the primary location in its zone.
     */
    public function isPrimaryInZone()
    {
        if (!$this->zone_id) {
            return false;
        }

        $zone = $this->zone;
        return $zone && $zone->primary_location_id === $this->id;
    }

    /**
     * Check if this location can edit its own settings.
     * Returns false if in a zone and not the primary location.
     */
    public function canEditSettings()
    {
        if (!$this->zone_id) {
            return true;
        }

        return $this->isPrimaryInZone();
    }

    /**
     * Decouple this location from its zone.
     */
    public function decoupleFromZone()
    {
        if ($this->zone_id) {
            // If this is the primary location, clear the zone's primary
            $zone = $this->zone;
            if ($zone && $zone->primary_location_id === $this->id) {
                $zone->primary_location_id = null;
                $zone->save();
            }

            $this->zone_id = null;
            $this->save();

            return true;
        }

        return false;
    }
}

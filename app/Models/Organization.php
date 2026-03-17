<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Organization extends Model
{
    protected $fillable = ['name', 'slug', 'owner_id', 'plan', 'features_override', 'plan_metadata'];

    protected $casts = [
        'features_override' => 'array',
        'plan_metadata'     => 'array',
    ];

    protected static function booted(): void
    {
        static::creating(function (Organization $org) {
            if (empty($org->slug)) {
                $org->slug = Str::slug($org->name) . '-' . Str::random(6);
            }
        });
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function locations()
    {
        return $this->hasMany(Location::class);
    }

    public function zones()
    {
        return $this->hasMany(Zone::class);
    }

    public function devices()
    {
        return $this->hasMany(Device::class);
    }

    /**
     * Get the plan config for this org.
     */
    public function getPlanConfig(): array
    {
        return config("plans.{$this->plan}", config('plans.free'));
    }

    /**
     * Get effective limits (plan defaults merged with per-org overrides).
     */
    public function getLimits(): array
    {
        $planConfig = $this->getPlanConfig();
        $limits = $planConfig['limits'] ?? [];

        // plan_metadata can override specific limits for managed orgs
        if ($this->plan_metadata && isset($this->plan_metadata['limits'])) {
            $limits = array_merge($limits, $this->plan_metadata['limits']);
        }

        return $limits;
    }

    /**
     * Get effective plan features (plan defaults + per-org overrides).
     */
    public function getPlanFeatures(): array
    {
        $planConfig = $this->getPlanConfig();
        $features = $planConfig['features'] ?? [];

        // features_override adds extra features for managed/custom orgs
        if ($this->features_override) {
            $features = array_unique(array_merge($features, $this->features_override));
        }

        return array_values($features);
    }

    /**
     * Check if the org has reached a limit. Returns true if within limit.
     */
    public function withinLimit(string $limitKey, int $currentCount): bool
    {
        $limits = $this->getLimits();
        $max = $limits[$limitKey] ?? 0;

        return $max === -1 || $currentCount < $max; // -1 = unlimited
    }
}

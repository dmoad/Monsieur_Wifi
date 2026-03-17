<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Cashier\Billable;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, Billable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'zitadel_sub',
        'profile_picture',
        'role',
        'email_verified_at',
        'current_organization_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get all captive portal designs created by this user
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function captivePortalDesigns()
    {
        return $this->hasMany(CaptivePortalDesign::class);
    }

    /**
     * Get all captive portal designs owned by this user
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function ownedCaptivePortalDesigns()
    {
        return $this->hasMany(CaptivePortalDesign::class, 'owner_id');
    }

    /**
     * Get all locations owned by this user
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function devices()
    {
        return $this->hasMany(Device::class, 'owner_id');
    }

    public function locations()
    {
        return $this->hasMany(Location::class, 'owner_id');
    }

    public function ownedLocations()
    {
        return $this->hasMany(Location::class, 'owner_id');
    }

    public function currentOrganization()
    {
        return $this->belongsTo(Organization::class, 'current_organization_id');
    }

    public function ownedOrganizations()
    {
        return $this->hasMany(Organization::class, 'owner_id');
    }
}

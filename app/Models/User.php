<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Laravel\Cashier\Billable;

class User extends Authenticatable implements JWTSubject, MustVerifyEmail
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
        'profile_picture',
        'role',
        'email_verified_at',
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
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
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
    public function ownedLocations()
    {
        return $this->hasMany(Location::class, 'owner_id');
    }

    /**
     * Check if user is a superadmin
     *
     * @return bool
     */
    public function isSuperAdmin(): bool
    {
        return $this->role === 'superadmin';
    }

    /**
     * Check if user is an admin
     *
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is admin or superadmin
     *
     * @return bool
     */
    public function isAdminOrAbove(): bool
    {
        return in_array($this->role, ['admin', 'superadmin']);
    }

    /**
     * Check if user has a specific role
     *
     * @param string $role
     * @return bool
     */
    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }
}

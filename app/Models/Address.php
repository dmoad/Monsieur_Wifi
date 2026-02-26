<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'first_name',
        'last_name',
        'company',
        'address_line1',
        'address_line2',
        'city',
        'province',
        'postal_code',
        'country',
        'phone',
        'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    /**
     * Get the user that owns the address.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Set this address as default.
     */
    public function setAsDefault()
    {
        // Remove default from other addresses
        static::where('user_id', $this->user_id)
            ->where('type', $this->type)
            ->update(['is_default' => false]);
        
        $this->is_default = true;
        $this->save();
    }

    /**
     * Scope shipping addresses.
     */
    public function scopeShipping($query)
    {
        return $query->whereIn('type', ['shipping', 'both']);
    }

    /**
     * Scope billing addresses.
     */
    public function scopeBilling($query)
    {
        return $query->whereIn('type', ['billing', 'both']);
    }

    /**
     * Scope default addresses.
     */
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    /**
     * Get full name.
     */
    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    /**
     * Get formatted address.
     */
    public function getFormattedAddressAttribute()
    {
        $lines = [$this->address_line1];
        
        if ($this->address_line2) {
            $lines[] = $this->address_line2;
        }
        
        $lines[] = $this->city . ', ' . $this->province . ' ' . $this->postal_code;
        $lines[] = $this->country;
        
        return implode("\n", $lines);
    }
}

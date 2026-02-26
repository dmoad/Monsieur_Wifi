<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShippingRate extends Model
{
    use HasFactory;

    protected $fillable = [
        'method',
        'name_en',
        'name_fr',
        'description_en',
        'description_fr',
        'cost',
        'estimated_days_min',
        'estimated_days_max',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'cost' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Get estimated delivery text.
     */
    public function getEstimatedDelivery($locale = 'en')
    {
        $min = $this->estimated_days_min;
        $max = $this->estimated_days_max;
        
        if ($locale === 'fr') {
            return $min === $max 
                ? "{$min} jours ouvrables" 
                : "{$min}-{$max} jours ouvrables";
        }
        
        return $min === $max 
            ? "{$min} business days" 
            : "{$min}-{$max} business days";
    }

    /**
     * Scope active shipping rates.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }

    /**
     * Get formatted cost.
     */
    public function getFormattedCostAttribute()
    {
        return '$' . number_format($this->cost, 2);
    }

    /**
     * Get name by locale.
     */
    public function getName($locale = 'en')
    {
        return $locale === 'fr' ? $this->name_fr : $this->name_en;
    }

    /**
     * Get description by locale.
     */
    public function getDescription($locale = 'en')
    {
        return $locale === 'fr' ? $this->description_fr : $this->description_en;
    }
}

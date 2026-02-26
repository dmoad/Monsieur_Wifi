<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductModel extends Model
{
    use HasFactory;

    public static $deviceTypes = ['820', '835'];

    protected $fillable = [
        'name',
        'slug',
        'description_en',
        'description_fr',
        'price',
        'is_active',
        'device_type',
        'specifications',
        'sort_order',
    ];

    protected $casts = [
        'specifications' => 'array',
        'is_active' => 'boolean',
        'price' => 'decimal:2',
    ];

    protected $appends = [
        'is_in_stock',
        'primary_image',
        'available_quantity',
    ];

    /**
     * Get the images for the product.
     */
    public function images()
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    /**
     * Get the primary image for the product.
     */
    public function primaryImage()
    {
        return $this->hasOne(ProductImage::class)->where('is_primary', true);
    }

    /**
     * Get the inventory for the product.
     */
    public function inventory()
    {
        return $this->hasOne(Inventory::class);
    }

    /**
     * Get all inventory items for this product.
     */
    public function inventoryItems()
    {
        return $this->hasMany(InventoryItem::class);
    }

    /**
     * Get available inventory items.
     */
    public function availableInventoryItems()
    {
        return $this->hasMany(InventoryItem::class)->where('status', 'available');
    }

    /**
     * Get the available stock quantity.
     */
    public function getAvailableStock()
    {
        return $this->inventory ? $this->inventory->getAvailableQuantity() : 0;
    }

    /**
     * Get is_in_stock attribute.
     */
    public function getIsInStockAttribute()
    {
        return $this->inventory ? $this->inventory->is_in_stock : false;
    }

    /**
     * Get primary_image attribute.
     */
    public function getPrimaryImageAttribute()
    {
        if ($this->relationLoaded('images')) {
            $primaryImage = $this->images->firstWhere('is_primary', true);
            if (!$primaryImage && $this->images->count() > 0) {
                $primaryImage = $this->images->first();
            }
            return $primaryImage ? $primaryImage->image_url : null;
        }
        return null;
    }

    /**
     * Get available_quantity attribute.
     */
    public function getAvailableQuantityAttribute()
    {
        return $this->getAvailableStock();
    }

    /**
     * Check if product is in stock.
     */
    public function isInStock()
    {
        return $this->inventory && $this->inventory->is_in_stock && $this->getAvailableStock() > 0;
    }

    /**
     * Get the primary image or first image.
     */
    public function getPrimaryImage()
    {
        $primary = $this->primaryImage;
        if ($primary) {
            return $primary;
        }
        return $this->images()->first();
    }

    /**
     * Get formatted price attribute.
     */
    public function getFormattedPriceAttribute()
    {
        return '$' . number_format($this->price, 2);
    }

    /**
     * Scope a query to only include active products.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to filter by device type.
     */
    public function scopeByType($query, $type)
    {
        return $query->where('device_type', $type);
    }
}

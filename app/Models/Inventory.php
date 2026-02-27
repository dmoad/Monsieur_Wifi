<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_model_id',
        'quantity',
        'reserved_quantity',
        'low_stock_threshold',
        'is_in_stock',
    ];

    protected $casts = [
        'is_in_stock' => 'boolean',
    ];

    protected $appends = [
        'available_quantity',
    ];

    /**
     * Get the product that owns the inventory.
     */
    public function productModel()
    {
        return $this->belongsTo(ProductModel::class);
    }

    /**
     * Get available quantity attribute for JSON serialization.
     */
    public function getAvailableQuantityAttribute()
    {
        return $this->getAvailableQuantity();
    }

    /**
     * Reserve a quantity for cart.
     */
    public function reserve($quantity)
    {
        if ($this->getAvailableQuantity() >= $quantity) {
            $this->reserved_quantity += $quantity;
            $this->save();
            return true;
        }
        return false;
    }

    /**
     * Release reserved quantity.
     */
    public function release($quantity)
    {
        $this->reserved_quantity = max(0, $this->reserved_quantity - $quantity);
        $this->save();
    }

    /**
     * Deduct quantity after order completion.
     */
    public function deduct($quantity)
    {
        $this->quantity -= $quantity;
        $this->reserved_quantity = max(0, $this->reserved_quantity - $quantity);
        $this->is_in_stock = $this->quantity > 0;
        $this->save();
    }

    /**
     * Get available quantity (not reserved).
     */
    public function getAvailableQuantity()
    {
        return max(0, $this->quantity - $this->reserved_quantity);
    }

    /**
     * Check if stock is low.
     */
    public function isLowStock()
    {
        return $this->quantity > 0 && $this->quantity <= $this->low_stock_threshold;
    }
}

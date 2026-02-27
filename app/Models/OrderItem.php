<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_model_id',
        'quantity',
        'price',
        'subtotal',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    /**
     * Get the order that owns the item.
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the product.
     */
    public function productModel()
    {
        return $this->belongsTo(ProductModel::class);
    }

    /**
     * Get the inventory items assigned to this order item.
     */
    public function inventoryItems()
    {
        return $this->hasMany(InventoryItem::class);
    }

    /**
     * Get devices created from assigned inventory items.
     */
    public function getAssignedDevices()
    {
        return $this->inventoryItems()
            ->with('device')
            ->whereNotNull('device_id')
            ->get()
            ->pluck('device');
    }
}

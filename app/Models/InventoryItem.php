<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_model_id',
        'mac_address',
        'serial_number',
        'status',
        'cart_item_id',
        'order_item_id',
        'notes',
        'received_at',
    ];

    protected $casts = [
        'received_at' => 'datetime',
    ];

    /**
     * Get the product model this item belongs to.
     */
    public function productModel()
    {
        return $this->belongsTo(ProductModel::class);
    }

    /**
     * Get the cart item this is reserved for.
     */
    public function cartItem()
    {
        return $this->belongsTo(CartItem::class);
    }

    /**
     * Get the order item this was sold in.
     */
    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class);
    }

    /**
     * Mark as reserved for a cart.
     */
    public function reserveForCart($cartItemId)
    {
        $this->update([
            'status' => 'reserved',
            'cart_item_id' => $cartItemId,
        ]);
    }

    /**
     * Release from reservation.
     */
    public function release()
    {
        $this->update([
            'status' => 'available',
            'cart_item_id' => null,
        ]);
    }

    /**
     * Mark as sold.
     */
    public function markAsSold($orderItemId)
    {
        $this->update([
            'status' => 'sold',
            'order_item_id' => $orderItemId,
            'cart_item_id' => null,
        ]);
    }

    /**
     * Mark as defective.
     */
    public function markAsDefective($notes = null)
    {
        $this->update([
            'status' => 'defective',
            'notes' => $notes,
        ]);
    }

    /**
     * Scope to get available items.
     */
    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }

    /**
     * Scope to get reserved items.
     */
    public function scopeReserved($query)
    {
        return $query->where('status', 'reserved');
    }

    /**
     * Scope to get sold items.
     */
    public function scopeSold($query)
    {
        return $query->where('status', 'sold');
    }

    /**
     * Scope to filter by product.
     */
    public function scopeForProduct($query, $productModelId)
    {
        return $query->where('product_model_id', $productModelId);
    }
}

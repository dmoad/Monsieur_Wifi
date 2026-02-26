<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'last_activity_at',
        'abandoned_email_sent_at',
    ];

    protected $casts = [
        'last_activity_at' => 'datetime',
        'abandoned_email_sent_at' => 'datetime',
    ];

    /**
     * Get the user that owns the cart.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the items in the cart.
     */
    public function items()
    {
        return $this->hasMany(CartItem::class);
    }

    /**
     * Add item to cart.
     */
    public function addItem($productId, $quantity)
    {
        $product = ProductModel::findOrFail($productId);
        
        // Check inventory
        if (!$product->inventory || $product->inventory->getAvailableQuantity() < $quantity) {
            return false;
        }

        $existingItem = $this->items()->where('product_model_id', $productId)->first();
        
        if ($existingItem) {
            $newQuantity = $existingItem->quantity + $quantity;
            if ($product->inventory->getAvailableQuantity() < $newQuantity - $existingItem->quantity) {
                return false;
            }
            // Release old reservation and reserve new
            $product->inventory->release($existingItem->quantity);
            $product->inventory->reserve($newQuantity);
            $existingItem->update(['quantity' => $newQuantity]);
        } else {
            $product->inventory->reserve($quantity);
            $this->items()->create([
                'product_model_id' => $productId,
                'quantity' => $quantity,
                'price_at_add' => $product->price,
            ]);
        }

        $this->touchActivity();
        return true;
    }

    /**
     * Update item quantity.
     */
    public function updateItem($itemId, $quantity)
    {
        $item = $this->items()->findOrFail($itemId);
        $product = $item->productModel;
        
        if ($quantity <= 0) {
            return $this->removeItem($itemId);
        }

        $difference = $quantity - $item->quantity;
        
        if ($difference > 0) {
            // Increasing quantity
            if ($product->inventory->getAvailableQuantity() < $difference) {
                return false;
            }
            $product->inventory->reserve($difference);
        } else {
            // Decreasing quantity
            $product->inventory->release(abs($difference));
        }

        $item->update(['quantity' => $quantity]);
        $this->touchActivity();
        return true;
    }

    /**
     * Remove item from cart.
     */
    public function removeItem($itemId)
    {
        $item = $this->items()->findOrFail($itemId);
        $item->productModel->inventory->release($item->quantity);
        $item->delete();
        $this->touchActivity();
        return true;
    }

    /**
     * Clear cart.
     */
    public function clear()
    {
        foreach ($this->items as $item) {
            $item->productModel->inventory->release($item->quantity);
        }
        $this->items()->delete();
    }

    /**
     * Get cart total.
     */
    public function getTotal()
    {
        return $this->items->sum(function ($item) {
            return $item->getSubtotal();
        });
    }

    /**
     * Touch last activity.
     */
    public function touchActivity()
    {
        $this->last_activity_at = Carbon::now();
        $this->save();
    }

    /**
     * Check if cart is abandoned.
     */
    public function isAbandoned()
    {
        if (!$this->last_activity_at) {
            return false;
        }
        
        $hours = config('system_settings.cart_abandonment_hours', 24);
        return $this->last_activity_at->diffInHours(Carbon::now()) >= $hours;
    }

    /**
     * Get abandoned duration in hours.
     */
    public function getAbandonedDuration()
    {
        if (!$this->last_activity_at) {
            return 0;
        }
        
        return $this->last_activity_at->diffInHours(Carbon::now());
    }
}

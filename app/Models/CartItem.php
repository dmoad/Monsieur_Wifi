<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'cart_id',
        'product_model_id',
        'quantity',
        'price_at_add',
    ];

    protected $casts = [
        'price_at_add' => 'decimal:2',
    ];

    protected $appends = [
        'product_id',
    ];

    /**
     * Get the cart that owns the item.
     */
    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    /**
     * Alias for product_model_id to maintain API consistency.
     */
    public function getProductIdAttribute()
    {
        return $this->product_model_id;
    }

    /**
     * Get the product.
     */
    public function productModel()
    {
        return $this->belongsTo(ProductModel::class);
    }

    /**
     * Get item subtotal.
     */
    public function getSubtotal()
    {
        return $this->price_at_add * $this->quantity;
    }

    /**
     * Get subtotal attribute.
     */
    public function getSubtotalAttribute()
    {
        return $this->getSubtotal();
    }
}

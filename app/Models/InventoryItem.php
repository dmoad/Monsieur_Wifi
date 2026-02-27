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
        'device_id',
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
     * Get the device created from this inventory item.
     */
    public function device()
    {
        return $this->belongsTo(Device::class);
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

    /**
     * Convert this inventory item to a Device.
     */
    public function convertToDevice($ownerId)
    {
        $productModel = $this->productModel()->first();
        
        $device = Device::create([
            'name' => "{$productModel->device_type}-{$this->serial_number}",
            'model' => $productModel->device_type,
            'serial_number' => $this->serial_number,
            'mac_address' => $this->mac_address,
            'device_key' => \Illuminate\Support\Str::random(32),
            'device_secret' => \Illuminate\Support\Str::random(64),
            'owner_id' => $ownerId,
            'configuration_version' => 1,
        ]);

        // Auto-assign firmware based on model
        $firmware = \App\Models\Firmware::getDefaultForModel($device->model);
        
        if (!$firmware) {
            $firmware = \App\Models\Firmware::forModel($device->model)
                ->enabled()
                ->orderBy('created_at', 'desc')
                ->first();
        }
        
        if (!$firmware) {
            $firmware = \App\Models\Firmware::forModel($device->model)
                ->orderBy('created_at', 'desc')
                ->first();
        }

        $device->firmware_id = $firmware ? $firmware->id : null;
        $device->save();

        return $device;
    }
}

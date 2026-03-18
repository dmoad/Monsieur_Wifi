<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ProductModel;

class Device extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'product_model_id',
        'serial_number',
        'mac_address',
        'firmware_version',
        'firmware_id',
        'owner_id',
        'last_seen',
        'configuration_version',
        'device_key',
        'device_secret',
        'reboot_count',
        'scan_counter',
        'uptime',
        'organization_id',
    ];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Get the product model associated with this device.
     */
    public function productModel()
    {
        return $this->belongsTo(ProductModel::class);
    }

    /**
     * Get the owner of the device.
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * Get the firmware of the device.
     */
    public function firmware()
    {
        return $this->belongsTo(Firmware::class, 'firmware_id');
    }

    /**
     * Get the location associated with the device.
     */
    public function location()
    {
        return $this->hasOne(Location::class);
    }

    /**
     * Get the locations associated with the device (legacy relationship).
     */
    public function locations()
    {
        return $this->hasMany(Location::class);
    }

    /**
     * Get the inventory item associated with the device.
     */
    public function inventoryItem()
    {
        return $this->hasOne(InventoryItem::class);
    }

    /**
     * Get the scan results associated with the device.
     */
    public function scanResults()
    {
        return $this->hasMany(ScanResult::class);
    }

    /**
     * Increment the scan counter and return the new value.
     */
    public function incrementScanCounter()
    {
        $this->increment('scan_counter');
        return $this->scan_counter;
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Firmware extends Model
{
    use HasFactory;

    /**
     * Status constants
     */
    const STATUS_ENABLED = true;
    const STATUS_DISABLED = false;

    protected $fillable = [
        'name',
        'model',
        'file_name',
        'file_path',
        'md5sum',
        'file_size',
        'is_enabled',
        'description',
        'version',
        'default_model_firmware',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
        'file_size' => 'integer',
        'default_model_firmware' => 'boolean',
    ];

    /**
     * Get the formatted file size
     */
    public function getFormattedFileSizeAttribute()
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];

        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Check if the file exists in storage
     */
    public function fileExists()
    {
        return Storage::disk('public')->exists($this->file_path);
    }

    /**
     * Get the full file path
     */
    public function getFullFilePathAttribute()
    {
        return Storage::disk('public')->path($this->file_path);
    }

    /**
     * Get the download URL
     */
    public function getDownloadUrlAttribute()
    {
        return Storage::disk('public')->url($this->file_path);
    }

    /**
     * Scope to get only enabled firmware
     */
    public function scopeEnabled($query)
    {
        return $query->where('is_enabled', true);
    }

    /**
     * Scope to get only disabled firmware
     */
    public function scopeDisabled($query)
    {
        return $query->where('is_enabled', false);
    }

    /**
     * Scope to filter firmware by device_type string
     */
    public function scopeForModel($query, $model)
    {
        return $query->where('model', $model);
    }

    /**
     * Scope to get only default firmware
     */
    public function scopeDefault($query)
    {
        return $query->where('default_model_firmware', true);
    }

    /**
     * Scope to get default firmware for a specific device_type
     */
    public function scopeDefaultForModel($query, $model)
    {
        return $query->where('model', $model)->where('default_model_firmware', true);
    }

    /**
     * Get the default firmware for a specific device_type string
     */
    public static function getDefaultForModel($model)
    {
        return static::defaultForModel($model)->first();
    }

    /**
     * Set this firmware as default for its model.
     * Unsets any other default firmware for the same model first.
     */
    public function setAsDefault()
    {
        static::where('model', $this->model)
            ->where('default_model_firmware', true)
            ->update(['default_model_firmware' => false]);

        return $this->update(['default_model_firmware' => true]);
    }

    /**
     * Get available models keyed by device_type, with ProductModel name as value.
     * Returns ['820' => 'MR 820AX', '835' => 'MR 835AX'] (or whatever names are in DB).
     */
    public static function getAvailableModels(): array
    {
        return ProductModel::whereIn('device_type', ProductModel::$deviceTypes)
            ->where('is_active', true)
            ->pluck('name', 'device_type')
            ->toArray();
    }

    /**
     * Get status text
     */
    public function getStatusTextAttribute()
    {
        return $this->is_enabled ? 'Enable' : 'Disable';
    }
}

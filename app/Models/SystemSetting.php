<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'default_essid',
        'default_password',
        'portal_timeout',
        'idle_timeout',
        'bandwidth_limit',
        'user_limit',
        'enable_terms',
        'radius_ip',
        'radius_port',
        'radius_secret',
        'accounting_port',
        'company_name',
        'company_website',
        'contact_email',
        'support_phone',
        'logo_path',
        'favicon_path',
        'splash_background_path',
        'primary_color',
        'secondary_color',
        'font_family',
        'portal_theme',
        'smtp_server',
        'smtp_port',
        'sender_email',
        'smtp_password',
        'tax_rate',
        'cart_abandonment_hours',
        'payment_mode',
        'stripe_enabled',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'default_password',
        'radius_secret',
        'smtp_password',
    ];

    /**
     * Get settings as a key-value array
     * 
     * @return array
     */
    public static function getSettings()
    {
        $settings = self::first() ?? new self();
        return $settings->toArray();
    }

    /**
     * Update settings from an array
     * 
     * @param array $data
     * @return SystemSetting
     */
    public static function updateSettings(array $data)
    {
        $settings = self::first();
        
        if (!$settings) {
            $settings = new self();
        }
        
        $settings->fill($data);
        $settings->save();
        
        return $settings;
    }
}
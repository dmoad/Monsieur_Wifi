<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'organization_id',
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

    protected $hidden = [
        'default_password',
        'radius_secret',
        'smtp_password',
    ];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Get settings for a specific org, merged with global defaults.
     *
     * - Global row: organization_id IS NULL
     * - Org row: organization_id = $orgId (overrides non-null fields)
     *
     * @param int|null $orgId
     * @return array
     */
    public static function getSettings(?int $orgId = null): array
    {
        $global = self::whereNull('organization_id')->first();
        $globalData = $global ? $global->toArray() : (new self())->toArray();

        if (! $orgId) {
            return $globalData;
        }

        $orgRow = self::where('organization_id', $orgId)->first();
        if (! $orgRow) {
            return $globalData;
        }

        // Merge: org values override global where they are not null
        $orgData = $orgRow->toArray();
        $merged = $globalData;
        foreach ($orgData as $key => $value) {
            if ($value !== null && ! in_array($key, ['id', 'organization_id', 'created_at', 'updated_at'])) {
                $merged[$key] = $value;
            }
        }

        // Keep the org row's ID so updates target the right record
        $merged['id'] = $orgRow->id;
        $merged['organization_id'] = $orgId;

        return $merged;
    }

    /**
     * Update settings. If $orgId is given, upserts the org-specific row.
     * If $orgId is null, updates the global defaults row.
     */
    public static function updateSettings(array $data, ?int $orgId = null): self
    {
        if ($orgId) {
            $settings = self::firstOrNew(['organization_id' => $orgId]);
        } else {
            $settings = self::whereNull('organization_id')->first() ?? new self();
        }

        $settings->fill($data);
        $settings->save();

        return $settings;
    }
}

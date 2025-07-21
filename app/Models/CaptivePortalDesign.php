<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CaptivePortalDesign extends Model
{
    use HasFactory;

    protected $table = 'captive_portal_designs';
    
    protected $fillable = [
        'user_id',
        'name',
        'description',
        'theme_color',
        'welcome_message',
        'login_instructions',
        'button_text',
        'show_terms',
        'terms_content',
        'privacy_content',
        'location_logo_path',
        'background_image_path',
        'additional_settings',
        'is_default',
        'background_color_gradient_start',
        'background_color_gradient_end',
    ];
    
    protected $casts = [
        'show_terms' => 'boolean',
        'is_default' => 'boolean',
        'additional_settings' => 'array'
    ];
    
    /**
     * Get the user that owns the design
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Get the locations using this design
     */
    public function locations()
    {
        return $this->hasMany(Location::class);
    }
    
    /**
     * Create a duplicate of the current design
     */
    public function duplicate()
    {
        $newDesign = $this->replicate();
        $newDesign->name = $this->name . ' (Copy)';
        $newDesign->is_default = false;
        $newDesign->created_at = now();
        $newDesign->updated_at = now();
        $newDesign->save();
        
        return $newDesign;
    }
}
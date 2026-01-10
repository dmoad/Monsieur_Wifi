<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TempCaptivePortalDesign extends Model
{
    use HasFactory;

    protected $table = 'temp_captive_portal_designs';
    
    protected $fillable = [
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
     * Transfer this temporary design to a permanent CaptivePortalDesign for a user
     *
     * @param int $userId The ID of the user to assign the design to
     * @return CaptivePortalDesign The newly created permanent design
     */
    public function transferToUser($userId)
    {
        $permanentDesign = CaptivePortalDesign::create([
            'user_id' => $userId,
            'owner_id' => $userId,
            'name' => $this->name,
            'description' => $this->description,
            'theme_color' => $this->theme_color,
            'welcome_message' => $this->welcome_message,
            'login_instructions' => $this->login_instructions,
            'button_text' => $this->button_text,
            'show_terms' => $this->show_terms,
            'terms_content' => $this->terms_content,
            'privacy_content' => $this->privacy_content,
            'location_logo_path' => $this->location_logo_path,
            'background_image_path' => $this->background_image_path,
            'additional_settings' => $this->additional_settings,
            'is_default' => $this->is_default,
            'background_color_gradient_start' => $this->background_color_gradient_start,
            'background_color_gradient_end' => $this->background_color_gradient_end,
        ]);
        
        // Delete the temporary design after successful transfer
        $this->delete();
        
        return $permanentDesign;
    }
}

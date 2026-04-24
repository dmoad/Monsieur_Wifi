<?php

namespace Database\Seeders;

use App\Models\CaptivePortalDesign;
use App\Models\User;
use Illuminate\Database\Seeder;

class CaptivePortalDesignSeeder extends Seeder
{
    public function run(): void
    {
        $ownerId = User::where('role', 'superadmin')->value('id')
            ?? User::orderBy('id')->value('id');

        if (! $ownerId) {
            return;
        }

        $templates = [
            [
                'name' => 'Modern Indigo',
                'description' => 'Soft indigo wash with the platform primary accent.',
                'theme_color' => '#6366F1',
                'background_color_gradient_start' => '#EEF2FF',
                'background_color_gradient_end' => '#C7D2FE',
                'welcome_message' => 'Welcome to our WiFi',
                'login_instructions' => 'Enter your email to connect to our WiFi network',
                'button_text' => 'Connect to WiFi',
            ],
            [
                'name' => 'Mint Breeze',
                'description' => 'Fresh emerald palette suited to wellness and outdoor venues.',
                'theme_color' => '#10B981',
                'background_color_gradient_start' => '#D1FAE5',
                'background_color_gradient_end' => '#A7F3D0',
                'welcome_message' => 'Welcome — connect and relax',
                'login_instructions' => 'Enter your email to access our WiFi network',
                'button_text' => 'Connect',
            ],
            [
                'name' => 'Rose Petal',
                'description' => 'Warm rose-pink palette for cafés, salons, and retail.',
                'theme_color' => '#EC4899',
                'background_color_gradient_start' => '#FCE7F3',
                'background_color_gradient_end' => '#FBCFE8',
                'welcome_message' => 'Welcome in',
                'login_instructions' => 'Pop in your email and you are online',
                'button_text' => 'Get online',
            ],
            [
                'name' => 'Sunset',
                'description' => 'Warm peach gradient for hospitality and events.',
                'theme_color' => '#F97316',
                'background_color_gradient_start' => '#FFEDD5',
                'background_color_gradient_end' => '#FED7AA',
                'welcome_message' => 'Welcome — enjoy the WiFi',
                'login_instructions' => 'Enter your email to connect',
                'button_text' => 'Connect to WiFi',
            ],
        ];

        foreach ($templates as $template) {
            CaptivePortalDesign::updateOrCreate(
                ['name' => $template['name']],
                array_merge($template, [
                    'user_id' => $ownerId,
                    'owner_id' => $ownerId,
                    'show_terms' => true,
                ])
            );
        }
    }
}

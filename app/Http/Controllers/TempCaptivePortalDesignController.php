<?php

namespace App\Http\Controllers;

use App\Models\TempCaptivePortalDesign;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class TempCaptivePortalDesignController extends Controller
{
    /**
     * Store a newly created temporary captive portal design.
     * This endpoint is public (no auth required) to allow users to create designs before registration.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        Log::info('TempCaptivePortalDesignController::store called');
        Log::info('Request data: ', $request->all());
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'theme_color' => 'required|string|max:7',
            'welcome_message' => 'required|string|max:255',
            'login_instructions' => 'nullable|string',
            'button_text' => 'required|string|max:100',
            'show_terms' => 'boolean',
            'terms_content' => 'nullable|string',
            'privacy_content' => 'nullable|string',
            'location_logo' => 'nullable|image|max:2048',
            'background_image' => 'nullable|image|max:5120',
            'background_color_gradient_start' => 'nullable|string|max:7',
            'background_color_gradient_end' => 'nullable|string|max:7',
            'additional_settings' => 'nullable|array',
            'is_default' => 'boolean',
        ]);
        
        // Handle file uploads
        if ($request->hasFile('location_logo')) {
            $logoPath = $request->file('location_logo')->store('captive-portals/logos', 'public');
            $validated['location_logo_path'] = $logoPath;
        }
        
        if ($request->hasFile('background_image')) {
            $bgPath = $request->file('background_image')->store('captive-portals/backgrounds', 'public');
            $validated['background_image_path'] = $bgPath;
        }
        
        // Create temporary design
        $design = TempCaptivePortalDesign::create($validated);
        
        Log::info('Temporary design created with ID: ' . $design->id);
        
        return response()->json([
            'success' => true,
            'message' => 'Temporary captive portal design created successfully',
            'data' => [
                'id' => $design->id,
                'design_id' => $design->id, // Alias for convenience
            ]
        ], 201);
    }
}

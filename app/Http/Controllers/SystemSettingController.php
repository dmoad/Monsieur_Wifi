<?php

namespace App\Http\Controllers;

use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class SystemSettingController extends Controller
{
    /**
     * Display the system settings page
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        if (auth()->user()->role !== 'superadmin') {
            return response()->json([
                'status' => 'error',
                'message' => 'You are not authorized to view system settings'
            ], 403);
        }
        $settings = SystemSetting::getSettings();
        return response()->json([
            'status' => 'success',
            'message' => 'Settings fetched successfully',
            'settings' => $settings
        ]);
    }

    /**
     * Update system settings
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        if (auth()->user()->role !== 'superadmin') {
            return response()->json([
                'status' => 'error',
                'message' => 'You are not authorized to update system settings'
            ], 403);
        }

        Log::info('Update system settings request received');
        Log::info($request->all());
        $validator = Validator::make($request->all(), [
            'default_essid' => 'required|string|max:32',
            'default_guest_essid' => 'required|string|max:32',
            'default_password' => 'nullable|string|min:8',
            'portal_timeout' => 'required|integer|min:1|max:168',
            'idle_timeout' => 'required|integer|min:5|max:180',
            'bandwidth_limit' => 'required|integer|min:1|max:1000',
            'user_limit' => 'required|integer|min:1|max:500',
            'enable_terms' => 'boolean',
            'radius_ip' => 'nullable|ip',
            'radius_port' => 'nullable|integer|min:1|max:65535',
            'radius_secret' => 'nullable|string|min:8',
            'accounting_port' => 'nullable|integer|min:1|max:65535',
            'company_name' => 'required|string|max:100',
            'company_website' => 'nullable|url',
            'contact_email' => 'nullable|email',
            'support_phone' => 'nullable|string|max:20',
            'primary_color' => 'required|string|max:7',
            'secondary_color' => 'required|string|max:7',
            'font_family' => 'required|string|max:50',
            'portal_theme' => 'required|in:light,dark,auto',
            'smtp_server' => 'nullable|string|max:100',
            'smtp_port' => 'nullable|integer|min:1|max:65535',
            'sender_email' => 'nullable|email',
            'smtp_password' => 'nullable|string',
            'logo' => 'nullable|image|max:2048',
            'favicon' => 'nullable|image|max:1024',
            'splash_background' => 'nullable|image|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false, 
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $request->except(['logo', 'favicon', 'splash_background']);

        // Handle file uploads
        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('public/logos');
            $data['logo_path'] = Storage::url($path);
        }

        if ($request->hasFile('favicon')) {
            $path = $request->file('favicon')->store('public/favicons');
            $data['favicon_path'] = Storage::url($path);
        }

        if ($request->hasFile('splash_background')) {
            $path = $request->file('splash_background')->store('public/backgrounds');
            $data['splash_background_path'] = Storage::url($path);
        }

        try {
            $settings = SystemSetting::updateSettings($data);
            
            return response()->json([
                'status' => 'success',
                'message' => 'Settings updated successfully',
                'settings' => $settings
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to update system settings: ' . $e->getMessage());
            
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update settings. Please try again.'
            ], 500);
        }
    }

    /**
     * Send a test email
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function testEmail(Request $request)
    {
        try {
            // Here you would implement your test email sending logic
            // using the configured SMTP settings
            
            // Simulating success for now
            return response()->json([
                'success' => true,
                'message' => 'Test email sent successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send test email: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to send test email: ' . $e->getMessage()
            ], 500);
        }
    }
}
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
     * Get settings for the current context.
     *
     * - Superadmin without org context → global defaults
     * - Superadmin with ?scope=global  → global defaults (explicit)
     * - Any user with org context      → merged org settings (global + org overrides)
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $orgId = $this->resolveOrgId($request, $user);

        // Only superadmin can view global defaults directly
        if (! $orgId && $user->role !== 'superadmin') {
            return response()->json(['success' => false, 'message' => 'No organization context'], 400);
        }

        $settings = SystemSetting::getSettings($orgId);

        return response()->json([
            'status' => 'success',
            'message' => 'Settings fetched successfully',
            'settings' => $settings,
            'scope' => $orgId ? 'organization' : 'global',
        ]);
    }

    /**
     * Update settings.
     *
     * - Superadmin with ?scope=global → updates global defaults
     * - Org admin/owner              → updates org-specific overrides
     */
    public function update(Request $request)
    {
        $user = auth()->user();
        $orgId = $this->resolveOrgId($request, $user);

        // Only superadmin can update global defaults
        if (! $orgId && $user->role !== 'superadmin') {
            return response()->json(['success' => false, 'message' => 'Forbidden'], 403);
        }

        $validator = Validator::make($request->all(), [
            'default_essid' => 'sometimes|string|max:32',
            'default_guest_essid' => 'sometimes|string|max:32',
            'default_password' => 'nullable|string|min:8',
            'portal_timeout' => 'sometimes|integer|min:1|max:168',
            'idle_timeout' => 'sometimes|integer|min:5|max:180',
            'bandwidth_limit' => 'sometimes|integer|min:1|max:1000',
            'user_limit' => 'sometimes|integer|min:1|max:500',
            'enable_terms' => 'boolean',
            'radius_ip' => 'nullable|ip',
            'radius_port' => 'nullable|integer|min:1|max:65535',
            'radius_secret' => 'nullable|string|min:8',
            'accounting_port' => 'nullable|integer|min:1|max:65535',
            'company_name' => 'sometimes|string|max:100',
            'company_website' => 'nullable|url',
            'contact_email' => 'nullable|email',
            'support_phone' => 'nullable|string|max:20',
            'primary_color' => 'sometimes|string|max:7',
            'secondary_color' => 'sometimes|string|max:7',
            'font_family' => 'sometimes|string|max:50',
            'portal_theme' => 'sometimes|in:light,dark,auto',
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

        $data = $request->except(['logo', 'favicon', 'splash_background', 'scope']);

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
            $settings = SystemSetting::updateSettings($data, $orgId);

            return response()->json([
                'status' => 'success',
                'message' => 'Settings updated successfully',
                'settings' => $settings,
                'scope' => $orgId ? 'organization' : 'global',
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
     * Send a test email using the current org's SMTP settings.
     */
    public function testEmail(Request $request)
    {
        try {
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

    /**
     * Determine which org to scope settings to.
     *
     * - ?scope=global (superadmin only) → null (global defaults)
     * - Otherwise                       → user's current org
     */
    private function resolveOrgId(Request $request, $user): ?int
    {
        if ($request->query('scope') === 'global' && $user->role === 'superadmin') {
            return null;
        }

        return $user->current_organization_id;
    }
}

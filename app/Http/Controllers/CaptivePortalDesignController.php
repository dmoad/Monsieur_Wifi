<?php

namespace App\Http\Controllers;

use App\Models\CaptivePortalDesign;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class CaptivePortalDesignController extends Controller
{
    /**
     * Display a listing of the captive portal designs.
     */
    public function index()
    {
        $user = auth()->user();
        $orgId = $user->current_organization_id;

        $query = CaptivePortalDesign::query();

        if ($orgId) {
            $query->where('organization_id', $orgId);
        } elseif (!in_array($user->role, ['admin', 'superadmin'])) {
            $query->where('user_id', $user->id);
        }

        $designs = $query->latest()->get();

        return response()->json([
            'success' => true,
            'data' => $designs
        ]);
    }

    public function get_all()
    {
        $user = auth()->user();
        $orgId = $user->current_organization_id;

        $query = CaptivePortalDesign::with(['user', 'owner']);

        if ($orgId) {
            $query->where('organization_id', $orgId);
        } elseif (!in_array($user->role, ['admin', 'superadmin'])) {
            $query->where('user_id', $user->id);
        }

        $designs = $query->latest()->get();

        // Add the storage URL for logo paths and owner information
        $designs->transform(function ($design) use ($user) {
            if ($design->location_logo_path) {
                $design->location_logo_url = asset('storage/' . $design->location_logo_path);
            }

            if (in_array($user->role, ['admin', 'superadmin'])) {
                $design->creator_name = $design->user->name ?? 'Unknown';
                $design->owner_name = $design->owner->name ?? $design->user->name ?? 'Unknown';
                $design->current_owner_id = $design->owner_id ?? $design->user_id;
            }

            return $design;
        });

        return response()->json([
            'success' => true,
            'data' => $designs,
            'is_admin' => in_array($user->role, ['admin', 'superadmin']),
        ]);
    }

    /**
     * Store a newly created captive portal design.
     */
    public function create(Request $request)
    {
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
        ]);

        if ($request->hasFile('location_logo')) {
            $validated['location_logo_path'] = $request->file('location_logo')->store('captive-portals/logos', 'public');
        }

        if ($request->hasFile('background_image')) {
            $validated['background_image_path'] = $request->file('background_image')->store('captive-portals/backgrounds', 'public');
        }

        $user = auth()->user();
        $validated['owner_id'] = $user->id;
        $validated['organization_id'] = $user->current_organization_id;

        $design = $user->captivePortalDesigns()->create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Captive portal design created successfully',
            'data' => $design
        ], 201);
    }

    /**
     * Display the specified captive portal design.
     */
    public function show($captivePortalDesign)
    {
        $design = $this->findScopedDesign($captivePortalDesign);

        if (!$design) {
            return response()->json(['success' => false, 'message' => 'Design not found'], 404);
        }

        if ($design->location_logo_path) {
            $design->location_logo_url = asset('storage/' . $design->location_logo_path);
        }

        return response()->json([
            'success' => true,
            'data' => $design
        ]);
    }

    /**
     * Update the specified captive portal design.
     */
    public function update(Request $request, $captivePortalDesign)
    {
        $design = $this->findScopedDesign($captivePortalDesign);

        if (!$design) {
            return response()->json(['success' => false, 'message' => 'Design not found'], 404);
        }

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
        ]);

        if ($request->hasFile('location_logo')) {
            if ($design->location_logo_path) {
                Storage::disk('public')->delete($design->location_logo_path);
            }
            $validated['location_logo_path'] = $request->file('location_logo')->store('captive-portals/logos', 'public');
        }

        if ($request->hasFile('background_image')) {
            if ($design->background_image_path) {
                Storage::disk('public')->delete($design->background_image_path);
            }
            $validated['background_image_path'] = $request->file('background_image')->store('captive-portals/backgrounds', 'public');
        }

        $design->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Captive portal design updated successfully',
            'data' => $design
        ]);
    }

    /**
     * Duplicate the specified captive portal design.
     */
    public function duplicate($design_id)
    {
        $design = $this->findScopedDesign($design_id);

        if (!$design) {
            return response()->json(['success' => false, 'message' => 'Design not found'], 404);
        }

        $newDesign = $design->duplicate();

        return response()->json([
            'success' => true,
            'message' => 'Captive portal design duplicated successfully',
            'data' => $newDesign
        ], 201);
    }

    /**
     * Remove the specified captive portal design.
     */
    public function destroy($design_id)
    {
        $design = $this->findScopedDesign($design_id);

        if (!$design) {
            return response()->json(['success' => false, 'message' => 'Design not found'], 404);
        }

        if ($design->location_logo_path) {
            Storage::disk('public')->delete($design->location_logo_path);
        }

        if ($design->background_image_path) {
            Storage::disk('public')->delete($design->background_image_path);
        }

        $design->delete();

        return response()->json([
            'success' => true,
            'message' => 'Captive portal design deleted successfully'
        ]);
    }

    /**
     * Change the owner of the specified captive portal design.
     * Only admins can perform this action.
     */
    public function changeOwner(Request $request, $design_id)
    {
        $user = auth()->user();
        if (!in_array($user->role, ['admin', 'superadmin'])) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'owner_id' => 'required|exists:users,id'
        ]);

        $design = $this->findScopedDesign($design_id);

        if (!$design) {
            return response()->json(['success' => false, 'message' => 'Design not found'], 404);
        }

        $design->update(['owner_id' => $validated['owner_id']]);
        $design->load(['user', 'owner']);

        return response()->json([
            'success' => true,
            'message' => 'Ownership changed successfully',
            'data' => [
                'design_id' => $design->id,
                'new_owner_id' => $design->owner_id,
                'new_owner_name' => $design->owner->name,
                'creator_name' => $design->user->name
            ]
        ]);
    }

    function registerWithCaptivePortal()
    {
        return view('register-with-captive-portal');
    }

    /**
     * Find a design scoped to the current user's organization.
     */
    private function findScopedDesign($id): ?CaptivePortalDesign
    {
        $user = auth()->user();
        $orgId = $user->current_organization_id;

        $design = CaptivePortalDesign::find($id);
        if (!$design) {
            return null;
        }

        if ($orgId) {
            return $design->organization_id == $orgId ? $design : null;
        }

        if (in_array($user->role, ['admin', 'superadmin'])) {
            return $design;
        }

        return ($design->user_id == $user->id || $design->owner_id == $user->id) ? $design : null;
    }
}

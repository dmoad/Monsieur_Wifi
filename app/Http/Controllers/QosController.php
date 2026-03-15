<?php

namespace App\Http\Controllers;

use App\Models\QosClass;
use App\Models\QosDomain;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QosController extends Controller
{
    // ── Authorization helper ──────────────────────────────────────────────────

    private function isSuperAdmin(): bool
    {
        return in_array(Auth::user()->role, ['superadmin']);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // GET /api/v1/qos/classes
    // Returns all four DSCP classes with their domain lists.
    // Accessible to all authenticated users (admins get read-only view).
    // ─────────────────────────────────────────────────────────────────────────

    public function index()
    {
        $classes = QosClass::with('domains')
            ->orderBy('priority')
            ->get()
            ->map(fn (QosClass $c) => [
                'id'          => $c->id,
                'label'       => $c->label,
                'dscp_value'  => $c->dscp_value,
                'nft_mark'    => $c->nft_mark,
                'priority'    => $c->priority,
                'description' => $c->description,
                'domains'     => $c->domains->pluck('domain')->values(),
            ]);

        return response()->json(['success' => true, 'data' => $classes]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // GET /api/v1/qos/classes/{classId}
    // ─────────────────────────────────────────────────────────────────────────

    public function show(string $classId)
    {
        $class = QosClass::with('domains')->find(strtoupper($classId));

        if (!$class) {
            return response()->json(['success' => false, 'message' => 'QoS class not found'], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => [
                'id'          => $class->id,
                'label'       => $class->label,
                'dscp_value'  => $class->dscp_value,
                'nft_mark'    => $class->nft_mark,
                'priority'    => $class->priority,
                'description' => $class->description,
                'domains'     => $class->domains->pluck('domain')->values(),
            ],
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // POST /api/v1/qos/classes/{classId}/domains
    // Body: { "domain": "*.example.com" }
    // SuperAdmin only.
    // ─────────────────────────────────────────────────────────────────────────

    public function addDomain(Request $request, string $classId)
    {
        if (!$this->isSuperAdmin()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $class = QosClass::find(strtoupper($classId));
        if (!$class) {
            return response()->json(['success' => false, 'message' => 'QoS class not found'], 404);
        }

        // BE has no domain list — it is the catch-all fallback
        if ($class->id === QosClass::BE) {
            return response()->json(['success' => false, 'message' => 'The Default (BE) class does not have a domain list — unmatched traffic falls into it automatically'], 422);
        }

        $validated = $request->validate([
            'domain' => 'required|string|max:253',
        ]);

        $domain = trim(strtolower($validated['domain']));

        // Check for duplicate within the class
        $exists = QosDomain::where('class_id', $class->id)
            ->where('domain', $domain)
            ->exists();

        if ($exists) {
            return response()->json(['success' => false, 'message' => 'Domain already exists in this class'], 422);
        }

        QosDomain::create(['class_id' => $class->id, 'domain' => $domain]);

        return response()->json([
            'success' => true,
            'message' => 'Domain added.',
            'data'    => ['domain' => $domain],
        ], 201);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // DELETE /api/v1/qos/classes/{classId}/domains/{domain}
    // SuperAdmin only.
    // ─────────────────────────────────────────────────────────────────────────

    public function removeDomain(string $classId, string $domain)
    {
        if (!$this->isSuperAdmin()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $class = QosClass::find(strtoupper($classId));
        if (!$class) {
            return response()->json(['success' => false, 'message' => 'QoS class not found'], 404);
        }

        $deleted = QosDomain::where('class_id', $class->id)
            ->where('domain', urldecode($domain))
            ->delete();

        if (!$deleted) {
            return response()->json(['success' => false, 'message' => 'Domain not found in this class'], 404);
        }

        return response()->json(['success' => true, 'message' => 'Domain removed.']);
    }
}

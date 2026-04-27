<?php

namespace App\Http\Controllers;

use App\Models\QosClass;

class QosController extends Controller
{
    // ─────────────────────────────────────────────────────────────────────────
    // GET /api/v1/qos/classes
    // Returns all four DSCP classes (metadata; domains are per network).
    // Accessible to all authenticated users (admins get read-only view).
    // ─────────────────────────────────────────────────────────────────────────

    public function index()
    {
        $classes = QosClass::orderBy('priority')
            ->get()
            ->map(fn (QosClass $c) => [
                'id' => $c->id,
                'label' => $c->label,
                'dscp_value' => $c->dscp_value,
                'nft_mark' => $c->nft_mark,
                'priority' => $c->priority,
                'description' => $c->description,
                'domains' => [],
            ]);

        return response()->json(['success' => true, 'data' => $classes]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // GET /api/v1/qos/classes/{classId}
    // ─────────────────────────────────────────────────────────────────────────

    public function show(string $classId)
    {
        $class = QosClass::find(strtoupper($classId));

        if (! $class) {
            return response()->json(['success' => false, 'message' => 'QoS class not found'], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $class->id,
                'label' => $class->label,
                'dscp_value' => $class->dscp_value,
                'nft_mark' => $class->nft_mark,
                'priority' => $class->priority,
                'description' => $class->description,
                'domains' => [],
            ],
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // POST /api/v1/qos/classes/{classId}/domains
    // Body: { "domain": "*.example.com" }
    // SuperAdmin only.
    // ─────────────────────────────────────────────────────────────────────────

    public function addDomain(string $classId)
    {
        return response()->json([
            'success' => false,
            'message' => 'Global QoS domain lists are removed. Manage domains per location: POST /api/locations/{locationId}/qos-domains',
        ], 410);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // DELETE /api/v1/qos/classes/{classId}/domains/{domain}
    // SuperAdmin only.
    // ─────────────────────────────────────────────────────────────────────────

    public function removeDomain(string $classId, string $domain)
    {
        return response()->json([
            'success' => false,
            'message' => 'Global QoS domain lists are removed. Manage domains per location: DELETE /api/locations/{locationId}/qos-domains/{classId}?domain=...',
        ], 410);
    }
}

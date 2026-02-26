<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShippingRate;
use Illuminate\Http\Request;

class AdminShippingController extends Controller
{
    /**
     * List all shipping rates.
     */
    public function index()
    {
        $rates = ShippingRate::orderBy('sort_order')->get();

        return response()->json([
            'success' => true,
            'shipping_rates' => $rates,
        ]);
    }

    /**
     * Update shipping rate cost and details.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name_en' => 'sometimes|string|max:255',
            'name_fr' => 'sometimes|string|max:255',
            'description_en' => 'sometimes|string',
            'description_fr' => 'sometimes|string',
            'cost' => 'sometimes|numeric|min:0',
            'estimated_days_min' => 'sometimes|integer|min:1',
            'estimated_days_max' => 'sometimes|integer|min:1',
            'sort_order' => 'sometimes|integer',
        ]);

        $rate = ShippingRate::findOrFail($id);

        $rate->update($request->only([
            'name_en',
            'name_fr',
            'description_en',
            'description_fr',
            'cost',
            'estimated_days_min',
            'estimated_days_max',
            'sort_order',
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Shipping rate updated successfully.',
            'shipping_rate' => $rate,
        ]);
    }

    /**
     * Enable/disable shipping method.
     */
    public function toggle($id)
    {
        $rate = ShippingRate::findOrFail($id);
        $rate->is_active = !$rate->is_active;
        $rate->save();

        return response()->json([
            'success' => true,
            'message' => $rate->is_active ? 'Shipping method enabled.' : 'Shipping method disabled.',
            'shipping_rate' => $rate,
        ]);
    }
}

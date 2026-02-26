<?php

namespace App\Http\Controllers;

use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AddressController extends Controller
{
    /**
     * List user's addresses.
     */
    public function index()
    {
        $user = Auth::user();
        $addresses = Address::where('user_id', $user->id)
            ->orderBy('is_default', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'addresses' => $addresses,
        ]);
    }

    /**
     * Create new address.
     */
    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:shipping,billing,both',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'company' => 'nullable|string|max:255',
            'address_line1' => 'required|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'city' => 'required|string|max:255',
            'province' => 'required|string|max:255',
            'postal_code' => 'required|string|max:20',
            'country' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'is_default' => 'boolean',
        ]);

        $user = Auth::user();

        $address = Address::create([
            'user_id' => $user->id,
            'type' => $request->type,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'company' => $request->company,
            'address_line1' => $request->address_line1,
            'address_line2' => $request->address_line2,
            'city' => $request->city,
            'province' => $request->province,
            'postal_code' => $request->postal_code,
            'country' => $request->country,
            'phone' => $request->phone,
            'is_default' => $request->is_default ?? false,
        ]);

        if ($request->is_default) {
            $address->setAsDefault();
        }

        return response()->json([
            'success' => true,
            'message' => 'Address created successfully.',
            'address' => $address,
        ], 201);
    }

    /**
     * Update address.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'type' => 'sometimes|in:shipping,billing,both',
            'first_name' => 'sometimes|string|max:255',
            'last_name' => 'sometimes|string|max:255',
            'company' => 'nullable|string|max:255',
            'address_line1' => 'sometimes|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'city' => 'sometimes|string|max:255',
            'province' => 'sometimes|string|max:255',
            'postal_code' => 'sometimes|string|max:20',
            'country' => 'sometimes|string|max:255',
            'phone' => 'sometimes|string|max:20',
        ]);

        $user = Auth::user();
        $address = Address::where('id', $id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $address->update($request->only([
            'type', 'first_name', 'last_name', 'company', 'address_line1',
            'address_line2', 'city', 'province', 'postal_code', 'country', 'phone'
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Address updated successfully.',
            'address' => $address,
        ]);
    }

    /**
     * Delete address.
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $address = Address::where('id', $id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $address->delete();

        return response()->json([
            'success' => true,
            'message' => 'Address deleted successfully.',
        ]);
    }

    /**
     * Set address as default.
     */
    public function setDefault($id)
    {
        $user = Auth::user();
        $address = Address::where('id', $id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $address->setAsDefault();

        return response()->json([
            'success' => true,
            'message' => 'Address set as default.',
            'address' => $address,
        ]);
    }
}

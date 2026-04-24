<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CartController extends Controller
{
    /**
     * Get user's cart with items.
     */
    public function show()
    {
        $user = Auth::user();
        $cart = Cart::with(['items.productModel.images', 'items.productModel.inventory'])
            ->firstOrCreate(['user_id' => $user->id]);

        return response()->json([
            'success' => true,
            'cart' => $cart,
            'total' => $cart->getTotal(),
            'item_count' => $cart->items->sum('quantity'),
        ]);
    }

    /**
     * Add item to cart.
     */
    public function addItem(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:product_models,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $user = Auth::user();
        
        DB::beginTransaction();
        try {
            $cart = Cart::firstOrCreate(['user_id' => $user->id]);
            
            $success = $cart->addItem($request->product_id, $request->quantity);
            
            if (!$success) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Insufficient stock available.',
                ], 400);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Item added to cart successfully.',
                'cart' => $cart->load('items'),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to add item to cart.',
            ], 500);
        }
    }

    /**
     * Update cart item quantity.
     */
    public function updateItem(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:0',
        ]);

        $user = Auth::user();
        $cart = Cart::where('user_id', $user->id)->firstOrFail();

        DB::beginTransaction();
        try {
            $success = $cart->updateItem($id, $request->quantity);
            
            if (!$success) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Insufficient stock available.',
                ], 400);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Cart updated successfully.',
                'cart' => $cart->load('items'),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update cart.',
            ], 500);
        }
    }

    /**
     * Remove cart item.
     */
    public function removeItem($id)
    {
        $user = Auth::user();
        $cart = Cart::where('user_id', $user->id)->firstOrFail();

        DB::beginTransaction();
        try {
            $cart->removeItem($id);
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Item removed from cart.',
                'cart' => $cart->load('items'),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to remove item.',
            ], 500);
        }
    }

    /**
     * Clear entire cart.
     */
    public function clear()
    {
        $user = Auth::user();
        $cart = Cart::where('user_id', $user->id)->first();

        if ($cart) {
            DB::beginTransaction();
            try {
                $cart->clear();
                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Cart cleared.',
        ]);
    }

    /**
     * Show cart page view.
     */
    public function view(Request $request)
    {
        return view('cart');
    }
}

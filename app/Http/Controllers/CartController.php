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
                    'message' => __('cart.insufficient_stock'),
                ], 400);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => __('cart.added'),
                'cart' => $cart->load('items'),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Cart addItem failed', [
                'user_id' => $user->id,
                'product_id' => $request->product_id,
                'error' => $e->getMessage(),
            ]);
            return response()->json([
                'success' => false,
                'message' => __('cart.add_failed'),
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
                    'message' => __('cart.insufficient_stock'),
                ], 400);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => __('cart.updated'),
                'cart' => $cart->load('items'),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Cart updateItem failed', [
                'user_id' => $user->id,
                'item_id' => $id,
                'error' => $e->getMessage(),
            ]);
            return response()->json([
                'success' => false,
                'message' => __('cart.update_failed'),
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
                'message' => __('cart.item_removed'),
                'cart' => $cart->load('items'),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Cart removeItem failed', [
                'user_id' => $user->id,
                'item_id' => $id,
                'error' => $e->getMessage(),
            ]);
            return response()->json([
                'success' => false,
                'message' => __('cart.remove_failed'),
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
                Log::error('Cart clear failed', [
                    'user_id' => $user->id,
                    'error' => $e->getMessage(),
                ]);
                return response()->json([
                    'success' => false,
                    'message' => __('cart.clear_failed'),
                ], 500);
            }
        }

        return response()->json([
            'success' => true,
            'message' => __('cart.cleared'),
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

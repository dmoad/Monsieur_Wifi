<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Cart;
use App\Models\Address;
use App\Models\ShippingRate;
use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
class OrderController extends Controller
{
    /**
     * List user's orders.
     */
    public function index()
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication required.',
            ], 401);
        }
        
        Log::info('Order Controller :: index - Loading orders for user', [
            'user_id' => $user->id,
            'user_role' => $user->role
        ]);
        
        // Regular users only see their own orders
        // Admin users can see all orders via admin endpoints
        $orders = Order::with(['items.productModel', 'shippingAddress', 'billingAddress'])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json([
            'success' => true,
            'orders' => $orders,
        ]);
    }

    /**
     * Get order details with tracking info.
     */
    public function show($orderNumber)
    {
        Log::info('Order Controller :: show - Loading order details', ['order_number' => $orderNumber]);
        
        $user = Auth::user();
        
        if (!$user) {
            Log::error('Order Controller :: show - No authenticated user');
            return response()->json([
                'success' => false,
                'message' => 'Authentication required.',
            ], 401);
        }
        
        Log::info('Order Controller :: show - User authenticated', [
            'user_id' => $user->id,
            'user_email' => $user->email
        ]);

        $isAdmin = in_array($user->role, ['admin', 'superadmin']);

        $query = Order::with(['items.productModel.images', 'shippingAddress', 'billingAddress'])
            ->where('order_number', $orderNumber);

        if (!$isAdmin) {
            $query->where('user_id', $user->id);
        }

        $order = $query->firstOrFail();

        Log::info('Order Controller :: show - Order loaded successfully', [
            'order_id' => $order->id,
            'order_number' => $order->order_number
        ]);

        return response()->json($order);
    }

    /**
     * Create order from cart.
     */
    public function store(Request $request)
    {
        Log::info('Order Controller :: store - Starting order creation');
        Log::info('Request data:', $request->all());
        
        $request->validate([
            'shipping_address_id' => 'required|exists:addresses,id',
            'billing_address_id' => 'required|exists:addresses,id',
            'shipping_method' => 'required|in:normal,expedited',
            'notes' => 'nullable|string',
        ]);

        $user = Auth::user();
        
        if (!$user) {
            Log::error('Order Controller :: store - No authenticated user found');
            return response()->json([
                'success' => false,
                'message' => 'Authentication required.',
            ], 401);
        }
        
        Log::info('Order Controller :: store - User authenticated', [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'user_role' => $user->role
        ]);
        $cart = Cart::with('items.productModel.inventory')->where('user_id', $user->id)->first();

        if (!$cart || $cart->items->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Cart is empty.',
            ], 400);
        }

        // Validate addresses belong to user
        $shippingAddress = Address::where('id', $request->shipping_address_id)
            ->where('user_id', $user->id)
            ->firstOrFail();
        
        $billingAddress = Address::where('id', $request->billing_address_id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        // Validate inventory
        foreach ($cart->items as $item) {
            if ($item->productModel->inventory->getAvailableQuantity() < $item->quantity) {
                return response()->json([
                    'success' => false,
                    'message' => "Insufficient stock for {$item->productModel->name}.",
                ], 400);
            }
        }

        // Get shipping rate
        $shippingRate = ShippingRate::where('method', $request->shipping_method)
            ->where('is_active', true)
            ->firstOrFail();

        DB::beginTransaction();
        try {
            // Calculate amounts
            $productAmount = $cart->getTotal();
            $settings = SystemSetting::first();
            $taxRate = $settings ? $settings->tax_rate : 0.13;
            $taxAmount = round($productAmount * $taxRate, 2);
            $shippingCost = $shippingRate->cost;
            $total = $productAmount + $taxAmount + $shippingCost;

            // Get payment mode from settings
            $settings = SystemSetting::first();
            $paymentMode = $settings ? $settings->payment_mode : 'mock';
            
            // Create order
            $order = Order::create([
                'order_number' => Order::generateOrderNumber(),
                'user_id' => $user->id,
                'status' => 'pending',
                'product_amount' => $productAmount,
                'discount_amount' => 0,
                'tax_amount' => $taxAmount,
                'shipping_cost' => $shippingCost,
                'shipping_method' => $request->shipping_method,
                'total' => $total,
                'payment_method' => $paymentMode,
                'payment_status' => 'pending',
                'shipping_address_id' => $request->shipping_address_id,
                'billing_address_id' => $request->billing_address_id,
                'notes' => $request->notes,
            ]);

            // Create order items
            foreach ($cart->items as $cartItem) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_model_id' => $cartItem->product_model_id,
                    'quantity' => $cartItem->quantity,
                    'price' => $cartItem->price_at_add,
                    'subtotal' => $cartItem->getSubtotal(),
                ]);
            }
            
            // NOTE: For mock payment mode, orders are created with payment_status = 'pending'
            // Admin will manually confirm payment via the admin panel
            // For Stripe mode (future), payment will be confirmed via webhook
            
            DB::commit();

            // Clear cart after successful order creation
            $cart->clear();

            Log::info('Order Controller :: store - Order created successfully', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'total' => $order->total,
                'payment_status' => $order->payment_status,
                'payment_mode' => $paymentMode
            ]);

            // Return order with redirect URL
            return response()->json([
                'success' => true,
                'order_number' => $order->order_number,
                'redirect_url' => $paymentMode === 'stripe' ? "/api/v1/orders/{$order->order_number}/payment" : null,
                'payment_mode' => $paymentMode,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create order: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Process order completion (payment success endpoint).
     */
    public function success(Request $request, $orderNumber)
    {
        Log::info('Order Controller :: success');
        Log::info($request->all());
        Log::info($orderNumber);
        
        $user = Auth::user();
        $isAdmin = $user && in_array($user->role, ['admin', 'superadmin']);
        
        // Admin can process any order, regular users can only process their own
        $query = Order::with([
            'items.productModel.inventory',
            'user',
            'shippingAddress',
            'billingAddress'
        ])
            ->where('order_number', $orderNumber);
            
        if (!$isAdmin) {
            $query->where('user_id', $user->id);
        }
        
        $order = $query->firstOrFail();

        if ($order->payment_status === 'succeeded') {
            return response()->json([
                'success' => true,
                'message' => 'Order already processed.',
                'order' => $order,
            ]);
        }

        DB::beginTransaction();
        try {
            // Mark as paid
            $order->markAsPaid();

            // Deduct inventory
            foreach ($order->items as $item) {
                $item->productModel->inventory->deduct($item->quantity);
            }

            // Clear cart for the order owner (not the admin)
            $cart = Cart::where('user_id', $order->user_id)->first();
            if ($cart) {
                $cart->clear();
            }

            DB::commit();

            // TODO: Send order confirmed email
            // Mail::to($order->user->email)->send(new OrderConfirmedMail($order, $order->user->name, app()->getLocale()));

            return response()->json([
                'success' => true,
                'message' => 'Order completed successfully!',
                'order' => $order->fresh(),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            $order->markPaymentFailed('Payment processing failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to process order.',
            ], 500);
        }
    }

    /**
     * Show checkout page view.
     */
    public function checkoutView(Request $request)
    {
        return view('checkout');
    }

    /**
     * Show order list page view.
     */
    public function listView(Request $request)
    {
        return view('orders');
    }

    /**
     * Show order success page view.
     */
    public function successView(Request $request, $orderNumber)
    {
        $path = $request->path();
        $locale = (str_starts_with($path, 'fr/') || str_contains($path, '/fr/')) ? 'fr' : 'en';
        return view("order-success-{$locale}", ['orderNumber' => $orderNumber]);
    }
    
    /**
     * Generate and download invoice PDF for user's own order
     */
    public function downloadInvoice($orderNumber)
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication required.',
            ], 401);
        }
        
        try {
            // Get order belonging to authenticated user
            $order = Order::with([
                'user',
                'items.productModel',
                'shippingAddress',
                'billingAddress'
            ])
            ->where('order_number', $orderNumber)
            ->where('user_id', $user->id)
            ->firstOrFail();
            
            // Only allow invoice download for paid orders
            if ($order->payment_status !== 'succeeded') {
                return response()->json([
                    'success' => false,
                    'message' => 'Invoice is only available for paid orders.',
                ], 400);
            }
            
            // Generate PDF
            $pdf = Pdf::loadView('invoices.order-invoice', ['order' => $order]);
            
            // Set PDF options
            $pdf->setPaper('a4', 'portrait');
            
            // Return PDF download
            return $pdf->download("invoice-{$orderNumber}.pdf");
            
        } catch (\Exception $e) {
            Log::error('Failed to generate invoice for user', [
                'order_number' => $orderNumber,
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate invoice.',
            ], 500);
        }
    }
}

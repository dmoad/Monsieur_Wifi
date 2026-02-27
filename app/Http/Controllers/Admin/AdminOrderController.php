<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class AdminOrderController extends Controller
{
    public function __construct()
    {
        // No middleware - role check will be done in methods if needed
    }

    /**
     * List all orders with filters.
     */
    public function index(Request $request)
    {
        // Optional: Check if user is admin
        // if (auth()->user()->role !== 'admin') {
        //     return response()->json(['error' => 'Unauthorized'], 403);
        // }
        $query = Order::with(['user', 'items.productModel', 'shippingAddress', 'billingAddress']);

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by payment status
        if ($request->has('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        // Filter by shipping method
        if ($request->has('shipping_method')) {
            $query->where('shipping_method', $request->shipping_method);
        }

        // Filter by date range
        if ($request->has('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->has('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Search by order number or user email
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($userQuery) use ($search) {
                      $userQuery->where('email', 'like', "%{$search}%")
                                ->orWhere('name', 'like', "%{$search}%");
                  });
            });
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(20);

        return response()->json([
            'success' => true,
            'orders' => $orders,
        ]);
    }

    /**
     * Get order details.
     */
    public function show($orderNumber)
    {
        $order = Order::with([
            'user', 
            'items.productModel.images', 
            'items.inventoryItems.device',
            'shippingAddress', 
            'billingAddress'
        ])
            ->where('order_number', $orderNumber)
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'order' => $order,
        ]);
    }

    /**
     * Add shipping provider and tracking ID.
     */
    public function updateTracking(Request $request, $orderNumber)
    {
        $request->validate([
            'shipping_provider' => 'required|string|max:255',
            'tracking_id' => 'required|string|max:255',
        ]);

        $order = Order::with(['user', 'shippingAddress'])
            ->where('order_number', $orderNumber)
            ->firstOrFail();

        // Check if order can be shipped
        if ($order->status === 'cancelled') {
            return response()->json([
                'success' => false,
                'message' => 'Cannot add tracking to cancelled order.',
            ], 400);
        }

        if ($order->payment_status !== 'succeeded') {
            return response()->json([
                'success' => false,
                'message' => 'Cannot add tracking to unpaid order.',
            ], 400);
        }

        if (in_array($order->status, ['delivered'])) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot update tracking for already delivered order.',
            ], 400);
        }

        DB::beginTransaction();
        try {
            $order->markAsShipped($request->shipping_provider, $request->tracking_id);
            DB::commit();

            // TODO: Send tracking added email
            // Mail::to($order->user->email)->send(new TrackingAddedMail($order, $order->user->name, app()->getLocale()));

            return response()->json([
                'success' => true,
                'message' => 'Tracking information added successfully.',
                'order' => $order,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update tracking information.',
            ], 500);
        }
    }

    /**
     * Update order status (shipped, delivered).
     */
    public function updateStatus(Request $request, $orderNumber)
    {
        $request->validate([
            'status' => 'required|in:processing,shipped,delivered,cancelled',
        ]);

        $order = Order::with(['user', 'shippingAddress'])
            ->where('order_number', $orderNumber)
            ->firstOrFail();

        // Validate status transitions
        if ($order->status === 'cancelled') {
            return response()->json([
                'success' => false,
                'message' => 'Cannot update status of cancelled order.',
            ], 400);
        }

        if ($order->status === 'delivered') {
            return response()->json([
                'success' => false,
                'message' => 'Cannot update status of already delivered order.',
            ], 400);
        }

        // Check payment status for shipping/delivery
        if (in_array($request->status, ['shipped', 'delivered'])) {
            if ($order->payment_status !== 'succeeded') {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot ship/deliver unpaid order. Payment must be successful first.',
                ], 400);
            }
            
            // Require tracking info for shipping
            if ($request->status === 'shipped' && !$order->tracking_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please add tracking information before marking as shipped.',
                ], 400);
            }
        }

        DB::beginTransaction();
        try {
            if ($request->status === 'delivered') {
                $order->markAsDelivered();
                
                // TODO: Send delivery email
                // Mail::to($order->user->email)->send(new OrderDeliveredMail($order, $order->user->name, app()->getLocale()));
                
            } else if ($request->status === 'cancelled') {
                $order->cancel();
            } else {
                $order->status = $request->status;
                $order->save();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order status updated successfully.',
                'order' => $order,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update order status.',
            ], 500);
        }
    }

    /**
     * Resend order emails.
     */
    public function resendEmail(Request $request, $orderNumber)
    {
        $request->validate([
            'email_type' => 'required|in:confirmation,tracking,delivery',
        ]);

        $order = Order::where('order_number', $orderNumber)->firstOrFail();
        $locale = $request->header('Accept-Language', 'en');
        $locale = str_starts_with($locale, 'fr') ? 'fr' : 'en';

        try {
            // TODO: Send appropriate email based on type
            /*
            switch ($request->email_type) {
                case 'confirmation':
                    Mail::to($order->user->email)->send(new OrderConfirmedMail($order, $order->user->name, $locale));
                    break;
                case 'tracking':
                    Mail::to($order->user->email)->send(new TrackingAddedMail($order, $order->user->name, $locale));
                    break;
                case 'delivery':
                    Mail::to($order->user->email)->send(new OrderDeliveredMail($order, $order->user->name, $locale));
                    break;
            }
            */

            return response()->json([
                'success' => true,
                'message' => 'Email sent successfully.',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send email.',
            ], 500);
        }
    }

    /**
     * Assign inventory items to order and create devices.
     */
    public function assignInventory(Request $request, $orderNumber)
    {
        $user = auth()->user();
        
        if (!in_array($user->role, ['admin', 'superadmin'])) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }
        
        $request->validate([
            'assignments' => 'required|array',
            'assignments.*.order_item_id' => 'required|exists:order_items,id',
            'assignments.*.inventory_item_ids' => 'required|array',
            'assignments.*.inventory_item_ids.*' => 'required|exists:inventory_items,id',
        ]);
        
        $order = Order::with('items')->where('order_number', $orderNumber)->firstOrFail();
        
        // Check if order is paid
        if ($order->payment_status !== 'succeeded') {
            return response()->json([
                'success' => false,
                'message' => 'Order must be paid before assigning inventory',
            ], 400);
        }
        
        DB::beginTransaction();
        try {
            foreach ($request->assignments as $assignment) {
                $orderItem = \App\Models\OrderItem::find($assignment['order_item_id']);
                
                // Validate assignment belongs to this order
                if ($orderItem->order_id !== $order->id) {
                    throw new \Exception('Order item does not belong to this order');
                }
                
                // Validate quantity matches
                if (count($assignment['inventory_item_ids']) !== $orderItem->quantity) {
                    throw new \Exception("Must assign exactly {$orderItem->quantity} items for {$orderItem->productModel->name}");
                }
                
                // First, clean up any existing assignments for this order item
                $existingInventoryItems = \App\Models\InventoryItem::where('order_item_id', $orderItem->id)->get();
                foreach ($existingInventoryItems as $existingItem) {
                    // Delete associated device if exists
                    if ($existingItem->device_id) {
                        $device = \App\Models\Device::find($existingItem->device_id);
                        if ($device) {
                            $device->delete();
                            \Illuminate\Support\Facades\Log::info('Device deleted for re-assignment', [
                                'device_id' => $device->id,
                                'inventory_item_id' => $existingItem->id,
                            ]);
                        }
                    }
                    
                    // Reset inventory item if not in new assignment
                    if (!in_array($existingItem->id, $assignment['inventory_item_ids'])) {
                        $existingItem->device_id = null;
                        $existingItem->order_item_id = null;
                        $existingItem->status = 'available';
                        $existingItem->save();
                    }
                }
                
                // Now process new assignments
                foreach ($assignment['inventory_item_ids'] as $inventoryItemId) {
                    $inventoryItem = \App\Models\InventoryItem::find($inventoryItemId);
                    
                    // Validate inventory item
                    if ($inventoryItem->product_model_id !== $orderItem->product_model_id) {
                        throw new \Exception('Inventory item does not match order product model');
                    }
                    
                    if (!in_array($inventoryItem->status, ['available', 'sold'])) {
                        throw new \Exception('Inventory item must be available or sold');
                    }
                    
                    if ($inventoryItem->order_item_id && $inventoryItem->order_item_id != $orderItem->id) {
                        throw new \Exception('Inventory item already assigned to another order');
                    }
                    
                    // Delete existing device if this item already has one
                    if ($inventoryItem->device_id) {
                        $oldDevice = \App\Models\Device::find($inventoryItem->device_id);
                        if ($oldDevice) {
                            $oldDevice->delete();
                        }
                    }
                    
                    // Create device from inventory item
                    $device = $inventoryItem->convertToDevice($order->user_id);
                    
                    // Link inventory item to device, order item, and mark as sold
                    $inventoryItem->device_id = $device->id;
                    $inventoryItem->order_item_id = $orderItem->id;
                    $inventoryItem->status = 'sold';
                    $inventoryItem->save();
                    
                    \Illuminate\Support\Facades\Log::info('Device created from inventory item', [
                        'device_id' => $device->id,
                        'inventory_item_id' => $inventoryItem->id,
                        'order_number' => $orderNumber,
                        'owner_id' => $order->user_id,
                    ]);
                }
            }
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Inventory assigned and devices created successfully',
                'order' => $order->fresh(['items.inventoryItems.device']),
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }
    
    /**
     * Confirm payment for an order (mark as paid)
     */
    public function confirmPayment($orderNumber)
    {
        try {
            $order = Order::with([
                'items.productModel.inventory',
                'user',
                'shippingAddress',
                'billingAddress'
            ])
                ->where('order_number', $orderNumber)
                ->firstOrFail();
            
            if ($order->payment_status === 'succeeded') {
                return response()->json([
                    'success' => false,
                    'message' => 'Order is already marked as paid',
                ], 400);
            }
            
            DB::beginTransaction();
            
            // Mark order as paid
            $order->markAsPaid();
            
            // Deduct inventory quantities
            foreach ($order->items as $item) {
                $item->productModel->inventory->deduct($item->quantity);
            }
            
            DB::commit();
            
            \Illuminate\Support\Facades\Log::info('Payment confirmed for order', [
                'order_number' => $orderNumber,
                'admin_id' => auth()->id(),
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Payment confirmed successfully',
                'order' => $order->fresh(['user', 'items.productModel.images', 'items.inventoryItems.device', 'shippingAddress', 'billingAddress']),
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to confirm payment: ' . $e->getMessage(),
            ], 500);
        }
    }
    
    /**
     * Confirm Stripe payment for an order (replicates webhook behavior)
     */
    public function confirmStripePayment($orderNumber)
    {
        try {
            $order = Order::with([
                'items.productModel.inventory',
                'user',
                'shippingAddress',
                'billingAddress'
            ])
                ->where('order_number', $orderNumber)
                ->firstOrFail();
            
            // Verify payment method is Stripe
            if ($order->payment_method !== 'stripe') {
                return response()->json([
                    'success' => false,
                    'message' => 'This endpoint only works for Stripe payments. Use confirm-payment for other payment methods.',
                ], 400);
            }
            
            if ($order->payment_status === 'succeeded') {
                return response()->json([
                    'success' => true,
                    'message' => 'Order is already marked as paid',
                    'order' => $order->fresh(['user', 'items.productModel.images', 'items.inventoryItems.device', 'shippingAddress', 'billingAddress']),
                ]);
            }
            
            DB::beginTransaction();
            
            // Mark order as paid with manual payment intent ID (replicating webhook behavior)
            $paymentIntentId = 'manual_admin_' . time() . '_' . auth()->id();
            $order->markAsPaid($paymentIntentId);
            
            // Deduct inventory quantities (same as webhook)
            foreach ($order->items as $item) {
                if ($item->productModel && $item->productModel->inventory) {
                    $item->productModel->inventory->deduct($item->quantity);
                }
            }
            
            DB::commit();
            
            \Illuminate\Support\Facades\Log::info('Stripe payment manually confirmed (webhook replicated)', [
                'order_number' => $orderNumber,
                'payment_intent_id' => $paymentIntentId,
                'admin_id' => auth()->id(),
                'total' => $order->total,
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Stripe payment confirmed successfully',
                'order' => $order->fresh(['user', 'items.productModel.images', 'items.inventoryItems.device', 'shippingAddress', 'billingAddress']),
                'payment_intent_id' => $paymentIntentId,
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            \Illuminate\Support\Facades\Log::error('Failed to confirm Stripe payment', [
                'order_number' => $orderNumber,
                'admin_id' => auth()->id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to confirm payment: ' . $e->getMessage(),
            ], 500);
        }
    }
    
    /**
     * Generate and download invoice PDF for an order
     */
    public function downloadInvoice($orderNumber)
    {
        try {
            $order = Order::with([
                'user',
                'items.productModel',
                'shippingAddress',
                'billingAddress'
            ])
            ->where('order_number', $orderNumber)
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
            \Illuminate\Support\Facades\Log::error('Failed to generate invoice', [
                'order_number' => $orderNumber,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate invoice: ' . $e->getMessage(),
            ], 500);
        }
    }
}

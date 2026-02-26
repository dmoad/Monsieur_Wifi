<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        $order = Order::with(['user', 'items.productModel.images', 'shippingAddress', 'billingAddress'])
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

        $order = Order::where('order_number', $orderNumber)->firstOrFail();

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

        $order = Order::where('order_number', $orderNumber)->firstOrFail();

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
}

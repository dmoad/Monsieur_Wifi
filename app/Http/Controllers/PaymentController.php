<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Stripe\Webhook;
use Stripe\Exception\SignatureVerificationException;

class PaymentController extends Controller
{
    /**
     * Create a Stripe Payment Intent for an order.
     */
    public function createPaymentIntent($orderNumber)
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication required.',
            ], 401);
        }
        
        // Get order belonging to authenticated user
        $order = Order::where('order_number', $orderNumber)
            ->where('user_id', $user->id)
            ->firstOrFail();
        
        // Check if order already has a payment intent
        if ($order->payment_intent_id) {
            try {
                Stripe::setApiKey(config('services.stripe.secret'));
                $intent = PaymentIntent::retrieve($order->payment_intent_id);
                
                // If payment intent is still valid, return it
                if (in_array($intent->status, ['requires_payment_method', 'requires_confirmation', 'requires_action'])) {
                    return response()->json([
                        'success' => true,
                        'client_secret' => $intent->client_secret,
                        'publishable_key' => config('services.stripe.key'),
                    ]);
                }
            } catch (\Exception $e) {
                Log::warning('Failed to retrieve existing payment intent', [
                    'order_number' => $orderNumber,
                    'payment_intent_id' => $order->payment_intent_id,
                    'error' => $e->getMessage()
                ]);
            }
        }
        
        try {
            Stripe::setApiKey(config('services.stripe.secret'));
            
            $paymentIntent = PaymentIntent::create([
                'amount' => $order->getAmountInCents(),
                'currency' => 'cad',
                'metadata' => [
                    'order_number' => $order->order_number,
                    'user_id' => $user->id,
                ],
                'description' => "Order {$order->order_number}",
            ]);
            
            // Save payment intent ID to order
            $order->payment_intent_id = $paymentIntent->id;
            $order->save();
            
            Log::info('Payment intent created', [
                'order_number' => $orderNumber,
                'payment_intent_id' => $paymentIntent->id,
                'amount' => $order->total,
            ]);
            
            return response()->json([
                'success' => true,
                'client_secret' => $paymentIntent->client_secret,
                'publishable_key' => config('services.stripe.key'),
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to create payment intent', [
                'order_number' => $orderNumber,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to initialize payment. Please try again.',
            ], 500);
        }
    }
    
    /**
     * Handle Stripe webhook events.
     */
    public function handleWebhook(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $webhookSecret = config('services.stripe.webhook.secret');
        
        Log::info('Webhook received', [
            'has_signature' => !empty($sigHeader),
            'has_secret' => !empty($webhookSecret),
            'payload_length' => strlen($payload)
        ]);
        
        if (!$webhookSecret) {
            Log::error('Stripe webhook secret not configured');
            return response()->json(['error' => 'Webhook secret not configured'], 500);
        }
        
        try {
            $event = Webhook::constructEvent(
                $payload,
                $sigHeader,
                $webhookSecret
            );
        } catch (SignatureVerificationException $e) {
            Log::error('Webhook signature verification failed', [
                'error' => $e->getMessage(),
                'signature_present' => !empty($sigHeader),
                'secret_configured' => !empty($webhookSecret),
                'secret_prefix' => substr($webhookSecret, 0, 10) . '...'
            ]);
            return response()->json(['error' => 'Invalid signature'], 400);
        } catch (\Exception $e) {
            Log::error('Webhook error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'Webhook error'], 400);
        }
        
        Log::info('Stripe webhook received', [
            'type' => $event->type,
            'id' => $event->id
        ]);
        
        // Handle the event
        switch ($event->type) {
            case 'payment_intent.succeeded':
                $this->handlePaymentIntentSucceeded($event->data->object);
                break;
                
            case 'payment_intent.payment_failed':
                $this->handlePaymentIntentFailed($event->data->object);
                break;
                
            default:
                Log::info('Unhandled webhook event type', ['type' => $event->type]);
        }
        
        return response()->json(['success' => true]);
    }
    
    /**
     * Handle successful payment intent.
     */
    protected function handlePaymentIntentSucceeded($paymentIntent)
    {
        $orderNumber = $paymentIntent->metadata->order_number ?? null;
        
        if (!$orderNumber) {
            Log::error('Payment intent succeeded but no order number in metadata', [
                'payment_intent_id' => $paymentIntent->id
            ]);
            return;
        }
        
        $order = Order::with([
            'items.productModel.inventory',
            'user',
            'shippingAddress',
            'billingAddress'
        ])
            ->where('order_number', $orderNumber)
            ->first();
        
        if (!$order) {
            Log::error('Order not found for payment intent', [
                'order_number' => $orderNumber,
                'payment_intent_id' => $paymentIntent->id
            ]);
            return;
        }
        
        if ($order->payment_status === 'succeeded') {
            Log::info('Order already marked as paid', [
                'order_number' => $orderNumber
            ]);
            return;
        }
        
        DB::beginTransaction();
        try {
            // Mark order as paid
            $order->markAsPaid($paymentIntent->id);
            
            // Deduct inventory
            foreach ($order->items as $item) {
                if ($item->productModel && $item->productModel->inventory) {
                    $item->productModel->inventory->deduct($item->quantity);
                }
            }
            
            DB::commit();
            
            Log::info('Payment processed successfully', [
                'order_number' => $orderNumber,
                'payment_intent_id' => $paymentIntent->id
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Failed to process successful payment', [
                'order_number' => $orderNumber,
                'payment_intent_id' => $paymentIntent->id,
                'error' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Handle failed payment intent.
     */
    protected function handlePaymentIntentFailed($paymentIntent)
    {
        $orderNumber = $paymentIntent->metadata->order_number ?? null;
        
        if (!$orderNumber) {
            Log::error('Payment intent failed but no order number in metadata', [
                'payment_intent_id' => $paymentIntent->id
            ]);
            return;
        }
        
        $order = Order::with(['user', 'items.productModel'])
            ->where('order_number', $orderNumber)
            ->first();
        
        if (!$order) {
            Log::error('Order not found for failed payment intent', [
                'order_number' => $orderNumber,
                'payment_intent_id' => $paymentIntent->id
            ]);
            return;
        }
        
        $failureMessage = $paymentIntent->last_payment_error->message ?? 'Payment failed';
        $order->markPaymentFailed($failureMessage);
        
        Log::info('Payment marked as failed', [
            'order_number' => $orderNumber,
            'payment_intent_id' => $paymentIntent->id,
            'reason' => $failureMessage
        ]);
    }
    
    /**
     * Verify payment with Stripe API and mark order as paid
     */
    public function verifyAndConfirmPayment($orderNumber)
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication required.',
            ], 401);
        }
        
        try {
            // Get order belonging to authenticated user with all relationships needed for email
            $order = Order::with([
                'items.productModel.inventory',
                'user',
                'shippingAddress',
                'billingAddress'
            ])
                ->where('order_number', $orderNumber)
                ->where('user_id', $user->id)
                ->first();
            
            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => 'Order not found.',
                ], 404);
            }
            
            // Verify payment method is Stripe
            if ($order->payment_method !== 'stripe') {
                return response()->json([
                    'success' => false,
                    'message' => 'This endpoint only works for Stripe payments.',
                ], 400);
            }
            
            // Check if already paid
            if ($order->payment_status === 'succeeded') {
                return response()->json([
                    'success' => true,
                    'message' => 'Order is already marked as paid.',
                    'order' => [
                        'order_number' => $order->order_number,
                        'payment_status' => $order->payment_status,
                        'total' => $order->total,
                    ],
                ]);
            }
            
            // Verify payment with Stripe API
            if (!$order->payment_intent_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'No payment intent found for this order.',
                ], 400);
            }
            
            Stripe::setApiKey(config('services.stripe.secret'));
            $paymentIntent = PaymentIntent::retrieve($order->payment_intent_id);
            
            Log::info('Payment intent retrieved from Stripe', [
                'order_number' => $orderNumber,
                'payment_intent_id' => $paymentIntent->id,
                'status' => $paymentIntent->status,
                'amount' => $paymentIntent->amount / 100,
            ]);
            
            // Verify payment succeeded
            if ($paymentIntent->status !== 'succeeded') {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment has not been completed. Status: ' . $paymentIntent->status,
                    'payment_status' => $paymentIntent->status,
                ], 400);
            }
            
            // Verify amount matches
            $expectedAmount = (int)($order->total * 100); // Convert to cents
            if ($paymentIntent->amount !== $expectedAmount) {
                Log::warning('Payment amount mismatch', [
                    'order_number' => $orderNumber,
                    'expected' => $expectedAmount,
                    'actual' => $paymentIntent->amount,
                ]);
            }
            
            DB::beginTransaction();
            
            // Mark order as paid
            $order->markAsPaid($paymentIntent->id);
            
            // Deduct inventory
            foreach ($order->items as $item) {
                if ($item->productModel && $item->productModel->inventory) {
                    $item->productModel->inventory->deduct($item->quantity);
                }
            }
            
            DB::commit();
            
            Log::info('Payment verified and confirmed via API', [
                'order_number' => $orderNumber,
                'payment_intent_id' => $paymentIntent->id,
                'user_id' => $user->id,
                'total' => $order->total,
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Payment verified and confirmed successfully.',
                'order' => [
                    'order_number' => $order->order_number,
                    'payment_status' => $order->payment_status,
                    'order_status' => $order->status,
                    'total' => $order->total,
                    'paid_at' => $order->paid_at,
                ],
            ]);
            
        } catch (\Stripe\Exception\ApiErrorException $e) {
            Log::error('Stripe API error while verifying payment', [
                'order_number' => $orderNumber,
                'error' => $e->getMessage(),
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to verify payment with Stripe: ' . $e->getMessage(),
            ], 500);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Failed to verify and confirm payment', [
                'order_number' => $orderNumber,
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to process payment. Please try again.',
            ], 500);
        }
    }
}

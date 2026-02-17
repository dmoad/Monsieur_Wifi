<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Laravel\Cashier\Exceptions\IncompletePayment;
use Log;

class SubscriptionController extends Controller
{
    /**
     * Get available subscription plans
     */
    public function plans()
    {
        $plans = [
            [
                'id' => 'starter',
                'name' => 'Starter',
                'price' => 29,
                'currency' => 'EUR',
                'interval' => 'month',
                'features' => [
                    '1 borne WiFi',
                    '100 connexions/mois',
                    'Portail captif personnalisé',
                    'Support email',
                ],
                'stripe_price_id' => env('STRIPE_PRICE_STARTER'),
            ],
            [
                'id' => 'business',
                'name' => 'Business',
                'price' => 79,
                'currency' => 'EUR',
                'interval' => 'month',
                'features' => [
                    '5 bornes WiFi',
                    'Connexions illimitées',
                    'Portail captif personnalisé',
                    'Analytiques avancées',
                    'Support prioritaire',
                ],
                'stripe_price_id' => env('STRIPE_PRICE_BUSINESS'),
                'popular' => true,
            ],
            [
                'id' => 'enterprise',
                'name' => 'Enterprise',
                'price' => 199,
                'currency' => 'EUR',
                'interval' => 'month',
                'features' => [
                    'Bornes illimitées',
                    'Connexions illimitées',
                    'Portail captif personnalisé',
                    'Analytiques avancées',
                    'API complète',
                    'Support dédié 24/7',
                ],
                'stripe_price_id' => env('STRIPE_PRICE_ENTERPRISE'),
            ],
        ];

        return response()->json([
            'success' => true,
            'plans' => $plans,
        ]);
    }

    /**
     * Create a checkout session for subscription
     */
    public function createCheckoutSession(Request $request)
    {
        $request->validate([
            'price_id' => 'required|string',
            'plan_name' => 'required|string',
        ]);

        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'error' => 'User not authenticated',
                ], 401);
            }

            // Create or get Stripe customer
            $user->createOrGetStripeCustomer();

            // Create checkout session
            $checkout = $user->newSubscription('default', $request->price_id)
                ->checkout([
                    'success_url' => url('/subscription/success?session_id={CHECKOUT_SESSION_ID}'),
                    'cancel_url' => url('/subscription/cancel'),
                    'billing_address_collection' => 'required',
                    'shipping_address_collection' => [
                        'allowed_countries' => ['FR', 'BE', 'CH', 'LU', 'MC', 'CA'],
                    ],
                    'metadata' => [
                        'user_id' => $user->id,
                        'plan_name' => $request->plan_name,
                    ],
                ]);

            return response()->json([
                'success' => true,
                'checkout_url' => $checkout->url,
                'session_id' => $checkout->id,
            ]);

        } catch (\Exception $e) {
            Log::error('Checkout session creation failed', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Failed to create checkout session: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Create a payment intent for one-time payment (device order)
     */
    public function createPaymentIntent(Request $request)
    {
        $request->validate([
            'amount' => 'required|integer|min:100', // Amount in cents
            'description' => 'required|string',
        ]);

        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'error' => 'User not authenticated',
                ], 401);
            }

            // Create or get Stripe customer
            $user->createOrGetStripeCustomer();

            // Create payment intent
            $paymentIntent = $user->pay($request->amount, [
                'description' => $request->description,
                'metadata' => [
                    'user_id' => $user->id,
                    'type' => 'device_order',
                ],
            ]);

            return response()->json([
                'success' => true,
                'client_secret' => $paymentIntent->client_secret,
                'payment_intent_id' => $paymentIntent->id,
            ]);

        } catch (\Exception $e) {
            Log::error('Payment intent creation failed', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Failed to create payment intent: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get current user's subscription status
     */
    public function status()
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'error' => 'User not authenticated',
            ], 401);
        }

        $subscription = $user->subscription('default');

        return response()->json([
            'success' => true,
            'has_subscription' => $user->subscribed('default'),
            'subscription' => $subscription ? [
                'name' => $subscription->name,
                'stripe_status' => $subscription->stripe_status,
                'ends_at' => $subscription->ends_at,
                'on_trial' => $subscription->onTrial(),
                'cancelled' => $subscription->cancelled(),
                'on_grace_period' => $subscription->onGracePeriod(),
            ] : null,
        ]);
    }

    /**
     * Cancel subscription
     */
    public function cancel()
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'error' => 'User not authenticated',
            ], 401);
        }

        try {
            $user->subscription('default')->cancel();

            return response()->json([
                'success' => true,
                'message' => 'Subscription cancelled successfully',
            ]);

        } catch (\Exception $e) {
            Log::error('Subscription cancellation failed', [
                'error' => $e->getMessage(),
                'user_id' => $user->id,
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Failed to cancel subscription',
            ], 500);
        }
    }

    /**
     * Resume cancelled subscription
     */
    public function resume()
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'error' => 'User not authenticated',
            ], 401);
        }

        try {
            $user->subscription('default')->resume();

            return response()->json([
                'success' => true,
                'message' => 'Subscription resumed successfully',
            ]);

        } catch (\Exception $e) {
            Log::error('Subscription resume failed', [
                'error' => $e->getMessage(),
                'user_id' => $user->id,
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Failed to resume subscription',
            ], 500);
        }
    }

    /**
     * Get billing portal URL
     */
    public function billingPortal()
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'error' => 'User not authenticated',
            ], 401);
        }

        try {
            $url = $user->billingPortalUrl(url('/subscription'));

            return response()->json([
                'success' => true,
                'url' => $url,
            ]);

        } catch (\Exception $e) {
            Log::error('Billing portal URL generation failed', [
                'error' => $e->getMessage(),
                'user_id' => $user->id,
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Failed to generate billing portal URL',
            ], 500);
        }
    }

    /**
     * Handle successful subscription
     */
    public function success(Request $request)
    {
        return view('subscription.success');
    }

    /**
     * Handle cancelled subscription
     */
    public function cancelled()
    {
        return view('subscription.cancel');
    }
}

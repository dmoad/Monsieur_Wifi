<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Mail\SubscriptionConfirmedMail;
use App\Mail\NewSubscriptionAdminNotification;
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

            // Create checkout session with subscription + deposit
            \Stripe\Stripe::setApiKey(config('cashier.secret'));

            $checkout = \Stripe\Checkout\Session::create([
                'customer' => $user->stripe_id,
                'customer_update' => [
                    'name' => 'auto',
                    'address' => 'auto',
                    'shipping' => 'auto',
                ],
                'mode' => 'subscription',
                'line_items' => [
                    [
                        'price' => $request->price_id,
                        'quantity' => 1,
                    ],
                    [
                        'price_data' => [
                            'currency' => 'eur',
                            'product_data' => [
                                'name' => 'Caution borne WiFi',
                                'description' => 'Caution remboursable pour la borne WiFi Monsieur WiFi',
                            ],
                            'unit_amount' => 5000, // 50€ in cents
                        ],
                        'quantity' => 1,
                    ],
                ],
                'success_url' => url('/subscription/success?session_id={CHECKOUT_SESSION_ID}'),
                'cancel_url' => url('/subscription/cancel'),
                'billing_address_collection' => 'required',
                'phone_number_collection' => ['enabled' => true],
                'tax_id_collection' => ['enabled' => true],
                'shipping_address_collection' => [
                    'allowed_countries' => ['FR', 'BE', 'CH', 'LU', 'MC', 'CA'],
                ],
                'consent_collection' => [
                    'terms_of_service' => 'required',
                ],
                'custom_text' => [
                    'submit' => [
                        'message' => 'Votre abonnement inclut : une borne WiFi avec portail captif pré-paramétré et une assistance à la mise en service. Une caution de 50€ est appliquée.',
                    ],
                    'terms_of_service_acceptance' => [
                        'message' => 'J\'accepte les [Conditions Générales de Vente](' . config('services.stripe.terms_url', 'https://monsieur-wifi.com/cgv') . ')',
                    ],
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

        // Sync subscription status with Stripe if subscription exists
        if ($subscription && $user->stripe_id) {
            try {
                $stripe = new \Stripe\StripeClient(config('cashier.secret'));
                $stripeSubscription = $stripe->subscriptions->retrieve($subscription->stripe_id);

                $endsAt = null;
                if ($stripeSubscription->cancel_at) {
                    $endsAt = \Carbon\Carbon::createFromTimestamp($stripeSubscription->cancel_at);
                }

                $subscription->update([
                    'stripe_status' => $stripeSubscription->status,
                    'ends_at' => $endsAt,
                ]);

                $subscription->refresh();
            } catch (\Exception $e) {
                Log::error('Failed to sync subscription status', ['error' => $e->getMessage()]);
            }
        }

        return response()->json([
            'success' => true,
            'has_subscription' => $user->subscribed('default'),
            'subscription' => $subscription ? [
                'name' => $subscription->name,
                'stripe_status' => $subscription->stripe_status,
                'ends_at' => $subscription->ends_at,
                'on_trial' => $subscription->onTrial(),
                'cancelled' => $subscription->canceled(),
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
            $url = $user->billingPortalUrl(url('/en/profile'));

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
     * Confirm subscription after Stripe checkout by syncing the session data
     */
    public function confirm(Request $request)
    {
        $request->validate([
            'session_id' => 'required|string',
        ]);

        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'error' => 'User not authenticated',
                ], 401);
            }

            $stripe = new \Stripe\StripeClient(config('cashier.secret'));
            $session = $stripe->checkout->sessions->retrieve($request->session_id, [
                'expand' => ['subscription'],
            ]);

            // Verify this session belongs to this user
            if ($session->customer !== $user->stripe_id) {
                return response()->json([
                    'success' => false,
                    'error' => 'Session does not belong to this user',
                ], 403);
            }

            if ($session->status !== 'complete' || !$session->subscription) {
                return response()->json([
                    'success' => false,
                    'error' => 'Checkout session is not complete',
                ], 400);
            }

            $stripeSubscription = $session->subscription;

            // Check if subscription already exists in DB
            if (!$user->subscribed('default')) {
                // Create subscription record in DB
                $user->subscriptions()->create([
                    'type' => 'default',
                    'stripe_id' => is_string($stripeSubscription) ? $stripeSubscription : $stripeSubscription->id,
                    'stripe_status' => is_string($stripeSubscription) ? 'active' : $stripeSubscription->status,
                    'stripe_price' => is_string($stripeSubscription) ? null : ($stripeSubscription->items->data[0]->price->id ?? null),
                    'quantity' => 1,
                ]);

                Log::info('Subscription synced from checkout session', [
                    'user_id' => $user->id,
                    'session_id' => $request->session_id,
                ]);

                // Send confirmation email (only if subscription was just created here, not already by webhook)
                if (!is_string($stripeSubscription)) {
                    $this->sendSubscriptionConfirmationEmail($user, $stripeSubscription, $session);
                }
            }

            return response()->json([
                'success' => true,
                'has_subscription' => true,
            ]);

        } catch (\Exception $e) {
            Log::error('Subscription confirmation failed', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
                'session_id' => $request->session_id,
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Failed to confirm subscription',
            ], 500);
        }
    }

    /**
     * Handle Stripe webhooks for subscriptions
     */
    public function handleWebhook(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $webhookSecret = config('services.stripe.webhook.secret');

        // If webhook secret is configured, verify signature
        if ($webhookSecret) {
            try {
                $event = \Stripe\Webhook::constructEvent($payload, $sigHeader, $webhookSecret);
            } catch (\Exception $e) {
                Log::error('Stripe subscription webhook signature failed', ['error' => $e->getMessage()]);
                return response()->json(['error' => 'Invalid signature'], 400);
            }
        } else {
            // Dev mode: parse without signature verification
            $event = json_decode($payload);
            if (!$event || !isset($event->type)) {
                return response()->json(['error' => 'Invalid payload'], 400);
            }
        }

        Log::info('Stripe subscription webhook received', ['type' => $event->type]);

        switch ($event->type) {
            case 'checkout.session.completed':
                $this->handleCheckoutCompleted($event->data->object);
                break;

            case 'customer.subscription.created':
            case 'customer.subscription.updated':
                $this->handleSubscriptionUpdated($event->data->object);
                break;

            case 'customer.subscription.deleted':
                $this->handleSubscriptionDeleted($event->data->object);
                break;

            default:
                Log::info('Unhandled subscription webhook event', ['type' => $event->type]);
        }

        return response()->json(['success' => true]);
    }

    protected function handleCheckoutCompleted($session)
    {
        if ($session->mode !== 'subscription') {
            return;
        }

        $user = User::where('stripe_id', $session->customer)->first();
        if (!$user) {
            Log::error('Checkout completed but user not found', ['customer' => $session->customer]);
            return;
        }

        // If subscription not yet in DB, create it
        if (!$user->subscribed('default')) {
            $subscriptionId = $session->subscription;

            $stripe = new \Stripe\StripeClient(config('cashier.secret'));
            $stripeSubscription = $stripe->subscriptions->retrieve($subscriptionId);

            $user->subscriptions()->create([
                'type' => 'default',
                'stripe_id' => $stripeSubscription->id,
                'stripe_status' => $stripeSubscription->status,
                'stripe_price' => $stripeSubscription->items->data[0]->price->id ?? null,
                'quantity' => 1,
            ]);

            Log::info('Subscription created from webhook', [
                'user_id' => $user->id,
                'subscription_id' => $stripeSubscription->id,
            ]);

            // Send confirmation email
            $this->sendSubscriptionConfirmationEmail($user, $stripeSubscription);
        }
    }

    protected function handleSubscriptionUpdated($subscription)
    {
        $user = User::where('stripe_id', $subscription->customer)->first();
        if (!$user) return;

        $dbSubscription = $user->subscriptions()->where('stripe_id', $subscription->id)->first();
        if ($dbSubscription) {
            $dbSubscription->update([
                'stripe_status' => $subscription->status,
                'stripe_price' => $subscription->items->data[0]->price->id ?? $dbSubscription->stripe_price,
                'ends_at' => isset($subscription->cancel_at) ? \Carbon\Carbon::createFromTimestamp($subscription->cancel_at) : null,
            ]);
        }
    }

    protected function handleSubscriptionDeleted($subscription)
    {
        $user = User::where('stripe_id', $subscription->customer)->first();
        if (!$user) return;

        $dbSubscription = $user->subscriptions()->where('stripe_id', $subscription->id)->first();
        if ($dbSubscription) {
            $dbSubscription->update([
                'stripe_status' => $subscription->status,
                'ends_at' => now(),
            ]);
        }
    }

    /**
     * Send subscription confirmation email to user
     */
    protected function sendSubscriptionConfirmationEmail(User $user, $stripeSubscription, $session = null)
    {
        try {
            // Get price details
            $planName = 'Standard';
            $amount = '';
            $interval = '';

            if (isset($stripeSubscription->items->data[0])) {
                $priceObj = $stripeSubscription->items->data[0]->price;
                $amount = number_format($priceObj->unit_amount / 100, 2) . ' ' . strtoupper($priceObj->currency);
                $interval = $priceObj->recurring->interval === 'month' ? 'Mensuel / Monthly' : 'Annuel / Annual';

                // Try to determine plan name from price ID
                if ($priceObj->id === env('STRIPE_PRICE_PREMIUM')) {
                    $planName = 'Premium';
                } elseif ($priceObj->id === env('STRIPE_PRICE_STANDARD') || $priceObj->id === env('STRIPE_PRICE_STARTER')) {
                    $planName = 'Standard';
                } else {
                    $planName = $priceObj->nickname ?? 'Standard';
                }
            }

            $startDate = $stripeSubscription->current_period_start
                ? \Carbon\Carbon::createFromTimestamp($stripeSubscription->current_period_start)->format('d/m/Y')
                : now()->format('d/m/Y');

            // Extract shipping address from checkout session
            $shippingAddress = null;
            if ($session) {
                // Try collected_information->shipping_details first, then customer_details
                $shipping = $session->collected_information->shipping_details ?? null;
                $address = $shipping->address ?? $session->customer_details->address ?? null;
                $name = $shipping->name ?? $session->customer_details->name ?? $user->name;

                if ($address && ($address->line1 ?? null)) {
                    $shippingAddress = [
                        'name' => $name,
                        'line1' => $address->line1 ?? '',
                        'line2' => $address->line2 ?? '',
                        'city' => $address->city ?? '',
                        'postal_code' => $address->postal_code ?? '',
                        'state' => $address->state ?? '',
                        'country' => $address->country ?? '',
                    ];
                }
            }

            $subscriptionData = [
                'plan_name' => $planName,
                'amount' => $amount,
                'interval' => $interval,
                'start_date' => $startDate,
                'shipping_address' => $shippingAddress,
            ];

            // Detect user language preference (default to French)
            $locale = 'fr';

            Mail::to($user->email)->send(new SubscriptionConfirmedMail($user, $subscriptionData, $locale));

            // Send admin notification
            Mail::to('lmermet@citypassenger.com')->send(new NewSubscriptionAdminNotification($user, $subscriptionData));

            Log::info('Subscription confirmation email sent', ['user_id' => $user->id, 'email' => $user->email]);
        } catch (\Exception $e) {
            Log::error('Failed to send subscription confirmation email', [
                'error' => $e->getMessage(),
                'user_id' => $user->id,
            ]);
        }
    }

    /**
     * Handle cancelled subscription
     */
    public function cancelled()
    {
        return view('subscription.cancel');
    }
}

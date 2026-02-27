<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderProcessedMail;
use App\Mail\PaymentFailedMail;
use App\Mail\ShippingTrackingMail;
use App\Mail\OrderDeliveredMail;
use Carbon\Carbon;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'user_id',
        'status',
        'product_amount',
        'discount_amount',
        'tax_amount',
        'shipping_cost',
        'shipping_method',
        'total',
        'payment_method',
        'payment_intent_id',
        'payment_status',
        'payment_received_at',
        'shipping_provider',
        'tracking_id',
        'shipping_address_id',
        'billing_address_id',
        'notes',
        'shipped_at',
        'delivered_at',
    ];

    protected $casts = [
        'product_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'total' => 'decimal:2',
        'payment_received_at' => 'datetime',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    /**
     * Get the user that owns the order.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the order items.
     */
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get the shipping address.
     */
    public function shippingAddress()
    {
        return $this->belongsTo(Address::class, 'shipping_address_id');
    }

    /**
     * Get the billing address.
     */
    public function billingAddress()
    {
        return $this->belongsTo(Address::class, 'billing_address_id');
    }

    /**
     * Generate a unique order number.
     */
    public static function generateOrderNumber()
    {
        $date = Carbon::now()->format('Ym');
        $count = static::whereYear('created_at', Carbon::now()->year)
            ->whereMonth('created_at', Carbon::now()->month)
            ->count() + 1;
        
        return 'ORD-' . $date . '-' . str_pad($count, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Mark order as paid.
     */
    public function markAsPaid($paymentIntentId = null)
    {
        $this->payment_status = 'succeeded';
        $this->payment_received_at = Carbon::now();
        
        if ($paymentIntentId) {
            $this->payment_intent_id = $paymentIntentId;
        }
        
        $this->status = 'processing';
        $this->save();
        
        // Ensure user relationship is loaded to get locale and email
        if (!$this->relationLoaded('user')) {
            $this->load('user');
        }
        
        $locale = $this->user->language ?? 'en';
        Mail::to($this->user->email)->send(new OrderProcessedMail($this, $locale));
    }

    /**
     * Mark order as shipped.
     */
    public function markAsShipped($provider, $trackingId)
    {
        $this->shipping_provider = $provider;
        $this->tracking_id = $trackingId;
        $this->status = 'shipped';
        $this->shipped_at = Carbon::now();
        $this->save();
        
        // Ensure user relationship is loaded to get locale and email
        if (!$this->relationLoaded('user')) {
            $this->load('user');
        }
        
        $locale = $this->user->language ?? 'en';
        Mail::to($this->user->email)->send(new ShippingTrackingMail($this, $locale));
    }

    /**
     * Mark order as delivered.
     */
    public function markAsDelivered()
    {
        $this->status = 'delivered';
        $this->delivered_at = Carbon::now();
        $this->save();
        
        // Ensure user relationship is loaded to get locale and email
        if (!$this->relationLoaded('user')) {
            $this->load('user');
        }
        
        $locale = $this->user->language ?? 'en';
        Mail::to($this->user->email)->send(new OrderDeliveredMail($this, $locale));
    }

    /**
     * Mark payment as failed.
     */
    public function markPaymentFailed($reason = null)
    {
        $this->payment_status = 'failed';
        $this->status = 'payment_failed';
        
        if ($reason) {
            $this->notes = $reason;
        }
        
        $this->save();
        
        // Ensure user relationship is loaded to get locale and email
        if (!$this->relationLoaded('user')) {
            $this->load('user');
        }
        
        $locale = $this->user->language ?? 'en';
        Mail::to($this->user->email)->send(new PaymentFailedMail($this, $locale));
    }

    /**
     * Cancel order.
     */
    public function cancel()
    {
        $this->status = 'cancelled';
        $this->save();
    }

    /**
     * Check if order has tracking info.
     */
    public function hasTracking()
    {
        return !empty($this->tracking_id) && !empty($this->shipping_provider);
    }

    /**
     * Check if order is shipped.
     */
    public function isShipped()
    {
        return in_array($this->status, ['shipped', 'delivered']);
    }

    /**
     * Check if order is delivered.
     */
    public function isDelivered()
    {
        return $this->status === 'delivered';
    }

    /**
     * Check if order is paid.
     */
    public function isPaid()
    {
        return $this->payment_status === 'succeeded';
    }

    /**
     * Get shipping method name.
     */
    public function getShippingMethodName()
    {
        $shippingRate = ShippingRate::where('method', $this->shipping_method)->first();
        
        if (!$shippingRate) {
            return ucfirst($this->shipping_method) . ' Shipping';
        }
        
        $locale = app()->getLocale();
        return $locale === 'fr' ? $shippingRate->name_fr : $shippingRate->name_en;
    }

    /**
     * Get amount in cents for Stripe.
     */
    public function getAmountInCents()
    {
        return (int) ($this->total * 100);
    }

    /**
     * Scope completed orders.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'delivered');
    }

    /**
     * Scope pending orders.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope payment failed orders.
     */
    public function scopePaymentFailed($query)
    {
        return $query->where('status', 'payment_failed');
    }

    /**
     * Scope shipped orders.
     */
    public function scopeShipped($query)
    {
        return $query->whereIn('status', ['shipped', 'delivered']);
    }

    /**
     * Scope orders awaiting shipment.
     */
    public function scopeAwaitingShipment($query)
    {
        return $query->where('status', 'processing')
            ->where('payment_status', 'succeeded');
    }
}

<?php

namespace App\Console\Commands;

use App\Models\Order;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderProcessedMail;

class TestOrderEmail extends Command
{
    protected $signature = 'email:test-order {order_number}';
    protected $description = 'Test sending order confirmation email';

    public function handle()
    {
        $orderNumber = $this->argument('order_number');
        
        $order = Order::with(['user', 'items.productModel', 'shippingAddress', 'billingAddress'])
            ->where('order_number', $orderNumber)
            ->first();
        
        if (!$order) {
            $this->error("Order {$orderNumber} not found!");
            return 1;
        }
        
        if (!$order->user) {
            $this->error("Order {$orderNumber} has no user!");
            return 1;
        }
        
        $this->info("Sending email to: {$order->user->email}");
        $this->info("User name: {$order->user->name}");
        $this->info("Order total: \${$order->total}");
        
        $locale = $order->user->language ?? 'en';
        
        try {
            // Send email directly (not queued)
            Mail::to($order->user->email)->send(new OrderProcessedMail($order, $locale));
            $this->info("✓ Email sent successfully!");
            return 0;
        } catch (\Exception $e) {
            $this->error("Failed to send email: " . $e->getMessage());
            return 1;
        }
    }
}

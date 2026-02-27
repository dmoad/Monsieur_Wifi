<?php

namespace App\Console\Commands;

use App\Models\Order;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderProcessedMail;
use Illuminate\Support\Facades\Log;

class TestOrderEmailVerbose extends Command
{
    protected $signature = 'email:test-order-verbose {order_number}';
    protected $description = 'Test sending order confirmation email with verbose output';

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
        
        $this->info("=== Order Information ===");
        $this->info("Order Number: {$order->order_number}");
        $this->info("User: {$order->user->name} ({$order->user->email})");
        $this->info("Total: \${$order->total}");
        $this->info("Items: {$order->items->count()}");
        
        foreach ($order->items as $item) {
            $this->info("  - {$item->productModel->name} x {$item->quantity}");
        }
        
        $this->info("Shipping: {$order->shippingAddress->address_line1}");
        $this->newLine();
        
        $locale = $order->user->language ?? 'en';
        
        try {
            $this->info("Creating mail instance...");
            $mail = new OrderProcessedMail($order, $locale);
            
            $this->info("Rendering email view...");
            $view = $mail->content()->view;
            $rendered = view($view, ['order' => $order])->render();
            $this->info("Email rendered successfully! Size: " . strlen($rendered) . " bytes");
            
            $this->info("Sending email via SMTP...");
            Mail::to($order->user->email)->send($mail);
            
            $this->info("✓ Email sent successfully!");
            $this->info("Check inbox for: {$order->user->email}");
            
            // Log the send
            Log::info('Test order email sent', [
                'order_number' => $orderNumber,
                'recipient' => $order->user->email,
                'email_size' => strlen($rendered)
            ]);
            
            return 0;
        } catch (\Exception $e) {
            $this->error("Failed to send email!");
            $this->error("Error: " . $e->getMessage());
            $this->error("File: " . $e->getFile() . ":" . $e->getLine());
            
            Log::error('Test order email failed', [
                'order_number' => $orderNumber,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return 1;
        }
    }
}

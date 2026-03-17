<?php

namespace App\Console\Commands;

use App\Models\Cart;
use App\Models\SystemSetting;
use App\Mail\CartAbandonmentMail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class SendAbandonedCartEmails extends Command
{
    protected $signature = 'cart:send-abandonment-emails';
    protected $description = 'Send emails to users who have abandoned their shopping carts';

    public function handle()
    {
        $settings = SystemSetting::getSettings();
        $abandonmentHours = $settings['cart_abandonment_hours'] ?? 24;
        
        $abandonmentTime = Carbon::now()->subHours($abandonmentHours);
        
        $abandonedCarts = Cart::whereHas('items')
            ->where('last_activity_at', '<=', $abandonmentTime)
            ->whereNull('abandoned_email_sent_at')
            ->with(['items.product', 'user'])
            ->get();
        
        $count = 0;
        foreach ($abandonedCarts as $cart) {
            try {
                $locale = $cart->user->language ?? 'en';
                
                Mail::to($cart->user->email)
                    ->send(new CartAbandonmentMail($cart, $locale));
                
                $cart->abandoned_email_sent_at = Carbon::now();
                $cart->save();
                
                $count++;
            } catch (\Exception $e) {
                $this->error("Failed to send email to {$cart->user->email}: {$e->getMessage()}");
            }
        }
        
        $this->info("Sent {$count} abandoned cart emails.");
        return Command::SUCCESS;
    }
}

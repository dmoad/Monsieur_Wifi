<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestEmailConfig extends Command
{
    protected $signature = 'email:test-config {email}';
    protected $description = 'Test email configuration by sending a simple test email';

    public function handle()
    {
        $email = $this->argument('email');
        
        $this->info("Testing email configuration...");
        $this->info("SMTP Host: " . config('mail.mailers.smtp.host'));
        $this->info("SMTP Port: " . config('mail.mailers.smtp.port'));
        $this->info("SMTP Encryption: " . config('mail.mailers.smtp.encryption'));
        $this->info("From Address: " . config('mail.from.address'));
        $this->info("From Name: " . config('mail.from.name'));
        $this->newLine();
        
        try {
            $this->info("Sending test email to: {$email}");
            
            Mail::raw('This is a test email from MrWiFi to verify SMTP configuration is working.', function ($message) use ($email) {
                $message->to($email)
                    ->subject('Test Email - MrWiFi System');
            });
            
            $this->info("✓ Email sent successfully!");
            $this->info("Please check the inbox (and spam folder) for: {$email}");
            
            return 0;
        } catch (\Exception $e) {
            $this->error("Failed to send email!");
            $this->error("Error: " . $e->getMessage());
            $this->error("Trace: " . $e->getTraceAsString());
            return 1;
        }
    }
}

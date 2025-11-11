<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CleanupExpiredPasswordResets extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auth:cleanup-password-resets';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove expired password reset tokens from the database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $expirationTime = Carbon::now()->subMinutes(60);
        
        $deletedCount = DB::table('password_reset_tokens')
            ->where('created_at', '<', $expirationTime)
            ->delete();
        
        if ($deletedCount > 0) {
            $this->info("Deleted {$deletedCount} expired password reset token(s).");
        } else {
            $this->info('No expired password reset tokens found.');
        }
        
        return 0;
    }
}


<?php

namespace App\Providers;

use App\Services\AuthzClient;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

class AuthzServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(AuthzClient::class, function () {
            return new AuthzClient();
        });
    }

    public function boot(): void
    {
        // Seed roles on every boot (idempotent). Skip during artisan commands
        // that don't need authz (migrate, config:cache, etc.) to avoid failures
        // when authz is unreachable.
        if ($this->app->runningInConsole() && ! $this->isAuthzRelevantCommand()) {
            return;
        }

        try {
            $authz = $this->app->make(AuthzClient::class);
            $authz->seedRoles(config('rbac.roles', []));
        } catch (\Exception $e) {
            Log::warning('Failed to seed authz roles on boot: ' . $e->getMessage());
        }
    }

    private function isAuthzRelevantCommand(): bool
    {
        $command = $_SERVER['argv'][1] ?? '';

        return in_array($command, ['serve', 'tinker', 'queue:work', 'queue:listen', 'horizon']);
    }
}

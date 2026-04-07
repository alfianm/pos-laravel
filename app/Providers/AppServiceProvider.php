<?php

namespace App\Providers;

use App\Services\TenantManager;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(\App\Services\TenantManager::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        RateLimiter::for('tenant-api', function (Request $request) {
            $tenantId = app(TenantManager::class)->getTenantId();
            $key = $tenantId ?: $request->ip();

            // Default limit: 60/min. Future: Load per-plan limits from SubscriptionPlan
            return Limit::perMinute(60)->by($key);
        });

        \Illuminate\Support\Facades\Event::subscribe(
            \App\Listeners\WebhookEventListener::class
        );

        \Illuminate\Support\Facades\Event::listen(
            \App\Events\SaleCreated::class,
            \App\Listeners\SaleJournalListener::class
        );

        \Illuminate\Support\Facades\Event::listen(
            \App\Events\PurchaseOrderReceived::class,
            \App\Listeners\PurchaseJournalListener::class
        );

        \Illuminate\Support\Facades\Event::listen(
            \App\Events\SaleReturnCompleted::class,
            \App\Listeners\ReturnJournalListener::class
        );
    }
}

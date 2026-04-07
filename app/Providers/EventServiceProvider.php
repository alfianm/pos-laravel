<?php

namespace App\Providers;

use App\Events\QuotaThresholdReached;
use App\Events\SaleCreated;
use App\Listeners\QuotaThresholdListener;
use App\Listeners\UpdateCustomerTimelineOnSale;
use App\Services\AuditLogService;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
            // SaleCreated is auto-discovered
        QuotaThresholdReached::class => [
            QuotaThresholdListener::class,
        ],
    ];

    public function boot(): void
    {
        parent::boot();

        $this->app['events']->listen(Login::class, function ($event) {
            AuditLogService::login($event->user);
        });

        $this->app['events']->listen(Logout::class, function ($event) {
            AuditLogService::logout($event->user);
        });

        $this->app['events']->listen(Failed::class, function ($event) {
            AuditLogService::loginFailed($event->credentials['email'] ?? 'unknown');
        });
    }
}

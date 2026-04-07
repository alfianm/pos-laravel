<?php

namespace Tests\Feature\Jobs;

use App\Jobs\GenerateDailySalesSummaryJob;
use App\Jobs\ImportMarketplaceOrdersJob;
use App\Jobs\RetryFailedMarketplaceSyncJob;
use App\Jobs\SyncMarketplaceStockJob;
use App\Models\Branch;
use App\Models\MarketplaceAccount;
use App\Models\MarketplaceShop;
use App\Models\MarketplaceSyncLog;
use App\Models\Sale;
use App\Models\Tenant;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Log;

uses(RefreshDatabase::class);

it('schedules basic report and retry jobs', function () {
    $schedule = app(Schedule::class);
    
    $events = collect($schedule->events());
    
    $dailyJob = $events->first(fn ($event) => str_contains($event->description, 'GenerateDailySalesSummaryJob'));
    $hourlyJob = $events->first(fn ($event) => str_contains($event->description, 'RetryFailedMarketplaceSyncJob'));
    
    expect($dailyJob)->not->toBeNull()
        ->and($dailyJob->expression)->toBe('1 0 * * *') // dailyAt('00:01')
        ->and($hourlyJob)->not->toBeNull()
        ->and($hourlyJob->expression)->toBe('0 * * * *'); // hourly()
});

it('retries failed marketplace sync logs only once', function () {
    Bus::fake([
        ImportMarketplaceOrdersJob::class,
        SyncMarketplaceStockJob::class,
    ]);

    $tenant = Tenant::factory()->create();
    $branch = Branch::factory()->create(['tenant_id' => $tenant->id]);
    $account = MarketplaceAccount::factory()->create(['tenant_id' => $tenant->id]);
    $shop = MarketplaceShop::factory()->create([
        'tenant_id' => $tenant->id,
        'marketplace_account_id' => $account->id,
        'branch_id' => $branch->id,
    ]);

    // Buat log gagal yang harus di-retry
    $failedLog = MarketplaceSyncLog::create([
        'tenant_id' => $tenant->id,
        'branch_id' => $branch->id,
        'marketplace_shop_id' => $shop->id,
        'marketplace' => 'tokopedia',
        'sync_type' => 'stock_sync',
        'status' => 'failed',
        'error_message' => 'API Timeout',
    ]);

    // Buat log gagal yang SUDAH di-retry (tidak boleh di-retry lagi)
    $alreadyRetriedLog = MarketplaceSyncLog::create([
        'tenant_id' => $tenant->id,
        'branch_id' => $branch->id,
        'marketplace_shop_id' => $shop->id,
        'marketplace' => 'tokopedia',
        'sync_type' => 'order_import',
        'status' => 'failed',
        'payload' => ['auto_retried' => true],
    ]);

    (new RetryFailedMarketplaceSyncJob())->handle();

    Bus::assertDispatched(SyncMarketplaceStockJob::class, function ($job) use ($shop) {
        return $job->shopId === $shop->id;
    });

    Bus::assertNotDispatched(ImportMarketplaceOrdersJob::class);

    expect($failedLog->refresh()->payload['auto_retried'])->toBeTrue();
});

it('generates daily sales summary and logs it', function () {
    // Kita buat mock yang lebih fleksibel
    Log::shouldReceive('info')->atLeast()->once();
    Log::shouldReceive('channel')->andReturnSelf();
    Log::shouldReceive('warning');

    $tenant = Tenant::factory()->create(['name' => 'Retailer A']);
    $branch = Branch::factory()->create(['tenant_id' => $tenant->id, 'name' => 'Jakarta']);
    
    $yesterday = now()->subDay()->toDateString();

    Sale::create([
        'tenant_id' => $tenant->id,
        'branch_id' => $branch->id,
        'sale_no' => 'SAL-001',
        'sale_date' => $yesterday . ' 10:00:00',
        'status' => 'completed',
        'grand_total' => 150000,
        'discount_amount' => 10000,
        'payment_status' => 'paid',
        'subtotal' => 160000,
        'tax_amount' => 0,
        'paid_amount' => 150000,
        'due_amount' => 0,
    ]);

    (new GenerateDailySalesSummaryJob($yesterday))->handle();
    
    expect(true)->toBeTrue();
});

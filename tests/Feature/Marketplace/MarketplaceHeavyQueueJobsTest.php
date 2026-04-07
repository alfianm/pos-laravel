<?php

use App\Jobs\ImportMarketplaceOrdersJob;
use App\Jobs\SyncMarketplaceStockJob;
use App\Models\Branch;
use App\Models\MarketplaceAccount;
use App\Models\MarketplaceShop;
use App\Models\MarketplaceSyncLog;
use App\Models\Tenant;
use App\Models\User;
use App\Services\OrderImportService;
use App\Services\StockSyncService;
use Mockery\MockInterface;

beforeEach(function (): void {
    $this->tenant = Tenant::factory()->create();
    $this->branch = Branch::factory()->create([
        'tenant_id' => $this->tenant->id,
    ]);
    $this->user = User::factory()->create([
        'tenant_id' => $this->tenant->id,
        'active_branch_id' => $this->branch->id,
    ]);

    $this->account = MarketplaceAccount::create([
        'tenant_id' => $this->tenant->id,
        'marketplace' => 'tokopedia',
        'name' => 'Tokopedia Account',
        'status' => 'active',
    ]);

    $this->shop = MarketplaceShop::create([
        'tenant_id' => $this->tenant->id,
        'marketplace_account_id' => $this->account->id,
        'branch_id' => $this->branch->id,
        'external_shop_id' => 'SHOP-QUEUE-001',
        'marketplace' => 'tokopedia',
        'name' => 'Queue Test Shop',
        'status' => 'active',
    ]);

    $this->actingAs($this->user);
});

it('queues marketplace order imports through a dedicated job configuration', function (): void {
    $filters = [
        'status' => 'pending',
        'date_from' => now()->subDay()->format('Y-m-d'),
    ];

    $this->mock(OrderImportService::class, function (MockInterface $mock) use ($filters): void {
        $mock->shouldReceive('importOrders')
            ->once()
            ->withArgs(function (MarketplaceShop $shop, array $resolvedFilters) use ($filters): bool {
                return $shop->is($this->shop) && $resolvedFilters === $filters;
            });
    });

    $job = new ImportMarketplaceOrdersJob($this->shop->id, $filters);

    expect($job->queue)->toBe('marketplace-imports')
        ->and($job->tries)->toBe(3)
        ->and($job->timeout)->toBe(300);

    $job->handle(app(OrderImportService::class));
});

it('logs marketplace order import failures using the current sync log schema', function (): void {
    $job = new ImportMarketplaceOrdersJob($this->shop->id, ['status' => 'pending']);

    $job->failed(new RuntimeException('Marketplace order import failed hard'));

    $log = MarketplaceSyncLog::query()->latest()->first();

    expect($log)->not->toBeNull()
        ->and($log->tenant_id)->toBe($this->tenant->id)
        ->and($log->branch_id)->toBe($this->branch->id)
        ->and($log->marketplace_shop_id)->toBe($this->shop->id)
        ->and($log->sync_type)->toBe('order_import')
        ->and($log->status)->toBe('failed')
        ->and($log->error_message)->toBe('Marketplace order import failed hard')
        ->and($log->payload['job'])->toBe(ImportMarketplaceOrdersJob::class);
});

it('queues marketplace stock sync through a dedicated job configuration', function (): void {
    $this->mock(StockSyncService::class, function (MockInterface $mock): void {
        $mock->shouldReceive('syncStock')
            ->once()
            ->withArgs(function (MarketplaceShop $shop, ?array $productMaps = null): bool {
                return $shop->is($this->shop) && $productMaps === null;
            });
    });

    $job = new SyncMarketplaceStockJob($this->shop->id);

    expect($job->queue)->toBe('marketplace-sync')
        ->and($job->tries)->toBe(3)
        ->and($job->timeout)->toBe(300);

    $job->handle(app(StockSyncService::class));
});

it('logs marketplace stock sync failures using the current sync log schema', function (): void {
    $job = new SyncMarketplaceStockJob($this->shop->id, ['product-1', 'product-2']);

    $job->failed(new RuntimeException('Marketplace stock sync failed hard'));

    $log = MarketplaceSyncLog::query()->latest()->first();

    expect($log)->not->toBeNull()
        ->and($log->tenant_id)->toBe($this->tenant->id)
        ->and($log->branch_id)->toBe($this->branch->id)
        ->and($log->marketplace_shop_id)->toBe($this->shop->id)
        ->and($log->sync_type)->toBe('stock_sync')
        ->and($log->status)->toBe('failed')
        ->and($log->error_message)->toBe('Marketplace stock sync failed hard')
        ->and($log->payload['job'])->toBe(SyncMarketplaceStockJob::class)
        ->and($log->payload['product_ids'])->toBe(['product-1', 'product-2']);
});

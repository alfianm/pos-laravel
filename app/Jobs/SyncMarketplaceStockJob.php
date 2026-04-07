<?php

namespace App\Jobs;

use App\Models\MarketplaceProductMap;
use App\Models\MarketplaceShop;
use App\Models\MarketplaceSyncLog;
use App\Services\StockSyncService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Throwable;

class SyncMarketplaceStockJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public int $timeout = 300;

    /**
     * Create a new job instance.
     */
    public function __construct(public string $shopId, public array $productIds = [])
    {
        $this->onQueue('marketplace-sync');
    }

    /**
     * Execute the job.
     */
    public function handle(StockSyncService $stockSyncService): void
    {
        $shop = MarketplaceShop::query()
            ->with('account')
            ->findOrFail($this->shopId);

        if ($this->productIds === []) {
            $stockSyncService->syncStock($shop);

            return;
        }

        $productMaps = MarketplaceProductMap::query()
            ->where('tenant_id', $shop->tenant_id)
            ->where('marketplace', $shop->marketplace)
            ->where('is_active', true)
            ->where('sync_stock', true)
            ->whereIn('product_id', $this->productIds)
            ->get();

        $stockSyncService->syncStock($shop, $productMaps->all());
    }

    public function backoff(): array
    {
        return [60, 300, 900];
    }

    public function failed(Throwable $exception): void
    {
        $shop = MarketplaceShop::query()->find($this->shopId);

        if (! $shop) {
            return;
        }

        MarketplaceSyncLog::create([
            'tenant_id' => $shop->tenant_id,
            'branch_id' => $shop->branch_id,
            'marketplace_shop_id' => $shop->id,
            'marketplace' => $shop->marketplace,
            'sync_type' => 'stock_sync',
            'status' => 'failed',
            'error_message' => $exception->getMessage(),
            'payload' => [
                'job' => static::class,
                'product_ids' => $this->productIds,
            ],
            'synced_at' => now(),
        ]);
    }
}

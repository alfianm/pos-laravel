<?php

namespace App\Jobs;

use App\Models\MarketplaceShop;
use App\Models\MarketplaceSyncLog;
use App\Services\OrderImportService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Throwable;

class ImportMarketplaceOrdersJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public int $timeout = 300;

    /**
     * Create a new job instance.
     */
    public function __construct(public string $shopId, public array $filters = [])
    {
        $this->onQueue('marketplace-imports');
    }

    /**
     * Execute the job.
     */
    public function handle(OrderImportService $orderImportService): void
    {
        $shop = MarketplaceShop::query()
            ->with('account')
            ->findOrFail($this->shopId);

        $orderImportService->importOrders($shop, $this->filters);
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
            'sync_type' => 'order_import',
            'status' => 'failed',
            'error_message' => $exception->getMessage(),
            'payload' => [
                'job' => static::class,
                'filters' => $this->filters,
            ],
            'synced_at' => now(),
        ]);
    }
}

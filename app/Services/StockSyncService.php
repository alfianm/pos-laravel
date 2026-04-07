<?php

namespace App\Services;

use App\Models\Inventory;
use App\Models\MarketplaceProductMap;
use App\Models\MarketplaceShop;
use App\Models\MarketplaceSyncLog;
use Illuminate\Support\Facades\Log;

class StockSyncService
{
    protected MarketplaceService $marketplaceService;

    public function __construct(MarketplaceService $marketplaceService)
    {
        $this->marketplaceService = $marketplaceService;
    }

    public function syncStock(MarketplaceShop $shop, ?array $productMaps = null): array
    {
        $account = $shop->account;
        if (! $account || ! $this->marketplaceService->isTokenValid($account)) {
            return [
                'success' => false,
                'message' => 'Account token is invalid or expired',
                'synced' => 0,
                'failed' => 0,
                'errors' => [],
            ];
        }

        $mappings = $productMaps ?? MarketplaceProductMap::where('tenant_id', $shop->tenant_id)
            ->where('is_active', true)
            ->get();

        $synced = 0;
        $failed = 0;
        $errors = [];
        foreach ($mappings as $mapping) {
            try {
                $result = $this->syncSingleMapping($shop, $mapping);
                if ($result['success']) {
                    $synced++;
                } else {
                    $failed++;
                    $errors[] = [
                        'mapping_id' => $mapping->id,
                        'external_product_id' => $mapping->external_product_id,
                        'error' => $result['message'] ?? 'Unknown error',
                    ];
                }
            } catch (\Exception $e) {
                $failed++;
                $errors[] = [
                    'mapping_id' => $mapping->id, 'external_product_id' => $mapping->external_product_id,
                    'error' => $e->getMessage(),
                ];
                Log::error('Stock sync failed for mapping', [
                    'mapping_id' => $mapping->id,
                    'shop_id' => $shop->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $this->logSync($shop, 'stock_sync', $synced > 0 ? 'success' : 'failed', "Synced: {$synced}, Failed: {$failed}");

        return [
            'success' => $synced > 0,
            'message' => 'Stock sync completed',
            'synced' => $synced,
            'failed' => $failed,
            'errors' => $errors,
        ];
    }

    public function syncSingleMapping(MarketplaceShop $shop, MarketplaceProductMap $mapping): array
    {
        if (! $mapping->sync_stock) {
            return [
                'success' => false,
                'message' => 'Stock sync is disabled for this mapping',
            ];
        }

        $inventory = $this->getInventory($shop, $mapping);
        if (! $inventory) {
            return [
                'success' => false,
                'message' => 'Inventory not found for this product',
            ];
        }

        $availableStock = $inventory->qty_available ?? 0;
        $platform = $shop->marketplace;
        $account = $shop->account;
        $credentials = $this->marketplaceService->getDecryptedCredentials($account);

        $response = $this->pushStockToPlatform($platform, $shop, $mapping, $availableStock, $credentials);

        if ($response['success']) {
            $this->updateMappingSyncStatus($mapping, 'success');

            return [
                'success' => true,
                'message' => 'Stock synced successfully',
                'stock' => $availableStock,
            ];
        }

        $this->updateMappingSyncStatus($mapping, 'failed', $response['message'] ?? null);

        return [
            'success' => false,
            'message' => $response['message'] ?? 'Failed to sync stock',
        ];
    }

    protected function getInventory(MarketplaceShop $shop, MarketplaceProductMap $mapping): ?Inventory
    {
        $branchId = $shop->branch_id;
        if (! $branchId) {
            return null;
        }

        return Inventory::where('branch_id', $branchId)
            ->where('product_id', $mapping->product_id)
            ->when($mapping->product_variant_id, function ($q) use ($mapping) {
                $q->where('product_variant_id', $mapping->product_variant_id);
            })
            ->when(! $mapping->product_variant_id, function ($q) {
                $q->whereNull('product_variant_id');
            })
            ->first();
    }

    protected function pushStockToPlatform(string $platform, MarketplaceShop $shop, MarketplaceProductMap $mapping, float $stock, array $credentials): array
    {
        return match ($platform) {
            'shopee' => $this->pushStockToShopee($shop, $mapping, $stock, $credentials),
            'tokopedia' => $this->pushStockToTokopedia($shop, $mapping, $stock, $credentials),
            'lazada' => $this->pushStockToLazada($shop, $mapping, $stock, $credentials),
            'bukalapak' => $this->pushStockToBukalapak($shop, $mapping, $stock, $credentials),
            'blibli' => $this->pushStockToBlibli($shop, $mapping, $stock, $credentials),
            default => ['success' => false, 'message' => 'Unsupported platform'],
        };
    }

    protected function pushStockToShopee(MarketplaceShop $shop, MarketplaceProductMap $mapping, float $stock, array $credentials): array
    {
        return ['success' => false, 'message' => 'Shopee API integration not implemented'];
    }

    protected function pushStockToTokopedia(MarketplaceShop $shop, MarketplaceProductMap $mapping, float $stock, array $credentials): array
    {
        return ['success' => false, 'message' => 'Tokopedia API integration not implemented'];
    }

    protected function pushStockToLazada(MarketplaceShop $shop, MarketplaceProductMap $mapping, float $stock, array $credentials): array
    {
        return ['success' => false, 'message' => 'Lazada API integration not implemented'];
    }

    protected function pushStockToBukalapak(MarketplaceShop $shop, MarketplaceProductMap $mapping, float $stock, array $credentials): array
    {
        return ['success' => false, 'message' => 'Bukalapak API integration not implemented'];
    }

    protected function pushStockToBlibli(MarketplaceShop $shop, MarketplaceProductMap $mapping, float $stock, array $credentials): array
    {
        return ['success' => false, 'message' => 'Blibli API integration not implemented'];
    }

    protected function updateMappingSyncStatus(MarketplaceProductMap $mapping, string $status, ?string $errorMessage = null): void
    {
        $mapping->update([
            'last_sync_at' => now(),
            'last_sync_status' => $status,
            'last_sync_error' => $errorMessage,
        ]);
    }

    protected function logSync(MarketplaceShop $shop, string $type, string $status, ?string $message = null, ?array $payload = null): MarketplaceSyncLog
    {
        return MarketplaceSyncLog::create([
            'tenant_id' => $shop->tenant_id,
            'branch_id' => $shop->branch_id,
            'marketplace_shop_id' => $shop->id,
            'marketplace' => $shop->marketplace,
            'sync_type' => $type,
            'status' => $status,
            'error_message' => $message,
            'payload' => $payload,
            'synced_at' => now(),
        ]);
    }

    public function syncStockForProduct(MarketplaceShop $shop, string $productId, ?string $variantId = null): array
    {
        $mappings = MarketplaceProductMap::where('tenant_id', $shop->tenant_id)
            ->where('marketplace', $shop->marketplace)
            ->where('product_id', $productId)
            ->when($variantId, function ($q) use ($variantId) {
                $q->where('product_variant_id', $variantId);
            })
            ->where('is_active', true)
            ->where('sync_stock', true)
            ->get();

        if ($mappings->isEmpty()) {
            return [
                'success' => false,
                'message' => 'No active stock-sync mappings found for this product',
                'synced' => 0,
                'failed' => 0,
            ];
        }

        return $this->syncStock($shop, $mappings->all());
    }

    public function getSyncQueuePayload(MarketplaceShop $shop, ?array $productIds = null): array
    {
        $mappings = MarketplaceProductMap::where('tenant_id', $shop->tenant_id)
            ->where('is_active', true)
            ->where('sync_stock', true)
            ->when($productIds, function ($q) use ($productIds) {
                $q->whereIn('product_id', $productIds);
            })
            ->get();

        $items = [];
        foreach ($mappings as $mapping) {
            $inventory = $this->getInventory($shop, $mapping);
            if ($inventory) {
                $items[] = [
                    'mapping_id' => $mapping->id,
                    'product_id' => $mapping->product_id,
                    'product_variant_id' => $mapping->product_variant_id,
                    'external_product_id' => $mapping->external_product_id,
                    'external_sku' => $mapping->external_sku,
                    'available_stock' => $inventory->qty_available,
                ];
            }
        }

        return [
            'shop_id' => $shop->id,
            'marketplace' => $shop->marketplace,
            'items' => $items,
        ];
    }
}

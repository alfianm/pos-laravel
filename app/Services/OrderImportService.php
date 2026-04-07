<?php

namespace App\Services;

use App\Models\MarketplaceOrder;
use App\Models\MarketplaceOrderItem;
use App\Models\MarketplaceProductMap;
use App\Models\MarketplaceShop;
use App\Models\MarketplaceSyncLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderImportService
{
    protected MarketplaceService $marketplaceService;

    protected array $supportedPlatforms = [
        'shopee', 'tokopedia',
        'lazada',
        'bukalapak',
        'blibli',
    ];

    public function __construct(MarketplaceService $marketplaceService)
    {
        $this->marketplaceService = $marketplaceService;
    }

    public function importOrders(MarketplaceShop $shop, array $filters = []): array
    {
        $account = $shop->account;
        if (! $account || ! $this->marketplaceService->isTokenValid($account)) {
            return [
                'success' => false,
                'message' => 'Account token is invalid or expired',
                'imported' => 0,
                'skipped' => 0,
                'errors' => [],
            ];
        }

        $platform = $shop->marketplace;
        $credentials = $this->marketplaceService->getDecryptedCredentials($account);

        try {
            $orders = $this->fetchOrdersFromPlatform($platform, $shop, $credentials, $filters);

            $imported = 0;
            $skipped = 0;
            $errors = [];

            foreach ($orders as $orderData) {
                try {
                    $result = $this->importSingleOrder($shop, $orderData);
                    if ($result['imported']) {
                        $imported++;
                    } else {
                        $skipped++;
                    }
                } catch (\Exception $e) {
                    $skipped++;
                    $errors[] = [
                        'external_order_id' => $orderData['external_order_id'] ?? 'unknown',
                        'error' => $e->getMessage(),
                    ];
                    Log::error('Order import failed', [
                        'shop_id' => $shop->id,
                        'order_data' => $orderData,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            $this->logSync($shop, 'order_import', $imported > 0 ? 'success' : 'failed', "Imported: {$imported}, Skipped: {$skipped}");

            return [
                'success' => true,
                'message' => 'Import completed',
                'imported' => $imported,
                'skipped' => $skipped,
                'errors' => $errors,
            ];
        } catch (\Exception $e) {
            $this->logSync($shop, 'order_import', 'failed', $e->getMessage());

            return [
                'success' => false,
                'message' => $e->getMessage(),
                'imported' => 0,
                'skipped' => 0,
                'errors' => [$e->getMessage()],
            ];
        }
    }

    protected function fetchOrdersFromPlatform(string $platform, MarketplaceShop $shop, array $credentials, array $filters = []): array
    {
        return match ($platform) {
            'shopee' => $this->fetchShopeeOrders($shop, $credentials, $filters),
            'tokopedia' => $this->fetchTokopediaOrders($shop, $credentials, $filters),
            'lazada' => $this->fetchLazadaOrders($shop, $credentials, $filters),
            'bukalapak' => $this->fetchBukalapakOrders($shop, $credentials, $filters),
            'blibli' => $this->fetchBlibliOrders($shop, $credentials, $filters),
            default => [],
        };
    }

    protected function fetchShopeeOrders(MarketplaceShop $shop, array $credentials, array $filters = []): array
    {
        return [];
    }

    protected function fetchTokopediaOrders(MarketplaceShop $shop, array $credentials, array $filters = []): array
    {
        return [];
    }

    protected function fetchLazadaOrders(MarketplaceShop $shop, array $credentials, array $filters = []): array
    {
        return [];
    }

    protected function fetchBukalapakOrders(MarketplaceShop $shop, array $credentials, array $filters = []): array
    {
        return [];
    }

    protected function fetchBlibliOrders(MarketplaceShop $shop, array $credentials, array $filters = []): array
    {
        return [];
    }

    public function importSingleOrder(MarketplaceShop $shop, array $orderData): array
    {
        $externalOrderId = $orderData['external_order_id'] ?? null;
        if (! $externalOrderId) {
            throw new \InvalidArgumentException('external_order_id is required');
        }

        $existingOrder = MarketplaceOrder::where('provider', $shop->marketplace)
            ->where('external_order_id', $externalOrderId)
            ->first();

        if ($existingOrder) {
            return [
                'imported' => false,
                'order' => $existingOrder,
                'reason' => 'Order already exists',
            ];
        }

        return DB::transaction(function () use ($shop, $orderData, $externalOrderId) {
            $normalizedOrder = $this->normalizeOrderData($shop->marketplace, $orderData);

            $order = MarketplaceOrder::create([
                'tenant_id' => $shop->tenant_id,
                'branch_id' => $shop->branch_id,
                'marketplace_shop_id' => $shop->id,
                'customer_id' => null,
                'provider' => $shop->marketplace,
                'external_order_id' => $externalOrderId,
                'external_order_no' => $normalizedOrder['external_order_no'] ?? null,
                'status' => $normalizedOrder['status'],
                'order_date' => $normalizedOrder['order_date'] ?? now(),
                'buyer_name' => $normalizedOrder['buyer_name'] ?? null,
                'buyer_phone' => $normalizedOrder['buyer_phone'] ?? null,
                'subtotal' => $normalizedOrder['subtotal'] ?? 0,
                'shipping_amount' => $normalizedOrder['shipping_amount'] ?? 0,
                'discount_amount' => $normalizedOrder['discount_amount'] ?? 0,
                'grand_total' => $normalizedOrder['grand_total'] ?? 0,
                'raw_data' => $orderData,
                'imported_at' => now(),
            ]);

            $importedItems = 0;
            $items = $normalizedOrder['items'] ?? [];
            foreach ($items as $itemData) {
                $this->importOrderItem($order, $itemData);
                $importedItems++;
            }

            return [
                'imported' => true,
                'order' => $order,
                'items_count' => $importedItems,
            ];
        });
    }

    protected function importOrderItem(MarketplaceOrder $order, array $itemData): MarketplaceOrderItem
    {
        $mapping = $this->findProductMapping($order->provider, $itemData);

        return MarketplaceOrderItem::create([
            'tenant_id' => $order->tenant_id,
            'marketplace_order_id' => $order->id,
            'marketplace_product_map_id' => $mapping?->id,
            'product_id' => $mapping?->product_id,
            'product_variant_id' => $mapping?->product_variant_id,
            'external_product_id' => $itemData['external_product_id'] ?? null,
            'external_variant_id' => $itemData['external_variant_id'] ?? null,
            'external_sku' => $itemData['external_sku'] ?? null,
            'name_snapshot' => $itemData['name'] ?? 'Unknown Product',
            'qty' => $itemData['qty'] ?? 1,
            'unit_price' => $itemData['unit_price'] ?? 0,
            'line_total' => $itemData['line_total'] ?? ($itemData['qty'] * $itemData['unit_price'] ?? 0),
            'raw_data' => $itemData,
        ]);
    }

    protected function findProductMapping(string $provider, array $itemData): ?MarketplaceProductMap
    {
        $externalProductId = $itemData['external_product_id'] ?? null;
        $externalSku = $itemData['external_sku'] ?? null;

        if ($externalProductId) {
            $mapping = MarketplaceProductMap::where('marketplace', $provider)
                ->where('external_product_id', $externalProductId)
                ->where('is_active', true)
                ->first();

            if ($mapping) {
                return $mapping;
            }
        }

        if ($externalSku) {
            $mapping = MarketplaceProductMap::where('marketplace', $provider)
                ->where('external_sku', $externalSku)
                ->where('is_active', true)
                ->first();

            if ($mapping) {
                return $mapping;
            }
        }

        return null;
    }

    protected function normalizeOrderData(string $platform, array $rawData): array
    {
        return match ($platform) {
            'shopee' => $this->normalizeShopeeOrder($rawData),
            'tokopedia' => $this->normalizeTokopediaOrder($rawData),
            'lazada' => $this->normalizeLazadaOrder($rawData),
            'bukalapak' => $this->normalizeBukalapakOrder($rawData),
            'blibli' => $this->normalizeBlibliOrder($rawData),
            default => $this->normalizeGenericOrder($rawData),
        };
    }

    protected function normalizeShopeeOrder(array $rawData): array
    {
        return $this->normalizeGenericOrder($rawData);
    }

    protected function normalizeTokopediaOrder(array $rawData): array
    {
        return $this->normalizeGenericOrder($rawData);
    }

    protected function normalizeLazadaOrder(array $rawData): array
    {
        return $this->normalizeGenericOrder($rawData);
    }

    protected function normalizeBukalapakOrder(array $rawData): array
    {
        return $this->normalizeGenericOrder($rawData);
    }

    protected function normalizeBlibliOrder(array $rawData): array
    {
        return $this->normalizeGenericOrder($rawData);
    }

    protected function normalizeGenericOrder(array $rawData): array
    {
        return [
            'external_order_id' => $rawData['external_order_id'] ?? $rawData['order_id'] ?? null,
            'external_order_no' => $rawData['external_order_no'] ?? $rawData['order_no'] ?? null,
            'status' => $rawData['status'] ?? 'pending',
            'order_date' => $rawData['order_date'] ?? $rawData['created_at'] ?? null,
            'buyer_name' => $rawData['buyer_name'] ?? $rawData['customer_name'] ?? null,
            'buyer_phone' => $rawData['buyer_phone'] ?? $rawData['customer_phone'] ?? null,
            'subtotal' => (float) ($rawData['subtotal'] ?? 0),
            'shipping_amount' => (float) ($rawData['shipping_amount'] ?? $rawData['shipping_cost'] ?? 0),
            'discount_amount' => (float) ($rawData['discount_amount'] ?? $rawData['discount'] ?? 0),
            'grand_total' => (float) ($rawData['grand_total'] ?? $rawData['total'] ?? 0),
            'items' => $rawData['items'] ?? [],
        ];
    }

    public function normalizeIncomingWebhook(string $platform, array $payload): array
    {
        return $this->normalizeOrderData($platform, $payload);
    }

    public function importFromWebhook(MarketplaceShop $shop, array $payload): array
    {
        $normalizedOrder = $this->normalizeIncomingWebhook($shop->marketplace, $payload);

        return $this->importSingleOrder($shop, $normalizedOrder);
    }

    protected function logSync(MarketplaceShop $shop, string $type, string $status, ?string $message = null): MarketplaceSyncLog
    {
        return MarketplaceSyncLog::create([
            'tenant_id' => $shop->tenant_id,
            'branch_id' => $shop->branch_id,
            'marketplace_shop_id' => $shop->id,
            'marketplace' => $shop->marketplace,
            'sync_type' => $type,
            'status' => $status,
            'error_message' => $message,
            'payload' => [
                'shop_id' => $shop->id,
                'shop_name' => $shop->name,
            ],
            'synced_at' => now(),
        ]);
    }

    public function getImportedOrders(MarketplaceShop $shop, array $filters = [])
    {
        $query = MarketplaceOrder::with(['items'])
            ->where('marketplace_shop_id', $shop->id)
            ->when($filters['status'] ?? null, function ($q, $status) {
                $q->where('status', $status);
            })
            ->when($filters['date_from'] ?? null, function ($q, $dateFrom) {
                $q->whereDate('order_date', '>=', $dateFrom);
            })
            ->when($filters['date_to'] ?? null, function ($q, $dateTo) {
                $q->whereDate('order_date', '<=', $dateTo);
            })
            ->orderBy('order_date', 'desc');

        return $query->paginate($filters['per_page'] ?? 15);
    }

    public function deleteImportedOrder(MarketplaceOrder $order): bool
    {
        return DB::transaction(function () use ($order) {
            $order->items()->delete();

            return $order->delete();
        });
    }
}

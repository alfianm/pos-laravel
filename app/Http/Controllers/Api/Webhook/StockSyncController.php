<?php

namespace App\Http\Controllers\Api\Webhook;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\Tenant;
use App\Models\WebhookInboundLog;
use App\Services\StockService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StockSyncController extends Controller
{
    /**
     * Handle inbound stock sync from marketplace or external system.
     */
    public function handle(Request $request, string $tenantId): JsonResponse
    {
        // 1. Find Tenant
        $tenant = Tenant::findOrFail($tenantId);

        // 2. Security: Verify Signature
        $signature = $request->header('X-RasaNusa-Webhook-Secret-Signature');
        
        $activeSecret = ($tenant->settings['webhook_inbound_secret'] ?? null) 
            ?: config('webhook.inbound_secret');

        if ($activeSecret) {
            if (!$signature) {
                return $this->logAndResponse($request, $tenant->id, 401, ['error' => 'Signature required']);
            }
            $computed = hash_hmac('sha256', $request->getContent(), $activeSecret);
            if (!hash_equals($computed, $signature)) {
                return $this->logAndResponse($request, $tenant->id, 401, ['error' => 'Invalid signature']);
            }
        }

        // 3. Process Payload
        try {
            $data = $request->validate([
                'branch_id' => 'required|uuid|exists:branches,id',
                'items' => 'required|array|min:1',
                'items.*.sku' => 'required|string',
                'items.*.qty' => 'required_without:items.*.absolute_qty|numeric',
                'items.*.absolute_qty' => 'required_without:items.*.qty|numeric',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->logAndResponse($request, $tenant->id, 422, ['errors' => $e->errors()]);
        }

        $branch = Branch::where('tenant_id', $tenant->id)->findOrFail($data['branch_id']);
        $stockService = app(StockService::class);
        $summary = ['success' => [], 'failed' => []];

        DB::beginTransaction();
        try {
            foreach ($data['items'] as $item) {
                $product = Product::where('tenant_id', $tenant->id)
                    ->where('sku', $item['sku'])
                    ->first();

                if (!$product) {
                    $summary['failed'][] = "SKU {$item['sku']} not found";
                    continue;
                }

                $adjustmentQty = $item['qty'] ?? null;

                if (isset($item['absolute_qty'])) {
                    $inventory = Inventory::where('branch_id', $branch->id)
                        ->where('product_id', $product->id)
                        ->first();
                    
                    $currentQty = $inventory ? $inventory->qty_on_hand : 0;
                    $adjustmentQty = $item['absolute_qty'] - $currentQty;
                }

                if ($adjustmentQty == 0) {
                    $summary['success'][] = $item['sku'] . " (no change)";
                    continue;
                }

                $stockService->recordMovement([
                    'tenant_id' => $tenant->id,
                    'branch_id' => $branch->id,
                    'product_id' => $product->id,
                    'qty' => $adjustmentQty,
                    'movement_type' => 'sync',
                    'reference_type' => 'Marketplace',
                    'notes' => 'Inbound bulk sync webhook',
                    'meta' => ['inbound_payload' => $item]
                ]);

                $summary['success'][] = $item['sku'];
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Stock Sync Webhook Failed: " . $e->getMessage());
            return $this->logAndResponse($request, $tenant->id, 500, ['error' => 'Sync process failed.']);
        }

        return $this->logAndResponse($request, $tenant->id, 200, [
            'message' => 'Stock sync processed',
            'summary' => $summary,
            'processed_at' => now()->toIso8601String(),
        ]);
    }

    /**
     * Log the inbound request and return JSON response.
     */
    private function logAndResponse(Request $request, string $tenantId, int $statusCode, array $response): JsonResponse
    {
        WebhookInboundLog::create([
            'tenant_id' => $tenantId,
            'source' => $request->header('X-Webhook-Source', 'unknown'),
            'event_type' => 'stock-sync',
            'payload' => $request->all(),
            'status_code' => $statusCode,
            'response_payload' => $response,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return response()->json($response, $statusCode);
    }
}

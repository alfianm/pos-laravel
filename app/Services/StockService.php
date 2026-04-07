<?php

namespace App\Services;

use App\Models\Inventory;
use App\Models\StockMovement;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Events\LowStockAlert;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class StockService
{
    /**
     * Record a stock movement and update inventory levels.
     */
    public function recordMovement(array $data): StockMovement
    {
        return DB::transaction(function () use ($data) {
            $tenantId = $data['tenant_id'];
            $branchId = $data['branch_id'];
            $productId = $data['product_id'];
            $variantId = $data['product_variant_id'] ?? null;
            $qty = $data['qty']; // positive for increase, negative for decrease
            
            // 1. Get or Create Inventory record
            $inventory = Inventory::firstOrCreate(
                [
                    'branch_id' => $branchId,
                    'product_id' => $productId,
                    'product_variant_id' => $variantId,
                ],
                [
                    'id' => (string) Str::uuid(),
                    'tenant_id' => $tenantId,
                    'qty_on_hand' => 0,
                    'qty_reserved' => 0,
                    'qty_available' => 0,
                ]
            );

            $beforeQty = $inventory->qty_on_hand;
            $afterQty = $beforeQty + $qty;

            // 2. Update Inventory
            $inventory->update([
                'qty_on_hand' => $afterQty,
                'qty_available' => $afterQty - $inventory->qty_reserved,
            ]);

            // Low Stock Check
            if ($qty < 0 && $inventory->qty_available <= $inventory->reorder_level && $inventory->reorder_level > 0) {
                event(new LowStockAlert($inventory->product, $inventory->qty_available));
            }

            // 3. Create Stock Movement Record
            return StockMovement::create([
                'id' => (string) Str::uuid(),
                'tenant_id' => $tenantId,
                'branch_id' => $branchId,
                'product_id' => $productId,
                'product_variant_id' => $variantId,
                'reference_type' => $data['reference_type'],
                'reference_id' => $data['reference_id'] ?? null,
                'movement_type' => $data['movement_type'],
                'qty' => $qty,
                'before_qty' => $beforeQty,
                'after_qty' => $afterQty,
                'unit_cost' => $data['unit_cost'] ?? 0,
                'notes' => $data['notes'] ?? null,
                'performed_by' => $data['performed_by'] ?? auth()->id(),
                'meta' => $data['meta'] ?? null,
            ]);
        });
    }
}

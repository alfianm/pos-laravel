<?php

namespace App\Services;

use App\Models\Sale;
use App\Models\CustomerTimeline;

class CustomerTimelineService
{
    /**
     * Record a sale event to customer timeline.
     */
    public function recordSale(Sale $sale): void
    {
        if (!$sale->customer_id) {
            return;
        }

        $items = $sale->items->map(fn ($item) => [
            'product_id' => $item->product_id,
            'product_name' => $item->product?->name ?? 'Unknown Product',
            'qty' => $item->qty,
            'price' => $item->unit_price,
        ])->toArray();

        CustomerTimeline::create([
            'tenant_id' => $sale->tenant_id,
            'branch_id' => $sale->branch_id,
            'customer_id' => $sale->customer_id,
            'sale_id' => $sale->id,
            'type' => 'sale',
            'event_type' => 'sale_completed', // SATISFY DB NOT NULL
            'title' => 'Pembelian #' . $sale->sale_no,
            'description' => 'Transaksi pembelian dengan total ' . number_format($sale->grand_total, 0, ',', '.'),
            'metadata' => [
                'sale_no' => $sale->sale_no,
                'grand_total' => $sale->grand_total,
                'payment_method' => $sale->payments->first()?->payment_method ?? 'cash',
                'items' => $items,
                'points_redeemed' => $sale->points_redeemed,
                'points_value' => $sale->points_value,
            ],
            'occurred_at' => $sale->sale_date ?? now(),
        ]);
    }

    /**
     * Record a generic timeline event.
     */
    public function recordEvent(
        int $customerId,
        string $type,
        string $title,
        string $description,
        ?array $metadata = null,
        ?int $saleId = null,
        ?int $branchId = null,
    ): void {
        $user = auth()->user();

        CustomerTimeline::create([
            'tenant_id' => $user?->tenant_id,
            'branch_id' => $branchId ?? $user?->active_branch_id,
            'customer_id' => $customerId,
            'sale_id' => $saleId,
            'type' => $type,
            'event_type' => $type,
            'title' => $title,
            'description' => $description,
            'metadata' => $metadata,
            'occurred_at' => now(),
        ]);
    }
}

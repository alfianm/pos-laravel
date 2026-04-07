<?php

namespace App\Services;

use App\Constants\ReturnStatus;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\SaleReturn;
use App\Models\ReturnItem;
use App\Models\Customer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ReturnService
{
    protected $stockService;

    public function __construct(StockService $stockService)
    {
        $this->stockService = $stockService;
    }

    /**
     * Create a return record from a sale.
     * $data format: [
     *   'sale_id' => '...',
     *   'return_date' => '...',
     *   'notes' => '...',
     *   'items' => [
     *      ['sale_item_id' => '...', 'qty' => 1, 'return_reason_id' => '...', 'notes' => '...'],
     *      ...
     *    ]
     * ]
     */
    public function createReturn(array $data): SaleReturn
    {
        return DB::transaction(function () use ($data) {
            $user = auth()->user();
            $sale = Sale::findOrFail($data['sale_id']);
            $tenantId = $user->tenant_id;
            $branchId = $user->active_branch_id;

            // 1. Create Return Header
            $return = SaleReturn::create([
                'tenant_id' => $tenantId,
                'branch_id' => $branchId,
                'user_id' => $user->id,
                'sale_id' => $sale->id,
                'customer_id' => $sale->customer_id,
                'return_number' => $this->generateReturnNo($tenantId),
                'return_date' => $data['return_date'] ?? now(),
                'status' => ReturnStatus::PENDING,
                'notes' => $data['notes'] ?? null,
                'subtotal' => 0,
                'tax_amount' => 0,
                'total_amount' => 0,
                'refund_amount' => 0,
                'refund_status' => ReturnStatus::REFUND_PENDING,
            ]);

            $totalSubtotal = 0;

            // 2. Create Return Items
            foreach ($data['items'] as $itemData) {
                $saleItem = SaleItem::findOrFail($itemData['sale_item_id']);
                
                $subtotal = $saleItem->unit_price * $itemData['qty'];
                $totalSubtotal += $subtotal;

                ReturnItem::create([
                    'return_id' => $return->id,
                    'sale_item_id' => $saleItem->id,
                    'product_id' => $saleItem->product_id,
                    'return_reason_id' => $itemData['return_reason_id'] ?? null,
                    'quantity' => $itemData['qty'],
                    'unit_price' => $saleItem->unit_price,
                    'subtotal' => $subtotal,
                    'notes' => $itemData['notes'] ?? null,
                ]);
            }

            // Update totals (simplification for MVP: no tax return calculation yet)
            $return->update([
                'subtotal' => $totalSubtotal,
                'total_amount' => $totalSubtotal,
            ]);

            return $return;
        });
    }

    /**
     * Complete/Process the return (Finalize).
     */
    public function completeReturn(SaleReturn $return): void
    {
        if ($return->status === ReturnStatus::COMPLETED) {
            throw new \Exception('Return sudah selesai diproses.');
        }

        DB::transaction(function () use ($return) {
            $user = auth()->user();

            // 1. Update Inventory for each item
            foreach ($return->items as $item) {
                $this->stockService->recordMovement([
                    'tenant_id' => $return->tenant_id,
                    'branch_id' => $return->branch_id,
                    'product_id' => $item->product_id,
                    'qty' => $item->quantity, // Positive to increase stock back
                    'movement_type' => 'sale_return',
                    'reference_type' => 'SaleReturn',
                    'reference_id' => $return->id,
                    'reference_no' => $return->return_number,
                    'performed_by' => $user->id,
                    'notes' => 'Return from INV: ' . ($return->sale?->sale_no ?? 'N/A'),
                ]);
            }

            // 2. Update Customer Stats (Reduce total spent)
            if ($return->customer_id) {
                $customer = Customer::find($return->customer_id);
                if ($customer) {
                    $customer->decrement('total_spent', (float) $return->total_amount);
                }
            }

            // 3. Update Status
            $return->update([
                'status' => ReturnStatus::COMPLETED,
            ]);

            // 4. Dispatch Event for Accounting
            event(new \App\Events\SaleReturnCompleted($return));
        });
    }

    /**
     * Generate a unique return number.
     */
    private function generateReturnNo($tenantId)
    {
        $prefix = 'RET/'.date('Y/m/d');
        $count = SaleReturn::where('tenant_id', $tenantId)
            ->whereDate('return_date', date('Y-m-d'))
            ->count();

        return $prefix.'/'.str_pad($count + 1, 5, '0', STR_PAD_LEFT);
    }
}

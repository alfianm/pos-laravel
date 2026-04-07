<?php

namespace App\Listeners;

use App\Events\SaleReturnCompleted;
use App\Models\ChartOfAccount;
use App\Services\JournalEntryService;
use Illuminate\Support\Facades\Log;

class ReturnJournalListener
{
    public function __construct(
        protected JournalEntryService $journalService
    ) {}

    public function handle(SaleReturnCompleted $event): void
    {
        $return = $event->return;
        $tenantId = $return->tenant_id;

        try {
            // 1. Prepare Revenue Reversal Entry
            $lines = [];

            // DEBIT Revenue (Reversing Sales) - '4101'
            $revenueAccount = ChartOfAccount::where('tenant_id', $tenantId)
                ->where('account_code', '4101')
                ->first();

            if ($revenueAccount) {
                $lines[] = [
                    'account_id' => $revenueAccount->id,
                    'description' => 'Retur Penjualan #' . $return->return_number . ' (Reversal of Sale #' . ($return->sale?->sale_no ?? '') . ')',
                    'debit' => $return->total_amount,
                    'credit' => 0,
                ];
            }

            // CREDIT Cash/Bank (Refund) - '1111'
            $cashAccount = ChartOfAccount::where('tenant_id', $tenantId)
                ->where('account_code', '1111')
                ->first();

            if ($cashAccount) {
                $lines[] = [
                    'account_id' => $cashAccount->id,
                    'description' => 'Pengembalian Dana (Refund) #' . $return->return_number,
                    'debit' => 0,
                    'credit' => $return->total_amount,
                ];
            }

            if (count($lines) >= 2) {
                $this->journalService->create([
                    'tenant_id' => $tenantId,
                    'branch_id' => $return->branch_id,
                    'date' => $return->return_date,
                    'reference_type' => 'SaleReturn',
                    'reference_id' => $return->id,
                    'description' => 'Jurnal Retur Penjualan #' . $return->return_number,
                    'lines' => $lines,
                ]);
            }

            // 2. Prepare Inventory & COGS Reversal
            $cogsTotal = 0;
            foreach ($return->items as $item) {
                // We need to get the cost price at the time of sale
                // For simplicity, we use the current product cost price or fallback to sale item cost
                $cogsTotal += ($item->saleItem?->cost_price ?? $item->product?->cost_price ?? 0) * $item->quantity;
            }

            if ($cogsTotal > 0) {
                $cogsLines = [];

                // DEBIT Inventory - '1131'
                $inventoryAccount = ChartOfAccount::where('tenant_id', $tenantId)
                    ->where('account_code', '1131')
                    ->first();
                
                if ($inventoryAccount) {
                    $cogsLines[] = [
                        'account_id' => $inventoryAccount->id,
                        'description' => 'Penerimaan Kembali Persediaan via Retur #' . $return->return_number,
                        'debit' => $cogsTotal,
                        'credit' => 0,
                    ];
                }

                // CREDIT COGS (reverse) - '5101'
                $cogsAccount = ChartOfAccount::where('tenant_id', $tenantId)
                    ->where('account_code', '5101')
                    ->first();

                if ($cogsAccount) {
                    $cogsLines[] = [
                        'account_id' => $cogsAccount->id,
                        'description' => 'Koreksi HPP via Retur #' . $return->return_number,
                        'debit' => 0,
                        'credit' => $cogsTotal,
                    ];
                }

                if (count($cogsLines) >= 2) {
                    $this->journalService->create([
                        'tenant_id' => $tenantId,
                        'branch_id' => $return->branch_id,
                        'date' => $return->return_date,
                        'reference_type' => 'SaleReturn',
                        'reference_id' => $return->id,
                        'description' => 'Jurnal Koreksi HPP Retur #' . $return->return_number,
                        'lines' => $cogsLines,
                    ]);
                }
            }

        } catch (\Exception $e) {
            Log::error('Accounting: Failed to generate journal entry for return ' . $return->id, [
                'error' => $e->getMessage()
            ]);
        }
    }
}

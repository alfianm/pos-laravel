<?php

namespace App\Listeners;

use App\Events\SaleCreated;
use App\Models\ChartOfAccount;
use App\Services\JournalEntryService;
use Illuminate\Support\Facades\Log;

class SaleJournalListener
{
    /**
     * Create the event listener.
     */
    public function __construct(
        protected JournalEntryService $journalService
    ) {}

    /**
     * Handle the event.
     */
    public function handle(SaleCreated $event): void
    {
        $sale = $event->sale;
        $tenantId = $sale->tenant_id;

        try {
            // 1. Prepare Revenue Entry
            $lines = [];

            // DEBIT Account (Payment Account)
            // For now, mapping everything to '1111' - Kas Kantor
            // In a real system, we'd check the payment method
            $cashAccount = ChartOfAccount::where('tenant_id', $tenantId)
                ->where('account_code', '1111')
                ->first();

            if ($cashAccount) {
                $lines[] = [
                    'account_id' => $cashAccount->id,
                    'description' => 'Penerimaan Penjualan #' . $sale->sale_no,
                    'debit' => $sale->grand_total,
                    'credit' => 0,
                ];
            } else {
                Log::warning('Accounting: Cash account (1111) not found for tenant ' . $tenantId);
            }

            // CREDIT Account (Revenue) - '4101'
            $revenueAccount = ChartOfAccount::where('tenant_id', $tenantId)
                ->where('account_code', '4101')
                ->first();

            if ($revenueAccount) {
                $lines[] = [
                    'account_id' => $revenueAccount->id,
                    'description' => 'Penjualan #' . $sale->sale_no,
                    'debit' => 0,
                    'credit' => $sale->grand_total,
                ];
            } else {
                Log::warning('Accounting: Sales Revenue account (4101) not found for tenant ' . $tenantId);
            }

            if (count($lines) >= 2) {
                $this->journalService->create([
                    'tenant_id' => $tenantId,
                    'branch_id' => $sale->branch_id,
                    'date' => $sale->sale_date,
                    'reference_type' => 'Sale',
                    'reference_id' => $sale->id,
                    'description' => 'Jurnal Penjualan #' . $sale->sale_no,
                    'lines' => $lines,
                ]);
            }

            // 2. Prepare COGS Entry (if products have cost price)
            $cogsTotal = 0;
            foreach ($sale->items as $item) {
                $cogsTotal += ($item->product->cost_price ?? 0) * $item->qty;
            }

            if ($cogsTotal > 0) {
                $cogsLines = [];

                // DEBIT COGS - '5101'
                $cogsAccount = ChartOfAccount::where('tenant_id', $tenantId)
                    ->where('account_code', '5101')
                    ->first();
                
                if ($cogsAccount) {
                    $cogsLines[] = [
                        'account_id' => $cogsAccount->id,
                        'description' => 'HPP Penjualan #' . $sale->sale_no,
                        'debit' => $cogsTotal,
                        'credit' => 0,
                    ];
                }

                // CREDIT Inventory - '1131'
                $inventoryAccount = ChartOfAccount::where('tenant_id', $tenantId)
                    ->where('account_code', '1131')
                    ->first();

                if ($inventoryAccount) {
                    $cogsLines[] = [
                        'account_id' => $inventoryAccount->id,
                        'description' => 'Pengeluaran Persediaan #' . $sale->sale_no,
                        'debit' => 0,
                        'credit' => $cogsTotal,
                    ];
                }

                if (count($cogsLines) >= 2) {
                    $this->journalService->create([
                        'tenant_id' => $tenantId,
                        'branch_id' => $sale->branch_id,
                        'date' => $sale->sale_date,
                        'reference_type' => 'Sale',
                        'reference_id' => $sale->id,
                        'description' => 'Jurnal HPP & Persediaan #' . $sale->sale_no,
                        'lines' => $cogsLines,
                    ]);
                }
            }

        } catch (\Exception $e) {
            Log::error('Accounting: Failed to generate journal entry for sale ' . $sale->id, [
                'error' => $e->getMessage()
            ]);
        }
    }
}

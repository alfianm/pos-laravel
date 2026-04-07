<?php

namespace App\Listeners;

use App\Events\PurchaseOrderReceived;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

use App\Models\ChartOfAccount;
use App\Services\JournalEntryService;
use Illuminate\Support\Facades\Log;

class PurchaseJournalListener
{
    /**
     * Create the event listener.
     */
    public function __construct(protected JournalEntryService $journalService)
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(PurchaseOrderReceived $event): void
    {
        $po = $event->purchaseOrder;
        $tenantId = $po->tenant_id;

        try {
            $lines = [];

            // DEBIT Inventory - '1131' (Persediaan Barang Dagangan)
            $inventoryAccount = ChartOfAccount::where('tenant_id', $tenantId)
                ->where('account_code', '1131')
                ->first();

            if ($inventoryAccount) {
                $lines[] = [
                    'account_id' => $inventoryAccount->id,
                    'description' => 'Penerimaan Persediaan via PO #' . $po->purchase_no,
                    'debit' => $po->grand_total,
                    'credit' => 0,
                ];
            } else {
                Log::warning('Accounting: Inventory account (1131) not found for tenant ' . $tenantId);
            }

            // CREDIT Accounts Payable - '2111' (Hutang Usaha)
            $apAccount = ChartOfAccount::where('tenant_id', $tenantId)
                ->where('account_code', '2111')
                ->first();

            if ($apAccount) {
                $lines[] = [
                    'account_id' => $apAccount->id,
                    'description' => 'Hutang Usaha via PO #' . $po->purchase_no,
                    'debit' => 0,
                    'credit' => $po->grand_total,
                ];
            } else {
                Log::warning('Accounting: Accounts Payable account (2111) not found for tenant ' . $tenantId);
            }

            if (count($lines) >= 2) {
                $this->journalService->create([
                    'tenant_id' => $tenantId,
                    'branch_id' => $po->branch_id,
                    'date' => now(),
                    'reference_type' => 'PurchaseOrder',
                    'reference_id' => $po->id,
                    'description' => 'Jurnal Transaksi Pembelian #' . $po->purchase_no,
                    'lines' => $lines,
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Accounting: Failed to generate journal entry for PO ' . $po->id, [
                'error' => $e->getMessage()
            ]);
        }
    }
}

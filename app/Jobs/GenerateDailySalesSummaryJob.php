<?php

namespace App\Jobs;

use App\Models\Branch;
use App\Models\Sale;
use App\Models\Tenant;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GenerateDailySalesSummaryJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public ?string $date = null
    ) {
        $this->date = $date ?? now()->subDay()->toDateString();
    }

    public function handle(): void
    {
        $summaryDate = $this->date;
        Log::info("Generating daily sales summary for: {$summaryDate}");

        // Loop per tenant untuk isolasi data yang aman
        Tenant::all()->each(function (Tenant $tenant) use ($summaryDate) {
            $this->processTenantSummary($tenant, $summaryDate);
        });
    }

    protected function processTenantSummary(Tenant $tenant, string $date): void
    {
        $summary = Sale::query()
            ->where('tenant_id', $tenant->id)
            ->whereDate('sale_date', $date)
            ->where('status', 'completed')
            ->select(
                'branch_id',
                DB::raw('COUNT(*) as total_transactions'),
                DB::raw('SUM(grand_total) as total_sales'),
                DB::raw('SUM(discount_amount) as total_discounts')
            )
            ->groupBy('branch_id')
            ->with('branch')
            ->get();

        if ($summary->isEmpty()) {
            return;
        }

        foreach ($summary as $item) {
            $branchName = $item->branch->name ?? 'Unknown Branch';
            
            // Simpan ke log aplikasi sebagai placeholder sebelum implementasi tabel report_summaries atau email.
            Log::info("DAILY SUMMARY [{$tenant->name}] - {$branchName} - {$date}: " . json_encode([
                'transactions' => $item->total_transactions,
                'sales' => number_format($item->total_sales, 0, ',', '.'),
                'discounts' => number_format($item->total_discounts, 0, ',', '.'),
            ]));
        }
    }
}

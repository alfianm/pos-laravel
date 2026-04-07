<?php

namespace App\Jobs;

use App\Models\Tenant;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class MonthlyInvoiceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = [60, 300, 600];

    public function __construct(
        private readonly ?string $targetDate = null
    ) {
    }

    public function handle(): void
    {
        // Determine the period for invoicing
        $targetDate = $this->targetDate ? Carbon::parse($this->targetDate) : now();

        // For monthly invoicing, we invoice for the previous month
        $periodStart = $targetDate->copy()->subMonth()->startOfMonth()->format('Y-m-d');
        $periodEnd = $targetDate->copy()->subMonth()->endOfMonth()->format('Y-m-d');

        Log::info('Starting monthly invoice generation job', [
            'period_start' => $periodStart,
            'period_end' => $periodEnd,
        ]);

        // Get all active tenants with subscriptions
        $tenants = Tenant::where('is_active', true)
            ->whereNotNull('plan')
            ->whereNotNull('subscription_started_at')
            ->cursor();

        $queuedCount = 0;
        $skippedCount = 0;

        foreach ($tenants as $tenant) {
            try {
                // Check if tenant already has an invoice for this period
                $existingInvoice = \App\Models\Invoice::where('tenant_id', $tenant->id)
                    ->where('period_start', $periodStart)
                    ->where('period_end', $periodEnd)
                    ->whereIn('status', ['draft', 'pending', 'paid'])
                    ->exists();

                if ($existingInvoice) {
                    $skippedCount++;
                    Log::info('Skipping tenant - invoice already exists for period', [
                        'tenant_id' => $tenant->id,
                        'period' => $periodStart . ' to ' . $periodEnd,
                    ]);
                    continue;
                }

                // Queue individual invoice generation job
                GenerateInvoiceJob::dispatch(
                    $tenant,
                    $periodStart,
                    $periodEnd
                )->onQueue('invoicing');

                $queuedCount++;

                Log::info('Queued invoice generation for tenant', [
                    'tenant_id' => $tenant->id,
                    'tenant_name' => $tenant->name,
                    'period' => $periodStart . ' to ' . $periodEnd,
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to queue invoice for tenant', [
                    'tenant_id' => $tenant->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        Log::info('Monthly invoice job completed', [
            'period_start' => $periodStart,
            'period_end' => $periodEnd,
            'tenants_queued' => $queuedCount,
            'tenants_skipped' => $skippedCount,
        ]);
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('MonthlyInvoiceJob failed', [
            'target_date' => $this->targetDate,
            'error' => $exception->getMessage(),
        ]);
    }
}
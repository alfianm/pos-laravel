<?php

namespace App\Jobs;

use App\Models\Tenant;
use App\Models\Invoice;
use App\Services\InvoiceService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GenerateInvoiceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = [60, 300, 600];

    public function __construct(
        private readonly Tenant $tenant,
        private readonly string $periodStart,
        private readonly string $periodEnd
    ) {
    }

    public function handle(InvoiceService $invoiceService): void
    {
        DB::beginTransaction();

        try {
            // Check if invoice already exists for this period
            $existingInvoice = Invoice::where('tenant_id', $this->tenant->id)
                ->where('period_start', $this->periodStart)
                ->where('period_end', $this->periodEnd)
                ->whereIn('status', ['draft', 'pending', 'paid'])
                ->first();

            if ($existingInvoice) {
                Log::info('Invoice already exists for period', [
                    'tenant_id' => $this->tenant->id,
                    'invoice_id' => $existingInvoice->id,
                    'period' => $this->periodStart . ' to ' . $this->periodEnd,
                ]);

                DB::commit();

                return;
            }

            // Calculate usage-based charges
            $usageStats = $this->calculateUsageStats();

            // Generate invoice
            $invoiceData = [
                'tenant_id' => $this->tenant->id,
                'invoice_number' => $invoiceService->generateInvoiceNumber($this->tenant),
                'period_start' => $this->periodStart,
                'period_end' => $this->periodEnd,
                'due_date' => now()->addDays(7)->format('Y-m-d'),
                'status' => 'draft',
                'line_items' => $this->buildLineItems($usageStats),
            ];

            $invoice = $invoiceService->create($invoiceData);

            // Send notification
            if ($this->tenant->owner) {
                $this->tenant->owner->notify(new \App\Notifications\InvoiceGeneratedNotification($invoice));
            }

            Log::info('Invoice generated successfully', [
                'tenant_id' => $this->tenant->id,
                'invoice_id' => $invoice->id,
                'amount' => $invoice->total_amount,
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Failed to generate invoice', [
                'tenant_id' => $this->tenant->id,
                'period' => $this->periodStart . ' to ' . $this->periodEnd,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    private function calculateUsageStats(): array
    {
        $startDate = $this->periodStart;
        $endDate = $this->periodEnd;

        return [
            'total_sales' => DB::table('sales')
                ->where('tenant_id', $this->tenant->id)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count(),
            'total_revenue' => DB::table('sales')
                ->where('tenant_id', $this->tenant->id)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->sum('total_amount') ?? 0,
            'api_calls' => DB::table('api_request_logs')
                ->where('tenant_id', $this->tenant->id)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count(),
            'storage_mb' => $this->calculateStorageUsage(),
            'active_users' => DB::table('users')
                ->where('tenant_id', $this->tenant->id)
                ->where('is_active', true)
                ->count(),
            'branches' => DB::table('branches')
                ->where('tenant_id', $this->tenant->id)
                ->count(),
        ];
    }

    private function calculateStorageUsage(): float
    {
        // Calculate total file storage in MB
        $mediaSize = DB::table('media')
            ->where('tenant_id', $this->tenant->id)
            ->sum('size') ?? 0;

        return round($mediaSize / 1024 / 1024, 2);
    }

    private function buildLineItems(array $usageStats): array
    {
        $lineItems = [];
        $plan = $this->tenant->plan ?? 'basic';

        // Base subscription fee
        $basePrice = match ($plan) {
            'enterprise' => 5000000,
            'professional' => 2000000,
            'basic' => 500000,
            default => 500000,
        };

        $lineItems[] = [
            'description' => 'Biaya Langganan ' . ucfirst($plan),
            'quantity' => 1,
            'unit_price' => $basePrice,
            'amount' => $basePrice,
        ];

        // Usage-based charges (if over limit)
        $quota = $this->tenant->quota;
        if ($quota) {
            // Branch overage
            $branchLimit = $quota->max_branches ?? 1;
            if ($usageStats['branches'] > $branchLimit) {
                $overage = $usageStats['branches'] - $branchLimit;
                $charge = $overage * 100000; // Rp 100,000 per extra branch
                $lineItems[] = [
                    'description' => "Cabang Tambahan ({$overage} cabang)",
                    'quantity' => $overage,
                    'unit_price' => 100000,
                    'amount' => $charge,
                ];
            }

            // User overage
            $userLimit = $quota->max_users ?? 3;
            if ($usageStats['active_users'] > $userLimit) {
                $overage = $usageStats['active_users'] - $userLimit;
                $charge = $overage * 50000; // Rp 50,000 per extra user
                $lineItems[] = [
                    'description' => "Pengguna Tambahan ({$overage} pengguna)",
                    'quantity' => $overage,
                    'unit_price' => 50000,
                    'amount' => $charge,
                ];
            }

            // API overage
            $apiLimit = $quota->max_api_calls_per_minute ?? 1000;
            $monthlyApiLimit = $apiLimit * 60 * 24 * 30; // Rough estimate
            if ($usageStats['api_calls'] > $monthlyApiLimit) {
                $overage = $usageStats['api_calls'] - $monthlyApiLimit;
                $charge = ceil($overage / 10000) * 50000; // Rp 50,000 per 10k calls
                $lineItems[] = [
                    'description' => 'Penggunaan API Berlebih',
                    'quantity' => $overage,
                    'unit_price' => 0.005,
                    'amount' => $charge,
                ];
            }
        }

        return $lineItems;
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('GenerateInvoiceJob failed after all retries', [
            'tenant_id' => $this->tenant->id,
            'period' => $this->periodStart . ' to ' . $this->periodEnd,
            'error' => $exception->getMessage(),
        ]);
    }
}
<?php

namespace App\Jobs;

use App\Models\Tenant;
use App\Models\TenantQuota;
use App\Events\QuotaThresholdReached;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DailyQuotaCheckJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = [30, 60, 120];

    private array $thresholds = [50, 75, 90, 95, 100];

    public function handle(): void
    {
        Log::info('Starting daily quota check');

        $activeTenants = Tenant::where('is_active', true)
            ->whereNotNull('plan')
            ->with('quota')
            ->cursor();

        $checkedCount = 0;
        $alertCount = 0;

        foreach ($activeTenants as $tenant) {
            try {
                $this->checkTenantQuotas($tenant);
                $checkedCount++;
            } catch (\Exception $e) {
                Log::error('Failed to check quota for tenant', [
                    'tenant_id' => $tenant->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        Log::info('Daily quota check completed', [
            'checked_tenants' => $checkedCount,
            'alerts_triggered' => $alertCount,
        ]);
    }

    private function checkTenantQuotas(Tenant $tenant): void
    {
        $quota = $tenant->quota;
        if (!$quota) {
            return;
        }

        $metrics = $this->gatherMetrics($tenant, $quota);

        foreach ($metrics as $metricName => $metricData) {
            $this->evaluateMetric($tenant, $quota, $metricName, $metricData);
        }
    }

    private function gatherMetrics(Tenant $tenant, TenantQuota $quota): array
    {
        // Branch count
        $branchCount = DB::table('branches')
            ->where('tenant_id', $tenant->id)
            ->count();

        // Active users
        $activeUsers = DB::table('users')
            ->where('tenant_id', $tenant->id)
            ->where('is_active', true)
            ->count();

        // API calls in last 24 hours
        $dailyApiCalls = DB::table('api_request_logs')
            ->where('tenant_id', $tenant->id)
            ->where('created_at', '>=', now()->subDay())
            ->count();

        // Storage usage in MB
        $storageBytes = DB::table('media')
            ->where('tenant_id', $tenant->id)
            ->sum('size') ?? 0;
        $storageMB = round($storageBytes / 1024 / 1024, 2);

        // Product count
        $productCount = DB::table('products')
            ->where('tenant_id', $tenant->id)
            ->whereNull('deleted_at')
            ->count();

        return [
            'branches' => [
                'current' => $branchCount,
                'limit' => $quota->max_branches ?? 1,
                'unit' => 'cabang',
            ],
            'users' => [
                'current' => $activeUsers,
                'limit' => $quota->max_users ?? 3,
                'unit' => 'pengguna',
            ],
            'api_calls' => [
                'current' => $dailyApiCalls,
                'limit' => $quota->max_api_calls_per_minute ?? 100,
                'unit' => 'panggilan/hari',
            ],
            'storage' => [
                'current' => $storageMB,
                'limit' => $quota->max_storage_mb ?? 1024,
                'unit' => 'MB',
            ],
            'products' => [
                'current' => $productCount,
                'limit' => $quota->max_products ?? 1000,
                'unit' => 'produk',
            ],
        ];
    }

    private function evaluateMetric(Tenant $tenant, TenantQuota $quota, string $metricName, array $metricData): void
    {
        $current = $metricData['current'];
        $limit = $metricData['limit'];
        $unit = $metricData['unit'];

        if ($limit <= 0) {
            return;
        }

        $percentage = min(100, round(($current / $limit) * 100, 2));

        // Check if we've already alerted for this threshold today
        $lastAlertKey = "quota_alert_{$tenant->id}_{$metricName}_" . now()->format('Y-m-d');
        $lastAlertPercentage = cache()->get($lastAlertKey, 0);

        foreach ($this->thresholds as $threshold) {
            // Only alert if crossing up (current >= threshold, last < threshold)
            if ($percentage >= $threshold && $lastAlertPercentage < $threshold) {
                $this->triggerAlert($tenant, $metricName, $current, $limit, $unit, $percentage, $threshold);
                cache()->put($lastAlertKey, $threshold, now()->addDay());
                break; // Only alert once per job run (highest threshold crossed)
            }
        }

        // Always update cache with current percentage at end of day check
        cache()->put($lastAlertKey, $percentage, now()->addDay());
    }

    private function triggerAlert(
        Tenant $tenant,
        string $metricName,
        int|float $current,
        int $limit,
        string $unit,
        float $percentage,
        int $threshold
    ): void {
        $metricLabels = [
            'branches' => 'Cabang',
            'users' => 'Pengguna Aktif',
            'api_calls' => 'Pemanggilan API',
            'storage' => 'Penyimpanan',
            'products' => 'Produk',
        ];

        $severity = match (true) {
            $percentage >= 100 => 'critical',
            $percentage >= 90 => 'high',
            $percentage >= 75 => 'medium',
            default => 'low',
        };

        // Dispatch event for listeners
        event(new QuotaThresholdReached($tenant, $metricName, $current, $limit, $threshold));

        // Log the alert
        Log::warning('Quota threshold reached', [
            'tenant_id' => $tenant->id,
            'tenant_name' => $tenant->name,
            'metric' => $metricName,
            'current' => $current,
            'limit' => $limit,
            'unit' => $unit,
            'percentage' => $percentage,
            'threshold' => $threshold,
            'severity' => $severity,
        ]);

        // Send notification to tenant owner if critical or high
        if (in_array($severity, ['critical', 'high']) && $tenant->owner) {
            $tenant->owner->notify(new \App\Notifications\QuotaAlertNotification(
                $metricLabels[$metricName] ?? $metricName,
                $current,
                $limit,
                $unit,
                $percentage,
                $severity
            ));
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('DailyQuotaCheckJob failed', [
            'error' => $exception->getMessage(),
        ]);
    }
}
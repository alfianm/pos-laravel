<?php

namespace App\Listeners;

use App\Events\QuotaThresholdReached;
use App\Models\Tenant;
use App\Notifications\QuotaAlertNotification;
use Illuminate\Support\Facades\Log;

class QuotaThresholdListener
{
    public function __construct()
    {
    }

    public function handle(QuotaThresholdReached $event): void
    {
        $tenant = $event->tenant;
        $metric = $event->metric;
        $usage = $event->usage;
        $threshold = $event->threshold;

        // Get tenant's primary admin user
        $adminUser = $this->getTenantAdmin($tenant);

        if (!$adminUser) {
            Log::warning('No admin user found for quota alert', [
                'tenant_id' => $tenant->id,
                'metric' => $metric,
            ]);

            return;
        }

        // Get limit from tenant quota
        $quota = $tenant->quota;
        if (!$quota) {
            Log::warning('No quota configuration found for tenant', [
                'tenant_id' => $tenant->id,
            ]);

            return;
        }

        $limit = match ($metric) {
            'branches' => $quota->branches_limit,
            'products' => $quota->products_limit,
            'users' => $quota->users_limit,
            'monthly_sales' => $quota->monthly_sales_limit,
            'transactions_per_month' => $quota->transactions_per_month_limit,
            'api_calls_per_minute' => $quota->api_calls_per_minute_limit,
            'webhook_calls_per_day' => $quota->webhook_calls_per_day_limit,
            default => null,
        };

        if ($limit === null || $limit === 0) {
            Log::warning('Invalid or unlimited quota limit', [
                'tenant_id' => $tenant->id,
                'metric' => $metric,
                'limit' => $limit,
            ]);

            return;
        }

        // Calculate percentage
        $percentage = min(100, ($usage / $limit) * 100);

        // Determine severity based on threshold
        $severity = match (true) {
            $threshold >= 95 => 'critical',
            $threshold >= 85 => 'high',
            $threshold >= 70 => 'medium',
            default => 'low',
        };

        // Get unit for metric
        $unit = $this->getUnitForMetric($metric);

        // Send notification
        try {
            $adminUser->notify(new QuotaAlertNotification(
                metricName: $this->getMetricDisplayName($metric),
                current: $usage,
                limit: $limit,
                unit: $unit,
                percentage: round($percentage, 2),
                severity: $severity
            ));

            Log::info('Quota alert notification sent', [
                'tenant_id' => $tenant->id,
                'user_id' => $adminUser->id,
                'metric' => $metric,
                'severity' => $severity,
                'percentage' => $percentage,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send quota alert notification', [
                'tenant_id' => $tenant->id,
                'metric' => $metric,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Get the primary admin user for the tenant.
     */
    private function getTenantAdmin(Tenant $tenant): ?\App\Models\User
    {
        // First, try to find a user with super-admin or admin role in this tenant
        $admin = \App\Models\User::whereHas('tenants', function ($query) use ($tenant) {
            $query->where('tenant_id', $tenant->id);
        })
            ->whereHas('roles', function ($query) {
                $query->whereIn('name', ['super-admin', 'tenant-admin', 'admin']);
            })
            ->first();

        if ($admin) {
            return $admin;
        }

        // Fallback: return any user associated with this tenant
        return \App\Models\User::whereHas('tenants', function ($query) use ($tenant) {
            $query->where('tenant_id', $tenant->id);
        })->first();
    }

    /**
     * Get display name for metric.
     */
    private function getMetricDisplayName(string $metric): string
    {
        return match ($metric) {
            'branches' => 'Cabang',
            'products' => 'Produk',
            'users' => 'Pengguna',
            'monthly_sales' => 'Penjualan Bulanan',
            'transactions_per_month' => 'Transaksi per Bulan',
            'api_calls_per_minute' => 'API Calls per Menit',
            'webhook_calls_per_day' => 'Webhook Calls per Hari',
            default => $metric,
        };
    }

    /**
     * Get unit for metric.
     */
    private function getUnitForMetric(string $metric): string
    {
        return match ($metric) {
            'monthly_sales' => 'IDR',
            'api_calls_per_minute', 'webhook_calls_per_day', 'transactions_per_month' => 'calls',
            default => 'items',
        };
    }
}
<?php

namespace App\Services;

use App\Constants\Subscription;
use App\Exceptions\QuotaExceededException;
use App\Models\Branch;
use App\Models\Product;
use App\Models\SubscriptionPlan;
use App\Models\Tenant;
use App\Models\TenantQuota;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class QuotaService
{
    private const CACHE_TTL = 300; // 5 minutes

    public function getOrCreateQuota(string $tenantId, string $quotaType): TenantQuota
    {
        return Cache::remember(
            "quota:{$tenantId}:{$quotaType}",
            self::CACHE_TTL,
            function () use ($tenantId, $quotaType) {
                return TenantQuota::firstOrCreate(
                    [
                        'tenant_id' => $tenantId,
                        'quota_type' => $quotaType,
                    ],
                    [
                        'id' => Str::uuid(),
                        'limit_value' => $this->getDefaultLimit($tenantId, $quotaType),
                        'used_value' => $this->calculateCurrentUsage($tenantId, $quotaType),
                        'alert_threshold' => 80,
                    ]
                );
            }
        );
    }

    public function getDefaultLimit(string $tenantId, string $quotaType): int
    {
        $tenant = Tenant::with('subscriptions.plan')->find($tenantId);

        if (! $tenant) {
            return 0;
        }

        $subscription = $tenant->subscriptions()->active()->first();

        if (! $subscription) {
            $freePlan = SubscriptionPlan::where('code', Subscription::PLAN_FREE)->first();
            if ($freePlan) {
                return $freePlan->features[$this->getFeatureKey($quotaType)] ?? 0;
            }

            return 0;
        }

        $plan = $subscription->plan;
        if (! $plan) {
            return 0;
        }

        return $plan->features[$this->getFeatureKey($quotaType)] ?? 0;
    }

    public function getFeatureKey(string $quotaType): string
    {
        return match ($quotaType) {
            Subscription::QUOTA_BRANCHES => 'max_branches',
            Subscription::QUOTA_PRODUCTS => 'max_products',
            Subscription::QUOTA_USERS => 'max_users',
            Subscription::QUOTA_TRANSACTIONS => 'max_monthly_transactions',
            Subscription::QUOTA_STORAGE => 'storage_mb',
            default => 'max_'.$quotaType,
        };
    }

    public function calculateCurrentUsage(string $tenantId, string $quotaType): int
    {
        return match ($quotaType) {
            Subscription::QUOTA_BRANCHES => Branch::where('tenant_id', $tenantId)->count(),
            Subscription::QUOTA_PRODUCTS => Product::where('tenant_id', $tenantId)->count(),
            Subscription::QUOTA_USERS => User::where('tenant_id', $tenantId)->where('is_super_admin', false)->count(),
            Subscription::QUOTA_TRANSACTIONS => $this->getMonthlyTransactionCount($tenantId),
            Subscription::QUOTA_STORAGE => $this->getStorageUsage($tenantId),
            default => 0,
        };
    }

    public function getMonthlyTransactionCount(string $tenantId): int
    {
        $cacheKey = "quota:{$tenantId}:monthly_transactions:".now()->format('Y-m');

        return Cache::remember($cacheKey, 3600, function () use ($tenantId) {
            return DB::table('sales')
                ->where('tenant_id', $tenantId)
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count();
        });
    }

    public function getStorageUsage(string $tenantId): int
    {
        return 0;
    }

    public function checkQuota(string $tenantId, string $quotaType, int $requestedAmount = 1): array
    {
        $quota = $this->getOrCreateQuota($tenantId, $quotaType);

        if ($quota->isUnlimited()) {
            return [
                'allowed' => true,
                'remaining' => PHP_INT_MAX,
                'message' => null,
            ];
        }

        $remaining = $quota->remaining();

        if ($remaining < $requestedAmount) {
            return [
                'allowed' => false,
                'remaining' => $remaining,
                'message' => "Quota {$quotaType} exceeded. Limit: {$quota->limit_value}, Used: {$quota->used_value}, Requested: {$requestedAmount}",
            ];
        }

        return [
            'allowed' => true,
            'remaining' => $remaining,
            'message' => null,
        ];
    }

    public function enforceQuota(string $tenantId, string $quotaType, int $requestedAmount = 1): void
    {
        $result = $this->checkQuota($tenantId, $quotaType, $requestedAmount);

        if (! $result['allowed']) {
            throw new QuotaExceededException($result['message']);
        }
    }

    public function incrementQuotaUsage(string $tenantId, string $quotaType, int $amount = 1): void
    {
        $quota = $this->getOrCreateQuota($tenantId, $quotaType);
        $quota->incrementUsage($amount);

        $this->clearQuotaCache($tenantId, $quotaType);
    }

    public function decrementQuotaUsage(string $tenantId, string $quotaType, int $amount = 1): void
    {
        $quota = $this->getOrCreateQuota($tenantId, $quotaType);
        $quota->decrementUsage($amount);

        $this->clearQuotaCache($tenantId, $quotaType);
    }

    public function recalculateAllQuotas(string $tenantId): void
    {
        foreach (Subscription::QUOTA_TYPES as $quotaType) {
            $quota = $this->getOrCreateQuota($tenantId, $quotaType);
            $quota->update([
                'limit_value' => $this->getDefaultLimit($tenantId, $quotaType),
                'used_value' => $this->calculateCurrentUsage($tenantId, $quotaType),
                'last_calculated_at' => now(),
            ]);

            $this->clearQuotaCache($tenantId, $quotaType);
        }
    }

    public function getQuotaSummary(string $tenantId): array
    {
        $summary = [];

        foreach (Subscription::QUOTA_TYPES as $quotaType) {
            $quota = $this->getOrCreateQuota($tenantId, $quotaType);

            $summary[$quotaType] = [
                'limit' => $quota->limit_value,
                'used' => $quota->used_value,
                'remaining' => $quota->remaining(),
                'percentage' => $quota->usagePercentage(),
                'is_unlimited' => $quota->isUnlimited(),
                'is_approaching_limit' => $quota->isApproachingLimit(),
                'is_exceeded' => $quota->isExceeded(),
            ];
        }

        return $summary;
    }

    public function getAlerts(string $tenantId): array
    {
        $alerts = [];

        foreach (Subscription::QUOTA_TYPES as $quotaType) {
            $quota = $this->getOrCreateQuota($tenantId, $quotaType);

            if ($quota->isExceeded()) {
                $alerts[] = [
                    'type' => 'error',
                    'quota_type' => $quotaType,
                    'message' => "Kuota {$quotaType} telah habis. Silakan upgrade paket Anda.",
                    'percentage' => 100,
                ];
            } elseif ($quota->isApproachingLimit()) {
                $alerts[] = [
                    'type' => 'warning',
                    'quota_type' => $quotaType,
                    'message' => "Kuota {$quotaType} hampir habis ({$quota->usagePercentage()}%).",
                    'percentage' => $quota->usagePercentage(),
                ];
            }
        }

        return $alerts;
    }

    public function clearQuotaCache(string $tenantId, string $quotaType): void
    {
        Cache::forget("quota:{$tenantId}:{$quotaType}");
    }

    public function syncQuotaWithSubscription(string $tenantId): void
    {
        $this->recalculateAllQuotas($tenantId);

        Log::info("Quotas synced for tenant: {$tenantId}");
    }
}

<?php
/**
 * POS Modular - Module Access Service
 * Created to support "Package-based" menu layout for SaaS architecture.
 */
namespace App\Services;

use App\Models\Tenant;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class ModuleService
{
    /**
     * Cache for requested modules in current request life cycle.
     */
    protected array $accessCache = [];

    /**
     * Check if current tenant has access to specific module.
     * 
     * @param string $module
     * @return bool
     */
    public function hasAccess(string $module): bool
    {
        // Super Admins see everything
        if (Auth::check() && Auth::user()->hasRole('super_admin')) {
            return true;
        }

        $tenantId = Auth::user()?->tenant_id;
        if (!$tenantId) {
            return false;
        }

        if (isset($this->accessCache[$module])) {
            return $this->accessCache[$module];
        }

        // Cache results for 1 hour per tenant
        $features = Cache::remember("tenant_{$tenantId}_features", 3600, function () use ($tenantId) {
            $tenant = Tenant::with(['activeSubscription.plan'])->find($tenantId);
            
            if (!$tenant || !$tenant->activeSubscription) {
                // Default features if no sub (should not happen if middleware is working)
                return ['pos', 'inventory', 'master_data'];
            }

            return $tenant->activeSubscription->plan->features['modules'] ?? [];
        });

        $this->accessCache[$module] = in_array($module, $features);

        return $this->accessCache[$module];
    }

    /**
     * Invalidate feature cache for a tenant.
     */
    public function clearCache(string $tenantId): void
    {
        Cache::forget("tenant_{$tenantId}_features");
        $this->accessCache = [];
    }
}

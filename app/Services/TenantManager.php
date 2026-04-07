<?php

namespace App\Services;

use App\Models\Tenant;
use App\Models\TenantDomain;
use Illuminate\Support\Facades\Cache;

class TenantManager
{
    protected ?Tenant $tenant = null;

    public function setTenant(?Tenant $tenant)
    {
        $this->tenant = $tenant;
    }

    public function getTenant(): ?Tenant
    {
        if (!$this->tenant && auth()->check()) {
            return auth()->user()->tenant;
        }

        return $this->tenant;
    }

    public function getTenantId(): ?string
    {
        return $this->getTenant()?->id;
    }

    public function resolveByDomain(string $host): ?Tenant
    {
        $host = strtolower($host);

        // Try to get from cache to avoid DB hits on every request
        return Cache::remember('tenant_domain_' . $host, 3600, function () use ($host) {
            $tenantDomain = TenantDomain::query()
                ->where('domain', $host)
                ->active()
                ->verified()
                ->with('tenant')
                ->first();

            return $tenantDomain?->tenant;
        });
    }
}

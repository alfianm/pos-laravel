<?php

namespace App\Http\Middleware;

use App\Services\TenantManager;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ResolveTenantByDomain
{
    public function __construct(protected TenantManager $tenantManager)
    {
    }

    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $host = $request->getHost();

        // Resolve tenant by domain
        $tenant = $this->tenantManager->resolveByDomain($host);

        if ($tenant) {
            $this->tenantManager->setTenant($tenant);
            
            // Optionally, bind to the container for easy dependency injection
            app()->instance(\App\Models\Tenant::class, $tenant);
            
            // Share with all views
            view()->share('currentTenant', $tenant);
        }

        return $next($request);
    }
}

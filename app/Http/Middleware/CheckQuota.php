<?php

namespace App\Http\Middleware;

use App\Exceptions\QuotaExceededException;
use App\Services\QuotaService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckQuota
{
    protected QuotaService $quotaService;

    public function __construct(QuotaService $quotaService)
    {
        $this->quotaService = $quotaService;
    }

    public function handle(Request $request, Closure $next, string $quotaType): Response
    {
        $user = auth()->user();

        if (! $user) {
            return $next($request);
        }

        $tenantId = $user->tenant_id;

        if (! $tenantId) {
            return $next($request);
        }

        try {
            $this->quotaService->enforceQuota($tenantId, $quotaType, 1);
        } catch (QuotaExceededException $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Quota Exceeded',
                    'message' => $e->getMessage(),
                    'quota_type' => $quotaType,
                ], 403);
            }

            return redirect()->back()->with('error', $e->getMessage());
        }

        return $next($request);
    }
}

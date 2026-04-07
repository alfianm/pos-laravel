<?php

use App\Exceptions\QuotaExceededException;
use App\Http\Middleware\CheckQuota;
use App\Http\Middleware\ResolveTenantByDomain;
use App\Models\Tenant;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->append(ResolveTenantByDomain::class);
        $middleware->alias([
            'quota' => CheckQuota::class,
            'tenant.domain' => ResolveTenantByDomain::class,
        ]);
    })
    ->booted(function (Application $app): void {
        // Register tier-based rate limiter for tenant API
        // Using app()->make to avoid facade before boot issues
        $rateLimiter = $app->make('Illuminate\Cache\RateLimiter');

        $rateLimiter->for('tenant-api', function (Request $request) {
            // Get tenant from request (resolved by middleware)
            $tenant = $request->attributes->get('current_tenant');

            // Default limits
            $requestsPerMinute = 60;
            $plan = 'free';

            if ($tenant instanceof Tenant) {
                $plan = $tenant->plan ?? 'free';

                // Tier-based rate limits
                $limits = [
                    'free' => 60,        // 60 requests per minute
                    'basic' => 120,      // 120 requests per minute
                    'pro' => 300,        // 300 requests per minute
                    'enterprise' => 1000, // 1000 requests per minute
                ];

                $requestsPerMinute = $limits[$plan] ?? 60;
            }

            return Limit::perMinute($requestsPerMinute)
                ->by($tenant?->id ?? $request->ip())
                ->response(function (Request $request, array $headers) use ($plan, $requestsPerMinute) {
                    return response()->json([
                        'error' => 'Too Many Requests',
                        'message' => 'Rate limit exceeded for your ' . $plan . ' plan. Limit: ' . $requestsPerMinute . ' requests per minute.',
                        'plan' => $plan,
                        'limit' => $requestsPerMinute,
                    ], 429, $headers);
                });
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (ThrottleRequestsException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Too Many Requests',
                    'message' => 'Rate limit exceeded for your tenant. Please upgrade your plan or try again later.',
                ], 429);
            }
        });

        $exceptions->render(function (QuotaExceededException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Quota Exceeded',
                    'message' => $e->getMessage(),
                ], 403);
            }

            return redirect()->back()->with('error', $e->getMessage());
        });
    })->create();

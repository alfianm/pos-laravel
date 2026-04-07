<?php

namespace App\Livewire\Dashboard;

use App\Models\Branch;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Attributes\Layout;
use Livewire\Component;

class SummaryDashboard extends Component
{
    public int $tenantsCount = 0;

    public int $branchesCount = 0;

    public int $usersCount = 0;

    public int $accessibleBranchesCount = 0;

    public string $todaySales = '0';

    public int $todayTransactions = 0;

    public string $avgSale = '0';

    public int $rateLimitUsed = 0;

    public int $rateLimitRemaining = 0;

    public int $rateLimitMax = 60;

    public string $currentTenantName = '';

    public string $primaryRole = '';

    public string $currentTimezone = 'UTC';

    public array $accessRows = [];

    public array $quotaSummary = [];

    public $recentBranches;

    private bool $isSuperAdmin = false;

    public function mount(): void
    {
        /** @var User $user */
        $user = auth()->user();
        $tenantId = $user->tenant_id;
        $this->isSuperAdmin = $user->hasRole('super_admin');

        $this->currentTenantName = $user->tenant?->name ?? 'System';
        $this->primaryRole = $user->roles->first()?->name ?? 'User';
        $this->currentTimezone = $user->timezone ?? config('app.timezone', 'UTC');

        // Cache all stats together for consistency
        $cacheKey = $this->isSuperAdmin ? 'dashboard_stats_global' : "dashboard_stats_{$tenantId}";
        $stats = Cache::remember($cacheKey, 300, function () use ($user, $tenantId) {
            $tenantQuery = $this->isSuperAdmin
                ? Tenant::query()
                : Tenant::query()->where('id', $tenantId);

            $branchQuery = $this->isSuperAdmin
                ? Branch::query()
                : Branch::query()->where('tenant_id', $tenantId);

            $userQuery = $this->isSuperAdmin
                ? User::query()
                : User::query()->where('tenant_id', $tenantId);

            $salesQuery = $this->isSuperAdmin
                ? \App\Models\Sale::query()
                : \App\Models\Sale::query()->where('tenant_id', $tenantId);

            $salesQuery = $salesQuery->whereDate('created_at', today());

            $salesStats = $salesQuery->select(
                DB::raw('SUM(grand_total) as total_sales'),
                DB::raw('COUNT(*) as total_transactions')
            )->first();

            return [
                'tenantsCount' => $this->isSuperAdmin ? Tenant::count() : ($tenantId ? 1 : 0),
                'branchesCount' => $branchQuery->count(),
                'usersCount' => $userQuery->count(),
                'accessibleBranchesCount' => $this->isSuperAdmin ? Branch::count() : $user->branches()->count(),
                'todaySales' => (string) ($salesStats->total_sales ?? 0),
                'todayTransactions' => (int) ($salesStats->total_transactions ?? 0),
            ];
        });

        $this->tenantsCount = $stats['tenantsCount'];
        $this->branchesCount = $stats['branchesCount'];
        $this->usersCount = $stats['usersCount'];
        $this->accessibleBranchesCount = $stats['accessibleBranchesCount'];
        $this->todaySales = $stats['todaySales'];
        $this->todayTransactions = $stats['todayTransactions'];
        $this->avgSale = $this->todayTransactions > 0
            ? (string) round((float) $this->todaySales / $this->todayTransactions, 2)
            : '0';

        // Optimasi: Eager loading relasi dan limitasi kolom
        $branchQuery = $this->isSuperAdmin
            ? Branch::query()
            : Branch::query()->where('tenant_id', $tenantId);

        $this->recentBranches = $branchQuery
            ->with('tenant:id,name')
            ->latest()
            ->take(4)
            ->get(['id', 'tenant_id', 'name', 'code', 'status', 'created_at']);

        if (!$this->isSuperAdmin) {
            $limiterKey = 'tenant-api:' . $tenantId;
            $this->rateLimitUsed = RateLimiter::attempts($limiterKey);
            $this->rateLimitRemaining = RateLimiter::remaining($limiterKey, $this->rateLimitMax);

            $this->quotaSummary = app(\App\Services\QuotaService::class)->getQuotaSummary($tenantId);
        }

        $this->accessRows = [
            [
                'label' => 'Workspace',
                'value' => $this->currentTenantName,
                'description' => $this->isSuperAdmin ? 'Global system view.' : 'Current tenant context.',
            ],
            [
                'label' => 'Primary Role',
                'value' => $this->primaryRole,
                'description' => 'Your main active permission set.',
            ],
            [
                'label' => 'Branch Access',
                'value' => $this->accessibleBranchesCount . ' branches',
                'description' => 'Branches linked to your account.',
            ],
            [
                'label' => 'Timezone',
                'value' => str_replace('_', ' ', $this->currentTimezone),
                'description' => 'Current local time: ' . now()->setTimezone($this->currentTimezone)->format('H:i') . '.',
            ],
        ];
    }

    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.dashboard.summary-dashboard');
    }
}

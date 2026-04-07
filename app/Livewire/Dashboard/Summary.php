<?php

namespace App\Livewire\Dashboard;

use App\Models\Branch;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Component;

class Summary extends Component
{
    public int $tenantsCount = 0;

    public int $branchesCount = 0;

    public int $usersCount = 0;

    public int $accessibleBranchesCount = 0;

    public int $mainWarehousesCount = 0;

    public string $currentTenantName = '';

    public string $currentTimezone = 'Asia/Jakarta';

    public string $firstName = '';

    public string $primaryRole = 'Staff';

    public bool $isSuperAdmin = false;

    public array $roleNames = [];

    public array $quickLinks = [];

    public array $accessRows = [];

    public $recentBranches;

    public $recentUsers;

    public function mount(): void
    {
        $user = Auth::user();

        $this->isSuperAdmin = $user->hasRole('super_admin');
        $this->firstName = (string) Str::of($user->name)->before(' ');
        $this->roleNames = $user->getRoleNames()
            ->map(fn (string $role): string => Str::headline($role))
            ->values()
            ->all();
        $this->primaryRole = $this->roleNames[0] ?? 'Staff';
        $this->currentTimezone = $user->tenant?->timezone ?? config('app.timezone', 'Asia/Jakarta');
        $this->currentTenantName = $this->isSuperAdmin
            ? 'Multi-tenant control'
            : ($user->tenant?->name ?? 'Tenant belum ditetapkan');

        $branchQuery = $this->isSuperAdmin
            ? Branch::query()
            : Branch::query()->where('tenant_id', $user->tenant_id);

        $userQuery = $this->isSuperAdmin
            ? User::query()
            : User::query()->where('tenant_id', $user->tenant_id);

        $this->tenantsCount = $this->isSuperAdmin ? Tenant::count() : ($user->tenant_id ? 1 : 0);
        $this->branchesCount = (clone $branchQuery)->count();
        $this->usersCount = (clone $userQuery)->count();
        $this->accessibleBranchesCount = $this->isSuperAdmin ? $this->branchesCount : $user->branches()->count();
        $this->mainWarehousesCount = (clone $branchQuery)->where('is_main_warehouse', true)->count();

        $this->recentBranches = (clone $branchQuery)
            ->latest()
            ->take(4)
            ->get([
                'id',
                'tenant_id',
                'name',
                'code',
                'address',
                'status',
                'is_main_warehouse',
                'created_at',
            ]);

        $this->recentUsers = (clone $userQuery)
            ->with('tenant:id,name')
            ->latest()
            ->take(4)
            ->get([
                'id',
                'tenant_id',
                'name',
                'email',
                'created_at',
            ]);

        $this->quickLinks = array_values(array_filter([
            [
                'visible' => $this->isSuperAdmin,
                'label' => 'Tenant Desk',
                'description' => 'Kelola identitas bisnis, timezone, dan status tenant pusat.',
                'route' => route('tenants.index'),
                'badge' => $this->tenantsCount.' tenant',
            ],
            [
                'visible' => $user->can('view_branches'),
                'label' => 'Branch Atlas',
                'description' => 'Pantau outlet dan gudang utama yang sudah aktif di sistem.',
                'route' => route('branches.index'),
                'badge' => $this->branchesCount.' cabang',
            ],
            [
                'visible' => $user->can('view users'),
                'label' => 'Users',
                'description' => 'Lihat staf, role, dan distribusi akses yang sedang berjalan.',
                'route' => route('users.index'),
                'badge' => $this->usersCount.' user',
            ],
            [
                'visible' => true,
                'label' => 'Profil Saya',
                'description' => 'Perbarui identitas akun, email, dan pengamanan sesi Anda.',
                'route' => route('profile'),
                'badge' => $this->primaryRole,
            ],
        ], fn (array $link): bool => $link['visible']));

        $this->accessRows = [
            [
                'label' => 'Workspace',
                'value' => $this->currentTenantName,
                'description' => $this->isSuperAdmin ? 'Ringkasan mencakup seluruh tenant yang hidup di sistem.' : 'Briefing difokuskan pada tenant aktif pengguna saat ini.',
            ],
            [
                'label' => 'Peran utama',
                'value' => $this->primaryRole,
                'description' => count($this->roleNames) > 1 ? count($this->roleNames).' peran terpasang pada akun ini.' : 'Akses aktif mengikuti satu peran utama.',
            ],
            [
                'label' => 'Cakupan cabang',
                'value' => $this->accessibleBranchesCount.' cabang',
                'description' => $this->isSuperAdmin ? 'Akses global ke seluruh cabang aktif.' : 'Cabang yang saat ini terkait ke akun ini.',
            ],
            [
                'label' => 'Timezone',
                'value' => str_replace('_', ' ', $this->currentTimezone),
                'description' => 'Waktu lokal saat ini '.now()->setTimezone($this->currentTimezone)->format('H:i').'.',
            ],
        ];
    }

    public function render()
    {
        return view('livewire.dashboard.summary');
    }
}

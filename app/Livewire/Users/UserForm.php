<?php

namespace App\Livewire\Users;

use App\Constants\Subscription;
use App\Exceptions\QuotaExceededException;
use App\Models\Branch;
use App\Models\Role;
use App\Models\Tenant;
use App\Models\User;
use App\Services\AuditLogService;
use App\Services\QuotaService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Spatie\Permission\PermissionRegistrar;

#[Layout('layouts.app')]
class UserForm extends Component
{
    public ?User $user = null;

    public string $tenant_id = '';

    public string $name = '';

    public string $email = '';

    public string $password = '';

    public array $selected_roles = [];

    public array $selected_branches = [];

    public function mount(?User $user = null)
    {
        if ($user && $user->exists) {
            $this->user = $user;
            $this->tenant_id = $user->tenant_id ?? '';
            $this->name = $user->name;
            $this->email = $user->email;
            $this->selected_roles = $user->roles->pluck('name')->toArray();
            $this->selected_branches = $user->branches->pluck('id')->toArray();
        } else {
            if (! Auth::user()->hasRole('super_admin')) {
                $this->tenant_id = Auth::user()->tenant_id;
            }
        }
    }

    public function rules()
    {
        return [
            'tenant_id' => 'nullable|uuid|exists:tenants,id',
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($this->user?->id),
            ],
            'password' => $this->user ? 'nullable|min:8' : 'required|min:8',
            'selected_roles' => 'required|array|min:1',
            'selected_branches' => 'nullable|array',
        ];
    }

    public function save()
    {
        if (! Auth::user()->hasRole('super_admin')) {
            $this->tenant_id = Auth::user()->tenant_id;
        }

        $this->validate();

        if (! $this->user || ! $this->user->exists) {
            try {
                $quotaService = app(QuotaService::class);
                $quotaService->enforceQuota($this->tenant_id, Subscription::QUOTA_USERS, 1);
            } catch (QuotaExceededException $e) {
                session()->flash('error', $e->getMessage());

                return;
            }
        }

        $data = [
            'tenant_id' => $this->tenant_id ?: null,
            'name' => $this->name,
            'email' => $this->email,
        ];

        if ($this->password) {
            $data['password'] = Hash::make($this->password);
        }

        if ($this->user && $this->user->exists) {
            $oldValues = $this->user->load(['roles', 'branches'])->toArray();
            $this->user->update($data);

            if (config('permission.teams')) {
                app(PermissionRegistrar::class)->setPermissionsTeamId($this->tenant_id ?: null);
            }
            $this->user->syncRoles($this->selected_roles);
            $this->user->branches()->sync($this->selected_branches);

            AuditLogService::log('update', $this->user, $oldValues, $this->user->fresh(['roles', 'branches'])->toArray(), 'user-mgmt');
        } else {
            $this->user = User::create($data);

            if (config('permission.teams')) {
                app(PermissionRegistrar::class)->setPermissionsTeamId($this->tenant_id ?: null);
            }
            $this->user->assignRole($this->selected_roles);
            $this->user->branches()->sync($this->selected_branches);

            AuditLogService::log('create', $this->user, null, $this->user->load(['roles', 'branches'])->toArray(), 'user-mgmt');
        }

        session()->flash('message', 'User berhasil disimpan.');

        return $this->redirect(route('users.index'), navigate: true);
    }

    public function render()
    {
        $branchesQuery = Branch::query();
        if ($this->tenant_id) {
            $branchesQuery->where('tenant_id', $this->tenant_id);
        }

        return view('livewire.users.user-form', [
            'tenants' => Auth::user()->hasRole('super_admin') ? Tenant::all() : [],
            'roles' => Role::all(),
            'branches' => $branchesQuery->get(),
        ]);
    }
}

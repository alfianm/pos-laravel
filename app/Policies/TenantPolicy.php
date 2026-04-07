<?php

namespace App\Policies;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TenantPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasRole('super_admin');
    }

    public function view(User $user, Tenant $tenant): bool
    {
        return $user->hasRole('super_admin') || $user->tenant_id === $tenant->id;
    }

    public function create(User $user): bool
    {
        return $user->hasRole('super_admin');
    }

    public function update(User $user, Tenant $tenant): bool
    {
        return $user->hasRole('super_admin') || $user->tenant_id === $tenant->id;
    }

    public function delete(User $user, Tenant $tenant): bool
    {
        return $user->hasRole('super_admin');
    }
}

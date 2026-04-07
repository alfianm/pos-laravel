<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Spatie\Permission\Models\Role;

class RolePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view roles');
    }

    public function view(User $user, Role $role): bool
    {
        return $user->can('view roles');
    }

    public function create(User $user): bool
    {
        return $user->can('manage roles');
    }

    public function update(User $user, Role $role): bool
    {
        return $user->can('manage roles');
    }

    public function delete(User $user, Role $role): bool
    {
        if (in_array($role->name, ['super_admin'])) {
            return false;
        }

        return $user->can('manage roles');
    }

    public function assignPermissions(User $user, Role $role): bool
    {
        return $user->can('manage roles');
    }
}

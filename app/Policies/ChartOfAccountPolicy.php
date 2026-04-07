<?php

namespace App\Policies;

use App\Models\ChartOfAccount;
use App\Models\User;

class ChartOfAccountPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view chart of accounts');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ChartOfAccount $chartOfAccount): bool
    {
        return $user->hasPermissionTo('view chart of accounts') &&
            $user->tenant_id === $chartOfAccount->tenant_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create chart of accounts');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ChartOfAccount $chartOfAccount): bool
    {
        return $user->hasPermissionTo('edit chart of accounts') &&
            $user->tenant_id === $chartOfAccount->tenant_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ChartOfAccount $chartOfAccount): bool
    {
        return $user->hasPermissionTo('delete chart of accounts') &&
            $user->tenant_id === $chartOfAccount->tenant_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, ChartOfAccount $chartOfAccount): bool
    {
        return $user->hasPermissionTo('edit chart of accounts') &&
            $user->tenant_id === $chartOfAccount->tenant_id;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, ChartOfAccount $chartOfAccount): bool
    {
        return $user->hasRole('super_admin');
    }
}

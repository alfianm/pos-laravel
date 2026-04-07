<?php

namespace App\Policies;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class BranchPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view branches');
    }

    public function view(User $user, Branch $branch): bool
    {
        return $user->can('view branches');
    }

    public function create(User $user): bool
    {
        return $user->can('manage branches');
    }

    public function update(User $user, Branch $branch): bool
    {
        return $user->can('manage branches');
    }

    public function delete(User $user, Branch $branch): bool
    {
        return $user->hasRole('super_admin');
    }
}

<?php

namespace App\Policies;

use App\Models\Proposal;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProposalPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view proposals');
    }

    public function view(User $user, Proposal $proposal): bool
    {
        return $user->can('view proposals');
    }

    public function create(User $user): bool
    {
        return $user->can('create proposals');
    }

    public function update(User $user, Proposal $proposal): bool
    {
        return $user->can('create proposals');
    }

    public function delete(User $user, Proposal $proposal): bool
    {
        return $user->can('create proposals');
    }
}

<?php

namespace App\Policies;

use App\Models\MarketplaceAccount;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class MarketplaceAccountPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view marketplace');
    }

    public function view(User $user, MarketplaceAccount $account): bool
    {
        return $user->can('view marketplace');
    }

    public function create(User $user): bool
    {
        return $user->can('manage marketplace accounts');
    }

    public function update(User $user, MarketplaceAccount $account): bool
    {
        return $user->can('manage marketplace accounts');
    }

    public function delete(User $user, MarketplaceAccount $account): bool
    {
        return $user->can('manage marketplace accounts');
    }
}

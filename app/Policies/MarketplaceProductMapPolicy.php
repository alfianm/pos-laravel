<?php

namespace App\Policies;

use App\Models\MarketplaceProductMap;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class MarketplaceProductMapPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view marketplace');
    }

    public function view(User $user, MarketplaceProductMap $map): bool
    {
        return $user->can('view marketplace');
    }

    public function create(User $user): bool
    {
        return $user->can('manage product mapping');
    }

    public function update(User $user, MarketplaceProductMap $map): bool
    {
        return $user->can('manage product mapping');
    }

    public function delete(User $user, MarketplaceProductMap $map): bool
    {
        return $user->can('manage product mapping');
    }
}

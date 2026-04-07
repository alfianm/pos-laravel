<?php

namespace App\Policies;

use App\Models\Inventory;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class InventoryPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view inventory');
    }

    public function view(User $user, Inventory $inventory): bool
    {
        return $user->can('view inventory');
    }

    public function create(User $user): bool
    {
        return $user->can('opening stock');
    }

    public function update(User $user, Inventory $inventory): bool
    {
        return $user->can('manage stocks');
    }

    public function adjust(User $user, Inventory $inventory): bool
    {
        return $user->can('stock adjustment');
    }

    public function transfer(User $user, Inventory $inventory): bool
    {
        return $user->can('manage transfers');
    }
}

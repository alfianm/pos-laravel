<?php

namespace App\Policies;

use App\Models\Sale;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SalePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view sales') || $user->can('access pos');
    }

    public function view(User $user, Sale $sale): bool
    {
        return $user->can('view sales') || $user->can('access pos');
    }

    public function create(User $user): bool
    {
        return $user->can('access pos');
    }

    public function void(User $user, Sale $sale): bool
    {
        return $user->can('void sales');
    }

    public function refund(User $user, Sale $sale): bool
    {
        return $user->can('refund sales');
    }
}

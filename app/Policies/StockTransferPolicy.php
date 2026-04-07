<?php

namespace App\Policies;

use App\Models\StockTransfer;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class StockTransferPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view inventory');
    }

    public function view(User $user, StockTransfer $transfer): bool
    {
        return $user->can('view inventory');
    }

    public function create(User $user): bool
    {
        return $user->can('manage transfers');
    }

    public function update(User $user, StockTransfer $transfer): bool
    {
        return $user->can('manage transfers');
    }

    public function approve(User $user, StockTransfer $transfer): bool
    {
        return $user->can('manage transfers');
    }

    public function send(User $user, StockTransfer $transfer): bool
    {
        return $user->can('manage transfers');
    }

    public function receive(User $user, StockTransfer $transfer): bool
    {
        return $user->can('manage transfers');
    }
}

<?php

namespace App\Policies;

use App\Models\StockAdjustment;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class StockAdjustmentPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view inventory');
    }

    public function view(User $user, StockAdjustment $stockAdjustment): bool
    {
        return $user->can('view inventory');
    }

    public function create(User $user): bool
    {
        return $user->can('stock adjustment');
    }

    public function update(User $user, StockAdjustment $stockAdjustment): bool
    {
        return $user->can('stock adjustment');
    }

    public function delete(User $user, StockAdjustment $stockAdjustment): bool
    {
        return $user->can('stock adjustment') && $stockAdjustment->status === 'draft';
    }

    public function finalize(User $user, StockAdjustment $stockAdjustment): bool
    {
        return $user->can('stock adjustment') && $stockAdjustment->status === 'draft';
    }
}

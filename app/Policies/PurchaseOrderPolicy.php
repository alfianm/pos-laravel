<?php

namespace App\Policies;

use App\Models\PurchaseOrder;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PurchaseOrderPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view purchases');
    }

    public function view(User $user, PurchaseOrder $purchaseOrder): bool
    {
        return $user->can('view purchases');
    }

    public function create(User $user): bool
    {
        return $user->can('create purchases');
    }

    public function update(User $user, PurchaseOrder $purchaseOrder): bool
    {
        return $user->can('create purchases') && $purchaseOrder->status === 'draft';
    }

    public function approve(User $user, PurchaseOrder $purchaseOrder): bool
    {
        return $user->can('approve purchases');
    }

    public function receive(User $user, PurchaseOrder $purchaseOrder): bool
    {
        return $user->can('receive purchases');
    }

    public function delete(User $user, PurchaseOrder $purchaseOrder): bool
    {
        return $user->can('create purchases') && $purchaseOrder->status === 'draft';
    }
}

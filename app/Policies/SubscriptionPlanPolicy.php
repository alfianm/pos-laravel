<?php

namespace App\Policies;

use App\Models\SubscriptionPlan;
use App\Models\User;

class SubscriptionPlanPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole('super_admin') || $user->hasPermissionTo('view subscription plans');
    }

    public function view(User $user, SubscriptionPlan $subscriptionPlan): bool
    {
        return $user->hasRole('super_admin') || $user->hasPermissionTo('view subscription plans');
    }

    public function create(User $user): bool
    {
        return $user->hasRole('super_admin') || $user->hasPermissionTo('manage subscription plans');
    }

    public function update(User $user, SubscriptionPlan $subscriptionPlan): bool
    {
        return $user->hasRole('super_admin') || $user->hasPermissionTo('manage subscription plans');
    }

    public function delete(User $user, SubscriptionPlan $subscriptionPlan): bool
    {
        return $user->hasRole('super_admin') || $user->hasPermissionTo('manage subscription plans');
    }

    public function restore(User $user, SubscriptionPlan $subscriptionPlan): bool
    {
        return $user->hasRole('super_admin') || $user->hasPermissionTo('manage subscription plans');
    }

    public function forceDelete(User $user, SubscriptionPlan $subscriptionPlan): bool
    {
        return $user->hasRole('super_admin');
    }
}

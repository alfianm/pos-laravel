<?php

namespace App\Policies;

use App\Models\Lead;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class LeadPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view leads');
    }

    public function view(User $user, Lead $lead): bool
    {
        return $user->can('view leads');
    }

    public function create(User $user): bool
    {
        return $user->can('manage leads');
    }

    public function update(User $user, Lead $lead): bool
    {
        return $user->can('manage leads');
    }

    public function delete(User $user, Lead $lead): bool
    {
        return $user->can('manage leads');
    }

    public function convert(User $user, Lead $lead): bool
    {
        return $user->can('convert leads');
    }
}

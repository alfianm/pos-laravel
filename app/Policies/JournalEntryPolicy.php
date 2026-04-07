<?php

namespace App\Policies;

use App\Models\JournalEntry;
use App\Models\User;

class JournalEntryPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view journal entries');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, JournalEntry $journalEntry): bool
    {
        return $user->hasPermissionTo('view journal entries') &&
            $user->tenant_id === $journalEntry->tenant_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create journal entries');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, JournalEntry $journalEntry): bool
    {
        return $user->hasPermissionTo('edit journal entries') &&
            $user->tenant_id === $journalEntry->tenant_id &&
            $journalEntry->status === 'draft';
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, JournalEntry $journalEntry): bool
    {
        return $user->hasPermissionTo('delete journal entries') &&
            $user->tenant_id === $journalEntry->tenant_id &&
            $journalEntry->status === 'draft';
    }

    /**
     * Determine whether the user can post the journal entry.
     */
    public function post(User $user, JournalEntry $journalEntry): bool
    {
        return $user->hasPermissionTo('post journal entries') &&
            $user->tenant_id === $journalEntry->tenant_id &&
            $journalEntry->status === 'draft';
    }

    /**
     * Determine whether the user can unpost the journal entry.
     */
    public function unpost(User $user, JournalEntry $journalEntry): bool
    {
        return $user->hasPermissionTo('post journal entries') &&
            $user->tenant_id === $journalEntry->tenant_id &&
            $journalEntry->status === 'posted';
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, JournalEntry $journalEntry): bool
    {
        return $user->hasPermissionTo('edit journal entries') &&
            $user->tenant_id === $journalEntry->tenant_id;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, JournalEntry $journalEntry): bool
    {
        return $user->hasRole('super_admin');
    }
}

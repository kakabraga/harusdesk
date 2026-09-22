<?php

namespace App\Policies\Ticket;

use App\Models\Ticket;
use App\Models\User;

class TicketPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->active;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Ticket $ticket): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        if ($user->enterprise_id !== $ticket->enterprise_id) {
            return false;
        }

        if ($user->isAdmin()) {
            return true;
        }

        if ($ticket->requester_id === $user->id) {
            return true;
        }

        return $user->sectors()->where('sectors.id', $ticket->sector_id)->exists();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Ticket $ticket): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        if ($user->enterprise_id !== $ticket->enterprise_id) {
            return false;
        }

        if ($ticket->requester_id === $user->id) {
            return true;
        }

        return $ticket->requester_id === $user->id || $user->isAdmin();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Ticket $ticket): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        if ($user->enterprise_id !== $ticket->enterprise_id) {
            return false;
        }

        return $user->isAdmin();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Ticket $ticket): bool
    {
        return $user->enterprise_id === $ticket->enterprise_id || $user->isSuperAdmin();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Ticket $ticket): bool
    {
        return $user->enterprise_id === $ticket->enterprise_id || $user->isSuperAdmin();
    }
}

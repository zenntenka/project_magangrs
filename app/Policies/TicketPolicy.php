<?php

namespace App\Policies;

use App\Models\Ticket;
use App\Models\User;

class TicketPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     * Pelapor hanya diizinkan melihat tiket miliknya sendiri (mencegah akses tiket orang lain / 403).
     * Admin dan Teknisi diizinkan melihat seluruh tiket untuk koordinasi helpdesk IT.
     */
    public function view(User $user, Ticket $ticket): bool
    {
        if ($user->role === 'admin' || $user->role === 'teknisi') {
            return true;
        }

        return (int) $user->id === (int) $ticket->user_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Ticket $ticket): bool
    {
        if ($user->role === 'admin') {
            return true;
        }

        if ($user->role === 'teknisi') {
            return (int) $ticket->teknisi_id === (int) $user->id;
        }

        return (int) $user->id === (int) $ticket->user_id && $ticket->status === 'baru';
    }

    /**
     * Determine whether the technician can take the ticket.
     */
    public function take(User $user, Ticket $ticket): bool
    {
        return $user->role === 'teknisi' && $ticket->status === 'baru' && $ticket->teknisi_id === null;
    }

    /**
     * Determine whether the user can comment on the ticket.
     */
    public function comment(User $user, Ticket $ticket): bool
    {
        if ($user->role === 'admin') {
            return true;
        }

        if ($user->role === 'teknisi') {
            return $ticket->teknisi_id === null || (int) $ticket->teknisi_id === (int) $user->id;
        }

        return (int) $user->id === (int) $ticket->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Ticket $ticket): bool
    {
        return $user->role === 'admin';
    }
}

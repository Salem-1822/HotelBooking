<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Reservation;
use Illuminate\Auth\Access\HandlesAuthorization;

class ReservationPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the reservation.
     */
    public function view(User $user, Reservation $reservation): bool
    {
        if ($user->id === $reservation->user_id) {
            return true;
        }

        // Allow access if reservation has no user_id but customer email matches the authenticated user
        if (is_null($reservation->user_id) && $reservation->customer && !empty($reservation->customer->email)) {
            if (strtolower(trim($user->email)) === strtolower(trim($reservation->customer->email))) {
                $reservation->forceFill(['user_id' => $user->id])->saveQuietly();
                return true;
            }
        }

        return false;
    }

    /**
     * Determine whether the user can cancel the reservation.
     */
    public function cancel(User $user, Reservation $reservation): bool
    {
        return $this->view($user, $reservation);
    }
}

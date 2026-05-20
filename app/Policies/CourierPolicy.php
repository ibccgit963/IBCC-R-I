<?php

namespace App\Policies;

use App\Models\User;

use App\Models\Courier;

use Illuminate\Auth\Access\Response;

class CourierPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->role->slug, ['super-admin', 'center-admin', 'staff-user']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Courier $courier): bool
    {
        if ($user->role->slug === 'super-admin') {
            return true;
        }

        if (in_array($user->role->slug, ['center-admin', 'staff-user'])) {
            return $user->center_id === $courier->center_id;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return in_array($user->role->slug, ['super-admin', 'center-admin', 'staff-user']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Courier $courier): bool
    {
        if ($user->role->slug === 'super-admin') {
            return true;
        }

        if (in_array($user->role->slug, ['center-admin', 'staff-user'])) {
            return $user->center_id === $courier->center_id;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Courier $courier): bool
    {
        if ($user->role->slug === 'super-admin') {
            return true;
        }

        if (in_array($user->role->slug, ['center-admin', 'staff-user'])) {
            return $user->center_id === $courier->center_id;
        }

        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Courier $courier): bool
    {
        if ($user->role->slug === 'super-admin') {
            return true;
        }

        if (in_array($user->role->slug, ['center-admin', 'staff-user'])) {
            return $user->center_id === $courier->center_id;
        }

        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Courier $courier): bool
    {
        if ($user->role->slug === 'super-admin') {
            return true;
        }

        if (in_array($user->role->slug, ['center-admin', 'staff-user'])) {
            return $user->center_id === $courier->center_id;
        }

        return false;
    }
}

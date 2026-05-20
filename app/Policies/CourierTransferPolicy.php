<?php

namespace App\Policies;

use App\Models\User;

use App\Models\CourierTransfer;

use Illuminate\Auth\Access\Response;

class CourierTransferPolicy
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
    public function view(User $user, CourierTransfer $courierTransfer): bool
    {
        if ($user->role->slug === 'super-admin') {
            return true;
        }

        if (in_array($user->role->slug, ['center-admin', 'staff-user'])) {
            return $user->center_id === $courierTransfer->courier->center_id;
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
    public function update(User $user, CourierTransfer $courierTransfer): bool
    {
        if ($user->role->slug === 'super-admin') {
            return true;
        }

        if (in_array($user->role->slug, ['center-admin', 'staff-user'])) {
            return $user->center_id === $courierTransfer->courier->center_id;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, CourierTransfer $courierTransfer): bool
    {
        if ($user->role->slug === 'super-admin') {
            return true;
        }

        if (in_array($user->role->slug, ['center-admin', 'staff-user'])) {
            return $user->center_id === $courierTransfer->courier->center_id;
        }

        return false;
    }

    /**
     * Determine whether the user can mark as received.
     */
    public function markReceived(User $user, CourierTransfer $courierTransfer): bool
    {
        if ($user->role->slug === 'super-admin') {
            return true;
        }

        if ($user->center_id !== $courierTransfer->courier->center_id) {
            return false;
        }

        // If transferred to an officer, the user must be that officer
        if ($courierTransfer->transferable_type === 'App\\Models\\Officer' && $courierTransfer->transferable_id === $user->id) {
            return true;
        }

        // If transferred to a department, the user must belong to that department
        if ($courierTransfer->transferable_type === 'App\\Models\\Department' && $user->department_id === $courierTransfer->transferable_id) {
            return true;
        }

        return false;
    }
}

<?php

namespace App\Policies;

use App\Models\Usulan;
use App\Models\User;

class UsulanPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->isActive();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Usulan $usulan): bool
    {
        if (! $user->isActive()) {
            return false;
        }

        if ($user->isSuperAdmin()) {
            return true;
        }

        return $usulan->user_id !== null && $usulan->user_id === $user->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isActive();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Usulan $usulan): bool
    {
        if (! $user->isActive()) {
            return false;
        }

        if ($user->isSuperAdmin()) {
            return true;
        }

        return $usulan->user_id !== null && $usulan->user_id === $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Usulan $usulan): bool
    {
        if (! $user->isActive()) {
            return false;
        }

        if ($user->isSuperAdmin()) {
            return true;
        }

        return $usulan->user_id !== null && $usulan->user_id === $user->id;
    }
}

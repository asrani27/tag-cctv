<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Usulan;

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
     * Determine whether the user can export usulans to Excel.
     */
    public function export(User $user): bool
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

        if ($user->isKelurahan()) {
            $kelurahanName = $user->getKelurahanName();
            if ($kelurahanName && strcasecmp((string) $usulan->kelurahan, $kelurahanName) === 0) {
                return true;
            }
        }

        return $usulan->user_id !== null && $usulan->user_id === $user->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        if ($user->isKelurahan() && $user->hasDefaultPassword()) {
            return false;
        }

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

        if ($user->isKelurahan() && $user->hasDefaultPassword()) {
            return false;
        }

        if ($user->isSuperAdmin()) {
            return true;
        }

        if ($user->isKelurahan()) {
            $kelurahanName = $user->getKelurahanName();
            if ($kelurahanName && strcasecmp((string) $usulan->kelurahan, $kelurahanName) === 0) {
                return true;
            }
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

        if ($user->isKelurahan() && $user->hasDefaultPassword()) {
            return false;
        }

        if ($user->isSuperAdmin()) {
            return true;
        }

        return $usulan->user_id !== null && $usulan->user_id === $user->id;
    }
}

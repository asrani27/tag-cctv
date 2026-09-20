<?php

namespace App\Policies;

use App\Models\SurveyLocation;
use App\Models\User;

class SurveyLocationPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->isActive();
    }

    /**
     * Determine whether the user can export surveys to Excel.
     * Only superadmins are allowed to export.
     */
    public function export(User $user): bool
    {
        return $user->isActive() && $user->isSuperAdmin();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, SurveyLocation $surveyLocation): bool
    {
        if (! $user->isActive()) {
            return false;
        }

        if ($user->isSuperAdmin()) {
            return true;
        }

        return $surveyLocation->user_id !== null && $surveyLocation->user_id === $user->id;
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
    public function update(User $user, SurveyLocation $surveyLocation): bool
    {
        if (! $user->isActive()) {
            return false;
        }

        if ($user->isSuperAdmin()) {
            return true;
        }

        return $surveyLocation->user_id !== null && $surveyLocation->user_id === $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, SurveyLocation $surveyLocation): bool
    {
        if (! $user->isActive()) {
            return false;
        }

        if ($user->isSuperAdmin()) {
            return true;
        }

        return $surveyLocation->user_id !== null && $surveyLocation->user_id === $user->id;
    }
}


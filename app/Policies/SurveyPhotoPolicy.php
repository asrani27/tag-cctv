<?php

namespace App\Policies;

use App\Models\SurveyPhoto;
use App\Models\User;

class SurveyPhotoPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->isActive();
    }

    /**
     * Determine whether the user can view the photo.
     */
    public function view(User $user, SurveyPhoto $photo): bool
    {
        if (! $user->isActive()) {
            return false;
        }

        if ($user->isSuperAdmin()) {
            return true;
        }

        $survey = $photo->surveyLocation;

        return $survey && $survey->user_id !== null && $survey->user_id === $user->id;
    }

    /**
     * Determine whether the user can create photos for a survey.
     */
    public function create(User $user): bool
    {
        return $user->isActive();
    }

    /**
     * Determine whether the user can delete the photo.
     */
    public function delete(User $user, SurveyPhoto $photo): bool
    {
        if (! $user->isActive()) {
            return false;
        }

        if ($user->isSuperAdmin()) {
            return true;
        }

        $survey = $photo->surveyLocation;

        return $survey && $survey->user_id !== null && $survey->user_id === $user->id;
    }
}

<?php

namespace App\Policies;

use App\Models\User;

class userPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, User $model): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update their own profile.
     */
    public function update(User $user, User $model): bool
    {
        // Users can only update their own profile
        return $user->id === $model->id;
    }

    /**
     * Determine whether the user can delete their own account.
     */
    public function delete(User $user, User $model): bool
    {
        // Users can only delete their own account
        return $user->id === $model->id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, User $model): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, User $model): bool
    {
        return false;
    }
}

<?php

namespace App\Policies;

use App\Repositories\User\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ReviewPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the review.
     *
     * @param  \App\Repositories\User\User $user
     * @return mixed
     */
    public function view(User $user)
    {
        if ($user->isInReviewerGroup()) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can update the review.
     *
     * @param  \App\Repositories\User\User $user
     * @return mixed
     */
    public function update(User $user)
    {
        if ($user->isInReviewerGroup()) {
            return true;
        }

        return false;
    }

}

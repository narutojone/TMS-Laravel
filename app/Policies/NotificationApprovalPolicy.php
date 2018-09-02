<?php

namespace App\Policies;

use App\Repositories\User\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class NotificationApprovalPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the given processed notification can be updated by the user.
     *
     * @param User $user
     * @return bool
     */
    public function update(User $user)
    {
        return $user->hasRole(User::ROLE_ADMIN);
    }

    /**
     * Determine if the given processed notification can be updated by the user.
     *
     * @param User $user
     * @return bool
     */
    public function view(User $user)
    {
        return $user->hasRole(User::ROLE_ADMIN);
    }
}

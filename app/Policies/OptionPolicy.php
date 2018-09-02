<?php

namespace App\Policies;

use App\Repositories\User\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class OptionPolicy
{
    use HandlesAuthorization;

    public function index(User $user)
    {
        return $user->hasRole(User::ROLE_ADMIN);
    }

    public function update(User $user)
    {
        return $user->hasRole(User::ROLE_ADMIN);
    }
}

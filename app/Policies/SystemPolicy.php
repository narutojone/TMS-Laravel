<?php

namespace App\Policies;

use App\Repositories\User\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SystemPolicy
{
    use HandlesAuthorization;

    public function index(User $user)
    {
        return $user->hasRole(User::ROLE_ADMIN) || $user->hasRole(User::ROLE_CUSTOMER_SERVICE);
    }

    public function create(User $user)
    {
        return $user->hasRole(User::ROLE_ADMIN) || $user->hasRole(User::ROLE_CUSTOMER_SERVICE);
    }

    public function store(User $user)
    {
        return $user->hasRole(User::ROLE_ADMIN) || $user->hasRole(User::ROLE_CUSTOMER_SERVICE);
    }

    public function show(User $user)
    {
        return $user->hasRole(User::ROLE_ADMIN) || $user->hasRole(User::ROLE_CUSTOMER_SERVICE);
    }

    public function edit(User $user)
    {
        return $user->hasRole(User::ROLE_ADMIN) || $user->hasRole(User::ROLE_CUSTOMER_SERVICE);
    }

    public function update(User $user)
    {
        return $user->hasRole(User::ROLE_ADMIN) || $user->hasRole(User::ROLE_CUSTOMER_SERVICE);
    }

    public function destroy(User $user)
    {
        return $user->hasRole(User::ROLE_ADMIN) || $user->hasRole(User::ROLE_CUSTOMER_SERVICE);
    }
}

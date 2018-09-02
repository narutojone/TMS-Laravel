<?php

namespace App\Policies;

use App\Repositories\Client\Client;
use App\Repositories\User\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ContactPolicy
{
    use HandlesAuthorization;

    public function index(User $user)
    {
        return $user->hasRole(User::ROLE_ADMIN) || $user->hasRole(User::ROLE_CUSTOMER_SERVICE);
    }

    public function create(User $user, Client $client)
    {
        if($client->internal == Client::IS_INTERNAL) {
            return false;
        }
        return $user->hasRole(User::ROLE_ADMIN);
    }

    public function view(User $user)
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
        return $user->hasRole(User::ROLE_ADMIN);
    }
}

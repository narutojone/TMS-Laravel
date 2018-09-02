<?php

namespace App\Policies;

use App\Repositories\Client\Client;
use App\Repositories\User\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ContractPolicy
{
    use HandlesAuthorization;

    public function create(User $user, Client $client)
    {
        if($client->contacts()->count() == 0) {
            return false;
        }

        if($client->internal == Client::IS_INTERNAL) {
            return false;
        }

        if($client->active == Client::NOT_ACTIVE) {
            return false;
        }

        return $user->hasRole(User::ROLE_ADMIN);
    }

    public function show(User $user)
    {
        return $user->hasRole(User::ROLE_ADMIN);
    }

    public function terminate(User $user)
    {
        return $user->hasRole(User::ROLE_ADMIN);
    }
}

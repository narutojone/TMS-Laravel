<?php

namespace App\Policies;

use App\Repositories\User\User;
use App\Repositories\Client\Client;
use Illuminate\Auth\Access\HandlesAuthorization;

class ClientPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the client.
     *
     * @param  \App\Repositories\User\User  $user
     * @param  \App\Repositories\Client\Client  $client
     * @return mixed
     */
    public function view(User $user, Client $client)
    {
        if($user->hasRole(User::ROLE_ADMIN)) {
            return true;
        }

        if($user->hasRole(User::ROLE_CUSTOMER_SERVICE)) {
            return true;
        }

        if($user->canAccessClient($client)) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can create clients.
     *
     * @param  \App\Repositories\User\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        if($user->hasRole(User::ROLE_ADMIN)) {
            return true;
        }

        if($user->hasRole(User::ROLE_CUSTOMER_SERVICE)) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can update the client.
     *
     * @param  \App\Repositories\User\User  $user
     * @param  \App\Repositories\Client\Client  $client
     * @return mixed
     */
    public function update(User $user, Client $client)
    {
        if($user->hasRole(User::ROLE_ADMIN)) {
            return true;
        }

        if($user->hasRole(User::ROLE_CUSTOMER_SERVICE)) {
            if(!$client->internal) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine whether the user can delete the client.
     *
     * @param  \App\Repositories\User\User  $user
     * @param  \App\Repositories\Client\Client  $client
     * @return mixed
     */
    public function delete(User $user, Client $client)
    {
        //
    }
}

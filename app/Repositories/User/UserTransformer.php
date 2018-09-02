<?php
namespace App\Repositories\User;

use League\Fractal\TransformerAbstract;
use App\Repositories\User\User;

/**
 * UserTransformer
 * 
 * With the trnasformer we can chose what data to send to api response and what relations to be included in the response
 */
class UserTransformer extends TransformerAbstract {

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [];

    /**
     * List of resources that are available to be added in the User object response
     *
     * @var array
     */
    protected $availableIncludes = [];

    public function transform(User $user)
    {
        return $user->toArray();
    }
} 
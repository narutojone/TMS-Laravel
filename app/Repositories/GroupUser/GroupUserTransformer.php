<?php

namespace App\Repositories\GroupUser;

use League\Fractal\TransformerAbstract;

/**
 * GroupUserTransformer
 * 
 * With the trnasformer we can chose what data to send to api response and what relations to be included in the response
 */
class GroupUserTransformer extends TransformerAbstract {

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [];

    /**
     * List of resources that are available to be added in the GroupUser object response
     *
     * @var array
     */
    protected $availableIncludes = [];

    public function transform(GroupUser $groupUser)
    {
        return $groupUser->toArray();
    }
} 
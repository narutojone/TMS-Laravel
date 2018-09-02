<?php

namespace App\Repositories\UserWorkload;

use League\Fractal\TransformerAbstract;

/**
 * UserWorkloadTransformer
 * 
 * With the transformer we can chose what data to send to api response and what relations to be included in the response
 */
class UserWorkloadTransformer extends TransformerAbstract {

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [];

    /**
     * List of resources that are available to be added in the UserWorkload object response
     *
     * @var array
     */
    protected $availableIncludes = [];

    public function transform(UserWorkload $userWorkload)
    {
        return $userWorkload->toArray();
    }
} 
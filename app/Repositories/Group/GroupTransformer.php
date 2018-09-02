<?php
namespace App\Repositories\Group;

use League\Fractal\TransformerAbstract;

/**
 * GroupTransformer
 * 
 * With the trnasformer we can chose what data to send to api response and what relations to be included in the response
 */
class GroupTransformer extends TransformerAbstract {

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [];

    /**
     * List of resources that are available to be added in the Group object response
     *
     * @var array
     */
    protected $availableIncludes = [];

    public function transform(Group $group)
    {
        return $group->toArray();
    }
} 
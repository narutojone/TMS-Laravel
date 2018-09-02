<?php
namespace App\Repositories\UserTaskType;

use League\Fractal\TransformerAbstract;
use App\Repositories\UserTaskType\UserTaskType;

/**
 * UserTaskTypeTransformer
 * 
 * With the trnasformer we can chose what data to send to api response and what relations to be included in the response
 */
class UserTaskTypeTransformer extends TransformerAbstract {

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = ['user'];

    /**
     * List of resources that are available to be added in the UserTaskType object response
     *
     * @var array
     */
    protected $availableIncludes = ['user'];

    public function transform(UserTaskType $userTaskType)
    {
        return $userTaskType->toArray();
    }
} 
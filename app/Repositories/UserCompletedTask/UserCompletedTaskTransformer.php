<?php
namespace App\Repositories\UserCompletedTask;

use League\Fractal\TransformerAbstract;

/**
 * UserCompletedTaskTransformer
 * 
 * With the trnasformer we can chose what data to send to api response and what relations to be included in the response
 */
class UserCompletedTaskTransformer extends TransformerAbstract {

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = ['user'];

    /**
     * List of resources that are available to be added in the userCompletedTask object response
     *
     * @var array
     */
    protected $availableIncludes = ['user'];

    public function transform(UserCompletedTask $userCompletedTask)
    {
        return $userCompletedTask->toArray();
    }

}
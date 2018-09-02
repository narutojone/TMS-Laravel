<?php
namespace App\Repositories\UserCompletedSubtask;

use League\Fractal\TransformerAbstract;

/**
 * UserCompletedSubtaskTransformer
 * 
 * With the transformer we can chose what data to send to api response and what relations to be included in the response
 */
class UserCompletedSubtaskTransformer extends TransformerAbstract {

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = ['user'];

    /**
     * List of resources that are available to be added in the userCompletedSubtask object response
     *
     * @var array
     */
    protected $availableIncludes = ['user'];

    public function transform(UserCompletedSubtask $userCompletedSubtask)
    {
        return $userCompletedSubtask->toArray();
    }

}
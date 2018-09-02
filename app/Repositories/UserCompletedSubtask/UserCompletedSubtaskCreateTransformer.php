<?php
namespace App\Repositories\UserCompletedSubtask;

use League\Fractal\TransformerAbstract;

/**
 * UserCompletedSubtaskCreateTransformer
 *
 * With the transformer we can chose what data to send to api response and what relations to be included in the response
 */
class UserCompletedSubtaskCreateTransformer extends TransformerAbstract {

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [];

    /**
     * List of resources that are available to be added in the userCompletedSubtask object response
     *
     * @var array
     */
    protected $availableIncludes = [];

    public function transform(UserCompletedSubtask $userCompletedSubtask)
    {
        return [
            'subtask_id'                => $userCompletedSubtask->subtask_id,
            'status'                    => $userCompletedSubtask->status,
        ];
    }

}
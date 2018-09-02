<?php
namespace App\Repositories\UserCompletedTask;

use League\Fractal\TransformerAbstract;

/**
 * UserCompletedTaskTransformer
 *
 * With the transformer we can chose what data to send to api response and what relations to be included in the response
 */
class UserCompletedTaskCreateTransformer extends TransformerAbstract {

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [];

    /**
     * List of resources that are available to be added in the userCompletedTask object response
     *
     * @var array
     */
    protected $availableIncludes = [];

    public function transform(UserCompletedTask $userCompletedTask)
    {
        return [
            'user_id'       => $userCompletedTask->user_id,
            'user_level'    => $userCompletedTask->user_level,
            'task_id'       => $userCompletedTask->task_id,
            'template_id'   => $userCompletedTask->template_id,
            'status'        => UserCompletedTask::STATUS_PENDING,
        ];
    }

}
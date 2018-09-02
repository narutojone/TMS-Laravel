<?php
namespace App\Repositories\TaskReopening;

use League\Fractal\TransformerAbstract;

/**
 * TaskReopeningTransformer
 * 
 * With the trnasformer we can chose what data to send to api response and what relations to be included in the response
 */
class TaskReopeningTransformer extends TransformerAbstract {

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [];

    /**
     * List of resources that are available to be added in the TaskReopening object response
     *
     * @var array
     */
    protected $availableIncludes = [];

    public function transform(TaskReopening $taskReopening)
    {
        return $taskReopening->toArray();
    }
} 
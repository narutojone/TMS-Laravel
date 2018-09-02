<?php
namespace App\Repositories\TaskType;

use League\Fractal\TransformerAbstract;
use App\Repositories\TaskType\TaskType;

/**
 * TaskTypeTransformer
 * 
 * With the trnasformer we can chose what data to send to api response and what relations to be included in the response
 */
class TaskTypeTransformer extends TransformerAbstract {

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [];

    /**
     * List of resources that are available to be added in the TaskType object response
     *
     * @var array
     */
    protected $availableIncludes = [];

    public function transform(TaskType $taskType)
    {
        return $taskType->toArray();
    }
} 
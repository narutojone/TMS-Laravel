<?php
namespace App\Repositories\Task;

use League\Fractal\TransformerAbstract;
use App\Repositories\Task\Task;

/**
 * TaskTransformer
 * 
 * With the trnasformer we can chose what data to send to api response and what relations to be included in the response
 */
class TaskTransformer extends TransformerAbstract {

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [];

    /**
     * List of resources that are available to be added in the Task object response
     *
     * @var array
     */
    protected $availableIncludes = [];

    public function transform(Task $task)
    {
        return $task->toArray();
    }
} 
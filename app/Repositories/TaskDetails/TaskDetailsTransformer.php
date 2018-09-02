<?php

namespace App\Repositories\TaskDetails;

use League\Fractal\TransformerAbstract;
use App\Repositories\TaskDetails\TaskDetails;

/**
 * TaskDetailsTransformer
 * 
 * With the transformer we can chose what data to send to api response and what relations to be included in the response
 */
class TaskDetailsTransformer extends TransformerAbstract {

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [];

    /**
     * List of resources that are available to be added in the TaskDetails object response
     *
     * @var array
     */
    protected $availableIncludes = [];

    public function transform(TaskDetails $taskDetails)
    {
        return $taskDetails->toArray();
    }
} 
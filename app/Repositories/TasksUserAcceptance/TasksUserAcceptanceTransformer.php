<?php
namespace App\Repositories\TasksUserAcceptance;

use League\Fractal\TransformerAbstract;
use App\Repositories\TasksUserAcceptance\TasksUserAcceptance;

/**
 * TasksUserAcceptanceTransformer
 * 
 * With the trnasformer we can chose what data to send to api response and what relations to be included in the response
 */
class TasksUserAcceptanceTransformer extends TransformerAbstract {

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [];

    /**
     * List of resources that are available to be added in the TasksUserAcceptance object response
     *
     * @var array
     */
    protected $availableIncludes = [];

    public function transform(TasksUserAcceptance $tasksUserAcceptance)
    {
        return $tasksUserAcceptance->toArray();
    }
} 
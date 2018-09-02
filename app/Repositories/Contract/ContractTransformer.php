<?php

namespace App\Repositories\Contract;

use League\Fractal\TransformerAbstract;
use App\Repositories\Task\Task;

/**
 * ContractTransformer
 * 
 * With the transformer we can chose what data to send to api response and what relations to be included in the response
 */
class ContractTransformer extends TransformerAbstract {

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [];

    /**
     * List of resources that are available to be added in the Contract object response
     *
     * @var array
     */
    protected $availableIncludes = [];

    public function transform(Task $task)
    {
        return $task->toArray();
    }
} 
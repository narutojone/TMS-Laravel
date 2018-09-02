<?php

namespace App\Repositories\Subtask;

use League\Fractal\TransformerAbstract;

/**
 * SubtaskTransformer
 * 
 * With the trnasformer we can chose what data to send to api response and what relations to be included in the response
 */
class SubtaskTransformer extends TransformerAbstract {

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [];

    /**
     * List of resources that are available to be added in the Subtask object response
     *
     * @var array
     */
    protected $availableIncludes = [];

    public function transform(Subtask $subtask)
    {
        return $subtask->toArray();
    }
} 
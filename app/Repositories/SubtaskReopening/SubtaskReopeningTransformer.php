<?php

namespace App\Repositories\SubtaskReopening;

use League\Fractal\TransformerAbstract;

/**
 * SubtaskReopeningTransformer
 * 
 * With the trnasformer we can chose what data to send to api response and what relations to be included in the response
 */
class SubtaskReopeningTransformer extends TransformerAbstract {

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [];

    /**
     * List of resources that are available to be added in the SubtaskReopening object response
     *
     * @var array
     */
    protected $availableIncludes = [];

    public function transform(SubtaskReopening $subtaskReopening)
    {
        return $subtaskReopening->toArray();
    }
} 
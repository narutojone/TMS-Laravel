<?php
namespace App\Repositories\HarvestDevTimeEntry;

use League\Fractal\TransformerAbstract;

/**
 * TaskTransformer
 * 
 * With the trnasformer we can chose what data to send to api response and what relations to be included in the response
 */
class HarvestDevTimeEntryTransformer extends TransformerAbstract {

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

    public function transform(HarvestDevTimeEntry $issue)
    {
        return $issue->toArray();
    }
} 
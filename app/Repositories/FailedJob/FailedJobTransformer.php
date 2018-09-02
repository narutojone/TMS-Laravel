<?php
namespace App\Repositories\FailedJob;

use League\Fractal\TransformerAbstract;
use App\Repositories\FailedJob\FailedJob;

/**
 * FailedJobTransformer
 * 
 * With the trnasformer we can chose what data to send to api response and what relations to be included in the response
 */
class FailedJobTransformer extends TransformerAbstract {

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [];

    /**
     * List of resources that are available to be added in the FailedJob object response
     *
     * @var array
     */
    protected $availableIncludes = [];

    public function transform(FailedJob $FailedJob)
    {
        return $FailedJob->toArray();
    }
} 
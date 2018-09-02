<?php
namespace App\Repositories\MatchingQueue;

use League\Fractal\TransformerAbstract;
use App\Repositories\MatchingQueue\MatchingQueue;

/**
 * MatchingQueueTransformer
 * 
 * With the trnasformer we can chose what data to send to api response and what relations to be included in the response
 */
class MatchingQueueTransformer extends TransformerAbstract {

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [];

    /**
     * List of resources that are available to be added in the MatchingQueue object response
     *
     * @var array
     */
    protected $availableIncludes = [];

    public function transform(MatchingQueue $matchingQueue)
    {
        return $matchingQueue->toArray();
    }
} 
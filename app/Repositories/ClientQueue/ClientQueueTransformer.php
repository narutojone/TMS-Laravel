<?php
namespace App\Repositories\ClientQueue;

use League\Fractal\TransformerAbstract;
use App\Repositories\ClientQueue\ClientQueue;

/**
 * ClientQueueTransformer
 * 
 * With the trnasformer we can chose what data to send to api response and what relations to be included in the response
 */
class ClientQueueTransformer extends TransformerAbstract {

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [];

    /**
     * List of resources that are available to be added in the ClientQueue object response
     *
     * @var array
     */
    protected $availableIncludes = [];

    public function transform(ClientQueue $clientQueue)
    {
        return $clientQueue->toArray();
    }
} 
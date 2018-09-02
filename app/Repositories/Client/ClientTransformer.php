<?php

namespace App\Repositories\Client;

use League\Fractal\TransformerAbstract;
use App\Repositories\Client\Client;

/**
 * ClientTransformer
 * 
 * With the transformer we can chose what data to send to api response and what relations to be included in the response
 */
class ClientTransformer extends TransformerAbstract {

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [];

    /**
     * List of resources that are available to be added in the Client object response
     *
     * @var array
     */
    protected $availableIncludes = [];

    public function transform(Client $client)
    {
        return $client->toArray();
    }
} 
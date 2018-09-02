<?php

namespace App\Repositories\ClientPhone;

use League\Fractal\TransformerAbstract;
use App\Repositories\ClientPhone\ClientPhone;

/**
 * ClientPhoneTransformer
 * 
 * With the trnasformer we can chose what data to send to api response and what relations to be included in the response
 */
class ClientPhoneTransformer extends TransformerAbstract {

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [];

    /**
     * List of resources that are available to be added in the ClientPhone object response
     *
     * @var array
     */
    protected $availableIncludes = [];

    public function transform(ClientPhone $clientPhone)
    {
        return $clientPhone->toArray();
    }
} 
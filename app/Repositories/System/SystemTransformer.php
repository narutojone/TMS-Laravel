<?php

namespace App\Repositories\System;

use League\Fractal\TransformerAbstract;

/**
 * SystemTransformer
 * 
 * With the trnasformer we can chose what data to send to api response and what relations to be included in the response
 */
class SystemTransformer extends TransformerAbstract {

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [];

    /**
     * List of resources that are available to be added in the System object response
     *
     * @var array
     */
    protected $availableIncludes = [];

    public function transform(System $system)
    {
        return $system->toArray();
    }
} 
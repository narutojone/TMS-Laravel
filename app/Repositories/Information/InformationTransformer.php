<?php

namespace App\Repositories\Information;

use League\Fractal\TransformerAbstract;

/**
 * InformationTransformer
 * 
 * With the trnasformer we can chose what data to send to api response and what relations to be included in the response
 */
class InformationTransformer extends TransformerAbstract {

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [];

    /**
     * List of resources that are available to be added in the Information object response
     *
     * @var array
     */
    protected $availableIncludes = [];

    public function transform(Information $information)
    {
        return $information->toArray();
    }
} 
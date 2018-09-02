<?php

namespace App\Repositories\Flag;

use League\Fractal\TransformerAbstract;

/**
 * FlagTransformer
 * 
 * With the trnasformer we can chose what data to send to api response and what relations to be included in the response
 */
class FlagTransformer extends TransformerAbstract {

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [];

    /**
     * List of resources that are available to be added in the Flag object response
     *
     * @var array
     */
    protected $availableIncludes = [];

    public function transform(Flag $flag)
    {
        return $flag->toArray();
    }
} 
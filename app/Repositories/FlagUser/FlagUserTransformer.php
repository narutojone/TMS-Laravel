<?php

namespace App\Repositories\FlagUser;

use League\Fractal\TransformerAbstract;

/**
 * FlagUserTransformer
 * 
 * With the trnasformer we can chose what data to send to api response and what relations to be included in the response
 */
class FlagUserTransformer extends TransformerAbstract {

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [];

    /**
     * List of resources that are available to be added in the FlagUser object response
     *
     * @var array
     */
    protected $availableIncludes = [];

    public function transform(FlagUser $flagUser)
    {
        return $flagUser->toArray();
    }
} 
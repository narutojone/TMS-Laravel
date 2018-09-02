<?php
namespace App\Repositories\RatingRequest;

use League\Fractal\TransformerAbstract;
use App\Repositories\RatingRequest\RatingRequest;

/**
 * RatingRequestTransformer
 * 
 * With the trnasformer we can chose what data to send to api response and what relations to be included in the response
 */
class RatingRequestTransformer extends TransformerAbstract {

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [];

    /**
     * List of resources that are available to be added in the RatingRequest object response
     *
     * @var array
     */
    protected $availableIncludes = [];

    public function transform(RatingRequest $ratingRequest)
    {
        return $ratingRequest->toArray();
    }
} 
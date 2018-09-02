<?php
namespace App\Repositories\Rating;

use League\Fractal\TransformerAbstract;
use App\Repositories\Rating\Rating;

/**
 * RatingTransformer
 * 
 * With the trnasformer we can chose what data to send to api response and what relations to be included in the response
 */
class RatingTransformer extends TransformerAbstract {

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [];

    /**
     * List of resources that are available to be added in the Rating object response
     *
     * @var array
     */
    protected $availableIncludes = [];

    public function transform(Rating $rating)
    {
        return $rating->toArray();
    }
} 
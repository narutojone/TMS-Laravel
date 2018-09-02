<?php

namespace App\Repositories\Review;

use League\Fractal\TransformerAbstract;

/**
 * ReviewTransformer
 * 
 * With the trnasformer we can chose what data to send to api response and what relations to be included in the response
 */
class ReviewTransformer extends TransformerAbstract {

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [];

    /**
     * List of resources that are available to be added in the Review object response
     *
     * @var array
     */
    protected $availableIncludes = [];

    public function transform(Review $review)
    {
        return $review->toArray();
    }
} 
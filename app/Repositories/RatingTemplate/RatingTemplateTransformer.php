<?php
namespace App\Repositories\RatingTemplate;

use League\Fractal\TransformerAbstract;
use App\Repositories\RatingTemplate\RatingTemplate;

/**
 * RatingTemplateTransformer
 * 
 * With the trnasformer we can chose what data to send to api response and what relations to be included in the response
 */
class RatingTemplateTransformer extends TransformerAbstract {

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [];

    /**
     * List of resources that are available to be added in the RatingTemplate object response
     *
     * @var array
     */
    protected $availableIncludes = [];

    public function transform(RatingTemplate $ratingTemplate)
    {
        return $ratingTemplate->toArray();
    }
} 
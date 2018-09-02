<?php

namespace App\Repositories\ReviewSetting;

use League\Fractal\TransformerAbstract;

/**
 * ReviewSettingTransformer
 * 
 * With the trnasformer we can chose what data to send to api response and what relations to be included in the response
 */
class ReviewSettingTransformer extends TransformerAbstract {

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [];

    /**
     * List of resources that are available to be added in the ReviewSetting object response
     *
     * @var array
     */
    protected $availableIncludes = [];

    public function transform(ReviewSetting $reviewSetting)
    {
        return $reviewSetting->toArray();
    }
} 
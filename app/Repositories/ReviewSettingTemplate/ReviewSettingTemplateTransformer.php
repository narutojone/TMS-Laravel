<?php

namespace App\Repositories\ReviewSettingTemplate;

use League\Fractal\TransformerAbstract;

/**
 * ReviewSettingTemplateTransformer
 * 
 * With the trnasformer we can chose what data to send to api response and what relations to be included in the response
 */
class ReviewSettingTemplateTransformer extends TransformerAbstract {

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [];

    /**
     * List of resources that are available to be added in the ReviewSettingTemplate object response
     *
     * @var array
     */
    protected $availableIncludes = [];

    public function transform(ReviewSettingTemplate $reviewSettingTemplate)
    {
        return $reviewSettingTemplate->toArray();
    }
} 
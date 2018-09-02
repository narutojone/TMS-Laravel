<?php

namespace App\Repositories\TemplateSubtaskVersion;

use League\Fractal\TransformerAbstract;
use App\Repositories\TemplateSubtaskVersion\TemplateSubtaskVersion;

/**
 * TemplateSubtaskVersionTransformer
 * 
 * With the transformer we can chose what data to send to api response and what relations to be included in the response
 */
class TemplateSubtaskVersionTransformer extends TransformerAbstract {

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [];

    /**
     * List of resources that are available to be added in the TemplateSubtaskVersion object response
     *
     * @var array
     */
    protected $availableIncludes = [];

    public function transform(TemplateSubtaskVersion $templateSubtaskVersion)
    {
        return $templateSubtaskVersion->toArray();
    }
} 
<?php

namespace App\Repositories\TemplateOverdueReason;

use League\Fractal\TransformerAbstract;

/**
 * TemplateOverdueReasonTransformer
 * 
 * With the transformer we can chose what data to send to api response and what relations to be included in the response
 */
class TemplateOverdueReasonTransformer extends TransformerAbstract {

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [];

    /**
     * List of resources that are available to be added in the TemplateOverdueReason object response
     *
     * @var array
     */
    protected $availableIncludes = [];

    public function transform(TemplateOverdueReason $templateOverdueReason)
    {
        return $templateOverdueReason->toArray();
    }
} 
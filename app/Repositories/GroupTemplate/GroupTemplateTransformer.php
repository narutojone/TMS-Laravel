<?php

namespace App\Repositories\GroupTemplate;

use League\Fractal\TransformerAbstract;

/**
 * GroupTemplateTransformer
 * 
 * With the trnasformer we can chose what data to send to api response and what relations to be included in the response
 */
class GroupTemplateTransformer extends TransformerAbstract {

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [];

    /**
     * List of resources that are available to be added in the GroupTemplate object response
     *
     * @var array
     */
    protected $availableIncludes = [];

    public function transform(GroupTemplate $groupTemplate)
    {
        return $groupTemplate->toArray();
    }
} 
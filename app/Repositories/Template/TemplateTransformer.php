<?php

namespace App\Repositories\Template;

use League\Fractal\TransformerAbstract;
use App\Repositories\Template\Template;

/**
 * TemplateTransformer
 * 
 * With the transformer we can chose what data to send to api response and what relations to be included in the response
 */
class TemplateTransformer extends TransformerAbstract {

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [];

    /**
     * List of resources that are available to be added in the Template object response
     *
     * @var array
     */
    protected $availableIncludes = [];

    public function transform(Template $template)
    {
        return $template->toArray();
    }
} 
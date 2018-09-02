<?php
namespace App\Repositories\TemplateSubtaskModule;

use League\Fractal\TransformerAbstract;

/**
 * TemplateSubtaskModuleTransformer
 * 
 * With the trnasformer we can chose what data to send to api response and what relations to be included in the response
 */
class TemplateSubtaskModuleTransformer extends TransformerAbstract {

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [];

    /**
     * List of resources that are available to be added in the TemplateSubtaskModule object response
     *
     * @var array
     */
    protected $availableIncludes = [];

    public function transform(TemplateSubtaskModule $templateSubtaskModule)
    {
        return $templateSubtaskModule->toArray();
    }
} 
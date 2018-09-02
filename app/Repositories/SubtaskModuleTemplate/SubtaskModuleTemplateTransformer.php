<?php
namespace App\Repositories\SubtaskModuleTemplate;

use League\Fractal\TransformerAbstract;
use App\Repositories\SubtaskModuleTemplate\SubtaskModuleTemplate;

/**
 * SubtaskModuleTemplateTransformer
 * 
 * With the trnasformer we can chose what data to send to api response and what relations to be included in the response
 */
class SubtaskModuleTemplateTransformer extends TransformerAbstract {

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [];

    /**
     * List of resources that are available to be added in the SubtaskModuleTemplate object response
     *
     * @var array
     */
    protected $availableIncludes = [];

    public function transform(SubtaskModuleTemplate $subtaskModuleTemplate)
    {
        return $subtaskModuleTemplate->toArray();
    }
} 
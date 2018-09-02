<?php
namespace App\Repositories\TemplateSubtask;

use League\Fractal\TransformerAbstract;
use App\Repositories\TemplateSubtask\TemplateSubtask;

/**
 * TemplateSubtaskTransformer
 * 
 * With the trnasformer we can chose what data to send to api response and what relations to be included in the response
 */
class TemplateSubtaskTransformer extends TransformerAbstract {

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [];

    /**
     * List of resources that are available to be added in the TemplateSubtask object response
     *
     * @var array
     */
    protected $availableIncludes = [];

    public function transform(TemplateSubtask $templateSubtask)
    {
        return $templateSubtask->toArray();
    }
} 
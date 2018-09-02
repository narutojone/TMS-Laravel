<?php
namespace App\Repositories\EmailTemplate;

use League\Fractal\TransformerAbstract;
use App\Repositories\EmailTemplate\EmailTemplate;

/**
 * EmailTemplateTransformer
 * 
 * With the trnasformer we can chose what data to send to api response and what relations to be included in the response
 */
class EmailTemplateTransformer extends TransformerAbstract {

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [];

    /**
     * List of resources that are available to be added in the EmailTemplate object response
     *
     * @var array
     */
    protected $availableIncludes = [];

    public function transform(EmailTemplate $emailTemplate)
    {
        return $emailTemplate->toArray();
    }
} 
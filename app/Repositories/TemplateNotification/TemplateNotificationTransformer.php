<?php
namespace App\Repositories\TemplateNotification;

use League\Fractal\TransformerAbstract;
use App\Repositories\TemplateNotification\TemplateNotification;

/**
 * TemplateNotificationTransformer
 * 
 * With the trnasformer we can chose what data to send to api response and what relations to be included in the response
 */
class TemplateNotificationTransformer extends TransformerAbstract {

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [];

    /**
     * List of resources that are available to be added in the TemplateNotification object response
     *
     * @var array
     */
    protected $availableIncludes = [];

    public function transform(TemplateNotification $templateNotification)
    {
        return $templateNotification->toArray();
    }
} 
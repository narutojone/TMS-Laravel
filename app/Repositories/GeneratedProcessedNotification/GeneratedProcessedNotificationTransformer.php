<?php
namespace App\Repositories\GeneratedProcessedNotification;

use League\Fractal\TransformerAbstract;
use App\Repositories\GeneratedProcessedNotification\GeneratedProcessedNotification;

/**
 * GeneratedProcessedNotificationTransformer
 * 
 * With the trnasformer we can chose what data to send to api response and what relations to be included in the response
 */
class GeneratedProcessedNotificationTransformer extends TransformerAbstract {

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [];

    /**
     * List of resources that are available to be added in the GeneratedProcessedNotification object response
     *
     * @var array
     */
    protected $availableIncludes = [];

    public function transform(GeneratedProcessedNotification $generatedProcessedNotification)
    {
        $data = $generatedProcessedNotification->toArray();
        $data['data'] = json_decode($data['data'], true);

        return $data;
    }
} 
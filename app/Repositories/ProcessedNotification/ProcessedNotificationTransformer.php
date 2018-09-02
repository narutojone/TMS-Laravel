<?php
namespace App\Repositories\ProcessedNotification;

use League\Fractal\TransformerAbstract;
use App\Repositories\ProcessedNotification\ProcessedNotification;

/**
 * ProcessedNotificationTransformer
 * 
 * With the trnasformer we can chose what data to send to api response and what relations to be included in the response
 */
class ProcessedNotificationTransformer extends TransformerAbstract {

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [];

    /**
     * List of resources that are available to be added in the ProcessedNotification object response
     *
     * @var array
     */
    protected $availableIncludes = [];

    public function transform(ProcessedNotification $processedNotification)
    {
        $data = $processedNotification->toArray();
        $data['data'] = json_decode($data['data'], true);

        return $data;
    }
} 
<?php
namespace App\Repositories\ProcessedNotificationLog;

use League\Fractal\TransformerAbstract;
use App\Repositories\ProcessedNotificationLog\ProcessedNotificationLog;

/**
 * ProcessedNotificationLogTransformer
 * 
 * With the trnasformer we can chose what data to send to api response and what relations to be included in the response
 */
class ProcessedNotificationLogTransformer extends TransformerAbstract {

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [];

    /**
     * List of resources that are available to be added in the ProcessedNotificationLog object response
     *
     * @var array
     */
    protected $availableIncludes = [];

    public function transform(ProcessedNotificationLog $processedNotificationLog)
    {
        $data = $processedNotificationLog->toArray();
        $data['data'] = json_decode($data['data'], true);

        return $data;
    }
} 
<?php
namespace App\Repositories\LogSubtaskUserAcceptance;

use League\Fractal\TransformerAbstract;
use App\Repositories\LogSubtaskUserAcceptance\LogSubtaskUserAcceptance;

/**
 * LogSubtaskUserAcceptanceTransformer
 * 
 * With the trnasformer we can chose what data to send to api response and what relations to be included in the response
 */
class LogSubtaskUserAcceptanceTransformer extends TransformerAbstract {

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [];

    /**
     * List of resources that are available to be added in the LogSubtaskUserAcceptance object response
     *
     * @var array
     */
    protected $availableIncludes = [];

    public function transform(LogSubtaskUserAcceptance $logSubtaskUserAcceptance)
    {
        return $logSubtaskUserAcceptance->toArray();
    }
} 
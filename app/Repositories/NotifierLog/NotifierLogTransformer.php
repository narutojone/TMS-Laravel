<?php
namespace App\Repositories\NotifierLog;

use League\Fractal\TransformerAbstract;
use App\Repositories\NotifierLog\NotifierLog;

/**
 * NotifierLogTransformer
 * 
 * With the trnasformer we can chose what data to send to api response and what relations to be included in the response
 */
class NotifierLogTransformer extends TransformerAbstract {

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [];

    /**
     * List of resources that are available to be added in the NotifierLog object response
     *
     * @var array
     */
    protected $availableIncludes = [];

    public function transform(NotifierLog $notifierLog)
    {
        return $notifierLog->toArray();
    }
} 
<?php
namespace App\Repositories\ClientEmployeeLog;

use League\Fractal\TransformerAbstract;
use App\Repositories\ClientEmployeeLog\ClientEmployeeLog;

/**
 * ClientEmployeeLogTransformer
 * 
 * With the trnasformer we can chose what data to send to api response and what relations to be included in the response
 */
class ClientEmployeeLogTransformer extends TransformerAbstract {

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [];

    /**
     * List of resources that are available to be added in the ClientEmployeeLog object response
     *
     * @var array
     */
    protected $availableIncludes = [];

    public function transform(ClientEmployeeLog $clientEmployeeLog)
    {
        return $clientEmployeeLog->toArray();
    }
} 
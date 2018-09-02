<?php
namespace App\Repositories\PhoneSystemLog;

use League\Fractal\TransformerAbstract;
use App\Repositories\PhoneSystemLog\PhoneSystemLog;

/**
 * PhoneSystemLogTransformer
 * 
 * With the trnasformer we can chose what data to send to api response and what relations to be included in the response
 */
class PhoneSystemLogTransformer extends TransformerAbstract {

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [];

    /**
     * List of resources that are available to be added in the PhoneSystemLog object response
     *
     * @var array
     */
    protected $availableIncludes = [];

    public function transform(PhoneSystemLog $phoneSystemLog)
    {
        return $phoneSystemLog->toArray();
    }
} 
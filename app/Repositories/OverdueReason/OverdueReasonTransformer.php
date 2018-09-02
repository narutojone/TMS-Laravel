<?php
namespace App\Repositories\OverdueReason;

use League\Fractal\TransformerAbstract;
use App\Repositories\OverdueReason\OverdueReason;

/**
 * OverdueReasonTransformer
 * 
 * With the trnasformer we can chose what data to send to api response and what relations to be included in the response
 */
class OverdueReasonTransformer extends TransformerAbstract {

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [];

    /**
     * List of resources that are available to be added in the OverdueReason object response
     *
     * @var array
     */
    protected $availableIncludes = [];

    public function transform(OverdueReason $overdueReason)
    {
        return $overdueReason->toArray();
    }
} 
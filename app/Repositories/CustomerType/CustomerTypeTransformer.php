<?php
namespace App\Repositories\CustomerType;

use League\Fractal\TransformerAbstract;
use App\Repositories\CustomerType\CustomerType;

/**
 * CustomerTypeTransformer
 * 
 * With the trnasformer we can chose what data to send to api response and what relations to be included in the response
 */
class CustomerTypeTransformer extends TransformerAbstract {

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [];

    /**
     * List of resources that are available to be added in the CustomerType object response
     *
     * @var array
     */
    protected $availableIncludes = [];

    public function transform(CustomerType $customerType)
    {
        return $customerType->toArray();
    }
} 
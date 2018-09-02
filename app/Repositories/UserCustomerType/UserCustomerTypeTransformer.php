<?php
namespace App\Repositories\UserCustomerType;

use League\Fractal\TransformerAbstract;
use App\Repositories\UserCustomerType\UserCustomerType;

/**
 * UserCustomerTypeTransformer
 * 
 * With the trnasformer we can chose what data to send to api response and what relations to be included in the response
 */
class UserCustomerTypeTransformer extends TransformerAbstract {

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [];

    /**
     * List of resources that are available to be added in the UserCustomerType object response
     *
     * @var array
     */
    protected $availableIncludes = [];

    public function transform(UserCustomerType $userCustomerType)
    {
        return $userCustomerType->toArray();
    }
} 
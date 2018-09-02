<?php
namespace App\Repositories\UserOutOutOffice;

use League\Fractal\TransformerAbstract;
use App\Repositories\UserOutOutOffice\UserOutOutOffice;

/**
 * UserOutOutOfficeTransformer
 * 
 * With the trnasformer we can chose what data to send to api response and what relations to be included in the response
 */
class UserOutOutOfficeTransformer extends TransformerAbstract {

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [];

    /**
     * List of resources that are available to be added in the UserOutOutOffice object response
     *
     * @var array
     */
    protected $availableIncludes = [];

    public function transform(UserOutOutOffice $userOutOutOffice)
    {
        return $userOutOutOffice->toArray();
    }
} 
<?php
namespace App\Repositories\InformationUser;

use League\Fractal\TransformerAbstract;
use App\Repositories\InformationUser\InformationUser;

/**
 * InformationUserTransformer
 * 
 * With the trnasformer we can chose what data to send to api response and what relations to be included in the response
 */
class InformationUserTransformer extends TransformerAbstract {

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [];

    /**
     * List of resources that are available to be added in the InformationUser object response
     *
     * @var array
     */
    protected $availableIncludes = [];

    public function transform(InformationUser $informationUser)
    {
        return $informationUser->toArray();
    }
} 
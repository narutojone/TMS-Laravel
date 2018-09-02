<?php
namespace App\Repositories\Invitation;

use League\Fractal\TransformerAbstract;
use App\Repositories\Invitation\Invitation;

/**
 * InvitationTransformer
 * 
 * With the trnasformer we can chose what data to send to api response and what relations to be included in the response
 */
class InvitationTransformer extends TransformerAbstract {

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [];

    /**
     * List of resources that are available to be added in the Invitation object response
     *
     * @var array
     */
    protected $availableIncludes = [];

    public function transform(Invitation $invitation)
    {
        return $invitation->toArray();
    }
} 
<?php
namespace App\Repositories\PasswordReset;

use League\Fractal\TransformerAbstract;
use App\Repositories\PasswordReset\PasswordReset;

/**
 * PasswordResetTransformer
 * 
 * With the trnasformer we can chose what data to send to api response and what relations to be included in the response
 */
class PasswordResetTransformer extends TransformerAbstract {

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [];

    /**
     * List of resources that are available to be added in the PasswordReset object response
     *
     * @var array
     */
    protected $availableIncludes = [];

    public function transform(PasswordReset $passwordReset)
    {
        return $passwordReset->toArray();
    }
} 
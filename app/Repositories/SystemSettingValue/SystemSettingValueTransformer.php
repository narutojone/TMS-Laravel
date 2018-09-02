<?php
namespace App\Repositories\SystemSettingValue;

use League\Fractal\TransformerAbstract;
use App\Repositories\SystemSettingValue\SystemSettingValue;

/**
 * SystemSettingValueTransformer
 * 
 * With the trnasformer we can chose what data to send to api response and what relations to be included in the response
 */
class SystemSettingValueTransformer extends TransformerAbstract {

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [];

    /**
     * List of resources that are available to be added in the SystemSettingValue object response
     *
     * @var array
     */
    protected $availableIncludes = [];

    public function transform(SystemSettingValue $systemSettingValue)
    {
        return $systemSettingValue->toArray();
    }
} 
<?php
namespace App\Repositories\SystemSettingGroup;

use League\Fractal\TransformerAbstract;
use App\Repositories\SystemSettingGroup\SystemSettingGroup;

/**
 * SystemSettingGroupTransformer
 * 
 * With the trnasformer we can chose what data to send to api response and what relations to be included in the response
 */
class SystemSettingGroupTransformer extends TransformerAbstract {

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [];

    /**
     * List of resources that are available to be added in the SystemSettingGroup object response
     *
     * @var array
     */
    protected $availableIncludes = [];

    public function transform(SystemSettingGroup $systemSettingGroup)
    {
        return $systemSettingGroup->toArray();
    }
} 
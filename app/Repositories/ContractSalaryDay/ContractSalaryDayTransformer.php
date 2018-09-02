<?php

namespace App\Repositories\ContractSalaryDay;

use League\Fractal\TransformerAbstract;

/**
 * ContractSalaryDayTransformer
 * 
 * With the transformer we can chose what data to send to api response and what relations to be included in the response
 */
class ContractSalaryDayTransformer extends TransformerAbstract {

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [];

    /**
     * List of resources that are available to be added in the ContractSalaryDay object response
     *
     * @var array
     */
    protected $availableIncludes = [];

    public function transform(ContractSalaryDay $contractSalaryDay)
    {
        return $contractSalaryDay->toArray();
    }
} 
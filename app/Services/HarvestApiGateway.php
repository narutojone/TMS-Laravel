<?php

namespace App\Services;

use Zttp\Zttp;
use Byte5\LaravelHarvest\ApiGateway;

class HarvestApiGateway extends ApiGateway
{
    /**
     * @var string
     */
    protected $apiKey;
    /**
     * @var string
     */
    protected $accountId;

    /**
     * HarvestApiGateway constructor.
     * @param bool $mainAccount
     */
    public function __construct($mainAccount = false)
    {
        $configApiKey = $mainAccount ? 'harvest.main_api_key' : 'harvest.api_key';
        $configAccountIdKey = $mainAccount ? 'harvest.main_account_id' : 'harvest.account_id';

        $this->apiKey = 'Bearer ' . config($configApiKey);
        $this->accountId = config($configAccountIdKey);
    }

    /**
     * @param $path
     * @return mixed
     */
    public function execute($path)
    {
        return Zttp::withHeaders([
            'Authorization' => $this->apiKey,
            'Harvest-Account-Id' => $this->accountId
        ])->get($path);
    }
}
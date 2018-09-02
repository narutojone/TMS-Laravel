<?php

namespace App\Providers;

use App\Services\HarvestApiGateway;
use Byte5\LaravelHarvest\ApiManager;
use Illuminate\Support\ServiceProvider;

class CustomHarvestServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('custom-harvest', function ($app, $params = []) {
            $mainAccount = $params['mainAccount'] ?? false;
            $apiGateway = new HarvestApiGateway($mainAccount);
            $apiManager = new ApiManager($apiGateway);
            return $apiManager;
        });
    }
}

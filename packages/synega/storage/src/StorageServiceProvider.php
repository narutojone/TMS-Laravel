<?php

namespace Synega\Storage;

use Illuminate\Support\ServiceProvider;

class StorageServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {

    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
		include __DIR__.'/routes.php';
		$this->app->make('Synega\Storage\StorageController');

		$loader = \Illuminate\Foundation\AliasLoader::getInstance();
		$loader->alias('FileVault', 'Synega\Storage\Facades\FileVault');


		$this->app->bind('FileVault', function()
		{
			return new \Synega\Storage\FileVault;
		});

    }
}

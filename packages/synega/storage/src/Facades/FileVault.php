<?php

namespace Synega\Storage\Facades;

use Illuminate\Support\Facades\Facade;

class FileVault extends Facade {

	protected static function getFacadeAccessor()
	{
		return 'FileVault';
	}
}
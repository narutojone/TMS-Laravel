<?php

namespace App\Lists\Facades;

use Illuminate\Support\Facades\Facade;

class ListsFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'lists';
    }
}

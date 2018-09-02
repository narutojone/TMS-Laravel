<?php

namespace App\Observers;

use App\Repositories\Information\Information;

class InformationObserver
{
    /**
     * @param \App\Repositories\Information\Information $information
     */
    public function deleting(Information $information)
    {
        $information->users()->detach();
    }
}
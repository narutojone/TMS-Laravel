<?php

namespace App\Lists;

use App\Lists\Contracts\Lists;

class InformationStatusesList implements Lists
{
    /**
     * @return array
     */
    public function get()
    {
        return [
            '---' => null,
            'Accepted' => 1,
            'Declined' => 0
        ];
    }
}

<?php

namespace App\Repositories\ClientEditLog;

interface ClientEditLogInterface
{
    /**
     * @param $clientId
     * @param $field
     * @return mixed
     */
    function endLatest($clientId, $field);
}

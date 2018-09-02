<?php

namespace App\Services\Notifications\Contracts;

interface NotificationLoggerInterface
{
    public function write(array $data);
}

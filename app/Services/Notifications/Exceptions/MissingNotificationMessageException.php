<?php

namespace App\Services\Notifications\Exceptions;

class MissingNotificationMessageException extends \Exception
{
    protected $message = 'We do not have any message to sent';
}

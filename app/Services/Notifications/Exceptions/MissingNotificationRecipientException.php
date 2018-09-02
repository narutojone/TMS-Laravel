<?php

namespace App\Services\Notifications\Exceptions;

class MissingNotificationRecipientException extends \Exception
{
    protected $message = 'We do not have any recipient.';
}

<?php

namespace App\Services\Notifications\Exceptions;

class MissingNotificationTemplateException extends \Exception
{
    protected $message = 'We do not have any template to process.';
}

<?php

namespace App\Repositories\ClientEditLog;

use App\Core\Validators\LaravelValidator;
use App\Core\Validators\ValidableInterface;

class ClientEditLogUpdateValidator extends LaravelValidator implements ValidableInterface
{
    /**
     * Validation rules
     *
     * @var array
     */
    protected $rules = [
        'reminder_sent_at' => 'sometimes|date',
    ];

    /**
     * Validation messages
     *
     * @var array
     */
    protected $messages = [];
}

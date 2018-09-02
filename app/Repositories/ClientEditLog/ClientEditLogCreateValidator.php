<?php

namespace App\Repositories\ClientEditLog;

use App\Core\Validators\LaravelValidator;
use App\Core\Validators\ValidableInterface;

class ClientEditLogCreateValidator extends LaravelValidator implements ValidableInterface
{
    /**
     * Validation rules
     *
     * @var array
     */
    protected $rules = [
        'client_id' => 'required|integer|exists:clients,id',
        'user_id'   => 'required|integer|exists:users,id',
        'field'     => 'required',
        'value'     => 'required|boolean',
        'starts_at' => 'required|date',
        'ends_at'   => 'sometimes|date',
    ];

    /**
     * Validation messages
     *
     * @var array
     */
    protected $messages = [];
}

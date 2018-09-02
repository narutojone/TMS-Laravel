<?php

namespace App\Repositories\Client;

use App\Core\Validators\LaravelValidator;
use App\Core\Validators\ValidableInterface;

/**
 * This validator is going to be used before saving into the database when we have a create request
 * 
 * Validation rules that we need to use before saving to the database.
 * We try to keep the data clean and without mistakes.
 * 
 * After a request passes it's request validator than we need to manipulate the data, do something with it.
 * After the manipulation, we need to check that the data that is going to be saved to database is correct
 */
class ClientCreateValidator extends LaravelValidator implements ValidableInterface {

    /**
     * Validation rules
     *
     * @var array
     */
    protected $rules = [
        'manager_id'          => 'required|numeric|exists:users,id',
        'employee_id'         => 'required|numeric|exists:users,id',
        'name'                => 'required|max:255',
        'organization_number' => 'required_if:internal,0|numeric|nullable|digits:9|unique:clients',
        'zendesk_id'          => 'sometimes|nullable|numeric',
        'system_id'           => 'required|numeric|exists:systems,id',
        'type'                => 'sometimes|numeric',
        'paid'                => 'required|boolean',
        'active'              => 'required|boolean',
        'paused'              => 'required|boolean',
        'internal'            => 'required|boolean',
        'show_folders'        => 'required|boolean',
        'risk'                => 'required|boolean',
        'risk_reason'         => 'sometimes|nullable',
        'complaint_case'      => 'required|boolean',
    ];

    /**
     * Validation messages
     *
     * @var array
     */
    protected $messages = [];

}

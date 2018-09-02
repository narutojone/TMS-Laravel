<?php

namespace App\Repositories\User;

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
class UserCreateValidator extends LaravelValidator implements ValidableInterface {

    /**
     * Validation rules
     *
     * @var array
     */
    protected $rules = [
        'name'                      => 'required|max:255',
        'email'                     => 'required|email|max:255|unique:users',
        'country'                   => 'sometimes|nullable|string|max:255',
        'experience'                => 'sometimes|nullable|int',
        'degree'                    => 'sometimes|nullable|string|max:255',
        'invoice_percentage'        => 'sometimes|nullable|between:0,100',
        'phone'                     => 'sometimes|nullable|numeric|unique:users',
        'password'                  => 'required',
        'role'                      => 'required|in:' . User::ROLE_ADMIN . ',' . User::ROLE_EMPLOYEE . ',' . User::ROLE_CUSTOMER_SERVICE,
        'active'                    => 'required|boolean',
        'level'                     => 'required|numeric',
        'api_token'                 => 'required|alpha_num',
        'pf_id'                     => 'sometimes|numeric|nullable',
        'level'                     => 'sometimes|numeric',
        'harvest_id'                => 'sometimes|numeric|nullable',
        'yearly_statement_capacity' => 'sometimes|numeric',
        'customer_capacity'         => 'sometimes|numeric',
    ];

    /**
     * Validation messages
     *
     * @var array
     */
    protected $messages = [];

}

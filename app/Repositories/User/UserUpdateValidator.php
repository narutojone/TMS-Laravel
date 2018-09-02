<?php namespace App\Repositories\User;

use App\Core\Validators\LaravelValidator;
use App\Core\Validators\ValidableInterface;

/**
 * This validator is going to be used before saving into the database when we have a update request
 * 
 * Validation rules that we need to use before saving to the database.
 * We try to keep the data clean and without mistakes.
 * 
 * After a request passes it's request validator than we need to manipulate the data, do something with it.
 * After the manipulation, we need to check that the data that is going to be saved to database is correct
 */
class UserUpdateValidator extends LaravelValidator implements ValidableInterface {

    /**
     * Validation rules
     *
     * @var array
     */
    protected $rules = [
        'name'                      => 'sometimes|max:255',
        'phone'                     => 'sometimes|nullable|integer',
        'email'                     => 'sometimes|email|max:255',
        'country'                   => 'sometimes|nullable|string|max:255',
        'experience'                => 'sometimes|nullable|int',
        'degree'                    => 'sometimes|nullable|string|max:255',
        'invoice_percentage'        => 'sometimes|nullable|between:0,100',
        'pf_id'                     => 'sometimes|nullable|numeric',
        'level'                     => 'sometimes|numeric',
        'password'                  => 'sometimes',
        'yearly_statement_capacity' => 'sometimes|numeric',
        'customer_capacity'         => 'sometimes|numeric',
        'groups'                    => 'sometimes|nullable|array',
        'groups.*'                  => 'integer|exists:groups,id',
    ];

    /**
     * Validation messages
     *
     * @var array
     */
    protected $messages = [];
}

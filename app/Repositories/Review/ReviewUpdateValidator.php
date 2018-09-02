<?php

namespace App\Repositories\Review;

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
class ReviewUpdateValidator extends LaravelValidator implements ValidableInterface {

    /**
     * Validation rules
     *
     * @var array
     */
    protected $rules = [
        'user_id'   => 'sometimes|exists:users,id',
        'status'    => 'sometimes|in:approved,declined,pending',
    ];

    /**
     * Validation messages
     *
     * @var array
     */
    protected $messages = [];

}

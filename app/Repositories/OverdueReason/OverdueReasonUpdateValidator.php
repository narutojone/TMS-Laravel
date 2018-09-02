<?php

namespace App\Repositories\OverdueReason;

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
class OverdueReasonUpdateValidator extends LaravelValidator implements ValidableInterface {

    /**
     * Validation rules
     *
     * @var array
     */
    protected $rules = [
        'reason'                => 'sometimes',
        'description'           => 'required',
        'required'              => 'sometimes|boolean',
        'priority'              => 'sometimes|numeric',
        'visible'               => 'sometimes|boolean',
        'default'               => 'sometimes|boolean',
        'is_visible_in_report'  => 'sometimes|boolean',
        'days'                  => 'sometimes|numeric|min:1',
        'complete_task'         => 'sometimes|boolean',
        'completed_user_id'     => 'sometimes|numeric|nullable|exists:users,id',
    ];

    /**
     * Validation messages
     *
     * @var array
     */
    protected $messages = [];

}

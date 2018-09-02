<?php

namespace App\Repositories\Task;

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
class TaskCreateValidator extends LaravelValidator implements ValidableInterface {

    /**
     * Validation rules
     *
     * @var array
     */
    protected $rules = [
        'template_id'   => 'sometimes|nullable|exists:templates,id',
        'client_id'     => 'required|exists:clients,id',
        'user_id'       => 'sometimes|nullable|exists:users,id',
        'version_no'    => 'required|numeric',
        'created_by'    => 'sometimes|nullable|exists:users,id',
        'category'      => 'required',
        'title'         => 'required',
        'repeating'     => 'required|boolean',
        'frequency'     => 'required_if:repeating,1',
        'deadline'      => 'required|date_format:Y-m-d H:i:s',
        'due_at'        => 'required|date_format:Y-m-d H:i:s',
        'active'        => 'sometimes|boolean',
        'regenerated'   => 'sometimes|boolean',
        'private'       => 'sometimes|boolean',
        'delivered'     => 'sometimes|boolean',
    ];

    /**
     * Validation messages
     *
     * @var array
     */
    protected $messages = [];

}

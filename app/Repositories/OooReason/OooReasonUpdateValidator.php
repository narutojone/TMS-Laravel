<?php

namespace App\Repositories\OooReason;

use App\Core\Validators\LaravelValidator;
use App\Core\Validators\ValidableInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Validation\Factory;

/**
 * This validator is going to be used before saving into the database when we have a update request
 * 
 * Validation rules that we need to use before saving to the database.
 * We try to keep the data clean and without mistakes.
 * 
 * After a request passes it's request validator than we need to manipulate the data, do something with it.
 * After the manipulation, we need to check that the data that is going to be saved to database is correct
 */
class OooReasonUpdateValidator extends LaravelValidator implements ValidableInterface {

    /**
     * Validation rules
     *
     * @var array
     */
    protected $rules = [
        'name'      => 'required|unique:ooo_reasons,name,:id|max:255',
        'default'   => 'sometimes|boolean',
    ];

    /**
     * Validation messages
     *
     * @var array
     */
    protected $messages = [];

    /**
     * @param Factory $validator
     */
    public function __construct(Factory $validator)
    {
        $this->validator = $validator;

        $routeParameters = optional(request()->route())->parameters;
        if (isset($routeParameters['reason'])) {
            foreach ($this->rules as &$rule) {
                $rule = str_replace(':id', $routeParameters['reason'], $rule);
            }
        }
    }

}

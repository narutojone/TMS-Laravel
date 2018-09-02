<?php

namespace App\Repositories\TemplateOverdueReason;

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
class TemplateOverdueReasonCreateValidator extends LaravelValidator implements ValidableInterface {

    /**
     * Validation rules
     *
     * @var array
     */
    protected $rules = [
        'template_id'       => 'required|numeric|exists:templates,id',
        'overdue_reason_id' => 'required|numeric|exists:overdue_reasons,id',
        'trigger_type'      => 'required|in:none,consecutive,total',
        'trigger_counter'   => 'required_unless:trigger_type,none|nullable|numeric|min:1',
        'action'            => 'required_unless:trigger_type,none|nullable|in:hide_reason,pause_client,deactivate_client,remove_employee',
    ];

    /**
     * Validation messages
     *
     * @var array
     */
    protected $messages = [];

}

<?php namespace App\Repositories\TemplateNotification;

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
class TemplateNotificationUpdateValidator extends LaravelValidator implements ValidableInterface {

    /**
     * Validation rules
     *
     * @var array
     */
    protected $rules = [
        'type'          => 'required',
        'template_id'   => 'required:exists,templates,id',
        'user_type'     => 'required|in:client,employee,manager',
        'template'      => 'required_if:type,template',
        'subject'       => 'required_if:type,template,email',
        'message'       => 'required_if:type,email,sms',
        'before'        => 'required',
        'paid'          => 'sometimes|numeric|in:0,1,2',
        'completed'     => 'sometimes|numeric|in:0,1,2',
        'delivered'     => 'sometimes|numeric|in:0,1,2',
        'paused'        => 'sometimes|numeric|in:0,1,2',
    ];

    /**
     * Validation messages
     *
     * @var array
     */
    protected $messages = [];

}

<?php namespace App\Repositories\TemplateNotification;

use App\Http\Requests\Request;

/**
 * Validation rules that are used when we try to create an TemplateNotification via api request
 */
class TemplateNotificationCreateRequest extends Request {

    protected $message = 'Request parameters for TemplateNotification are not valid.';

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */

    public function rules()
    {
        return [
            'type'      => 'required',
            'user_type' => 'required|in:client,employee,manager',
            'template'  => 'required_if:type,template',
            'subject'   => 'required_if:type,template,email',
            'message'   => 'required_if:type,email,sms',
            'before'    => 'required',
            'paid'      => 'sometimes|numeric|in:0,1,2',
            'completed' => 'sometimes|numeric|in:0,1,2',
            'delivered' => 'sometimes|numeric|in:0,1,2',
            'paused'    => 'sometimes|numeric|in:0,1,2',
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }
}

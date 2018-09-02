<?php namespace App\Repositories\GeneratedProcessedNotification;

use App\Http\Requests\Request;

/**
 * Validation rules that are used when we try to create an GeneratedProcessedNotification via api request
 */
class GeneratedProcessedNotificationCreateRequest extends Request {

    protected $message = 'Request parameters for GeneratedProcessedNotification are not valid.';

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */

    public function rules()
    {
        return [
            // Laravel validation rules
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

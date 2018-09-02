<?php namespace App\Repositories\ProcessedNotification;

use App\Http\Requests\Request;

/**
 * Validation rules that are used when we try to create an ProcessedNotification via api request
 */
class ProcessedNotificationApproveDeclineRequest extends Request {

    protected $message = 'Request parameters for ProcessedNotification are not valid.';

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */

    public function rules()
    {
        return [
            'processedNotificationIds' => 'required|array',
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

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'processedNotificationIds.required' => 'Select at least one notification.',
        ];
    }
}

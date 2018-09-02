<?php

namespace App\Repositories\TemplateOverdueReason;

use App\Http\Requests\Request;

/**
 * Validation rules that are used when we try to update an TemplateOverdueReason entity
 */
class TemplateOverdueReasonUpdateRequest extends Request {

    protected $message = 'Request parameters for TemplateOverdueReason are not valid.';

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */

    public function rules()
    {
        return [
            'trigger_counter' => 'sometimes|nullable|numeric|min:1',
            'action'          => 'sometimes|nullable|required_with:trigger|in:hide_reason,pause_client,deactivate_client,remove_employee',
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

<?php

namespace App\Repositories\OverdueReason;

use App\Http\Requests\Request;

/**
 * Validation rules that are used when we try to create an OverdueReason via api request
 */
class OverdueReasonCreateRequest extends Request {

    protected $message = 'Request parameters for OverdueReason are not valid.';

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */

    public function rules()
    {
        return [
            'reason'                => 'required',
            'description'           => 'sometimes|nullable',
            'required'              => 'sometimes|boolean',
            'priority'              => 'sometimes|numeric',
            'visible'               => 'sometimes|boolean',
            'default'               => 'sometimes|boolean',
            'is_visible_in_report'  => 'required|boolean',
            'days'                  => 'required|min:1',
            'complete_task'         => 'sometimes|boolean',
            'completed_user_id'     => 'sometimes|numeric|nullable|exists:users,id',
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

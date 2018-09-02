<?php

namespace App\Repositories\OverdueReason;

use App\Http\Requests\Request;

/**
 * Validation rules that are used when we try to move an OverdueReason via api request
 */
class OverdueReasonMoveRequest extends Request {

    protected $message = 'Request parameters for OverdueReason are not valid.';

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */

    public function rules()
    {
        return [
            'direction' => 'required|in:'.OverdueReason::DIRECTION_UP.','.OverdueReason::DIRECTION_DOWN,
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

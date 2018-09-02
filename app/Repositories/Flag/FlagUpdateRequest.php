<?php

namespace App\Repositories\Flag;

use App\Http\Requests\Request;

/**
 * Validation rules that are used when we try to update a Flag entity via api request
 */
class FlagUpdateRequest extends Request {

    protected $message = 'Request parameters for Flag are not valid.';

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */

    public function rules()
    {
        return [
            'reason'    => 'required',
            'hex'       => 'required',
            'client_specific' => 'sometimes|digits:1',
            'client_removal'  => 'sometimes|digits:1',
            'sms'             => 'max:130',
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

<?php

namespace App\Repositories\OooReason;

use App\Http\Requests\Request;

/**
 * Validation rules that are used when we try to create an OooReason via api request
 */
class OooReasonCreateRequest extends Request {

    protected $message = 'Request parameters for OooReason are not valid.';

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */

    public function rules()
    {
        return [
            'name'      => 'required|unique:ooo_reasons|max:255',
            'default'   => 'sometimes|boolean',

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

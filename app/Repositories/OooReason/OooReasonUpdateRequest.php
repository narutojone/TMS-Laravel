<?php

namespace App\Repositories\OooReason;

use App\Http\Requests\Request;

/**
 * Validation rules that are used when we try to update an OooReason entity via api request
 */
class OooReasonUpdateRequest extends Request {

    protected $message = 'Request parameters for OooReason are not valid.';

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */

    public function rules()
    {
        $reasonId = $this->route()->parameter('reason');

        return [
            'name'      => 'required|unique:ooo_reasons,name,'.$reasonId.'|max:255',
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

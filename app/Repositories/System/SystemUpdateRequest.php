<?php

namespace App\Repositories\System;

use App\Http\Requests\Request;

/**
 * Validation rules that are used when we try to update an System entity via api request
 */
class SystemUpdateRequest extends Request {

    protected $message = 'Request parameters for System are not valid.';

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */

    public function rules()
    {
        return [
            'name'      => 'required|min:3',
            'visible'   => 'required|boolean',
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

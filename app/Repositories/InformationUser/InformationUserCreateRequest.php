<?php

namespace App\Repositories\InformationUser;

use App\Http\Requests\Request;

/**
 * Validation rules that are used when we try to create an InformationUser via api request
 */
class InformationUserCreateRequest extends Request {

    protected $message = 'Request parameters for InformationUser are not valid.';

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */

    public function rules()
    {
        return [
            'information_id' => 'required',
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

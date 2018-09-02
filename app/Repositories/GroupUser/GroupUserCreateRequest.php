<?php

namespace App\Repositories\GroupUser;

use App\Http\Requests\Request;

/**
 * Validation rules that are used when we try to create an GroupUser via api request
 */
class GroupUserCreateRequest extends Request {

    protected $message = 'Request parameters for GroupUser are not valid.';

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */

    public function rules()
    {
        return [
            'user' => 'required',
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

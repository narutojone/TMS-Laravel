<?php

namespace App\Repositories\User;

use App\Http\Requests\Request;

/**
 * Validation rules that are used when we try to create an User via api request
 */
class UserChangePasswordRequest extends Request {

    protected $message = 'Request parameters for User are not valid.';

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */

    public function rules()
    {
        return [
            'current_password'  => 'required',
            'password'          => 'required|min:6|confirmed',
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

<?php

namespace App\Repositories\User;

use App\Http\Requests\Request;

/**
 * Validation rules that are used when we try to create an User via api request
 */
class UserCreateRequest extends Request {

    protected $message = 'Request parameters for User are not valid.';

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */

    public function rules()
    {
        return [
            'name'                      => 'required|max:255',
            'email'                     => 'required|email|max:255|unique:users',
            'phone'                     => 'sometimes|nullable|numeric|unique:users',
            'country'                   => 'sometimes|nullable|string|max:255',
            'experience'                => 'sometimes|nullable|int',
            'degree'                    => 'sometimes|nullable|string|max:255',
            'invoice_percentage'        => 'sometimes|nullable|between:0,100',
            'password'                  => 'required|min:6',
            'r-password'                => 'required|min:6|same:password',
            'role'                      => 'required|in:' . User::ROLE_ADMIN . ',' . User::ROLE_EMPLOYEE . ',' . User::ROLE_CUSTOMER_SERVICE,
            'pf_id'                     => 'sometimes|numeric|nullable',
            'level'                     => 'sometimes|numeric',
            'yearly_statement_capacity' => 'sometimes|numeric',
            'customer_capacity'         => 'sometimes|numeric',
            'harvest_id'                => 'sometimes|integer|nullable',
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

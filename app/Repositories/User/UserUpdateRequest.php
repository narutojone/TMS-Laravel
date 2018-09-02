<?php namespace App\Repositories\User;

use App\Http\Requests\Request;

/**
 * Validation rules that are used when we try to update an User entity via api request
 */
class UserUpdateRequest extends Request {

    protected $message = 'Request parameters for User are not valid.';

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */

    public function rules()
    {
        $user = $this->route()->parameter('user');
            
        return [
            'name'                      => 'sometimes|max:255',
            'phone'                     => 'sometimes|nullable|integer|unique:users,phone,' . $user->id,
            'email'                     => 'sometimes|email|max:255|unique:users,email,' . $user->id,
            'country'                   => 'sometimes|nullable|string|max:255',
            'experience'                => 'sometimes|nullable|int',
            'degree'                    => 'sometimes|nullable|string|max:255',
            'invoice_percentage'        => 'sometimes|nullable|between:0,100',
            'pf_id'                     => 'sometimes|nullable|numeric|unique:users,pf_id,' . $user->id,
            'level'                     => 'sometimes|numeric',
            'password'                  => 'sometimes|min:6',
            'yearly_statement_capacity' => 'sometimes|numeric',
            'customer_capacity'         => 'sometimes|numeric',
            'groups'                    => 'sometimes||nullable|array',
            'groups.*'                  => 'integer|exists:groups,id',
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

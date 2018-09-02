<?php

namespace App\Repositories\ContactPhone;

use App\Http\Requests\Request;

/**
 * Validation rules that are used when we try to update an ContactPhone entity
 */
class ContactPhoneUpdateRequest extends Request {

    protected $message = 'Request parameters for ContactPhone are not valid.';

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */

    public function rules()
    {
        $phone = $this->route()->parameter('phone');

        return [
            'number'     => 'sometimes|numeric|unique:contact_phones,number,'.$phone.'|digits_between:10,13',
            'primary'    => 'sometimes|boolean',
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

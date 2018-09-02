<?php

namespace App\Repositories\Contact;

use App\Http\Requests\Request;

/**
 * Validation rules that are used when we try to create an Contact
 */
class ContactCreateRequest extends Request {

    protected $message = 'Request parameters for Contact are not valid.';

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */

    public function rules()
    {
        return [
            'client_id' => 'required|numeric|exists:clients,id',
            'name'      => 'required|max:255|unique:contacts,name',
            'address'   => 'required|email|unique:contact_emails,address',
            'number'    => 'sometimes|numeric|unique:contact_phones,number|digits_between:10,13',
            'notes'     => 'sometimes|max:255',
            'active'    => 'sometimes|boolean',
            'primary'   => 'required|boolean',
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

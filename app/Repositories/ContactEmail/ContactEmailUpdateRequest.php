<?php

namespace App\Repositories\ContactEmail;

use App\Http\Requests\Request;

/**
 * Validation rules that are used when we try to update an ContactEmail entity
 */
class ContactEmailUpdateRequest extends Request {

    protected $message = 'Request parameters for ContactEmail are not valid.';

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */

    public function rules()
    {
        $email = $this->route()->parameter('email');

        return [
            'address'    => 'required|email|unique:contact_emails,address,'.$email,
            'zendesk_id' => 'sometimes|nullable|max:20',
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

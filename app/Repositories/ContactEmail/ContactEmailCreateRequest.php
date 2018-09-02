<?php

namespace App\Repositories\ContactEmail;

use App\Http\Requests\Request;

/**
 * Validation rules that are used when we try to create an ContactEmail
 */
class ContactEmailCreateRequest extends Request {

    protected $message = 'Request parameters for ContactEmail are not valid.';

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */

    public function rules()
    {
        return [
            'address'    => 'required|email|unique:contact_emails,address',
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

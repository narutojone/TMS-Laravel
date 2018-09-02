<?php

namespace App\Repositories\Contact;

use App\Http\Requests\Request;

/**
 * Validation rules that are used when we try to update an Contact entity
 */
class ContactUpdateRequest extends Request {

    protected $message = 'Request parameters for Contact are not valid.';

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */

    public function rules()
    {
        return [
            'name'   => 'sometimes|max:255',
            'notes'  => 'sometimes|max:255',
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

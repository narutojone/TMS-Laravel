<?php

namespace App\Repositories\Information;

use App\Http\Requests\Request;

/**
 * Validation rules that are used when we try to update an Information entity via api request
 */
class InformationUpdateRequest extends Request {

    protected $message = 'Request parameters for Information are not valid.';

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */

    public function rules()
    {
        return [
            'title'         => 'required',
            'visibility'    => 'required|fit_for_visibility',
            'description'   => 'required',
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

    /**
     * @return array
     */
    public function messages()
    {
        return [
            'visibility.fit_for_visibility' => 'Visibility should be set to valid group or user email.'
        ];
    }
}

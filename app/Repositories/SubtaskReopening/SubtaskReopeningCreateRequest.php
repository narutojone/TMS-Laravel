<?php

namespace App\Repositories\SubtaskReopening;

use App\Http\Requests\Request;

/**
 * Validation rules that are used when we try to create an SubtaskReopening via api request
 */
class SubtaskReopeningCreateRequest extends Request {

    protected $message = 'Request parameters for SubtaskReopening are not valid.';

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */

    public function rules()
    {
        return [
            'user'      => 'required|exists:users,id',
            'reason'    => 'required',
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

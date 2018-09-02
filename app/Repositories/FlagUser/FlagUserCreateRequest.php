<?php

namespace App\Repositories\FlagUser;

use App\Http\Requests\Request;

/**
 * Validation rules that are used when we try to create an FlagUser via api request
 */
class FlagUserCreateRequest extends Request {

    protected $message = 'Request parameters for FlagUser are not valid.';

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */

    public function rules()
    {
        return [
            'flag_id' => 'required|exists:flags,id',
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

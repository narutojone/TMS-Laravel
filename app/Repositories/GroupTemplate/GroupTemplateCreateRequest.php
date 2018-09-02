<?php

namespace App\Repositories\GroupTemplate;

use App\Http\Requests\Request;

/**
 * Validation rules that are used when we try to create an GroupTemplate via api request
 */
class GroupTemplateCreateRequest extends Request {

    protected $message = 'Request parameters for GroupTemplate are not valid.';

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */

    public function rules()
    {
        return [
            'groups' => 'required|array',
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

<?php

namespace App\Repositories\ReviewSettingTemplate;

use App\Http\Requests\Request;

/**
 * Validation rules that are used when we try to create an ReviewSettingTemplate via api request
 */
class ReviewSettingTemplateCreateRequest extends Request {

    protected $message = 'Request parameters for ReviewSettingTemplate are not valid.';

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */

    public function rules()
    {
        return [
            'asd' => 'required'
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

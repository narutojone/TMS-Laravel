<?php

namespace App\Repositories\ReviewSettingTemplate;

use App\Http\Requests\Request;

/**
 * Validation rules that are used when we try to update an ReviewSettingTemplate entity via api request
 */
class ReviewSettingTemplateUpdateRequest extends Request {

    protected $message = 'Request parameters for ReviewSettingTemplate are not valid.';

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */

    public function rules()
    {
        return [
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

<?php

namespace App\Repositories\Template;

use App\Http\Requests\Request;

/**
 * Validation rules that are used when we try to create an Template via api request
 */
class TemplateCreateRequest extends Request {

    protected $message = 'Request parameters for Template are not valid.';

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */

    public function rules()
    {
        return [
            'title'         => 'required|unique:templates|max:255',
            'category'      => 'required|max:255',
            'description'   => 'sometimes|nullable',
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

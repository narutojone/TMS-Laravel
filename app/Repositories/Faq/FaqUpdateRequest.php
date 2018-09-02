<?php

namespace App\Repositories\Faq;

use App\Http\Requests\Request;

/**
 * Validation rules that are used when we try to update an Faq entity via api request
 */
class FaqUpdateRequest extends Request {

    protected $message = 'Request parameters for Faq are not valid.';

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */

    public function rules()
    {
        return [
            'title'         => 'sometimes',
            'content'       => 'sometimes',
            'category'      => 'sometimes',
            'new_category'  => 'required_if:category,add_category',
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

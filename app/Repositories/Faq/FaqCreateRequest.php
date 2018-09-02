<?php

namespace App\Repositories\Faq;

use App\Http\Requests\Request;

/**
 * Validation rules that are used when we try to create an Faq via api request
 */
class FaqCreateRequest extends Request {

    protected $message = 'Request parameters for Faq are not valid.';

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */

    public function rules()
    {
        return [
            'faq_category_id' => 'required',
            'use_template'    => 'required|boolean', // value not saved in DB
            'template_id'     => 'required_if:use_template,1|nullable|numeric|exists:templates,id',
            'title'           => 'required|max:255',
            'content'         => 'required_if:use_template,0',
            'visible'         => 'required|boolean',
            'active'          => 'sometimes|boolean',
            'order'           => 'sometimes|numeric',
            'new_category'    => 'required_if:category,add_category',
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

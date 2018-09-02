<?php

namespace App\Repositories\FaqCategory;

use App\Http\Requests\Request;

/**
 * Validation rules that are used when we try to update an FaqCategory entity via api request
 */
class FaqCategoryUpdateRequest extends Request {

    protected $message = 'Request parameters for FaqCategory are not valid.';

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */

    public function rules()
    {
        return [
            'name' => 'required',
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

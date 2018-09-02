<?php

namespace App\Repositories\Review;

use App\Http\Requests\Request;

/**
 * Validation rules that are used when we try to update an Review entity via api request
 */
class ReviewUpdateRequest extends Request {

    protected $message = 'Request parameters for Review are not valid.';

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */

    public function rules()
    {
        return [
            'critical'  => 'sometimes|boolean',
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

<?php

namespace App\Repositories\Comment;

use App\Http\Requests\Request;

/**
 * Validation rules that are used when we try to update an Comment entity via api request
 */
class CommentUpdateRequest extends Request {

    protected $message = 'Request parameters for Comment are not valid.';

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */

    public function rules()
    {
        return [
            // some validations here
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

<?php

namespace App\Repositories\Comment;

use App\Http\Requests\Request;

/**
 * Validation rules that are used when we try to create an Comment via api request
 */
class CommentCreateRequest extends Request {

    protected $message = 'Request parameters for Comment are not valid.';

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */

    public function rules()
    {
        return [
            'comment' => 'required',
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

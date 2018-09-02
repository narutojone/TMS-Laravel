<?php

namespace App\Repositories\RatingTemplate;

use App\Http\Requests\Request;

/**
 * Validation rules that are used when we try to update an RatingTemplate entity via api request
 */
class RatingTemplateUpdateRequest extends Request {

    protected $message = 'Request parameters for RatingTemplate are not valid.';

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */

    public function rules()
    {
        return [
            'subject'               => 'required|in:client,user',
            'email_template'        => 'required',
            'tasks_completed'       => 'required|numeric',
            'days_from_last_review' => 'required|numeric',
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

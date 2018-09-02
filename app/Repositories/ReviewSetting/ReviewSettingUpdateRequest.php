<?php

namespace App\Repositories\ReviewSetting;

use App\Http\Requests\Request;

/**
 * Validation rules that are used when we try to update an ReviewSetting entity via api request
 */
class ReviewSettingUpdateRequest extends Request {

    protected $message = 'Request parameters for ReviewSetting are not valid.';

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */

    public function rules()
    {
        return [
            'no_of_tasks_for_level_two' =>  'required|numeric',
            'deadline_offset'           =>  'required|numeric',
            'review_template_id'        =>  'required|exists:templates,id',
            'first_review_group_id'     =>  'required|exists:groups,id',
            'second_review_group_id'    =>  'required|exists:groups,id',
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

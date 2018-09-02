<?php namespace App\Repositories\UserCompletedTask;

use App\Http\Requests\Request;

/**
 * Validation rules that are used when we try to update an UserCompletedTask entity via api request
 */
class UserCompletedTaskUpdateRequest extends Request {

    protected $message = 'Request parameters for UserCompletedTask are not valid.';

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

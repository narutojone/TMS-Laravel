<?php namespace App\Repositories\UserCompletedSubtask;

use App\Http\Requests\Request;

/**
 * Validation rules that are used when we try to create an UserCompletedSubtask via api request
 */
class UserCompletedSubtaskCreateRequest extends Request {

    protected $message = 'Request parameters for UserCompletedSubtask are not valid.';

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

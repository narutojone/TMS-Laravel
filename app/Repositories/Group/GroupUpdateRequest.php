<?php namespace App\Repositories\Group;

use App\Http\Requests\Request;

/**
 * Validation rules that are used when we try to update an Group entity via api request
 */
class GroupUpdateRequest extends Request {

    protected $message = 'Request parameters for Group are not valid.';

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */

    public function rules()
    {
        return [
            // laravel vaidation rules
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

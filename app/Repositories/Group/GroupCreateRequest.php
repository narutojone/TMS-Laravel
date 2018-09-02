<?php namespace App\Repositories\Group;

use App\Http\Requests\Request;

/**
 * Validation rules that are used when we try to create an Group via api request
 */
class GroupCreateRequest extends Request {

    protected $message = 'Request parameters for Group are not valid.';

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */

    public function rules()
    {
        return [
            // laravel validation rules
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

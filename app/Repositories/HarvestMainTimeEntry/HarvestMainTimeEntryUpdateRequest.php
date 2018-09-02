<?php namespace App\Repositories\HarvestMainTimeEntry;

use App\Http\Requests\Request;

/**
 * Validation rules that are used when we try to update an Task entity via api request
 */
class HarvestMainTimeEntryUpdateRequest extends Request {

    protected $message = 'Request parameters for Task are not valid.';

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */

    public function rules()
    {
        return [
            // Laravel validation rules
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

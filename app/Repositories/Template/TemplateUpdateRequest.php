<?php namespace App\Repositories\Template;

use App\Http\Requests\Request;

/**
 * Validation rules that are used when we try to update an Template entity via api request
 */
class TemplateUpdateRequest extends Request {

    protected $message = 'Request parameters for Template are not valid.';

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */

    public function rules()
    {
        return [
            'title'         => 'sometimes|max:255',
            'category'      => 'sometimes|max:255',
            'description'   => 'sometimes|nullable',
            'version'       => 'sometimes|boolean',
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

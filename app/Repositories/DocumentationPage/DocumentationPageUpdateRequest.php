<?php

namespace App\Repositories\DocumentationPage;

use App\Http\Requests\Request;

/**
 * Validation rules that are used when we try to update an Documentation Page entity via api request
 */
class DocumentationPageUpdateRequest extends Request
{

    protected $message = 'Request parameters for Documentation Page are not valid.';

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */

    public function rules()
    {
        return [
            'title'          => 'required|max:255',
            'content'        => 'required',
            'order'          => 'required|integer|min:0',
            'parent_page_id' => 'nullable|integer|exists:documentation_pages,id',
        ];
    }

    /**
     * Determine if the documentation page is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }
}

<?php

namespace App\Repositories\Task;

use App\Http\Requests\Request;
use Illuminate\Validation\Rule;

/**
 * Validation rules that are used when we try to update an Task entity via api request
 */
class TaskUpdateRequest extends Request {

    protected $message = 'Request parameters for Task are not valid.';

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */

    public function rules()
    {
        return [
            'title'       => $this->wantsJson() ? 'max:255' : 'required|max:255',
            'description' => 'sometimes|nullable',
            'user'        => [
                Rule::exists('users', 'id')->where('active', true),
            ],
            'repeating'   => 'boolean',
            'frequency'   => 'nullable|required_if:repeating,true|frequency',
            'deadline'    => 'date_format:Y-m-d H:i:s',
            'end_date'    => 'nullable|date_format:Y-m-d',
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

<?php

namespace App\Repositories\Task;

use App\Http\Requests\Request;

/**
 * Validation rules that are used when we try to create an Task via api request
 */
class TaskCreateRequest extends Request {

    protected $message = 'Request parameters for Task are not valid.';

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */

    public function rules()
    {
        return [
            'client_id'   => 'required|numeric|exists:clients,id',
            'template_id' => 'sometimes|nullable|numeric|exists:templates,id',
            'user_id'     => 'required|numeric|exists:users,id',
            'title'       => 'sometimes|string|max:255',
            'repeating'   => 'required|boolean',
            'frequency'   => 'sometimes|nullable|frequency|required_if:repeating,1',
            'deadline'    => 'required|date_format:Y-m-d H:i:s',
            'end_date'    => 'sometimes|nullable|date',
            'private'     => 'sometimes|boolean',
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

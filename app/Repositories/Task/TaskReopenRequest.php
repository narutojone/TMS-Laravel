<?php namespace App\Repositories\Task;

use App\Http\Requests\Request;
use Illuminate\Validation\Rule;

/**
 * Validation rules that are used when we try to create an Task via api request
 */
class TaskReopenRequest extends Request {

    protected $message = 'Request parameters for Task Reopen are not valid.';

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */

    public function rules()
    {
        $taskId = $this->route()->parameter('task');

        return [
            'reason' => 'required',
            'subtasks' => [
                'array',
                Rule::exists('subtasks', 'id')->where(function ($query) use ($taskId) {
                    $query->where('task_id', $taskId);
                }),
            ],
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

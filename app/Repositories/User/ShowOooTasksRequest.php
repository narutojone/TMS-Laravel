<?php namespace App\Repositories\User;

use App\Http\Requests\Request;
use Carbon\Carbon;

/**
 * Validation rules that are used when we try show Ooo Tasks
 */
class ShowOooTasksRequest extends Request {

    protected $message = 'Request parameters for ShowOooTasks are not valid.';

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */

    public function rules()
    {
        return [
            'from'   => 'required|date|after:'.Carbon::now()->subDays(1)->toDateString().'|before:'.Carbon::now()->addWeeks(3)->toDateString(),
            'to'     => 'required|date|after:from',
            'reason' => 'required',
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

<?php

namespace App\Repositories\TemplateOverdueReason;

use App\Http\Requests\Request;

/**
 * Validation rules that are used when we try to create an TemplateOverdueReason
 */
class TemplateOverdueReasonCreateRequest extends Request {

    protected $message = 'Request parameters for TemplateOverdueReason are not valid.';

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */

    public function rules()
    {
        return [
            'template_id'       => 'required|numeric|exists:templates,id',
            'overdue_reason_id' => 'required|numeric|exists:overdue_reasons,id',
            'trigger_type'      => 'required|in:none,consecutive,total',
            'trigger_counter'   => 'required_unless:trigger_type,none|nullable|numeric|min:1',
            'action'            => 'required_unless:trigger_type,none|nullable|required_unless:trigger_type,none|in:hide_reason,pause_client,deactivate_client,remove_employee',
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

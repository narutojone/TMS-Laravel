<?php

namespace App\Repositories\ContractSalaryDay;

use App\Http\Requests\Request;

/**
 * Validation rules that are used when we try to create an ContractSalaryDay
 */
class ContractSalaryDayCreateRequest extends Request {

    protected $message = 'Request parameters for ContractSalaryDay are not valid.';

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */

    public function rules()
    {
        return [
            'contract_id' => 'required|numeric|exists:contracts,id',
            'day'         => 'required|numeric|min:1|max:30',
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

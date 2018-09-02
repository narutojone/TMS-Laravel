<?php

namespace App\Repositories\Contract;

use App\Http\Requests\Request;
use Carbon\Carbon;

/**
 * Validation rules that are used when we try to create an Contract
 */
class ContractCreateRequest extends Request {

    protected $message = 'Request parameters for Contract are not valid.';

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */

    public function rules()
    {
        $maxFinancialStatementsYear = Carbon::now()->year + 2;

        return [
            'client_id'                             => 'required|numeric|exists:clients,id',
            'active'                                => 'sometimes|boolean',
            'start_date'                            => 'required|date',
            'end_date'                              => 'sometimes|nullable|date',
            'one_time'                              => 'required|boolean',
            'under_50_bills'                        => 'required|boolean',
            'shareholder_registry'                  => 'required|boolean',
            'control_client'                        => 'required|boolean',
            'bank_reconciliation'                   => 'required|boolean',
            'bank_reconciliation_date'              => 'required_if:bank_reconciliation,1|nullable|date',
            'bank_reconciliation_frequency_custom'  => 'sometimes|boolean',
            'bank_reconciliation_frequency'         => 'required_if:bank_reconciliation_frequency_custom,1|frequency|contract_frequency|nullable',
            'bookkeeping'                           => 'required|boolean',
            'bookkeeping_date'                      => 'required_if:bookkeeping,1|nullable|date',
            'bookkeeping_frequency_custom'          => 'sometimes|boolean',
            'bookkeeping_frequency_1'               => 'required_if:bookkeeping_frequency_custom,1|frequency|contract_frequency|nullable',
            'bookkeeping_frequency_2'               => 'required_if:bookkeeping_frequency_custom,1|frequency|contract_frequency|nullable',
            'mva'                                   => 'required|boolean',
            'mva_type'                              => 'required_if:mva,1|nullable|in:'.Contract::MVA_TYPE_TERM.','.Contract::MVA_TYPE_YEARLY,
            'financial_statements'                  => 'required|boolean',
            'financial_statements_year'             => 'required_if:financial_statements,1|nullable|digits:4|integer|max:'.$maxFinancialStatementsYear,
            'salary_check'                          => 'required|boolean',
            'salary'                                => 'required|boolean',
            'salary_day'                            => 'required_if:salary,1|nullable|array',
            'salary_day.*'                          => 'integer|min:1|max:30',
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

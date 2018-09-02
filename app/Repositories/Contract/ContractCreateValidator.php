<?php

namespace App\Repositories\Contract;

use App\Core\Validators\LaravelValidator;
use App\Core\Validators\ValidableInterface;

/**
 * This validator is going to be used before saving into the database when we have a create request
 * 
 * Validation rules that we need to use before saving to the database.
 * We try to keep the data clean and without mistakes.
 * 
 * After a request passes it's request validator than we need to manipulate the data, do something with it.
 * After the manipulation, we need to check that the data that is going to be saved to database is correct
 */
class ContractCreateValidator extends LaravelValidator implements ValidableInterface {

    /**
     * Validation rules
     *
     * @var array
     */
    protected $rules = [
        'client_id'                     => 'required|numeric|exists:clients,id',
        'active'                        => 'required|boolean',
        'start_date'                    => 'required|date',
        'end_date'                      => 'sometimes|nullable|date',
        'one_time'                      => 'required|boolean',
        'under_50_bills'                => 'required|boolean',
        'shareholder_registry'          => 'required|boolean',
        'control_client'                => 'required|boolean',
        'bank_reconciliation'           => 'required|boolean',
        'bank_reconciliation_date'      => 'required_if:bank_reconciliation,1|nullable|date',
        'bank_reconciliation_frequency' => 'required|frequency|contract_frequency',
        'bookkeeping'                   => 'required|boolean',
        'bookkeeping_date'              => 'required_if:bookkeeping,1|nullable|date',
        'bookkeeping_frequency_1'       => 'required|frequency|contract_frequency',
        'bookkeeping_frequency_2'       => 'required|frequency|contract_frequency',
        'mva'                           => 'required|boolean',
        'mva_type'                      => 'required_if:mva,1|nullable|in:'.Contract::MVA_TYPE_TERM.','.Contract::MVA_TYPE_YEARLY,
        'financial_statements'          => 'required|boolean',
        'financial_statements_year'     => 'required_if:financial_statements,1|nullable|digits:4|integer',
        'salary_check'                  => 'required|boolean',
        'salary'                        => 'required|boolean',
        'created_by'                    => 'required|numeric|exists:users,id',
    ];

    /**
     * Validation messages
     *
     * @var array
     */
    protected $messages = [];

}

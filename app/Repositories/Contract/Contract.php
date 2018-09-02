<?php

namespace App\Repositories\Contract;

use App\Repositories\Client\Client;
use App\Repositories\ContractSalaryDay\ContractSalaryDay;
use App\Repositories\User\User;
use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    const ACTIVE = 1;
    const NOT_ACTIVE = 0;

    const MVA_TYPE_UNKNOWN = 0;
    const MVA_TYPE_TERM = 1;
    const MVA_TYPE_YEARLY = 2;

    public static  $mvaTypes = [
        self::MVA_TYPE_UNKNOWN => 'Unknown',
        self::MVA_TYPE_TERM => 'Term',
        self::MVA_TYPE_YEARLY => 'Yearly',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'client_id',
        'active',
        'start_date',
        'end_date',
        'one_time',
        'under_50_bills',
        'accounting_software',
        'shareholder_registry',
        'control_client',
        'bank_reconciliation',
        'bank_reconciliation_date',
        'bank_reconciliation_frequency',
        'bookkeeping',
        'bookkeeping_date',
        'bookkeeping_frequency_1',
        'bookkeeping_frequency_2',
        'mva',
        'mva_type',
        'financial_statements',
        'financial_statements_year',
        'salary_check',
        'salary',
        'created_by',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
    ];

    /**
     * The client for which the contract is created for
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    /**
     * User that created the contract
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function author()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Salary days for a contract
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function salaryDays()
    {
        return $this->hasMany(ContractSalaryDay::class, 'contract_id');
    }
}

<?php

namespace App\Repositories\ContractSalaryDay;

use App\Repositories\Contract\Contract;
use Illuminate\Database\Eloquent\Model;

class ContractSalaryDay extends Model
{
    protected $table = 'contract_salary_days';

    const DAY_END = 29;
    const DAY_MAN = 30;

    public static $specialSalaryDays = [
        self::DAY_END => 'END',
        self::DAY_MAN => 'MAN',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'contract_id',
        'day',
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
     * The contract for which the salary day is created for
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function contract()
    {
        return $this->belongsTo(Contract::class, 'contract_id');
    }
}

<?php
 
namespace App\Repositories\ContractSalaryDay;

use App\Repositories\BaseEloquentRepository;
use Illuminate\Validation\ValidationException;

/**
 * The eloquent element in a repository should contain all data manipulation related to a entity
 */
class EloquentRepositoryContractSalaryDay extends BaseEloquentRepository implements ContractSalaryDayInterface
{
    /**
     * @var $model
     */
    protected $model;

    /**
     * EloquentRepositoryContractSalaryDay constructor.
     *
     * @param ContractSalaryDay $model
     */
    public function __construct(ContractSalaryDay $model)
    {
        parent::__construct();
        
        $this->model = $model;
    }
    
    /**
     * Create a new Contract salary day.
     *
     * @param array $input
     *
     * @return ContractSalaryDay
     * @throws ValidationException
     */
    public function create(array $input)
    {
        $input = $this->prepareCreateData($input);

        if(!$this->isValid('create', $input)) {
            throw new ValidationException($this->validators['create']);
        }

        // Create the new contract salary day
        $contractSalaryDay = $this->model->create($input);
        return $contractSalaryDay;
    }

    /**
     * Prepare data for insert action
     *
     * @param array $input
     * @return array
     */
    protected function prepareCreateData(array $input)
    {
        return $input;
    }

    /**
     * Generate all possible options for salary days
     *
     * @return array
     */
    public function generateAllSalaryDays() : array
    {
        $salaryDays = [];

        // From 1st to 28th of the month we save the actual day
        for ($i=1; $i<=28 ; $i++) {
            $salaryDays[$i] = $i;
        }

        // Special cases for salary days
        $salaryDays += ContractSalaryDay::$specialSalaryDays;

        return $salaryDays;
    }
}
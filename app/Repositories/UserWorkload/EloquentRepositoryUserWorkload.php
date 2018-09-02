<?php
 
namespace App\Repositories\UserWorkload;

use App\Repositories\BaseEloquentRepository;
use App\Repositories\User\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * The eloquent element in a repository should contain all data manipulation related to a entity
 */
class EloquentRepositoryUserWorkload extends BaseEloquentRepository implements UserWorkloadInterface
{
    /**
     * @var $model
     */
    protected $model;

    /**
     * EloquentRepositoryUserWorkload constructor.
     *
     * @param UserWorkload $model
     */
    public function __construct(UserWorkload $model)
    {
        parent::__construct();

        $this->model = $model;
    }

    /**
     * Create a new UserWorkload
     *
     * @param array $input
     *
     * @return UserWorkload
     * @throws ValidationException
     */
    public function create(array $input)
    {
        $input = $this->prepareCreateData($input);

        if(!$this->isValid('create', $input)) {
            throw new ValidationException($this->validators['create']);
        }

        return $this->model->create($input);
    }

    public function getWorkload(User $user, int $numberOfMonths = 4)
    {
        $userWorkloadMonthsModels = $user->workload($numberOfMonths)->get();

        $userWorkloadMonths = [];
        foreach ($userWorkloadMonthsModels as $userWorkloadMonthsModel) {
            $key = $userWorkloadMonthsModel->year . '-' . str_pad($userWorkloadMonthsModel->month, 2, '0', STR_PAD_LEFT);
            $userWorkloadMonths[$key] = [
                'hours'  => $userWorkloadMonthsModel->hours ? $userWorkloadMonthsModel->hours : 0,
                'locked' => $userWorkloadMonthsModel->locked,
            ];
        }

        // Prepare view data for the next months
        for ($i=0; $i < $numberOfMonths; $i++) {
            $key = Carbon::now()->addMonth($i)->format('Y-m');

            if(!array_key_exists($key, $userWorkloadMonths)) {
                $userWorkloadMonths[$key] = [
                    'hours'  => 0,
                    'locked' => false,
                ];
            }
        }

        // use 'ksort' to have dates in ascending order
        ksort($userWorkloadMonths);

        // $userWorkloadMonths can contain further dates which are not in our scope, that's why we only first only the first elements
        array_splice($userWorkloadMonths, $numberOfMonths);

        return $userWorkloadMonths;
    }


    public function updateAll(User $user, array $input)
    {
        $input = $this->prepareUpdateData($input);

        DB::beginTransaction();

        try {
            foreach ($input as $workloadDate => $workloadData) {
                $date = Carbon::parse($workloadDate);
                $year = $date->format('Y');
                $month = $date->format('m');

                $workloadModel = $this->model->firstOrNew([
                    'user_id' => $user->id,
                    'year'    => $year,
                    'month'   => $month,
                ]);
                $workloadModel->hours = $workloadData['hours'] ? $workloadData['hours']: 0;
                $workloadModel->locked = $workloadData['locked'];
                $workloadModel->save();
            }
        }
        catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }

        DB::commit();
    }

    /**
     * Delete a UserWorkload
     * Currently not supported
     *
     * @param $id
     * @return void
     * @throws \Exception
     */
    public function delete($id)
    {

    }

    /**
     * Prepare data for create action
     *
     * @param $input
     * @return array
     */
    protected function prepareCreateData(array $input) : array
    {
        return $input;
    }

    /**
     * Prepare data for update action
     *
     * @param $input
     * @return array
     */
    protected function prepareUpdateData(array $input) : array
    {
        return $input;
    }
}
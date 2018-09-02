<?php
 
namespace App\Repositories\User;

use App\Repositories\BaseEloquentRepository;
use App\Repositories\Client\Client;
use App\Repositories\Client\ClientInterface;
use App\Repositories\ClientEmployeeLog\ClientEmployeeLogInterface;
use App\Repositories\ReviewSetting\ReviewSettingInterface;
use App\Repositories\UserCompletedTask\UserCompletedTask;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Hash;
use App\Repositories\ClientPhone\ClientPhone;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Carbon\Carbon;
use Exception;

/**
 * THe eloquent element in a repository should contain all data manipulation related to a enetity
 */
class EloquentRepositoryUser extends BaseEloquentRepository implements UserInterface
{
    /**
     * @var $model
     */
    protected $model;

    /**
     * EloquentRepositoryUser constructor.
     *
     * @param User $model
     */
    public function __construct(User $model)
    {
        $this->model = $model;
    }

    /**
     * Create a new User.
     *
     * @param array $input
     * @return User
     * @throws ValidationException
     * @throws \Exception
     */
    public function create(array $input)
    {
        DB::beginTransaction();

        $input = $this->prepareCreateData($input);
        
        if(!$this->isValid('create', $input)) {
            throw new ValidationException($this->validators['create']);
        }

        try {
            // Create user
            $user = $this->model->create($input);

            // Create internal client. Each newly created user must have it's own internal client.
            $clientRepository = app()->make(ClientInterface::class);
            $client = $clientRepository->create([
                'name'         => $user->name,
                'employee_id'  => $user->id,
                'manager_id'   => 8, // Wenche Skatt
                'system_id'    => 4,
                'internal'     => Client::IS_INTERNAL,
                'show_folders' => 0,
            ]);

            // Add client ID for internal project.
            $this->update($user->id, [
                'client_id' => $client->id,
            ]);
        }
        catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
        
        DB::commit();
        return $user;
    }

    /**
     * Update a User.
     *
     * @param integer $id
     * @param array $input
     * @return User
     * @throws Exception
     * @throws ValidationException
     */
    public function update($id, array $input)
    {
        $input = $this->prepareUpdateData($id, $input);

        if(!$this->isValid('update', $input)) {
            throw new ValidationException($this->validators['update']);
        }

        $user = $this->find($id);
        if ($user) {
            try {
                DB::beginTransaction();
                $user->fill($input);
                $user->save();

                // Update user groups
                if(isset($input['groups'])) {
                    $this->syncUserGroups($user, $input['groups']);
                }

                $user->customerTypes()->sync($input['customer_types']);
                $user->systems()->sync($input['systems']);
                $user->taskTypes()->sync($input['task_types']);

                DB::commit();
                return $user;

            } catch (Exception $e) {
                DB::rollback();
                throw $e;
            }
        }
        
        throw new ModelNotFoundException('Model User not found', 404);
    }

    /**
     * Delete a User.
     *
     * @param integer $id
     *
     * @return void
     */
    public function delete($id)
    {
        $user = $this->model->find($id);
        if (!$user) {
            throw new ModelNotFoundException('Model User not found.', 404);
        }
        $user->delete();
    }

    /**
     * Prepare data to be manipulated/validated.
     * 
     * @param array $input - data to work with
     * 
     * @return array
     */
    private function prepareCreateData(array $input) 
    {
        $input['active'] = 1;
        $input['api_token'] = str_random(40);
        $input['password'] = Hash::make($input['password']);
        
        if(!isset($input['level'])) {
            $input['level'] = 0;
        }

        return $input;
    }

    /**
     * Prepare data to be manipulated/validated.
     *
     * @param $id
     * @param array $input - data to work with
     * @return array
     */
    private function prepareUpdateData($id, array $input)
    {
        $initialUser = $this->find($id);

        if (isset($input['password']) && !empty($input['password'])) {
            $input['password'] = Hash::make($input['password']);
        }

        $input['customer_types'] = isset($input['customer_types']) ? $input['customer_types'] : [];
        $input['systems'] = isset($input['systems']) ? $input['systems'] : [];
        $input['task_types'] = isset($input['task_types']) ? $input['task_types'] : [];
        $input['authorized'] = (isset($input['authorized']) && $input['authorized'] == 'on') ? 1 : 0;
        $input['yearly_statement_capacity'] = isset($input['yearly_statement_capacity']) ? $input['yearly_statement_capacity'] : $initialUser->yearly_statement_capacity;
        $input['weekly_capacity'] = isset($input['weekly_capacity']) ? $input['weekly_capacity'] : $initialUser->weekly_capacity;

        if (!$initialUser->authorized && !$input['authorized']) {
            $input['yearly_statement_capacity'] = 0;
        }

        return $input;
    }

    /**
     * Activate user
     *
     * @param int $id - user id to be activated
     *
     * @return User
     * @throws ValidationException
     */
    public function activate($id)
    {
        return $this->update($id, ['active' => true]);
    }

    /**
     * Deactive a user.
     *
     * @param User $user
     * @param array $input
     * @param int $requestUserId
     * 
     * @return \Illuminate\Http\Response
     * @throws ValidationException
     */
    public function deactivate(User $user, array $input, int $requestUserId)
    {
        if ($user->id == $requestUserId) {
            throw ValidationException::withMessages([
                'error' => 'You may not deactivate yourself.',
             ]);
        }
        
        DB::beginTransaction();
        
        $this->unassignActiveTasks($user);
        $this->logEmployeeForRemoval($user);
        $this->removeUserFromManagerOrEmployeePositions($user);

        $user->active = false;
        $user->pf_id = null;

        $userData = $user->toArray();

        if(!$this->isValid('update', $userData)) {
            throw new ValidationException($this->validators['update']);
        }
        $user->save();
        
        DB::commit();

        return $user;
    }

    /**
     * Remove the user from all manager or employee positions
     *
     * @param User $user
     * @return void
     */
    private function removeUserFromManagerOrEmployeePositions(User $user)
    {
        $user->clientsManaging(false)->update(['manager_id' => null]);
        $user->clients(false)->update(['employee_id' => null]);
    }

    /**
     * Unassign all the active tasks assigned to the user
     *
     * @param User $user
     * @return void
     */
    private function unassignActiveTasks(User $user)
    {
        $user->tasks(false)->uncompleted()->update([
            'user_id' => null,
        ]);
    }

    /**
     * Log employee for removal
     *
     * @param User $user
     * @return void
     */
    private function logEmployeeForRemoval(User $user)
    {
        $clientEmployeeLogRepository = app()->make(ClientEmployeeLogInterface::class);

        foreach ($user->clients(false)->get() as $client){
            // Check if log does not have entry from before and add it
            $log = $client->employeeLogs()->where('user_id', $client->employee_id)->first();
            if (!$log) {
                $log = $clientEmployeeLogRepository->create([
                    'user_id' => $client->employee_id,
                    'client_id' => $client->id,
                ]);
            }

            $clientEmployeeLogRepository->update($log->id, [
                'rating'     => null,
                'removed_at' => Carbon::now(),
            ]);
        
            // Create new log for current assigned user
            $clientEmployeeLogRepository->create([
                'client_id' => $client->id,
                'user_id' => null,
                'assigned_at' => Carbon::now(),
            ]);
        }
    }

    /**
     * Reset a user flag
     *
     * @param User $user
     * @return Flag
     */
    public function resetFlag(User $user)
    {
        $lastFlag = $user->lastFlag();
        $lastFlag->pivot->active = 0;
        $lastFlag->pivot->save();

        return $lastFlag;
    }

    public function getValidUsersForGroupAdd(int $groupId)
    {
        // Retrieve all valid users
        return User::active()
            ->whereNotIn('id',
                DB::table('group_user')
                    ->select(DB::raw('`user_id` as `id`'))
                    ->where('group_id', $groupId)
            )->orderBy('name')->get();
    }

    /**
     * Change password for a user
     *
     * @param $user
     * @param $currentInsertedPassword
     * @param $newInsertedPassword
     *
     * @return User
     * @throws ValidationException
     */
    public function changePassword($user, $currentInsertedPassword, $newInsertedPassword) : User
    {
        if (!Hash::check($currentInsertedPassword, $user->password)) {
            throw ValidationException::withMessages([
                'error' => 'The current password you entered is incorrect.',
            ]);
        }

        $user->update([
            'password' => Hash::make($newInsertedPassword),
        ]);

        return $user;
    }


    /**
     * Check if a user need it's level to be increased and increase if it is the case
     *
     * @param $userId
     * @return bool
     * @throws ValidationException
     */
    public function increaseUserLevel($userId) : bool
    {
        $reviewSettingsRepository = app()->make(ReviewSettingInterface::class);
        $reviewSetting = $reviewSettingsRepository->model()->first();

        $user = $this->find($userId);

        if ($this->getNoOfApprovedTaskReviews($user) == $reviewSetting->no_of_tasks_for_level_two) {
            $data = $user->toArray();
            $data['level'] =  $data['level'] + 1;

            $user = $this->update($data);
            return true;
        }

        return false;
    }

    /**
     * Get no of approved tasks for a user
     * @param User $user
     * @return int
     */
    public function getNoOfApprovedTaskReviews(User $user) : int
    {
        $approvedTasks = 0;

        foreach ($user->completedTasksForReviewForCurrentLevel as $completedTask) {
            if ($completedTask->status == UserCompletedTask::STATUS_APPROVED) {
                $approvedTasks++;
            }
        }

        return $approvedTasks;
    }

    /**
     * Return the list of active users.
     * Get the scope active of the model User.
     *
     * @return mixed
     */
    public function activeUsers() : Collection
    {
        return $this->model->active()->get();
    }

    /**
     * Update groups for a user
     *
     * @param User $user
     * @param array $newUserGroups
     */
    protected function syncUserGroups(User $user, array $newUserGroups)
    {
        $user->groups()->sync($newUserGroups);
    }
}

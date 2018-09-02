<?php

namespace App\Policies;

use App\Frequency;
use App\Repositories\Client\Client;
use App\Repositories\User\User;
use App\Repositories\Task\Task;
use Illuminate\Auth\Access\HandlesAuthorization;

class TaskPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the task.
     *
     * @param  \App\Repositories\User\User  $user
     * @param  \App\Repositories\Task\Task  $task
     * @return mixed
     */
    public function view(User $user, Task $task)
    {
        return $user->can('view', $task->client);
    }

    /**
     * Determine whether the user can create tasks.
     *
     * @param  \App\Repositories\User\User $user
     * @param Client $client
     * @return mixed
     */
    public function create(User $user, Client $client)
    {
        if(!$client->active && !$user->isAdmin()) {
            return false;
        }

        return true;
    }

    /**
     * Determine whether the user can create custom tasks (without template).
     *
     * @param  \App\Repositories\User\User $user
     * @param Client $client
     * @return mixed
     */
    public function createCustom(User $user, Client $client)
    {
        return true;
    }



    /**
     * Determine whether the user can update the task.
     *
     * @param  \App\Repositories\User\User  $user
     * @param  \App\Repositories\Task\Task  $task
     * @return mixed
     */
    public function update(User $user, Task $task)
    {
        // Completed tasks can't be updated (no matter what)
        if ($task->isComplete()) {
            return false;
        }

        if ($user->hasRole(User::ROLE_ADMIN)) {
            return true;
        }

        if($task->isCustom()) {
            if($task->author) {
                if($task->author->id == $user->id) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Determine whether the user can delete the task.
     *
     * @param  \App\Repositories\User\User  $user
     * @param  \App\Repositories\Task\Task  $task
     * @return mixed
     */
    public function delete(User $user, Task $task)
    {
        // Completed tasks can't be deleted (no matter what)
        if ($task->isComplete()) {
            return false;
        }

        if ($user->hasRole(User::ROLE_ADMIN)) {
            return true;
        }

        if($task->isCustom()) {
            if($task->author) {
                if($task->author->id == $user->id) {
                    return true;
                }
            }
        }
        
        return false;
    }

    /**
     * Determine whether the user can mark the task as completed.
     *
     * @param  \App\Repositories\User\User  $user
     * @param  \App\Repositories\Task\Task  $task
     * @return mixed
     */
    public function complete(User $user, Task $task)
    {
        // Completed tasks can't be completed again
        if ($task->isComplete()) {
            return false;
        }
        
        // All subtasks must be completed
        if ($task->activeSubtasks->count() > 0) {
            return false;
        }
        
        // Do not allow to complete a task if client don't exist
        if (is_null($task->client)) {
            return false;
        }

        // Tasks without a user can't be completed
        if (!$task->user) {
            return false;
        }

        // Let administrators complete tasks
        if ($user->hasRole(User::ROLE_ADMIN)) {
            return true;
        }

        // Not active tasks can't be completed
        if (! $task->active) {
            return false;
        }

        // Let managers complete tasks under their clients
        if ($task->client->manager_id == $user->id) {
            return true;
        }

        // Let users complete their own tasks
        return $user->id == $task->user_id;
    }

	/**
	 * Determine whether the user can regenerate an existing task.
	 *
	 * @param  \App\Repositories\User\User  $user
	 * @param  \App\Repositories\Task\Task  $task
	 * @return boolean
	 */

	public function regenerate(User $user, Task $task)
	{
		// Completed tasks can't be regenerated
		if ($task->isComplete()) {
			return false;
		}

		// Tasks which have been reopened can't be regenerated
		if($task->reopenings->count() > 0) {
			return false;
		}

		// A task can be regenerated once
		if($task->regenerated) {
			return false;
		}

        // Not active tasks can't be regenerated
        if (! $task->active) {
            return false;
        }

        // A task can't be regenerated if not repeating
        if (! $task->repeating) {
            return false;
        }

        // Custom tasks can't be regenerated
        if ($task->template_id == NULL) {
            return false;
        }

        // A task can't be regenerated if next date is over end date
        if ($task->end_date <= (new Frequency($task->frequency))->next($task->deadline) && ! is_null($task->end_date)){
            return false;
        }

		return true;
	}
}

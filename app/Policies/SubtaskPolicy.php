<?php

namespace App\Policies;

use App\Repositories\User\User;
use App\Repositories\Subtask\Subtask;
use Illuminate\Auth\Access\HandlesAuthorization;

class SubtaskPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the subtask.
     *
     * @param  \App\Repositories\User\User  $user
     * @param  \App\Repositories\Subtask\Subtask  $subtask
     * @return mixed
     */
    public function view(User $user, Subtask $subtask)
    {
        return $user->can('view', $subtask->task);
    }

    /**
     * Determine whether the user can create subtasks.
     *
     * @param  \App\Repositories\User\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the subtask.
     *
     * @param  \App\Repositories\User\User  $user
     * @param  \App\Repositories\Subtask\Subtask  $subtask
     * @return mixed
     */
    public function update(User $user, Subtask $subtask)
    {
        return $user->hasRole(User::ROLE_ADMIN);
    }

    /**
     * Determine whether the user can delete the subtask.
     *
     * @param  \App\Repositories\User\User  $user
     * @param  \App\Repositories\Subtask\Subtask  $subtask
     * @return mixed
     */
    public function delete(User $user, Subtask $subtask)
    {
        //
    }

    /**
     * Determine whether the user can mark the subtask as completed.
     *
     * @param  \App\Repositories\User\User  $user
     * @param  \App\Repositories\Subtask\Subtask  $subtask
     * @return mixed
     */
    public function complete(User $user, Subtask $subtask)
    {
        // Completed tasks can't be completed again
        if ($subtask->isComplete()) {
            return false;
        }

        // Tasks without a user can't be completed
        if (!$subtask->task->user) {
            return false;
        }

        // Let administators complete subtasks
        if ($user->hasRole(User::ROLE_ADMIN)) {
            return true;
        }

        // Not active subtasks can't be completed
        if (! $subtask->task->active) {
            return false;
        }

        // Let managers complete subtasks under their clients
        if ($subtask->task->client->manager_id == $user->id) {
            return true;
        }

        // Let users complete their own tasks
        return $user->id == $subtask->task->user_id;
    }

    /**
     * Determine whether the user can reopen the subtask.
     *
     * @param  \App\Repositories\User\User  $user
     * @param  \App\Repositories\Subtask\Subtask  $subtask
     * @return mixed
     */
    public function reopen(User $user, Subtask $subtask)
    {
        // The subtask must be completed
        if (!$subtask->isComplete()) {
            return false;
        }

        // Can't reopen if client is not active
        if(!$subtask->task->client->active) {
            return false;
        }

        // The parent task can't be completed
        if ($subtask->task->isComplete()) {
            return false;
        }

        // Users who can view the task can also reopen it
        return $user->can('view', $subtask);
    }
}

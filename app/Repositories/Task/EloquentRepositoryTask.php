<?php
 
namespace App\Repositories\Task;

use App\Frequency;
use App\Repositories\BaseEloquentRepository;
use App\Repositories\OverdueReason\OverdueReasonInterface;
use App\Repositories\ProcessedNotification\ProcessedNotification;
use App\Repositories\ProcessedNotification\ProcessedNotificationInterface;
use App\Repositories\Subtask\SubtaskInterface;
use App\Repositories\TaskDetails\TaskDetailsInterface;
use App\Repositories\TaskOverdueReason\TaskOverdueReasonInterface;
use App\Repositories\Option\OptionInterface;
use App\Repositories\Template\Template;
use App\Repositories\Template\TemplateInterface;
use App\Repositories\User\UserInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Repositories\SubtaskReopening\SubtaskReopeningInterface;
use App\Repositories\TaskReopening\TaskReopeningInterface;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;
use App\Repositories\User\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * THe eloquent element in a repository should contain all data manipulation related to a enetity
 */
class EloquentRepositoryTask extends BaseEloquentRepository implements TaskInterface
{
    /**
     * @var $model
     */
    protected $model;

    /**
     * @var Template $template
     */
    protected $template = null;

    /**
     * EloquentRepositoryTask constructor.
     *
     * @param Task $model
     */
    public function __construct(Task $model)
    {
        parent::__construct();
        
        $this->model = $model;
    }
    
    /**
     * Create a new Task.
     *
     * @param array $input
     *
     * @return Task
     * @throws ValidationException
     */
    public function create(array $input)
    {
        $input = $this->prepareCreateData($input);

        DB::beginTransaction();

        if(!$this->isValid('create', $input)) {
            throw new ValidationException($this->validators['create']);
        }

        $task = $this->model->create($input);

        if(!$task->isCustom()){
            $subtaskRepository = app()->make(SubtaskInterface::class);
            // Copy all the subtasks from the template to the task
            foreach ($this->template->subtasks()->where('active', 1)->get() as $subtask) {
                $subtask = $subtaskRepository->create([
                    'task_id'           => $task->id,
                    'order'             => $subtask->order,
                    'title'             => $subtask->title,
                    'version_no'        => $subtask->versions->first()->version_no,
                    'subtaskTemplateId' => $subtask->id,
                    'user_id'           => $task->user_id,
                ]);
            }
        }
        else { // Custom task
            // Create task details
            $taskDetailsRepository = app()->make(TaskDetailsInterface::class);
            $taskDetailsRepository->create([
                'task_id'     => $task->id,
                'description' => $input['description'],
            ]);
        }

        // Give the task an overdue reason assigned from system if it's overdue
        if($task->isOverdue()){
            // Get overdue reason for client move
            $optionRepository = app()->make(OptionInterface::class);
            $overdueReason = $optionRepository->model()->where('key', '=', 'overdue_reason_backdated_task')->first()->overdueReason;

            // Check if we find the overdue reason
            if($overdueReason){
                $this->createOverdue($task, [
                    'reason'    => $overdueReason->id,
                    'user_id'   => null,
                    'comment'   => 'This overdue reason was automatically added to this task. (The task was overdue when the reason was made)',
                ]);
            }
        }

        DB::commit();

        return $task;
    }
 
    /**
     * Update a Task.
     *
     * @param integer $id
     * @param array $input
     *
     * @return Task
     * @throws ValidationException
     * @throws ModelNotFoundException
     */
    public function update($id, array $input)
    {
        $input = $this->prepareUpdateData($id, $input);

        if(!$this->isValid('update', $input)) {
            throw new ValidationException($this->validators['update']);
        }

        $task = $this->find($id);
        if ($task) {
            $task->fill($input);

            try {
                DB::beginTransaction();
                $task->save();

                // Update description for custom tasks
                if ($task->isCustom() && isset($input['description'])) {
                    $taskDetailsRepository = app()->make(TaskDetailsInterface::class);
                    $taskDetailsRepository->update($task->details->id, [
                        'description' => $input['description'],
                    ]);
                }
                DB::commit();
            }
            catch( \Exception $e) {
                DB::rollback();
                throw $e;
            }

            return $task;
        }
        
        throw new ModelNotFoundException('Model Task not found', 404);
    }

    /**
     * Delete a Task.
     *
     * @param integer $id
     *
     * @return void
     */
    public function delete($id)
    {
        DB::beginTransaction();

        $task = $this->model->find($id);
        if (!$task) {
            throw new ModelNotFoundException('Model Task not found', 404);
        }

        $subtaskRepository = app()->make(SubtaskInterface::class);
        foreach ($task->subtasks as $subtask) {
            $subtaskRepository->delete($subtask->id);
        }

        $processedNotificationRepository = app()->make(ProcessedNotificationInterface::class);
        $processedNotificationsNotSent = $task->processedNotifications()->where("sent", "=", ProcessedNotification::IS_NOT_SENT)->get();

        foreach ($processedNotificationsNotSent as $processedNotificationNotSent) {
            $processedNotificationRepository->delete($processedNotificationNotSent->id);
        }

        $task->delete();
        DB::commit();
    }

    /**
     * Get tasks that will become overdue for a certain user in a given period
     *
     * @param User $user
     * @param string $fromDate - Y-m-d
     * @param string $toDate - Y-m-d
     * @return void
     */
    public function getTasksThatWillBecomeOverdue(User $user, $fromDate, $toDate)
    {
        // 1. Check tasks that don't have an overdue reason and will become overdue during the OOO period
        // 2. Check tasks that already have an overdue reason right now but that reason will expire during OOO period

        // Fetch all tasks that will become overdue in that period
        $tasks = $this->model->with(['overdueReason'])
            ->active()->uncompleted()
            ->where('user_id', $user->id)
            ->whereBetween('deadline', [$fromDate, $toDate])
            ->orderBy('deadline', 'ASC')
            ->get();

        // Exclude tasks that will still have an overdue reason in that period
        foreach($tasks as $id => $task)
        {
            if($task->overdueReason) {
                $dateOnWhichReasonExpires = Carbon::parse($task->overdueReason->created_at)->addDays($task->overdueReason->reason->days);
                if($dateOnWhichReasonExpires > Carbon::parse($toDate)) {
                    unset($tasks[$id]);
                }
            }

        }
        return $tasks;
    }

    /**
     * Prepare data for db insert
     *
     * @param array $input
     * @return array
     */
    protected function prepareCreateData(array $input)
    {
        $userRepository = app()->make(UserInterface::class);

        $input['created_by'] = Auth::user() ? Auth::user()->id : null;
        $input['due_at'] = $input['deadline'];
        
        if(!isset($input['repeating'])) {
            $input['repeating'] = Task::NOT_REPEATING;
        }
        if($input['repeating'] == Task::NOT_REPEATING) {
            $input['frequency'] = null;
        }
        if(!isset($input['active'])) {
            $input['active'] = Task::ACTIVE;
        }
        if(!isset($input['regenerated'])) {
            $input['regenerated'] = Task::NOT_REGENERATED;
        }
        if(!isset($input['private'])) {
            $input['private'] = Task::NOT_PRIVATE;
        }
        if(!isset($input['delivered'])) {
            $input['delivered'] = Task::NOT_DELIVERED;
        }
        if(!isset($input['end_date'])) {
            $input['end_date'] = null;
        }
        if(!isset($input['completed_at'])) {
            $input['completed_at'] = null;
        }
        if(!isset($input['template_id'])) {
            $input['template_id'] = null;
        }
        if(!isset($input['description'])) {
            $input['description'] = '';
        }

        if (isset($input['template_id']) && !empty($input["template_id"])) {
            $templateRepository = app()->make(TemplateInterface::class);
            $this->template = $templateRepository->find($input['template_id']);

            $input['category'] = $this->template->category;
            $input['version_no'] = $this->template->versions->first()->version_no;

            // Check if user can do task if it's a template
            if (isset($input['user_id']) && $input['user_id']) {
                $user = $userRepository->model()->find($input['user_id']);
                if ($user && !is_null($this->template) && $user->canProcessTemplate($this->template)) {
                    $input['user_id'] = $user->id;
                } else {
                    $input['user_id'] = NULL;
                }
            }
        }
        else {
            $input['category'] = 'Custom';
            $input['version_no'] = 0;
        }

        if (!is_null($this->template) && (!isset($input['title']) || empty($input['title']))) {
            $input['title'] = $this->template->title;
        }

        return $input;
    }

    /**
     * Prepare data for db update
     *
     * @param $id
     * @param array $input
     * @return array
     */
    protected function prepareUpdateData($id, array $input)
    {
        $task = $this->model->find($id);
        $userRepository = app()->make(UserInterface::class);
        $userId = isset($input['user']) ? $input['user'] : $task->user_id;
        $user = $userRepository->model()->find($userId);

        // Check if user is changed
        $data = [];

        if ($user && $task->template) {
            $data['user_id'] = $user->canProcessTemplate($task->template) ? $user->id : null;
        } elseif ($user) {
            $data['user_id'] = $user->id;
        } else {
            $data['user_id'] = null;
        }

        $task->subtasks()->whereNull('completed_at')->update($data);

        $input['user_id'] = $data['user_id'] ? $data['user_id'] : $task->user_id;
        $input['title'] = isset($input['title']) ? $input['title'] : $task->title;
        $input['repeating'] = isset($input['repeating']) ? $input['repeating'] : $task->repeating;
        $input['frequency'] = isset($input['frequency']) ? $input['frequency'] : $task->frequency;
        $input['end_date'] = isset($input['end_date']) ? $input['end_date'] : $task->end_date;
        $input['private'] = isset($input['private']) ? $input['private'] : $task->private;

        // Check if deadline is updated and if due date needs to be changed
        if (isset($input['deadline'])) {
            // Check if we have an overdue reason and if expired_at is bigger than deadline. If not we update due_date to deadline datetime.
            if ((is_null($task->lastOverdueReason)) || ($task->lastOverdueReason && ($task->lastOverdueReason->expired_at < $input['deadline']))) {
                $input['due_at'] = $input['deadline'];
            } elseif ($task->lastOverdueReason && ($task->lastOverdueReason->expired_at > $input['deadline'])) {
                // If last overdue reason is greather than new deadline, then change due_date back to expired_at.
                $input['due_at'] = $task->lastOverdueReason->expired_at->format('Y-m-d H:i:s');
            }
        } else {
            $input['deadline'] = $task->deadline->format('Y-m-d H:i:s');
        }

        return $input;
    }

    /**
     * Create an overdue reason for a task
     *
     * @param Task $task
     * @param array $input
     * @throws ValidationException
     */
    public function createOverdue(Task $task, array $input)
    {
        $overdueReasonRepository = app()->make(OverdueReasonInterface::class);
        $taskOverdueReasonRepository = app()->make(TaskOverdueReasonInterface::class);
        $taskRepository = app()->make(TaskInterface::class);

        $overdueReason = $overdueReasonRepository->find($input['reason']);
        if($overdueReason->required) {
            if(empty($input['comment']) && empty($input['ticket_id'])) {
                throw ValidationException::withMessages([
                    'error' => 'A comment or a Zendesk ticket ID must be supplied',
                ]);
            }
        }

        // Format comment data
        $comment = '';
        if (!empty($input['ticket_id']) && !empty($input['comment'])) {
            $comment .= 'Zendesk Ticket ID: ' . $input['ticket_id'] . ' - Comment: ' . $input['comment'];
        } elseif (!empty($input['ticket_id'])){
            $comment .= 'Zendesk Ticket ID: ' . $input['ticket_id'];
        } elseif(!empty($input['comment'])) {
            $comment .= 'Comment: ' . $input['comment'];
        }

        // Check if system made the task if not use the authenticated user
        if(isset($input['user_id'])){
            $userId = $input['user_id'];
        } else {
            $userId = Auth::user() ? Auth::user()->id : null;
        }

        // Deactivate previous task overdue reasons (if any)
        $lastTaskOverdueReason = $taskOverdueReasonRepository->model()->where('task_id', $task->id)->orderBy('id', 'DESC')->first();
        if($lastTaskOverdueReason) {
            $lastTaskOverdueReason->update(['active'=>0]);
        }

        // Calculate the expiration Time
        if($task->isOverdue()){
            $expiredAt = Carbon::now()->addDays($overdueReason->days);
        } else {  
            $expiredAt = $task->deadline->addDays($overdueReason->days);
        }

        // Create the new task overdue reason
        $counter = ($lastTaskOverdueReason && $lastTaskOverdueReason->reason_id == $overdueReason->id) ? $lastTaskOverdueReason->counter + 1 : 1;

        $taskOverdueReasonRepository->create([
            'task_id'         => $task->id,
            'active'          => 1,
            'user_id'         => $userId,
            'reason_id'       => $overdueReason->id,
            'counter'         => $counter,
            'comment'         => $comment,
            'expired_at'      => $expiredAt,
        ]);

        // Update the due_at field
        $this->update($task->id, [
            'due_at'    => $expiredAt->format('Y-m-d H:i:s'),
        ]);

        // Check if overdue reason will automatically complete the task
        if ($overdueReason->complete_task) {
            $subtasksNotCompleted = $task->activeSubtasks;
            $task = $taskRepository->markTaskAsCompleted($task);

            if (!is_null($overdueReason->completed_user_id)) {
                $task->user_id = $overdueReason->completed_user_id;
                $task->save();

                foreach ($subtasksNotCompleted as $subtask) {
                    $subtask->user_id = $overdueReason->completed_user_id;
                    $subtask->save();
                }
            }
        }

    }
    /**
     * Reopen a task and reopen selected subtasks
     *
     * @param Task $task
     * @param int $userId
     * @param array $input
     *
     * @return bool
     * @throws ValidationException
     */
    public function reopen(Task $task, int $userId, array $input) : bool
    {
//        if (! $task->client->active) {
//            throw ValidationException::withMessages([
//                'error' => 'Task client is deactivated.',
//            ]);
//        }

        if (!$task->isComplete()) {
            throw ValidationException::withMessages([
                'error' => 'Task is not complete.',
            ]);
        }
        
        // Create a task reopening entry
        $taskReopeningRepository = app()->make(TaskReopeningInterface::class);
        $taskReopening = $taskReopeningRepository->create([
            'task_id'       => $task->id,
            'user_id'       => $userId,
            'reason'        => $input['reason'],
            'completed_at'  => $task->completed_at,
        ]);

        // Reopen all of the selected subtasks
        if(isset($input['subtasks']) && !empty($input['subtasks'])) {
            $subtaskReopeningRepository = app()->make(SubtaskReopeningInterface::class);
            $subtaskRepository = app()->make(SubtaskInterface::class);

            collect($input['subtasks'])->each(function ($id) use (
                $userId,
                $task,
                $subtaskReopeningRepository,
                $subtaskRepository
            ) {
                $subtask = $subtaskRepository->find($id);

                $subtaskReopening = $subtaskReopeningRepository->create([
                    'subtask_id' => $subtask->id,
                    'user_id' => $userId,
                    'reason' => 'Reopened with parent task (Assigned User: ' . $subtask->user->name . ')',
                    'completed_at' => $subtask->completed_at,
                ]);

                $subtask->completed_at = null;
                $subtask->save();
            });
        }

        // Unassign the task if the task was assigned to a deactivated user
        if (!$task->user->active) {
            $task->user_id = null;
        }

        // Unassign the task if the user is not able to complete the template
        if ($task->template){
            if (! $task->user->canProcessTemplate($task->template)) {
                $task->user_id = null;
            }  
        }
        
        // Set the task as not completed
        $task->completed_at = null;
        $task->save();

        return true;
    }

    /**
     * Mark a task as completed.
     *
     * @param Task $task
     * @return Task
     */
    public function markTaskAsCompleted(Task $task) : Task
    {
        $initialActiveStatus = $task->active;

        // Run rulesets for regeneration
        if (! $task->regenerated && $task->repeating && $task->reopenings()->count() == 0) {
            if (($task->end_date > (new Frequency($task->frequency))->next($task->deadline) || is_null($task->end_date))) {
                
                // Set the next deadline
                $nextDeadline = (new Frequency($task->frequency))->next($task->deadline)->format('Y-m-d H:i:s');

                // Create a new task
                $newTask = $this->create([
                    'template_id'   => $task->template_id,
                    'client_id'     => $task->client_id,
                    'user_id'       => $task->user_id,
                    'created_by'    => $task->created_by,
                    'title'         => ($task->template) ? $task->template->title : $task->title,
                    'description'   => $task->isCustom() ? $task->details->description : '',
                    'repeating'     => Task::REPEATING,
                    'frequency'     => $task->frequency,
                    'deadline'      => $nextDeadline,
                    'due_at'        => $nextDeadline,
                    'end_date'      => $task->end_date,
                    'active'        => $initialActiveStatus,
                    'regenerated'   => Task::NOT_REGENERATED,
                    'private'       => $task->private,
                    'delivered'     => Task::NOT_DELIVERED,
                    'completed_at'  => null,
                ]);
            }
        }

        // Finish task completion
        $this->update($task->id, [
            'completed_at'  => Carbon::now(),
            'active'        => Task::ACTIVE,
        ]);

        // Mark all active subtasks as completed.
        foreach ($task->activeSubtasks as $subtask) {
            $subtaskRepository = app()->make(SubtaskInterface::class);
            $subtaskRepository->update($subtask->id, [
                'completed_at' => Carbon::now(), 
            ]);
        }

        return $task;
    }

    /**
     * Regenerate a task, and return the newly created record for the regenerated task.
     *
     * @param Task $task
     * @return Task
     * @throws ValidationException
     */
    public function regenerate(Task $task) : Task
    {
        DB::beginTransaction();

        // Set the next deadline
        $nextDeadline = (new Frequency($task->frequency))->next($task->deadline)->format('Y-m-d H:i:s');

        // Create a new task
        $newTask = $this->create([
            'template_id'   => $task->template_id,
            'client_id'     => $task->client_id,
            'user_id'       => $task->user_id,
            'created_by'    => $task->created_by,
            'title'         => ($task->template) ? $task->template->title : $task->title,
            'repeating'     => Task::REPEATING,
            'frequency'     => $task->frequency,
            'deadline'      => $nextDeadline,
            'due_at'        => $nextDeadline,
            'end_date'      => $task->end_date,
            'active'        => $task->active,
            'regenerated'   => Task::NOT_REGENERATED,
            'private'       => $task->private,
            'delivered'     => Task::NOT_DELIVERED,
            'completed_at'  => null,
        ]);

        // Add description if custom task
        if(! $task->template) {
            $taskDetailsRepo = app()->make(TaskDetailsInterface::class);
            $taskDetailsRepo->create([
                'task_id'       => $newTask->id,
                'description'   => $task->details->description,
            ]);
        }

        // Process regeneration
        $this->update($task->id, [
            'regenerated' => Task::REGENERATED,
        ]);

        DB::commit();

        return $newTask;
    }

    /**
     * Return counted task eligible for notification
     * (returned attributes :user_name, user_phone, tasks_counted)
     *
     * @return \Illuminate\Support\Collection
     */
    public function getCountedTasksForSMSReminder()
    {
        return $this->model()
            ->selectRaw("users.name as user_name, users.phone as user_phone, count(*) as tasks_counted")
            ->join('users', 'tasks.user_id', 'users.id')
            ->leftjoin(DB::raw("(SELECT task_overdue_reasons.task_id, task_overdue_reasons.expired_at FROM task_overdue_reasons WHERE task_overdue_reasons.active = 1) as tor"), 'tor.task_id', 'tasks.id')
            ->where(function ($query) {
                $query->where(function ($query) {
                        $query->where('tor.expired_at', '<=', now()->addDay())
                            ->where('tor.expired_at', '>=', now())
                            ->whereNotNull('tor.expired_at');
                    })->orWhere(function ($query) {
                        $query->where('tasks.deadline', '<=', now()->endOfDay())
                            ->whereNull('tor.expired_at');
                    });
            })
            ->where('tasks.active', 1)
            ->whereNull('tasks.completed_at')
            ->whereNotNull('users.phone')
            ->groupBy('users.id')
            ->get();
    }
}
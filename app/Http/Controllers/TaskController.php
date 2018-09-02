<?php

namespace App\Http\Controllers;

use App\Repositories\Task\TaskCreateRequest;
use App\Repositories\TaskDetails\TaskDetailsInterface;
use App\Repositories\UserCompletedTask\UserCompletedTask;
use App\Repositories\UserCompletedTask\UserCompletedTaskInterface;
use App\Repositories\Task\TaskInterface;
use App\Repositories\TasksUserAcceptance\TasksUserAcceptanceInterface;
use App\Repositories\TemplateVersion\TemplateVersionInterface;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Frequency;
use App\Repositories\Client\Client;
use App\Repositories\Task\Task;
use App\Repositories\Template\Template;
use App\Repositories\User\User;
use App\Repositories\OverdueReason\OverdueReason;

class TaskController extends Controller
{
    /**
     * @var TaskInterface
     */
    private $taskRepository;

    /**
     * Instantiate a new controller instance.
     *
     * @param TaskInterface $taskRepository
     */
    public function __construct(TaskInterface $taskRepository)
    {
        $this->middleware('admin_only')->only('create', 'store');
        parent::__construct();

        $this->taskRepository = $taskRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Repositories\Client\Client $client
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function index(Request $request, Client $client)
    {
        if ($request->wantsJson()) {
            return $client->tasks(false)
                ->leftJoin('task_reopenings', function ($join) {
                    $join->on('tasks.id', '=', 'task_reopenings.task_id')->whereNotNull('task_reopenings.completed_at');
                })
                ->select('tasks.*', DB::raw('IF(task_reopenings.id > 0  , 1, 0) as reopened'))
                ->paginate(25);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param  \App\Repositories\Client\Client  $client
     * @return \Illuminate\Http\Response
     */
    public function create(Client $client)
    {
        if (! $client->active && ! Auth::user()->hasRole(User::ROLE_ADMIN)) {
            return back()->with('error', "Task can't be created. Client is deactivated!");
        }

        return view('tasks.create', compact('client'));
    }

    /**
     * @param \App\Repositories\Client\Client $client
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function createCustom(Client $client)
    {
        if (! $client->active && Auth::user()->hasRole(User::ROLE_EMPLOYEE)) {
            return back()->with('error', "Task can't be created. Client is deactivated!");
        }

        return view('tasks.create-custom', compact('client'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param TaskCreateRequest|Request $request
     * @param  \App\Repositories\Client\Client $client
     * @return \Illuminate\Http\Response
     */
    public function store(TaskCreateRequest $request, Client $client)
    {
        // Authorize request
        $this->authorize('create', [Task::class, $client]);

        // Create task via repository
        $task = $this->taskRepository->create($request->all());

        return redirect()
            ->action('ClientController@show', $task->client)
            ->with('success', 'Task created.');
    }

    /**
     * @param TaskCreateRequest|Request $request
     * @param \App\Repositories\Client\Client $client
     *
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Http\RedirectResponse
     */
    public function storeCustom(TaskCreateRequest $request, Client $client)
    {
        // Authorize request
        $this->authorize('createCustom', [Task::class, $client]);

        // Create task via repository
        $task = $this->taskRepository->create($request->all());

        return redirect()
            ->action('ClientController@show', $task->client)
            ->with('success', 'Task created.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Repositories\Task\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, Task $task)
    {
        // todo: after merging user groups and permissions this should be reflected as well
        if ($task->isPrivate()) {
            if (! $request->user()->isAdmin() && $task->user_id != $request->user()->id) {
                return back()->with('info', "You don't have permissions to view task.");
            }
        }

        // Return the task as JSON for the API
        if ($request->wantsJson()) {
            return $task;
        }

        // Get paginated subtasks
        $subtasks = $task->subtasks()->orderBy('order')->paginate(50);

        $comment = $task
            ->comments()
            ->select([
                'comments.id AS id',
                'comments.task_id AS task_id',
                'comments.user_id AS user_id',
                'comments.comment AS comment',
                'comments.after_complete AS after_complete',
                DB::raw('"comments" AS type'),
                'comments.created_at AS created_at']);

        $reason = $task
            ->taskOverdueReasons()
            ->select([
                'task_overdue_reasons.id AS id',
                'task_overdue_reasons.task_id AS task_id',
                'task_overdue_reasons.user_id AS user_id',
                DB::raw('CONCAT(or.reason, ". ", task_overdue_reasons.comment) AS comment'),
                DB::raw('"" AS after_complete'),
                DB::raw('"reason" AS type'),
                'task_overdue_reasons.created_at AS created_at'])
            ->join('overdue_reasons as or', 'or.id', '=', 'task_overdue_reasons.reason_id')
            ->union($comment)->with('user')->orderBy('created_at')
            ->get();

        //Set from overdue page if present
        $request->user()->hasOverdueTasks() ? $tasksoverdue = 1 : $tasksoverdue = 0;

        // Check if task has been reopened due to a declined review
        $declinedReview = false;
        if($task->isReopened()) {
            $userCompletedTasksRepository = app()->make(UserCompletedTaskInterface::class);
            $userCompletedTask = $userCompletedTasksRepository->model()->where('task_id', $task->id)->orderBy('id', 'DESC')->first();
            if($userCompletedTask) {
                if($userCompletedTask->status == UserCompletedTask::STATUS_DECLINED) {
                    $declinedReview = true;
                }
            }
        }

        return view('tasks.show')->with([
            'task'           => $task,
            'client'         => $task->client,
            'subtasks'       => $subtasks,
            'comments'       => $reason,
            'overdues'       => OverdueReason::where('active', 1)->where('default',  1)->orderBy('priority')->get(),
            'tasksoverdue'   => $tasksoverdue,
            'declinedReview' => $declinedReview
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Repositories\Task\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function edit(Task $task)
    {
        return view('tasks.edit')->with([
            'client' => $task->client,
            'task'   => $task,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Repositories\Task\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Task $task)
    {
        $this->authorize('update', $task);

        $task = $this->taskRepository->update($task->id, $request->all());

        return redirect()
            ->action('TaskController@show', $task)
            ->with('success', 'Task updated.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Repositories\Task\Task $task
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(Request $request, Task $task)
    {
        $client = $task->client;
        
        if ($task->review) {
            return redirect()
                ->back()
                ->with('error', 'You can\'t delete a task linked to a review.');
        }

        $this->taskRepository->delete($task->id);

        if (! $request->wantsJson()) {
            return redirect()
                ->action('ClientController@show', $client)
                ->with('info', 'Task deleted.');
        }
    }

    /**
     * Mark the task as completed.
     *
     * @param  \App\Repositories\Task\Task $task
     * @param bool $redirect
     * @return \Illuminate\Http\Response
     */
    public function completed(Task $task, $redirect = true)
    {
        $task = $this->taskRepository->markTaskAsCompleted($task);

        if ($redirect) {
            return redirect()
                ->action('ClientController@show', $task->client)
                ->with('success', 'Task completed.');
        }
    }

    /**
     * @param Task $task
     *
     * @return $this
     */
    public function reviewChanges(Request $request, Task $task)
    {
        // Get the latest accepted task version (for comparison)
        $taskUserAcceptanceRepository = app()->make(TasksUserAcceptanceInterface::class);
        $latestAcceptedVersion = $taskUserAcceptanceRepository->model()
            ->where('type', 1)
            ->where('user_id', $request->user()->id)
            ->where('template_id', $task->template_id)
            ->where('version_no', '<>', $task->version->version_no)
            ->orderBy('version_no', 'DESC')
            ->first();

        if($latestAcceptedVersion) {
            $noChanges = false;
            $oldVersionDescription = null;

            // Get details of latest accepted version
            $templateVersionRepository = app()->make(TemplateVersionInterface::class);
            $oldVersion = $templateVersionRepository->model()
                ->where('template_id', $task->template->id)
                ->where('version_no', $latestAcceptedVersion->version_no)
                ->first();

            $oldVersionDescription = $oldVersion ? $oldVersion->description : null;
        }
        else {
            $noChanges = true;
        }

        // Prepare data for view
        $currentVersionDetails = $oldVersionDetails = [
            'title'       => $task->title,
            'description' => $task->version->description,
        ];
        if(!$noChanges) {
            $oldVersionDetails['description'] = $oldVersionDescription;
        }

        return view('tasks.review-changes')->with([
            'task'                  => $task,
            'noChanges'             => $noChanges,
            'tasksoverdue'          => $request->user()->hasOverdueTasks(), // Set from overdue page if present
            'currentVersionDetails' => $currentVersionDetails,
            'oldVersionDetails'     => $oldVersionDetails,
        ]);
    }

    /**
     * @param \App\Repositories\Task\Task $task
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function acceptReviewedChanges(Task $task)
    {
        // Save logs for this action
        DB::table('logs_task_user_acceptance')->insert([
            'user_id'           => Auth::user()->id,
            'template_id'       => $task->template->id,
            'version_no'        => $task->version_no,
            'terms_accepted'    => json_encode(['title'=>$task->title, 'description'=>$task->version->description])
        ]);

        // Mark the template as accepted
        if (isset($task->user)) {
            DB::table('tasks_user_acceptance')->insert([
                'type'        => 1,
                'user_id'     => Auth::user()->id,
                'template_id' => $task->template->id,
                'version_no'  => $task->version_no,
            ]);
        }

        return redirect()
            ->action('ClientController@show', $task->client)
            ->with('success', 'Changes accepted!.');
    }

    /**
     * @param \App\Repositories\Task\Task $task
     *
     * @return $this
     */
    public function showRegenerateForm(Task $task)
    {
        return view('tasks.regenerate')->with([
            'task' => $task,
            'client' => $task->client
        ]);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Repositories\Task\Task $task
     *
     * @return array|\Illuminate\Http\RedirectResponse
     */
    public function regenerate(Request $request, Task $task)
    {
        // validate if a reason has been provided
        $this->validate($request, [
            'reason' => 'required',
        ]);

        // validate if task can be regenerated
        if ((! is_null($task->completed_at) || $task->reopenings->count() > 0 || $task->regenerated) || ($task->end_date <= (new Frequency($task->frequency))->next($task->deadline) && ! is_null($task->end_date))) {
            return ['error' => "Task can't be regenerated."];
        }

        $newTask = $this->taskRepository->regenerate($task);

        // Save reason as task note
        $task->comments()->create([
            'user_id' => $request->user()->id,
            'comment' => 'Regeneration reason: '.$request->input('reason'),
            'after_complete' => (int) $task->isComplete(),
        ]);

        return redirect()
            ->action('TaskController@show', $newTask)
            ->with('success', 'Task regenerated. You are now in the new task.');
    }

    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function showOverdue(Request $request)
    {
        $task = $request->user()->overdueTasks()->first();

        //  Redirect user to dashboard if there are no overdue tasks to show
        if(!$task) {
            return redirect('/dashboard');
        }

        // Get paginated subtasks
        $subtasks = $task->subtasks()->orderBy('order')->paginate(50);

        // Get all possible overdue reasons
        $overdues = OverdueReason::where('active', 1)->where('default',  1)->orderBy('priority')->get();

        $comments = $task->comments()->orderBy('created_at', 'DESC')->get();
        $taskOverdueReasons = $task->taskOverdueReasons()
            ->select(
                'task_overdue_reasons.id',
                'task_overdue_reasons.task_id',
                'task_overdue_reasons.user_id',
                'overdue_reasons.reason',
                'task_overdue_reasons.comment',
                'task_overdue_reasons.created_at'
            )
            ->join('overdue_reasons', 'overdue_reasons.id', '=', 'task_overdue_reasons.reason_id')
            ->orderBy('task_overdue_reasons.created_at', 'DESC')
            ->get();

        // $commentsAndReasons will store the combined results of '$comments' and '$taskOverdueReasons'
        $commentsAndReasons = [];

        foreach ($comments as $comment) {
            $commentsAndReasons[] = [
                'type'           => 'comments',
                'id'             => $comment->id,
                'task_id'        => $comment->task_id,
                'user'           => $comment->user,
                'comment'        => $comment->comment,
                'after_complete' => $comment->after_complete,
                'created_at'     => $comment->created_at,
            ];
        }
        foreach ($taskOverdueReasons as $taskOverdueReason) {
            $commentsAndReasons[] = [
                'type'           => 'reason',
                'id'             => $taskOverdueReason->id,
                'task_id'        => $taskOverdueReason->task_id,
                'user'           => $taskOverdueReason->user,
                'comment'        => $taskOverdueReason->reason . '. ' . $taskOverdueReason->comment,
                'after_complete' => null,
                'created_at'     => $taskOverdueReason->created_at,
            ];
        }

        // Sort '$commentsAndReasons' by 'created_at'. Results are needed in chronological order
        usort($commentsAndReasons, array($this, 'compare_creation_time'));
        // Return the show view
        return view('tasks.overdue')->with([
            'task'     => $task,
            'subtasks' => $subtasks,
            'comments' => [],
            'overdues' => $overdues,
            'commentsAndReasons' => $commentsAndReasons,
        ]);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Repositories\Task\Task $task
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function createOverdue(Request $request, Task $task)
    {
        // Validate request
        $this->validate($request, [
            'reason'    => 'required|numeric|exists:overdue_reasons,id',
            'ticket_id' => 'sometimes|nullable',
            'comment'   => 'sometimes|nullable',
        ]);

        $this->taskRepository->createOverdue($task, $request->all());

        if ($request->has('overdue')) {
            return redirect()
                ->action('TaskController@showOverdue')
                ->with('success', 'Overdue reason created.');
        }

        return redirect()
            ->action('TaskController@show', $task)
            ->with('success', 'Overdue reason created.');
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Repositories\Task\Task $task
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function completedOverdue(Request $request, Task $task)
    {
        if ($task->subtasks()->where('completed_at', null)->count()) {
            foreach ($task->subtasks()->where('completed_at', null)->get() as $subtask) {
                $subtaskController = new SubtaskController();
                $subtaskController->completed($subtask);
            }
        } else {
            $this->completed($task);
        }

        return redirect()
            ->action('TaskController@showOverdue')
            ->with('success', 'Task completed.');
    }

    /**
     * @param \App\Repositories\Task\Task $task
     *
     * @return \Illuminate\Http\Response
     */
    public function createDelivered(Task $task)
    {
        $task->update([
            'delivered' => Task::DELIVERED,
        ]);

        return view('tasks.delivered.create')->with([
            'task' => $task,
        ]);
    }

    /**
     * Create delivered status for task from hash
     *
     * @param string $hash
     * @return \Illuminate\Http\Response
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function createDeliveredFromHash($hash)
    {
        $task = $this->taskRepository->find(decrypt($hash));

        if (! $task) {
            abort(404);
        }

        $task->update([
            'delivered' => Task::DELIVERED,
        ]);

        return view('tasks.delivered.create')->with([
            'task' => $task,
        ]);
    }

    /**
     * @param string $hash
     *
     * @return \Illuminate\Http\Response
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function removeDeliveredFromHash($hash)
    {
        $task = $this->taskRepository->find(decrypt($hash));

        if (! $task) {
            abort(404);
        }

        $task->update([
            'delivered' => Task::NOT_DELIVERED,
            'delivered_read_at' => null,
        ]);

        return view('tasks.delivered.destroy');
    }


    /**
     * callback function used to sort multimensional arrays based on 'created_at' key
     * @param array $a
     * @param array $b
     * @return bool
     */
    public function compare_creation_time(array $a, array $b)
    {
        return $a['created_at'] < $b['created_at'];
    }
}

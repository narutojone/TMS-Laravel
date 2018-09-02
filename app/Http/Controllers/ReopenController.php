<?php

namespace App\Http\Controllers;

use App\Repositories\Task\TaskInterface;
use App\Repositories\Task\TaskReopenRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ReopenController extends Controller
{
    /**
     * @var TaskInterface
     */
    protected $taskRepository;

    /**
     * ReopenController constructor.
     * @param TaskInterface $taskRepository
     */
    public function __construct(TaskInterface $taskRepository)
    {
        parent::__construct();

        $this->taskRepository = $taskRepository;
    }

    /**
     * Show the form for reopening the task.
     *
     * @param int $taskId
     *
     * @return View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function form(int $taskId)
    {
        $task = $this->taskRepository->find($taskId);

        $this->authorize('view', $task);

        if (! $task->client->active) {
            return redirect()->back()->withErrors("Task can't be reopened. Client is deactivated!");
        }

        if (! $task->isComplete()) {
            return redirect()->back()->withErrors('Task is not complete!');
        }

        $subtasks = $task->subtasks->split(2);

        return view('tasks.reopen')->with([
            'client'    => $task->client,
            'subtasks'  => $subtasks,
            'task'      => $task,
        ]);
    }

    /**
     * Reopen the task.
     *
     * @param TaskReopenRequest $request
     * @param $taskId
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function submit(TaskReopenRequest $request, $taskId)
    {
        $task = $this->taskRepository->find($taskId);

        $this->authorize('view', $task);
        
        $this->taskRepository->reopen($task, $request->user()->id, $request->all());

        return redirect()
            ->action('ClientController@show', $task->client)
            ->with('success', 'Task reopened.');
    }
}

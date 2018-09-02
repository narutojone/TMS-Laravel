<?php

namespace App\Http\Controllers;

use App\Repositories\Subtask\SubtaskInterface;
use Illuminate\Http\Request;

class ReopenSubtaskController extends Controller
{
    /**
     * @var $subtaskRepository - EloquentRepositorySubtask
     */
    private $subtaskRepository;

    public function __construct(SubtaskInterface $subtaskRepository)
    {
        parent::__construct();

        $this->subtaskRepository = $subtaskRepository;
    }

    /**
     * Show the form for reopening the subtask.
     *
     * @param int $subtaskId
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function form(int $subtaskId)
    {
        $subtask = $this->subtaskRepository->make()->find($subtaskId);

        // Authorize request
        $this->authorize('reopen', $subtask);

        $task = $subtask->task;
        $client = $task->client;

        return view('subtasks.reopen')->with([
            'client'    => $client,
            'task'      => $task,
            'subtask'   => $subtask,
        ]);
    }

    /**
     * Reopen the subtask.
     *
     * @param  \Illuminate\Http\Request $request
     * @param int $subtaskId
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function submit(Request $request, int $subtaskId)
    {
        $subtask = $this->subtaskRepository->make()->find($subtaskId);

        // Authorize request
        $this->authorize('reopen', $subtask);

        $subtask = $this->subtaskRepository->createReopenings($subtaskId, $request->user()->id, $request->all());

        // Redirect back to the parent task with a success flash message
        return redirect()
            ->action('TaskController@show', $subtask->task)
            ->with('success', 'Subtask reopened.');
    }
}

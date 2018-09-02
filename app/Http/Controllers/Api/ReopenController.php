<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\Task\TaskInterface;
use App\Repositories\Task\TaskReopenRequest;
use App\Repositories\Task\TaskTransformer;

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
     * Reopen the task.
     *
     * @param TaskReopenRequest $request
     * @param $taskId
     *
     * @return \Illuminate\Http\Response
     */
    public function submit(TaskReopenRequest $request, $taskId)
    {
        $task = $this->taskRepository->find($taskId);
        $this->taskRepository->reopen($task, $request->user()->id, $request->all());

        return (new TaskTransformer)->transform($task);
    }
}
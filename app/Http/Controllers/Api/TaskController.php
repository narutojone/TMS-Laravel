<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\Client\Client;
use App\Repositories\Task\TaskCreateRequest;
use App\Repositories\Task\TaskInterface;
use App\Repositories\Task\TaskTransformer;
use App\Repositories\Task\TaskUpdateRequest;
use Illuminate\Http\Request;
use App\Repositories\Template\TemplateCreateRequest;

class TaskController extends Controller
{
    /**
     * @var $taskRepository - EloquentRepositoryTask
     */
    private $taskRepository;

    /**
     * Instantiate a new controller instance.
     *
     * @param TaskInterface $taskRepository
     */
    public function __construct(TaskInterface $taskRepository)
    {
        $this->middleware('admin_only');
        parent::__construct();

        $this->taskRepository = $taskRepository;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param TaskCreateRequest|TemplateCreateRequest $request
     * @param Client $client
     * @return \Illuminate\Http\Response
     */
    public function store(TaskCreateRequest $request, Client $client)
    {
        // Authorize request
        $this->authorize('create', $client);

        $task = $this->taskRepository->create($request->all());

        return (new TaskTransformer())->transform($task);
    }

    /**
     * Display the specified resource.
     *
     * @param  \Illuminate\Http\Request $request
     * @param Task $task
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, Task $task)
    {
        // Authorize request
        $this->authorize('view', $task);

        return (new TaskTransformer)->transform($task);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param TaskUpdateRequest|TemplateCreateRequest $request
     * @param Task $task
     * @return \Illuminate\Http\Response
     */
    public function update(TaskUpdateRequest $request, Task $task)
    {
        // Authorize request
        $this->authorize('view', $task);

        $task = $this->taskRepository->update($task->id, $request->all());

        return (new TaskTransformer)->transform($task);
    }
}
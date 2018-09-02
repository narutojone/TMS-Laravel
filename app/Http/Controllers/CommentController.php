<?php

namespace App\Http\Controllers;

use App\Repositories\Comment\CommentCreateRequest;
use App\Repositories\Comment\CommentInterface;
use App\Repositories\Task\TaskInterface;

class CommentController extends Controller
{
    /**
     * @var $commentRepository - EloquentRepositoryComment
     */
    private $commentRepository;

    /**
     * @var $taskRepository - EloquentRepositoryTask
     */
    private $taskRepository;

    /**
     * CommentController constructor.
     *
     * @param CommentInterface $commentRepository
     * @param TaskInterface $taskRepository
     */
    public function __construct(CommentInterface $commentRepository, TaskInterface $taskRepository)
    {
        parent::__construct();

        $this->commentRepository = $commentRepository;
        $this->taskRepository = $taskRepository;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param CommentCreateRequest $request
     * @param int $taskId
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(CommentCreateRequest $request, int $taskId)
    {
        $task = $this->taskRepository->make()->find($taskId);

        // Authorize request
        $this->authorize('view', $task);

        $data = $request->all();
        $data['user_id'] = $request->user()->id;
        $data['task_id'] = $task->id;
        $data['after_complete'] = (int) $task->isComplete();

        $this->commentRepository->create($data);

        return redirect()
            ->action('TaskController@show', $task)
            ->with('success', 'Comment posted.');
    }

    public function reviewStore(CommentCreateRequest $request, int $taskId)
    {
        $task = $this->taskRepository->make()->find($taskId);

        // Authorize request
        $this->authorize('view', $task);

        $data = $request->all();
        $data['user_id'] = $request->user()->id;
        $data['task_id'] = $task->id;
        $data['after_complete'] = (int) $task->isComplete();
        $data['from_review_page'] = true;

        $comment = $this->commentRepository->create($data);
        if ($comment) {
            return back()->with('success', 'Comment posted.');
        }
    }
}

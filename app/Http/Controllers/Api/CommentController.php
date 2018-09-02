<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\Comment\CommentCreateRequest;
use App\Repositories\Comment\CommentInterface;
use App\Repositories\Comment\CommentTransformer;
use App\Repositories\Task\TaskInterface;
use Illuminate\Http\Request;


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
     * Display a listing of the resource.
     *
     * @param Request $request
     * @param int $taskId
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, int $taskId)
    {
        $task = $this->taskRepository->make()->find($taskId);

        // Authorize request
        $this->authorize('view', $task);

        return $task->comments()->paginate(25);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param CommentCreateRequest $request
     * @param int $taskId
     *
     * @return \Illuminate\Http\Response
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

        $comment = $this->commentRepository->create($data);

        return (new CommentTransformer)->transform($comment);
    }

    /**
     * Display the specified resource.
     *
     * @param int $commentId
     *
     * @return \Illuminate\Http\Response
     */
    public function show(int $commentId)
    {
        $comment = $this->commentRepository->make()->find($commentId);

        return (new CommentTransformer)->transform($comment);
    }
}
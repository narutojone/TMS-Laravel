<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\Subtask\SubtaskInterface;
use App\Repositories\Subtask\SubtaskTransformer;


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
     * Reopen the subtask.
     *
     * @param Request $request
     * @param int $subtaskId
     *
     * @return \Illuminate\Http\Response
     */
    public function submit(Request $request, int $subtaskId)
    {
        $subtask = $this->subtaskRepository->createReopenings($subtaskId, $request->user()->id, $request->all());

        return (new SubtaskTransformer)->transform($subtask);
    }
}
<?php

namespace App\Lib\Reports\src;

use App\Lib\Reports\AbstractReport;
use App\Repositories\OverdueReason\OverdueReasonInterface;
use App\Repositories\Task\TaskInterface;
use Carbon\Carbon;

class AggregatedOverdueReport extends AbstractReport
{
    private $overdueReasonRepository;

    public function __construct()
    {
        $this->overdueReasonRepository = app()->make(OverdueReasonInterface::class);
    }

    public function getReportData(array $requestData)
    {
        $taskRepository = app()->make(TaskInterface::class);

        $tasksQueryBuilder = $taskRepository->model()
            ->with(['client', 'user'])
            ->select('tasks.*', 'task_overdue_reasons.counter')
            ->leftJoin('task_overdue_reasons', 'task_overdue_reasons.task_id', '=', 'tasks.id')
            ->where('tasks.active', 1)
            ->whereNull('tasks.completed_at')
            ->where('tasks.deadline', '<', Carbon::now())
            ->whereRaw('task_overdue_reasons.id = (SELECT MAX(id) FROM task_overdue_reasons WHERE task_id = tasks.id)')
            ->orderBy('task_overdue_reasons.counter', 'DESC');

        // Prepare counter filter
        $minCount = isset($requestData['counter']) && !empty($requestData['counter']) ? (int)$requestData['counter'] : 2;
        // Prepare reason filter
        $reasonId = isset($requestData['overdue-reason']) ? (int)$requestData['overdue-reason'] : null;


        // Apply query filters
        $tasksQueryBuilder = $tasksQueryBuilder->where('task_overdue_reasons.counter', '>=', $minCount);
        if($reasonId) {
            $tasksQueryBuilder = $tasksQueryBuilder->where('task_overdue_reasons.reason_id', $reasonId);
        }

        return [
            'tasks' => $tasksQueryBuilder->get(),
        ];
    }

    public function getFilterData()
    {
        $overdueReasons = $this->overdueReasonRepository->all();

        return [
            'overdueReasons' => $overdueReasons,
        ];
    }
}
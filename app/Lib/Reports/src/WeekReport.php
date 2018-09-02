<?php

namespace App\Lib\Reports\src;

use App\Lib\Reports\AbstractReport;
use App\Repositories\Task\TaskInterface;
use App\Repositories\User\UserInterface;
use Carbon\Carbon;

class WeekReport extends AbstractReport
{
    const NUMBER_OF_WEEKS = 4;

    private $userRepository = null;

    private $taskRepository = null;

    public function __construct()
    {
        $this->userRepository = app()->make(UserInterface::class);
        $this->taskRepository = app()->make(TaskInterface::class);
    }

    public function getReportData(array $requestData)
    {
        $users = $this->getAllUsersInReport();

        for ($weekIndex = 0; $weekIndex < self::NUMBER_OF_WEEKS; $weekIndex++) {
            // Get all tasks for all users that are going to be due in current $weekIndex
            $tasks = $this->getDueTasks($weekIndex);

            foreach ($tasks as $task) {
                if(isset($users[$task->user_id])) {
                    if (!isset($users[$task->user_id]['data'][$weekIndex])) {
                        $users[$task->user_id]['data'][$weekIndex] = 1;
                    } else {
                        $users[$task->user_id]['data'][$weekIndex] += 1;
                    }
                }
            }
        }

        return $users;
    }

    public function getFilterData()
    {
        return [];
    }

    private function getAllUsersInReport()
    {
        $users = [];

        $userModels =  $this->userRepository->model()
            ->where('active', 1)
            ->where('level', '>', 0)
            ->where('level', '<', 6)
            ->orderBy('level', 'ASC')
            ->get();

        foreach ($userModels as $userModel) {
            $users[$userModel->id]['userData'] = [
                'name'  => $userModel->name,
                'level' => $userModel->level,
            ];
        }

        return $users;
    }

    private function getDueTasks(int $weekIndex)
    {
        $startOfWeek = Carbon::parse('this week')->startOfDay()->addWeek($weekIndex);
        $endOfWeek = Carbon::parse($startOfWeek)->addDays(6)->endOfDay();

        $tasks = $this->taskRepository->model()
            ->where('active', 1)
            ->whereNull('completed_at')
            ->whereNotNull('user_id')
            ->where('due_at', '>=', $startOfWeek)
            ->where('due_at', '<=', $endOfWeek)
            ->orderBy('due_at', 'ASC')
            ->get();

        return $tasks;
    }
}

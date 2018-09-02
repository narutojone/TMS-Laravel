<?php

namespace App\Lib\Modules\ReviewCheck;

use App\Lib\Modules\iModules;
use App\Repositories\Review\Review;
use App\Repositories\Subtask\Subtask;
use App\Repositories\Task\Task;
use App\Repositories\TemplateSubtask\TemplateSubtask;
use App\Repositories\TemplateSubtaskModule\TemplateSubtaskModuleInterface;

class ReviewCheck implements iModules
{

    private $id = 4;
    private $moduleName = 'Review check';

    public function validateRequest(TemplateSubtask $templateSubtask, $moduleData)
    {
        return [];
    }

    public function getSettings(TemplateSubtask $subtask)
    {
        return [];
    }

    public function update(TemplateSubtask $subtask, $params)
    {
        $templateSubtaskModuleRepo = app()->make(TemplateSubtaskModuleInterface::class);
        $templateSubtaskModuleRepo->model()->where('subtask_id', $subtask->id)->where('subtask_module_id', $this->id)->delete();

        if (isset($params['active']) && $params['active'] == 'on') {

            $input = [
                'subtask_id' => $subtask->id,
                'subtask_module_id' => $this->id,
                'settings' => json_encode([]),
            ];

            $templateSubtaskModule = $templateSubtaskModuleRepo->create($input);
        }

        return [];
    }

    public function validateUserInput(Subtask $subtask, $moduleData)
    {
        $errors = [];
        return $errors;
    }

    public function processData(Subtask $subtask, $moduleData)
    {
        $returnValue = ['success'=>true, 'errors'=>[]];

        $review = $subtask->task->review;

        if ($review && $review->status == Review::STATUS_PENDING) {
            $returnValue = [
                'success'   => false,
                'errors'    => ["A review must be completed before completing this task!"]
            ];
        }

        return $returnValue;
    }

    private function generateErrorLine($error)
    {
        return 'Module - ' . $this->moduleName . ': ' . $error;
    }
}
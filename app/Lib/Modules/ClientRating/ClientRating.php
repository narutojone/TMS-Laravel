<?php

namespace App\Lib\Modules\ClientRating;

use App\Lib\Modules\iModules;
use App\Repositories\Rating\RatingInterface;
use App\Repositories\Subtask\Subtask;
use App\Repositories\TemplateSubtask\TemplateSubtask;
use App\Repositories\TemplateSubtaskModule\TemplateSubtaskModuleInterface;
use Illuminate\Support\Facades\Validator;

class ClientRating implements iModules
{

    private $id = 5;
    private $moduleName = 'Client rating';

    public function validateRequest(TemplateSubtask $templateSubtask, $moduleData)
    {
        return [];
    }

    public function getSettings(TemplateSubtask $subtask)
    {
        return [
            'rating'   => isset($existingSettings['rating']) ? $existingSettings['rating'] : '',
            'feedback' => isset($existingSettings['feedback']) ? $existingSettings['feedback'] : '',
        ];
    }

    public function update(TemplateSubtask $subtask, $params)
    {
        $templateSubtaskModuleRepo = app()->make(TemplateSubtaskModuleInterface::class);
        $templateSubtaskModuleRepo->model()->where('subtask_id', $subtask->id)->where('subtask_module_id', $this->id)->delete();

        if (isset($params['active']) && $params['active'] == 'on') {

            $input = [
                'subtask_id'        => $subtask->id,
                'subtask_module_id' => $this->id,
                'settings'          => json_encode([]),
            ];

            $templateSubtaskModuleRepo->create($input);
        }

        return [];
    }

    public function validateUserInput(Subtask $subtask, $moduleData)
    {
        $rules = [
            'rating'   => 'required|integer|min:1|max:5',
            'feedback' => 'required_if:rating,1,2',
        ];

        $errors = [];
        $validator = Validator::make($moduleData, $rules, []);
        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $message) {
                $errors[] = $message;
            }
        }

        return $errors;
    }

    public function processData(Subtask $subtask, $moduleData)
    {
        $returnValue = ['success' => true, 'errors' => []];

        $task = $subtask->task;

        $data = [
            'commentable_id'   => $task->user_id,
            'commentable_type' => 'App\Repositories\User\User',
            'ratingable_id'    => $task->client_id,
            'ratingable_type'  => 'App\Repositories\Client\Client',
            'rate'             => $moduleData['rating'],
            'feedback'         => $moduleData['feedback'],
        ];

        $ratingRepository = app()->make(RatingInterface::class);
        $ratingRepository->create($data);

        return $returnValue;
    }

    private function generateErrorLine($error)
    {
        return 'Module - ' . $this->moduleName . ': ' . $error;
    }
}

<?php

namespace App\Lib\Modules\UserTutorial;

use App\Lib\Modules\iModules;
use App\Repositories\Subtask\Subtask;
use App\Repositories\TemplateSubtask\TemplateSubtask;
use App\Repositories\User\User;
use Illuminate\Support\Facades\DB;

class UserTutorial implements iModules
{

    private $id = 2;
    private $moduleName = 'User tutorial';

    public function validateRequest(TemplateSubtask $templateSubtask, $moduleData)
    {
        return [];
    }

    public function getSettings(TemplateSubtask $subtask)
    {
        $module = DB::table('template_subtasks_modules')->where('subtask_id', $subtask->id)->where('subtask_module_id', $this->id)->first();
        $existingSettings = ($module) ? json_decode($module->settings, true) : [];

        return [
            'custom-title' => isset($existingSettings['custom-title']) ? $existingSettings['custom-title'] : '',
        ];
    }

    public function update(TemplateSubtask $subtask, $params)
    {
        $active = (isset($params['active']) && $params['active'] == 'on');
        if (!$active) {
            DB::table('template_subtasks_modules')->where('subtask_id', $subtask->id)->where('subtask_module_id', $this->id)->delete();
        } else {
            $settings = [
                'custom-title' => (isset($params['custom-title'])) ? $params['custom-title'] : '',
            ];

            // Persist data
            $subtaskModule = DB::table('template_subtasks_modules')->where('subtask_id', $subtask->id)->where('subtask_module_id', $this->id)->exists();
            if (!$subtaskModule) {
                DB::table('template_subtasks_modules')->insert(
                    [
                        'subtask_id' => $subtask->id,
                        'subtask_module_id' => $this->id,
                        'settings' => json_encode($settings)
                    ]
                );
            } else {
                DB::table('template_subtasks_modules')->where('subtask_id', $subtask->id)->where('subtask_module_id',$this->id)->update(
                    ['settings' => json_encode($settings)]
                );
            }
        }
        return [];
    }

    public function validateUserInput(Subtask $subtask, $moduleData)
    {
        $errors = [];
        // This module doesn't require user input BUT we need to make sure this is the last subtask completed from the main task template
        $openSubtasks = Subtask::where('task_id', $subtask->task_id)->where('id', '<>', $subtask->id)->whereNull('completed_at')->count();
        if ($openSubtasks > 0) {
            $errors[] = $this->generateErrorLine("This subtask can only be completed after you finish all other subtasks");
        }
        return $errors;
    }

    public function processData(Subtask $subtask, $moduleData)
    {
        $returnValue = ['success'=>true, 'errors'=>[]];
        // Check if user is at level 1 (aka 10) and upgrate it to level 2 (aka 20)
        User::where('id', $subtask->task->user_id)->where('level', 0)->update([
            'level' => 1,
        ]);

        return $returnValue;
    }

    private function generateErrorLine($error)
    {
        return 'Module - ' . $this->moduleName . ': ' . $error;
    }
}
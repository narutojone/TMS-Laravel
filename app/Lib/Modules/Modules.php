<?php

namespace App\Lib\Modules;

use App\Repositories\Subtask\Subtask;
use App\Repositories\TemplateSubtask\TemplateSubtask;
use App\Repositories\SubtaskModuleTemplate\SubtaskModuleTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Modules
{
    const MODULE_FILE_UPLOAD = 1;
    const MODULE_USER_TUTORIAL = 2;
    const MODULE_TASK_GENERATOR = 3;
    const MODULE_REVIEW_CHECK = 4;
    const MODULE_CLIENT_RATING = 5;

    private $modulesClassMap = [];
    private $activeModules = null;

    public function __construct()
    {
        $this->modulesClassMap = [
            1 => FileUpload\FileUpload::class,
            2 => UserTutorial\UserTutorial::class,
            3 => TaskGenerator\TaskGenerator::class,
            4 => ReviewCheck\ReviewCheck::class,
            5 => ClientRating\ClientRating::class,
        ];
    }

    public function getAvailableModules()
    {
        return SubtaskModuleTemplate::all();
    }

    public function getActiveModules(TemplateSubtask $subtaskTemplate)
    {
        // Check if already fetched these values from DB
        if(!is_array($this->activeModules) || is_null($this->activeModules)) {
            $this->activeModules = DB::table('template_subtasks_modules')->where('subtask_id', $subtaskTemplate->id)->pluck('subtask_module_id')->toArray();
        }
        return $this->activeModules;
    }

    public function validateRequest(TemplateSubtask $templateSubtask, Request $request)
    {
        $errors = [];
        $modulesRequest = $request->get('modules');
        foreach ($modulesRequest as $moduleId => $module) {
            if (isset($module['active']) && $module['active'] == 'on') {
                $m = new $this->modulesClassMap[$moduleId];
                $errors = array_merge($errors, $m->validateRequest($templateSubtask, $modulesRequest[$moduleId]));
            }
        }
        return $errors;
    }

    public function getTemplateSettings(TemplateSubtask $subtask)
    {
        $settings = [];
        foreach ($this->modulesClassMap as $moduleId => $moduleClass) {
            $m = new $moduleClass;
            $settings[$moduleId] = $m->getSettings($subtask);
        }

        return $settings;
    }

    public function update(Request $request, TemplateSubtask $subtask)
    {
        $errors = [];
        $requestData = $request->all();
        $modulesRequest = $requestData['modules'];
        foreach ($this->modulesClassMap as $moduleId => $moduleClass) {
            $m = new $moduleClass;
            $errors = array_merge($errors, $m->update($subtask, $modulesRequest[$moduleId]));
        }

        return $errors;
    }

    // Check if subtask requires user input for completion
    public function requiresUserInput(Subtask $subtask)
    {
        // Check if current subtask has a template
        if (!$subtask->template) {
            return false;
        }
        $activeModules = $this->getActiveModules($subtask->template);

        // Check if there is at least 1 module that requires user input
        return SubtaskModuleTemplate::whereIn('id', $activeModules)->where('user_input', 1)->exists();
    }


    public function validateUserInput(Subtask $subtask, $modulesData)
    {
        if (!isset($subtask->template)) {
            return [];
        }
        $errors = [];
        $activeModules = $this->getActiveModules($subtask->template);
        foreach ($this->modulesClassMap as $moduleId => $moduleClass) {
            if (in_array($moduleId, $activeModules)) {
                $m = new $moduleClass;
                $data = isset($modulesData[$moduleId]) ? $modulesData[$moduleId] : null;
                $errors = array_merge($errors, $m->validateUserInput($subtask, $data));
            }
        }
        return $errors;
    }

    public function processData($subtask, $modulesData)
    {
        $returnValue = ['success'=>true, 'errors'=>[]];
        if (!isset($subtask->template)) {
            return $returnValue;
        }
        $activeModules = $this->getActiveModules($subtask->template);
        foreach ($this->modulesClassMap as $moduleId => $moduleClass) {
            if (in_array($moduleId, $activeModules)) {
                $m = new $moduleClass;
                $data = isset($modulesData[$moduleId]) ? $modulesData[$moduleId] : null;
                $moduleResponse = $m->processData($subtask, $data);
                if($moduleResponse['success'] == false) {
                    $returnValue['success'] = false;
                    if (!is_null($moduleResponse)) {
                        $returnValue['errors'] += $moduleResponse['errors'];
                    }
                }
            }
        }
        return $returnValue;
    }
} 
<?php

namespace App\Lib\Modules;

use App\Repositories\Subtask\Subtask;
use App\Repositories\TemplateSubtask\TemplateSubtask;

interface iModules
{
    public function validateRequest(TemplateSubtask $templateSubtask, $moduleData);

    public function getSettings(TemplateSubtask $subtask);

    public function update(TemplateSubtask $subtask, $params);

    public function validateUserInput(Subtask $subtask, $moduleData);

    public function processData(Subtask $subtask, $moduleData);

}
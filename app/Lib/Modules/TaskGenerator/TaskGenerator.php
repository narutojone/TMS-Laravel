<?php
namespace App\Lib\Modules\TaskGenerator;

use App\Lib\Modules\iModules;
use App\Repositories\Group\GroupInterface;
use App\Repositories\Subtask\Subtask;
use App\Repositories\Task\Task;
use App\Repositories\Task\TaskInterface;
use App\Repositories\Template\TemplateInterface;
use App\Repositories\GroupUser\GroupUserInterface;
use App\Repositories\TemplateSubtask\TemplateSubtask;
use App\Repositories\TemplateSubtaskModule\TemplateSubtaskModule;
use App\Repositories\TemplateSubtaskModule\TemplateSubtaskModuleInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TaskGenerator implements iModules
{

    private $id = 3;
    private $moduleName = 'Task Generator';
    private $fileTemplatePath = 'task-generator';

    public function validateRequest(TemplateSubtask $templateSubtask, $moduleData)
    {
        $errors = [];

        if (isset($moduleData['template'])) {
            foreach ($moduleData['template'] as $template) {
                if (is_null($template)) {
                    $errors[] = $this->generateErrorLine('Please select a template.');
                    break;
                }
            }
        }

        if (isset($moduleData['assignee'])) {
            foreach ($moduleData['assignee'] as $assignee) {
                if (is_null($assignee)) {
                    $errors[] = $this->generateErrorLine('Please assign a Manager or Employee.');
                    break;
                }
            }
        }

        if (isset($moduleData['repeating'])) {
            $rules['frequency'] = 'required_if:repeating,1|frequency';

            foreach ($moduleData['repeating'] as $key => $repeating) {

                if ((int)$repeating === TemplateSubtaskModule::REPEATING_YES_VALUE) {
                    $dataToValidate = [];
                    $dataToValidate['frequency'] = $moduleData['frequency'][$key];

                    $validator = Validator::make($dataToValidate, $rules, []);
                    if ($validator->fails()) {
                        $errors[] = $validator->errors()->first('frequency');
                        break;
                    }
                }
            }
        }

        return $errors;
    }

    public function getSettings(TemplateSubtask $subtask)
    {
        $module = DB::table('template_subtasks_modules')->where('subtask_id', $subtask->id)->where('subtask_module_id', $this->id)->first();
        $existingSettings = ($module) ? json_decode($module->settings, true) : [];

        $templates = app()->make(TemplateInterface::class)->all();

        // to avoid modifying the html and javascript for the case when 'assignments' does not exists, we add some empty values by default
        $assignmentsDefaultRow = [];
        $assignmentsDefaultRow[0] = [
            'template' => '',
            'assignee' => '',
            'deadline-offset' => 0,
            'repeating' => TemplateSubtaskModule::REPEATING_NO_VALUE,
            'frequency' => '',
            'private' => Task::NOT_PRIVATE,
        ];

        return [
            'templates' => $templates,
            'custom-title' => isset($existingSettings['custom-title']) ? $existingSettings['custom-title'] : '',
            'assignments' => isset($existingSettings['assignments']) ? $existingSettings['assignments'] : $assignmentsDefaultRow,
            'assignees' => $this->getAssignees(),
            'targets' => TemplateSubtaskModule::TARGETS,
            'repeatingOptions' => TemplateSubtaskModule::REPEATING_OPTIONS,
        ];
    }


    /**
     * Build the data for the select assignee
     *
     * @return array
     */
    protected function getAssignees(): array
    {
        // Build assignees select
        $assignees = TemplateSubtaskModule::ASIGNEES;

        $groupRepository = app()->make(GroupInterface::class);
        $groups = $groupRepository->model()->whereHas('users')->get();

        foreach ($groups as $group) {
            $assignees['group_' . $group->id] = 'Group: ' . $group->name;
        }

        return $assignees;
    }

    public function update(TemplateSubtask $subtask, $params)
    {
        $templateSubtaskModuleRepo = app()->make(TemplateSubtaskModuleInterface::class);
        $templateSubtaskModuleRepo->model()->where('subtask_id', $subtask->id)->where('subtask_module_id', $this->id)->delete();

        if (isset($params['active']) && $params['active'] == 'on') {
            $settings = [];
            $settings['custom-title'] = (isset($params['custom-title'])) ? $params['custom-title'] : '';

            if (isset($params['template'])) {
                $settings['assignments'] = [];

                for ($i = 0; $i < count($params['template']); $i++) {
                    $settings['assignments'][$i] = [];
                    $settings['assignments'][$i]['template'] = $params['template'][$i];
                    $settings['assignments'][$i]['assignee'] = $params['assignee'][$i];
                    $settings['assignments'][$i]['deadline-offset'] = $params['deadline-offset'][$i];
                    $settings['assignments'][$i]['target'] = $params['target'][$i];
                    $settings['assignments'][$i]['repeating'] = $params['repeating'][$i];
                    $settings['assignments'][$i]['frequency'] = $params['frequency'][$i];
                    $settings['assignments'][$i]['private'] = $params['private'][$i];
                }
            }

            $input = [
                'subtask_id' => $subtask->id,
                'subtask_module_id' => $this->id,
                'settings' => json_encode($settings),
            ];

            $templateSubtaskModule = $templateSubtaskModuleRepo->create($input);
        }

        return [];

    }

    public function validateUserInput(Subtask $subtask, $moduleData)
    {
        return [];
    }

    public function processData(Subtask $subtask, $moduleData)
    {
        $returnValue = ['success'=>true, 'errors'=>[]];

        $taskRepository = app()->make(TaskInterface::class);
        $settings = $this->getSettings($subtask->template);

        if (!$subtask->isReopened() && isset($settings['assignments']) && (is_array($settings['assignments']))) {

            foreach ($settings['assignments'] as $assignment) {

                // Fetch template from assignment
                $template = app()->make(TemplateInterface::class)->model()->find($assignment['template']);

                if ($assignment['assignee'] == TemplateSubtaskModule::MANAGER) {
                    $userId = $subtask->task->client->manager->id;
                } elseif ($assignment['assignee'] == TemplateSubtaskModule::EMPLOYEE) {
                    $userId = $subtask->task->client->employee->id;
                } elseif (strpos($assignment['assignee'], 'group_') !== false) {
                    $userId = null;
                    $groupId = str_replace('group_', '', $assignment['assignee']);

                    // Find user based on group
                    $groupUserRepository = app()->make(GroupUserInterface::class);
                    $groupUser = $groupUserRepository->model()->where('group_id', $groupId)->inRandomOrder()->first();
                    if ($groupUser) {
                        $userId = $groupUser->user_id;
                    }
                }

                $deadline = $subtask->task->deadline->addDays($assignment['deadline-offset'])->format('Y-m-d H:i:s');
                $clientId = $this->determineClientId($assignment['target'], $subtask);

                $repeating = (isset($assignment['repeating']) && $assignment['repeating']) ? $assignment['repeating'] : Task::NOT_REPEATING;
                $frequency = (isset($assignment['frequency']) && $assignment['frequency']) ? $assignment['frequency'] : NULL;
                $private = (isset($assignment['private']) && $assignment['private']) ? $assignment['private'] : Task::NOT_PRIVATE;

                $data = [
                    'template_id'   => $assignment['template'],
                    'client_id'     => $clientId,
                    'user_id'       => $userId,
                    'repeating'     => $repeating,
                    'frequency'     => $frequency,
                    'deadline'      => $deadline,
                    'private'       => $private,
                ];

                if (!is_null($clientId)) {
                    $task = $taskRepository->create($data);
                }
            }
        }

        return $returnValue;
    }

    /**
     * Determine client_id for the task that we are going to create.
     *
     * @param string $target
     * @param Subtask $subtask
     * @return null|int
     */
    protected function determineClientId(string $target, Subtask $subtask)
    {
        $user = $subtask->task->client->employee()->first();

        if ($target == TemplateSubtaskModule::TARGET_INTERNAL_PROJECT) {
            if ($user->internalClient()->exists()) {
                return $user->client_id;
            } else {
                return null;
            }
        }

        return $subtask->task->client_id;
    }

    private function generateErrorLine($error)
    {
        return 'Module - ' . $this->moduleName . ': ' . $error;
    }

}
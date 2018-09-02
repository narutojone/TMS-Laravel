<?php

namespace App\Repositories\TemplateSubtask;

use App\Repositories\BaseEloquentRepository;
use App\Repositories\Subtask\SubtaskInterface;
use App\Repositories\Task\TaskInterface;
use App\Repositories\TasksUserAcceptance\TasksUserAcceptance;
use App\Repositories\TasksUserAcceptance\TasksUserAcceptanceInterface;
use App\Repositories\TemplateSubtask\TemplateSubtask;
use App\Repositories\TemplateSubtask\TemplateSubtaskInterface;
use App\Repositories\TemplateSubtaskVersion\TemplateSubtaskVersionInterface;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * THe eloquent element in a repository should contain all data manipulation related to a enetity
 */
class EloquentRepositoryTemplateSubtask extends BaseEloquentRepository implements TemplateSubtaskInterface
{
    /**
     * @var $model
     */
    protected $model;

    /**
     * EloquentRepositoryTemplateSubtask constructor.
     *
     * @param TemplateSubtask $model
     */
    public function __construct(TemplateSubtask $model)
    {
        parent::__construct();

        $this->model = $model;
    }

    /**
     * Create a new TemplateSubtask.
     *
     * @param array $input
     * @return TemplateSubtask
     * @throws ValidationException
     */
    public function create(array $input)
    {
        $input = $this->prepareCreateData($input);

        if (!$this->isValid('create', $input)) {
            throw new ValidationException($this->validators['create']);
        }

        DB::beginTransaction();
        $templateSubtask = $this->model->create($input);

        // Create a version/revision for the new TemplateSubtask
        $templateSubtaskVersionRepository = app()->make(TemplateSubtaskVersionInterface::class);
        $templateSubtaskVersionRepository->create([
            'subtask_template_id' => $templateSubtask->id,
            'version_no'          => 1,
            'created_by'          => Auth::user()->id,
            'title'               => $input['title'],
            'description'         => $input['description'],
        ]);

        // Add the new subtask template to all existing tasks (if needed)
        if ((int)$input['add-to-tasks'] == 1) {
            $this->addSubtaskTemplateToExistingTasks($templateSubtask, $input);
        }

        DB::commit();
        return $templateSubtask;
    }

    /**
     * Update a TemplateSubtask.
     *
     * @param integer $id
     * @param array $input
     * @return TemplateSubtask
     * @throws ValidationException
     */
    public function update($id, array $input)
    {
        $input = $this->prepareUpdateData($input);

        if (!$this->isValid('update', $input)) {
            throw new ValidationException($this->validators['update']);
        }

        DB::beginTransaction();

        $templateSubtask = $this->find($id);
        if ($templateSubtask) {
            $templateSubtask->fill($input);
            $templateSubtask->save();

            $subtaskRepository = app()->make(SubtaskInterface::class);
            $templateSubtaskVersionRepository = app()->make(TemplateSubtaskVersionInterface::class);

            if ((int)$input['version'] == 1) { // Create a new version/revision
                $newVersionNo = (int)$templateSubtask->versions->first()->version_no + 1;
                $templateSubtaskVersionRepository->create([
                    'subtask_template_id' => $templateSubtask->id,
                    'version_no'          => $newVersionNo,
                    'created_by'          => Auth::user()->id,
                    'title'               => $input['title'],
                    'description'         => $input['description'],
                ]);

                // Update all non completed subtasks with the new version number (skip those which were reopened)
                $subtaskRepository->model()->uncompleted()->where('subtaskTemplateId', $templateSubtask->id)->doesntHave('reopenings')->update([
                    'version_no' => $newVersionNo,
                    'title'      => $templateSubtask->title,
                ]);

            } else { // Update existing version
                $dataToBeUpdated = [];

                if(isset($input['description'])) {
                    $dataToBeUpdated['description'] = $input['description'];
                }
                if(isset($input['title'])) {
                    $dataToBeUpdated['title'] = $input['title'];

                    // Update title on all non completed subtasks
                    $subtaskRepository->model()->uncompleted()->where('subtaskTemplateId', $templateSubtask->id)->update([
                        'title'      => $templateSubtask->title,
                    ]);
                }

                if(!empty($dataToBeUpdated)) {
                    $templateSubtaskVersionId = $templateSubtask->versions->first()->id;
                    $templateSubtaskVersionRepository->update($templateSubtaskVersionId, $dataToBeUpdated);
                }
            }

            DB::commit();
            return $templateSubtask;
        }

        throw new ModelNotFoundException('Model TemplateSubtask not found.', 404);
    }

    /**
     * Deactivate a subtask template
     *
     * @param $id
     * @param array $input
     */
    public function deactivate($id, array $input)
    {
        $templateSubtask = $this->find($id);
        if (!$templateSubtask) {
            throw new ModelNotFoundException('Model TemplateSubtask not found.', 404);
        }

        DB::beginTransaction();

        // Move order numbers
        $templateSubtask->template->subtasks()->where('order', '>', $templateSubtask->order)->each(function ($subtask) {
            $subtask->update(['order' => $subtask->order - 1]);
        });

        // Deactivate the subtask
        $templateSubtask->update([
            'active' => 0,
        ]);

        // Remove subtasks which use this template (if needed)
        if (isset($input['add-to-tasks']) && (int)$input['add-to-tasks'] == 1) {
            $this->removeSubtaskTemplateFromExistingTasks($templateSubtask, $input);
        }

        DB::commit();
    }

    /**
     * Prepare data for update action.
     *
     * @param array $data
     * @return array
     */
    protected function prepareUpdateData(array $data): array
    {
        if(!isset($data['version'])) {
            $data['version'] = 0;
        }

        if(array_key_exists('description', $data) && is_null($data['description'])) {
            $data['description'] = '';
        }

        return $data;
    }

    /**
     * Prepare data for create.
     *
     * @param array $data
     * @return array
     */
    protected function prepareCreateData(array $data): array
    {
        if (!isset($data['add-to-tasks']) || is_null($data['add-to-tasks'])) {
            $data['add-to-tasks'] = 0;
        }

        if (!isset($data['min-date'])) {
            $data['min-date'] = null;
        }

        if(!isset($data['description']) || empty($data['description'])) {
            $data['description'] = '';
        }

        if (!isset($data['order']) || is_null($data['order'])) {
            // Get the currently highest order number under the template
            $highest = $this->model->where('template_id', $data['template_id'])->orderBy('order', 'desc')->first();

            // Set the new subtask's order number to the highest plus 1
            // if the template didn't have any tasks, set the order number to 1
            $data['order'] = ($highest) ? $highest->order + 1 : 1;
        }

        $data['active'] = 1;
        return $data;
    }

    /**
     * Add subtask to existing tasks
     *
     * @param TemplateSubtask $templateSubtask
     * @param array $input
     */
    protected function addSubtaskTemplateToExistingTasks($templateSubtask, $input)
    {
        $taskRepository = app()->make(TaskInterface::class);
        $tasks = $taskRepository->make()->uncompleted()->doesntHave('reopenings')->where('template_id', $templateSubtask->template->id);
        if (!is_null($input['min-date'])) {
            $tasks->where('deadline', '>', Carbon::parse($input['min-date']));
        }
        $tasks = $tasks->get();

        foreach ($tasks as $task) {
            $task->subtasks()->create([
                'subtaskTemplateId' => $templateSubtask->id,
                'order'             => $templateSubtask->order,
                'title'             => $templateSubtask->title,
                'version_no'        => $templateSubtask->versions->first()->version_no,
                'user_id'           => $task->user_id,
            ]);
        }
    }

    /**
     * Remove subtask from existing tasks
     *
     * @param TemplateSubtask $templateSubtask
     * @param array $input
     */
    protected function removeSubtaskTemplateFromExistingTasks($templateSubtask, $input)
    {
        $taskRepository = app()->make(TaskInterface::class);

        $tasks = $taskRepository->make()->uncompleted()->where('template_id', $templateSubtask->template->id);
        if (!is_null($input['min-date'])) {
            $tasks->where('deadline', '>', Carbon::parse($input['min-date']));
        }
        $tasks = $tasks->get();

        foreach ($tasks as $task) {
            foreach ($task->subtasks as $subtask) {
                if ($subtask->subtaskTemplateId == $templateSubtask->id) {
                    $subtask->delete();
                    break;
                }
            }
        }
    }

}
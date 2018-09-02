<?php
 
namespace App\Repositories\GroupTemplate;

use App\Repositories\BaseEloquentRepository;
use App\Repositories\Group\Group;
use App\Repositories\Template\Template;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * THe eloquent element in a repository should contain all data manipulation related to a enetity
 */
class EloquentRepositoryGroupTemplate extends BaseEloquentRepository implements GroupTemplateInterface
{
    /**
     * @var $model
     */
    protected $model;

    /**
     * EloquentRepositoryGroupTemplate constructor.
     *
     * @param GroupTemplate $model
     */
    public function __construct(GroupTemplate $model)
    {
        parent::__construct();

        $this->model = $model;
    }

    /**
     * Create a new GroupTemplate.
     *
     * @param array $input
     *
     * @return GroupTemplate
     * @throws ValidationException
     */
    public function create(array $input)
    {
        if(!$this->isValid('create', $input)) {
            throw new ValidationException($this->validators['create']);
        }
        return $this->model->create($input);
    }

    /**
     * Add multiple groups to template
     *
     * @param array $groups
     * @param int $templateId
     * @return array
     * @throws ValidationException
     */
    public function batchCreate(array $groups, int $templateId)
    {
        $groupTemplates = [];

        foreach ($groups as $group) {
            $data = [
                'group_id'      =>  $group,
                'template_id'   =>  $templateId,
            ];

            // check if the combination template group already exists into the database, and if not assign the group to the template
            $groupTemplate = $this->make()->where($data)->get()->toArray();

            if (!$groupTemplate) {
                $groupTemplateCreated = $this->create($data);
                array_push($groupTemplates, $groupTemplateCreated);
            }
        }

        return $groupTemplates;
    }

    /**
     * Update a GroupTemplate.
     *
     * @param integer $id
     * @param array $input
     *
     * @return \Illuminate\Database\Eloquent\Model
     * @throws ValidationException
     */
    public function update($id, array $input)
    {
        if(!$this->isValid('update', $input)) {
            throw new ValidationException($this->validators['update']);
        }

        $groupTemplate = $this->find($id);
        if ($groupTemplate) {
            $groupTemplate->fill($input);
            $groupTemplate->save();
            return $groupTemplate;
        }

        throw new ModelNotFoundException('Model GroupTemplate not found.', 404);
    }

    /**
     * Delete a GroupTemplate.
     *
     * @param Template $template
     * @param Group $group
     * @return bool
     */
    public function deleteTemplateGroup(Template $template, Group $group)
    {
        $groupTemplate = $this->model->where([
            'group_id' => $group->id,
            'template_id'   => $template->id,
        ])->first();

        if (!$groupTemplate) {
            return false;
        }

        $groupUsers = $group->users()->pluck('id')->toArray();

        try {
            DB::beginTransaction();
            $template->tasks()->uncompleted()->whereIn('user_id', $groupUsers)->update(['user_id' => null]);
            $template->groups()->detach($group);

        } catch (\Exception $e) {

            app('log')->error($e->getMessage());
            DB::rollBack();

            return false;
        }

        DB::commit();
        return true;
    }
}
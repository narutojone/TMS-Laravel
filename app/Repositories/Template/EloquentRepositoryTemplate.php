<?php
 
namespace App\Repositories\Template;

use App\Repositories\BaseEloquentRepository;
use App\Repositories\Task\TaskInterface;
use App\Repositories\TasksUserAcceptance\TasksUserAcceptanceInterface;
use App\Repositories\TemplateSubtask\TemplateSubtaskInterface;
use App\Repositories\TemplateVersion\TemplateVersion;
use App\Repositories\TemplateVersion\TemplateVersionInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * The eloquent element in a repository should contain all data manipulation related to a enetity
 */
class EloquentRepositoryTemplate extends BaseEloquentRepository implements TemplateInterface
{
    /**
     * @var $model
     */
    protected $model;

    /**
     * EloquentRepositoryTemplate constructor.
     *
     * @param Template $model
     */
    public function __construct(Template $model)
    {
        parent::__construct();
        
        $this->model = $model;
    }

    /**
     * Create a new Template.
     *
     * @param array $input
     * @return Template
     * @throws ValidationException
     * @throws \Exception
     */
    public function create(array $input)
    {
        $input = $this->prepareCreateData($input);

        if(!$this->isValid('create', $input)) {
            throw new ValidationException($this->validators['create']);
        }
        
        DB::beginTransaction();
        try {
            // Create the template
            $template = $this->model->create($input);

            // Create a new version for the template
            $templateVersionRepository = app()->make(TemplateVersionInterface::class);
            $templateVersionRepository->create([
                'template_id' => $template->id,
                'version_no' => 1,
                'created_by' => Auth::user()->id,
                'title' => $input['title'],
                'description' => $input['description'],
            ]);
            DB::commit();
            return $template;
        }
        catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Update a Template.
     *
     * @param integer $id
     * @param array $input
     * @return Template
     * @throws ValidationException
     */
    public function update($id, array $input)
    {
        $input = $this->prepareUpdateData($input);

        if(!$this->isValid('update', $input)) {
            throw new ValidationException($this->validators['update']);
        }

        $template = $this->find($id);
        if ($template) {
            DB::beginTransaction();
            $template->fill($input);
            $template->save();

            $taskRepository = app()->make(TaskInterface::class);
            $templateVersionRepository = app()->make(TemplateVersionInterface::class);

            if((int)$input['version'] == 1) { // Create a new version
                // Create the new version
                $newVersionNo = (int)$template->versions->first()->version_no + 1;
                $templateVersionRepository->create([
                    'template_id' => $id,
                    'version_no'  => $newVersionNo,
                    'created_by'  => Auth::user()->id,
                    'title'       => $input['title'],
                    'description' => $input['description'],
                ]);

                // Update all non completed tasks with the new version (skip those which were reopened)
                $taskRepository->model()->uncompleted()->where('template_id', $template->id)->doesntHave('reopenings')->update([
                    'version_no' => $newVersionNo,
                    'title'      => $template->title,
                ]);
            }
            else { // Update the existing version
                $dataToBeUpdated = [];

                if(isset($input['title'])) {
                    $dataToBeUpdated['title'] = $input['title'];

                    // Update title on all non completed tasks
                    $taskRepository->model()->uncompleted()->where('template_id', $template->id)->update([
                        'title'      => $input['title'],
                    ]);
                }
                if(isset($input['description'])) {
                    $dataToBeUpdated['description'] = $input['description'];
                }

                if(!empty($dataToBeUpdated)) {
                    $templateVersionId = $template->versions->first()->id;
                    $templateVersionRepository->update($templateVersionId, $dataToBeUpdated);
                }
            }

            DB::commit();
            return $template;
        }
        throw new ModelNotFoundException('Model Template not found', 404);
    }

    public function duplicate($id)
    {
        $existingTemplate = $this->find($id);
        if ($existingTemplate) {
            DB::beginTransaction();

            // Replicate the existing template and add (Copy) to title
            $newTemplate = $existingTemplate->replicate();
            $newTemplate->title .= " (Copy)";
            $newTemplate->push();

            // Create a version for the new template
            $versionAttributes = $existingTemplate->versions()->first()->attributesToArray();
            $versionAttributes['version_no'] = 1;
            $versionAttributes['created_by'] = Auth::user()->id;
            $newTemplate->versions()->create($versionAttributes);

            // Copy subtasks from existing template and replicate them to the new created template
            $templateSubtaskRepository = app()->make(TemplateSubtaskInterface::class);
            foreach ($existingTemplate->subtasks()->get() as $templateSubtask) {
                $templateSubtaskRepository->create([
                    'template_id' => $newTemplate->id,
                    'order'       => $templateSubtask->order,
                    'title'       => $templateSubtask->title,
                    'description' => $templateSubtask->versions->first()->description,
                ]);
            }
            DB::commit();

            return $newTemplate;
        }
        throw new ModelNotFoundException('Model Template not found', 404);
    }
 
    /**
     * Delete a Template.
     *
     * @param integer $id
     *
     * @return boolean
     */
    public function delete($id)
    {
        $template = $this->model->find($id);
        if (!$template) {
            throw new ModelNotFoundException('Model Template not found.', 404);
        }
        $template->delete();
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

        return $data;
    }

    /**
     * Prepare data for create action.
     *
     * @param array $data
     * @return array
     */
    protected function prepareCreateData(array $data): array
    {
        return $data;
    }

    /**
     * Get a list of templates filtered
     *
     * @param Request $request
     * 
     * @return \Illuminate\Database\Eloquent\Builder - Laravel eloquent query builder with filters applied
     */
    public function getFilteredTemplates(Request $request)
    {
        $query = $this->model->query();
        
        // FIlter only active templates
        $query->where("active", 1);

         // Filter by category
         if ($request->filled('category')) {
            $query->where('category', $request->input('category'));
        }
        
        // Filter by search term (only title)
        if ($request->filled('search')) {
            $query->where('title', 'LIKE', '%' . $request->input('search') . '%');
        }

        return $query;
    }

    /**
     * Generate the url path for the current page, with filters applied
     *
     * @param Request $request
     * @return string
     */
    public function generatePagePathWithFilterParams(Request $request)
    {
        // Generate the page path with filter parameters
       return url()->current().'?category='.$request->input('category').'&search='.$request->input('search');                
    }

    /**
     * Get existing categories for templates as a valid json array
     *
     * @return string
     */
    public function getExistingCategories()
    {
        $categories =  $this->model
            ->groupBy('category')
            ->select('category')
            ->get()
            ->implode('category', '", "');

        $categories = '["' .$categories. '"]';    

        return $categories;
    } 
}
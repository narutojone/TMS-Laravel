<?php

namespace App\Http\Controllers;

use App\Lib\Modules\Modules;
use App\Repositories\Subtask\SubtaskInterface;
use App\Repositories\SubtaskModuleTemplate\SubtaskModuleTemplate;
use App\Repositories\Task\TaskInterface;
use App\Repositories\TemplateSubtask\TemplateSubtaskCreateRequest;
use App\Repositories\TemplateSubtask\TemplateSubtaskDeactivateRequest;
use App\Repositories\TemplateSubtask\TemplateSubtaskInterface;
use App\Repositories\TemplateSubtask\TemplateSubtaskUpdateRequest;
use Illuminate\Http\Request;
use App\Repositories\Template\Template;
use App\Repositories\TemplateSubtask\TemplateSubtask;
use Illuminate\Support\Facades\DB;

class TemplateSubtaskController extends Controller
{
    /**
     * @var $templateSubtaskRepository - EloquentRepositoryTemplateSubtask
     */
    private $templateSubtaskRepository;

    /**
     * Instantiate a new controller instance.
     *
     * @param TemplateSubtaskInterface $templateSubtaskRepository
     */
    public function __construct(TemplateSubtaskInterface $templateSubtaskRepository)
    {
        $this->middleware('admin_only')->except('show');
        parent::__construct();

        $this->templateSubtaskRepository = $templateSubtaskRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \App\Repositories\Template\Template  $template
     * @return \Illuminate\Http\Response
     */
    public function index(Template $template)
    {
        // Return the subtasks as paginated JSON for the API
        return $template->subtasks()->where('active', 1)->paginate(25);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param  \App\Repositories\Template\Template  $template
     * @return \Illuminate\Http\Response
     */
    public function create(Template $template)
    {
        // Return the create form with the template
        return view('templates.subtasks.create')->with([
            'template' => $template,
        ]);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param TemplateSubtaskCreateRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(TemplateSubtaskCreateRequest $request, Template $template)
    {
        $data = $request->all();
        $data['template_id'] = $template->id;
        $templateSubtask = $this->templateSubtaskRepository->create($data);

        return redirect()
            ->action('TemplateController@show', $template)
            ->with('success', 'Subtask created.');
    }

    /**
     * Display the specified resource.
     *
     * @param  TemplateSubtask $templateSubtask
     * @return \Illuminate\Http\Response
     */
    public function show(TemplateSubtask $templateSubtask)
    {
        // Return show view for the subtask
        return view('templates.subtasks.show')->with([
            'subtask' => $templateSubtask,
            'modules' => SubtaskModuleTemplate::all(),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  TemplateSubtask $templateSubtask
     * @return \Illuminate\Http\Response
     */
    public function edit(TemplateSubtask $templateSubtask)
    {
        $modules = new Modules();

        // Return the edit form with the subtask
        return view('templates.subtasks.edit')->with([
            'subtask'                 => $templateSubtask,
            'modules'                 => $modules->getAvailableModules(),
            'activeModules'           => $modules->getActiveModules($templateSubtask),
            'modulesTemplateSettings' => $modules->getTemplateSettings($templateSubtask),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  TemplateSubtaskUpdateRequest $request
     * @param TemplateSubtask $templateSubtask
     * @return \Illuminate\Http\Response
     */
    public function update(TemplateSubtaskUpdateRequest $request, TemplateSubtask $templateSubtask)
    {
        // Validate modules input
        $modules = new Modules();
        $errors = $modules->validateRequest($templateSubtask, $request);
        if(!empty($errors)) {
            return redirect()->back()->withErrors($errors);
        }

        // Update TemplateSubtask
        $this->templateSubtaskRepository->update($templateSubtask->id, $request->all());

        // Update modules
        $modules->update($request, $templateSubtask);

        return redirect()
            ->action('TemplateController@show', $templateSubtask->template)
            ->with('success', 'Subtask updated.');
    }

    public function showDeactivationSettings(Request $request, TemplateSubtask $templateSubtask)
    {
        return view('templates.subtasks.deactivate')->with([
            'templateSubtask'   => $templateSubtask,
        ]);
    }

    /**
     * Deactivate subtask template
     *
     * @param  TemplateSubtaskDeactivateRequest  $request
     * @param  \App\Repositories\TemplateSubtask\TemplateSubtask  $templateSubtask
     * @return \Illuminate\Http\Response
     */
    public function deactivate(TemplateSubtaskDeactivateRequest $request, TemplateSubtask $templateSubtask)
    {
        $this->templateSubtaskRepository->deactivate($templateSubtask->id, $request->all());

        return redirect()
            ->action('TemplateController@show', $templateSubtask->template)
            ->with('info', 'Subtask template deactivated.');
    }


    /**
     * Update order for all subtask templates
     * The order is also updated on all related active subtasks
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function sort(Request $request)
    {
        // validate the input
        $this->validate($request, [
            'items'      => 'required|array',
        ]);

        $items = $request->get('items');

        if ($items) {
            DB::beginTransaction();
            try {
                // generated array where keys are sort indexes, and values are subtask ids
                $sortData = array_combine(range(1, count($items)), $items);
                $subtaskRepository = app()->make(SubtaskInterface::class);

                // update sorting order for each subtask for specified template
                foreach ($sortData as $templateSubtaskOrder => $templateSubtaskId) {
                    // Update subtask template
                    $this->templateSubtaskRepository->update($templateSubtaskId, [
                        'order' => $templateSubtaskOrder,
                    ]);

                    // Update subtasks for uncompleted and non-reopened tasks
                    $subtaskRepository->model()->where('subtaskTemplateId', $templateSubtaskId)->whereHas('task', function ($query) {
                        $query->uncompleted()->doesntHave('reopenings');
                    })->update([
                        'order' => $templateSubtaskOrder,
                    ]);
                }
                DB::commit();
            } catch (\Exception $e) {
                DB::rollback();
                throw $e;
            }
        }

        return response()->json(['order' => $items]);
    }
}

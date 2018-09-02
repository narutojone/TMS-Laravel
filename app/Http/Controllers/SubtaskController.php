<?php

namespace App\Http\Controllers;

use App\Lib\Modules\Modules;
use App\Repositories\Task\TaskInterface;
use App\Repositories\TasksUserAcceptance\TasksUserAcceptance;
use App\Repositories\TasksUserAcceptance\TasksUserAcceptanceInterface;
use App\Repositories\TemplateSubtaskVersion\TemplateSubtaskVersionInterface;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Repositories\Subtask\Subtask;
use App\Repositories\Task\Task;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class SubtaskController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Repositories\Task\Task $task
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function index(Request $request, Task $task)
    {
        return $task->subtasks()->orderBy('order')->paginate(25);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return void
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return void
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Repositories\Subtask\Subtask  $subtask
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, Subtask $subtask)
    {
        // Return the subtask as JSON for the API
        if ($request->wantsJson()) {
            return $subtask;
        }

        // Return the show view with the client, subtasks and task itself
        return view('subtasks.show')
            ->withClient($subtask->task->client)
            ->withSubtask($subtask)
            ->withTask($subtask->task);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return void
     */
    public function destroy($id)
    {
        //
    }

    /**
     * Mark the subtask as completed.
     *
     * @param  \App\Repositories\Subtask\Subtask $subtask
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function completed(Subtask $subtask, Request $request)
    {
        // Subtask can't be completed if there are changes that need to be reviewed
        if(!$subtask->changesAccepted()) {
            return redirect()->back()->with('error', 'You need to accept changes before you can complete the subtask');
        }

        // TODO - bug on $request->get('modules'); returns false even in 'modules' is present withing the request; That's why i've used '$requestData';
        // TODO - We should fix this in the near future
        $requestData = $request->all();

        // Check if subtask requires user input before completion
        $modules = new Modules();
        if($modules->requiresUserInput($subtask)) {
            if (!isset($requestData['modules'])) {
                return redirect()->action('SubtaskController@renderCompletionPage', $subtask);
            }
        }

        $data = isset($requestData['modules']) ? $requestData['modules'] : [];

        // Validate input for modules
        $errors = $modules->validateUserInput($subtask, $data);
        if (!empty($errors)) {
            return redirect()->action('SubtaskController@renderCompletionPage', $subtask)->withErrors($errors);
        }

        DB::beginTransaction();
        try {
            $modulesResponse = $modules->processData($subtask, $data);
            if($modulesResponse['success'] == true ) {
                DB::commit();
                return $this->completeSubtask($subtask, $request);
            }
            else {
                DB::rollback();
                return redirect()->back()->withErrors($modulesResponse['errors']);
            }
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }


    }

    public function reviewChanges(Request $request, Subtask $subtask)
    {
        // Get the latest accepted subtask version (for comparison)
        $taskUserAcceptanceRepository = app()->make(TasksUserAcceptanceInterface::class);
        $latestAcceptedVersion = $taskUserAcceptanceRepository->model()
            ->where('type', 2)
            ->where('user_id', $request->user()->id)
            ->where('template_id', $subtask->subtaskTemplateId)
            ->where('version_no', '<>', $subtask->version->version_no)
            ->orderBy('version_no', 'DESC')
            ->first();

        if($latestAcceptedVersion) {
            $noChanges = false;
            $oldVersionDescription = null;

            // Get details of latest accepted version
            $templateSubtaskVersionRepository = app()->make(TemplateSubtaskVersionInterface::class);
            $oldVersion = $templateSubtaskVersionRepository->model()
                ->where('subtask_template_id', $subtask->template->id)
                ->where('version_no', $latestAcceptedVersion->version_no)
                ->first();

            $oldVersionDescription = $oldVersion ? $oldVersion->description : null;
        }
        else {
            $noChanges = true;
        }

        // Prepare data for view
        $currentVersionDetails = $oldVersionDetails = [
            'title'       => $subtask->title,
            'description' => $subtask->version->description,
        ];
        if(!$noChanges) {
            $oldVersionDetails['description'] = $oldVersionDescription;
        }

        return view('subtasks.review-changes')->with([
            'subtask'               => $subtask,
            'noChanges'             => $noChanges,
            'tasksoverdue'          => $request->user()->hasOverdueTasks(), // Set from overdue page if present
            'currentVersionDetails' => $currentVersionDetails,
            'oldVersionDetails'     => $oldVersionDetails,
        ]);
    }

    public function acceptReviewedChanges(Request $request, Subtask $subtask)
    {
        // Save logs for this action
        DB::table('logs_subtask_user_acceptance')->insert([
          'user_id'               => $request->user()->id,
          'subtask_template_id'   => $subtask->template->id,
                'version_no'            => $subtask->version_no,
          'terms_accepted'        => json_encode(['title'=>$subtask->title, 'description'=>$subtask->version->description]),
        ]);

        // Mark the template as accepted
        TasksUserAcceptance::insert([
            'type'        => 2,
            'user_id'     => $request->user()->id,
            'template_id' => $subtask->template->id,
            'version_no'  => $subtask->version_no,
        ]);

        return redirect()
            ->action('TaskController@show', $subtask->task)
            ->with('success', 'Changes accepted!.');
    }

    public function renderCompletionPage(Subtask $subtask, Request $request)
    {
        $modules = new Modules();

        //Set from overdue page if present
        $request->user()->hasOverdueTasks() ? $tasksoverdue = 1 : $tasksoverdue = 0;

        return view('subtasks.complete')->with([
            'subtask'       => $subtask,
            'modules'       => $modules->getAvailableModules(),
            'activeModules' => $subtask->template ? $modules->getActiveModules($subtask->template) : [],
            'settings'      => $modules->getTemplateSettings($subtask->template),
            'tasksoverdue'  => $tasksoverdue,
        ]);
    }

    private function completeSubtask(Subtask $subtask, Request $request)
    {
        if ( ! $subtask->user_id)
        {
            $subtask->user_id = $subtask->task->user_id;
        }
        $subtask->completed_at = Carbon::now(); // Save the current time as the completed time
        $subtask->save();

        // Mark main task as completed if all subtasks are done
        $activeSubtasksCount = Subtask::where('task_id', $subtask->task->id)->whereNull('completed_at')->count();
        if($activeSubtasksCount == 0) {
            $taskRepository = app()->make(TaskInterface::class);
            $taskRepository->markTaskAsCompleted($subtask->task);
            // Redirect back to the parent task with a success flash message
            if ($request->has('overdue')) {
                return redirect()
                    ->action('TaskController@showOverdue')
                    ->with('success', 'Task completed.');
            }
            return redirect()
                ->action('ClientController@show', $subtask->task->client)
                ->with('success', 'Task completed.');

        }
        else {
            // Redirect back to the parent task with a success flash message
            if ($request->has('overdue') || $request->user()->hasOverdueTasks()) {
                return redirect()
                    ->action('TaskController@showOverdue')
                    ->with(['success' => 'Subtask completed.', 'complete_task' => Task::where('id', $subtask->task_id)->first()->id]);
            }
            return redirect()
                ->action('TaskController@show', $subtask->task)
                ->with('success', 'Subtask completed.');
        }
    }
}

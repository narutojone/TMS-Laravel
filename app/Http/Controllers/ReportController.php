<?php

namespace App\Http\Controllers;

use App\Lib\Reports\Reports;
use App\Repositories\Report\Report;
use App\Repositories\User\User;
use App\Repositories\Task\Task;
use App\Repositories\Client\Client;
use App\Repositories\OverdueReason\OverdueReason;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('admin_only', ['except' =>['index']]);
        parent::__construct();
    }

    public function show(Request $request, Report $report)
    {
        // Authorize request
        $this->authorize('view', $report);

        $report = new Reports($report, $request);
        return $report->render();
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
		// We are using this select because we need to sort results in the view based on the difference between 'customer_capacity' and 'clients count'
        $users = User::with(['customerTypes','systems','taskTypes'])
                ->selectRaw('users.* , users.customer_capacity-count(clients.employee_id) AS clientSlots')
                ->join('clients',  'users.id', '=', 'clients.employee_id')
				->where('users.active', 1)
				->where('users.out_of_office', 0)
                ->where('users.id','<>', 24)
                ->where('users.level', '!=', 0)
                ->whereNotNull('users.pf_id')
				->groupBy('users.id')
                ->orderBy('users.level')
				->orderByRaw('clientSlots DESC')
                ->get();

        $flagged = collect();

        $users->each(function ($user, $key) use ($users, $flagged) {
            if($user->hasFlags() && $user->lastFlag()) {
                $flagged->push($user);
                $users->forget($key);
            }
        });

        $users = $users->merge($flagged);

        // Set average client time per month and calculate it to weeks
        $clientAverageTime = 3 / 4.34524;

        return view('reports.capacity.index')->with([
            'users' => $users,
            'clientAverageTime' => $clientAverageTime,
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function overdueTaskPerClient(Request $request)
    {
        // Fetch data
        $clients = DB::table('task_overdue_reasons')
            ->select(DB::raw('
                clients.id as client_id,
                clients.name as client_name,
                clients.paid as paid,
                clients.paused as paused,
                clients.complaint_case as complaint_case,
                clients.active as active,
                users.id as user_id,
                users.name as user_name,
                count(DISTINCT tasks.id) as task_count,
                count(*) as overdue_count'))
            ->join('tasks', 'tasks.id', '=', 'task_overdue_reasons.task_id')
            ->join('clients', 'clients.id', '=', 'tasks.client_id')
            ->join('users', 'users.id', '=', 'clients.employee_id')
            ->whereNull('tasks.completed_at')
            ->where('tasks.deadline', '<', Carbon::now())
            ->groupBy('clients.id')
            ->orderBy('overdue_count', 'DESC')
            ->get();

        // Filter result
        $clients = $clients->where('overdue_count', '>=', '4')->where('task_count', '>=', '4');

        return view('reports.client.overdue')->with([
            'clients' => $clients,
        ]);
    }
    
    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function overdueTask(Request $request)
    {
        // Fetch data
        $tasks = DB::table('task_overdue_reasons')
            ->select(DB::raw('tasks.id as task_id, tasks.title as task_title, tasks.deadline as task_deadline, clients.id as client_id, clients.name as client_name, users.id as user_id, users.name as user_name, count(*) as overdue_count'))
            ->join('tasks', 'tasks.id', '=', 'task_overdue_reasons.task_id')
            ->join('users', 'users.id', '=', 'tasks.user_id')
            ->join('clients', 'clients.id', '=', 'tasks.client_id')
            ->whereNull('tasks.completed_at')
            ->groupBy('tasks.id')
            ->orderBy('overdue_count', 'DESC')
            ->orderBy('clients.id')
            ->orderBy('tasks.due_at')
            ->get();

        // Order result
        $tasks = $tasks->where('overdue_count', '>', '4');

        return view('reports.task.overdue')->with([
            'tasks' => $tasks,
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function task(Request $request)
    {
        // Initialize tasks used by all filters
        $tasks = Task::select('tasks.*', 'clients.paid')
            ->with(['client', 'user'])
            ->join('clients', 'clients.id', '=', 'tasks.client_id')
            ->leftJoin(DB::raw('(SELECT * FROM task_overdue_reasons JOIN (SELECT MAX(task_overdue_reasons.id) max_id FROM task_overdue_reasons GROUP BY task_id) AS tor1 ON task_overdue_reasons.id = tor1.max_id) as tor'), 'tasks.id', '=', 'tor.task_id')
            ->leftJoin('overdue_reasons', 'overdue_reasons.id', '=', 'tor.reason_id')
            ->orderBy('tasks.due_at');

        // If report is view as
        if ($request->filled('viewasuser')) {
            // Get the user to view as
            $viewAsUser = User::find($request->input('viewasuser'));

            // Fetch allowed clients and find tasks
            $clients = $viewAsUser->getAccessibleClientsQuery()->pluck('id');
            $tasks = $tasks->whereIn('client_id', $clients);

            // Check task completed filter
            if ($request->input('completed') == 'Yes') {
                $tasks = $tasks->completed();
            }
            else {
                $tasks = $tasks->uncompleted();
            }

            // Get avalible users
            $users = Client::active()
                ->whereIn('id', $clients)
                ->with('employee')
                ->has('employee')
                ->groupBy('employee_id')
                ->select('employee_id')
                ->get()
                ->pluck('employee');

            // Get the available categories
            $categories = Task::active()
                ->whereIn('client_id', $clients)
                ->groupBy('category')
                ->select('category')
                ->get()
                ->pluck('category');
            
            // Get the available templates
            $templates = Task::active()
                ->whereIn('client_id', $clients)
                ->with('template')
                ->has('template')
                ->groupBy('template_id')
                ->select('template_id')
                ->get()
                ->pluck('template');

            // Get the available clients
            $clients = $viewAsUser->getAccessibleClientsQuery()->get();
        } else {
            //  Check task completed filter
            if ($request->input('completed') == 'Yes') {
                $tasks = $tasks->completed();
            }
            else {
                $tasks = $tasks->uncompleted();
            }

            // Get the available view as users
            $users = Task::active()
                ->whereNotNull('user_id')
                ->with('user')
                ->groupBy('user_id')
                ->select('user_id')
                ->get()
                ->pluck('user');

            // Get the available clients
            $clients = Task::active()
                ->with('client')
                ->has('client')
                ->groupBy('client_id')
                ->select('client_id')
                ->get()
                ->pluck('client');

            // Get the available categories
            $categories = Task::active()
                ->groupBy('category')
                ->select('category')
                ->get()
                ->pluck('category');
            
            // Get the available templates
            $templates = Task::active()
                ->with('template')
                ->has('template')
                ->groupBy('template_id')
                ->select('template_id')
                ->get()
                ->pluck('template');
        }

        // Filter by delivered
        if ($request->filled('delivered')) {
            $tasks->where('tasks.delivered', $request->input('delivered') == 'Yes' ? 1 : 0);
        }

        // Filter by paid
        if ($request->filled('paid')) {
            $tasks->where('clients.paid', $request->input('paid') == 'Yes' ? 1 : 0);
        }

        // Filter by user
        if ($request->filled('user')) {
            $tasks->where('tasks.user_id', $request->input('user'));
        }

        // Filter by client
        if ($request->filled('client')) {
            $tasks->where('tasks.client_id', $request->input('client'));
        }

        // Filter by category
        if ($request->filled('category')) {
            $tasks->where('tasks.category', $request->input('category'));
        }

        // Filter by template
        if ($request->filled('template')) {
            $tasks->where('tasks.template_id', $request->input('template'));
        }

        // Filter by overdue reason
        if ($request->filled('reason')) {
            if ($request->input('reason') === 'no_reason') {
                $tasks->with(['client', 'user','overdueReason.overdueReason', 'subtasks', 'activeSubtasks', 'template'])
                    ->leftJoin('task_overdue_reasons', function($join)
                    {
                        $join->on('tasks.id', '=', 'task_overdue_reasons.task_id');
                    })
                    ->whereNull('tasks.completed_at')
                    ->whereNull('task_overdue_reasons.id')
                    ->where('tasks.deadline', '<', Carbon::now());
            } else {
                $tasks->where('tor.reason_id', $request->input('reason'));
            }
        }

        // Filter by from date
        if ($request->filled('from')) {
            $tasks->where('deadline', '>=' ,$request->input('from'));
        }

        // Filter by to date
        if ($request->filled('to')) {
            $tasks->where('deadline', '<=' ,$request->input('to'));
        }

        // Get completed, delivered and paid options
        $paid = $delivered = $completed = ['Yes', 'No'];

        // Get the avalible overdue reasons
        $overdues = OverdueReason::orderBy('priority')
            ->where('complete_task', 0)
            ->where('active', 1)
            ->get();

        // Get the available view as users
        $viewAsUsers = Task::active()
            ->whereNotNull('user_id')
            ->with('user')
            ->groupBy('user_id')
            ->select('user_id')
            ->get()
            ->pluck('user');

        // Generate the page path with filter parameters
        $path = url()->current() .
            '?completed=' . $request->input('completed') .
            '&delivered=' . $request->input('delivered') .
            '&paid=' . $request->input('paid') .
            '&user=' . $request->input('user') .
            '&viewasuser=' . $request->input('viewasuser') .
            '&client=' . $request->input('client') .
            '&category=' . $request->input('category') .
            '&template=' . $request->input('template') .
            '&reason=' . $request->input('reason') .
            '&from=' . $request->input('from') .
            '&to=' . $request->input('to');

        return view('reports.task.index')->with([
            'completed' => $completed,
            'selectedCompleted' => $request->input('completed', 'No'),
            'delivered' => $delivered,
            'selectedDelivered' => $request->input('delivered'),
            'paid' => $paid,
            'selectedPaid' => $request->input('paid'),
            'users' => $users,
            'selectedUser' => $request->input('user'),
            'viewAsUsers' => $viewAsUsers,
            'selectedViewAsUser' => $request->input('viewasuser'),
            'clients' => $clients,
            'selectedClient' => $request->input('client'),
            'categories' => $categories,
            'selectedCategory' => $request->input('category'),
            'templates' => $templates,
            'selectedTemplate' => $request->input('template'),
            'overdues' => $overdues,
            'selectedOverdue' => $request->input('reason'),
            'selectedFromDate' => $request->input('from'),
            'selectedToDate' => $request->input('to'),
            'taskcount' => $tasks->count(),
            'tasks' => $tasks->paginate(25)->withPath($path),
        ]);
    }
}
